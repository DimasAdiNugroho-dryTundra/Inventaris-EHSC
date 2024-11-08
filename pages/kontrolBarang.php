<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../server/crudKontrolBarang.php');
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
                            Data Kontrol Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahKontrolModal">
                                Tambah Kontrol
                            </button>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari kode inventaris..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="kontrol_barang.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5
                                        </option>
                                        <option value="kontrol_barang.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>
                                            10</option>
                                        <option value="kontrol_barang.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>
                                            20</option>
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
                                        <th>Nama Barang</th>
                                        <th>Cawu</th>
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
                                        switch($row['status']) {
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
                                        <td class="align-middle"><?php echo $row['cawu']; ?></td>
                                        <td class="align-middle">
                                            <?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td class="align-middle"><?php echo $statusText; ?></td>
                                        <td class="align-middle"><?php echo $row['jumlah']; ?></td>
                                        <td class="align-middle"><?php echo $row['keterangan']; ?></td>
                                        <td class="align-middle">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?php echo $row['id_kontrol_barang']; ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal<?php echo $row['id_kontrol_barang']; ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editModal<?php echo $row['id_kontrol_barang']; ?>"
                                        tabindex="-1" aria-hidden="true">
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
                                                            value="<?php echo $row['id_kontrol_barang']; ?>">
                                                        <input type="hidden" name="id_inventaris"
                                                            value="<?php echo $row['id_inventaris']; ?>">

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal</label>
                                                            <input type="date" name="tanggal" class="form-control"
                                                                value="<?php echo date('Y-m-d', strtotime($row['tanggal'])); ?>"
                                                                required>

                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="1"
                                                                    <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>
                                                                    Baik</option>
                                                                <option value="2"
                                                                    <?php echo ($row['status'] == 2) ? 'selected' : ''; ?>>
                                                                    Pindah</option>
                                                                <option value="3"
                                                                    <?php echo ($row['status'] == 3) ? 'selected' : ''; ?>>
                                                                    Rusak</option>
                                                                <option value="4"
                                                                    <?php echo ($row['status'] == 4) ? 'selected' : ''; ?>>
                                                                    Hilang</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Jumlah</label>
                                                            <input type="number" name="jumlah" class="form-control"
                                                                value="<?php echo $row['jumlah']; ?>" required>
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
                                    <div class="modal fade" id="deleteModal<?php echo $row['id_kontrol_barang']; ?>"
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
                                                    <a href="?delete=<?php echo $row['id_kontrol_barang']; ?>"
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
        $invResult = getAvailableInventaris($conn);
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
                                            <input type="number" name="jumlah" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="keterangan" class="form-control" rows="3"
                                                required>Barang dalam kondisi baik</textarea>
                                        </div>

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
<!-- / Layout wrapper -->

<?php require('../layouts/assetsFooter.php'); ?>