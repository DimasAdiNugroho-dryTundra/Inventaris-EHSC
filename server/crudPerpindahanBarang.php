<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT pb.*, i.nama_barang, i.kode_inventaris, r.nama_ruangan 
          FROM perpindahan_barang pb
          JOIN inventaris i ON pb.id_inventaris = i.id_inventaris 
          JOIN ruangan r ON pb.id_ruangan = r.id_ruangan
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
function getPerpindahanBarang($conn)
{
    $query = "SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, i.merk, i.id_kategori,
                     i.tanggal_perolehan, i.id_departemen, k.jumlah_pindah, 
                     'Caturwulan 1' as cawu, k.tanggal_kontrol
              FROM kontrol_barang_cawu_satu k
              JOIN inventaris i ON k.id_inventaris = i.id_inventaris
              LEFT JOIN perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris 
                   AND k.tanggal_kontrol = pb.tanggal_perpindahan
              WHERE k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL
              
              UNION ALL
              
              SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, i.merk, i.id_kategori,
                     i.tanggal_perolehan, i.id_departemen, k.jumlah_pindah, 
                     'Caturwulan 2' as cawu, k.tanggal_kontrol
              FROM kontrol_barang_cawu_dua k
              JOIN inventaris i ON k.id_inventaris = i.id_inventaris
              LEFT JOIN perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris 
                   AND k.tanggal_kontrol = pb.tanggal_perpindahan
              WHERE k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL
              
              UNION ALL
              
              SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, i.merk, i.id_kategori,
                     i.tanggal_perolehan, i.id_departemen, k.jumlah_pindah, 
                     'Caturwulan 3' as cawu, k.tanggal_kontrol
              FROM kontrol_barang_cawu_tiga k
              JOIN inventaris i ON k.id_inventaris = i.id_inventaris
              LEFT JOIN perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris 
                   AND k.tanggal_kontrol = pb.tanggal_perpindahan
              WHERE k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL";
    return mysqli_query($conn, $query);
}

// Proses penambahan perpindahan barang
if (isset($_POST['tambahPerpindahan'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $id_ruangan = $_POST['id_ruangan'];
    $tanggal_perpindahan = $_POST['tanggal_perpindahan'];
    $cawu = $_POST['cawu'];
    $jumlah_perpindahan = $_POST['jumlah_perpindahan'];
    $keterangan = $_POST['keterangan'];

    // 1. Insert ke tabel perpindahan_barang
    $query = "INSERT INTO perpindahan_barang (id_inventaris, id_ruangan, tanggal_perpindahan, 
              cawu, jumlah_perpindahan, keterangan)
              VALUES ('$id_inventaris', '$id_ruangan', '$tanggal_perpindahan', 
              '$cawu', '$jumlah_perpindahan', '$keterangan')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // 2. Get data inventaris yang akan dipindah
        $query = "SELECT * FROM inventaris WHERE id_inventaris = '$id_inventaris'";
        $result = mysqli_query($conn, $query);
        $inventaris = mysqli_fetch_assoc($result);

        // 3. Insert inventaris baru dengan ruangan yang baru
        $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, merk, id_departemen, 
                  id_ruangan, id_kategori, tanggal_perolehan, jumlah_awal, jumlah_akhir, satuan, sumber_inventaris)
                  VALUES (
                      '{$inventaris['kode_inventaris']}',
                      '{$inventaris['nama_barang']}',
                      '{$inventaris['merk']}',
                      '{$inventaris['id_departemen']}',
                      '$id_ruangan',
                      '{$inventaris['id_kategori']}',
                      '{$inventaris['tanggal_perolehan']}',
                      '$jumlah_perpindahan',
                      '$jumlah_perpindahan',
                      '{$inventaris['satuan']}',
                      'Pindah Barang'
                  )";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $_SESSION['success_message'] = "Data perpindahan barang berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan data inventaris baru!";
        }
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan data perpindahan barang!";
    }

    header("Location: perpindahanBarang.php");
    exit();
}

// Proses update perpindahan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_perpindahan_barang = $_POST['id_perpindahan_barang'];
    $id_ruangan = $_POST['id_ruangan'];
    $tanggal_perpindahan = $_POST['tanggal_perpindahan'];
    $cawu = $_POST['cawu'];
    $jumlah_perpindahan = $_POST['jumlah_perpindahan'];
    $keterangan = $_POST['keterangan'];

    // 1. Get data perpindahan barang yang akan diupdate
    $query = "SELECT pb.*, i.id_inventaris as inventaris_baru_id 
             FROM perpindahan_barang pb
             JOIN inventaris i ON i.id_ruangan = pb.id_ruangan 
             WHERE pb.id_perpindahan_barang = '$id_perpindahan_barang'";
    $result = mysqli_query($conn, $query);
    $perpindahan = mysqli_fetch_assoc($result);

    // 2. Update data di tabel perpindahan_barang
    $query = "UPDATE perpindahan_barang SET 
              id_ruangan = '$id_ruangan',
              tanggal_perpindahan = '$tanggal_perpindahan',
              cawu = '$cawu',
              jumlah_perpindahan = '$jumlah_perpindahan',
              keterangan = '$keterangan'
              WHERE id_perpindahan_barang = $id_perpindahan_barang";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // 3. Update data di tabel inventaris untuk barang yang dipindah
        $query = "UPDATE inventaris SET 
                  id_ruangan = '$id_ruangan',
                  jumlah_awal = '$jumlah_perpindahan',
                  jumlah_akhir = '$jumlah_perpindahan'
                  WHERE id_inventaris = '{$perpindahan['inventaris_baru_id']}'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $_SESSION['success_message'] = "Data perpindahan barang berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data inventaris!";
        }
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data perpindahan barang!";
    }

    header("Location: perpindahanBarang.php");
    exit();
}

// Proses delete perpindahan barang
if (isset($_GET['delete'])) {
    $id_perpindahan_barang = $_GET['delete'];

    // 1. Get perpindahan barang data
    $query = "SELECT * FROM perpindahan_barang WHERE id_perpindahan_barang = '$id_perpindahan_barang'";
    $result = mysqli_query($conn, $query);
    $perpindahan = mysqli_fetch_assoc($result);

    if ($result) {
        // 2. Delete inventaris yang terkait dengan perpindahan
        $query = "DELETE FROM inventaris 
                 WHERE id_ruangan = '{$perpindahan['id_ruangan']}' 
                 AND id_inventaris IN (
                     SELECT id_inventaris FROM perpindahan_barang 
                     WHERE id_perpindahan_barang = '$id_perpindahan_barang'
                 )";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // 3. Delete perpindahan barang
            $query = "DELETE FROM perpindahan_barang WHERE id_perpindahan_barang = '$id_perpindahan_barang'";
            $result = mysqli_query($conn, $query);

            if ($result) {
                $_SESSION['success_message'] = "Data perpindahan barang berhasil dihapus!";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data perpindahan barang!";
            }
        } else {
            $_SESSION['error_message'] = "Gagal menghapus data inventaris terkait!";
        }
    } else {
        $_SESSION['error_message'] = "Gagal mendapatkan data perpindahan barang!";
    }

    header("Location: perpindahanBarang.php");
    exit();
}
?>