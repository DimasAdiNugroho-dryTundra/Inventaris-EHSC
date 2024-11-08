<?php
function deleteKontrolBarang($conn, $id_kontrol)
{
    mysqli_begin_transaction($conn);

    try {
        // Ambil data kontrol sebelum penghapusan
        $data_query = "SELECT * FROM kontrol_barang WHERE id_kontrol_barang = '$id_kontrol'";
        $result = mysqli_query($conn, $data_query);
        $control_data = mysqli_fetch_assoc($result);

        // Hapus data kontrol barang
        mysqli_query($conn, "DELETE FROM kontrol_barang WHERE id_kontrol_barang = '$id_kontrol'");

        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}
?>