<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');


// Ambil tahun dan cawu dari session atau POST
$cawu = isset($_SESSION['cawu']) ? $_SESSION['cawu'] : (isset($_POST['cawu']) ? intval($_POST['cawu']) : 1);
$year = isset($_SESSION['year']) ? $_SESSION['year'] : (isset($_POST['year']) ? intval($_POST['year']) : date('Y'));

// Simpan ke session jika ada perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cawu'])) {
        $_SESSION['cawu'] = intval($_POST['cawu']);
    }
    if (isset($_POST['year'])) {
        $_SESSION['year'] = intval($_POST['year']);
    }
}

// Pastikan $cawu dan $year adalah integer
$cawu = intval($cawu);
$year = intval($year);

$currentYear = date('Y');
$years = range($currentYear - 5, $currentYear + 5); // Tahun dari 5 tahun lalu hingga 5 tahun ke depan

// Tentukan nama file berdasarkan cawu yang dipilih
$crudFile = '';
if ($cawu == 1) {
    $crudFile = 'crudKontrolBarangCawuSatu.php';
} elseif ($cawu == 2) {
    $crudFile = 'crudKontrolBarangCawuDua.php';
} elseif ($cawu == 3) {
    $crudFile = 'crudKontrolBarangCawuTiga.php';
}

// Tentukan nama field ID kontrol barang berdasarkan cawu yang dipilih
$cawuIdField = '';
if ($cawu == 1) {
    $cawuIdField = 'id_kontrol_barang_cawu_satu';
} elseif ($cawu == 2) {
    $cawuIdField = 'id_kontrol_barang_cawu_dua';
} elseif ($cawu == 3) {
    $cawuIdField = 'id_kontrol_barang_cawu_tiga';
}

// Include file CRUD yang sesuai
require("../server/$crudFile");

?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
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


                    <!-- Alert Messages -->
                    <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                            <i class="ti ti-ban"></i>
                        </span>
                        <?php echo $_SESSION['error_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                            <i class="ti ti-check"></i>
                        </span>
                        <?php echo $_SESSION['success_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); endif; ?>

                    <div class="card">
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge bg-label-primary p-2 me-2">
                                    <i class="ti ti-user"></i>
                                </div>
                                <h5 class="mb-0">Petugas: <?php echo $_SESSION['nama']; ?></h5>
                            </div>
                        </div>
                        <h4 class="card-header d-flex justify-content-between align-items-center">
                            Data Kontrol Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahKontrolModal">
                                Tambah Kontrol
                            </button>
                        </h4>

                        <!-- Dropdown Cawu dan Tahun -->
                        <div class="row p-3">
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="ti ti-info-circle me-2"></i>
                                    Untuk mengatur data kontrol, silahkan memilih cawu dan tahun terlebih dahulu.
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label for="cawu" class="form-label">Cawu</label>
                                            <select id="cawu" name="cawu" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="1" <?php if ($cawu == 1) echo 'selected'; ?>>Cawu 1
                                                </option>
                                                <option value="2" <?php if ($cawu == 2) echo 'selected'; ?>>Cawu 2
                                                </option>
                                                <option value="3" <?php if ($cawu == 3) echo 'selected'; ?>>Cawu 3
                                                </option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="year" value="<?php echo $year; ?>">
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label for="year" class="form-label">Tahun</label>
                                            <select id="year" name="year" class="form-select"
                                                onchange="this.form.submit()">
                                                <?php foreach ($years as $yr): ?>
                                                <option value="<?php echo $yr; ?>"
                                                    <?php if ($year == $yr) echo 'selected'; ?>>
                                                    <?php echo $yr; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                    </form>
                                </div>
                            </div>

                            <!-- Tampilkan informasi cawu dan tahun yang dipilih -->
                            <div class="row p-3">
                                <div class="col-md-12">
                                    <div class="alert alert-secondary" role="alert">
                                        Anda telah memilih Cawu <?php echo $cawu; ?> untuk tahun <?php echo $year; ?>.
                                    </div>
                                </div>
                            </div>

                            <!-- Search, Cawu and Year Dropdown -->
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <div class="flex-grow-1 me-2">
                                        <label for="search" class="form-label">Cari kode inventaris</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                            placeholder="Masukkan kode..."
                                            value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                    <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                                    <div class="d-flex align-items-end">
                                        <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                    </div>
                                </form>
                            </div>
                            <!-- Form limit -->
                            <div class="col-md-6">
                                <form method="GET" class="d-flex justify-content-end align-items-center">
                                    <div class="flex-grow-1">
                                        <label for="limit" class="form-label">Tampilkan</label>
                                        <select id="limit" name="limit" class="form-select"
                                            onchange="changeLimit(this.value)">
                                            <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5
                                            </option>
                                            <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10
                                            </option>
                                            <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Menyertakan parameter lain yang sedang aktif -->
                                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                                    <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                                </form>
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
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        $no = $offset + 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $statusText = '';
                                            switch ($row['status_kontrol']) {
                                                case 1:
                                                    $statusText = '<span class="badge bg-success">Baik</span>';
                                                    break;
                                                case 2:
                                                    $statusText = '<span class="badge bg-warning">Pindah</span>';
                                                    break;
                                                case 3:
                                                    $statusText = '<span class="badge bg-danger">Rusak</span>';
                                                    break;
                                                case 4:
                                                    $statusText = '<span class="badge bg-dark">Hilang</span>';
                                                    break;
                                            }
                                    ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $no++; ?></td>
                                        <td class="align-middle"><?php echo $row['kode_inventaris']; ?></td>
                                        <td class="align-middle"><?php echo $row['nama_barang']; ?></td>
                                        <td class="align-middle">
                                            <?php echo date('d/m/Y', strtotime($row['tanggal_kontrol'])); ?></td>
                                        <td class="align-middle"><?php echo $statusText; ?></td>
                                        <td class="align-middle"><?php echo $row['jumlah_kontrol']; ?></td>
                                        <td class="align-middle"><?php echo $row['keterangan']; ?></td>
                                        <td class="align-middle">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?php echo $row[$cawuIdField]; ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal<?php echo $row[$cawuIdField]; ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editModal<?php echo $row[$cawuIdField]; ?>"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Kontrol Barang</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="kontrolBarang.php">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id_kontrol"
                                                            value="<?php echo $row[$cawuIdField]; ?>">
                                                        <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                                        <input type="hidden" name="year" value="<?php echo $year; ?>">
                                                        <input type="hidden" name="id_inventaris"
                                                            value="<?php echo $row['id_inventaris']; ?>">

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal</label>
                                                            <input type="date" name="tanggal" class="form-control"
                                                                value="<?php echo date('Y-m-d', strtotime($row['tanggal_kontrol'])); ?>"
                                                                required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="1"
                                                                    <?php echo ($row['status_kontrol'] == 1) ? 'selected' : ''; ?>>
                                                                    Baik</option>
                                                                <option value="2"
                                                                    <?php echo ($row['status_kontrol'] == 2) ? 'selected' : ''; ?>>
                                                                    Pindah</option>
                                                                <option value="3"
                                                                    <?php echo ($row['status_kontrol'] == 3) ? 'selected' : ''; ?>>
                                                                    Rusak</option>
                                                                <option value="4"
                                                                    <?php echo ($row['status_kontrol'] == 4) ? 'selected' : ''; ?>>
                                                                    Hilang</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Jumlah</label>
                                                            <input type="number" name="jumlah" class="form-control"
                                                                value="<?php echo $row['jumlah_kontrol']; ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Keterangan</label>
                                                            <textarea name="keterangan" class="form-control" rows="3"
                                                                required><?php echo $row['keterangan']; ?></textarea>
                                                        </div>

                                                        <div class="d-flex justify-content-end gap-2">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Delete -->
                                    <div class="modal fade" id="deleteModal<?php echo $row[$cawuIdField]; ?>"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus data kontrol barang ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <a href="kontrolBarang.php?delete=<?php echo $row[$cawuIdField]; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"
                                                        class="btn btn-danger">Hapus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php 
                                            } // End of while loop
                                        } else { // Jika tidak ada data
                                        ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data kontrol barang untuk
                                            Cawu
                                            <?php echo $cawu; ?> tahun <?php echo $year; ?>.</td>
                                    </tr>
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
                                        href="?page=<?php echo ($page - 1); ?>&limit=<?php echo $limit; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"
                                        aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php } ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link"
                                        href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php } ?>

                                <?php if ($page < $totalPages) { ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page=<?php echo ($page + 1); ?>&limit=<?php echo $limit; ?>&cawu=<?php echo $cawu; ?>&year=<?php echo $year; ?>"
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
                                    <form method="POST" action="kontrolBarang.php" id="tambahKontrolForm">
                                        <input type="hidden" name="tambahKontrol" value="1">
                                        <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                        <input type="hidden" name="year" value="<?php echo $year; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Inventaris</label>
                                            <select name="id_inventaris" class="form-select" required>
                                                <option value="">Pilih Barang</option>
                                                <?php
            $invResult = getAvailableInventaris($conn, $year);
            while ($inv = mysqli_fetch_assoc($invResult)) {
                echo "<option value='" . $inv['id_inventaris'] . "' data-stock='" . ($inv['jumlah'] - $inv['jumlah_terkontrol']) . "'>"
                    . $inv['kode_inventaris'] . " - "
                    . $inv['nama_barang'] . " (Total: " . $inv['jumlah'] . ", Belum terkontrol: " . ($inv['jumlah'] - $inv['jumlah_terkontrol']) . " " . $inv['satuan'] . ")</option>";
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
                                            <select name="status" class="form-select" required>
                                                <option value="1">Baik</option>
                                                <option value="2">Pindah</option>
                                                <option value="3">Rusak</option>
                                                <option value="4">Hilang</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="jumlah" class="form-control" min="1" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="keterangan" class="form-control" rows="3"
                                                required></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



<?php require('../layouts/assetsFooter.php') ?>