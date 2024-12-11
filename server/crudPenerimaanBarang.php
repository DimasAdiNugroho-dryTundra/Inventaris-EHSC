<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk menampilkan data penerimaan barang
$query = "SELECT pb.*, d.nama_departemen 
          FROM penerimaan_barang pb
          LEFT JOIN departemen d ON pb.id_departemen = d.id_departemen
          WHERE pb.nama_barang LIKE '%$search%'
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM penerimaan_barang pb
               WHERE pb.nama_barang LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Proses penambahan penerimaan barang
if (isset($_POST['tambahPenerimaan'])) {
    $jenis_input = $_POST['jenis_input'];
    $tanggal_terima = $_POST['tanggal_terima'];
    $status = $_POST['status'];
    $satuan = $_POST['satuan'];

    if ($jenis_input === 'permintaan') {
        $id_permintaan = $_POST['id_permintaan'];

        // Ambil data dari permintaan
        $get_permintaan = "SELECT pb.*, d.id_departemen 
                          FROM permintaan_barang pb 
                          JOIN departemen d ON pb.id_departemen = d.id_departemen 
                          WHERE pb.id_permintaan = '$id_permintaan'";
        $permintaan_result = mysqli_query($conn, $get_permintaan);
        $permintaan_data = mysqli_fetch_assoc($permintaan_result);

        $id_departemen = $permintaan_data['id_departemen'];
        $nama_barang = $permintaan_data['nama_barang'];
        $merk = $permintaan_data['merk'];
        $jumlah = $permintaan_data['jumlah_kebutuhan'];
        $satuan = $permintaan_data['satuan'];
        $sumber_penerimaan = 'Permintaan';

        // Insert data ke penerimaan_barang
        $query = "INSERT INTO penerimaan_barang (id_permintaan, id_departemen, nama_barang, merk, tanggal_terima, jumlah, satuan, status, sumber_penerimaan) 
                  VALUES ('$id_permintaan', '$id_departemen', '$nama_barang', '$merk', '$tanggal_terima', '$jumlah', '$satuan', '$status', '$sumber_penerimaan')";

    } else {
        // Input manual
        $nama_barang = $_POST['nama_barang'];
        $merk = $_POST['merk'];
        $id_departemen = $_POST['id_departemen'];
        $jumlah = $_POST['jumlah'];
        $sumber_penerimaan = 'Pengadaan Kantor';

        $query = "INSERT INTO penerimaan_barang (id_permintaan, id_departemen, nama_barang, merk, tanggal_terima, jumlah, satuan, status, sumber_penerimaan) 
                  VALUES (NULL, '$id_departemen', '$nama_barang', '$merk', '$tanggal_terima', '$jumlah', '$satuan', '$status', '$sumber_penerimaan')";
    }

    mysqli_query($conn, $query);
    $_SESSION['success_message'] = "Penerimaan barang berhasil ditambahkan!";
    
    header("Location: penerimaanBarang.php");
    exit();
}

// Proses update penerimaan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_penerimaan = $_POST['id_penerimaan'];
    $tanggal_terima = $_POST['tanggal_terima'];
    $satuan = $_POST['satuan'];
    $status = $_POST['status'];

    // Cek apakah dari permintaan atau input manual
    $query_cek = "SELECT id_permintaan FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan";
    $hasil_cek = mysqli_query($conn, $query_cek);
    $data_penerimaan = mysqli_fetch_assoc($hasil_cek);

    if ($data_penerimaan['id_permintaan']) {
        // Update untuk data dari permintaan
        $query = "UPDATE penerimaan_barang SET 
                  tanggal_terima = '$tanggal_terima',
                  satuan = '$satuan',
                  status = '$status'
                  WHERE id_penerimaan = $id_penerimaan";
    } else {
        // Update untuk input manual
        $nama_barang = $_POST['nama_barang'];
        $id_departemen = $_POST['id_departemen'];
        $jumlah = $_POST['jumlah'];

        $query = "UPDATE penerimaan_barang SET 
                  nama_barang = '$nama_barang',
                  id_departemen = $id_departemen,
                  tanggal_terima = '$tanggal_terima',
                  jumlah = $jumlah,
                  satuan = '$satuan',
                  status = '$status'
                  WHERE id_penerimaan = $id_penerimaan";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data penerimaan barang berhasil diperbarui!";
    }

    header("Location: penerimaanBarang.php");
    exit();
}

// Fungsi hapus penerimaan
function deletePenerimaan($conn, $id_penerimaan)
{
    // Dapatkan id_inventaris terkait
    $query = "SELECT id_inventaris FROM inventaris WHERE id_penerimaan = $id_penerimaan";
    $result = mysqli_query($conn, $query);
    $inventaris = mysqli_fetch_assoc($result);

    if ($inventaris) {
        $id_inventaris = $inventaris['id_inventaris'];

        // Hapus dari tabel terkait
        $tables = [
            'kontrol_barang_cawu_satu',
            'kontrol_barang_cawu_dua',
            'kontrol_barang_cawu_tiga',
            'kerusakan_barang',
            'kehilangan_barang',
            'perpindahan_barang'
        ];

        foreach ($tables as $table) {
            mysqli_query($conn, "DELETE FROM $table WHERE id_inventaris = $id_inventaris");
        }

        // Hapus dari inventaris
        mysqli_query($conn, "DELETE FROM inventaris WHERE id_inventaris = $id_inventaris");
    }

    // Hapus dari penerimaan_barang
    mysqli_query($conn, "DELETE FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan");
    return true;
}

// Proses hapus penerimaan barang
if (isset($_GET['delete'])) {
    $id_penerimaan = $_GET['delete'];
    if (deletePenerimaan($conn, $id_penerimaan)) {
        $_SESSION['success_message'] = "Penerimaan barang berhasil dihapus!";
    }
    header("Location: penerimaanBarang.php");
    exit();
}
?>