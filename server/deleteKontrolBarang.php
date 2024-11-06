<?php
function deleteKontrolBarang($conn, $id_kontrol)
{
    mysqli_begin_transaction($conn);

    try {
        // Get control data before deletion
        $data_query = "SELECT kb.*, i.id_inventaris 
                      FROM kontrol_barang kb
                      JOIN inventaris i ON kb.id_invetaris = i.id_inventaris
                      WHERE kb.id_kontrol_barang = '$id_kontrol'";
        $result = mysqli_query($conn, $data_query);
        $control_data = mysqli_fetch_assoc($result);

        // If status is damaged or moved, restore inventory quantity
        if ($control_data['status'] != 1) {
            $restore_query = "UPDATE inventaris 
                            SET jumlah = jumlah + {$control_data['jumlah']}
                            WHERE id_inventaris = '{$control_data['id_inventaris']}'";
            mysqli_query($conn, $restore_query);
        }

        // Delete related records
        mysqli_query($conn, "DELETE FROM kerusakan_barang WHERE id_kontrol_barang = '$id_kontrol'");
        mysqli_query($conn, "DELETE FROM perpindahan_barang WHERE id_kontrol_barang = '$id_kontrol'");

        // Delete main control record
        mysqli_query($conn, "DELETE FROM kontrol_barang WHERE id_kontrol_barang = '$id_kontrol'");

        // Check if item needs to be restored from archive
        $check_inventaris = "SELECT COUNT(*) as total_control 
                           FROM kontrol_barang 
                           WHERE id_invetaris = '{$control_data['id_inventaris']}'";
        $check_result = mysqli_query($conn, $check_query);
        $check_data = mysqli_fetch_assoc($check_result);

        if ($check_data['total_control'] == 0) {
            // Restore from archive if exists
            $restore_query = "SELECT * FROM arsip_inventaris 
                            WHERE id_inventaris = '{$control_data['id_inventaris']}'";
            $restore_result = mysqli_query($conn, $restore_query);

            if (mysqli_num_rows($restore_result) > 0) {
                $arsip_data = mysqli_fetch_assoc($restore_result);

                // Insert back to inventaris
                $restore_inventaris = "INSERT INTO inventaris (
                    id_inventaris, kode_inventaris, id_penerimaan,
                    id_departemen, id_kategori, jumlah, satuan, kondisi
                ) SELECT 
                    id_inventaris, kode_inventaris, 
                    (SELECT id_penerimaan FROM penerimaan_barang WHERE nama_barang = '{$arsip_data['nama_barang']}' LIMIT 1),
                    (SELECT id_departemen FROM departemen WHERE nama_departemen = 'Gudang' LIMIT 1),
                    (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Umum' LIMIT 1),
                    {$control_data['jumlah']}, '{$arsip_data['satuan']}', 1
                FROM arsip_inventaris
                WHERE id_inventaris = '{$control_data['id_inventaris']}'";

                mysqli_query($conn, $restore_inventaris);

                // Delete from archive
                mysqli_query($conn, "DELETE FROM arsip_inventaris 
                                   WHERE id_inventaris = '{$control_data['id_inventaris']}'");
            }
        }

        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

// Helper function untuk update status barang di inventaris setelah delete
function updateInventarisStatus($conn, $id_inventaris)
{
    $check_query = "SELECT COUNT(*) as total_rusak 
                   FROM kontrol_barang 
                   WHERE id_invetaris = '$id_inventaris' 
                   AND status = 2";
    $result = mysqli_query($conn, $check_query);
    $data = mysqli_fetch_assoc($result);

    if ($data['total_rusak'] == 0) {
        mysqli_query($conn, "UPDATE inventaris 
                           SET kondisi = 1 
                           WHERE id_inventaris = '$id_inventaris'");
    }
}

// Helper function untuk mengembalikan barang dari arsip
function restoreFromArchive($conn, $id_arsip)
{
    mysqli_begin_transaction($conn);

    try {
        $arsip_query = "SELECT * FROM arsip_inventaris WHERE id_arsip = '$id_arsip'";
        $arsip_result = mysqli_query($conn, $arsip_query);
        $arsip_data = mysqli_fetch_assoc($arsip_result);

        if ($arsip_data) {
            // Restore ke inventaris
            $restore_query = "INSERT INTO inventaris (
                id_inventaris, kode_inventaris, id_penerimaan,
                id_departemen, id_kategori, jumlah, satuan, kondisi
            ) VALUES (
                '{$arsip_data['id_inventaris']}',
                '{$arsip_data['kode_inventaris']}',
                (SELECT id_penerimaan FROM penerimaan_barang WHERE nama_barang = '{$arsip_data['nama_barang']}' LIMIT 1),
                (SELECT id_departemen FROM departemen WHERE nama_departemen = 'Gudang' LIMIT 1),
                (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Umum' LIMIT 1),
                '{$arsip_data['jumlah']}',
                '{$arsip_data['satuan']}',
                1
            )";
            mysqli_query($conn, $restore_query);

            // Delete from archive
            mysqli_query($conn, "DELETE FROM arsip_inventaris WHERE id_arsip = '$id_arsip'");
        }

        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}
?>