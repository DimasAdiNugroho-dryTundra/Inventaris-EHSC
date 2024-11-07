<?php
// File: departemen.php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../server/crudDepartemen.php'); 
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
                            <h2 class="mb-1">Manajemen Departemen</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Departemen</li>
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
                            Data Departemen
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahDepartemenModal">Tambah Departemen</button>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari nama departemen..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="manajemen-user.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="manajemen-user.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="manajemen-user.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Departemen</th>
                                        <th>Nama Departemen</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
            $no = $offset + 1;
            while ($row = mysqli_fetch_assoc($result)) { 
            ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['kode_departemen']; ?></td>
                                        <td><?php echo $row['nama_departemen']; ?></td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_departemen']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_departemen']; ?>">Delete</button>
                                        </td>
                                    </tr>
                                    <?php 
            } 
            ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) { 
?>
                        <!-- Modal Edit/Update -->
                        <div class="modal fade" id="modal-update-<?php echo $row['id_departemen']; ?>" tabindex="-1"
                            aria-labelledby="modalUpdateLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modalUpdateLabel">Edit Departemen</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="departemen.php" enctype="multipart/form-data"
                                            class="needs-validation" novalidate>
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_departemen"
                                                value="<?php echo $row['id_departemen']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Kode Departemen</label>
                                                <input type="text" class="form-control check-changes"
                                                    name="kode_departemen"
                                                    id="kode_departemen_edit_<?php echo $row['id_departemen']; ?>"
                                                    value="<?php echo $row['kode_departemen']; ?>"
                                                    data-original-value="<?php echo $row['kode_departemen']; ?>"
                                                    required>
                                                <div class="invalid-feedback">
                                                    Kolom kode departemen wajib diisi!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Departemen</label>
                                                <input type="text" class="form-control check-changes"
                                                    name="nama_departemen"
                                                    id="nama_departemen_edit_<?php echo $row['id_departemen']; ?>"
                                                    value="<?php echo $row['nama_departemen']; ?>"
                                                    data-original-value="<?php echo $row['nama_departemen']; ?>"
                                                    required>
                                                <div class="invalid-feedback">
                                                    Kolom nama departemen wajib diisi!
                                                </div>
                                                <div class="text-danger no-changes-message"
                                                    style="display: none; margin-top: 0.5rem; font-size: 0.875em;">
                                                    Belum ada perubahan data yang dilakukan!
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary"
                                                    id="submit-btn">Simpan</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="modal-delete-<?php echo $row['id_departemen']; ?>" tabindex="-1"
                            aria-labelledby="modalDeleteLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus departemen
                                        "<?php echo $row['nama_departemen']; ?>"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <a href="departemen.php?delete=<?php echo $row['id_departemen']; ?>"
                                            class="btn btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- Modal Tambah Departemen -->
                        <div class="modal fade" id="tambahDepartemenModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Departemen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="departemen.php" enctype="multipart/form-data"
                                            class="needs-validation" novalidate>
                                            <div class="mb-3">
                                                <label class="form-label">Kode Departemen</label>
                                                <input type="text" name="kode_departemen" class="form-control" required
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="Silakan masukkan kode departemen">
                                                <div class="invalid-feedback">
                                                    Kolom kode departemen wajib diisi!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Departemen</label>
                                                <input type="text" name="nama_departemen" class="form-control" required
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="Silakan masukkan nama departemen">
                                                <div class="invalid-feedback">
                                                    Kolom nama departemen wajib diisi!
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="tambahDepartemen"
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
                <?php
            require('../layouts/footer.php');
            ?>
            </div>
            <!-- Footer -->

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validasi untuk form tambah data
    const tambahForm = document.querySelector("#tambahDepartemenModal form");
    if (tambahForm) {
        tambahForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Reset semua validasi sebelumnya
            this.querySelectorAll('.is-invalid').forEach(element => {
                element.classList.remove('is-invalid');
            });

            let isValid = true;
            const inputs = this.querySelectorAll('input[required]');

            // Cek setiap input required
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                }
            });

            // Jika semua valid, submit form
            if (isValid) {
                this.submit();
            }
        });
    }

    // Validasi untuk form edit data
    document.querySelectorAll("[id^='modal-update-']").forEach(modal => {
        const form = modal.querySelector('form');
        const inputs = form.querySelectorAll('input:not([type="hidden"])');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Handle form submission
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Reset semua validasi sebelumnya
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                // Hapus pesan error yang ada
                const nextElement = input.nextElementSibling;
                if (nextElement && nextElement.classList.contains('invalid-feedback')) {
                    nextElement.remove();
                }
            });

            let isValid = true;
            let hasChanges = false;

            // Validasi input kosong dan cek perubahan
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');

                    // Tambah pesan error untuk input kosong
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent =
                        `Kolom ${input.previousElementSibling.textContent.toLowerCase()} wajib diisi!`;
                    input.parentNode.appendChild(errorDiv);
                }

                // Cek apakah ada perubahan
                if (input.value !== input.getAttribute('data-original-value')) {
                    hasChanges = true;
                }
            });

            // Jika tidak ada perubahan, tambahkan pesan error
            if (!hasChanges && isValid) {
                inputs.forEach(input => {
                    input.classList.add('is-invalid');
                });

                // Tambah pesan error untuk tidak ada perubahan
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Belum ada perubahan data yang dilakukan!';
                inputs[inputs.length - 1].parentNode.appendChild(errorDiv);
                return;
            }

            // Jika semua valid dan ada perubahan, submit form
            if (isValid && hasChanges) {
                this.submit();
            }
        });

        // Reset form saat modal ditutup
        modal.addEventListener('hidden.bs.modal', function() {
            inputs.forEach(input => {
                input.value = input.getAttribute('data-original-value');
                input.classList.remove('is-invalid');
                // Hapus pesan error yang ada
                const nextElement = input.nextElementSibling;
                if (nextElement && nextElement.classList.contains('invalid-feedback')) {
                    nextElement.remove();
                }
            });
        });
    });
});
</script>
<!-- / Layout wrapper -->

<?php
require('../layouts/assetsFooter.php')
    ?>