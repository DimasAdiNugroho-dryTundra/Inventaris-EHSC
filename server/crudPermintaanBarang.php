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
    // Ubah nama field dari 'tanggal' menjadi 'tanggal_permintaan'
    $tanggal_permintaan = $_POST['tanggal'] ?? date('Y-m-d'); // Tambahkan default value
    $spesifikasi = $_POST['spesifikasi'];
    $kebutuhan_qty = $_POST['kebutuhan_qty'];
    $harga_satuan = str_replace(['Rp', '.', ' '], '', $_POST['harga_satuan']);
    $harga_satuan = (int) $harga_satuan;
    $status = 0; // Default status

    // Validasi tanggal
    if (!empty($tanggal_permintaan)) {
        $query = "INSERT INTO permintaan_barang (id_departemen, nama_barang, tanggal_permintaan, spesifikasi, kebutuhan_qty, harga_satuan, status) 
                  VALUES ('$id_departemen', '$nama_barang', '$tanggal_permintaan', '$spesifikasi', '$kebutuhan_qty', '$harga_satuan', '$status')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Permintaan barang berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan permintaan barang: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Tanggal permintaan tidak boleh kosong!";
    }
    header("Location: permintaanBarang.php");
    exit();
}

// Proses pengeditan permintaan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_permintaan = $_POST['id_permintaan'];
    $id_departemen = $_POST['id_departemen'];
    $nama_barang = $_POST['nama_barang'];
    // Ubah nama field dari 'tanggal' menjadi 'tanggal_permintaan'
    $tanggal_permintaan = $_POST['tanggal'] ?? date('Y-m-d'); // Tambahkan default value
    $spesifikasi = $_POST['spesifikasi'];
    $kebutuhan_qty = $_POST['kebutuhan_qty'];
    $harga_satuan = str_replace(['Rp', '.', ' '], '', $_POST['harga_satuan']);
    $harga_satuan = (int) $harga_satuan;
    $status = $_POST['status'];

    // Validasi tanggal
    if (!empty($tanggal_permintaan)) {
        $query = "UPDATE permintaan_barang SET 
                  id_departemen = '$id_departemen', 
                  nama_barang = '$nama_barang', 
                  tanggal_permintaan = '$tanggal_permintaan', 
                  spesifikasi = '$spesifikasi', 
                  kebutuhan_qty = '$kebutuhan_qty', 
                  harga_satuan = '$harga_satuan', 
                  status = '$status' 
                  WHERE id_permintaan = '$id_permintaan'";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Permintaan barang berhasil diubah!";
        } else {
            $_SESSION['error_message'] = "Gagal mengubah permintaan barang: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Tanggal permintaan tidak boleh kosong!";
    }
    header("Location: permintaanBarang.php");
    exit();
}

// Fungsi untuk menghapus permintaan dan data terkait
function deletePermintaan($conn, $id_permintaan)
{
    mysqli_begin_transaction($conn);

    try {
        // 1. Cek apakah ada inventaris terkait
        $query = "SELECT i.id_inventaris 
                 FROM inventaris i 
                 JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
                 WHERE pb.id_permintaan = '$id_permintaan'";
        $result = mysqli_query($conn, $query);

        // 2. Hapus inventaris terkait jika ada
        while ($row = mysqli_fetch_assoc($result)) {
            deleteInventaris($conn, $row['id_inventaris']);
        }

        // 3. Hapus penerimaan barang
        $query = "DELETE FROM penerimaan_barang WHERE id_permintaan = '$id_permintaan'";
        mysqli_query($conn, $query);

        // 4. Hapus permintaan barang
        $query = "DELETE FROM permintaan_barang WHERE id_permintaan = '$id_permintaan'";
        mysqli_query($conn, $query);

        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

// Proses penghapusan permintaan barang
if (isset($_GET['delete'])) {
    $id_permintaan = $_GET['delete'];
    if (deletePermintaan($conn, $id_permintaan)) {
        $_SESSION['success_message'] = "Permintaan barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus permintaan barang";
    }
    header("Location: permintaanBarang.php");
    exit();
}
?>