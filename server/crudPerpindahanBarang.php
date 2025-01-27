<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query tampil data perpindahan barang
$query = "SELECT pb.*, 
            i.nama_barang, 
            i.kode_inventaris AS kode_inventaris_asal, 
            i.merk, 
            r_asal.nama_ruangan AS ruangan_asal, 
            r_tujuan.nama_ruangan AS ruangan_tujuan, 
            i.sumber_inventaris,
            d.nama_departemen,
            COALESCE(u1.nama, u2.nama, u3.nama) AS nama_petugas
            FROM perpindahan_barang pb
            JOIN inventaris i ON pb.id_inventaris = i.id_inventaris
            JOIN ruangan r_asal ON i.id_ruangan = r_asal.id_ruangan
            JOIN inventaris i_baru ON pb.kode_inventaris_baru = i_baru.kode_inventaris
            JOIN ruangan r_tujuan ON i_baru.id_ruangan = r_tujuan.id_ruangan
            JOIN departemen d ON i.id_departemen = d.id_departemen  
            LEFT JOIN kontrol_barang_cawu_satu k1 ON k1.id_inventaris = i.id_inventaris
            LEFT JOIN kontrol_barang_cawu_dua k2 ON k2.id_inventaris = i.id_inventaris
            LEFT JOIN kontrol_barang_cawu_tiga k3 ON k3.id_inventaris = i.id_inventaris
            LEFT JOIN user u1 ON k1.id_user = u1.id_user
            LEFT JOIN user u2 ON k2.id_user = u2.id_user
            LEFT JOIN user u3 ON k3.id_user = u3.id_user
            WHERE (i.nama_barang LIKE '%$search%' 
            OR i.kode_inventaris LIKE '%$search%')
            GROUP BY pb.id_perpindahan_barang
            ORDER BY pb.tanggal_perpindahan DESC
            LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM perpindahan_barang pb
               JOIN inventaris i ON pb.id_inventaris = i.id_inventaris 
               WHERE i.nama_barang LIKE '%$search%' OR i.kode_inventaris LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Fungsi untuk mendapatkan data perpindahan barang daei semua cawu
function getBarangPindah($conn)
{
    $query = "SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk, 
                i.sumber_inventaris, 
                k.jumlah_pindah, 
                i.id_ruangan, 
                r.nama_ruangan, 
                i.satuan, 
                'Caturwulan 1' AS cawu, 
                k.tanggal_kontrol, 
                d.nama_departemen,
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
                perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris AND k.tanggal_kontrol = pb.tanggal_perpindahan
            WHERE 
                k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL
                
            UNION ALL
            
            SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk, 
                i.sumber_inventaris, 
                k.jumlah_pindah, 
                i.id_ruangan, 
                r.nama_ruangan, 
                i.satuan, 
                'Caturwulan 2' AS cawu, 
                k.tanggal_kontrol, 
                d.nama_departemen,
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
                perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris AND k.tanggal_kontrol = pb.tanggal_perpindahan
            WHERE 
                k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL
                
            UNION ALL
            
            SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk, 
                i.sumber_inventaris, 
                k.jumlah_pindah, 
                i.id_ruangan, 
                r.nama_ruangan, 
                i.satuan, 
                'Caturwulan 3' AS cawu, 
                k.tanggal_kontrol, 
                d.nama_departemen,
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
                perpindahan_barang pb ON k.id_inventaris = pb.id_inventaris AND k.tanggal_kontrol = pb.tanggal_perpindahan
            WHERE 
                k.jumlah_pindah > 0 AND pb.id_inventaris IS NULL
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    return $result;
}

// Fungsi untuk generate kode inventaris baru untuk barang yang pindah
function generateKodeInventarisPindah($conn, $departemen_kode, $kategori_kode, $ruangan_kode, $tanggal_perolehan)
{
    // Ambil tahun dari tanggal perolehan
    $year = date('Y', strtotime($tanggal_perolehan));

    $query = "SELECT MAX(CAST(SUBSTRING_INDEX(kode_inventaris, '/', -1) AS UNSIGNED)) as angka_terakhir 
              FROM inventaris 
              WHERE kode_inventaris LIKE '$departemen_kode/$kategori_kode/$ruangan_kode/$year/%'";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $angka_berikutnya = ($row['angka_terakhir'] ?? 0) + 1;

    return sprintf(
        "%s/%s/%s/%s/%03d",
        $departemen_kode,
        $ruangan_kode,
        $kategori_kode,
        $year,
        $angka_berikutnya
    );
}

// Proses penambahan perpindahan barang
if (isset($_POST['tambahPerpindahan'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $id_ruangan = $_POST['id_ruangan'];
    $tanggal_perpindahan = $_POST['tanggal_perpindahan'];
    $cawu = $_POST['cawu'];
    $jumlah_perpindahan = $_POST['jumlah_perpindahan'];
    $keterangan = $_POST['keterangan'];

    // Get data inventaris yang akan dipindah
    $query = "SELECT i.*, d.kode_departemen, k.kode_kategori, r.kode_ruangan 
              FROM inventaris i
              JOIN departemen d ON i.id_departemen = d.id_departemen
              JOIN kategori k ON i.id_kategori = k.id_kategori
              JOIN ruangan r ON r.id_ruangan = '$id_ruangan'
              WHERE i.id_inventaris = '$id_inventaris'";
    $result = mysqli_query($conn, $query);
    $inventaris = mysqli_fetch_assoc($result);

    // Generate kode inventaris baru
    $kode_inventaris_baru = generateKodeInventarisPindah(
        $conn,
        $inventaris['kode_departemen'],
        $inventaris['kode_kategori'],
        $inventaris['kode_ruangan'],
        $inventaris['tanggal_perolehan']
    );

    // Query tambah ke tabel perpindahan barang
    $query = "INSERT INTO perpindahan_barang (id_inventaris, id_ruangan, kode_inventaris_baru, tanggal_perpindahan, 
              cawu, jumlah_perpindahan, keterangan)
              VALUES ('$id_inventaris', '$id_ruangan', '$kode_inventaris_baru', '$tanggal_perpindahan', 
              '$cawu', '$jumlah_perpindahan', '$keterangan')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Query tambah inventaris baru dengan ruangan yang baru ke tabel inventaris
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
                    '$tanggal_perpindahan',
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

// Proses pengeditan perpindahan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_perpindahan_barang = $_POST['id_perpindahan_barang'];
    $id_ruangan = $_POST['id_ruangan'];
    $keterangan = $_POST['keterangan'];

    // Get data perpindahan barang dan inventaris terkait
    $query = "SELECT pb.*, i.id_inventaris as id_inventaris_baru,
              i_asal.id_departemen, d.kode_departemen, k.kode_kategori, r.kode_ruangan
              FROM perpindahan_barang pb
              JOIN inventaris i ON i.kode_inventaris = pb.kode_inventaris_baru
              JOIN inventaris i_asal ON i_asal.id_inventaris = pb.id_inventaris
              JOIN departemen d ON i_asal.id_departemen = d.id_departemen
              JOIN kategori k ON i_asal.id_kategori = k.id_kategori
              JOIN ruangan r ON r.id_ruangan = $id_ruangan
              WHERE pb.id_perpindahan_barang = '$id_perpindahan_barang'";
    $result = mysqli_query($conn, $query);
    $perpindahan = mysqli_fetch_assoc($result);

    if ($perpindahan) {
        // Cek apakah barang pindah yang dicatat sebagai inventaris baru sudah dikontrol? jika sudah maka tidak dapat diubah
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
            // Jika tidak ada data kontrol maka akan dilanjurkan dengan query dibawah
            // Query pengeditan data perpindahan barang
            $updatePerpindahan = "UPDATE perpindahan_barang SET 
                                    id_ruangan = '$id_ruangan',
                                    kode_inventaris_baru = '$kode_inventaris_baru',
                                    keterangan = '$keterangan'
                                    WHERE id_perpindahan_barang = '$id_perpindahan_barang'";

            // 4. Query pengeditan inventaris baru
            $updateInventaris = "UPDATE inventaris SET 
                                    kode_inventaris = '$kode_inventaris_baru',
                                    id_ruangan = '$id_ruangan'
                                    WHERE id_inventaris = '{$perpindahan['id_inventaris_baru']}'";

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

// Proses penghapusan perpindahan barang
if (isset($_GET['delete'])) {
    $id_perpindahan_barang = $_GET['delete'];

    // Get data perpindahan barang dan inventaris terkait
    $query = "SELECT pb.*, i.id_inventaris as id_inventaris_baru 
              FROM perpindahan_barang pb
              JOIN inventaris i ON i.kode_inventaris = pb.kode_inventaris_baru 
              WHERE pb.id_perpindahan_barang = '$id_perpindahan_barang'";
    $result = mysqli_query($conn, $query);
    $perpindahan = mysqli_fetch_assoc($result);

    if ($perpindahan) {
        // Cek apakah barang pindah yang dicatat sebagai inventaris baru sudah dikontrol? jika sudah maka tidak dapat diubah
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
            // Jika tidak ada data kontrol maka akan dilanjurkan dengan query dibawah
            mysqli_begin_transaction($conn);
            try {
                // Query hapus data inventaris baru
                $deleteInventaris = "DELETE FROM inventaris 
                                   WHERE kode_inventaris = '{$perpindahan['kode_inventaris_baru']}'";
                mysqli_query($conn, $deleteInventaris);

                // Query hapus data perpindahan
                $deletePerpindahan = "DELETE FROM perpindahan_barang 
                                    WHERE id_perpindahan_barang = '$id_perpindahan_barang'";
                mysqli_query($conn, $deletePerpindahan);

                mysqli_commit($conn);
                $_SESSION['success_message'] = "Data perpindahan barang berhasil dihapus!";
            } catch (Exception $e) {
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