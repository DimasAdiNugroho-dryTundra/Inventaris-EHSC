<?php
require_once('../server/configDB.php');

// Pagination settings
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5; // Default to 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search handling
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT * FROM ruangan WHERE nama_ruangan LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Count total data for pagination
$totalQuery = "SELECT COUNT(*) as total FROM ruangan WHERE nama_ruangan LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Process adding ruangan
if (isset($_POST['tambahRuangan'])) {
    $nama_ruangan = $_POST['nama_ruangan'];

    $query = "INSERT INTO ruangan (nama_ruangan) VALUES ('$nama_ruangan')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Ruangan berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan ruangan: " . mysqli_error($conn);
    }
    header("Location: ruangan.php");
    exit();
}

// Process editing ruangan
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_ruangan = $_POST['id_ruangan'];
    $nama_ruangan = $_POST['nama_ruangan'];

    $query = "UPDATE ruangan SET nama_ruangan = '$nama_ruangan' WHERE id_ruangan = '$id_ruangan'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Ruangan berhasil diubah!";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah ruangan: " . mysqli_error($conn);
    }
    header("Location: ruangan.php");
    exit();
}

// Process deleting ruangan
if (isset($_GET['delete'])) {
    $id_ruangan = $_GET['delete'];

    $query = "DELETE FROM ruangan WHERE id_ruangan='$id_ruangan'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Ruangan berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus ruangan: " . mysqli_error($conn);
    }
    header("Location: ruangan.php");
    exit();
}
?>