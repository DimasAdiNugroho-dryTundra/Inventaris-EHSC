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
          ORDER BY pb.id_penerimaan DESC
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
    $satuan = $_POST['satuan'];

    if ($jenis_input === 'permintaan') {
        $id_permintaan = $_POST['id_permintaan'];

        // Ambil data dari permintaan
        $get_permintaan = "SELECT pb.*, d.id_departemen 
                          FROM permintaan_barang pb 
                          JOIN departemen d ON pb.id_departemen = d.id_departemen 
                          WHERE pb.id_permintaan = '$id_permintaan'
                          ORDER BY pb.tanggal_permintaan DESC";
        $permintaan_result = mysqli_query($conn, $get_permintaan);
        $permintaan_data = mysqli_fetch_assoc($permintaan_result);

        $id_departemen = $permintaan_data['id_departemen'];
        $nama_barang = $permintaan_data['nama_barang'];
        $merk = $permintaan_data['merk'];
        $jumlah = $permintaan_data['jumlah_kebutuhan'];
        $satuan = $permintaan_data['satuan'];
        $sumber_penerimaan = 'Permintaan';

        $query = "INSERT INTO penerimaan_barang (id_permintaan, id_departemen, nama_barang, merk, tanggal_terima, jumlah, satuan, sumber_penerimaan) 
                  VALUES ('$id_permintaan', '$id_departemen', '$nama_barang', '$merk', '$tanggal_terima', '$jumlah', '$satuan', '$sumber_penerimaan')";
    } else {
        // Input manual dari pengadaan kantor
        $nama_barang = $_POST['nama_barang'];
        $merk = $_POST['merk'];
        $id_departemen = $_POST['id_departemen'];
        $jumlah = $_POST['jumlah'];
        $sumber_penerimaan = 'Pengadaan Kantor';

        $query = "INSERT INTO penerimaan_barang (id_permintaan, id_departemen, nama_barang, merk, tanggal_terima, jumlah, satuan, sumber_penerimaan) 
                  VALUES (NULL, '$id_departemen', '$nama_barang', '$merk', '$tanggal_terima', '$jumlah', '$satuan', '$sumber_penerimaan')";
    }

    mysqli_query($conn, $query);
    $_SESSION['success_message'] = "Penerimaan barang berhasil ditambahkan!";

    header("Location: penerimaanBarang.php");
    exit();
}

// Proses edit
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_penerimaan = $_POST['id_penerimaan'];
    $tanggal_terima = $_POST['tanggal_terima'];
    $satuan = $_POST['satuan'];

    // Tidak bisa diedit jika sudah ada di daftar inventaris
    $checkInventarisQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_penerimaan = $id_penerimaan";
    $checkInventarisResult = mysqli_query($conn, $checkInventarisQuery);
    $checkInventarisRow = mysqli_fetch_assoc($checkInventarisResult);

    if ($checkInventarisRow['count'] > 0) {
        $_SESSION['error_message'] = "Data penerimaan barang tidak dapat diubah karena sudah ada di inventaris!";
    } else {
        // Cek apakah dari permintaan atau input manual
        $query_cek = "SELECT id_permintaan FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan";
        $hasil_cek = mysqli_query($conn, $query_cek);
        $data_penerimaan = mysqli_fetch_assoc($hasil_cek);

        if ($data_penerimaan['id_permintaan']) {
            // Edit untuk data dari permintaan
            $query = "UPDATE penerimaan_barang SET 
                      tanggal_terima = '$tanggal_terima',
                      satuan = '$satuan'
                      WHERE id_penerimaan = $id_penerimaan";
        } else {
            // Edit untuk input manual dari pengadaan kantor
            $nama_barang = $_POST['nama_barang'];
            $merk = $_POST['merk'];
            $id_departemen = $_POST['id_departemen'];
            $jumlah = $_POST['jumlah'];

            $query = "UPDATE penerimaan_barang SET 
                      nama_barang = '$nama_barang',
                      merk = '$merk',
                      id_departemen = $id_departemen,
                      tanggal_terima = '$tanggal_terima',
                      jumlah = $jumlah,
                      satuan = '$satuan'
                      WHERE id_penerimaan = $id_penerimaan";
        }

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Data penerimaan barang berhasil diperbarui!";
        }
    }

    header("Location: penerimaanBarang.php");
    exit();
}

// Proses penghapusan penerimaan barang
if (isset($_GET['delete'])) {
    $id_penerimaan = $_GET['delete'];

    // Tidak bisa dihapus jika ada di data inventaris
    $checkInventarisQuery = "SELECT COUNT(*) as count FROM inventaris WHERE id_penerimaan = $id_penerimaan";
    $checkInventarisResult = mysqli_query($conn, $checkInventarisQuery);
    $checkInventarisRow = mysqli_fetch_assoc($checkInventarisResult);

    if ($checkInventarisRow['count'] > 0) {
        $_SESSION['error_message'] = "Data penerimaan barang tidak dapat dihapus karena sudah ada di inventaris!";
    } else {
        // Hapus dari penerimaan_barang jika tidak ada data di inventaris
        $deleteQuery = "DELETE FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan";
        if (mysqli_query($conn, $deleteQuery)) {
            $_SESSION['success_message'] = "Penerimaan barang berhasil dihapus!";
        }
    }

    header("Location: penerimaanBarang.php");
    exit();
}
?>