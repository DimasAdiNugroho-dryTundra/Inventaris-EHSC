<?php
function createKontrolBarang($conn, $data)
{
    mysqli_begin_transaction($conn);

    try {
        // Cek jumlah barang di inventaris
        $check_query = "SELECT jumlah FROM inventaris WHERE id_inventaris = '{$data['id_inventaris']}'";
        $check_result = mysqli_query($conn, $check_query);
        $inv_data = mysqli_fetch_assoc($check_result);

        // Validasi jumlah untuk status pindah, rusak atau hilang
        if (($data['status'] == 2 || $data['status'] == 3 || $data['status'] == 4) && $data['jumlah'] > $inv_data['jumlah']) {
            throw new Exception("Jumlah melebihi stok yang tersedia!");
        }

        // Jika status baik, set jumlah sesuai inventaris
        if ($data['status'] == 1) {
            $data['jumlah'] = $inv_data['jumlah'];
        }

        // Insert data ke tabel kontrol_barang
        $query = "INSERT INTO kontrol_barang (id_user, id_inventaris, cawu, tanggal, status, keterangan)
                  VALUES (
                      '{$data['id_user']}',
                      '{$data['id_inventaris']}',
                      '{$data['cawu']}',
                      '{$data['tanggal']}',
                      '{$data['status']}',
                      '{$data['keterangan']}'
                  )";

        mysqli_query($conn, $query);
        $id_kontrol = mysqli_insert_id($conn);

        // Get inventaris data for detail tables
        $inv_query = "SELECT i.kode_inventaris, pb.nama_barang 
                     FROM inventaris i 
                     JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan 
                     WHERE i.id_inventaris = '{$data['id_inventaris']}'";
        $inv_result = mysqli_query($conn, $inv_query);
        $inv_detail = mysqli_fetch_assoc($inv_result);

        // Jika status Pindah (2), Rusak (3), atau Hilang (4)
        if ($data['status'] > 1) {
            // Update jumlah inventaris
            $update_inv = "UPDATE inventaris 
                          SET jumlah = jumlah - {$data['jumlah']}
                          WHERE id_inventaris = '{$data['id_inventaris']}'";
            mysqli_query($conn, $update_inv);

            if ($data['status'] == 2) {
                // Insert ke tabel perpindahan_barang
                $pindah_query = "INSERT INTO perpindahan_barang 
                                (id_kontrol_barang, kode_barang, nama_barang, cawu,
                                 tanggal_perpindahan, jumlah, keterangan)
                                VALUES (
                                    '$id_kontrol',
                                    '{$inv_detail['kode_inventaris']}',
                                    '{$inv_detail['nama_barang']}',
                                    '{$data['cawu']}',
                                    '{$data['tanggal']}',
                                    '{$data['jumlah']}',
                                    '{$data['keterangan']}'
                                )";
                mysqli_query($conn, $pindah_query);
            } elseif ($data['status'] == 3) {
                // Insert ke tabel kerusakan_barang
                $rusak_query = "INSERT INTO kerusakan_barang 
                               (id_kontrol_barang, kode_barang, nama_barang, cawu,
                                tanggal_kerusakan, jumlah, keterangan)
                               VALUES (
                                   '$id_kontrol',
                                   '{$inv_detail['kode_inventaris']}',
                                   '{$inv_detail['nama_barang']}',
                                   '{$data['cawu']}',
                                   '{$data['tanggal']}',
                                   '{$data['jumlah']}',
                                   '{$data['keterangan']}'
                               )";
                mysqli_query($conn, $rusak_query);
            } elseif ($data['status'] == 4) {
                // Insert ke tabel kehilangan_barang
                $hilang_query = "INSERT INTO kehilangan_barang 
                               (id_kontrol_barang, kode_barang, nama_barang, cawu,
                                tanggal_kerusakan, jumlah, keterangan)
                               VALUES (
                                   '$id_kontrol',
                                   '{$inv_detail['kode_inventaris']}',
                                   '{$inv_detail['nama_barang']}',
                                   '{$data['cawu']}',
                                   '{$data['tanggal']}',
                                   '{$data['jumlah']}',
                                   '{$data['keterangan']}'
                               )";
                mysqli_query($conn, $hilang_query);
            }
        }

        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}