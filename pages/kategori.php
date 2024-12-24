<?php
// File: kategori.php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudKategori.php');
require('../layouts/header.php');
?>

<style>
.w-50px {
    width: 50px !important;
}

.w-100px {
    width: 100px !important;
}

.w-150px {
    width: 150px !important;
}

.table-sm td,
.table-sm th {
    padding: 0.4rem !important;
}

.btn-sm {
    padding: 0.2rem 0.5rem !important;
    font-size: 0.75rem !important;
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
                            <h2 class="mb-1">Manajemen Kategori</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Kategori</li>
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
                            Data Kategori
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahKategoriModal">Tambah Kategori</button>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari nama kategori..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="kategori.php?limit=5" <?php if ($limit == 5) echo 'selected'; ?>>
                                            5
                                        </option>
                                        <option value="kategori.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10
                                        </option>
                                        <option value="kategori.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>20
                                        </option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-sm small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center align-middle w-50px">No</th>
                                        <th class="text-center align-middle w-150px">Kode Kategori</th>
                                        <th class="text-center align-middle w-150px">Nama Kategori</th>
                                        <th class="text-center align-middle w-150px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = $offset + 1;
                                    while ($row = mysqli_fetch_assoc($result)) { 
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo $no++; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['kode_kategori']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['nama_kategori']; ?></td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_kategori']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_kategori']; ?>">Hapus</button>
                                        </td>
                                    </tr>
                                    <?php 
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Edit -->
                        <?php
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)) { 
                        ?>
                        <div class="modal fade" id="modal-update-<?php echo $row['id_kategori']; ?>" tabindex="-1"
                            aria-labelledby="modalUpdateLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modalUpdateLabel">Edit Kategori</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="kategori.php">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_kategori"
                                                value="<?php echo $row['id_kategori']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Kode Kategori</label>
                                                <input type="text" class="form-control" name="kode_kategori"
                                                    value="<?php echo $row['kode_kategori']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Kategori</label>
                                                <input type="text" class="form-control" name="nama_kategori"
                                                    value="<?php echo $row['nama_kategori']; ?>" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div class="modal fade" id="modal-delete-<?php echo $row['id_kategori']; ?>" tabindex="-1"
                            aria-labelledby="modalDeleteLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus kategori
                                        "<?php echo $row['nama_kategori']; ?>"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <a href="kategori.php?delete=<?php echo $row['id_kategori']; ?>"
                                            class="btn btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- Modal Tambah -->
                        <div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="kategori.php">
                                            <div class="mb-3">
                                                <label class="form-label">Kode Kategori</label>
                                                <input type="text" name="kode_kategori" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Kategori</label>
                                                <input type="text" name="nama_kategori" class="form-control" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="tambahKategori"
                                                    class="btn btn-primary">Tambah</button>
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
                </div>
            </div>
            <?php require('../layouts/footer.php'); ?>
            <div class="content-backdrop fade"></div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>

<script>
// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'kode kategori': 'Kolom kode kategori wajib diisi!',
        'nama kategori': 'Kolom nama kategori wajib diisi!'
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
    const formManual = document.querySelectorAll('.needs-validation');
    if (formManual) {
        formManual.forEach(form => {
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.hasAttribute('required')) {
                    hapusPesanError(input);
                }
            });
        });
    }
}

// Event listener saat modal tambah dibuka
document.getElementById('tambahKategoriModal').addEventListener('show.bs.modal', function() {
    // Reset form saat modal dibuka
    const form = this.querySelector('form');
    if (form) form.reset();

    // Terapkan validasi
    setTimeout(terapkanValidasi, 100);
});

// Event listener saat modal update dibuka
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

<?php require('../layouts/assetsFooter.php') ?>