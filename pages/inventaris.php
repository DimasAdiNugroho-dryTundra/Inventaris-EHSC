<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../server/crudInventaris.php');
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
                            <h2 class="mb-1">Manajemen Inventaris</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Inventaris</li>
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
                            Data Inventaris
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahInventarisModal">Tambah Inventaris</button>
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
                                        <option value="permintaan_barang.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="permintaan_barang.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="permintaan_barang.php?limit=20"
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
                                        <th>Kode Inventaris</th>
                                        <th>Departemen</th>
                                        <th>Kategori</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah Awal</th>
                                        <th>Jumlah Akhir</th>
                                        <th>Satuan</th>
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
                                        <td><?php echo $row['kode_inventaris']; ?></td>
                                        <td><?php echo $row['nama_departemen']; ?></td>
                                        <td><?php echo $row['nama_kategori']; ?></td>
                                        <td><?php echo $row['nama_barang']; ?></td>
                                        <td><?php echo $row['jumlah']; ?></td>
                                        <td><?php echo $row['jumlah_akhir']; ?></td>
                                        <td><?php echo $row['satuan']; ?></td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_inventaris']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_inventaris']; ?>">Delete</button>
                                            <a href="detail_inventaris.php?id=<?php echo $row['id_inventaris']; ?>"
                                                class="btn btn-primary btn-sm">Lihat Detail</a>
                                        </td>
                                    </tr>

                                    <!-- Modal Update -->
                                    <div class="modal fade" id="modal-update-<?php echo $row['id_inventaris']; ?>"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Inventaris</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id_inventaris"
                                                            value="<?php echo $row['id_inventaris']; ?>">

                                                        <div class="mb-3">
                                                            <label class="form-label">Kode Inventaris</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row['kode_inventaris']; ?>" readonly>
                                                        </div>

                                                        <?php if ($row['id_penerimaan'] === NULL): ?>
                                                        <!-- Form untuk barang input manual - semua field bisa diedit -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Barang</label>
                                                            <input type="text" name="nama_barang" class="form-control"
                                                                value="<?php echo $row['nama_barang']; ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Jumlah</label>
                                                            <input type="number" name="jumlah" class="form-control"
                                                                value="<?php echo $row['jumlah']; ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Perolehan</label>
                                                            <input type="date" name="tanggal_perolehan"
                                                                class="form-control"
                                                                value="<?php echo $row['tanggal_perolehan']; ?>"
                                                                required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Departemen</label>
                                                            <select name="id_departemen"
                                                                class="form-select editDepartemenSelect" required>
                                                                <?php
        // Ambil kode departemen dari kode inventaris
        $kode_parts = explode('/', $row['kode_inventaris']);
        $kode_departemen = $kode_parts[0];
        
        $dept_query = "SELECT * FROM departemen ORDER BY 
                      CASE WHEN kode_departemen = '$kode_departemen' THEN 0 ELSE 1 END, 
                      nama_departemen ASC";
        $dept_result = mysqli_query($conn, $dept_query);
        while ($dept = mysqli_fetch_assoc($dept_result)) {
            $selected = ($dept['id_departemen'] == $row['id_departemen']) ? 'selected' : '';
            echo "<option value='" . $dept['id_departemen'] . "' " . $selected . ">" 
                 . $dept['nama_departemen'] . " (" . $dept['kode_departemen'] . ")</option>";
        }
        ?>
                                                            </select>
                                                        </div>
                                                        <?php else: ?>
                                                        <!-- Form untuk barang dari penerimaan - readonly fields -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Barang</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row['nama_barang']; ?>" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Jumlah</label>
                                                            <input type="number" class="form-control bg-light"
                                                                value="<?php echo $row['jumlah']; ?>" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Perolehan</label>
                                                            <input type="date" class="form-control bg-light"
                                                                value="<?php echo $row['tanggal_perolehan']; ?>"
                                                                readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Departemen</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row['nama_departemen']; ?>" readonly>
                                                            <input type="hidden" name="id_departemen"
                                                                value="<?php echo $row['id_departemen']; ?>">
                                                        </div>
                                                        <?php endif; ?>

                                                        <div class="mb-3">
                                                            <label class="form-label">Kategori</label>
                                                            <select name="id_kategori" class="form-select" required>
                                                                <?php
                            $kat_query = "SELECT * FROM kategori";
                            $kat_result = mysqli_query($conn, $kat_query);
                            while ($kat = mysqli_fetch_assoc($kat_result)) {
                                $selected = ($kat['id_kategori'] == $row['id_kategori']) ? 'selected' : '';
                                echo "<option value='" . $kat['id_kategori'] . "' $selected>" 
                                     . $kat['nama_kategori'] . "</option>";
                            }
                            ?>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Satuan</label>
                                                            <input type="text" name="satuan" class="form-control"
                                                                value="<?php echo $row['satuan']; ?>" required>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Delete -->
                                    <div class="modal fade" id="modal-delete-<?php echo $row['id_inventaris']; ?>"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus inventaris
                                                    <?php echo $row['nama_barang']; ?>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <a href="inventaris.php?delete=<?php echo $row['id_inventaris']; ?>"
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

                    <!-- Modal Tambah Inventaris -->
                    <div class="modal fade" id="tambahInventarisModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Inventaris</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Input</label>
                                            <select name="jenis_input" class="form-select" id="jenis_input"
                                                onchange="toggleInputForm()">
                                                <option value="penerimaan">Dari Penerimaan</option>
                                                <option value="manual">Input Manual</option>
                                            </select>
                                        </div>

                                        <!-- Form untuk input dari penerimaan -->
                                        <div id="form_penerimaan">
                                            <div class="mb-3">
                                                <label class="form-label">Penerimaan Barang</label>
                                                <select name="id_penerimaan" class="form-select" id="id_penerimaan"
                                                    onchange="updateDepartemen()">
                                                    <?php
                                                        $penerimaan_query = "SELECT pb.id_penerimaan, pb.nama_barang, d.nama_departemen, d.id_departemen 
                                                                            FROM penerimaan_barang pb 
                                                                            JOIN permintaan_barang pmb ON pb.id_permintaan = pmb.id_permintaan
                                                                            JOIN departemen d ON pmb.id_departemen = d.id_departemen
                                                                            WHERE pb.id_penerimaan NOT IN (SELECT id_penerimaan FROM inventaris WHERE id_penerimaan IS NOT NULL)";
                                                        $penerimaan_result = mysqli_query($conn, $penerimaan_query);
                                                        while ($penerimaan = mysqli_fetch_assoc($penerimaan_result)) {
                                                            echo "<option value='" . $penerimaan['id_penerimaan'] . "' 
                                                                data-departemen='" . $penerimaan['id_departemen'] . "'>" 
                                                                . $penerimaan['nama_barang'] . " - " 
                                                                . $penerimaan['nama_departemen'] . "</option>";
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Form untuk input manual -->
                                        <div class="mb-3" id="form_manual">
                                            <label class="form-label">Nama Barang</label>
                                            <input type="text" name="nama_barang" class="form-control">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="jumlah" class="form-control">
                                            <label class="form-label">Satuan</label>
                                            <input type="text" name="satuan" class="form-control">
                                            <label class="form-label">Tanggal Perolehan</label>
                                            <input type="date" name="tanggal_perolehan" class="form-control">
                                            <label class="form-label">Departemen</label>
                                            <select name="id_departemen" class="form-select">
                                                <?php
        $dept_query = "SELECT * FROM departemen";
        $dept_result = mysqli_query($conn, $dept_query);
        while ($dept = mysqli_fetch_assoc($dept_result)) {
            echo "<option value='" . $dept['id_departemen'] . "'>" 
                 . $dept['nama_departemen'] . "</option>";
        }
        ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Kategori</label>
                                            <select name="id_kategori" class="form-select" required>
                                                <?php
                            $kat_query = "SELECT * FROM kategori";
                            $kat_result = mysqli_query($conn, $kat_query);
                            while ($kat = mysqli_fetch_assoc($kat_result)) {
                                echo "<option value='" . $kat['id_kategori'] . "'>" 
                                     . $kat['nama_kategori'] . "</option>";
                            }
                            ?>
                                            </select>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" name="tambahInventaris"
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
                <!-- Footer -->
                <?php require('../layouts/footer.php'); ?>
                <!-- / Footer -->
                <div class="content-backdrop fade"></div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    <!-- JavaScript untuk toggle form -->
    <script>
    // Fungsi untuk toggle form tambah inventaris
    function updateDepartemen() {
        if (document.getElementById('jenis_input').value === 'penerimaan') {
            var selectPenerimaan = document.getElementById('id_penerimaan');
            var selectDepartemen = document.querySelector('select[name="id_departemen"]');
            var selectedOption = selectPenerimaan.options[selectPenerimaan.selectedIndex];
            var departemenId = selectedOption.getAttribute('data-departemen');

            // Set nilai departemen dan disabled
            selectDepartemen.value = departemenId;
            selectDepartemen.disabled = true;
        }
    }

    function toggleInputForm() {
        const jenisInput = document.getElementById('jenis_input').value;
        const formPenerimaan = document.getElementById('form_penerimaan');
        const formManual = document.getElementById('form_manual');
        const selectDepartemen = document.querySelector('select[name="id_departemen"]');

        if (jenisInput === 'penerimaan') {
            formPenerimaan.style.display = 'block';
            formManual.style.display = 'none';
            selectDepartemen.disabled = true;
            updateDepartemen();
        } else {
            formPenerimaan.style.display = 'none';
            formManual.style.display = 'block';
            selectDepartemen.disabled = false;
        }
    }

    // Fungsi khusus untuk form edit
    function initializeEditForms() {
        // Aktifkan semua dropdown departemen pada form edit untuk input manual
        document.querySelectorAll('[id^="modal-update-"]').forEach(modal => {
            const form = modal.querySelector('form');
            if (form) {
                const isManualInput = !form.querySelector('input[readonly]');
                const deptSelect = form.querySelector('select[name="id_departemen"]');

                if (isManualInput && deptSelect) {
                    deptSelect.disabled = false;
                }
            }
        });

        // Tambahkan event listener untuk setiap form edit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Pastikan semua select yang tidak readonly dapat mengirim nilai
                this.querySelectorAll('select').forEach(select => {
                    const isFromPenerimaan = this.querySelector('input[readonly]') !== null;
                    if (!isFromPenerimaan) {
                        select.disabled = false;
                    }
                });
            });
        });
    }

    // Event listener saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi form tambah
        toggleInputForm();

        // Hilangkan disabled attribute dari semua select departemen untuk input manual
        document.querySelectorAll('.editDepartemenSelect').forEach(select => {
            select.removeAttribute('disabled');
        });

        // Event listener untuk modal edit saat dibuka
        document.querySelectorAll('[id^="modal-update-"]').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) {
                    const isManualInput = !form.querySelector('input[readonly]');
                    const deptSelect = form.querySelector('.editDepartemenSelect');

                    if (isManualInput && deptSelect) {
                        deptSelect.removeAttribute('disabled');
                    }
                }
            });
        });

        // Event listener untuk form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const deptSelect = this.querySelector('.editDepartemenSelect');
                if (deptSelect) {
                    deptSelect.removeAttribute('disabled');
                }
            });
        });
    });
    </script>

    <?php require('../layouts/assetsFooter.php'); ?>