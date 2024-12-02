<?php
// Handling Pencarian dan Pagination untuk Barang Tersedia
$search = isset($_POST['search']) ? $_POST['search'] : '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query untuk barang inventaris tersedia
$query = "SELECT i.*, d.nama_departemen, k.nama_kategori
          FROM inventaris i
          JOIN departemen d ON i.id_departemen = d.id_departemen  
          JOIN kategori k ON i.id_kategori = k.id_kategori
          WHERE i.jumlah_akhir > 0";

if (!empty($search)) {
    $query .= " AND (i.nama_barang LIKE '%$search%' 
                     OR i.kode_inventaris LIKE '%$search%' 
                     OR d.nama_departemen LIKE '%$search%' 
                     OR k.nama_kategori LIKE '%$search%')";
}

$query .= " ORDER BY i.id_inventaris DESC";

// Hitung total data untuk pagination
$total_records_query = mysqli_query($conn, $query);
$total_records = mysqli_num_rows($total_records_query);
$totalPages = ceil($total_records / $limit);

// Tambahkan LIMIT dan OFFSET ke query utama
$query .= " LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);


// Handling Pencarian dan Pagination untuk Barang Tidak Tersedia
$search_zero = isset($_POST['search_zero']) ? $_POST['search_zero'] : '';
$limit_zero = isset($_GET['limit_zero']) ? (int) $_GET['limit_zero'] : 5;
$page_zero = isset($_GET['page_zero']) ? (int) $_GET['page_zero'] : 1;
$offset_zero = ($page_zero - 1) * $limit_zero;

// Query untuk barang inventaris tidak tersedia
$query_zero = "SELECT i.*, d.nama_departemen, k.nama_kategori
               FROM inventaris i
               JOIN departemen d ON i.id_departemen = d.id_departemen
               JOIN kategori k ON i.id_kategori = k.id_kategori  
               WHERE i.jumlah_akhir = 0";

if (!empty($search_zero)) {
    $query_zero .= " AND (i.nama_barang LIKE '%$search_zero%' 
                         OR i.kode_inventaris LIKE '%$search_zero%' 
                         OR d.nama_departemen LIKE '%$search_zero%' 
                         OR k.nama_kategori LIKE '%$search_zero%')";
}

$query_zero .= " ORDER BY i.id_inventaris DESC";

// Hitung total data untuk pagination
$total_records_zero_query = mysqli_query($conn, $query_zero);
$total_records_zero = mysqli_num_rows($total_records_zero_query);
$totalPages_zero = ceil($total_records_zero / $limit_zero);

// Tambahkan LIMIT dan OFFSET ke query utama
$query_zero .= " LIMIT $offset_zero, $limit_zero";
$result_zero = mysqli_query($conn, $query_zero);

// Function untuk generate kode inventaris
function generateKodeInventaris($conn, $departemen_kode, $kategori_kode)
{
    $year = date('Y');

    // Ambil nomor urut terakhir untuk kombinasi departemen dan kategori ini
    $query = "SELECT MAX(CAST(SUBSTRING_INDEX(kode_inventaris, '/', -1) AS UNSIGNED)) as last_number 
              FROM inventaris 
              WHERE kode_inventaris LIKE '$departemen_kode/$kategori_kode/$year/%'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    $next_number = ($row['last_number'] ?? 0) + 1;
    return sprintf("%s/%s/%s/%03d", $departemen_kode, $kategori_kode, $year, $next_number);
}

// Handling Create
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    // Validasi input dari penerimaan
    if (!empty($_POST['id_penerimaan'])) {
        $id_penerimaan = $_POST['id_penerimaan'];
        $id_kategori = $_POST['id_kategori'];

        // Ambil data penerimaan
        $query_penerimaan = "SELECT * FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan";
        $result_penerimaan = mysqli_query($conn, $query_penerimaan);

        if ($penerimaan = mysqli_fetch_assoc($result_penerimaan)) {
            $id_departemen = $penerimaan['id_departemen'];

            // Ambil kode departemen dan kategori
            $query_dept = "SELECT kode_departemen FROM departemen WHERE id_departemen = $id_departemen";
            $query_kat = "SELECT kode_kategori FROM kategori WHERE id_kategori = $id_kategori";

            $result_dept = mysqli_query($conn, $query_dept);
            $dept = mysqli_fetch_assoc($result_dept);

            $result_kat = mysqli_query($conn, $query_kat);
            $kat = mysqli_fetch_assoc($result_kat);

            if ($dept && $kat) {
                $kode_inventaris = generateKodeInventaris($conn, $dept['kode_departemen'], $kat['kode_kategori']);

                // Query untuk insert
                $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, id_penerimaan,
                         id_departemen, id_kategori, tanggal_perolehan, jumlah_awal,
                         jumlah_akhir, satuan)
                         VALUES ('$kode_inventaris', '{$penerimaan['nama_barang']}', $id_penerimaan,
                         $id_departemen, $id_kategori, '{$penerimaan['tanggal_terima']}',
                         {$penerimaan['jumlah']}, {$penerimaan['jumlah']}, '{$penerimaan['satuan']}')";

                if (mysqli_query($conn, $query)) {
                    $_SESSION['success_message'] = "Data inventaris berhasil ditambahkan!";
                } else {
                    $_SESSION['error_message'] = "Data inventaris gagal ditambahkan!";
                }
            } else {
                $_SESSION['error_message'] = "Silahkan pilih penerimaan barang!";
            }
        } else {
            $_SESSION['error_message'] = "Data penerimaan tidak ditemukan!";
        }
    } else {
        $_SESSION['error_message'] = "Silakan pilih penerimaan barang!";
    }

    header("Location: inventaris.php");
    exit();
}

// Handling Update
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_inventaris = $_POST['id_inventaris'];
    $id_kategori = $_POST['id_kategori'];
    $satuan = $_POST['satuan'];

    // Ambil data inventaris
    $query_inventaris = "SELECT * FROM inventaris WHERE id_inventaris = $id_inventaris";
    $result_inventaris = mysqli_query($conn, $query_inventaris);
    $inventaris = mysqli_fetch_assoc($result_inventaris);

    // Ambil kode departemen dan kategori
    $query_dept = "SELECT kode_departemen FROM departemen WHERE id_departemen = {$inventaris['id_departemen']}";
    $query_kat = "SELECT kode_kategori FROM kategori WHERE id_kategori = $id_kategori";

    $result_dept = mysqli_query($conn, $query_dept);
    $dept = mysqli_fetch_assoc($result_dept);

    $result_kat = mysqli_query($conn, $query_kat);
    $kat = mysqli_fetch_assoc($result_kat);

    if ($dept && $kat) {
        // Generate kode inventaris baru
        $kode_inventaris_baru = generateKodeInventaris($conn, $dept['kode_departemen'], $kat['kode_kategori']);

        // Query untuk update
        $query = "UPDATE inventaris SET
                  kode_inventaris = '$kode_inventaris_baru',
                  id_kategori = $id_kategori,
                  satuan = '$satuan'
                  WHERE id_inventaris = $id_inventaris";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Data inventaris berhasil diupdate!";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Data departemen atau kategori tidak ditemukan!";
    }

    header("Location: inventaris.php");
    exit();
}

// Handling Update
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_inventaris = $_POST['id_inventaris'];
    $id_kategori = $_POST['id_kategori'];
    $satuan = $_POST['satuan'];

    // Cek apakah barang dari penerimaan atau input manual
    $check_query = "SELECT id_penerimaan, nama_barang, id_departemen, tanggal_perolehan, jumlah_awal FROM inventaris WHERE id_inventaris = $id_inventaris";
    $check_result = mysqli_query($conn, $check_query);
    $inventaris = mysqli_fetch_assoc($check_result);

    if ($inventaris['id_penerimaan'] === NULL) {
        // Untuk barang input manual
        $nama_barang = $_POST['nama_barang'];
        $id_departemen = $_POST['id_departemen'];
        $tanggal_perolehan = $_POST['tanggal_perolehan'];
        $jumlah_awal = $_POST['jumlah_awal'];

        // Validasi input
        if (empty($nama_barang) || empty($id_departemen) || empty($id_kategori) || empty($tanggal_perolehan) || empty($jumlah_awal)) {
            $_SESSION['error_message'] = "Semua kolom harus diisi!";
            header("Location: inventaris.php");
            exit();
        }

        // Buat query update
        $query = "UPDATE inventaris SET 
                  nama_barang = '$nama_barang',
                  id_departemen = $id_departemen,
                  id_kategori = $id_kategori,
                  tanggal_perolehan = '$tanggal_perolehan',
                  jumlah_awal = $jumlah_awal,
                  satuan = '$satuan'
                  WHERE id_inventaris = $id_inventaris";
    } else {
        // Untuk barang dari penerimaan
        $query = "UPDATE inventaris SET 
                  id_kategori = $id_kategori,
                  satuan = '$satuan'
                  WHERE id_inventaris = $id_inventaris";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data inventaris berhasil diupdate!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }

    header("Location: inventaris.php");
    exit();
}


// Handling Delete
if (isset($_GET['delete'])) {
    $id_inventaris = $_GET['delete'];

    // Hapus data dari tabel kehilangan_barang yang terkait
    $deleteKehilanganQuery = "DELETE FROM kehilangan_barang WHERE id_inventaris = $id_inventaris";
    mysqli_query($conn, $deleteKehilanganQuery);

    // Hapus data dari tabel kerusakan_barang yang terkait
    $deleteKerusakanQuery = "DELETE FROM kerusakan_barang WHERE id_inventaris = $id_inventaris";
    mysqli_query($conn, $deleteKerusakanQuery);

    // Hapus data dari tabel perpindahan_barang yang terkait
    $deletePerpindahanQuery = "DELETE FROM perpindahan_barang WHERE id_inventaris = $id_inventaris";
    mysqli_query($conn, $deletePerpindahanQuery);

    // Hapus data dari tabel kontrol_barang_cawu_satu, dua, dan tiga yang terkait
    $deleteKontrolSatuQuery = "DELETE FROM kontrol_barang_cawu_satu WHERE id_inventaris = $id_inventaris";
    mysqli_query($conn, $deleteKontrolSatuQuery);

    $deleteKontrolDuaQuery = "DELETE FROM kontrol_barang_cawu_dua WHERE id_inventaris = $id_inventaris";
    mysqli_query($conn, $deleteKontrolDuaQuery);

    $deleteKontrolTigaQuery = "DELETE FROM kontrol_barang_cawu_tiga WHERE id_inventaris = $id_inventaris";
    mysqli_query($conn, $deleteKontrolTigaQuery);

    // Hapus data dari tabel inventaris
    $query = "DELETE FROM inventaris WHERE id_inventaris = $id_inventaris";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data inventaris beserta data terkait berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }

    header("Location: inventaris.php");
    exit();
}
?>