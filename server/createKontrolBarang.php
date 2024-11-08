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
        if ($data['jumlah'] > $inv_data['jumlah']) {
            throw new Exception("Jumlah melebihi stok yang tersedia!");
        }

        // Insert data ke tabel kontrol_barang tanpa kondisi status baik
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

        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}
?>