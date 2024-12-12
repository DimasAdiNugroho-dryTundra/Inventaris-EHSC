<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5; // Default ke 5
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT pb.*, d.nama_departemen FROM permintaan_barang pb 
          JOIN departemen d ON pb.id_departemen = d.id_departemen 
          WHERE pb.nama_barang LIKE '%$search%' 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM permintaan_barang WHERE nama_barang LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Proses penambahan permintaan barang
if (isset($_POST['tambahPermintaan'])) {
    $id_departemen = $_POST['id_departemen'];
    $nama_barang = $_POST['nama_barang'];
    $merk = $_POST['merk'];
    $tanggal_permintaan = $_POST['tanggal'] ?? date('Y-m-d');
    $spesifikasi = $_POST['spesifikasi'];
    $jumlah_kebutuhan = $_POST['jumlah_kebutuhan'];
    $satuan = $_POST['satuan'];
    $status = $_POST['status'];

    $query = "INSERT INTO permintaan_barang (id_departemen, nama_barang, merk, tanggal_permintaan, spesifikasi, jumlah_kebutuhan, satuan, status) 
    VALUES ('$id_departemen', '$nama_barang', '$merk', '$tanggal_permintaan', '$spesifikasi', '$jumlah_kebutuhan', '$satuan', '$status')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Permintaan barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan permintaan barang: " . mysqli_error($conn);
    }
    header("Location: permintaanBarang.php");
    exit();
}

// Proses edit
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_permintaan = $_POST['id_permintaan'];

    // Tidak bisa diedit ketika sudah diterima
    $checkQuery = "SELECT COUNT(*) as count FROM penerimaan_barang WHERE id_permintaan = $id_permintaan";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data permintaan barang tidak dapat dihapus karena sudah diproses menjadi penerimaan!";
    } else {
        $id_departemen = $_POST['id_departemen'];
        $nama_barang = $_POST['nama_barang'];
        $merk = $_POST['merk'];
        $tanggal_permintaan = $_POST['tanggal'] ?? date('Y-m-d');
        $spesifikasi = $_POST['spesifikasi'];
        $jumlah_kebutuhan = $_POST['jumlah_kebutuhan'];
        $satuan = $_POST['satuan'];
        $status = $_POST['status'];

        $query = "UPDATE permintaan_barang SET 
                  id_departemen = '$id_departemen', 
                  nama_barang = '$nama_barang', 
                  merk = '$merk', 
                  tanggal_permintaan = '$tanggal_permintaan', 
                  spesifikasi = '$spesifikasi', 
                  jumlah_kebutuhan = '$jumlah_kebutuhan', 
                  satuan = '$satuan', 
                  status = '$status' 
                  WHERE id_permintaan = '$id_permintaan'";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Permintaan barang berhasil diubah!";
        } else {
            $_SESSION['error_message'] = "Gagal mengubah permintaan barang: " . mysqli_error($conn);
        }
    }
    header("Location: permintaanBarang.php");
    exit();
}
 
// Hapus data 
if (isset($_GET['delete'])) {
    $id_permintaan = $_GET['delete'];

    // Cek apakah permintaan sudah memiliki penerimaan
    $checkQuery = "SELECT COUNT(*) as count FROM penerimaan_barang WHERE id_permintaan = $id_permintaan";
    $checkResult = mysqli_query($conn, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        $_SESSION['error_message'] = "Data permintaan barang tidak dapat dihapus karena sudah diproses menjadi penerimaan!";
    } else {
        // Query untuk menghapus permintaan barang
        $deleteQuery = "DELETE FROM permintaan_barang WHERE id_permintaan = $id_permintaan";
        if (mysqli_query($conn, $deleteQuery)) {
            $_SESSION['success_message'] = "Data Permintaan barang berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus permintaan barang: " . mysqli_error($conn);
        }
    }
    header("Location: permintaanBarang.php");
    exit();
}
?>