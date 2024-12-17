<?php
// Handling Pencarian dan Pagination untuk Barang Tersedia
$search = isset($_POST['search']) ? $_POST['search'] : '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query untuk barang inventaris tersedia
$query = "SELECT i.*, d.*, k.nama_kategori, r.nama_ruangan
          FROM inventaris i
          JOIN departemen d ON i.id_departemen = d.id_departemen  
          JOIN kategori k ON i.id_kategori = k.id_kategori
          JOIN ruangan r ON i.id_ruangan = r.id_ruangan
          WHERE i.jumlah_akhir > 0";

if (!empty($search)) {
    $query .= " AND (i.nama_barang LIKE '%$search%' 
                OR i.kode_inventaris LIKE '%$search%' 
                OR i.merk LIKE '%$search%'
                OR d.nama_departemen LIKE '%$search%' 
                OR r.nama_ruangan LIKE '%$search%' 
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
$query_zero = "SELECT i.*, d.*, k.nama_kategori, r.nama_ruangan
               FROM inventaris i
               JOIN departemen d ON i.id_departemen = d.id_departemen
               JOIN kategori k ON i.id_kategori = k.id_kategori
               JOIN ruangan r ON i.id_ruangan = r.id_ruangan
               WHERE i.jumlah_akhir = 0";

if (!empty($search_zero)) {
    $query_zero .= " AND (i.nama_barang LIKE '%$search_zero%' 
                    OR i.kode_inventaris LIKE '%$search_zero%' 
                    OR i.merk LIKE '%$search_zero%'
                    OR d.nama_departemen LIKE '%$search_zero%' 
                    OR r.nama_ruangan LIKE '%$search_zero%' 
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

// Query mengambil data dari pebnerimaan barang
$penerimaan_query = "SELECT pb.id_penerimaan, pb.nama_barang, pb.merk, pb.tanggal_terima, 
                        d.nama_departemen, pb.jumlah, pb.satuan, pb.sumber_penerimaan 
                        FROM penerimaan_barang pb
                        JOIN departemen d ON pb.id_departemen = d.id_departemen 
                        WHERE pb.id_penerimaan NOT IN (
                        SELECT id_penerimaan FROM inventaris WHERE id_penerimaan IS NOT NULL)
                        ORDER BY pb.id_penerimaan ASC";

// Function untuk generate kode inventaris
function generateKodeInventaris($conn, $departemen_kode, $kategori_kode)
{
    $year = date('Y');

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
        $id_ruangan = $_POST['id_ruangan'];

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
                $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, merk, id_penerimaan,
                         id_departemen, id_ruangan, id_kategori, tanggal_perolehan, jumlah_awal,
                         jumlah_akhir, satuan, sumber_inventaris)
                         VALUES ('$kode_inventaris', '{$penerimaan['nama_barang']}', '{$penerimaan['merk']}', $id_penerimaan,
                         $id_departemen, $id_ruangan, $id_kategori, '{$penerimaan['tanggal_terima']}',
                         {$penerimaan['jumlah']}, {$penerimaan['jumlah']}, '{$penerimaan['satuan']}', '{$penerimaan['sumber_penerimaan']}')";

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
    $id_ruangan = $_POST['id_ruangan'];
    $id_kategori = $_POST['id_kategori'];

    // Cek sumber inventaris
    $check_query = "SELECT sumber_inventaris FROM inventaris WHERE id_inventaris = $id_inventaris";
    $check_result = mysqli_query($conn, $check_query);
    $inventaris_data = mysqli_fetch_assoc($check_result);

    // Jika sumber inventaris adalah "Pindah Barang"
    if ($inventaris_data['sumber_inventaris'] == 'Pindah Barang') {
        $_SESSION['error_message'] = "Data inventaris dari sumber Pindah Barang tidak dapat diedit!";
        header("Location: inventaris.php");
        exit();
    }

    // Cek apakah ada data kontrol untuk inventaris ini
    $checkKontrolQuery = "SELECT 
                            (SELECT COUNT(*) FROM kontrol_barang_cawu_satu WHERE id_inventaris = $id_inventaris) +
                            (SELECT COUNT(*) FROM kontrol_barang_cawu_dua WHERE id_inventaris = $id_inventaris) +
                            (SELECT COUNT(*) FROM kontrol_barang_cawu_tiga WHERE id_inventaris = $id_inventaris) as total_kontrol";

    $checkResult = mysqli_query($conn, $checkKontrolQuery);
    $kontrolData = mysqli_fetch_assoc($checkResult);

    // Keterangan berdasarkan hasil pengecekan
    if ($kontrolData['total_kontrol'] > 0) {
        $_SESSION['error_message'] = "Data inventaris tidak dapat diedit karena masih memiliki data kontrol terkait!";
        header("Location: inventaris.php");
        exit();
    }

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

    // Generate kode inventaris baru
    $kode_inventaris_baru = generateKodeInventaris($conn, $dept['kode_departemen'], $kat['kode_kategori']);

    // Query untuk update
    $query = "UPDATE inventaris SET
              kode_inventaris = '$kode_inventaris_baru',
              id_kategori = $id_kategori,
              id_ruangan = $id_ruangan
              WHERE id_inventaris = $id_inventaris";

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

    // Cek sumber inventaris
    $check_query = "SELECT sumber_inventaris FROM inventaris WHERE id_inventaris = $id_inventaris";
    $check_result = mysqli_query($conn, $check_query);
    $inventaris_data = mysqli_fetch_assoc($check_result);

    // Jika sumber inventaris adalah "Pindah Barang"
    if ($inventaris_data['sumber_inventaris'] == 'Pindah Barang') {
        $_SESSION['error_message'] = "Data inventaris dari sumber Pindah Barang tidak dapat dihapus!";
        header("Location: inventaris.php");
        exit();
    }

    // Cek apakah ada data kontrol untuk inventaris ini
    $checkKontrolQuery = "SELECT 
                            (SELECT COUNT(*) FROM kontrol_barang_cawu_satu WHERE id_inventaris = $id_inventaris) +
                            (SELECT COUNT(*) FROM kontrol_barang_cawu_dua WHERE id_inventaris = $id_inventaris) +
                            (SELECT COUNT(*) FROM kontrol_barang_cawu_tiga WHERE id_inventaris = $id_inventaris) as total_kontrol";

    $checkResult = mysqli_query($conn, $checkKontrolQuery);
    $kontrolData = mysqli_fetch_assoc($checkResult);

    // Keterangan berdasarkan hasil pengecekan
    if ($kontrolData['total_kontrol'] > 0) {
        $_SESSION['error_message'] = "Data inventaris tidak dapat dihapus karena masih memiliki data kontrol terkait!";
        header("Location: inventaris.php");
        exit();
    }

    // Hapus data dari tabel inventaris
    $query = "DELETE FROM inventaris WHERE id_inventaris = $id_inventaris";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data inventaris berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }

    header("Location: inventaris.php");
    exit();
}
?>