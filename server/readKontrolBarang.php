<?php
// Query untuk menampilkan data kontrol barang
function getKontrolBarang($conn, $search = '', $limit = 5, $offset = 0)
{
    $query = "SELECT kb.*, 
              i.kode_inventaris, 
              i.nama_barang, 
              u.nama as nama_petugas
              FROM kontrol_barang kb
              JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
              JOIN user u ON kb.id_user = u.id_user
              WHERE i.kode_inventaris LIKE '%$search%'
              OR i.nama_barang LIKE '%$search%'
              ORDER BY kb.id_kontrol_barang DESC
              LIMIT $limit OFFSET $offset";

    return mysqli_query($conn, $query);
}

// Hitung total jumlah kontrol barang dari tabel kontrol_barang
function getTotalKontrolBarang($conn)
{
    $query = "SELECT SUM(jumlah) as total FROM kontrol_barang"; // Menghitung total jumlah dari kontrol_barang

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?: 0; // Mengembalikan 0 jika tidak ada hasil
}

// Get available inventaris for dropdown
function getAvailableInventaris($conn)
{
    $query = "SELECT i.*,
              COALESCE(kb.jumlah_kontrol, 0) as jumlah_terkontrol,
              i.jumlah - COALESCE(kb.jumlah_kontrol, 0) as sisa_belum_terkontrol
              FROM inventaris i
              LEFT JOIN (
                  SELECT kb.id_inventaris, SUM(kb.jumlah) as jumlah_kontrol
                  FROM kontrol_barang kb
                  GROUP BY kb.id_inventaris
              ) kb ON i.id_inventaris = kb.id_inventaris
              WHERE i.jumlah - COALESCE(kb.jumlah_kontrol, 0) > 0
              ORDER BY i.kode_inventaris ASC";

    return mysqli_query($conn, $query);
}


// Get detail kontrol barang by ID
function getKontrolBarangById($conn, $id)
{
    $query = "SELECT kb.*, i.kode_inventaris, i.jumlah as jumlah_inventaris,
              i.nama_barang
              FROM kontrol_barang kb
              JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
              WHERE kb.id_kontrol_barang = '$id'";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
?>