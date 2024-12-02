<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudPenerimaanBarang.php');
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
                            <h2 class="mb-1">Manajemen Penerimaan Barang</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Penerimaan Barang</li>
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
                            Data Penerimaan Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahPenerimaanModal">Tambah Penerimaan</button>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari nama barang..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="permintaanBarang.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="permintaanBarang.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="permintaanBarang.php?limit=20"
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
                                        <th class="text-center align-middle">Departemen</th>
                                        <th class="text-center align-middle">Nama Barang</th>
                                        <th class="text-center align-middle">Tanggal Terima</th>
                                        <th class="text-center align-middle">Jumlah</th>
                                        <th class="text-center align-middle">Satuan</th>
                                        <th class="text-center align-middle">Status</th>
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
                                        <td class="text-center align-middle"><?php echo $row['nama_departemen']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['nama_barang']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['tanggal_terima']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['jumlah']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['satuan']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['status']; ?></td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_penerimaan']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_penerimaan']; ?>">Delete</button>
                                            <a href="../report/printLaporanPenerimaanBarang.php?id=<?php echo $row['id_permintaan']; ?>"
                                                class="btn btn-primary btn-sm">Laporan</a>
                        </div>
                        </td>
                        </tr>

                        <!-- Modal Edit/Update -->
                        <div class="modal fade" id="modal-update-<?php echo $row['id_penerimaan']; ?>" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Data Penerimaan Barang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_penerimaan"
                                                value="<?php echo $row['id_penerimaan']; ?>">
                                            <?php
                    // Cek apakah data berasal dari permintaan atau input manual
                    $query_cek = "SELECT id_permintaan FROM penerimaan_barang WHERE id_penerimaan = " . $row['id_penerimaan'];
                    $hasil_cek = mysqli_query($conn, $query_cek);
                    $data_penerimaan = mysqli_fetch_assoc($hasil_cek);
                    
                    if ($data_penerimaan['id_permintaan']): // Jika berasal dari permintaan 
                    ?>
                                            <!-- Form untuk data dari permintaan - editing terbatas -->
                                            <div class="mb-3">
                                                <label class="form-label">Nama Barang</label>
                                                <input type="text" class="form-control bg-light"
                                                    value="<?php echo $row['nama_barang']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Departemen</label>
                                                <input type="text" class="form-control bg-light"
                                                    value="<?php echo $row['nama_departemen']; ?>" readonly>
                                                <input type="hidden" name="id_departemen"
                                                    value="<?php echo $row['id_departemen']; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah</label>
                                                <input type="number" class="form-control bg-light"
                                                    value="<?php echo $row['jumlah']; ?>" readonly>
                                            </div>
                                            <!-- Field yang bisa diedit -->
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Terima</label>
                                                <input type="date" name="tanggal_terima" class="form-control"
                                                    value="<?php echo $row['tanggal_terima']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Satuan</label>
                                                <input type="text" name="satuan" class="form-control"
                                                    value="<?php echo $row['satuan']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="Diterima"
                                                        <?php echo ($row['status'] == 'Diterima') ? 'selected' : ''; ?>>
                                                        Diterima</option>
                                                    <option value="Ditolak"
                                                        <?php echo ($row['status'] == 'Ditolak') ? 'selected' : ''; ?>>
                                                        Ditolak</option>
                                                </select>
                                            </div>
                                            <?php else: // Jika input manual ?>
                                            <!-- Form untuk input manual - bisa edit semua -->
                                            <div class="mb-3">
                                                <label class="form-label">Nama Barang</label>
                                                <input type="text" name="nama_barang" class="form-control"
                                                    value="<?php echo $row['nama_barang']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Departemen</label>
                                                <select name="id_departemen" class="form-select" required>
                                                    <?php
                                $query_dept = "SELECT * FROM departemen ORDER BY 
                                    CASE WHEN id_departemen = '{$row['id_departemen']}' THEN 0 ELSE 1 END";
                                $hasil_dept = mysqli_query($conn, $query_dept);
                                while ($dept = mysqli_fetch_assoc($hasil_dept)) {
                                    $selected = ($dept['id_departemen'] == $row['id_departemen']) ? 'selected' : '';
                                    echo "<option value='" . $dept['id_departemen'] . "' " . $selected . ">" 
                                        . $dept['nama_departemen'] . " (" . $dept['kode_departemen'] . ")</option>";
                                }
                                ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah</label>
                                                <input type="number" name="jumlah" class="form-control"
                                                    value="<?php echo $row['jumlah']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Terima</label>
                                                <input type="date" name="tanggal_terima" class="form-control"
                                                    value="<?php echo $row['tanggal_terima']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Satuan</label>
                                                <input type="text" name="satuan" class="form-control"
                                                    value="<?php echo $row['satuan']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="Diterima"
                                                        <?php echo ($row['status'] == 'Diterima') ? 'selected' : ''; ?>>
                                                        Diterima</option>
                                                    <option value="Ditolak"
                                                        <?php echo ($row['status'] == 'Ditolak') ? 'selected' : ''; ?>>
                                                        Ditolak</option>
                                                </select>
                                            </div>
                                            <?php endif; ?>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="modal-delete-<?php echo $row['id_penerimaan']; ?>" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus penerimaan barang
                                        <?php echo $row['nama_barang']; ?>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <a href="penerimaanBarang.php?delete=<?php echo $row['id_penerimaan']; ?>"
                                            class="btn btn-danger">Hapus</a>
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

                    <!-- Modal Tambah Penerimaan -->
                    <div class="modal fade" id="tambahPenerimaanModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Penerimaan Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Input</label>
                                            <select name="jenis_input" class="form-select" id="jenis_input"
                                                onchange="togglePenerimaanForm()" required>
                                                <option value="permintaan">Dari Permintaan</option>
                                                <option value="manual">Input Manual</option>
                                            </select>
                                        </div>

                                        <!-- Form untuk input dari permintaan -->
                                        <div id="form_permintaan">
                                            <div class="mb-3">
                                                <label class="form-label">Permintaan Barang</label>
                                                <select name="id_permintaan" class="form-select" id="select_permintaan"
                                                    required>
                                                    <option value="">Pilih Permintaan Barang</option>
                                                    <?php
                                $permintaan_query = "SELECT pb.id_permintaan, pb.nama_barang, d.nama_departemen, pb.kebutuhan_qty, pb.satuan 
                                                    FROM permintaan_barang pb 
                                                    JOIN departemen d ON pb.id_departemen = d.id_departemen 
                                                    WHERE pb.status = 1 
                                                    AND NOT EXISTS (
                                                        SELECT 1 FROM penerimaan_barang pr 
                                                        WHERE pr.id_permintaan = pb.id_permintaan
                                                    )";
                                $permintaan_result = mysqli_query($conn, $permintaan_query);
                                while ($permintaan = mysqli_fetch_assoc($permintaan_result)) {
                                    echo "<option value='" . $permintaan['id_permintaan'] . "' 
                                          data-qty='" . $permintaan['kebutuhan_qty'] . "'
                                          data-satuan='" . $permintaan['satuan'] . "'>" 
                                        . $permintaan['nama_barang'] . " - " 
                                        . $permintaan['nama_departemen'] . " - " 
                                        . $permintaan['kebutuhan_qty'] . "</option>";
                                }
                                ?>
                                                </select>
                                                <input type="hidden" name="jumlah">
                                            </div>
                                        </div>

                                        <!-- Form untuk input manual -->
                                        <div id="form_manual" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Barang</label>
                                                <input type="text" name="nama_barang" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Departemen</label>
                                                <select name="id_departemen" class="form-select" data-required="true">
                                                    <option value="">Pilih Departemen</option>
                                                    <?php
                                $dept_query = "SELECT * FROM departemen WHERE nama_departemen != ''";
                                $dept_result = mysqli_query($conn, $dept_query);
                                while ($dept = mysqli_fetch_assoc($dept_result)) {
                                    echo "<option value='" . $dept['id_departemen'] . "'>" 
                                        . $dept['nama_departemen'] . " (" . $dept['kode_departemen'] . ")</option>";
                                }
                                ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah</label>
                                                <input type="number" name="jumlah" class="form-control"
                                                    data-required="true">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Satuan</label>
                                            <input type="text" name="satuan" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Terima</label>
                                            <input type="date" name="tanggal_terima" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="">Pilih status</option>
                                                <option value="Diterima">Diterima</option>
                                                <option value="Ditolak">Ditolak</option>
                                            </select>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="tambahPenerimaan"
                                                class="btn btn-primary">Tambah</button>
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
            <!-- / Footer -->
            <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
    </div>
    <!-- / Layout page -->

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->

<!-- Tambahkan script ini sebelum penutup body -->
<script>
// Fungsi untuk toggle form penerimaan
function togglePenerimaanForm() {
    var jenisInput = document.getElementById('jenis_input').value;
    var formPermintaan = document.getElementById('form_permintaan');
    var formManual = document.getElementById('form_manual');

    if (jenisInput === 'permintaan') {
        // Tampilkan form permintaan dan sembunyikan form manual
        formPermintaan.style.display = 'block';
        formManual.style.display = 'none';

        // Aktifkan validasi untuk form permintaan
        document.getElementById('select_permintaan').required = true;

        // Nonaktifkan validasi dan hapus required attribute untuk form manual
        document.querySelectorAll('#form_manual input, #form_manual select').forEach(input => {
            input.required = false;
            input.removeAttribute('required');
        });

        // Set nilai satuan berdasarkan permintaan yang dipilih
        setSelectedValues();
    } else {
        // Tampilkan form manual dan sembunyikan form permintaan
        formPermintaan.style.display = 'none';
        formManual.style.display = 'block';

        // Nonaktifkan validasi untuk form permintaan
        document.getElementById('select_permintaan').required = false;
        document.getElementById('select_permintaan').removeAttribute('required');

        // Aktifkan validasi untuk form manual
        document.querySelectorAll('#form_manual input, #form_manual select').forEach(input => {
            if (input.getAttribute('data-required') !== 'false') {
                input.required = true;
                input.setAttribute('required', '');
            }
        });
    }
}

// Fungsi untuk mengatur nilai berdasarkan permintaan yang dipilih
function setSelectedValues() {
    var selectPermintaan = document.getElementById('select_permintaan');
    var selectedOption = selectPermintaan.options[selectPermintaan.selectedIndex];

    if (selectedOption) {
        // Set nilai satuan dari data permintaan
        var satuanInput = document.querySelector('input[name="satuan"]');
        if (satuanInput) {
            satuanInput.value = selectedOption.getAttribute('data-satuan') || '';
        }
    }
}

// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'jenis input': 'Mohon pilih jenis input data',
        'permintaan barang': 'Mohon pilih data permintaan barang',
        'nama barang': 'Mohon masukkan nama barang',
        'departemen': 'Kolom departemen wajib diisi!',
        'jumlah': 'Kolom jumlah barang wajib diisi!',
        'satuan': 'Mohon masukkan satuan barang',
        'tanggal terima': 'Mohon masukkan tanggal terima',
        'status': 'Mohon pilih status barang'
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
    const elemenWajib = document.querySelectorAll('input[required], select[required]');

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
    const formManual = document.getElementById('form_manual');
    if (formManual) {
        const inputs = formManual.querySelectorAll('input[required], select[required]');
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
    }
}

// Event listener saat modal tambah dibuka
document.getElementById('tambahPenerimaanModal').addEventListener('show.bs.modal', function() {
    // Reset form saat modal dibuka
    const form = this.querySelector('form');
    if (form) form.reset();

    // Set jenis input default dan trigger toggle
    const jenisInput = document.getElementById('jenis_input');
    if (jenisInput) {
        jenisInput.value = 'permintaan';
        togglePenerimaanForm();
    }

    // Terapkan validasi
    setTimeout(terapkanValidasi, 100);
});

// Event listener untuk modal edit
document.querySelectorAll('[id^="modal-update-"]').forEach(modal => {
    modal.addEventListener('show.bs.modal', function() {
        setTimeout(terapkanValidasi, 100);
    });
});

// Event listener untuk select permintaan
document.getElementById('select_permintaan')?.addEventListener('change', setSelectedValues);

// Event listener saat dokumen dimuat
document.addEventListener('DOMContentLoaded', function() {
    terapkanValidasi();
    validasiFormManual();

    // Set initial state
    const jenisInput = document.getElementById('jenis_input');
    if (jenisInput) {
        togglePenerimaanForm();
    }
});
</script>


<?php require('../layouts/assetsFooter.php'); ?>