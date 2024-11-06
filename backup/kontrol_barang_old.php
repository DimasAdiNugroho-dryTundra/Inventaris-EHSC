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
        // Get current inventory amount
        $invQuery = "SELECT jumlah FROM inventaris WHERE id_inventaris = '$id_inventaris' FOR UPDATE";
        $invResult = mysqli_query($conn, $invQuery);
        $invData = mysqli_fetch_assoc($invResult);
        $currentAmount = $invData['jumlah'];

        // Validate amount
        if ($status != 1 && $jumlah > $currentAmount) {
            throw new Exception("Jumlah yang diinput melebihi stok yang tersedia!");
        }
        
        // Insert kontrol_barang
        $query = "INSERT INTO kontrol_barang (id_user, id_invetaris, cawu, tanggal, status, jumlah, keterangan)
                  VALUES ('$id_user', '$id_inventaris', '$cawu', '$tanggal', '$status', '$jumlah', '$keterangan')";
        mysqli_query($conn, $query);
        $id_kontrol = mysqli_insert_id($conn);
        
        if ($status == 2) { // Rusak
            // Update inventaris
            $newAmount = $currentAmount - $jumlah;
            $updateQuery = "UPDATE inventaris SET jumlah = '$newAmount' WHERE id_inventaris = '$id_inventaris'";
            if (!mysqli_query($conn, $updateQuery)) {
                throw new Exception("Gagal mengupdate jumlah inventaris");
            }
            
            // Insert kerusakan_barang
            $kerusakanQuery = "INSERT INTO kerusakan_barang (id_kontrol_barang, cawu, tanggal_kerusakan, jumlah, keterangan)
                              VALUES ('$id_kontrol', '$cawu', '$tanggal', '$jumlah', '$keterangan')";
            mysqli_query($conn, $kerusakanQuery);
        }
        elseif ($status == 3) { // Pindah
            // Update inventaris
            $newAmount = $currentAmount - $jumlah;
            $updateQuery = "UPDATE inventaris SET jumlah = '$newAmount' WHERE id_inventaris = '$id_inventaris'";
            if (!mysqli_query($conn, $updateQuery)) {
                throw new Exception("Gagal mengupdate jumlah inventaris");
            }
            
            // Insert perpindahan_barang
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
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    
    mysqli_begin_transaction($conn);
    
    try {
        // Ambil data kontrol lama
        $oldDataQuery = "SELECT kb.*, i.jumlah as jumlah_inventaris 
                        FROM kontrol_barang kb
                        JOIN inventaris i ON kb.id_invetaris = i.id_inventaris
                        WHERE kb.id_kontrol_barang = '$id_kontrol' FOR UPDATE";
        $oldResult = mysqli_query($conn, $oldDataQuery);
        $oldData = mysqli_fetch_assoc($oldResult);
        $status_lama = $oldData['status'];
        
        // Jika status berubah dari rusak/pindah menjadi baik
        if (($status_lama == 2 || $status_lama == 3) && $status_baru == 1) {
            // Update jumlah di inventaris
            $updateInvQuery = "UPDATE inventaris 
                             SET jumlah = jumlah + {$oldData['jumlah']} 
                             WHERE id_inventaris = '$id_inventaris'";
            mysqli_query($conn, $updateInvQuery);
            
            // Update atau hapus data di tabel terkait
            if ($status_lama == 2) {
                $updateRusakQuery = "UPDATE kerusakan_barang 
                                   SET jumlah = jumlah - {$oldData['jumlah']}
                                   WHERE id_kontrol_barang = '$id_kontrol'";
                mysqli_query($conn, $updateRusakQuery);
                
                // Hapus jika jumlah 0
                mysqli_query($conn, "DELETE FROM kerusakan_barang 
                                   WHERE id_kontrol_barang = '$id_kontrol' 
                                   AND jumlah <= 0");
            } else {
                $updatePindahQuery = "UPDATE perpindahan_barang 
                                    SET jumlah = jumlah - {$oldData['jumlah']}
                                    WHERE id_kontrol_barang = '$id_kontrol'";
                mysqli_query($conn, $updatePindahQuery);
                
                // Hapus jika jumlah 0
                mysqli_query($conn, "DELETE FROM perpindahan_barang 
                                   WHERE id_kontrol_barang = '$id_kontrol' 
                                   AND jumlah <= 0");
            }
        }
        // Jika status berubah dari baik menjadi rusak/pindah
        else if ($status_lama == 1 && ($status_baru == 2 || $status_baru == 3)) {
            // Validasi jumlah
            if ($jumlah > $oldData['jumlah_inventaris']) {
                throw new Exception("Jumlah melebihi stok yang tersedia!");
            }
            
            // Update jumlah di inventaris
            $updateInvQuery = "UPDATE inventaris 
                             SET jumlah = jumlah - '$jumlah' 
                             WHERE id_inventaris = '$id_inventaris'";
            mysqli_query($conn, $updateInvQuery);
            
            // Insert atau update ke tabel terkait
            if ($status_baru == 2) {
                $rusakQuery = "INSERT INTO kerusakan_barang (id_kontrol_barang, cawu, tanggal_kerusakan, jumlah, keterangan)
                              VALUES ('$id_kontrol', '{$oldData['cawu']}', CURRENT_DATE(), '$jumlah', '$keterangan')
                              ON DUPLICATE KEY UPDATE
                              jumlah = jumlah + VALUES(jumlah),
                              keterangan = VALUES(keterangan)";
                mysqli_query($conn, $rusakQuery);
            } else {
                $pindahQuery = "INSERT INTO perpindahan_barang (id_kontrol_barang, cawu, tanggal_perpindahan, jumlah, keterangan)
                               VALUES ('$id_kontrol', '{$oldData['cawu']}', CURRENT_DATE(), '$jumlah', '$keterangan')
                               ON DUPLICATE KEY UPDATE
                               jumlah = jumlah + VALUES(jumlah),
                               keterangan = VALUES(keterangan)";
                mysqli_query($conn, $pindahQuery);
            }
        }
        
        // Update kontrol_barang
        $query = "UPDATE kontrol_barang SET 
                  status = '$status_baru',
                  jumlah = '$jumlah',
                  keterangan = '$keterangan'
                  WHERE id_kontrol_barang = '$id_kontrol'";
        mysqli_query($conn, $query);
        
        // Hapus dari inventaris jika jumlah 0
        mysqli_query($conn, "DELETE FROM inventaris 
                           WHERE id_inventaris = '$id_inventaris' 
                           AND jumlah <= 0");
        
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
                                                        <?php
                // Ambil data detail barang
                $detailQuery = "SELECT kb.*, i.kode_inventaris, i.jumlah as jumlah_inventaris,
                               pb.nama_barang
                               FROM kontrol_barang kb
                               JOIN inventaris i ON kb.id_invetaris = i.id_inventaris
                               JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
                               WHERE kb.id_kontrol_barang = '{$row['id_kontrol_barang']}'";
                $detailResult = mysqli_query($conn, $detailQuery);
                $detail = mysqli_fetch_assoc($detailResult);
                ?>

                                                        <form method="POST">
                                                            <input type="hidden" name="action" value="update">
                                                            <input type="hidden" name="id_kontrol"
                                                                value="<?php echo $row['id_kontrol_barang']; ?>">
                                                            <input type="hidden" name="id_inventaris"
                                                                value="<?php echo $row['id_invetaris']; ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label">Barang</label>
                                                                <input type="text" class="form-control"
                                                                    value="<?php echo $detail['kode_inventaris'] . ' - ' . $detail['nama_barang']; ?>"
                                                                    readonly>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-select" required
                                                                    onchange="toggleEditFields(this, '<?php echo $row['id_kontrol_barang']; ?>', <?php echo $detail['jumlah_inventaris']; ?>)">
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
                                                                    class="form-control jumlah-field-<?php echo $row['id_kontrol_barang']; ?>"
                                                                    value="<?php echo $row['jumlah']; ?>"
                                                                    <?php echo ($row['status'] == 1) ? 'readonly' : ''; ?>
                                                                    required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Keterangan</label>
                                                                <textarea name="keterangan"
                                                                    class="form-control keterangan-field-<?php echo $row['id_kontrol_barang']; ?>"
                                                                    rows="3"
                                                                    <?php echo ($row['status'] == 1) ? 'readonly' : ''; ?>
                                                                    required><?php echo $row['keterangan']; ?></textarea>
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
                                                <label class="form-label">Petugas</label>
                                                <input type="text" class="form-control"
                                                    value="<?php echo $_SESSION['nama']; ?>" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Inventaris</label>
                                                <select name="id_inventaris" class="form-select" required
                                                    onchange="toggleFields(document.querySelector('select[name=\'status\']'))">
                                                    <option value="">Pilih Barang</option>
                                                    <?php
        $invQuery = "SELECT i.*, pb.nama_barang 
                    FROM inventaris i
                    JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
                    WHERE i.jumlah > 0";
        $invResult = mysqli_query($conn, $invQuery);
        while ($inv = mysqli_fetch_assoc($invResult)) {
            echo "<option value='" . $inv['id_inventaris'] . "' data-jumlah='" . $inv['jumlah'] . "'>" 
                 . $inv['kode_inventaris'] . " - " 
                 . $inv['nama_barang'] . "</option>";
        }
        ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required
                                                    onchange="toggleFields(this)">
                                                    <option value="1">Baik</option>
                                                    <option value="2">Rusak</option>
                                                    <option value="3">Pindah</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Jumlah</label>
                                                <input type="number" name="jumlah" id="jumlahField" class="form-control"
                                                    readonly required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea name="keterangan" id="keteranganField" class="form-control"
                                                    rows="3" readonly required></textarea>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" name="tambahKontrol"
                                                    class="btn btn-primary">Tambah</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
    function toggleFields(selectElement) {
        const status = selectElement.value;
        const inventarisSelect = document.querySelector('select[name="id_inventaris"]');
        const jumlahField = document.querySelector('#jumlahField');
        const keteranganField = document.querySelector('#keteranganField');

        // Ambil jumlah inventaris dari data-jumlah yang akan kita tambahkan pada option
        const selectedOption = inventarisSelect.options[inventarisSelect.selectedIndex];
        const jumlahInventaris = selectedOption.getAttribute('data-jumlah');

        if (status === '1') { // Baik
            jumlahField.value = jumlahInventaris;
            jumlahField.setAttribute('readonly', true);
            keteranganField.value = 'Barang dalam kondisi baik';
            keteranganField.setAttribute('readonly', true);
            jumlahField.classList.add('bg-light');
            keteranganField.classList.add('bg-light');
        } else { // Rusak atau Pindah
            jumlahField.removeAttribute('readonly');
            keteranganField.removeAttribute('readonly');
            jumlahField.classList.remove('bg-light');
            keteranganField.classList.remove('bg-light');
            jumlahField.value = '';
            keteranganField.value = '';
            jumlahField.max = jumlahInventaris;
        }
    }

    function toggleEditFields(selectElement, id, jumlahInventaris) {
        const status = selectElement.value;
        const jumlahField = document.querySelector(`.jumlah-field-${id}`);
        const keteranganField = document.querySelector(`.keterangan-field-${id}`);

        if (status === '1') {
            jumlahField.setAttribute('readonly', true);
            keteranganField.setAttribute('readonly', true);
            jumlahField.classList.add('bg-light');
            keteranganField.classList.add('bg-light');
            jumlahField.value = jumlahInventaris;
            keteranganField.value = 'Barang dalam kondisi baik';
        } else {
            jumlahField.removeAttribute('readonly');
            keteranganField.removeAttribute('readonly');
            jumlahField.classList.remove('bg-light');
            keteranganField.classList.remove('bg-light');
            jumlahField.max = jumlahInventaris;
        }
    }
    </script>

    <?php require('../layouts/assetsFooter.php'); ?>