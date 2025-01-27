<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT * FROM ruangan WHERE nama_ruangan LIKE '%$search%' OR kode_ruangan LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung data untuk Pagination
$totalQuery = "SELECT COUNT(*) as total FROM ruangan WHERE nama_ruangan LIKE '%$search%' OR kode_ruangan LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Proses penambahan ruangan
if (isset($_POST['tambahRuangan'])) {
    $kode_ruangan = $_POST['kode_ruangan'];
    $nama_ruangan = $_POST['nama_ruangan'];

    $query = "INSERT INTO ruangan (kode_ruangan, nama_ruangan) VALUES ('$kode_ruangan', '$nama_ruangan')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Ruangan berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan ruangan: " . mysqli_error($conn);
    }
    header("Location: ruangan.php");
    exit();
}

// Proses pengeditan ruangan
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_ruangan = $_POST['id_ruangan'];
    
    // Cek apakah ruangan sudah digunakan di tabel inventaris
    $checkQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_ruangan = $id_ruangan";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data ruangan tidak dapat diubah karena sudah digunakan pada data inventaris!";
    } else {
        $kode_ruangan = $_POST['kode_ruangan'];
        $nama_ruangan = $_POST['nama_ruangan'];

        $query = "UPDATE ruangan SET kode_ruangan = '$kode_ruangan', nama_ruangan = '$nama_ruangan' WHERE id_ruangan = '$id_ruangan'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Ruangan berhasil diubah!";
        } else {
            $_SESSION['error_message'] = "Gagal mengubah ruangan: " . mysqli_error($conn);
        }
    }
    header("Location: ruangan.php");
    exit();
}

// Proses penghapusan ruangan
if (isset($_GET['delete'])) {
    $id_ruangan = $_GET['delete'];

    // Cek apakah ruangan sudah digunakan di tabel inventaris
    $checkQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_ruangan = $id_ruangan";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data ruangan tidak dapat dihapus karena sudah digunakan pada data inventaris!";
    } else {
        $query = "DELETE FROM ruangan WHERE id_ruangan='$id_ruangan'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Ruangan berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus ruangan: " . mysqli_error($conn);
        }
    }
    header("Location: ruangan.php");
    exit();
}
?>