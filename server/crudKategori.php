<?php
// Pagination settings
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5; // Default to 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search handling
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT * FROM kategori WHERE nama_kategori LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Count total data for pagination
$totalQuery = "SELECT COUNT(*) as total FROM kategori WHERE nama_kategori LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Process adding category
if (isset($_POST['tambahKategori'])) {
    $kode_kategori = $_POST['kode_kategori'];
    $nama_kategori = $_POST['nama_kategori'];

    // Check if category code already exists
    $checkKode = "SELECT * FROM kategori WHERE kode_kategori='$kode_kategori'";
    $checkResult = mysqli_query($conn, $checkKode);
    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error_message'] = "Kode kategori sudah ada, silakan gunakan kode lain.";
    } else {
        $query = "INSERT INTO kategori (kode_kategori, nama_kategori) VALUES ('$kode_kategori', '$nama_kategori')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Kategori berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan kategori: " . mysqli_error($conn);
        }
    }
    header("Location: kategori.php");
    exit(); // Ensure no further execution
}

// Process editing category
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kategori = $_POST['id_kategori'];

    // Cek apakah kategori sudah digunakan di tabel inventaris
    $checkQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_kategori = $id_kategori";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data kategori tidak dapat diubah karena sudah digunakan pada data inventaris!";
    } else {
        $kode_kategori = $_POST['kode_kategori'];
        $nama_kategori = $_POST['nama_kategori'];

        $query = "UPDATE kategori SET kode_kategori = '$kode_kategori', nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";

        if (!mysqli_query($conn, $query)) {
            $_SESSION['error_message'] = "Gagal mengubah kategori: " . mysqli_error($conn);
        } else {
            $_SESSION['success_message'] = "Kategori berhasil diubah!";
        }
    }
    header("Location: kategori.php");
    exit();
}

// Process deleting category
if (isset($_GET['delete'])) {
    $id_kategori = $_GET['delete'];

    // Cek apakah kategori sudah digunakan di tabel inventaris
    $checkQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_kategori = $id_kategori";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data kategori tidak dapat dihapus karena sudah digunakan pada data inventaris!";
    } else {
        $query = "DELETE FROM kategori WHERE id_kategori='$id_kategori'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Kategori berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus kategori: " . mysqli_error($conn);
        }
    }
    header("Location: kategori.php");
    exit();
}
?>