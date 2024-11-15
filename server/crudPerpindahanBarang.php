<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT pb.*, i.nama_barang, i.kode_inventaris 
          FROM perpindahan_barang pb
          JOIN inventaris i ON pb.id_inventaris = i.id_inventaris 
          WHERE i.nama_barang LIKE '%$search%' OR i.kode_inventaris LIKE '%$search%'
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM perpindahan_barang pb
               JOIN inventaris i ON pb.id_inventaris = i.id_inventaris 
               WHERE i.nama_barang LIKE '%$search%' OR i.kode_inventaris LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Fungsi untuk mendapatkan data perpindahan barang
function getPerpindahanBarang($conn) {
    $query = "SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, k.jumlah_pindah, 
            'Caturwulan 1' as cawu, k.tanggal_kontrol
        FROM kontrol_barang_cawu_satu k
        JOIN inventaris i ON k.id_inventaris = i.id_inventaris
        LEFT JOIN perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris AND k.tanggal_kontrol = pb.tanggal_perpindahan
        WHERE k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL
        
        UNION ALL
        
        SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, k.jumlah_pindah, 
               'Caturwulan 2' as cawu, k.tanggal_kontrol
        FROM kontrol_barang_cawu_dua k
        JOIN inventaris i ON k.id_inventaris = i.id_inventaris
        LEFT JOIN perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris AND k.tanggal_kontrol = pb.tanggal_perpindahan
        WHERE k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL
        
        UNION ALL
        
        SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, k.jumlah_pindah, 
               'Caturwulan 3' as cawu, k.tanggal_kontrol
        FROM kontrol_barang_cawu_tiga k
        JOIN inventaris i ON k.id_inventaris = i.id_inventaris
        LEFT JOIN perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris AND k.tanggal_kontrol = pb.tanggal_perpindahan
        WHERE k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL";
    return mysqli_query($conn, $query);
}


// Proses penambahan perpindahan barang
if (isset($_POST['tambahPerpindahan'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $tanggal_perpindahan = $_POST['tanggal_perpindahan'];
    $cawu = $_POST['cawu'];
    $jumlah_perpindahan = $_POST['jumlah_perpindahan'];
    $keterangan = $_POST['keterangan'];

    $query = "INSERT INTO perpindahan_barang (id_inventaris, tanggal_perpindahan, cawu, 
              jumlah_perpindahan, keterangan)
              VALUES ('$id_inventaris', '$tanggal_perpindahan', '$cawu', 
              '$jumlah_perpindahan', '$keterangan')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data perpindahan barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan data perpindahan barang: " . mysqli_error($conn);
    }

    header("Location: perpindahanBarang.php");
    exit();
}

// Proses update perpindahan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_perpindahan_barang = $_POST['id_perpindahan_barang'];
    $tanggal_perpindahan = $_POST['tanggal_perpindahan'];
    $cawu = $_POST['cawu'];
    $jumlah_perpindahan = $_POST['jumlah_perpindahan'];
    $keterangan = $_POST['keterangan'];

    $query = "UPDATE perpindahan_barang SET 
              tanggal_perpindahan = '$tanggal_perpindahan',
              cawu = '$cawu',
              jumlah_perpindahan = '$jumlah_perpindahan',
              keterangan = '$keterangan'
              WHERE id_perpindahan_barang = $id_perpindahan_barang";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data perpindahan barang berhasil diperbarui!";
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data perpindahan barang: " . mysqli_error($conn);
    }

    header("Location: perpindahanBarang.php");
    exit();
}

// Proses delete perpindahan barang
if (isset($_GET['delete'])) {
    $id_perpindahan_barang = $_GET['delete'];

    // Hapus data dari database
    $query = "DELETE FROM perpindahan_barang WHERE id_perpindahan_barang = $id_perpindahan_barang";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data perpindahan barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data perpindahan barang: " . mysqli_error($conn);
    }

    header("Location: perpindahanBarang.php");
    exit();
}
?>