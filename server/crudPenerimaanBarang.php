<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk menampilkan data penerimaan barang dengan join ke permintaan
$query = "SELECT pb.*, d.nama_departemen, p.nama_barang 
          FROM penerimaan_barang pb
          JOIN permintaan_barang p ON pb.id_permintaan = p.id_permintaan
          JOIN departemen d ON p.id_departemen = d.id_departemen
          WHERE p.nama_barang LIKE '%$search%'
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM penerimaan_barang prb
               JOIN permintaan_barang pb ON prb.id_permintaan = pb.id_permintaan
               WHERE pb.nama_barang LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Proses penambahan penerimaan barang
if (isset($_POST['tambahPenerimaan'])) {
    $id_permintaan = $_POST['id_permintaan'];

    // Cek apakah barang sudah pernah diterima
    $check_query = "SELECT * FROM penerimaan_barang WHERE id_permintaan = '$id_permintaan'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error_message'] = "Barang ini sudah pernah diterima!";
    } else {
        // Ambil nama barang dari tabel permintaan
        $get_barang = "SELECT nama_barang FROM permintaan_barang WHERE id_permintaan = '$id_permintaan'";
        $barang_result = mysqli_query($conn, $get_barang);
        $barang_data = mysqli_fetch_assoc($barang_result);
        $nama_barang = $barang_data['nama_barang'];

        $tanggal_terima = $_POST['tanggal_terima'];
        $jumlah = $_POST['jumlah'];
        $satuan = $_POST['satuan'];
        $status = $_POST['status'];

        $query = "INSERT INTO penerimaan_barang (id_permintaan, nama_barang, tanggal_terima, jumlah, satuan, status) 
                  VALUES ('$id_permintaan', '$nama_barang', '$tanggal_terima', '$jumlah', '$satuan', '$status')";

        if (mysqli_query($conn, $query)) {
            // Update status permintaan menjadi selesai
            $update_query = "UPDATE permintaan_barang SET status = 3 WHERE id_permintaan = '$id_permintaan'";
            mysqli_query($conn, $update_query);

            $_SESSION['success_message'] = "Penerimaan barang berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan penerimaan barang: " . mysqli_error($conn);
        }
    }
    header("Location: penerimaanBarang.php");
    exit();
}

// Proses pengeditan penerimaan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_penerimaan = $_POST['id_penerimaan'];
    $tanggal_terima = $_POST['tanggal_terima'];
    $jumlah = $_POST['jumlah'];
    $satuan = $_POST['satuan'];
    $status = $_POST['status'];

    $query = "UPDATE penerimaan_barang SET
              tanggal_terima = '$tanggal_terima',
              jumlah = '$jumlah',
              satuan = '$satuan',
              status = '$status'
              WHERE id_penerimaan = '$id_penerimaan'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Penerimaan barang berhasil diubah!";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah penerimaan barang: " . mysqli_error($conn);
    }
    header("Location: penerimaanBarang.php");
    exit();
}

// Fungsi untuk menghapus penerimaan dan data terkait
function deletePenerimaan($conn, $id_penerimaan)
{
    // Dapatkan id_permintaan terkait
    $query = "SELECT id_permintaan FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan";
    $result = mysqli_query($conn, $query);
    $penerimaan = mysqli_fetch_assoc($result);

    if ($penerimaan) {
        $id_permintaan = $penerimaan['id_permintaan'];

        // Dapatkan id_inventaris terkait
        $query = "SELECT id_inventaris FROM inventaris WHERE id_penerimaan = $id_penerimaan";
        $result = mysqli_query($conn, $query);
        $inventaris = mysqli_fetch_assoc($result);

        if ($inventaris) {
            $id_inventaris = $inventaris['id_inventaris'];

            // Hapus dari tabel yang terkait
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

            // Hapus dari tabel inventaris
            $query = "DELETE FROM inventaris WHERE id_inventaris = $id_inventaris";
            mysqli_query($conn, $query);
        }

        // Hapus dari tabel penerimaan_barang
        $query = "DELETE FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan";
        mysqli_query($conn, $query);
    }

    return true; // atau return mysqli_affected_rows($conn) untuk melihat jumlah baris yang terpengaruh
}

// Proses penghapusan penerimaan barang
if (isset($_GET['delete'])) {
    $id_penerimaan = $_GET['delete'];
    if (deletePenerimaan($conn, $id_penerimaan)) {
        $_SESSION['success_message'] = "Penerimaan barang berhasil dihapus beserta data terkait!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus penerimaan barang";
    }
    header("Location: penerimaanBarang.php");
    exit();
}
?>