<?php
function updateKontrolBarang($conn, $data)
{
    // Memulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // Mengambil data lama untuk perbandingan
        $old_data_query = "SELECT * FROM kontrol_barang WHERE id_kontrol_barang = '{$data['id_kontrol']}'";
        $old_result = mysqli_query($conn, $old_data_query);
        $old_data = mysqli_fetch_assoc($old_result);

        // Update data kontrol barang
        $update_kontrol = "UPDATE kontrol_barang 
                           SET tanggal = '{$data['tanggal']}', 
                               status = '{$data['status']}', 
                               jumlah = '{$data['jumlah']}', 
                               keterangan = '{$data['keterangan']}'
                           WHERE id_kontrol_barang = '{$data['id_kontrol']}'";
        mysqli_query($conn, $update_kontrol);

        // Commit transaksi jika semua operasi berhasil
        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        // Rollback semua perubahan jika terjadi kesalahan
        mysqli_rollback($conn);
        throw new Exception("Gagal mengupdate data: " . $e->getMessage());
    }
}
?>