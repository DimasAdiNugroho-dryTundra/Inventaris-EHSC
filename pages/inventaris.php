<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
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
                            <div class="col-12 d-flex justify-content-start align-items-center">
                                <a href="scanQRcode.php" class="btn btn-primary me-2">
                                    <i class="ti ti-qrcode"></i> Scan QR Code
                                </a>
                            </div>
                        </div>
                        <h4 class="card-header">Barang Inventaris Tersedia</h4>
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
                                        <option value="inventaris.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="inventaris.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="inventaris.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="row p-3">
                            <div class="col-12 d-flex justify-content-start align-items-center">
                                <a href="../report/printLaporanInventarisTersedia.php" class="btn btn-success">
                                    <i class="ti ti-printer"></i> Cetak Laporan
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Kode Inventaris</th>
                                        <th class="text-center align-middle">Departemen</th>
                                        <th class="text-center align-middle">Kategori</th>
                                        <th class="text-center align-middle">Nama Barang</th>
                                        <th class="text-center align-middle">Jumlah Awal</th>
                                        <th class="text-center align-middle">Jumlah Akhir</th>
                                        <th class="text-center align-middle">Satuan</th>
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
                                        <td class="text-center align-middle"><?php echo $row['nama_departemen']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['nama_kategori']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['nama_barang']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['jumlah_awal']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['jumlah_akhir']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['satuan']; ?></td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_inventaris']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_inventaris']; ?>">Delete</button>
                                            <a href="detail_inventaris.php?id=<?php echo $row['id_inventaris']; ?>"
                                                class="btn btn-primary btn-sm">Detail</a>
                                        </td>
                                    </tr>

                                    <!-- Modal Update untuk Barang Tersedia -->
                                    <div class="modal fade" id="modal-update-<?php echo $row['id_inventaris']; ?>"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Data Inventaris</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" class="form-edit-inventaris">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id_inventaris"
                                                            value="<?php echo $row['id_inventaris']; ?>">

                                                        <div class="mb-3">
                                                            <label class="form-label">Kode Inventaris</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row['kode_inventaris']; ?>" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Barang</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row['nama_barang']; ?>" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Jumlah Awal</label>
                                                            <input type="number" class="form-control bg-light"
                                                                value="<?php echo $row['jumlah_awal']; ?>" readonly>
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

                                                        <div class="mb-3">
                                                            <label class="form-label">Satuan</label>
                                                            <input type="text" name="satuan"
                                                                class="form-control bg-light"
                                                                value="<?php echo $row['satuan']; ?>" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Kategori</label>
                                                            <select name="id_kategori" class="form-select" required>
                                                                <option value="">Pilih Kategori</option>
                                                                <?php
                            $kat_query = "SELECT * FROM kategori ORDER BY 
                                CASE WHEN id_kategori = '{$row['id_kategori']}' THEN 0 ELSE 1 END,
                                nama_kategori ASC";
                            $kat_result = mysqli_query($conn, $kat_query);
                            while ($kat = mysqli_fetch_assoc($kat_result)) {
                                $selected = ($kat['id_kategori'] == $row['id_kategori']) ? 'selected' : '';
                                echo "<option value='" . $kat['id_kategori'] . "' $selected>" 
                                     . $kat['nama_kategori'] . "</option>";
                            }
                            ?>
                                                            </select>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan
                                                                Perubahan</button>
                                                        </div>
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
                            <!-- Pagination untuk Barang Inventaris Tersedia -->
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

                        <!-- Tabel untuk menampilkan barang dengan jumlah akhir 0 -->
                        <h4 class="card-header">Barang Inventaris Tidak Tersedia</h4>
                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search_zero"
                                        placeholder="Cari nama barang..." value="<?php echo $search_zero; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit_zero" class="label me-2">Tampilkan:</label>
                                    <select id="limit_zero" class="select2 form-select"
                                        onchange="location = this.value;">
                                        <option value="inventaris.php?limit_zero=5"
                                            <?php if ($limit_zero == 5) echo 'selected'; ?>>5</option>
                                        <option value="inventaris.php?limit_zero=10"
                                            <?php if ($limit_zero == 10) echo 'selected'; ?>>10</option>
                                        <option value="inventaris.php?limit_zero=20"
                                            <?php if ($limit_zero == 20) echo 'selected'; ?>>20</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="row p-3">
                            <div class="col-12 d-flex justify-content-start align-items-center">
                                <a href="../report/printLaporanInventarisTidakTersedia.php" class="btn btn-success">
                                    <i class="ti ti-printer"></i> Cetak Laporan
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Kode Inventaris</th>
                                        <th class="text-center align-middle">Departemen</th>
                                        <th class="text-center align-middle">Kategori</th>
                                        <th class="text-center align-middle">Nama Barang</th>
                                        <th class="text-center align-middle">Jumlah Awal</th>
                                        <th class="text-center align-middle">Jumlah Akhir</th>
                                        <th class="text-center align-middle">Satuan</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
            $no = 1;
            while ($row_zero = mysqli_fetch_assoc($result_zero)) {
            ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo $no++; ?></td>
                                        <td class="text-center align-middle"><?php echo $row_zero['kode_inventaris']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row_zero['nama_departemen']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row_zero['nama_kategori']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row_zero['nama_barang']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row_zero['jumlah_awal']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row_zero['jumlah_akhir']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row_zero['satuan']; ?></td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-zero-<?php echo $row_zero['id_inventaris']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-zero-<?php echo $row_zero['id_inventaris']; ?>">Delete</button>
                                            <a href="detail_inventaris.php?id=<?php echo $row_zero['id_inventaris']; ?>"
                                                class="btn btn-primary btn-sm">Detail</a>
                                        </td>
                                    </tr>

                                    <!-- Modal Update untuk Barang Tidak Tersedia -->
                                    <div class="modal fade"
                                        id="modal-update-zero-<?php echo $row_zero['id_inventaris']; ?>" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Data Inventaris</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" class="form-edit-inventaris">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id_inventaris"
                                                            value="<?php echo $row_zero['id_inventaris']; ?>">

                                                        <div class="mb-3">
                                                            <label class="form-label">Kode Inventaris</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row_zero['kode_inventaris']; ?>"
                                                                readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Barang</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row_zero['nama_barang']; ?>"
                                                                readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Jumlah Awal</label>
                                                            <input type="number" class="form-control bg-light"
                                                                value="<?php echo $row_zero['jumlah_awal']; ?>"
                                                                readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal Perolehan</label>
                                                            <input type="date" class="form-control bg-light"
                                                                value="<?php echo $row_zero['tanggal_perolehan']; ?>"
                                                                readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Departemen</label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="<?php echo $row_zero['nama_departemen']; ?>"
                                                                readonly>
                                                            <input type="hidden" name="id_departemen"
                                                                value="<?php echo $row_zero['id_departemen']; ?>">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Satuan</label>
                                                            <input type="text" name="satuan"
                                                                class="form-control bg-light"
                                                                value="<?php echo $row_zero['satuan']; ?>" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Kategori</label>
                                                            <select name="id_kategori" class="form-select" required>
                                                                <option value="">Pilih Kategori</option>
                                                                <?php
                            $kat_query = "SELECT * FROM kategori ORDER BY 
                                CASE WHEN id_kategori = '{$row_zero['id_kategori']}' THEN 0 ELSE 1 END,
                                nama_kategori ASC";
                            $kat_result = mysqli_query($conn, $kat_query);
                            while ($kat = mysqli_fetch_assoc($kat_result)) {
                                $selected = ($kat['id_kategori'] == $row_zero['id_kategori']) ? 'selected' : '';
                                echo "<option value='" . $kat['id_kategori'] . "' $selected>" 
                                     . $kat['nama_kategori'] . "</option>";
                            }
                            ?>
                                                            </select>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan
                                                                Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Delete untuk barang dengan jumlah akhir 0 -->
                                    <div class="modal fade"
                                        id="modal-delete-zero-<?php echo $row_zero_zero['id_inventaris']; ?>"
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
                                                    <?php echo $row_zero['nama_barang']; ?>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <a href="inventaris.php?delete=<?php echo $row_zero['id_inventaris']; ?>"
                                                        class="btn btn-danger">Hapus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination untuk Barang Inventaris Tidak Tersedia -->
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination pagination-rounded justify-content-center">
                                <?php if ($page_zero > 1) { ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page_zero=<?php echo $page_zero - 1; ?>&limit_zero=<?php echo $limit_zero; ?>"
                                        aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php for ($i = 1; $i <= $totalPages_zero; $i++) { ?>
                                <li class="page-item <?php if ($i == $page_zero) echo 'active'; ?>">
                                    <a class="page-link"
                                        href="?page_zero=<?php echo $i; ?>&limit_zero=<?php echo $limit_zero; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php } ?>
                                <?php if ($page_zero < $totalPages_zero) { ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page_zero=<?php echo $page_zero + 1; ?>&limit_zero=<?php echo $limit_zero; ?>"
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
                                    <form method="POST" id="formTambahInventaris" onsubmit="return validasiForm()">
                                        <input type="hidden" name="action" value="create">

                                        <div class="mb-3">
                                            <label class="form-label">Penerimaan Barang</label>
                                            <select name="id_penerimaan" class="form-select" id="id_penerimaan" required
                                                onchange="updateKolom()">
                                                <option value="">Pilih Penerimaan Barang</option>
                                                <?php
                            $penerimaan_query = "SELECT pb.id_penerimaan, pb.nama_barang, pb.tanggal_terima, d.nama_departemen, pb.jumlah, pb.satuan 
                                                 FROM penerimaan_barang pb
                                                 JOIN departemen d ON pb.id_departemen = d.id_departemen 
                                                 WHERE pb.id_penerimaan NOT IN (SELECT id_penerimaan FROM inventaris WHERE id_penerimaan IS NOT NULL)";
                            $penerimaan_result = mysqli_query($conn, $penerimaan_query);
                            while ($penerimaan = mysqli_fetch_assoc($penerimaan_result)) {
                                echo "<option value='" . $penerimaan['id_penerimaan'] . "' data-nama='" . $penerimaan['nama_barang'] . "' data-tanggal='" . $penerimaan['tanggal_terima'] . "' data-departemen='" . $penerimaan['nama_departemen'] . "' data-jumlah='" . $penerimaan['jumlah'] . "' data-satuan='" . $penerimaan['satuan'] . "'>" . $penerimaan['nama_barang'] . "</option>";
                            }
                            ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nama Barang</label>
                                            <input type="text" class="form-control bg-light" id="nama_barang" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Perolehan</label>
                                            <input type="date" class="form-control bg-light" id="tanggal_perolehan"
                                                readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Departemen</label>
                                            <input type="text" class="form-control bg-light" id="departemen" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Jumlah Awal</label>
                                            <input type="number" name="jumlah_awal" class="form-control bg-light"
                                                id="jumlah_awal" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Satuan</label>
                                            <input type="text" class="form-control bg-light" id="satuan" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Kategori</label>
                                            <select name="id_kategori" class="form-select" required>
                                                <option value="">Pilih Kategori</option>
                                                <?php
                            $kat_query = "SELECT * FROM kategori";
                            $kat_result = mysqli_query($conn, $kat_query);
                            while ($kat = mysqli_fetch_assoc($kat_result)) {
                                echo "<option value='" . $kat['id_kategori'] . "'>" . $kat['nama_kategori'] . "</option>";
                            }
                            ?>
                                            </select>
                                            <div class="invalid-feedback">Silakan pilih kategori</div>
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

    <script>
    function updateKolom() {
        const select = document.getElementById('id_penerimaan');
        const selectedOption = select.options[select.selectedIndex];

        document.getElementById('nama_barang').value = selectedOption.getAttribute('data-nama') || '';
        document.getElementById('tanggal_perolehan').value = selectedOption.getAttribute('data-tanggal') || '';
        document.getElementById('departemen').value = selectedOption.getAttribute('data-departemen') || '';
        document.getElementById('jumlah_awal').value = selectedOption.getAttribute('data-jumlah') || '';
        document.getElementById('satuan').value = selectedOption.getAttribute('data-satuan') || '';
    }

    // Fungsi untuk mendapatkan pesan validasi
    function getPesanValidasi(labelText, jenisInput) {
        labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

        const pesanKhusus = {
            'penerimaan barang': 'Mohon pilih penerimaan barang',
            'kategori': 'Mohon pilih kategori',
            'jumlah awal': 'Mohon masukkan jumlah awal'
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
        const formManual = document.querySelectorAll('.form-tambah-inventaris, .form-edit-inventaris');
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
    document.getElementById('tambahInventarisModal').addEventListener('show.bs.modal', function() {
        // Reset form saat modal dibuka
        const form = this.querySelector('form');
        if (form) form.reset();

        // Terapkan validasi
        setTimeout(terapkanValidasi, 100);
    });

    // Event listener saat modal update dibuka
    document.querySelectorAll('[id^="modal-update-"], [id^="modal-update-zero-"]').forEach(modal => {
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