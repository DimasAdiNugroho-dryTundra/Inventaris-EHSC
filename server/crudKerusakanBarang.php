<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT kb.*, i.nama_barang, i.kode_inventaris 
          FROM kerusakan_barang kb
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
          WHERE i.nama_barang LIKE '%$search%' OR i.kode_inventaris LIKE '%$search%'
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM kerusakan_barang kb
               JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
               WHERE i.nama_barang LIKE '%$search%' OR i.kode_inventaris LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Fungsi untuk mendapatkan data barang rusak dari semua cawu
function getBarangRusak($conn) {
    $query = "SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, k.jumlah_rusak, 
            'Caturwulan 1' as cawu, k.tanggal_kontrol
        FROM kontrol_barang_cawu_satu k
        JOIN inventaris i ON k.id_inventaris = i.id_inventaris
        LEFT JOIN kerusakan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kerusakan
        WHERE k.jumlah_rusak > 0 AND kb.id_inventaris IS NULL
        
        UNION ALL
        
        SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, k.jumlah_rusak, 
               'Caturwulan 2' as cawu, k.tanggal_kontrol
        FROM kontrol_barang_cawu_dua k
        JOIN inventaris i ON k.id_inventaris = i.id_inventaris
        LEFT JOIN kerusakan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kerusakan
        WHERE k.jumlah_rusak > 0 AND kb.id_inventaris IS NULL
        
        UNION ALL
        
        SELECT i.id_inventaris, i.kode_inventaris, i.nama_barang, k.jumlah_rusak, 
               'Caturwulan 3' as cawu, k.tanggal_kontrol
        FROM kontrol_barang_cawu_tiga k
        JOIN inventaris i ON k.id_inventaris = i.id_inventaris
        LEFT JOIN kerusakan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kerusakan
        WHERE k.jumlah_rusak > 0 AND kb.id_inventaris IS NULL";

    return mysqli_query($conn, $query);
}


// Proses penambahan kerusakan barang
if (isset($_POST['tambahKerusakan'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $tanggal_kerusakan = $_POST['tanggal_kerusakan'];
    $cawu = $_POST['cawu'];
    $jumlah_kerusakan = $_POST['jumlah_kerusakan'];
    $keterangan = $_POST['keterangan'];
    
    // Upload foto
    $foto = $_FILES['foto_kerusakan'];
    $fotoName = '';
    
    if ($foto['error'] == 0) {
        $uploadDir = '../upload/kerusakan/';
        $fotoName = time() . '_' . basename($foto['name']);
        
        if (!move_uploaded_file($foto['tmp_name'], $uploadDir . $fotoName)) {
            $_SESSION['error_message'] = "Gagal mengupload foto.";
            header("Location: kerusakanBarang.php");
            exit();
        }
    }
    
    $query = "INSERT INTO kerusakan_barang (id_inventaris, tanggal_kerusakan, cawu, jumlah_kerusakan, foto_kerusakan, keterangan)
              VALUES ('$id_inventaris', '$tanggal_kerusakan', '$cawu', '$jumlah_kerusakan', '$fotoName', '$keterangan')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data kerusakan barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan data kerusakan barang: " . mysqli_error($conn);
    }
    
    header("Location: kerusakanBarang.php");
    exit();
}

// Proses update kerusakan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kerusakan_barang = $_POST['id_kerusakan_barang'];
    $tanggal_kerusakan = $_POST['tanggal_kerusakan'];
    $cawu = $_POST['cawu'];
    $jumlah_kerusakan = $_POST['jumlah_kerusakan'];
    $keterangan = $_POST['keterangan'];

    // Ambil foto lama
    $queryFoto = "SELECT foto_kerusakan FROM kerusakan_barang WHERE id_kerusakan_barang = $id_kerusakan_barang";
    $resultFoto = mysqli_query($conn, $queryFoto);
    $rowFoto = mysqli_fetch_assoc($resultFoto);
    $fotoLama = $rowFoto['foto_kerusakan'];

    $query = "UPDATE kerusakan_barang SET 
              tanggal_kerusakan = '$tanggal_kerusakan',
              cawu = '$cawu',
              jumlah_kerusakan = '$jumlah_kerusakan',
              keterangan = '$keterangan'
              WHERE id_kerusakan_barang = $id_kerusakan_barang";

    if (mysqli_query($conn, $query)) {
        // Jika ada foto baru
        if (isset($_FILES['foto_kerusakan']) && $_FILES['foto_kerusakan']['error'] == 0) {
            $foto = $_FILES['foto_kerusakan'];
            $uploadDir = '../upload/kerusakan/';
            $fotoName = time() . '_' . basename($foto['name']);

            if (move_uploaded_file($foto['tmp_name'], $uploadDir . $fotoName)) {
                // Update foto di database
                $queryFotoUpdate = "UPDATE kerusakan_barang SET foto_kerusakan = '$fotoName' WHERE id_kerusakan_barang = $id_kerusakan_barang";
                mysqli_query($conn, $queryFotoUpdate);

                // Hapus foto lama dari server
                if ($fotoLama && file_exists($uploadDir . $fotoLama)) {
                    unlink($uploadDir . $fotoLama);
                }
            }
        }

        $_SESSION['success_message'] = "Data kerusakan barang berhasil diperbarui!";
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data kerusakan barang: " . mysqli_error($conn);
    }

    header("Location: kerusakanBarang.php");
    exit();
}
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kerusakan_barang = $_POST['id_kerusakan_barang'];
    $tanggal_kerusakan = $_POST['tanggal_kerusakan'];
    $cawu = $_POST['cawu'];
    $jumlah_kerusakan = $_POST['jumlah_kerusakan'];
    $keterangan = $_POST['keterangan'];
    
    $query = "UPDATE kerusakan_barang SET 
              tanggal_kerusakan = '$tanggal_kerusakan',
              cawu = '$cawu',
              jumlah_kerusakan = '$jumlah_kerusakan',
              keterangan = '$keterangan'
              WHERE id_kerusakan_barang = $id_kerusakan_barang";
    
    if (mysqli_query($conn, $query)) {
        // Jika ada foto baru
        if (isset($_FILES['foto_kerusakan']) && $_FILES['foto_kerusakan']['error'] == 0) {
            $foto = $_FILES['foto_kerusakan'];
            $uploadDir = '../upload/kerusakan/';
            $fotoName = time() . '_' . basename($foto['name']);
            
            if (move_uploaded_file($foto['tmp_name'], $uploadDir . $fotoName)) {
                // Update foto di database
                $queryFoto = "UPDATE kerusakan_barang SET foto_kerusakan = '$fotoName' WHERE id_kerusakan_barang = $id_kerusakan_barang";
                mysqli_query($conn, $queryFoto);
            }
        }
        
        $_SESSION['success_message'] = "Data kerusakan barang berhasil diperbarui!";
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data kerusakan barang: " . mysqli_error($conn);
    }
    
    header("Location: kerusakanBarang.php");
    exit();
}

// Proses delete kerusakan barang
if (isset($_GET['delete'])) {
    $id_kerusakan_barang = $_GET['delete'];
    
    // Ambil info foto sebelum menghapus
    $queryFoto = "SELECT foto_kerusakan FROM kerusakan_barang WHERE id_kerusakan_barang = $id_kerusakan_barang";
    $resultFoto = mysqli_query($conn, $queryFoto);
    $row = mysqli_fetch_assoc($resultFoto);
    
    // Hapus file foto jika ada
    if ($row && !empty($row['foto_kerusakan'])) {
        $fotoPath = '../upload/kerusakan/' . $row['foto_kerusakan'];
        if (file_exists($fotoPath)) {
            unlink($fotoPath);
        }
    }
    
    // Hapus data dari database
    $query = "DELETE FROM kerusakan_barang WHERE id_kerusakan_barang = $id_kerusakan_barang";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data kerusakan barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data kerusakan barang: " . mysqli_error($conn);
    }
    
    header("Location: kerusakanBarang.php");
    exit();
}

// Fungsi untuk mendapatkan detail barang berdasarkan ID
function getDetailBarang($conn, $id_inventaris) {
    $query = "SELECT * FROM inventaris WHERE id_inventaris = $id_inventaris";
    return mysqli_query($conn, $query);
}
?>