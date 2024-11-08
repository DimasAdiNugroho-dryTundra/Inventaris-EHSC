<?php
// crudKontrolBarangCawuSatu.php

// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian dan filter
$cawu = isset($_POST['cawu']) ? (int)$_POST['cawu'] : 1;
$year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
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

// Fungsi untuk menghitung total kontrol barang
function getTotalKontrolBarang($conn, $table) {
    $query = "SELECT SUM(jumlah_kontrol) as total FROM $table"; // Menghitung total jumlah dari kontrol_barang
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?: 0; // Mengembalikan 0 jika tidak ada hasil
}

// Get available inventaris for dropdown
function getAvailableInventaris($conn, $year) {
    $query = "SELECT i.*, 
              COALESCE(kb.jumlah_kontrol, 0) as jumlah_terkontrol,
              i.jumlah - COALESCE(kb.jumlah_kontrol, 0) as sisa_belum_terkontrol
              FROM inventaris i 
              LEFT JOIN (
                  SELECT id_inventaris, SUM(jumlah_kontrol) as jumlah_kontrol 
                  FROM kontrol_barang_cawu_satu 
                  WHERE tanggal_kontrol BETWEEN '$year-01-01' AND '$year-04-30'
                  GROUP BY id_inventaris
              ) kb ON i.id_inventaris = kb.id_inventaris 
              WHERE i.jumlah > COALESCE(kb.jumlah_kontrol, 0)
              ORDER BY i.kode_inventaris ASC";

    return mysqli_query($conn, $query);
}

// Proses penambahan kontrol barang
if (isset($_POST['tambahKontrol'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $tanggal_kontrol = $_POST['tanggal'];
    $jumlah_kontrol = $_POST['jumlah'];
    $status_kontrol = $_POST['status'];
    $keterangan = $_POST['keterangan'];

    // Validasi tanggal sesuai dengan cawu yang dipilih
    $valid_date = true;
    $date_error = '';

    if (strtotime($tanggal_kontrol) < strtotime("$year-01-01") || strtotime($tanggal_kontrol) > strtotime("$year-04-30")) {
        $valid_date = false;
        $date_error = "Tanggal harus berada di antara 1 Januari - 30 April $year";
    }

    if (!$valid_date) {
        $_SESSION['error_message'] = $date_error;
        header("Location: kontrolBarang.php");
        exit();
    }

    // Lanjutkan dengan proses insert
    $query = "INSERT INTO $table (id_inventaris, id_user, tanggal_kontrol, jumlah_kontrol, status_kontrol, keterangan) 
              VALUES ('$id_inventaris', '{$_SESSION['id_user']}', '$tanggal_kontrol', '$jumlah_kontrol', '$status_kontrol', '$keterangan')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan kontrol barang: " . mysqli_error($conn);
    }
    header("Location: kontrolBarang.php");
    var_dump($query);
    exit();
}

// Proses pengeditan kontrol barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kontrol = $_POST['id_kontrol'];
    $tanggal_kontrol = $_POST['tanggal'];
    $jumlah_kontrol = $_POST['jumlah'];
    $status_kontrol = $_POST['status'];
    $keterangan = $_POST['keterangan'];

    // Data untuk update
    $data = [
        'id_kontrol' => $id_kontrol,
        'tanggal_kontrol' => $tanggal_kontrol,
        'jumlah_kontrol' => $jumlah_kontrol,
        'status_kontrol' => $status_kontrol,
        'keterangan' => $keterangan
    ];

    // Panggil fungsi untuk memperbarui kontrol barang
    if (updateKontrolBarang($conn, $data, $table)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil diubah!";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrolBarang.php");
    exit();
}

// Proses penghapusan kontrol barang
if (isset($_GET['delete'])) {
    $id_kontrol = $_GET['delete'];

    // Hapus data kontrol barang dari database
    $query = "DELETE FROM $table WHERE $idColumn='$id_kontrol'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrolBarang.php");
    exit();
}

// Fungsi untuk memperbarui kontrol barang
function updateKontrolBarang($conn, $data, $table) {
    global $idColumn; // Memastikan kolom ID yang benar digunakan
    $query = "UPDATE $table SET 
              tanggal_kontrol = '{$data['tanggal_kontrol']}', 
              jumlah_kontrol = '{$data['jumlah_kontrol']}', 
              status_kontrol = '{$data['status_kontrol']}', 
              keterangan = '{$data['keterangan']}'
              WHERE $idColumn = '{$data['id_kontrol']}'";
    
    return mysqli_query($conn, $query);
}

?>