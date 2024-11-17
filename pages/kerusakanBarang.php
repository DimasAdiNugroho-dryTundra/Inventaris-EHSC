<?php
// File: kerusakanBarang.php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudKerusakanBarang.php'); // Ganti dengan file CRUD yang sesuai
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
                            <h2 class="mb-1">Manajemen Kerusakan Barang</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Kerusakan Barang</li>
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
                            Data Kerusakan Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahKerusakanModal">Tambah Kerusakan</button>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari kerusakan barang..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="kerusakanBarang.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="kerusakanBarang.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="kerusakanBarang.php?limit=20"
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
                                        <th class="text-center align-middle">Nama Barang</th>
                                        <th class="text-center align-middle">Tanggal Kerusakan</th>
                                        <th class="text-center align-middle">Cawu</th>
                                        <th class="text-center align-middle">Jumlah Kerusakan</th>
                                        <th class="text-center align-middle">Foto Kerusakan</th>
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
                                        <td class="text-center align-middle"><?php echo $row['nama_barang']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['tanggal_kerusakan']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['cawu']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['jumlah_kerusakan']; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($row['foto_kerusakan']) { ?>
                                            <img src="../upload/kerusakan/<?php echo $row['foto_kerusakan']; ?>"
                                                alt="Foto Kerusakan" width="100">
                                            <?php } else { ?>
                                            <span>Tidak ada foto</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['keterangan']; ?></td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_kerusakan_barang']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_kerusakan_barang']; ?>">Delete</button>
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
                        <div class="modal fade" id="modal-update-<?php echo $row['id_kerusakan_barang']; ?>"
                            tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modalUpdateLabel">Edit Kerusakan Barang</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" enctype="multipart/form-data" class="needs-validation"
                                            novalidate>
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_kerusakan_barang"
                                                value="<?php echo $row['id_kerusakan_barang']; ?>">
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
                                                <label class="form-label">Tanggal Kerusakan</label>
                                                <input type="date" class="form-control bg-light"
                                                    name="tanggal_kerusakan"
                                                    value="<?php echo $row['tanggal_kerusakan']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cawu</label>
                                                <input type="text" class="form-control bg-light" name="cawu"
                                                    value="<?php echo $row['cawu']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Kerusakan</label>
                                                <input type="number" class="form-control bg-light"
                                                    name="jumlah_kerusakan"
                                                    value="<?php echo $row['jumlah_kerusakan']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Foto Kerusakan</label>
                                                <input type="file" class="form-control" name="foto_kerusakan"
                                                    accept="image/*">
                                                <div class="invalid-feedback">
                                                    Silakan upload foto kerusakan jika ada!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control" name="keterangan"
                                                    required><?php echo $row['keterangan']; ?></textarea>
                                                <div class="invalid-feedback">
                                                    Kolom keterangan wajib diisi!
                                                </div>
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
                        <div class="modal fade" id="modal-delete-<?php echo $row['id_kerusakan_barang']; ?>"
                            tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus kerusakan barang
                                        "<?php echo $row['nama_barang']; ?>"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <a href="kerusakanBarang.php?delete=<?php echo $row['id_kerusakan_barang']; ?>"
                                            class="btn btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- Modal Tambah -->
                        <div class="modal fade" id="tambahKerusakanModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Kerusakan Barang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data" class="needs-validation"
                                            novalidate>
                                            <div class="mb-3">
                                                <label class="form-label">Barang</label>
                                                <select name="id_inventaris" class="form-select" required
                                                    onchange="fillKerusakanData(this)">
                                                    <option value="">Pilih Barang</option>
                                                    <?php
                            $barangRusak = getBarangRusak($conn);
                            while ($row = mysqli_fetch_assoc($barangRusak)) {
                                echo "<option value='{$row['id_inventaris']}' 
                                            data-tanggal='{$row['tanggal_kontrol']}'
                                            data-cawu='{$row['cawu']}'
                                            data-jumlah='{$row['jumlah_rusak']}'
                                            data-tahun='" . date('Y', strtotime($row['tanggal_kontrol'])) . "'>
                                        {$row['kode_inventaris']} - {$row['nama_barang']} - {$row['cawu']}  - " . date('Y', strtotime($row['tanggal_kontrol'])) . " - {$row['jumlah_rusak']} rusak
                                    </option>";
                            }
                            ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Silakan pilih barang!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Kerusakan</label>
                                                <input type="date" class="form-control bg-light"
                                                    name="tanggal_kerusakan" id="tanggal_kerusakan" required readonly>
                                                <div class="invalid-feedback">
                                                    Kolom tanggal kerusakan wajib diisi!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cawu</label>
                                                <input type="text" class="form-control bg-light" name="cawu" id="cawu"
                                                    required readonly>
                                                <div class="invalid-feedback">
                                                    Kolom cawu wajib diisi!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Kerusakan</label>
                                                <input type="number" class="form-control bg-light"
                                                    name="jumlah_kerusakan" id="jumlah_kerusakan" required readonly>
                                                <div class="invalid-feedback">
                                                    Kolom jumlah kerusakan wajib diisi!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Foto Kerusakan</label>
                                                <input type="file" class="form-control" name="foto_kerusakan"
                                                    accept="image/*" required>
                                                <div class="invalid-feedback">
                                                    Silakan upload foto kerusakan!
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control" name="keterangan" required></textarea>
                                                <div class="invalid-feedback">
                                                    Kolom keterangan wajib diisi!
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="tambahKerusakan"
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
function fillKerusakanData(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    document.getElementById('tanggal_kerusakan').value = selectedOption.dataset.tanggal;
    document.getElementById('cawu').value = selectedOption.dataset.cawu;
    document.getElementById('jumlah_kerusakan').value = selectedOption.dataset.jumlah;
}
</script>

<?php require('../layouts/assetsFooter.php'); ?>