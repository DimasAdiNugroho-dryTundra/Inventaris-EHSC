<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudKontrolBarangCawuSatu.php');
require('../layouts/header.php');

$tahun = isset($_GET['year']) ? (int) $_GET['year'] : date('Y');
$tahunSekarang = date('Y');
$tahunRange = range($tahunSekarang - 5, $tahunSekarang + 5);
?>

<style>
.w-30px {
    width: 30px !important;
}

.w-50px {
    width: 50px !important;
}

.w-70px {
    width: 70px !important;
}

.w-80px {
    width: 80px !important;
}

.w-100px {
    width: 100px !important;
}

.table-sm td,
.table-sm th {
    padding: 0.25rem !important;
    font-size: 0.8rem !important;
}

.btn-sm {
    padding: 0.2rem 0.5rem !important;
    font-size: 0.75rem !important;
}

.status-cell {
    width: 35px !important;
    padding: 0.2rem !important;
}
</style>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>
        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h2 class="mb-1">Manajemen Kontrol Barang Caturwulan 1</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Kontrol Barang Caturwulan 1</li>
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
                                    <a href="pilihCawuKontrolBarang.php" class="btn btn-secondary btn-sm ms-auto">
                                        <i class="ti ti-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                            <div class="row p-3">
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="ti ti-info-circle me-2"></i>
                                        Untuk mengatur data, silahkan memilih tahun terlebih dahulu.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <form method="GET">
                                        <div class="flex-grow-1 me-2">
                                            <label for="year" class="form-label">Tahun</label>
                                            <select id="year" name="year" class="form-select"
                                                onchange="this.form.submit()">
                                                <?php foreach ($tahunRange as $thn): ?>
                                                <option value="<?php echo $thn; ?>"
                                                    <?php echo $thn == $tahun ? 'selected' : ''; ?>>
                                                    <?php echo $thn; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="limit" value="<?php echo $limit; ?>">
                                        <?php if (!empty($search)): ?>
                                        <input type="hidden" name="search"
                                            value="<?php echo htmlspecialchars($search); ?>">
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                            <div class="card">
                                <h4 class="card-header d-flex justify-content-between align-items-center">
                                    Data Kontrol Barang Caturwulan 1
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#tambahKontrolModal">
                                        Tambah Kontrol
                                    </button>
                                </h4>
                                <div class="row p-3">
                                    <!-- Search, Cawu and Year Dropdown -->
                                    <div class="col-md-6">
                                        <form method=GET" class="d-flex">
                                            <div class="flex-grow-1 me-2">
                                                <label for="search" class="form-label">Cari kode inventaris</label>
                                                <input type="text" class="form-control" id="search" name="search"
                                                    placeholder="Masukkan kode..."
                                                    value="<?php echo htmlspecialchars($search); ?>">
                                            </div>
                                            <input type="hidden" name="year" value="<?php echo $tahun; ?>">
                                            <div class="d-flex align-items-end">
                                                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Form limit -->
                                    <div class="col-md-6">
                                        <form method="GET">
                                            <label for="limit" class="form-label">Tampilkan: </label>
                                            <select id="limit" name="limit" class="select2 form-select"
                                                onchange="this.form.submit()">
                                                <?php
                                                $limitOptions = [5, 10, 20];
                                                foreach ($limitOptions as $option) {
                                                    $selected = $limit == $option ? 'selected' : '';
                                                    echo "<option value='{$option}' {$selected}>{$option}</option>";
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" name="year" value="<?php echo $tahun; ?>">
                                            <?php if (!empty($search)): ?>
                                            <input type="hidden" name="search"
                                                value="<?php echo htmlspecialchars($search); ?>">
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                                <div class="row p-3">
                                    <div class="col-md-12">
                                        <div class="alert alert-secondary" role="alert">
                                            Data yang tampil adalah Cawu 1 untuk tahun
                                            <?php echo $tahun; ?>.
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive text-nowrap">
                                    <table class="table table-hover table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th rowspan="2" class="text-center align-middle w-30px">No</th>
                                                <th rowspan="2" class="text-center align-middle w-80px">Kode</th>
                                                <th rowspan="2" class="text-center align-middle w-100px">Barang</th>
                                                <th rowspan="2" class="text-center align-middle w-70px">Tanggal</th>
                                                <th colspan="4" class="text-center align-middle w-140px">Jumlah</th>
                                                <th rowspan="2" class="text-center align-middle w-80px">Petugas</th>
                                                <th rowspan="2" class="text-center align-middle w-100px">Aksi</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center align-middle status-cell">B</th>
                                                <th class="text-center align-middle status-cell">R</th>
                                                <th class="text-center align-middle status-cell">P</th>
                                                <th class="text-center align-middle status-cell">H</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (mysqli_num_rows($result) > 0) {
                                                $no = $offset + 1; // Calculate the correct row number
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $tampil_nama_merk_ruangan = $row['nama_barang'] . ' - ' . $row['merk'] . ' - ' . $row['nama_ruangan'];
                                            ?>
                                            <tr>
                                                <td class="text-center align-middle"><?php echo $no++; ?></td>
                                                <td class="text-center align-middle">
                                                    <?php echo $row['kode_inventaris']; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php echo $tampil_nama_merk_ruangan; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php echo date('d/m/Y', strtotime($row['tanggal_kontrol'])); ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php echo $row['jumlah_baik']; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php echo $row['jumlah_rusak']; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php echo $row['jumlah_pindah']; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php echo $row['jumlah_hilang']; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php echo $row['nama_petugas']; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editModal<?php echo $row['id_kontrol_barang_cawu_satu']; ?>">
                                                            Edit
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal<?php echo $row['id_kontrol_barang_cawu_satu']; ?>">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit -->
                                            <div class="modal fade"
                                                id="editModal<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Kontrol Barang Catuwulan 1</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-warning" role="alert">
                                                                <i class="ti ti-alert-circle me-2"></i>
                                                                <strong>Peringatan!</strong> Harap periksa dengan teliti
                                                                data yang akan diubah:
                                                                <ul class="mb-0 mt-2">
                                                                    <li>Pastikan jumlah barang yang diinput sesuai
                                                                        dengan kondisi fisik</li>
                                                                    <li>Periksa kembali tanggal kontrol</li>
                                                                    <li>Pastikan status dan jumlah barang sudah benar
                                                                    </li>
                                                                    <li>Data yang sudah disimpan tidak dapat dibatalkan,
                                                                        jika ada data kontrol selanjutnya
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="update">
                                                                <input type="hidden" name="id_kontrol"
                                                                    value="<?php echo $row['id_kontrol_barang_cawu_satu']; ?>">
                                                                <input type="hidden" name="year"
                                                                    value="<?php echo $tahun; ?>">

                                                                <div class="mb-3">
                                                                    <label class="form-label">Inventaris</label>
                                                                    <input type="text" class="form-control bg-light"
                                                                        value="<?php 
                                                                                $tampil_nama_merk_ruangan = $row['nama_barang'] . ' - ' . $row['merk'] . ' - ' . $row['nama_ruangan'];
                                                                                echo $tampil_nama_merk_ruangan . ' (Kode: ' . $row['kode_inventaris'] . ', Jumlah terkontrol: ' . 
                                                                                    ($row['jumlah_baik'] + $row['jumlah_rusak'] + $row['jumlah_pindah'] + $row['jumlah_hilang']) . ')'; 
                                                                                ?>" readonly>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Tanggal</label>
                                                                    <input type="date" name="tanggal"
                                                                        class="form-control"
                                                                        value="<?php echo date('Y-m-d', strtotime($row['tanggal_kontrol'])); ?>"
                                                                        required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="mb-3">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <label class="form-check-label">Baik</label>
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    id="switchBaikEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                    onchange="toggleInputEdit(this, 'inputBaikEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>')"
                                                                                    <?php echo ($row['jumlah_baik'] > 0) ? 'checked' : ''; ?>>
                                                                                <input type="hidden" name="status[baik]"
                                                                                    id="status_inputBaikEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                    value="<?php echo ($row['jumlah_baik'] > 0) ? '1' : '0'; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <input type="number" name="jumlah_baik"
                                                                            id="inputBaikEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                            class="form-control <?php echo ($row['jumlah_baik'] > 0) ? '' : 'bg-light'; ?>"
                                                                            min="1"
                                                                            value="<?php echo ($row['jumlah_baik'] > 0) ? $row['jumlah_baik'] : ''; ?>"
                                                                            <?php echo ($row['jumlah_baik'] > 0) ? '' : 'disabled'; ?>>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <label class="form-check-label">Rusak</label>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                id="switchRusakEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                onchange="toggleInputEdit(this, 'inputRusakEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>')"
                                                                                <?php echo ($row['jumlah_rusak'] > 0) ? 'checked' : ''; ?>>
                                                                            <input type="hidden" name="status[rusak]"
                                                                                id="status_inputRusakEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                value="<?php echo ($row['jumlah_rusak'] > 0) ? '1' : '0'; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <input type="number" name="jumlah_rusak"
                                                                        id="inputRusakEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                        class="form-control <?php echo ($row['jumlah_rusak'] > 0) ? '' : 'bg-light'; ?>"
                                                                        min="1"
                                                                        value="<?php echo ($row['jumlah_rusak'] > 0) ? $row['jumlah_rusak'] : ''; ?>"
                                                                        <?php echo ($row['jumlah_rusak'] > 0) ? '' : 'disabled'; ?>>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <label class="form-check-label">Pindah</label>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                id="switchPindahEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                onchange="toggleInputEdit(this, 'inputPindahEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>')"
                                                                                <?php echo ($row['jumlah_pindah'] > 0) ? 'checked' : ''; ?>>
                                                                            <input type="hidden" name="status[pindah]"
                                                                                id="status_inputPindahEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                value="<?php echo ($row['jumlah_pindah'] > 0) ? '1' : '0'; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <input type="number" name="jumlah_pindah"
                                                                        id="inputPindahEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                        class="form-control <?php echo ($row['jumlah_pindah'] > 0) ? '' : 'bg-light'; ?>"
                                                                        min="1"
                                                                        value="<?php echo ($row['jumlah_pindah'] > 0) ? $row['jumlah_pindah'] : ''; ?>"
                                                                        <?php echo ($row['jumlah_pindah'] > 0) ? '' : 'disabled'; ?>>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <label class="form-check-label">Hilang</label>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                id="switchHilangEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                onchange="toggleInputEdit(this, 'inputHilangEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>')"
                                                                                <?php echo ($row['jumlah_hilang'] > 0) ? 'checked' : ''; ?>>
                                                                            <input type="hidden" name="status[hilang]"
                                                                                id="status_inputHilangEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                                value="<?php echo ($row['jumlah_hilang'] > 0) ? '1' : '0'; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <input type="number" name="jumlah_hilang"
                                                                        id="inputHilangEdit<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                                        class="form-control <?php echo ($row['jumlah_hilang'] > 0) ? '' : 'bg-light'; ?>"
                                                                        min="1"
                                                                        value="<?php echo ($row['jumlah_hilang'] > 0) ? $row['jumlah_hilang'] : ''; ?>"
                                                                        <?php echo ($row['jumlah_hilang'] > 0) ? '' : 'disabled'; ?>>
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
                                                id="deleteModal<?php echo $row['id_kontrol_barang_cawu_satu']; ?>"
                                                tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus data kontrol barang cawu 1
                                                            ini?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <a href="kontrolBarangCawuSatu.php?delete=<?php echo $row['id_kontrol_barang_cawu_satu']; ?>&year=<?php echo $tahun; ?>"
                                                                class="btn btn-danger">Hapus</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                        }
                                    } else {
                                    ?>
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data kontrol barang cawu
                                                    1
                                                    untuk
                                                    tahun <?php echo $tahun; ?>.</td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <nav aria-label="Page navigation" class="mt-4 mb-4">
                                    <ul class="pagination pagination-rounded justify-content-center">
                                        <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo ($page - 1); ?>&limit=<?php echo $limit; ?>&year=<?php echo $tahun; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                                                aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&year=<?php echo $tahun; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo ($page + 1); ?>&limit=<?php echo $limit; ?>&year=<?php echo $tahun; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                                                aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>

                            <!-- Modal Tambah Kontrol -->
                            <div class="modal fade" id="tambahKontrolModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Kontrol Barang Catuwulan 1</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-warning" role="alert">
                                                <i class="ti ti-alert-circle me-2"></i>
                                                <strong>Peringatan!</strong> Harap perhatikan hal-hal berikut sebelum
                                                menambah data:
                                                <ul class="mb-0 mt-2">
                                                    <li>Pilih barang dengan teliti sesuai kode inventaris</li>
                                                    <li>Pastikan jumlah barang sesuai dengan perhitungan fisik</li>
                                                    <li>Periksa kembali tanggal kontrol</li>
                                                    <li>Minimal pilih satu status kondisi barang</li>
                                                    <li>Data yang sudah disimpan tidak dapat dibatalkan,
                                                        jika ada data kontrol selanjutnya</li>
                                                </ul>
                                            </div>
                                            <form method="POST" id="tambahKontrolForm">
                                                <input type="hidden" name="tambahKontrol" value="1">
                                                <input type="hidden" name="year" value="<?php echo $tahun; ?>">

                                                <div class="mb-3">
                                                    <label class="form-label">Inventaris</label>
                                                    <select name="id_inventaris" class="form-select" required>
                                                        <option value="">Pilih Barang</option>
                                                        <?php
                                                        $result = getInventarisTersedia($conn, $tahun, 'kontrol_barang_cawu_satu');
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            $tampil_nama_merk_ruangan = $row['nama_barang'] . ' - ' . $row['merk'] . ' - ' . $row['nama_ruangan'];
                                                            echo "<option value=\"{$row['id_inventaris']}\">{$tampil_nama_merk_ruangan} (Kode: {$row['kode_inventaris']}, Jumlah: {$row['jumlah']})</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal</label>
                                                    <input type="date" name="tanggal" class="form-control" required>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-check-label">Baik</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="switchBaik"
                                                                onchange="toggleInput(this, 'inputBaik')">
                                                            <input type="hidden" name="status[baik]"
                                                                id="status_inputBaik" value="0">
                                                        </div>
                                                    </div>
                                                    <input type="number" name="jumlah_baik" id="inputBaik"
                                                        class="form-control bg-light" min="1" disabled>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-check-label">Rusak</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="switchRusak"
                                                                onchange="toggleInput(this, 'inputRusak')">
                                                            <input type="hidden" name="status[rusak]"
                                                                id="status_inputRusak" value="0">
                                                        </div>
                                                    </div>
                                                    <input type="number" name="jumlah_rusak" id="inputRusak"
                                                        class="form-control bg-light" min="1" disabled>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-check-label">Pindah</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="switchPindah"
                                                                onchange="toggleInput(this, 'inputPindah')">
                                                            <input type="hidden" name="status[pindah]"
                                                                id="status_inputPindah" value="0">
                                                        </div>
                                                    </div>
                                                    <input type="number" name="jumlah_pindah" id="inputPindah"
                                                        class="form-control bg-light" min="1" disabled>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-check-label">Hilang</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="switchHilang"
                                                                onchange="toggleInput(this, 'inputHilang')">
                                                            <input type="hidden" name="status[hilang]"
                                                                id="status_inputHilang" value="0">
                                                        </div>
                                                    </div>
                                                    <input type="number" name="jumlah_hilang" id="inputHilang"
                                                        class="form-control bg-light" min="1" disabled>
                                                </div>

                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Tambah</button>
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
            <?php require('../layouts/footer.php'); ?>
        </div>
    </div>
</div>
<?php require('../layouts/assetsFooter.php'); ?>
</div>

<script>
function toggleInput(checkbox, inputId) {
    const input = document.getElementById(inputId);
    const statusInput = document.getElementById('status_' + inputId);
    if (checkbox.checked) {
        input.disabled = false;
        input.classList.remove('bg-light');
        statusInput.value = '1';
    } else {
        input.disabled = true;
        input.classList.add('bg-light');
        input.value = '';
        statusInput.value = '0';
    }
}

function toggleInputEdit(checkbox, inputId) {
    const input = document.getElementById(inputId);
    const statusInput = document.getElementById('status_' + inputId);
    if (checkbox.checked) {
        input.disabled = false;
        input.classList.remove('bg-light');
        statusInput.value = '1';
    } else {
        input.disabled = true;
        input.classList.add('bg-light');
        input.value = '';
        statusInput.value = '0';
    }
}

// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'inventaris': 'Kolom inventaris wajib diisi!',
        'tanggal': 'Kolom tanggal wajib diisi!',
        'baik': 'Kolom jumlah barang baik wajib diisi!',
        'rusak': 'Kolom jumlah barang rusak wajib diisi!',
        'pindah': 'Kolom jumlah barang pindah wajib diisi!',
        'hilang': 'Kolom jumlah barang hilang wajib diisi!'
    };

    return pesanKhusus[labelText] ||
        (jenisInput === 'select' ? `Mohon pilih ${labelText}` : `Mohon masukkan ${labelText}`);
}

// Fungsi untuk menghapus pesan error
function hapusPesanError(element) {
    element.addEventListener('input', function() {
        this.setCustomValidity('');
    });
}

// Fungsi untuk menerapkan validasi
function terapkanValidasi() {
    const elemenWajib = document.querySelectorAll('input[required], select[required], textarea[required]');

    elemenWajib.forEach(elemen => {
        // Atur pesan error kustom
        elemen.oninvalid = function(e) {
            if (e.target.validity.valueMissing) {
                const labelElemen = elemen.previousElementSibling;
                const labelTeks = labelElemen ? labelElemen.textContent : '';
                const jenisInput = elemen.tagName.toLowerCase();

                e.target.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
            }
        };

        // Hapus pesan error saat mulai diisi
        hapusPesanError(elemen);
    });
}

// Fungsi untuk validasi form manual
function validasiFormManual() {
    const formManual = document.querySelectorAll('.needs-validation');
    if (formManual) {
        formManual.forEach(form => {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            inputs.forEach(input => {
                if (input.hasAttribute('required')) {
                    // Atur pesan error kustom
                    input.oninvalid = function(e) {
                        if (e.target.validity.valueMissing) {
                            const labelElemen = input.previousElementSibling;
                            const labelTeks = labelElemen ? labelElemen.textContent : '';
                            const jenisInput = input.tagName.toLowerCase();

                            e.target.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
                        }
                    };

                    // Hapus pesan error saat mulai diisi
                    hapusPesanError(input);
                }
            });
        });
    }
}

function tampilAlertStatus(modalElement, message) {
    const existingAlert = modalElement.querySelector('.alert-status');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Buat elemen alert baru
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible d-flex align-items-center mb-3 alert-status';
    alertDiv.setAttribute('role', 'alert');

    // Tambahkan ikon dan pesan
    alertDiv.innerHTML = `
        <span class="alert-icon rounded me-2">
            <i class="ti ti-alert-circle"></i>
        </span>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    const form = modalElement.querySelector('form');
    form.insertBefore(alertDiv, form.firstChild);
}

// Validasi form tambah kontrol 
document.getElementById('tambahKontrolForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const statusBaik = document.getElementById('status_inputBaik').value;
    const statusRusak = document.getElementById('status_inputRusak').value;
    const statusPindah = document.getElementById('status_inputPindah').value;
    const statusHilang = document.getElementById('status_inputHilang').value;

    let valid = true;

    if (statusBaik === '0' && statusRusak === '0' && statusPindah === '0' && statusHilang === '0') {
        valid = false;
        const modalElement = document.getElementById('tambahKontrolModal');
        tampilAlertStatus(modalElement, 'Minimal pilih satu status kondisi barang!');
    }

    if (statusBaik === '1' && !document.getElementById('inputBaik').value) {
        valid = false;
        const inputBaik = document.getElementById('inputBaik');
        inputBaik.setCustomValidity(getPesanValidasi('baik', 'input'));
        inputBaik.reportValidity(); // Menampilkan pesan validasi segera
        inputBaik.addEventListener('input', function() {
            if (this.value && this.value.trim() !== '') {
                this.setCustomValidity('');
                this.reportValidity();
            }
        });
    }

    if (statusRusak === '1' && !document.getElementById('inputRusak').value) {
        valid = false;
        const inputRusak = document.getElementById('inputRusak');
        inputRusak.setCustomValidity(getPesanValidasi('rusak', 'input'));
        inputRusak.reportValidity(); // Menampilkan pesan validasi segera
        inputRusak.addEventListener('input', function() {
            if (this.value && this.value.trim() !== '') {
                this.setCustomValidity('');
                this.reportValidity();
            }
        });
    }

    if (statusPindah === '1' && !document.getElementById('inputPindah').value) {
        valid = false;
        const inputPindah = document.getElementById('inputPindah');
        inputPindah.setCustomValidity(getPesanValidasi('pindah', 'input'));
        inputPindah.reportValidity(); // Menampilkan pesan validasi segera
        inputPindah.addEventListener('input', function() {
            if (this.value && this.value.trim() !== '') {
                this.setCustomValidity('');
                this.reportValidity();
            }
        });
    }

    if (statusHilang === '1' && !document.getElementById('inputHilang').value) {
        valid = false;
        const inputHilang = document.getElementById('inputHilang');
        inputHilang.setCustomValidity(getPesanValidasi('hilang', 'input'));
        inputHilang.reportValidity(); // Menampilkan pesan validasi segera
        inputHilang.addEventListener('input', function() {
            if (this.value && this.value.trim() !== '') {
                this.setCustomValidity('');
                this.reportValidity();
            }
        });
    }

    // Validasi input wajib lainnya
    const elemenWajib = this.querySelectorAll('input[required], select[required], textarea[required]');

    elemenWajib.forEach(elemen => {
        if (elemen.validity.valueMissing) {
            const labelElemen = elemen.previousElementSibling;
            const labelTeks = labelElemen ? labelElemen.textContent : '';
            const jenisInput = elemen.tagName.toLowerCase();
            elemen.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
            elemen.reportValidity(); // Menampilkan pesan validasi segera
        } else {
            elemen.setCustomValidity('');
        }
    });

    if (valid && this.checkValidity()) {
        this.submit(); // Submit form hanya jika semua validasi berhasil
    }
});

// Validasi form edit kontrol
document.querySelectorAll('[id^="editModal"]').forEach(modal => {
    modal.querySelector('form').addEventListener('submit', function(event) {
        event.preventDefault();

        const modalId = modal.id.replace('editModal', '');

        const statusBaikEdit = document.getElementById(`status_inputBaikEdit${modalId}`).value;
        const statusRusakEdit = document.getElementById(`status_inputRusakEdit${modalId}`).value;
        const statusPindahEdit = document.getElementById(`status_inputPindahEdit${modalId}`).value;
        const statusHilangEdit = document.getElementById(`status_inputHilangEdit${modalId}`).value;

        let valid = true;

        if (statusBaikEdit === '0' && statusRusakEdit === '0' && statusPindahEdit === '0' &&
            statusHilangEdit === '0') {
            valid = false;
            tampilAlertStatus(modal, 'Minimal pilih satu status kondisi barang!');
        }

        if (statusBaikEdit === '1' && !document.getElementById(`inputBaikEdit${modalId}`).value) {
            valid = false;
            const inputBaikEdit = document.getElementById(`inputBaikEdit${modalId}`);
            inputBaikEdit.setCustomValidity(getPesanValidasi('baik', 'input'));
            inputBaikEdit.reportValidity(); // Menampilkan pesan validasi segera
            inputBaikEdit.addEventListener('input', function() {
                if (this.value && this.value.trim() !== '') {
                    this.setCustomValidity('');
                    this.reportValidity();
                }
            });
        }

        if (statusRusakEdit === '1' && !document.getElementById(`inputRusakEdit${modalId}`).value) {
            valid = false;
            const inputRusakEdit = document.getElementById(`inputRusakEdit${modalId}`);
            inputRusakEdit.setCustomValidity(getPesanValidasi('rusak', 'input'));
            inputRusakEdit.reportValidity(); // Menampilkan pesan validasi segera
            inputRusakEdit.addEventListener('input', function() {
                if (this.value && this.value.trim() !== '') {
                    this.setCustomValidity('');
                    this.reportValidity();
                }
            });
        }

        if (statusPindahEdit === '1' && !document.getElementById(`inputPindahEdit${modalId}`).value) {
            valid = false;
            const inputPindahEdit = document.getElementById(`inputPindahEdit${modalId}`);
            inputPindahEdit.setCustomValidity(getPesanValidasi('pindah', 'input'));
            inputPindahEdit.reportValidity(); // Menampilkan pesan validasi segera
            inputPindahEdit.addEventListener('input', function() {
                if (this.value && this.value.trim() !== '') {
                    this.setCustomValidity('');
                    this.reportValidity();
                }
            });
        }

        if (statusHilangEdit === '1' && !document.getElementById(`inputHilangEdit${modalId}`).value) {
            valid = false;
            const inputHilangEdit = document.getElementById(`inputHilangEdit${modalId}`);
            inputHilangEdit.setCustomValidity(getPesanValidasi('hilang', 'input'));
            inputHilangEdit.reportValidity(); // Menampilkan pesan validasi segera
            inputHilangEdit.addEventListener('input', function() {
                if (this.value && this.value.trim() !== '') {
                    this.setCustomValidity('');
                    this.reportValidity();
                }
            });
        }

        // Validasi input wajib lainnya
        const elemenWajib = this.querySelectorAll(
            'input[required], select[required], textarea[required]');

        elemenWajib.forEach(elemen => {
            if (elemen.validity.valueMissing) {
                const labelElemen = elemen.previousElementSibling;
                const labelTeks = labelElemen ? labelElemen.textContent : '';
                const jenisInput = elemen.tagName.toLowerCase();
                elemen.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
                elemen.reportValidity(); // Menampilkan pesan validasi segera
            } else {
                elemen.setCustomValidity('');
            }
        });

        if (valid && this.checkValidity()) {
            this.submit(); // Submit form hanya jika semua validasi berhasil
        }
    });
});

// Event listener saat modal tambah dibuka
document.getElementById('tambahKontrolModal').addEventListener('show.bs.modal', function() {
    // Reset form saat modal dibuka
    const form = this.querySelector('form');
    if (form) form.reset();

    // Terapkan validasi
    setTimeout(terapkanValidasi, 100);
});

// Event listener saat modal edit dibuka
document.querySelectorAll('[id^="editModal"]').forEach(modal => {
    modal.addEventListener('show.bs.modal', function() {
        setTimeout(terapkanValidasi, 100);
    });
});

// Event listener saat dokumen dimuat
document.addEventListener('DOMContentLoaded', function() {
    terapkanValidasi();
    validasiFormManual();
});
</script>