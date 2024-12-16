<?php
// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT kb.*, 
          i.nama_barang, 
          i.kode_inventaris, 
          i.merk,
          r.nama_ruangan,
          i.sumber_inventaris,
          d.nama_departemen,
          COALESCE(u1.nama, u2.nama, u3.nama) AS nama_petugas
          FROM kehilangan_barang kb
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
          ORDER BY kb.tanggal_kehilangan DESC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

// Hitung total data untuk pagination
$totalQuery = "SELECT COUNT(*) as total FROM kehilangan_barang pb
               JOIN inventaris i ON pb.id_inventaris = i.id_inventaris 
               WHERE i.nama_barang LIKE '%$search%' OR i.kode_inventaris LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Fungsi untuk mendapatkan data kehilangan barangfunction getBarangHilang($conn)
function getBarangHilang($conn)
{
    $query = "SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk,          
                i.sumber_inventaris, 
                k.jumlah_hilang, 
                i.id_ruangan,
                r.nama_ruangan, 
                i.satuan,        
                'Caturwulan 1' as cawu, 
                k.tanggal_kontrol,
                d.nama_departemen,
                u.nama AS nama_petugas
            FROM 
                kontrol_barang_cawu_satu k
            JOIN 
                inventaris i ON k.id_inventaris = i.id_inventaris
            LEFT JOIN 
                kehilangan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kehilangan
            LEFT JOIN 
                departemen d ON i.id_departemen = d.id_departemen
            LEFT JOIN 
                ruangan r ON i.id_ruangan = r.id_ruangan
            LEFT JOIN 
                user u ON k.id_user = u.id_user 
            WHERE 
                k.jumlah_hilang > 0 AND kb.id_inventaris IS NULL
            
            UNION ALL
            
            SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk,
                i.sumber_inventaris,
                k.jumlah_hilang, 
                i.id_ruangan,
                r.nama_ruangan, 
                i.satuan,
                'Caturwulan 2' as cawu, 
                k.tanggal_kontrol,
                d.nama_departemen,
                u.nama AS nama_petugas
            FROM 
                kontrol_barang_cawu_dua k
            JOIN 
                inventaris i ON k.id_inventaris = i.id_inventaris
            LEFT JOIN 
                kehilangan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kehilangan
            LEFT JOIN 
                departemen d ON i.id_departemen = d.id_departemen
            LEFT JOIN 
                ruangan r ON i.id_ruangan = r.id_ruangan
            LEFT JOIN 
                user u ON k.id_user = u.id_user
            WHERE 
                k.jumlah_hilang > 0 AND kb.id_inventaris IS NULL
            
            UNION ALL
            
            SELECT 
                i.id_inventaris, 
                i.kode_inventaris, 
                i.nama_barang, 
                i.merk,
                i.sumber_inventaris,
                k.jumlah_hilang, 
                i.id_ruangan,
                r.nama_ruangan, 
                i.satuan,
                'Caturwulan 3' as cawu, 
                k.tanggal_kontrol,
                d.nama_departemen,
                u.nama AS nama_petugas
            FROM 
                kontrol_barang_cawu_tiga k
            JOIN 
                inventaris i ON k.id_inventaris = i.id_inventaris
            LEFT JOIN 
                kehilangan_barang kb ON k.id_inventaris = kb.id_inventaris AND k.tanggal_kontrol = kb.tanggal_kehilangan
            LEFT JOIN 
                departemen d ON i.id_departemen = d.id_departemen
            LEFT JOIN 
                ruangan r ON i.id_ruangan = r.id_ruangan
            LEFT JOIN 
                user u ON k.id_user = u.id_user
            WHERE 
                k.jumlah_hilang > 0 AND kb.id_inventaris IS NULL
    ";

    return mysqli_query($conn, $query);
}

// Proses penambahan kehilangan barang
if (isset($_POST['tambahKehilangan'])) {
    $id_inventaris = $_POST['id_inventaris'];
    $tanggal_kehilangan = $_POST['tanggal_kehilangan'];
    $cawu = $_POST['cawu'];
    $jumlah_kehilangan = $_POST['jumlah_kehilangan'];
    $keterangan = $_POST['keterangan'];

    $query = "INSERT INTO kehilangan_barang (id_inventaris, tanggal_kehilangan, cawu, 
              jumlah_kehilangan, keterangan)
              VALUES ('$id_inventaris', '$tanggal_kehilangan', '$cawu', 
              '$jumlah_kehilangan', '$keterangan')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data kehilangan barang berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan data kehilangan barang: " . mysqli_error($conn);
    }

    header("Location: kehilanganBarang.php");
    exit();
}

// Proses update kehilangan barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kehilangan_barang = $_POST['id_kehilangan_barang'];
    $keterangan = $_POST['keterangan'];

    $query = "UPDATE kehilangan_barang SET 
              keterangan = '$keterangan'
              WHERE id_kehilangan_barang = $id_kehilangan_barang";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data kehilangan barang berhasil diperbarui!";
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data kehilangan barang: " . mysqli_error($conn);
    }

    header("Location: kehilanganBarang.php");
    exit();
}

// Proses delete kehilangan barang
if (isset($_GET['delete'])) {
    $id_kehilangan_barang = $_GET['delete'];

    // Hapus data dari database
    $query = "DELETE FROM kehilangan_barang WHERE id_kehilangan_barang = $id_kehilangan_barang";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Data kehilangan barang berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data kehilangan barang: " . mysqli_error($conn);
    }

    header("Location: kehilanganBarang.php");
    exit();
}
?>