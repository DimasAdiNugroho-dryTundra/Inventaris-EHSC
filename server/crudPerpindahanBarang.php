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

function generateKodeInventarisPindah($conn, $departemen_kode, $kategori_kode, $year)
{
    $query = "SELECT MAX(CAST(SUBSTRING_INDEX(kode_inventaris, '/', -1) AS UNSIGNED)) as angka_terakhir 
              FROM inventaris 
              WHERE kode_inventaris LIKE '$departemen_kode/$kategori_kode/$year/%'";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $angka_berikutnya = ($row['angka_terakhir'] ?? 0) + 1;

    return sprintf(
        "%s/%s/%s/%03d",
        $departemen_kode,
        $kategori_kode,
        $year,
        $angka_berikutnya
    );
}


// Proses penambahan perpindahan barang
if (isset($_POST['tambahPerpindahan'])) {
    // Ambil data dari form
    $id_inventaris = $_POST['id_inventaris'];
    $id_ruangan = $_POST['id_ruangan'];
    $tanggal_perpindahan = $_POST['tanggal_perpindahan'];
    $cawu = $_POST['cawu'];
    $jumlah_perpindahan = $_POST['jumlah_perpindahan'];
    $keterangan = $_POST['keterangan'];

    // Get data inventaris yang akan dipindah
    $query = "SELECT i.*, d.kode_departemen, k.kode_kategori 
              FROM inventaris i
              JOIN departemen d ON i.id_departemen = d.id_departemen
              JOIN kategori k ON i.id_kategori = k.id_kategori
              WHERE i.id_inventaris = '$id_inventaris'";
    $result = mysqli_query($conn, $query);
    $inventaris = mysqli_fetch_assoc($result);

    // Generate kode inventaris baru
    $kode_inventaris_baru = generateKodeInventarisPindah(
        $conn,
        $inventaris['kode_departemen'],
        $inventaris['kode_kategori'],
        date('Y') // Menggunakan tahun saat ini
    );

    // Insert ke tabel perpindahan_barang
    $query = "INSERT INTO perpindahan_barang (id_inventaris, id_ruangan, kode_inventaris_baru, tanggal_perpindahan, 
              cawu, jumlah_perpindahan, keterangan)
              VALUES ('$id_inventaris', '$id_ruangan', '$kode_inventaris_baru', '$tanggal_perpindahan', 
              '$cawu', '$jumlah_perpindahan', '$keterangan')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Insert inventaris baru dengan ruangan yang baru
        $query = "INSERT INTO inventaris (
                    kode_inventaris, 
                    nama_barang, 
                    merk, 
                    id_departemen, 
                    id_ruangan, 
                    id_kategori, 
                    tanggal_perolehan, 
                    jumlah_awal, 
                    jumlah_akhir, 
                    satuan, 
                    sumber_inventaris
                ) VALUES (
                    '$kode_inventaris_baru',
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

    // 1. Get data perpindahan barang dan inventaris terkait
    $query = "SELECT pb.*, i.id_inventaris as id_inventaris_baru 
              FROM perpindahan_barang pb
              JOIN inventaris i ON i.kode_inventaris = pb.kode_inventaris_baru 
              WHERE pb.id_perpindahan_barang = '$id_perpindahan_barang'";
    $result = mysqli_query($conn, $query);
    $perpindahan = mysqli_fetch_assoc($result);

    if ($perpindahan) {
        // 2. Cek apakah ada data kontrol untuk inventaris baru
        $checkKontrolQuery = "SELECT COUNT(*) as total_kontrol 
                             FROM (
                                 SELECT id_inventaris FROM kontrol_barang_cawu_satu 
                                 WHERE id_inventaris = '{$perpindahan['id_inventaris_baru']}'
                                 UNION ALL
                                 SELECT id_inventaris FROM kontrol_barang_cawu_dua 
                                 WHERE id_inventaris = '{$perpindahan['id_inventaris_baru']}'
                                 UNION ALL
                                 SELECT id_inventaris FROM kontrol_barang_cawu_tiga 
                                 WHERE id_inventaris = '{$perpindahan['id_inventaris_baru']}'
                             ) as kontrol";
        
        $kontrolResult = mysqli_query($conn, $checkKontrolQuery);
        $kontrolData = mysqli_fetch_assoc($kontrolResult);
        
        if ($kontrolData['total_kontrol'] > 0) {
            $_SESSION['error_message'] = "Data perpindahan tidak dapat diubah karena inventaris baru telah memiliki data kontrol!";
        } else {
            // 3. Update data perpindahan barang
            $updatePerpindahan = "UPDATE perpindahan_barang SET 
                                id_ruangan = '$id_ruangan',
                                keterangan = '$keterangan'
                                WHERE id_perpindahan_barang = '$id_perpindahan_barang'";
            
            // 4. Update data inventaris baru
            $updateInventaris = "UPDATE inventaris SET 
                               id_ruangan = '$id_ruangan'
                               WHERE kode_inventaris = '{$perpindahan['kode_inventaris_baru']}'";
            
            if (mysqli_query($conn, $updatePerpindahan) && mysqli_query($conn, $updateInventaris)) {
                $_SESSION['success_message'] = "Data perpindahan barang berhasil diperbarui!";
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui data perpindahan barang!";
            }
        }
    }
    header("Location: perpindahanBarang.php");
    exit();
}

// Proses delete perpindahan barang
if (isset($_GET['delete'])) {
    $id_perpindahan_barang = $_GET['delete'];

    // 1. Get data perpindahan barang dan inventaris terkait
    $query = "SELECT pb.*, i.id_inventaris as id_inventaris_baru 
              FROM perpindahan_barang pb
              JOIN inventaris i ON i.kode_inventaris = pb.kode_inventaris_baru 
              WHERE pb.id_perpindahan_barang = '$id_perpindahan_barang'";
    $result = mysqli_query($conn, $query);
    $perpindahan = mysqli_fetch_assoc($result);

    if ($perpindahan) {
        // 2. Cek apakah ada data kontrol untuk inventaris baru
        $checkKontrolQuery = "SELECT COUNT(*) as total_kontrol 
                             FROM (
                                 SELECT id_inventaris FROM kontrol_barang_cawu_satu 
                                 WHERE id_inventaris = '{$perpindahan['id_inventaris_baru']}'
                                 UNION ALL
                                 SELECT id_inventaris FROM kontrol_barang_cawu_dua 
                                 WHERE id_inventaris = '{$perpindahan['id_inventaris_baru']}'
                                 UNION ALL
                                 SELECT id_inventaris FROM kontrol_barang_cawu_tiga 
                                 WHERE id_inventaris = '{$perpindahan['id_inventaris_baru']}'
                             ) as kontrol";
        
        $kontrolResult = mysqli_query($conn, $checkKontrolQuery);
        $kontrolData = mysqli_fetch_assoc($kontrolResult);
        
        if ($kontrolData['total_kontrol'] > 0) {
            $_SESSION['error_message'] = "Data perpindahan tidak dapat dihapus karena inventaris baru telah memiliki data kontrol!";
        } else {
            // Begin transaction
            mysqli_begin_transaction($conn);
            try {
                // 3. Hapus data inventaris baru
                $deleteInventaris = "DELETE FROM inventaris 
                                   WHERE kode_inventaris = '{$perpindahan['kode_inventaris_baru']}'";
                mysqli_query($conn, $deleteInventaris);

                // 4. Hapus data perpindahan
                $deletePerpindahan = "DELETE FROM perpindahan_barang 
                                    WHERE id_perpindahan_barang = '$id_perpindahan_barang'";
                mysqli_query($conn, $deletePerpindahan);

                // Commit transaction
                mysqli_commit($conn);
                $_SESSION['success_message'] = "Data perpindahan barang berhasil dihapus!";
            } catch (Exception $e) {
                // Rollback transaction on error
                mysqli_rollback($conn);
                $_SESSION['error_message'] = "Gagal menghapus data perpindahan barang!";
            }
        }
    } else {
        $_SESSION['error_message'] = "Data perpindahan barang tidak ditemukan!";
    }

    header("Location: perpindahanBarang.php");
    exit();
}
?>