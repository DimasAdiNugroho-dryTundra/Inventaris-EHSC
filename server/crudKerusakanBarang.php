<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query tampil data kerusakan barang
$query = "SELECT kb.*, 
          i.nama_barang, 
          i.kode_inventaris, 
          i.merk,
          r.nama_ruangan,
          i.sumber_inventaris,
          d.nama_departemen,
          COALESCE(u1.nama, u2.nama, u3.nama) AS nama_petugas
          FROM kerusakan_barang kb
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris
          JOIN ruangan r ON i.id_ruangan = r.id_ruangan 
          JOIN departemen d ON i.id_departemen = d.id_departemen
          LEFT JOIN kontrol_barang_cawu_satu k1 ON k1.id_inventaris = i.id_inventaris
          LEFT JOIN kontrol_barang_cawu_dua k2 ON k2.id_inventaris = i.id_inventaris
          LEFT JOIN kontrol_barang_cawu_tiga k3 ON k3.id_inventaris = i.id_inventaris
          LEFT JOIN user u1 ON k1.id_user = u1.id_user
          LEFT JOIN user u2 ON k2.id_user = u2.id_user
          LEFT JOIN user u3 ON k3.id_user = u3.id_user
          WHERE (i.nama_barang LIKE '%$search%' 
          OR i.kode_inventaris LIKE '%$search%')
          GROUP BY kb.id_kerusakan_barang
          ORDER BY kb.tanggal_kerusakan DESC
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
function getBarangRusak($conn)
{
    $query = "SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk,          
                i.sumber_inventaris, 
                k.jumlah_rusak, 
                r.nama_ruangan,
                d.nama_departemen,
                i.satuan,        
                'Caturwulan 1' AS cawu, 
                k.tanggal_kontrol,
                u.nama AS nama_petugas
            FROM 
                kontrol_barang_cawu_satu k
            JOIN 
                inventaris i ON k.id_inventaris = i.id_inventaris
            JOIN 
                ruangan r ON i.id_ruangan = r.id_ruangan
            JOIN 
                departemen d ON i.id_departemen = d.id_departemen
            LEFT JOIN 
                user u ON k.id_user = u.id_user
            LEFT JOIN 
                kerusakan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kerusakan
            WHERE 
                k.jumlah_rusak > 0 AND kb.id_inventaris IS NULL
            
            UNION ALL
            
            SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk,
                i.sumber_inventaris,
                k.jumlah_rusak, 
                r.nama_ruangan,
                d.nama_departemen,
                i.satuan,
                'Caturwulan 2' AS cawu, 
                k.tanggal_kontrol,
                u.nama AS nama_petugas
            FROM 
                kontrol_barang_cawu_dua k
            JOIN 
                inventaris i ON k.id_inventaris = i.id_inventaris
            JOIN 
                ruangan r ON i.id_ruangan = r.id_ruangan
            JOIN 
                departemen d ON i.id_departemen = d.id_departemen
            LEFT JOIN 
                user u ON k.id_user = u.id_user
            LEFT JOIN 
                kerusakan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kerusakan
            WHERE 
                k.jumlah_rusak > 0 AND kb.id_inventaris IS NULL
            
            UNION ALL
            
            SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk,
                i.sumber_inventaris,
                k.jumlah_rusak, 
                r.nama_ruangan,
                d.nama_departemen,
                i.satuan,
                'Caturwulan 3' AS cawu, 
                k.tanggal_kontrol,
                u.nama AS nama_petugas
            FROM 
                kontrol_barang_cawu_tiga k
            JOIN 
                inventaris i ON k.id_inventaris = i.id_inventaris
            JOIN 
                ruangan r ON i.id_ruangan = r.id_ruangan
            JOIN 
                departemen d ON i.id_departemen = d.id_departemen
            LEFT JOIN 
                user u ON k.id_user = u.id_user
            LEFT JOIN 
                kerusakan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kerusakan
            WHERE 
                k.jumlah_rusak > 0 AND kb.id_inventaris IS NULL
            ORDER BY tanggal_kontrol ASC
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    return $result;
}

// Fungsi untuk validasi foto
function validasiFoto($file)
{
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png']; // Tipe file yang diperbolehkan
    $maxSize = 2097152;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Tidak ada file yang diupload.";
    }

    if ($file['size'] > $maxSize) {
        return "Ukuran file tidak boleh lebih dari 2MB.";
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return "Tipe file tidak valid. Harus berupa JPG, JPEG, atau PNG.";
    }

    return true;
}

// Proses penambahan kerusakan barang
if (isset($_POST['tambahKerusakan'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $tanggal_kerusakan = $_POST['tanggal_kerusakan'];
    $cawu = $_POST['cawu'];
    $jumlah_kerusakan = $_POST['jumlah_kerusakan'];
    $keterangan = $_POST['keterangan'];

    $foto = $_FILES['foto_kerusakan'];
    $fotoName = '';

    // Validasi foto
    $validationResult = validasiFoto($foto);
    if ($validationResult !== true) {
        $_SESSION['error_message'] = $validationResult;
        header("Location: kerusakanBarang.php");
        exit();
    }

    $uploadDir = '../upload/kerusakan/';
    $fotoName = time() . '_' . basename($foto['name']);

    if (!move_uploaded_file($foto['tmp_name'], $uploadDir . $fotoName)) {
        $_SESSION['error_message'] = "Gagal mengupload foto.";
        header("Location: kerusakanBarang.php");
        exit();
    }

    // Query tambah
    $query = "INSERT INTO kerusakan_barang (id_inventaris, tanggal_kerusakan, cawu, jumlah_kerusakan, foto_kerusakan, keterangan)
              VALUES ('$id_inventaris', '$tanggal_kerusakan', '$cawu', 
              '$jumlah_kerusakan', '$fotoName', '$keterangan')";

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
    $keterangan = $_POST['keterangan'];

    // Ambil foto lama
    $queryFoto = "SELECT foto_kerusakan FROM kerusakan_barang WHERE id_kerusakan_barang = $id_kerusakan_barang";
    $resultFoto = mysqli_query($conn, $queryFoto);
    $rowFoto = mysqli_fetch_assoc($resultFoto);
    $fotoLama = $rowFoto['foto_kerusakan'];

    // Query edit
    $query = "UPDATE kerusakan_barang SET 
              keterangan = '$keterangan'
              WHERE id_kerusakan_barang = $id_kerusakan_barang";

    if (mysqli_query($conn, $query)) {
        // Jika ada foto baru
        if (isset($_FILES['foto_kerusakan']) && $_FILES['foto_kerusakan']['error'] == 0) {
            $foto = $_FILES['foto_kerusakan'];

            // Validasi foto baru
            $validationResult = validasiFoto($foto);
            if ($validationResult !== true) {
                $_SESSION['error_message'] = $validationResult;
                header("Location: kerusakanBarang.php");
                exit();
            }

            $uploadDir = '../upload/kerusakan/';
            $fotoName = time() . '_' . basename($foto['name']);

            if (move_uploaded_file($foto['tmp_name'], $uploadDir . $fotoName)) {
                // Query update foto
                $queryFotoUpdate = "UPDATE kerusakan_barang SET foto_kerusakan = '$fotoName' WHERE id_kerusakan_barang = $id_kerusakan_barang";
                mysqli_query($conn, $queryFotoUpdate);

                // Hapus foto lama
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

// Proses penghapusan kerusakan barang
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

    // Query hapus 
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
// function getDetailBarang($conn, $id_inventaris)
// {
//     $query = "SELECT * FROM inventaris WHERE id_inventaris = $id_inventaris";
//     return mysqli_query($conn, $query);
// }
?>