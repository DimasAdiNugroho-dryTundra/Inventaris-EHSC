<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');

// Fungsi untuk menentukan Cawu berdasarkan bulan
function determineCawu($month)
{
    if ($month >= 1 && $month <= 3) {
        return "Cawu 1";
    } elseif ($month >= 4 && $month <= 6) {
        return "Cawu 2";
    } elseif ($month >= 7 && $month <= 9) {
        return "Cawu 3";
    } else {
        return "Cawu 4";
    }
}

// Pengaturan untuk pagination
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Penanganan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Query untuk menampilkan data kontrol barang dengan join ke tabel terkait
$query = "SELECT kb.*, i.kode_inventaris, i.jumlah as jumlah_inventaris, 
          pb.nama_barang, i.kondisi
          FROM kontrol_barang kb
          JOIN inventaris i ON kb.id_invetaris = i.id_inventaris
          JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
          WHERE i.kode_inventaris LIKE '%$search%'
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Hitung total data untuk keperluan pagination
$totalQuery = "SELECT COUNT(*) as total FROM kontrol_barang kb
               JOIN inventaris i ON kb.id_invetaris = i.id_inventaris
               WHERE i.kode_inventaris LIKE '%$search%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// Proses penambahan kontrol barang
if (isset($_POST['tambahKontrol'])) {
    $id_user = $_SESSION['id_user'];
    $id_inventaris = $_POST['id_inventaris'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    
    $month = date('n', strtotime($tanggal));
    $cawu = determineCawu($month);
    
    mysqli_begin_transaction($conn);
    
    try {
        // Cek stok tersedia
        $invQuery = "SELECT jumlah FROM inventaris WHERE id_inventaris = '$id_inventaris' FOR UPDATE";
        $invResult = mysqli_query($conn, $invQuery);
        $invData = mysqli_fetch_assoc($invResult);
        
        if ($status != 1 && $jumlah > $invData['jumlah']) {
            throw new Exception("Jumlah melebihi stok yang tersedia!");
        }
        
        // Insert ke kontrol_barang
        $query = "INSERT INTO kontrol_barang (id_user, id_invetaris, cawu, tanggal, status, jumlah, keterangan)
                  VALUES ('$id_user', '$id_inventaris', '$cawu', '$tanggal', '$status', '$jumlah', '$keterangan')";
        mysqli_query($conn, $query);
        $id_kontrol = mysqli_insert_id($conn);
        
        if ($status == 2) { // Rusak
            // Kurangi stok inventaris
            $updateInv = "UPDATE inventaris SET jumlah = jumlah - $jumlah 
                         WHERE id_inventaris = '$id_inventaris'";
            mysqli_query($conn, $updateInv);
            
            // Catat di kerusakan_barang
            $rusakQuery = "INSERT INTO kerusakan_barang (id_kontrol_barang, cawu, tanggal_kerusakan, jumlah, keterangan)
                          VALUES ('$id_kontrol', '$cawu', '$tanggal', '$jumlah', '$keterangan')";
            mysqli_query($conn, $rusakQuery);
        }
        elseif ($status == 3) { // Pindah
            // Kurangi stok inventaris
            $updateInv = "UPDATE inventaris SET jumlah = jumlah - $jumlah 
                         WHERE id_inventaris = '$id_inventaris'";
            mysqli_query($conn, $updateInv);
            
            // Catat di perpindahan_barang
            $pindahQuery = "INSERT INTO perpindahan_barang (id_kontrol_barang, cawu, tanggal_perpindahan, jumlah, keterangan)
                           VALUES ('$id_kontrol', '$cawu', '$tanggal', '$jumlah', '$keterangan')";
            mysqli_query($conn, $pindahQuery);
        }
        
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Kontrol barang berhasil ditambahkan!";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Gagal menambahkan kontrol barang: " . $e->getMessage();
    }
    
    header("Location: kontrol_barang.php");
    exit();
}

// Proses update data kontrol barang
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id_kontrol = $_POST['id_kontrol'];
    $id_inventaris = $_POST['id_inventaris'];
    $status_baru = $_POST['status'];
    $jumlah_baru = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    
    mysqli_begin_transaction($conn);
    
    try {
        // Ambil data kontrol lama
        $oldDataQuery = "SELECT kb.*, i.jumlah as jumlah_inventaris 
                        FROM kontrol_barang kb
                        JOIN inventaris i ON kb.id_invetaris = i.id_inventaris
                        WHERE kb.id_kontrol_barang = '$id_kontrol'";
        $oldResult = mysqli_query($conn, $oldDataQuery);
        $oldData = mysqli_fetch_assoc($oldResult);
        $status_lama = $oldData['status'];
        $jumlah_lama = $oldData['jumlah'];
        
        // Jika status berubah dari rusak ke baik
        if ($status_lama == 2 && $status_baru == 1) {
            // Cek jumlah di tabel kerusakan_barang
            $rusakQuery = "SELECT jumlah FROM kerusakan_barang 
                          WHERE id_kontrol_barang = '$id_kontrol'";
            $rusakResult = mysqli_query($conn, $rusakQuery);
            $rusakData = mysqli_fetch_assoc($rusakResult);
            
            // Kembalikan jumlah ke inventaris
            $updateInv = "UPDATE inventaris 
                         SET jumlah = jumlah + {$rusakData['jumlah']} 
                         WHERE id_inventaris = '$id_inventaris'";
            mysqli_query($conn, $updateInv);
            
            // Hapus data kerusakan
            mysqli_query($conn, "DELETE FROM kerusakan_barang 
                               WHERE id_kontrol_barang = '$id_kontrol'");
        }
        // Jika status berubah dari pindah ke baik
        else if ($status_lama == 3 && $status_baru == 1) {
            // Cek jumlah di tabel perpindahan_barang
            $pindahQuery = "SELECT jumlah FROM perpindahan_barang 
                           WHERE id_kontrol_barang = '$id_kontrol'";
            $pindahResult = mysqli_query($conn, $pindahQuery);
            $pindahData = mysqli_fetch_assoc($pindahResult);
            
            // Kembalikan jumlah ke inventaris
            $updateInv = "UPDATE inventaris 
                         SET jumlah = jumlah + {$pindahData['jumlah']} 
                         WHERE id_inventaris = '$id_inventaris'";
            mysqli_query($conn, $updateInv);
            
            // Hapus data perpindahan
            mysqli_query($conn, "DELETE FROM perpindahan_barang 
                               WHERE id_kontrol_barang = '$id_kontrol'");
        }
        // Jika jumlah berubah dengan status sama
        else if (($status_lama == 2 || $status_lama == 3) && $status_lama == $status_baru) {
            $selisih = $jumlah_baru - $jumlah_lama;
            
            if ($selisih != 0) {
                // Update jumlah di inventaris
                $updateInv = "UPDATE inventaris 
                             SET jumlah = jumlah - $selisih 
                             WHERE id_inventaris = '$id_inventaris'";
                mysqli_query($conn, $updateInv);
                
                // Update jumlah di tabel terkait
                if ($status_baru == 2) {
                    $updateTable = "UPDATE kerusakan_barang 
                                  SET jumlah = $jumlah_baru 
                                  WHERE id_kontrol_barang = '$id_kontrol'";
                } else {
                    $updateTable = "UPDATE perpindahan_barang 
                                  SET jumlah = $jumlah_baru 
                                  WHERE id_kontrol_barang = '$id_kontrol'";
                }
                mysqli_query($conn, $updateTable);
            }
        }
        
        // Update kontrol_barang
        $updateKontrol = "UPDATE kontrol_barang 
                         SET status = '$status_baru',
                             jumlah = '$jumlah_baru',
                             keterangan = '$keterangan'
                         WHERE id_kontrol_barang = '$id_kontrol'";
        mysqli_query($conn, $updateKontrol);
        
        // Cek apakah inventaris perlu diarsipkan
        $cekInvQuery = "SELECT jumlah FROM inventaris 
                       WHERE id_inventaris = '$id_inventaris'";
        $cekInvResult = mysqli_query($conn, $cekInvQuery);
        $invData = mysqli_fetch_assoc($cekInvResult);
        
        if ($invData['jumlah'] <= 0) {
            // Pindahkan ke arsip
            $arsipQuery = "INSERT INTO arsip_inventaris (
                id_inventaris, kode_inventaris, nama_barang,
                jumlah, satuan, status, tanggal_arsip, keterangan
              )
              SELECT 
                i.id_inventaris, i.kode_inventaris, pb.nama_barang,
                kb.jumlah, i.satuan, kb.status,
                CURRENT_DATE(), kb.keterangan
              FROM inventaris i
              JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
              JOIN kontrol_barang kb ON kb.id_invetaris = i.id_inventaris
              WHERE i.id_inventaris = '$id_inventaris'";
              mysqli_query($conn, $arsipQuery);

            // Hapus dari inventaris
            mysqli_query($conn, "DELETE FROM inventaris WHERE id_inventaris = '$id_inventaris'");
        }
        
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Kontrol barang berhasil diupdate!";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Gagal mengupdate kontrol barang: " . $e->getMessage();
    }
    
    header("Location: kontrol_barang.php");
    exit();
}

// Proses hapus data kontrol barang
if (isset($_GET['delete'])) {
    $id_kontrol = $_GET['delete'];

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // Ambil data kontrol sebelum dihapus
        $dataQuery = "SELECT kb.*, i.id_inventaris 
                     FROM kontrol_barang kb
                     JOIN inventaris i ON kb.id_invetaris = i.id_inventaris
                     WHERE kb.id_kontrol_barang = '$id_kontrol'";
        $dataResult = mysqli_query($conn, $dataQuery);
        $controlData = mysqli_fetch_assoc($dataResult);

        // Kembalikan jumlah inventaris jika status sebelumnya rusak atau pindah
        if ($controlData['status'] != 1) {
            $restoreQuery = "UPDATE inventaris 
                            SET jumlah = jumlah + {$controlData['jumlah']}
                            WHERE id_inventaris = '{$controlData['id_inventaris']}'";
            mysqli_query($conn, $restoreQuery);
        }

        // Hapus data terkait di tabel kerusakan dan perpindahan
        mysqli_query($conn, "DELETE FROM kerusakan_barang WHERE id_kontrol_barang = '$id_kontrol'");
        mysqli_query($conn, "DELETE FROM perpindahan_barang WHERE id_kontrol_barang = '$id_kontrol'");

        // Hapus data kontrol
        mysqli_query($conn, "DELETE FROM kontrol_barang WHERE id_kontrol_barang = '$id_kontrol'");

        // Commit transaksi jika semua query berhasil
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Kontrol barang berhasil dihapus!";
    } catch (Exception $e) {
        // Rollback jika terjadi error
        mysqli_rollback($conn);
        $_SESSION['error_message'] = "Gagal menghapus kontrol barang: " . $e->getMessage();
    }

    header("Location: kontrol_barang.php");
    exit();
}

function restoreFromArsip($conn, $id_arsip) {
    mysqli_begin_transaction($conn);
    
    try {
        // Ambil data arsip
        $arsipQuery = "SELECT * FROM arsip_inventaris WHERE id_arsip = '$id_arsip'";
        $arsipResult = mysqli_query($conn, $arsipQuery);
        $arsipData = mysqli_fetch_assoc($arsipResult);
        
        // Kembalikan ke inventaris
        if ($arsipData['status'] == 2) { // Rusak
            $updateKerusakan = "UPDATE kerusakan_barang 
                               SET jumlah = 0
                               WHERE id_kontrol_barang IN (
                                   SELECT id_kontrol_barang 
                                   FROM kontrol_barang 
                                   WHERE id_invetaris = '{$arsipData['id_inventaris']}'
                               )";
            mysqli_query($conn, $updateKerusakan);
        } else if ($arsipData['status'] == 3) { // Pindah
            $updatePerpindahan = "UPDATE perpindahan_barang 
                                 SET jumlah = 0
                                 WHERE id_kontrol_barang IN (
                                     SELECT id_kontrol_barang 
                                     FROM kontrol_barang 
                                     WHERE id_invetaris = '{$arsipData['id_inventaris']}'
                                 )";
            mysqli_query($conn, $updatePerpindahan);
        }
        
        // Update inventaris
        $restoreQuery = "INSERT INTO inventaris (
                            id_inventaris, kode_inventaris, id_penerimaan,
                            jumlah, satuan, kondisi
                        ) VALUES (
                            '{$arsipData['id_inventaris']}',
                            '{$arsipData['kode_inventaris']}',
                            (SELECT id_penerimaan FROM penerimaan_barang 
                             WHERE nama_barang = '{$arsipData['nama_barang']}' LIMIT 1),
                            '{$arsipData['jumlah']}',
                            '{$arsipData['satuan']}',
                            1
                        )";
        mysqli_query($conn, $restoreQuery);
        
        // Hapus dari arsip
        mysqli_query($conn, "DELETE FROM arsip_inventaris WHERE id_arsip = '$id_arsip'");
        
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h2 class="mb-1">Manajemen Kontrol Barang</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Kontrol Barang</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="card">
                        <!-- Alert Messages -->
                        <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                            <span class="alert-icon rounded">
                                <i class="ti ti-ban"></i>
                            </span>
                            <?php echo $_SESSION['error_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                            <span class="alert-icon rounded">
                                <i class="ti ti-check"></i>
                            </span>
                            <?php echo $_SESSION['success_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>

                        <h4 class="card-header d-flex justify-content-between align-items-center">
                            Data Kontrol Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahKontrolModal">
                                Tambah Kontrol
                            </button>
                        </h4>

                        <!-- Search and Limit -->
                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari kode inventaris..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="kontrol_barang.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="kontrol_barang.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="kontrol_barang.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body pb-0">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="badge bg-label-primary p-2 me-2">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <h5 class="mb-0">Petugas: <?php echo $_SESSION['nama']; ?></h5>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Inventaris</th>
                                            <th>Nama Barang</th>
                                            <th>Cawu</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                    $no = $offset + 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $statusText = '';
                                        switch($row['status']) {
                                            case 1:
                                                $statusText = '<span class="badge bg-success">Baik</span>';
                                                break;
                                            case 2:
                                                $statusText = '<span class="badge bg-danger">Rusak</span>';
                                                break;
                                            case 3:
                                                $statusText = '<span class="badge bg-warning">Pindah</span>';
                                                break;
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row['kode_inventaris']; ?></td>
                                            <td><?php echo $row['nama_barang']; ?></td>
                                            <td><?php echo $row['cawu']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                            <td><?php echo $statusText; ?></td>
                                            <td><?php echo $row['jumlah']; ?></td>
                                            <td><?php echo $row['keterangan']; ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modal-update-<?php echo $row['id_kontrol_barang']; ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal-delete-<?php echo $row['id_kontrol_barang']; ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade"
                                            id="modal-update-<?php echo $row['id_kontrol_barang']; ?>" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Kontrol Barang</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST">
                                                            <input type="hidden" name="action" value="update">
                                                            <input type="hidden" name="id_kontrol"
                                                                value="<?php echo $row['id_kontrol_barang']; ?>">
                                                            <input type="hidden" name="id_inventaris"
                                                                value="<?php echo $row['id_invetaris']; ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label">Barang</label>
                                                                <input type="text" class="form-control"
                                                                    value="<?php echo $row['kode_inventaris'] . ' - ' . $row['nama_barang']; ?>"
                                                                    readonly>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-select" required
                                                                    onchange="toggleInputs(this.value, 'edit-<?php echo $row['id_kontrol_barang']; ?>')">
                                                                    <option value="1"
                                                                        <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>
                                                                        Baik</option>
                                                                    <option value="2"
                                                                        <?php echo ($row['status'] == 2) ? 'selected' : ''; ?>>
                                                                        Rusak</option>
                                                                    <option value="3"
                                                                        <?php echo ($row['status'] == 3) ? 'selected' : ''; ?>>
                                                                        Pindah</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Jumlah</label>
                                                                <input type="number" name="jumlah"
                                                                    id="jumlah-edit-<?php echo $row['id_kontrol_barang']; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row['jumlah']; ?>" required
                                                                    <?php echo ($row['status'] == 1) ? 'readonly' : ''; ?>>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Keterangan</label>
                                                                <textarea name="keterangan"
                                                                    id="keterangan-edit-<?php echo $row['id_kontrol_barang']; ?>"
                                                                    class="form-control" rows="3" required
                                                                    <?php echo ($row['status'] == 1) ? 'readonly' : ''; ?>><?php echo $row['keterangan']; ?></textarea>
                                                            </div>

                                                            <button type="submit"
                                                                class="btn btn-primary">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Delete -->
                                        <div class="modal fade"
                                            id="modal-delete-<?php echo $row['id_kontrol_barang']; ?>" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin menghapus kontrol barang dengan kode
                                                        <?php echo $row['kode_inventaris']; ?>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <a href="kontrol_barang.php?delete=<?php echo $row['id_kontrol_barang']; ?>"
                                                            class="btn btn-danger">Hapus</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination pagination-rounded justify-content-center">
                                    <?php if ($page > 1) { ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>"
                                            aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                        <a class="page-link"
                                            href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($page < $totalPages) { ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>"
                                            aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </nav>
                        </div>

                        <!-- Modal Tambah Kontrol -->
                        <div class="modal fade" id="tambahKontrolModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Kontrol Barang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST">
                                            <div class="mb-3">
                                                <label class="form-label">Inventaris</label>
                                                <select name="id_inventaris" class="form-select" required>
                                                    <option value="">Pilih Barang</option>
                                                    <?php
                            $invQuery = "SELECT i.*, pb.nama_barang 
                                        FROM inventaris i
                                        JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
                                        WHERE i.jumlah > 0";
                            $invResult = mysqli_query($conn, $invQuery);
                            while ($inv = mysqli_fetch_assoc($invResult)) {
                                echo "<option value='" . $inv['id_inventaris'] . "'>" 
                                     . $inv['kode_inventaris'] . " - " 
                                     . $inv['nama_barang'] . "</option>";
                            }
                            ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required
                                                    onchange="toggleInputs(this.value, 'add')">
                                                    <option value="1">Baik</option>
                                                    <option value="2">Rusak</option>
                                                    <option value="3">Pindah</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Jumlah</label>
                                                <input type="number" name="jumlah" id="jumlah-add" class="form-control"
                                                    required readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea name="keterangan" id="keterangan-add" class="form-control"
                                                    rows="3" required readonly>Barang dalam kondisi baik</textarea>
                                            </div>

                                            <button type="submit" name="tambahKontrol"
                                                class="btn btn-primary">Tambah</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <?php require('../layouts/footer.php'); ?>
                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
                <div class="layout-overlay layout-menu-toggle"></div>
                <div class="drag-target"></div>
            </div>

            <!-- JavaScript untuk mengatur field yang bisa diisi -->
            <script>
            function toggleInputs(status, prefix) {
                const jumlahField = document.getElementById('jumlah-' + prefix);
                const keteranganField = document.getElementById('keterangan-' + prefix);

                if (status === '1') {
                    jumlahField.readOnly = true;
                    keteranganField.readOnly = true;
                    jumlahField.classList.add('bg-light');
                    keteranganField.classList.add('bg-light');
                    if (prefix === 'add') {
                        keteranganField.value = 'Barang dalam kondisi baik';
                    }
                } else {
                    jumlahField.readOnly = false;
                    keteranganField.readOnly = false;
                    jumlahField.classList.remove('bg-light');
                    keteranganField.classList.remove('bg-light');
                    if (prefix === 'add') {
                        jumlahField.value = '';
                        keteranganField.value = '';
                    }
                }
            }

            // Inisialisasi status field saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                const statusSelects = document.querySelectorAll('select[name="status"]');
                statusSelects.forEach(select => {
                    if (select.closest('.modal').id === 'tambahKontrolModal') {
                        toggleInputs(select.value, 'add');
                    } else {
                        const modalId = select.closest('.modal').id;
                        const kontrol_id = modalId.split('-')[2];
                        toggleInputs(select.value, 'edit-' + kontrol_id);
                    }
                });
            });
            </script>

            <?php require('../layouts/assetsFooter.php'); ?>