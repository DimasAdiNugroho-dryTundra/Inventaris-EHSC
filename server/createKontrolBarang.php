<?php
function createKontrolBarang($conn, $data)
{
    mysqli_begin_transaction($conn);

    try {
        // Cek jumlah barang di inventaris
        $check_query = "SELECT jumlah FROM inventaris WHERE id_inventaris = '{$data['id_inventaris']}'";
        $check_result = mysqli_query($conn, $check_query);
        $inv_data = mysqli_fetch_assoc($check_result);

        // Hanya memasukkan kontrol barang tanpa mengubah jumlah untuk status baik
        if ($data['status'] == 1) {
            // Pastikan jumlah yang dimasukkan adalah sesuai input
            $query = "INSERT INTO kontrol_barang (id_user, id_inventaris, cawu, tanggal, status, keterangan, jumlah)
                      VALUES (
                          '{$data['id_user']}',
                          '{$data['id_inventaris']}',
                          '{$data['cawu']}',
                          '{$data['tanggal']}',
                          '{$data['status']}',
                          '{$data['keterangan']}',
                          '{$data['jumlah']}'  -- Menggunakan jumlah dari input
                      )";
            mysqli_query($conn, $query);
        } else {
            // Validasi jumlah untuk status pindah, rusak atau hilang
            if ($data['jumlah'] > $inv_data['jumlah']) {
                throw new Exception("Jumlah melebihi stok yang tersedia!");
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

            // Jika status Pindah (2), Rusak (3), atau Hilang (4)
            $inv_query = "SELECT kode_inventaris, nama_barang FROM inventaris WHERE id_inventaris = '{$data['id_inventaris']}'";
            $inv_result = mysqli_query($conn, $inv_query);
            $inv_detail = mysqli_fetch_assoc($inv_result);

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
                                    '{$data['jumlah']}',  -- Menggunakan jumlah dari input
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
                                   '{$data['jumlah']}',  -- Menggunakan jumlah dari input
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
                                   '{$data['jumlah']}',  -- Menggunakan jumlah dari input
                                   '{$data['keterangan']}'
                               )";
                mysqli_query($conn, $hilang_query);
            }
        }

        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

?>