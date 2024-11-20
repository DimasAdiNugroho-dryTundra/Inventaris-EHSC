<?php
// crudkontrolBarangCawuSatuCawuSatu.php

// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian dan filter
$year = isset($_POST['year']) ? (int) $_POST['year'] : date('Y');
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Tentukan tabel dan rentang tanggal berdasarkan cawu
$table = 'kontrol_barang_cawu_satu';
$idColumn = 'id_kontrol_barang_cawu_satu';
$startDate = "$year-01-01";
$endDate = "$year-04-30";

// Query untuk mengambil data kontrol barang dengan filter tanggal
$query = "SELECT kb.*, i.kode_inventaris, i.nama_barang, u.nama as nama_petugas 
          FROM $table kb 
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
          JOIN user u ON kb.id_user = u.id_user 
          WHERE (i.kode_inventaris LIKE '%$search%' OR i.nama_barang LIKE '%$search%') 
          AND YEAR(kb.tanggal_kontrol) = '$year'
          ORDER BY kb.$idColumn DESC 
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination dengan filter yang sama
$totalQuery = "SELECT COUNT(*) as total FROM $table kb 
               JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
               WHERE (i.kode_inventaris LIKE '%$search%' OR i.nama_barang LIKE '%$search%')
               AND YEAR(kb.tanggal_kontrol) = '$year'";

$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRows = $totalRow['total'];
$totalPages = ceil($totalRows / $limit);

function getAvailableInventaris($conn, $year, $table) {
    $query = "SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.jumlah_akhir AS jumlah,  -- Menggunakan jumlah_akhir
                i.satuan,
                IFNULL((SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) 
                         FROM $table 
                         WHERE id_inventaris = i.id_inventaris 
                         AND YEAR(tanggal_kontrol) = '$year'), 0) AS jumlah_terkontrol
              FROM inventaris i 
              WHERE (i.jumlah_akhir - IFNULL((SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) 
                                                 FROM $table 
                                                 WHERE id_inventaris = i.id_inventaris 
                                                 AND YEAR(tanggal_kontrol) = '$year'), 0)) > 0
              AND i.jumlah_akhir > 0"; // Pastikan hanya mengambil barang yang tersedia
    return mysqli_query($conn, $query);
}

// Fungsi untuk menghitung total kontrol barang
function getTotalkontrolBarangCawuSatu($conn, $table)
{
    $query = "SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) as total FROM $table"; 
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?: 0; 
}

// Proses penambahan kontrol barang
if (isset($_POST['tambahKontrol'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $tanggal_kontrol = $_POST['tanggal'];
    $year = date('Y', strtotime($tanggal_kontrol));

    $jumlah_baik = $_POST['jumlah_baik'] ?? 0;
    $jumlah_rusak = $_POST['jumlah_rusak'] ?? 0;
    $jumlah_pindah = $_POST['jumlah_pindah'] ?? 0;
    $jumlah_hilang = $_POST['jumlah_hilang'] ?? 0;

    // Validasi tanggal
    $valid_date = true;
    $date_error = '';

    if (strtotime($tanggal_kontrol) < strtotime("$year-01-01") || strtotime($tanggal_kontrol) > strtotime("$year-04-30")) {
        $valid_date = false;
        $date_error = "Tanggal harus berada di antara 1 Januari - 30 April $year";
    }

    if (!$valid_date) {
        $_SESSION['error_message'] = $date_error;
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Lanjutkan dengan proses insert
    $query = "INSERT INTO $table (id_inventaris, id_user, tanggal_kontrol, tahun_kontrol, jumlah_baik, jumlah_rusak, jumlah_pindah, jumlah_hilang) 
              VALUES ('$id_inventaris', '{$_SESSION['id_user']}', '$tanggal_kontrol', '$year', '$jumlah_baik', '$jumlah_rusak', '$jumlah_pindah', '$jumlah_hilang')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan kontrol barang: " . mysqli_error($conn);
    }

    if (!isset($_POST['status']) || !in_array('1', $_POST['status'])) {
        $_SESSION['error_message'] = "Harap pilih setidaknya satu status.";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }    
}

// Proses pembaruan kontrol barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kontrol = $_POST['id_kontrol'];
    $tanggal_kontrol = $_POST['tanggal'];
    $jumlah_baik = $_POST['jumlah_baik'];
    $jumlah_rusak = $_POST['jumlah_rusak'];
    $jumlah_pindah = $_POST['jumlah_pindah'];
    $jumlah_hilang = $_POST['jumlah_hilang'];

    $query = "UPDATE $table SET 
              tanggal_kontrol = '$tanggal_kontrol', 
              jumlah_baik = '$jumlah_baik', 
              jumlah_rusak = '$jumlah_rusak', 
              jumlah_pindah = '$jumlah_pindah', 
              jumlah_hilang = '$jumlah_hilang' 
              WHERE $idColumn = '$id_kontrol'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil diubah!";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrolBarangCawuSatu.php");
    exit();
}

// Proses penghapusan kontrol barang
if (isset($_GET['delete'])) {
    $id_kontrol = $_GET['delete'];

    $query = "DELETE FROM $table WHERE $idColumn='$id_kontrol'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrolBarangCawuSatu.php");
    exit();
}
?>