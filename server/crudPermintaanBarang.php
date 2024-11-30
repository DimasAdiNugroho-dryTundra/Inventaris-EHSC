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
    $status = 0;

    $query = "INSERT INTO permintaan_barang (id_departemen, nama_barang, tanggal_permintaan, spesifikasi, kebutuhan_qty, harga_satuan, status) 
    VALUES ('$id_departemen', '$nama_barang', '$tanggal_permintaan', '$spesifikasi', '$kebutuhan_qty', '$harga_satuan', '$status')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Permintaan barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan permintaan barang: " . mysqli_error($conn);
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
    header("Location: permintaanBarang.php");
    exit();
}

// Fungsi untuk menghapus permintaan dan data terkait
function deletePermintaan($conn, $id_permintaan)
{
    // Dapatkan id_penerimaan terkait
    $query = "SELECT id_penerimaan FROM penerimaan_barang WHERE id_permintaan = $id_permintaan";
    $result = mysqli_query($conn, $query);
    $penerimaan = mysqli_fetch_assoc($result);

    if ($penerimaan) {
        $id_penerimaan = $penerimaan['id_penerimaan'];

        // Dapatkan id_inventaris terkait
        $query = "SELECT id_inventaris FROM inventaris WHERE id_penerimaan = $id_penerimaan";
        $result = mysqli_query($conn, $query);
        $inventaris = mysqli_fetch_assoc($result);

        if ($inventaris) {
            $id_inventaris = $inventaris['id_inventaris'];

            // Hapus data dari tabel-tabel terkait
            $tables = [
                'kontrol_barang_cawu_satu',
                'kontrol_barang_cawu_dua',
                'kontrol_barang_cawu_tiga',
                'kerusakan_barang',
                'kehilangan_barang',
                'perpindahan_barang'
            ];

            foreach ($tables as $table) {
                $query = "DELETE FROM $table WHERE id_inventaris = $id_inventaris";
                mysqli_query($conn, $query);
            }

            // Hapus dari inventaris
            $query = "DELETE FROM inventaris WHERE id_inventaris = $id_inventaris";
            mysqli_query($conn, $query);
        }

        // Hapus dari penerimaan_barang
        $query = "DELETE FROM penerimaan_barang WHERE id_permintaan = $id_permintaan";
        mysqli_query($conn, $query);
    }

    // Hapus dari permintaan_barang
    $query = "DELETE FROM permintaan_barang WHERE id_permintaan = $id_permintaan";
    $result = mysqli_query($conn, $query);

    return $result;
}

// Proses penghapusan permintaan barang
if (isset($_GET['delete'])) {
    $id_permintaan = $_GET['delete'];
    if (deletePermintaan($conn, $id_permintaan)) {
        $_SESSION['success_message'] = "Permintaan barang berhasil dihapus beserta data terkait!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus permintaan barang";
    }
    header("Location: permintaanBarang.php");
    exit();
}
?>