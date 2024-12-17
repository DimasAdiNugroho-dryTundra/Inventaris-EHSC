<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudRuangan.php');
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
                            <h2 class="mb-1">Manajemen Ruangan</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Ruangan</li>
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

                        <h4 class="card-header d-flex justify-content-between align-items-center">
                            Data Ruangan
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahRuanganModal">Tambah Ruangan</button>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari ruangan..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="ruangan.php?limit=5" <?php if ($limit == 5) echo 'selected'; ?>>5
                                        </option>
                                        <option value="ruangan.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="ruangan.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                                    </select>
                                </form>
                            </div>

                            <div class="table-responsive text-nowrap" style="max-height: 340px;">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Nama Ruangan</th>
                                            <th class="text-center align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo $no++; ?></td>
                                            <td class="text-center align-middle"><?php echo $row['nama_ruangan']; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modal-update-<?php echo $row['id_ruangan']; ?>">Edit</button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal-delete-<?php echo $row['id_ruangan']; ?>">Hapus</button>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="modal-update-<?php echo $row['id_ruangan']; ?>"
                                            tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="modalUpdateLabel">Edit Ruangan</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" action="ruangan.php">
                                                            <input type="hidden" name="action" value="update">
                                                            <input type="hidden" name="id_ruangan"
                                                                value="<?php echo $row['id_ruangan']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Ruangan</label>
                                                                <input type="text" class="form-control"
                                                                    name="nama_ruangan"
                                                                    value="<?php echo $row['nama_ruangan']; ?>"
                                                                    required>
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
                                        <div class="modal fade" id="modal-delete-<?php echo $row['id_ruangan']; ?>"
                                            tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin menghapus ruangan
                                                        "<?php echo $row['nama_ruangan']; ?>"?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <a href="ruangan.php?delete=<?php echo $row['id_ruangan']; ?>"
                                                            class="btn btn-danger">Hapus</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Modal Tambah Ruangan -->
                            <div class="modal fade" id="tambahRuanganModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Ruangan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="ruangan.php">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Ruangan</label>
                                                    <input type="text" name="nama_ruangan" class="form-control"
                                                        required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="tambahRuangan"
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
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>"
                                            aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                    <li class="page-item <?php if ($i == $page)
                                        echo 'active'; ?>">
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
                </div>
                <?php require('../layouts/footer.php'); ?>
                <div class="content-backdrop fade"></div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    <?php require('../layouts/assetsFooter.php'); ?>

    <script>
    // Fungsi untuk mendapatkan pesan validasi
    function getPesanValidasi(labelText, jenisInput) {
        labelText = labelText.replace(/[:\\s]+$/, '').toLowerCase();

        const pesanKhusus = {
            'nama ruangan': 'Kolom nama ruangan wajib diisi!',
        };

        return pesanKhus[labelText] || (jenisInput === 'select' ? `Mohon pilih ${labelText}` :
            `Mohon masukkan ${labelText}`);
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

    // Event listener saat modal tambah dibuka
    document.getElementById('tambahRuanganModal').addEventListener('show.bs.modal', function() {
        // Reset form saat modal dibuka
        const form = this.querySelector('form');
        if (form) form.reset();

        // Terapkan validasi
        setTimeout(terapkanValidasi, 100);
    });

    // Event listener saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', function() {
        terapkanValidasi();
    });
    </script>