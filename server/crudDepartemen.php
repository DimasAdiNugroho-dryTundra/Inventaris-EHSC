<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5; // Default ke 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT * FROM departemen WHERE nama_departemen LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM departemen WHERE nama_departemen LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Proses penambahan departemen
if (isset($_POST['tambahDepartemen'])) {
    $kode_departemen = $_POST['kode_departemen'];
    $nama_departemen = $_POST['nama_departemen'];

    // Cek apakah kode departemen sudah ada
    $checkKode = "SELECT * FROM departemen WHERE kode_departemen='$kode_departemen'";
    $checkResult = mysqli_query($conn, $checkKode);
    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error_message'] = "Kode departemen sudah ada, silakan gunakan kode lain.";
    } else {
        $query = "INSERT INTO departemen (kode_departemen, nama_departemen) VALUES ('$kode_departemen', '$nama_departemen')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Departemen berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan departemen: " . mysqli_error($conn);
        }
    }
    header("Location: departemen.php");
    exit(); // Tambahkan exit agar tidak melanjutkan eksekusi
}

// Proses pengeditan departemen
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_departemen = $_POST['id_departemen'];

    // Cek apakah departemen sudah digunakan di tabel inventaris
    $checkQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_departemen = $id_departemen";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data departemen tidak dapat diubah karena sudah digunakan pada data inventaris!";
    } else {
        $kode_departemen = $_POST['kode_departemen'];
        $nama_departemen = $_POST['nama_departemen'];

        $query = "UPDATE departemen SET kode_departemen = '$kode_departemen', nama_departemen = '$nama_departemen' WHERE id_departemen = '$id_departemen'";

        if (!mysqli_query($conn, $query)) {
            $_SESSION['error_message'] = "Gagal mengubah departemen: " . mysqli_error($conn);
        } else {
            $_SESSION['success_message'] = "Departemen berhasil diubah!";
        }
    }
    header("Location: departemen.php");
    exit();
}

// Proses penghapusan departemen
if (isset($_GET['delete'])) {
    $id_departemen = $_GET['delete'];

    // Cek apakah departemen sudah digunakan di tabel inventaris
    $checkQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_departemen = $id_departemen";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data departemen tidak dapat dihapus karena sudah digunakan pada data inventaris!";
    } else {
        // Hapus data departemen dari database
        $query = "DELETE FROM departemen WHERE id_departemen='$id_departemen'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Departemen berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus departemen: " . mysqli_error($conn);
        }
    }
    header("Location: departemen.php");
    exit();
}
?>