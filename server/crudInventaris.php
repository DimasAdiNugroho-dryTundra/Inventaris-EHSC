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
         k.nama_kategori,
         (i.jumlah - IFNULL((SELECT SUM(jumlah_kontrol) FROM kontrol_barang_cawu_satu WHERE id_inventaris = i.id_inventaris AND status_kontrol != 1), 0)
          - IFNULL((SELECT SUM(jumlah_kontrol) FROM kontrol_barang_cawu_dua WHERE id_inventaris = i.id_inventaris AND status_kontrol != 1), 0)
          - IFNULL((SELECT SUM(jumlah_kontrol) FROM kontrol_barang_cawu_tiga WHERE id_inventaris = i.id_inventaris AND status_kontrol != 1), 0)) AS jumlah_akhir,
         CASE 
             WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang
             ELSE i.nama_barang
         END as nama_barang
  FROM inventaris i
  JOIN departemen d ON i.id_departemen = d.id_departemen
  JOIN kategori k ON i.id_kategori = k.id_kategori
  LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
  WHERE CASE 
            WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang
            ELSE i.nama_barang
        END LIKE '%$search%'
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
function generateKodeInventaris($conn, $kode_departemen, $kode_kategori, $tanggal_perolehan)
{
    // Tentukan tahun berdasarkan tanggal perolehan
    $tahun = date('Y', strtotime($tanggal_perolehan));

    // Query untuk mencari nomor urut terakhir untuk kombinasi departemen/kategori/tahun
    $pattern = "$kode_departemen/$kode_kategori/$tahun/%";
    $query = "SELECT MAX(CAST(SUBSTRING_INDEX(kode_inventaris, '/', -1) AS UNSIGNED)) as max_num 
              FROM inventaris 
              WHERE kode_inventaris LIKE '$pattern'";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    $nextNum = $row['max_num'] ? $row['max_num'] + 1 : 1;

    return sprintf("%s/%s/%s/%03d", $kode_departemen, $kode_kategori, $tahun, $nextNum);
}


// Proses penambahan inventaris
if (isset($_POST['tambahInventaris'])) {
    $jenis_input = $_POST['jenis_input'];
    $id_kategori = $_POST['id_kategori'];

    if ($jenis_input == 'penerimaan') {
        $id_penerimaan = $_POST['id_penerimaan'];

        // Ambil data departemen dari tabel permintaan barang berdasarkan penerimaan
        $dept_query = "SELECT d.id_departemen, d.kode_departemen, d.nama_departemen 
                      FROM penerimaan_barang pb
                      JOIN permintaan_barang prb ON pb.id_permintaan = prb.id_permintaan
                      JOIN departemen d ON prb.id_departemen = d.id_departemen
                      WHERE pb.id_penerimaan = '$id_penerimaan'";
        $dept_result = mysqli_query($conn, $dept_query);
        $dept_data = mysqli_fetch_assoc($dept_result);
        $id_departemen = $dept_data['id_departemen'];

        // Ambil tanggal terima untuk dijadikan tanggal perolehan
        $date_query = "SELECT tanggal_terima FROM penerimaan_barang WHERE id_penerimaan = '$id_penerimaan'";
        $date_result = mysqli_query($conn, $date_query);
        $date_data = mysqli_fetch_assoc($date_result);
        $tanggal_perolehan = $date_data['tanggal_terima'];

        // Ambil kode kategori
        $kat_query = "SELECT kode_kategori FROM kategori WHERE id_kategori = '$id_kategori'";
        $kat_result = mysqli_query($conn, $kat_query);
        $kat_data = mysqli_fetch_assoc($kat_result);

        // Generate kode inventaris
        $kode_inventaris = generateKodeInventaris($conn, $dept_data['kode_departemen'], $kat_data['kode_kategori'], $tanggal_perolehan);

        // Ambil data dari penerimaan barang
        $penerimaan_query = "SELECT nama_barang, jumlah, satuan FROM penerimaan_barang WHERE id_penerimaan = '$id_penerimaan'";
        $penerimaan_result = mysqli_query($conn, $penerimaan_query);
        $penerimaan_data = mysqli_fetch_assoc($penerimaan_result);

        $nama_barang = $penerimaan_data['nama_barang'];
        $jumlah = $penerimaan_data['jumlah'];
        $satuan = $penerimaan_data['satuan'];

        $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, id_penerimaan, id_departemen, id_kategori, tanggal_perolehan, jumlah, satuan)
                  VALUES ('$kode_inventaris', '$nama_barang', '$id_penerimaan', '$id_departemen', '$id_kategori', '$tanggal_perolehan', '$jumlah', '$satuan')";
    } else {
        // Input manual
        $id_departemen = $_POST['id_departemen'];
        $nama_barang = $_POST['nama_barang'];
        $jumlah = $_POST['jumlah'];
        $satuan = $_POST['satuan'];
        $tanggal_perolehan = $_POST['tanggal_perolehan'];

        // Ambil kode departemen
        $dept_query = "SELECT kode_departemen FROM departemen WHERE id_departemen = '$id_departemen'";
        $dept_result = mysqli_query($conn, $dept_query);
        $dept_data = mysqli_fetch_assoc($dept_result);

        // Ambil kode kategori
        $kat_query = "SELECT kode_kategori FROM kategori WHERE id_kategori = '$id_kategori'";
        $kat_result = mysqli_query($conn, $kat_query);
        $kat_data = mysqli_fetch_assoc($kat_result);

        // Generate kode inventaris untuk input manual
        $kode_inventaris = generateKodeInventaris($conn, $dept_data['kode_departemen'], $kat_data['kode_kategori'], $tanggal_perolehan);

        $query = "INSERT INTO inventaris (kode_inventaris, nama_barang, id_penerimaan, id_departemen, id_kategori, tanggal_perolehan, jumlah, satuan)
                  VALUES ('$kode_inventaris', '$nama_barang', NULL, '$id_departemen', '$id_kategori', '$tanggal_perolehan', '$jumlah', '$satuan')";
    }

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
    $current_query = "SELECT i.*, d.kode_departemen, d.id_departemen, i.tanggal_perolehan,
                     CASE 
                         WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
                         ELSE i.nama_barang 
                     END as nama_barang
                     FROM inventaris i 
                     JOIN departemen d ON i.id_departemen = d.id_departemen
                     LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan 
                     WHERE i.id_inventaris = '$id_inventaris'";
    $current_result = mysqli_query($conn, $current_query);
    $current_data = mysqli_fetch_assoc($current_result);

    if (!$current_data) {
        $_SESSION['error_message'] = "Data inventaris tidak ditemukan!";
        header("Location: inventaris.php");
        exit();
    }

    if ($current_data['id_penerimaan'] === NULL) {
        // Input manual - semua field bisa diupdate
        $nama_barang = $_POST['nama_barang'];
        $jumlah = $_POST['jumlah'];
        $id_departemen = isset($_POST['id_departemen']) ? $_POST['id_departemen'] : $current_data['id_departemen'];
        $tanggal_perolehan = $_POST['tanggal_perolehan'];

        // Ambil kode departemen baru
        $dept_query = "SELECT kode_departemen FROM departemen WHERE id_departemen = '$id_departemen'";
        $dept_result = mysqli_query($conn, $dept_query);
        $dept_data = mysqli_fetch_assoc($dept_result);

        if (!$dept_data) {
            $_SESSION['error_message'] = "Departemen tidak valid!";
            header("Location: inventaris.php");
            exit();
        }

        // Ambil kode kategori baru
        $kat_query = "SELECT kode_kategori FROM kategori WHERE id_kategori = '$id_kategori'";
        $kat_result = mysqli_query($conn, $kat_query);
        $kat_data = mysqli_fetch_assoc($kat_result);

        if (!$kat_data) {
            $_SESSION['error_message'] = "Kategori tidak valid!";
            header("Location: inventaris.php");
            exit();
        }

        // Generate kode inventaris baru
        $kode_inventaris = generateKodeInventaris($conn, $dept_data['kode_departemen'], $kat_data['kode_kategori'], $tanggal_perolehan);

        $query = "UPDATE inventaris SET 
                  kode_inventaris = '$kode_inventaris',
                  nama_barang = '$nama_barang',
                  jumlah = '$jumlah',
                  id_departemen = '$id_departemen',
                  id_kategori = '$id_kategori',
                  tanggal_perolehan = '$tanggal_perolehan',
                  satuan = '$satuan'
                  WHERE id_inventaris = '$id_inventaris'";
    } else {
        // Dari penerimaan - hanya update kategori dan satuan
        $kat_query = "SELECT kode_kategori FROM kategori WHERE id_kategori = '$id_kategori'";
        $kat_result = mysqli_query($conn, $kat_query);
        $kat_data = mysqli_fetch_assoc($kat_result);

        if (!$kat_data) {
            $_SESSION['error_message'] = "Kategori tidak valid!";
            header("Location: inventaris.php");
            exit();
        }

        $kode_inventaris = generateKodeInventaris($conn, $current_data['kode_departemen'], $kat_data['kode_kategori'], $current_data['tanggal_perolehan']);

        $query = "UPDATE inventaris SET 
                  kode_inventaris = '$kode_inventaris',
                  id_kategori = '$id_kategori',
                  satuan = '$satuan'
                  WHERE id_inventaris = '$id_inventaris'";
    }

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
        // 1. Hapus data di tabel kontrol_barang_cawu_satu
        $query1 = "DELETE FROM kontrol_barang_cawu_satu WHERE id_inventaris = '$id_inventaris'";
        mysqli_query($conn, $query1);

        // 2. Hapus data di tabel kontrol_barang_cawu_dua
        $query2 = "DELETE FROM kontrol_barang_cawu_dua WHERE id_inventaris = '$id_inventaris'";
        mysqli_query($conn, $query2);

        // 3. Hapus data di tabel kontrol_barang_cawu_tiga
        $query3 = "DELETE FROM kontrol_barang_cawu_tiga WHERE id_inventaris = '$id_inventaris'";
        mysqli_query($conn, $query3);

        // 4. Hapus data di tabel inventaris
        $query4 = "DELETE FROM inventaris WHERE id_inventaris = '$id_inventaris'";
        mysqli_query($conn, $query4);

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