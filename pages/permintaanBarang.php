<?php
// File: permintaanBarang.php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudPermintaanBarang.php'); // Adjust this file accordingly
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
                            <h2 class="mb-1">Manajemen Permintaan Barang</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Permintaan Barang</li>
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

                        <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
                        <h4 class="card-header d-flex justify-content-between align-items-center">
                            Data Permintaan Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahPermintaanModal">Tambah Permintaan</button>
                        </h4>
                        <?php endif; ?>

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

                        <div class="table-responsive text-nowrap" style="max-height: 340px;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Departemen</th>
                                        <th class="text-center align-middle">Barang</th>
                                        <th class="text-center align-middle">Merk</th>
                                        <th class="text-center align-middle">Tanggal</th>
                                        <th class="text-center align-middle">Spesifikasi</th>
                                        <th class="text-center align-middle">Kebutuhan Qty</th>
                                        <th class="text-center align-middle">Satuan</th>
                                        <th class="text-center align-middle">Status</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = $offset + 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $status_text = $row['status'] == 1 ? 'Disetujui' : ($row['status'] == 2 ? 'Tidak Disetujui' : 'Menunggu');
                                        $warna_status = $row['status'] == 1 ? 'text-success' : ($row['status'] == 2 ? 'text-danger' : 'text-warning');
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo $no++; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['nama_departemen']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['nama_barang']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['merk']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['tanggal_permintaan']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['spesifikasi']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['jumlah_kebutuhan']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['satuan']; ?></td>
                                        <td class="text-center align-middle">
                                            <span
                                                class="<?php echo $warna_status; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_permintaan']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_permintaan']; ?>">Hapus</button>
                                            <?php endif; ?>
                                            <a href="../report/printLaporanPermintaanBarang.php?id=<?php echo $row['id_permintaan']; ?>"
                                                class="btn btn-primary btn-sm">Cetak</a>
                                        </td>
                                    </tr>
                                    <!-- modal edit -->
                                    <div class='modal fade' id='modal-update-<?php echo $row['id_permintaan']; ?>'
                                        tabindex='-1' aria-labelledby='modalUpdateLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-lg modal-dialog-centered' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='modalUpdateLabel'>Edit Permintaan Barang
                                                    </h4>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal'
                                                        aria-label='Close'></button>
                                                </div>
                                                <div class='modal-body'>
                                                    <form method='POST' enctype='multipart/form-data'>
                                                        <input type='hidden' name='action' value='update'>
                                                        <input type='hidden' name='id_permintaan'
                                                            value='<?php echo $row['id_permintaan']; ?>'>
                                                        <div class='mb-3'>
                                                            <label for='id_departemen'
                                                                class='form-label'>Departemen</label>
                                                            <select class='form-select' name='id_departemen' required>
                                                                <option value="">Pilih Departemen</option>
                                                                <?php
                                                                $dept_query = "SELECT * FROM departemen";
                                                                $dept_result = mysqli_query($conn, $dept_query);
                                                                while ($dept = mysqli_fetch_assoc($dept_result)) {
                                                                    $selected = ($dept['id_departemen'] == $row['id_departemen']) ? 'selected' : '';
                                                                    echo "<option value='" . $dept['id_departemen'] . "' $selected>" . $dept['nama_departemen'] . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <div class='mb-3'>
                                                            <label for='nama_barang' class='form-label'>Nama
                                                                Barang</label>
                                                            <input type='text' class='form-control' name='nama_barang'
                                                                value='<?php echo $row['nama_barang']; ?>' required>
                                                        </div>

                                                        <div class='mb-3'>
                                                            <label for='merk' class='form-label'>Merk</label>
                                                            <input type='text' class='form-control' name='merk'
                                                                value='<?php echo $row['merk']; ?>' required>
                                                        </div>

                                                        <div class='mb-3'>
                                                            <label for='tanggal_permintaan'
                                                                class='form-label'>Tanggal</label>
                                                            <input type='date' class='form-control' name='tanggal'
                                                                value='<?php echo date('Y-m-d', strtotime($row['tanggal_permintaan'])); ?>'
                                                                required>
                                                        </div>

                                                        <div class='mb-3'>
                                                            <label for='spesifikasi'
                                                                class='form-label'>Spesifikasi</label>
                                                            <textarea class='form-control' name='spesifikasi'
                                                                required><?php echo $row['spesifikasi']; ?></textarea>
                                                        </div>

                                                        <div class='mb-3'>
                                                            <label for='jumlah_kebutuhan' class='form-label'>Jml
                                                                Kebutuhan</label>
                                                            <input type='number' class='form-control'
                                                                name='jumlah_kebutuhan' id='jumlah_kebutuhan_update'
                                                                value='<?php echo $row['jumlah_kebutuhan']; ?>'
                                                                required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Satuan</label>
                                                            <input type="text" name="satuan" class="form-control"
                                                                value="<?php echo $row['satuan']; ?>" required>
                                                        </div>

                                                        <div class='mb-3'>
                                                            <label for='status' class='form-label'>Status</label>
                                                            <select class='form-select' name='status' required>
                                                                <option value="">Pilih Status</option>
                                                                <option value='0'
                                                                    <?php echo ($row['status'] == 1 ? 'selected' : ''); ?>>
                                                                    Disetujui</option>
                                                                <option value='1'
                                                                    <?php echo ($row['status'] == 0 ? 'selected' : ''); ?>>
                                                                    Tidak Disetujui</option>
                                                            </select>
                                                        </div>

                                                        <div class="modal-footer">
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
                                    <!-- Modal Hapus -->
                                    <div class='modal fade' id='modal-delete-<?php echo $row['id_permintaan']; ?>'
                                        tabindex='-1' aria-labelledby='modalDeleteLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='modalDeleteLabel'>Konfirmasi Hapus</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal'
                                                        aria-label='Close'></button>
                                                </div>
                                                <div class='modal-body'>
                                                    Apakah Anda yakin ingin menghapus permintaan barang
                                                    "<?php echo $row['nama_barang']; ?>"?
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary'
                                                        data-bs-dismiss='modal'>Batal</button>
                                                    <a href='permintaanBarang.php?delete=<?php echo $row['id_permintaan']; ?>'
                                                        class='btn btn-danger'>Hapus</a>
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

                    <!-- Modal Tambah Permintaan Barang -->
                    <div class="modal fade" id="tambahPermintaanModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Permintaan Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formTambahPermintaan" method="POST" action="permintaanBarang.php">
                                        <input type="hidden" name="tambahPermintaan" value="1">
                                        <div class="mb-3">
                                            <label class="form-label">Departemen</label>
                                            <select name="id_departemen" class="form-select" required>
                                                <option value="">Pilih Departemen</option>
                                                <?php
                                                $dept_query = "SELECT * FROM departemen";
                                                $dept_result = mysqli_query($conn, $dept_query);
                                                while ($dept = mysqli_fetch_assoc($dept_result)) {
                                                    echo "<option value='" . $dept['id_departemen'] . "'>" . $dept['nama_departemen'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nama Barang</label>
                                            <input type="text" name="nama_barang" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Merk</label>
                                            <input type="text" name="merk" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Spesifikasi</label>
                                            <textarea name="spesifikasi" class="form-control" required></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Jumlah Kebutuhan</label>
                                            <input type="number" name="jumlah_kebutuhan" id="jumlah_kebutuhan"
                                                class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Satuan</label>
                                            <input type="text" name="satuan" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="1">Disetujui</option>
                                                <option value="2">Tidak Disetujui</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
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
            <?php
            require('../layouts/footer.php');
            ?>
            <div class="content-backdrop fade"></div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>

<?php
require('../layouts/assetsFooter.php')
?>


<script>
// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'departemen': 'Kolom departemen wajib diisi!',
        'nama barang': 'Kolom nama barang wajib diisi!',
        'merk': 'Kolom merk wajib diisi!',
        'tanggal': 'Kolom tanggal wajib diisi!',
        'spesifikasi': 'Kolom spesifikasi wajib diisi!',
        'kebutuhan qty': 'Kolom kebutuhan qty wajib diisi!',
        'satuan': 'Kolom satuan wajib diisi!',
        'status': 'Kolom status wajib diisi!'
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

// Validasi form tambah
document.getElementById('formTambahPermintaan').addEventListener('submit', function(event) {
    const elemenWajib = this.querySelectorAll('input[required], select[required], textarea[required]');

    elemenWajib.forEach(elemen => {
        if (elemen.validity.valueMissing) {
            const labelElemen = elemen.previousElementSibling;
            const labelTeks = labelElemen ? labelElemen.textContent : '';
            const jenisInput = elemen.tagName.toLowerCase();
            elemen.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
        } else {
            elemen.setCustomValidity('');
        }
    });

    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
}, false);

// Validasi form edit
document.addEventListener('submit', function(e) {
    if (e.target && e.target.matches('form')) {
        const elemenWajib = e.target.querySelectorAll('input[required], select[required], textarea[required]');

        elemenWajib.forEach(elemen => {
            if (elemen.validity.valueMissing) {
                const labelElemen = elemen.previousElementSibling;
                const labelTeks = labelElemen ? labelElemen.textContent : '';
                const jenisInput = elemen.tagName.toLowerCase();
                elemen.setCustomValidity(getPesanValidasi(labelTeks, jenisInput));
            } else {
                elemen.setCustomValidity('');
            }
        });

        if (!e.target.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
    }
});

// Event listener saat modal tambah dibuka
document.getElementById('tambahPermintaanModal').addEventListener('show.bs.modal', function() {
    // Reset form
    document.getElementById('formTambahPermintaan').reset();
    // Terapkan validasi
    terapkanValidasi();
});

// Event listener saat modal edit dibuka
document.querySelectorAll('[data-bs-target^="#modal-update-"]').forEach(button => {
    button.addEventListener('click', function() {
        const modalId = this.getAttribute('data-bs-target');
        const modal = document.querySelector(modalId);
        if (modal) {
            // Terapkan validasi
            terapkanValidasi();
        }
    });
});
</script>