<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../server/crudKontrolBarang.php');
require('../layouts/header.php');

// Ambil tahun yang tersedia dari database
$cawu = isset($_POST['cawu']) ? $_POST['cawu'] : (isset($_GET['cawu']) ? $_GET['cawu'] : 1);
$year = isset($_POST['year']) ? $_POST['year'] : (isset($_GET['year']) ? $_GET['year'] : date('Y'));

// Pastikan $cawu dan $year adalah integer
$cawu = intval($cawu);
$year = intval($year);

$currentYear = date('Y');
$years = range($currentYear - 5, $currentYear + 5); // Tahun dari 5 tahun lalu hingga 5 tahun ke depan

// Tentukan tanggal berdasarkan cawu
$startDate = '';
$endDate = '';
if ($cawu == 1) {
    $startDate = "$year-01-01";
    $endDate = "$year-04-30";
} elseif ($cawu == 2) {
    $startDate = "$year-05-01";
    $endDate = "$year-08-31";
} elseif ($cawu == 3) {
    $startDate = "$year-09-01";
    $endDate = "$year-12-31";
}

// Tentukan tabel berdasarkan cawu
$table = '';
$idColumn = '';
if ($cawu == 1) {
    $table = 'kontrol_barang_cawu_satu';
    $idColumn = 'id_kontrol_barang_cawu_satu';
} elseif ($cawu == 2) {
    $table = 'kontrol_barang_cawu_dua';
    $idColumn = 'id_kontrol_barang_cawu_dua';
} elseif ($cawu == 3) {
    $table = 'kontrol_barang_cawu_tiga';
    $idColumn = 'id_kontrol_barang_cawu_tiga';
}

// Query untuk mengambil data kontrol barang berdasarkan cawu dan tahun
$query = "SELECT kb.*, i.kode_inventaris, i.nama_barang, u.nama as nama_petugas 
          FROM $table kb 
          JOIN inventaris i ON kb.id_inventaris = i.id_inventaris 
          JOIN user u ON kb.id_user = u.id_user 
          WHERE YEAR(kb.tanggal_kontrol) = '$year' 
          ORDER BY kb.$idColumn DESC";

$result = mysqli_query($conn, $query);
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

                    <!-- Informasi Petugas -->
                    <div class="card">
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge bg-label-primary p-2 me-2">
                                    <i class="ti ti-user"></i>
                                </div>
                                <h5 class="mb-0">Petugas: <?php echo $_SESSION['nama']; ?></h5>
                            </div>
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
                        <h4 class="card-header d-flex justify-content-between align-items-center">
                            Data Kontrol Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahKontrolModal">
                                Tambah Kontrol
                            </button>
                        </h4>

                        <!-- Search, Cawu and Year Dropdown -->
                        <div class="row p-3">
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
                                    <label for="limit" class="form-label me-2">Tampilkan:</label>
                                    <select id="limit" name="limit" class="form-select"
                                        onchange="changeLimit(this.value)">
                                        <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>5</option>
                                        <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                                        <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option>
                                    </select>
                                    <!-- Menyertakan parameter lain yang sedang aktif -->
                                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                                    <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                                </form>
                            </div>
                        </div>


                        <!-- Dropdown for Cawu and Year -->
                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="cawu" class="form-label">Cawu</label>
                                        <select id="cawu" name="cawu" class="form-select" onchange="this.form.submit()">
                                            <option value="1" <?php if ($cawu == 1) echo 'selected'; ?>>Cawu 1</option>
                                            <option value="2" <?php if ($cawu == 2) echo 'selected'; ?>>Cawu 2</option>
                                            <option value="3" <?php if ($cawu == 3) echo 'selected'; ?>>Cawu 3</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="year" class="form-label">Tahun</label>
                                        <select id="year" name="year" class="form-select" onchange="this.form.submit()">
                                            <?php foreach ($years as $yr): ?>
                                            <option value="<?php echo $yr; ?>"
                                                <?php if ($year == $yr) echo 'selected'; ?>><?php echo $yr; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
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
                                                    data-bs-target="#editModal<?php echo $row[$idColumn]; ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal<?php echo $row[$idColumn]; ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>

                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade"
                                        id="editModal<?php echo $row['id_kontrol_barang_cawu_dua']; ?>" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
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
                                                            value="<?php echo $row['id_kontrol_barang_cawu_dua']; ?>">
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
                                    <div class="modal fade"
                                        id="deleteModal<?php echo $row['id_kontrol_barang_cawu_dua']; ?>" tabindex="-1"
                                        aria-hidden="true">
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
                                                    <a href="?delete=<?php echo $row['id_kontrol_barang_cawu_dua']; ?>"
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
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Inventaris</label>
                                            <select name="id_inventaris" class="form-select" required>
                                                <option value="">Pilih Barang</option>
                                                <?php
                            $invResult = getAvailableInventaris($conn, $cawu, $year);
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
                                            <select name="status" class="form-select" required
                                                onchange="toggleInputs(this.value, 'add')">
                                                <option value="1">Baik</option>
                                                <option value="2">Pindah</option>
                                                <option value="3">Rusak</option>
                                                <option value="4">Hilang</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="jumlah" id="jumlah-add" class="form-control"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="keterangan" id="keterangan-add" class="form-control"
                                                rows="3" required>Barang dalam kondisi baik</textarea>
                                        </div>

                                        <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                        <input type="hidden" name="year" value="<?php echo $year; ?>">

                                        <button type="submit" name="tambahKontrol"
                                            class="btn btn-primary">Tambah</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
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
function toggleInputs(status, prefix) {
    const jumlahField = document.getElementById('jumlah-' + prefix);
    const keteranganField = document.getElementById('keterangan-' + prefix);

    if (prefix === 'add') {
        const inventarisSelect = document.querySelector('select[name="id_inventaris"]');
        const selectedOption = inventarisSelect.options[inventarisSelect.selectedIndex];
        const totalStock = selectedOption.getAttribute('data-stock');

        // Mengatur nilai default untuk keterangan berdasarkan status
        switch (status) {
            case '1': // Baik
                keteranganField.value = 'Barang dalam kondisi baik';
                break;
            case '2': // Pindah
                keteranganField.value = 'Barang dalam kondisi pindah';
                break;
            case '3': // Rusak
                keteranganField.value = 'Barang dalam kondisi rusak';
                break;
            case '4': // Hilang
                keteranganField.value = 'Barang hilang';
                break;
        }
        jumlahField.value = totalStock; // Mengatur jumlah berdasarkan stok yang tersedia
        jumlahField.readOnly = false; // Mengizinkan pengguna untuk mengedit
    }
}

function changeLimit(limit) {
    var currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('limit', limit);
    currentUrl.searchParams.set('page', '1'); // Reset ke halaman pertama

    // Pastikan parameter cawu dan year tetap ada
    var cawu = currentUrl.searchParams.get('cawu') || '<?php echo $cawu; ?>';
    var year = currentUrl.searchParams.get('year') || '<?php echo $year; ?>';

    currentUrl.searchParams.set('cawu', cawu);
    currentUrl.searchParams.set('year', year);

    window.location.href = currentUrl.toString();
}
</script>
<?php require('../layouts/assetsFooter.php'); ?>