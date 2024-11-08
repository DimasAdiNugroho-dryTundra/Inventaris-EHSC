<?php
// crudKontrolBarang.php

// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5; // Default ke 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk mengambil data kontrol barang
$query = "SELECT kb.*, i.kode_inventaris, i.nama_barang, u.nama as nama_petugas 
          FROM kontrol_barang kb 
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
          JOIN user u ON kb.id_user = u.id_user 
          WHERE i.kode_inventaris LIKE '%$search%' OR i.nama_barang LIKE '%$search%' 
          ORDER BY kb.id_kontrol_barang DESC 
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM kontrol_barang kb 
                JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
                WHERE i.kode_inventaris LIKE '%$search%' OR i.nama_barang LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

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

// Fungsi untuk menentukan cawu berdasarkan bulan
function determineCawu($month)
{
    if ($month >= 1 && $month <= 4) {
        return "Cawu 1";
    } elseif ($month >= 5 && $month <= 8) {
        return "Cawu 2";
    } else {
        return "Cawu 3";
    }
}

// Proses penambahan kontrol barang
if (isset($_POST['tambahKontrol'])) {
    $id_inventaris = $_POST['id_inventaris'];

    // Cek apakah barang sudah pernah dikontrol
    $check_query = "SELECT * FROM kontrol_barang WHERE id_inventaris = '$id_inventaris'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error_message'] = "Barang ini sudah pernah dikontrol!";
    } else {
        // Ambil nama barang dari tabel inventaris
        $get_barang = "SELECT nama_barang FROM inventaris WHERE id_inventaris = '$id_inventaris'";
        $barang_result = mysqli_query($conn, $get_barang);
        $barang_data = mysqli_fetch_assoc($barang_result);
        $nama_barang = $barang_data['nama_barang'];

        $tanggal = $_POST['tanggal'];
        $jumlah = $_POST['jumlah'];
        $status = $_POST['status'];
        $keterangan = $_POST['keterangan'];
        $cawu = determineCawu(date('n', strtotime($tanggal)));

        $query = "INSERT INTO kontrol_barang (id_inventaris, nama_barang, tanggal, jumlah, status, keterangan, cawu) 
                  VALUES ('$id_inventaris', '$nama_barang', '$tanggal', '$jumlah', '$status', '$keterangan', '$cawu')";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Kontrol barang berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan kontrol barang: " . mysqli_error($conn);
        }
    }
    header("Location: kontrol_barang.php");
    exit();
}

// Proses pengeditan kontrol barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kontrol = $_POST['id_kontrol'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $status = $_POST['status'];
    $keterangan = $_POST['keterangan'];
    $cawu = determineCawu(date('n', strtotime($tanggal))); // Hitung cawu berdasarkan bulan dari tanggal baru

    // Data untuk update
    $data = [
        'id_kontrol' => $id_kontrol,
        'tanggal' => $tanggal,
        'jumlah' => $jumlah,
        'status' => $status,
        'keterangan' => $keterangan,
        'cawu' => $cawu // Sertakan cawu dalam data
    ];

    // Panggil fungsi untuk memperbarui kontrol barang
    if (updateKontrolBarang($conn, $data)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil diubah!";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrol_barang.php");
    exit();
}

// Proses pengeditan kontrol barang
function updateKontrolBarang($conn, $data)
{
    $query = "UPDATE kontrol_barang SET
              tanggal = '{$data['tanggal']}',
              jumlah = '{$data['jumlah']}',
              status = '{$data['status']}',
              keterangan = '{$data['keterangan']}',
              cawu = '{$data['cawu']}'  
              WHERE id_kontrol_barang = '{$data['id_kontrol']}'";

    return mysqli_query($conn, $query);
}

// Proses penghapusan kontrol barang
if (isset($_GET['delete'])) {
    $id_kontrol = $_GET['delete'];

    // Hapus data kontrol barang dari database
    $query = "DELETE FROM kontrol_barang WHERE id_kontrol_barang='$id_kontrol'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrol_barang.php");
    exit(); // Tambahkan exit agar tidak melanjutkan eksekusi
}
?>