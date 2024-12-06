<?php
// crudkontrolBarangCawuSatuCawuSatu.php

// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian dan filter
$year = isset($_POST['year']) ? (int) $_POST['year'] : date('Y');
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Tentukan tabel dan rentang tanggal berdasarkan cawu
$table = 'kontrol_barang_cawu_satu';
$idKolom = 'id_kontrol_barang_cawu_satu';
$tanggalMulai = "$year-01-01";
$tanggalAkhir = "$year-04-30";

// Query untuk mengambil data kontrol barang dengan filter tanggal
$query = "SELECT kb.*, 
          i.kode_inventaris, 
          i.nama_barang,
          i.jumlah_akhir,
          u.nama as nama_petugas 
          FROM $table kb 
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
          JOIN user u ON kb.id_user = u.id_user 
          WHERE (i.kode_inventaris LIKE '%$search%' OR i.nama_barang LIKE '%$search%') 
          AND YEAR(kb.tanggal_kontrol) = '$year'
          ORDER BY kb.$idKolom DESC 
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination dengan filter yang sama
$totalQuery = "SELECT COUNT(*) as total FROM $table kb 
               JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
               WHERE (i.kode_inventaris LIKE '%$search%' OR i.nama_barang LIKE '%$search%')
               AND YEAR(kb.tanggal_kontrol) = '$year'";

$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRows = $totalRow['total'];
$totalPages = ceil($totalRows / $limit);

function getAvailableInventaris($conn, $year, $table)
{
    $query = "SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.jumlah_akhir AS jumlah,  -- Menggunakan jumlah_akhir
                i.satuan,
                IFNULL((SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) 
                         FROM $table 
                         WHERE id_inventaris = i.id_inventaris 
                         AND YEAR(tanggal_kontrol) = '$year'), 0) AS jumlah_terkontrol
              FROM inventaris i 
              WHERE (i.jumlah_akhir - IFNULL((SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) 
                                                 FROM $table 
                                                 WHERE id_inventaris = i.id_inventaris 
                                                 AND YEAR(tanggal_kontrol) = '$year'), 0)) > 0
              AND i.jumlah_akhir > 0";
    return mysqli_query($conn, $query);
}

// Fungsi untuk menghitung total kontrol barang
function getTotalkontrolBarangCawuSatu($conn, $table)
{
    $query = "SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) as total FROM $table";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?: 0;
}

function getJumlahTerkontrol($conn, $id_inventaris, $year, $exclude_id = null)
{
    $exclude_clause = $exclude_id ? "AND id_kontrol_barang_cawu_satu != $exclude_id" : "";

    $query = "SELECT COALESCE(SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang), 0) as total 
              FROM kontrol_barang_cawu_satu 
              WHERE id_inventaris = '$id_inventaris' 
              AND YEAR(tanggal_kontrol) = '$year'
              $exclude_clause";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total'];
}

// Proses penambahan kontrol barang
if (isset($_POST['tambahKontrol'])) {
    $id_inventaris = isset($_POST['id_inventaris']) ? (int) $_POST['id_inventaris'] : 0;
    $tanggal_kontrol = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';

    // Validasi input kosong
    if (empty($id_inventaris) || empty($tanggal_kontrol)) {
        $_SESSION['error_message'] = "Data inventaris dan tanggal kontrol harus diisi!";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    $year = date('Y', strtotime($tanggal_kontrol));

    // Inisialisasi dengan nilai default 0 jika tidak ada input
    $jumlah_baik = isset($_POST['jumlah_baik']) ? (int) $_POST['jumlah_baik'] : 0;
    $jumlah_rusak = isset($_POST['jumlah_rusak']) ? (int) $_POST['jumlah_rusak'] : 0;
    $jumlah_pindah = isset($_POST['jumlah_pindah']) ? (int) $_POST['jumlah_pindah'] : 0;
    $jumlah_hilang = isset($_POST['jumlah_hilang']) ? (int) $_POST['jumlah_hilang'] : 0;

    $total_kontrol = $jumlah_baik + $jumlah_rusak + $jumlah_pindah + $jumlah_hilang;

    // Ambil jumlah akhir inventaris
    $query_inventaris = "SELECT jumlah_akhir FROM inventaris WHERE id_inventaris = '$id_inventaris'";
    $result_inventaris = mysqli_query($conn, $query_inventaris);
    $row_inventaris = mysqli_fetch_assoc($result_inventaris);
    $jumlah_akhir = (int) $row_inventaris['jumlah_akhir'];

    // Validasi total kontrol harus sama dengan jumlah akhir
    if ($total_kontrol !== $jumlah_akhir) {
        $_SESSION['error_message'] = "Total kontrol ($total_kontrol) harus sama dengan jumlah barang ($jumlah_akhir)";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Validasi total kontrol tidak boleh 0
    if ($total_kontrol === 0) {
        $_SESSION['error_message'] = "Total jumlah kontrol tidak boleh 0";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Validasi periode tanggal Cawu 1
    if (
        strtotime($tanggal_kontrol) < strtotime($tanggalMulai) ||
        strtotime($tanggal_kontrol) > strtotime($tanggalAkhir)
    ) {
        $_SESSION['error_message'] = "Tanggal harus berada di antara 1 Januari - 30 April $year";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Ambil jumlah yang sudah terkontrol
    $jumlah_terkontrol = getJumlahTerkontrol($conn, $id_inventaris, $year);

    // Validasi jumlah kontrol tidak melebihi sisa yang tersedia
    $sisa_dapat_dikontrol = $jumlah_akhir - $jumlah_terkontrol;
    if ($total_kontrol > $sisa_dapat_dikontrol) {
        $_SESSION['error_message'] = "Total kontrol ($total_kontrol) melebihi jumlah yang tersedia ($sisa_dapat_dikontrol)";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Proses insert data
    $query = "INSERT INTO $table (id_inventaris, id_user, tanggal_kontrol, tahun_kontrol, 
              jumlah_baik, jumlah_rusak, jumlah_pindah, jumlah_hilang) 
              VALUES ('$id_inventaris', '{$_SESSION['id_user']}', '$tanggal_kontrol', '$year',
              '$jumlah_baik', '$jumlah_rusak', '$jumlah_pindah', '$jumlah_hilang')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrolBarangCawuSatu.php");
    exit();
}

// Proses pembaruan kontrol barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kontrol = (int) $_POST['id_kontrol'];
    $tanggal_kontrol = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';

    // Validasi input kosong
    if (empty($id_kontrol) || empty($tanggal_kontrol)) {
        $_SESSION['error_message'] = "ID kontrol dan tanggal harus diisi!";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    $year = date('Y', strtotime($tanggal_kontrol));

    // Inisialisasi dengan nilai default 0 jika tidak ada input
    $jumlah_baik = isset($_POST['jumlah_baik']) ? (int) $_POST['jumlah_baik'] : 0;
    $jumlah_rusak = isset($_POST['jumlah_rusak']) ? (int) $_POST['jumlah_rusak'] : 0;
    $jumlah_pindah = isset($_POST['jumlah_pindah']) ? (int) $_POST['jumlah_pindah'] : 0;
    $jumlah_hilang = isset($_POST['jumlah_hilang']) ? (int) $_POST['jumlah_hilang'] : 0;

    $total_kontrol = $jumlah_baik + $jumlah_rusak + $jumlah_pindah + $jumlah_hilang;

    // Validasi periode tanggal Cawu 1
    if (
        strtotime($tanggal_kontrol) < strtotime($tanggalMulai) ||
        strtotime($tanggal_kontrol) > strtotime($tanggalAkhir)
    ) {
        $_SESSION['error_message'] = "Tanggal harus berada di antara 1 Januari - 30 April $year";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Ambil data kontrol yang akan diupdate dan total jumlah dari database
    $query_kontrol = "SELECT kb.*, 
                     (SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) 
                      FROM $table 
                      WHERE id_inventaris = kb.id_inventaris 
                      AND tahun_kontrol = '$year' 
                      AND $idKolom != '$id_kontrol') as total_other_kontrol
                     FROM $table kb
                     WHERE kb.$idKolom = '$id_kontrol'";
    
    $result_kontrol = mysqli_query($conn, $query_kontrol);

    if (!$result_kontrol || mysqli_num_rows($result_kontrol) === 0) {
        $_SESSION['error_message'] = "Data kontrol barang tidak ditemukan!";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    $data_kontrol = mysqli_fetch_assoc($result_kontrol);
    $total_other_kontrol = (int) ($data_kontrol['total_other_kontrol'] ?? 0);
    
    // Ambil total jumlah dari database (jumlah yang sudah terkontrol)
    $query_total = "SELECT SUM(jumlah_baik + jumlah_rusak + jumlah_pindah + jumlah_hilang) as total_database
                    FROM $table 
                    WHERE id_inventaris = '{$data_kontrol['id_inventaris']}'
                    AND tahun_kontrol = '$year'";
    
    $result_total = mysqli_query($conn, $query_total);
    $row_total = mysqli_fetch_assoc($result_total);
    $total_database = (int) ($row_total['total_database'] ?? 0);

    // Validasi setiap input jumlah tidak boleh negatif
    if ($jumlah_baik < 0 || $jumlah_rusak < 0 || $jumlah_pindah < 0 || $jumlah_hilang < 0) {
        $_SESSION['error_message'] = "Jumlah barang tidak boleh negatif!";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Validasi total kontrol tidak boleh 0
    if ($total_kontrol === 0) {
        $_SESSION['error_message'] = "Total jumlah kontrol tidak boleh 0";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Validasi total dengan jumlah yang sudah ada di database
    $new_total = $total_other_kontrol + $total_kontrol;
    
    if ($new_total < $total_database) {
        $_SESSION['error_message'] = "Total kontrol ($new_total) kurang dari jumlah yang harus dikontrol ($total_database)!";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    if ($new_total > $total_database) {
        $_SESSION['error_message'] = "Total kontrol ($new_total) melebihi jumlah yang harus dikontrol ($total_database)!";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    // Proses update data setelah semua validasi berhasil
    $query = "UPDATE $table SET 
              tanggal_kontrol = '$tanggal_kontrol',
              tahun_kontrol = '$year',
              jumlah_baik = '$jumlah_baik',
              jumlah_rusak = '$jumlah_rusak',
              jumlah_pindah = '$jumlah_pindah',
              jumlah_hilang = '$jumlah_hilang'
              WHERE $idKolom = '$id_kontrol'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil diubah!";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrolBarangCawuSatu.php");
    exit();
}

// Proses delete kontrol barang
if (isset($_GET['delete'])) {
    $id_kontrol = (int) $_GET['delete'];

    if (empty($id_kontrol)) {
        $_SESSION['error_message'] = "ID kontrol tidak valid!";
        header("Location: kontrolBarangCawuSatu.php");
        exit();
    }

    $query = "DELETE FROM $table WHERE $idKolom = '$id_kontrol'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Kontrol barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus kontrol barang: " . mysqli_error($conn);
    }

    header("Location: kontrolBarangCawuSatu.php");
    exit();
}
?>