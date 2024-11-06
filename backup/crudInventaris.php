<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk menampilkan data inventaris dengan join ke departemen dan kategori
$query = "SELECT i.*, d.nama_departemen, k.nama_kategori, pb.nama_barang 
          FROM inventaris i
          JOIN departemen d ON i.id_departemen = d.id_departemen
          JOIN kategori k ON i.id_kategori = k.id_kategori
          JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
          WHERE pb.nama_barang LIKE '%$search%'
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM inventaris i
               JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
               WHERE pb.nama_barang LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Fungsi untuk generate kode inventaris
// function generateKodeInventaris($conn, $kode_departemen, $kode_kategori)
// {
//     $tahun = date('Y');
//     $query = "SELECT MAX(SUBSTRING_INDEX(kode_inventaris, '/', -1)) as max_num 
//               FROM inventaris 
//               WHERE kode_inventaris LIKE '$kode_departemen/$kode_kategori/$tahun/%'";
//     $result = mysqli_query($conn, $query);
//     $row = mysqli_fetch_assoc($result);
//     $nextNum = $row['max_num'] ? $row['max_num'] + 1 : 1;
//     return "$kode_departemen/$kode_kategori/$tahun/" . sprintf('%03d', $nextNum);
// }

// Fungsi untuk generate kode inventaris
function generateKodeInventaris($conn, $kode_departemen, $kode_kategori, $id_penerimaan)
{
    // Ambil tahun dari tabel penerimaan_barang
    $queryTahun = "SELECT YEAR(tanggal_terima) AS tahun 
                   FROM penerimaan_barang 
                   WHERE id_penerimaan = $id_penerimaan";
    $resultTahun = mysqli_query($conn, $queryTahun);
    $rowTahun = mysqli_fetch_assoc($resultTahun);
    $tahun = $rowTahun['tahun'] ?? date('Y'); // Gunakan tahun dari tabel atau tahun sekarang jika tidak ditemukan

    // Query untuk mencari nomor inventaris berikutnya
    $query = "SELECT MAX(SUBSTRING_INDEX(kode_inventaris, '/', -1)) as max_num 
              FROM inventaris 
              WHERE kode_inventaris LIKE '$kode_departemen/$kode_kategori/$tahun/%'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $nextNum = $row['max_num'] ? $row['max_num'] + 1 : 1;

    // Kode inventaris yang dihasilkan
    return "$kode_departemen/$kode_kategori/$tahun/" . sprintf('%03d', $nextNum);
}



// Proses penambahan inventaris
if (isset($_POST['tambahInventaris'])) {
    $id_penerimaan = $_POST['id_penerimaan'];
    $id_departemen = $_POST['id_departemen'];
    $id_kategori = $_POST['id_kategori'];

    // Ambil kode departemen dan kategori
    $dept_query = "SELECT kode_departemen FROM departemen WHERE id_departemen = '$id_departemen'";
    $kat_query = "SELECT kode_kategori FROM kategori WHERE id_kategori = '$id_kategori'";

    $dept_result = mysqli_query($conn, $dept_query);
    $kat_result = mysqli_query($conn, $kat_query);

    $dept_data = mysqli_fetch_assoc($dept_result);
    $kat_data = mysqli_fetch_assoc($kat_result);

    // Generate kode inventaris
    $kode_inventaris = generateKodeInventaris($conn, $dept_data['kode_departemen'], $kat_data['kode_kategori'], $id_penerimaan);

    // Ambil data dari penerimaan barang
    $penerimaan_query = "SELECT nama_barang, jumlah, satuan FROM penerimaan_barang WHERE id_penerimaan = '$id_penerimaan'";
    $penerimaan_result = mysqli_query($conn, $penerimaan_query);
    $penerimaan_data = mysqli_fetch_assoc($penerimaan_result);

    $nama_barang = $penerimaan_data['nama_barang'];
    $jumlah = $penerimaan_data['jumlah'];
    $satuan = $penerimaan_data['satuan'];

    $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, id_penerimaan, id_departemen, id_kategori, jumlah, satuan)
              VALUES ('$kode_inventaris', '$nama_barang', '$id_penerimaan', '$id_departemen', '$id_kategori', '$jumlah', '$satuan')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Inventaris berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan inventaris: " . mysqli_error($conn);
    }
    header("Location: inventaris.php");
    exit();
}

// Proses pengeditan inventaris
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_inventaris = $_POST['id_inventaris'];
    $id_kategori = $_POST['id_kategori'];
    $satuan = $_POST['satuan'];

    // Ambil data inventaris sekarang
    $current_query = "SELECT i.*, d.kode_departemen 
                     FROM inventaris i 
                     JOIN departemen d ON i.id_departemen = d.id_departemen 
                     WHERE i.id_inventaris = '$id_inventaris'";
    $current_result = mysqli_query($conn, $current_query);
    $current_data = mysqli_fetch_assoc($current_result);

    // Ambil kode kategori baru
    $kat_query = "SELECT kode_kategori FROM kategori WHERE id_kategori = '$id_kategori'";
    $kat_result = mysqli_query($conn, $kat_query);
    $kat_data = mysqli_fetch_assoc($kat_result);

    // Generate kode inventaris baru
    $tahun = date('Y', strtotime($current_data['tahun_perolehan'])); // Pastikan ada kolom tahun_perolehan atau ambil dari tanggal lain
    $urutan = substr($current_data['kode_inventaris'], strrpos($current_data['kode_inventaris'], '/') + 1);
    $kode_inventaris = $current_data['kode_departemen'] . "/" . $kat_data['kode_kategori'] . "/$tahun/" . $urutan;

    $query = "UPDATE inventaris SET 
              kode_inventaris = '$kode_inventaris',
              id_kategori = '$id_kategori',
              satuan = '$satuan'
              WHERE id_inventaris = '$id_inventaris'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Inventaris berhasil diubah!";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah inventaris: " . mysqli_error($conn);
    }
    header("Location: inventaris.php");
    exit();
}

function deleteInventaris($conn, $id_inventaris)
{
    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Hapus data di tabel kehilangan_barang
        $query1 = "DELETE kb FROM kehilangan_barang kb
                  JOIN kontrol_barang kb2 ON kb.id_kontrol_barang = kb2.id_kontrol_barang
                  WHERE kb2.id_invetaris = '$id_inventaris'";
        mysqli_query($conn, $query1);

        // 2. Hapus data di tabel kerusakan_barang
        $query2 = "DELETE krb FROM kerusakan_barang krb
                  JOIN kontrol_barang kb2 ON krb.id_kontrol_barang = kb2.id_kontrol_barang
                  WHERE kb2.id_invetaris = '$id_inventaris'";
        mysqli_query($conn, $query2);

        // 3. Hapus data di tabel perpindahan_barang
        $query3 = "DELETE pb FROM perpindahan_barang pb
                  JOIN kontrol_barang kb2 ON pb.id_kontrol_barang = kb2.id_kontrol_barang
                  WHERE kb2.id_invetaris = '$id_inventaris'";
        mysqli_query($conn, $query3);

        // 4. Hapus data di tabel kontrol_barang
        $query4 = "DELETE FROM kontrol_barang WHERE id_invetaris = '$id_inventaris'";
        mysqli_query($conn, $query4);

        // 5. Hapus data di tabel arsip_inventaris
        $query5 = "DELETE FROM arsip_inventaris WHERE id_inventaris = '$id_inventaris'";
        mysqli_query($conn, $query5);

        // 6. Hapus data di tabel inventaris
        $query6 = "DELETE FROM inventaris WHERE id_inventaris = '$id_inventaris'";
        mysqli_query($conn, $query6);

        // Commit transaction
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        // Rollback transaction jika terjadi error
        mysqli_rollback($conn);
        return false;
    }
}

// Proses penghapusan inventaris
if (isset($_GET['delete'])) {
    $id_inventaris = $_GET['delete'];
    if (deleteInventaris($conn, $id_inventaris)) {
        $_SESSION['success_message'] = "Inventaris berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus inventaris";
    }
    header("Location: inventaris.php");
    exit();
}
?>