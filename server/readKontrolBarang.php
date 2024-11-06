<?php
// Query untuk menampilkan data kontrol barang dengan join ke tabel terkait
function getKontrolBarang($conn, $search = '', $limit = 5, $offset = 0)
{
    $query = "SELECT kb.*, i.kode_inventaris, i.jumlah as jumlah_inventaris,
              i.nama_barang, u.nama as nama_petugas,
              kr.jumlah as jumlah_rusak,
              kh.jumlah as jumlah_hilang,
              pb.jumlah as jumlah_pindah
              FROM kontrol_barang kb
              JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
              JOIN user u ON kb.id_user = u.id_user
              LEFT JOIN kerusakan_barang kr ON kb.id_kontrol_barang = kr.id_kontrol_barang
              LEFT JOIN kehilangan_barang kh ON kb.id_kontrol_barang = kh.id_kontrol_barang
              LEFT JOIN perpindahan_barang pb ON kb.id_kontrol_barang = pb.id_kontrol_barang
              WHERE i.kode_inventaris LIKE '%$search%'
              OR i.nama_barang LIKE '%$search%'
              ORDER BY kb.tanggal DESC, kb.id_kontrol_barang DESC
              LIMIT $limit OFFSET $offset";

    return mysqli_query($conn, $query);
}


// Hitung total data untuk pagination
function getTotalKontrolBarang($conn, $search = '')
{
    $query = "SELECT COUNT(*) as total FROM kontrol_barang kb
              JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
              WHERE i.kode_inventaris LIKE '%$search%'
              OR i.nama_barang LIKE '%$search%'";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Get available inventaris for dropdown
function getAvailableInventaris($conn)
{
    $query = "SELECT i.*,
              COALESCE(kb_baik.jumlah_baik, 0) as jumlah_terkontrol_baik,
              i.jumlah - COALESCE(kb_baik.jumlah_baik, 0) as sisa_belum_terkontrol
              FROM inventaris i
              LEFT JOIN (
                  SELECT kb.id_inventaris, COUNT(*) as jumlah_kontrol, SUM(CASE WHEN kb.status = 1 THEN i.jumlah ELSE 0 END) as jumlah_baik
                  FROM kontrol_barang kb
                  JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
                  GROUP BY kb.id_inventaris
              ) kb_baik ON i.id_inventaris = kb_baik.id_inventaris
              HAVING sisa_belum_terkontrol > 0
              ORDER BY i.kode_inventaris ASC";

    return mysqli_query($conn, $query);
}
// Get detail kontrol barang by ID
function getKontrolBarangById($conn, $id)
{
    $query = "SELECT kb.*, i.kode_inventaris, i.jumlah as jumlah_inventaris,
              i.nama_barang,
              kr.jumlah as jumlah_rusak,
              kh.jumlah as jumlah_hilang,
              pb.jumlah as jumlah_pindah
              FROM kontrol_barang kb
              JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
              LEFT JOIN kerusakan_barang kr ON kb.id_kontrol_barang = kr.id_kontrol_barang
              LEFT JOIN kehilangan_barang kh ON kb.id_kontrol_barang = kh.id_kontrol_barang
              LEFT JOIN perpindahan_barang pb ON kb.id_kontrol_barang = pb.id_kontrol_barang
              WHERE kb.id_kontrol_barang = '$id'";

    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
?>