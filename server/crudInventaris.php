<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk menampilkan data inventaris dengan join ke departemen dan kategori
$query = "SELECT i.*, 
         d.nama_departemen, 
         k.nama_kategori
         FROM inventaris i
         JOIN departemen d ON i.id_departemen = d.id_departemen
         JOIN kategori k ON i.id_kategori = k.id_kategori";

// Tambahkan kondisi pencarian jika ada
if (!empty($search)) {
    $query .= " WHERE i.nama_barang LIKE '%$search%'
                OR i.kode_inventaris LIKE '%$search%'
                OR d.nama_departemen LIKE '%$search%'
                OR k.nama_kategori LIKE '%$search%'";
}

// Hitung total data untuk pagination
$total_records_query = mysqli_query($conn, $query);
$total_records = mysqli_num_rows($total_records_query);
$totalPages = ceil($total_records / $limit);

// Tambahkan LIMIT dan OFFSET ke query utama
$query .= " LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Function untuk generate kode inventaris
function generateKodeInventaris($conn, $departemen_kode, $kategori_kode) {
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
    $jenis_input = $_POST['jenis_input'];
    
    if ($jenis_input == 'penerimaan') {
        // Input dari penerimaan
        $id_penerimaan = $_POST['id_penerimaan'];
        $id_departemen = $_POST['id_departemen'];
        $id_kategori = $_POST['id_kategori'];
        
        // Ambil data penerimaan
        $query_penerimaan = "SELECT * FROM penerimaan_barang WHERE id_penerimaan = $id_penerimaan";
        $result_penerimaan = mysqli_query($conn, $query_penerimaan);
        $penerimaan = mysqli_fetch_assoc($result_penerimaan);
        
        // Ambil kode departemen dan kategori
        $query_dept = "SELECT kode_departemen FROM departemen WHERE id_departemen = $id_departemen";
        $query_kat = "SELECT kode_kategori FROM kategori WHERE id_kategori = $id_kategori";
        
        $result_dept = mysqli_query($conn, $query_dept);
        $result_kat = mysqli_query($conn, $query_kat);
        
        $dept = mysqli_fetch_assoc($result_dept);
        $kat = mysqli_fetch_assoc($result_kat);
        
        $kode_inventaris = generateKodeInventaris($conn, $dept['kode_departemen'], $kat['kode_kategori']);
        
        $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, id_penerimaan, id_departemen, 
                  id_kategori, tanggal_perolehan, jumlah_awal, jumlah_akhir, satuan) 
                  VALUES ('$kode_inventaris', '{$penerimaan['nama_barang']}', $id_penerimaan, 
                  $id_departemen, $id_kategori, '{$penerimaan['tanggal_terima']}', 
                  {$penerimaan['jumlah']}, {$penerimaan['jumlah']}, '{$penerimaan['satuan']}')";
    } else {
        // Input manual
        $nama_barang = $_POST['nama_barang'];
        $id_departemen = $_POST['id_departemen'];
        $id_kategori = $_POST['id_kategori'];
        $tanggal_perolehan = $_POST['tanggal_perolehan'];
        $jumlah_awal = $_POST['jumlah_awal'];
        $satuan = $_POST['satuan'];
        
        // Ambil kode departemen dan kategori
        $query_dept = "SELECT kode_departemen FROM departemen WHERE id_departemen = $id_departemen";
        $query_kat = "SELECT kode_kategori FROM kategori WHERE id_kategori = $id_kategori";
        
        $result_dept = mysqli_query($conn, $query_dept);
        $result_kat = mysqli_query($conn, $query_kat);
        
        $dept = mysqli_fetch_assoc($result_dept);
        $kat = mysqli_fetch_assoc($result_kat);
        
        $kode_inventaris = generateKodeInventaris($conn, $dept['kode_departemen'], $kat['kode_kategori']);
        
        $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, id_departemen, id_kategori, 
                  tanggal_perolehan, jumlah_awal, jumlah_akhir, satuan) 
                  VALUES ('$kode_inventaris', '$nama_barang', $id_departemen, $id_kategori, 
                  '$tanggal_perolehan', $jumlah_awal, $jumlah_awal, '$satuan')";
    }
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data inventaris berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
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
    $check_query = "SELECT id_penerimaan FROM inventaris WHERE id_inventaris = $id_inventaris";
    $check_result = mysqli_query($conn, $check_query);
    $inventaris = mysqli_fetch_assoc($check_result);
    
    if ($inventaris['id_penerimaan'] === NULL) {
        // Untuk barang input manual
        $nama_barang = $_POST['nama_barang'];
        $id_departemen = $_POST['id_departemen'];
        $tanggal_perolehan = $_POST['tanggal_perolehan'];
        $jumlah_awal = $_POST['jumlah_awal'];
        
        $query = "UPDATE inventaris SET 
                  nama_barang = '$nama_barang',
                  id_departemen = $id_departemen,
                  id_kategori = $id_kategori,
                  tanggal_perolehan = '$tanggal_perolehan',
                  jumlah_awal = $jumlah_awal,
                  jumlah_akhir = $jumlah_awal,
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
    
    // Cek apakah ada data terkait di tabel kontrol
    $check_query = "SELECT COUNT(*) as count FROM (
        SELECT id_inventaris FROM kontrol_barang_cawu_satu WHERE id_inventaris = $id_inventaris
        UNION ALL
        SELECT id_inventaris FROM kontrol_barang_cawu_dua WHERE id_inventaris = $id_inventaris
        UNION ALL
        SELECT id_inventaris FROM kontrol_barang_cawu_tiga WHERE id_inventaris = $id_inventaris
    ) as combined_kontrol";
    
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['count'] > 0) {
        $_SESSION['error_message'] = "Data inventaris tidak dapat dihapus karena masih memiliki data kontrol terkait!";
    } else {
        $query = "DELETE FROM inventaris WHERE id_inventaris = $id_inventaris";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Data inventaris berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
    }
    
    header("Location: inventaris.php");
    exit();
}
?>