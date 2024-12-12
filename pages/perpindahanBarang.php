<?php
// File: perpindahanBarang.php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudPerpindahanBarang.php'); 
require('../layouts/header.php');
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h2 class="mb-1">Manajemen Perpindahan Barang</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Perpindahan Barang</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="card">

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
                            Data Perpindahan Barang
                            <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahPerpindahanModal">Tambah Perpindahan</button>
                            <?php endif; ?>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari perpindahan barang..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="perpindahanBarang.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="perpindahanBarang.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="perpindahanBarang.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Kode Inventaris</th>
                                        <th class="text-center align-middle">Kode Inventaris Baru</th>
                                        <th class="text-center align-middle">Nama Barang</th>
                                        <th class="text-center align-middle">Ruangan</th>
                                        <th class="text-center align-middle">Tanggal Perpindahan</th>
                                        <th class="text-center align-middle">Cawu</th>
                                        <th class="text-center align-middle">Jumlah Perpindahan</th>
                                        <th class="text-center align-middle">Keterangan</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
        $no = $offset + 1;
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo $no++; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['kode_inventaris']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['kode_inventaris_baru']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['nama_barang']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['nama_ruangan']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['tanggal_perpindahan']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['cawu']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['jumlah_perpindahan']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['keterangan']; ?></td>
                                        <td class="text-center align-middle">
                                            <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_perpindahan_barang']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_perpindahan_barang']; ?>">Delete</button>
                                            <?php endif; ?>
                                            <a href="../report/printLaporanPerpindahanBarang.php?id=<?php echo $row['id_perpindahan_barang']; ?>"
                                                class="btn btn-primary btn-sm">Cetak</a>
                                        </td>
                                    </tr>
                                    <?php
        }
        ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Edit/Update -->
                        <?php
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
?>
                        <div class="modal fade" id="modal-update-<?php echo $row['id_perpindahan_barang']; ?>"
                            tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modalUpdateLabel">Edit Perpindahan Barang</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_perpindahan_barang"
                                                value="<?php echo $row['id_perpindahan_barang']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Kode Inventaris</label>
                                                <input type="text" class="form-control bg-light" name="kode_inventaris"
                                                    value="<?php echo $row['kode_inventaris']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Barang</label>
                                                <input type="text" class="form-control bg-light" name="nama_barang"
                                                    value="<?php echo $row['nama_barang']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ruangan Tujuan</label>
                                                <select name="id_ruangan" class="form-select" required>
                                                    <option value="">Pilih Ruangan</option>
                                                    <?php
                                                    $ruangan_query = "SELECT * FROM ruangan ORDER BY nama_ruangan ASC";
                                                    $ruangan_result = mysqli_query($conn, $ruangan_query);
                                                    while ($ruangan = mysqli_fetch_assoc($ruangan_result)) {
                                                        $selected = ($ruangan['id_ruangan'] == $row['id_ruangan']) ? 'selected' : '';
                                                        echo "<option value='" . $ruangan['id_ruangan'] . "' " . $selected . ">" 
                                                            . $ruangan['nama_ruangan'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Perpindahan</label>
                                                <input type="date" class="form-control bg-light"
                                                    name="tanggal_perpindahan"
                                                    value="<?php echo $row['tanggal_perpindahan']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cawu</label>
                                                <input type="text" class="form-control bg-light" name="cawu"
                                                    value="<?php echo $row['cawu']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Perpindahan</label>
                                                <input type="number" class="form-control bg-light"
                                                    name="jumlah_perpindahan"
                                                    value="<?php echo $row['jumlah_perpindahan']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control" name="keterangan"
                                                    required><?php echo $row['keterangan']; ?></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="modal-delete-<?php echo $row['id_perpindahan_barang']; ?>"
                            tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus perpindahan barang
                                        "<?php echo $row['nama_barang']; ?>"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <a href="perpindahanBarang.php?delete=<?php echo $row['id_perpindahan_barang']; ?>"
                                            class="btn btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- Modal Tambah -->
                        <div class="modal fade" id="tambahPerpindahanModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Perpindahan Barang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label class="form-label">Barang</label>
                                                <select name="id_inventaris" class="form-select" required
                                                    onchange="fillPerpindahanData(this)">
                                                    <option value="">Pilih Barang</option>
                                                    <?php
                                                    $barang = getPerpindahanBarang($conn);
                                                    while ($row = mysqli_fetch_assoc($barang)) {
                                                        echo "<option value='{$row['id_inventaris']}' 
                                                                    data-tanggal='{$row['tanggal_kontrol']}'
                                                                    data-cawu='{$row['cawu']}'
                                                                    data-jumlah='{$row['jumlah_pindah']}'
                                                                    data-tahun='" . date('Y', strtotime($row['tanggal_kontrol'])) . "'>
                                                                {$row['kode_inventaris']} - {$row['nama_barang']} - {$row['cawu']}  - " . date('Y', strtotime($row['tanggal_kontrol'])) . " - {$row['jumlah_pindah']} pindah
                                                            </option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Perpindahan</label>
                                                <input type="date" class="form-control bg-light"
                                                    name="tanggal_perpindahan" id="tanggal_perpindahan" required
                                                    readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cawu</label>
                                                <input type="text" class="form-control bg-light" name="cawu" id="cawu"
                                                    required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Perpindahan</label>
                                                <input type="number" class="form-control bg-light"
                                                    name="jumlah_perpindahan" id="jumlah_perpindahan" required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ruangan</label>
                                                <select name="id_ruangan" class="form-select" required>
                                                    <option value="">Pilih Ruangan</option>
                                                    <?php
                                                    $ruangan_query = "SELECT * FROM ruangan ORDER BY nama_ruangan ASC";
                                                    $ruangan_result = mysqli_query($conn, $ruangan_query);
                                                    while ($ruangan = mysqli_fetch_assoc($ruangan_result)) {
                                                        echo "<option value='" . $ruangan['id_ruangan'] . "'>" . $ruangan['nama_ruangan'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control" name="keterangan" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="tambahPerpindahan"
                                                    class="btn btn-primary">Tambah</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination pagination-rounded justify-content-center">
                                <?php if ($page > 1) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php } ?>
                                <?php if ($page < $totalPages) { ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php require('../layouts/footer.php'); ?>
            </div>
            <div class="content-backdrop fade"></div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>

<script>
function fillPerpindahanData(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    document.getElementById('tanggal_perpindahan').value = selectedOption.dataset.tanggal;
    document.getElementById('cawu').value = selectedOption.dataset.cawu;
    document.getElementById('jumlah_perpindahan').value = selectedOption.dataset.jumlah;
}

// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'barang': 'Silahkan pilih data barang yang pindah!',
        'keterangan': 'Kolom keterangan wajib diisi!'
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

// Event listener saat modal tambah dibuka
document.getElementById('tambahPerpindahanModal').addEventListener('show.bs.modal', function() {
    // Reset form saat modal dibuka
    const form = this.querySelector('form');
    if (form) form.reset();

    // Terapkan validasi
    setTimeout(terapkanValidasi, 100);
});

// Event listener saat modal edit dibuka
document.querySelectorAll('[id^="modal-update-"]').forEach(modal => {
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


<?php require('../layouts/assetsFooter.php'); ?>