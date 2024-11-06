<?php
// File: permintaanBarang.php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../server/crudPermintaanBarang.php'); // Adjust this file accordingly
require('../layouts/header.php');
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
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

                        <h4 class="card-header d-flex justify-content-between align-items-center">
                            Data Permintaan Barang
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahPermintaanModal">Tambah Permintaan</button>
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
                                        <th>Departemen</th>
                                        <th>Nama</th>
                                        <th>Tanggal</th>
                                        <th>Spesifikasi</th>
                                        <th>Kebutuhan</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = $offset + 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $total_harga = $row['kebutuhan_qty'] * $row['harga_satuan'];
                                        $status_text = $row['status'] == 1 ? 'Disetujui' : ($row['status'] == 2 ? 'Tidak Disetujui' : 'Menunggu');
                                        $status_class = $row['status'] == 1 ? 'text-success' : ($row['status'] == 2 ? 'text-danger' : 'text-warning');
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['nama_departemen']; ?></td>
                                        <td><?php echo $row['nama_barang']; ?></td>
                                        <td><?php echo $row['tanggal_permintaan']; ?></td>
                                        <td><?php echo $row['spesifikasi']; ?></td>
                                        <td><?php echo $row['kebutuhan_qty']; ?></td>
                                        <td>Rp <?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                                        <td><span
                                                class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_permintaan']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_permintaan']; ?>">Delete</button>
                                            <a href="laporan_permintaan.php?id=<?php echo $row['id_permintaan']; ?>"
                                                class="btn btn-primary btn-sm">Laporan</a>
                                        </td>
                                    </tr>
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
                                                            <label for='kebutuhan_qty' class='form-label'>Kebutuhan
                                                                Qty</label>
                                                            <input type='number' class='form-control'
                                                                name='kebutuhan_qty' id='kebutuhan_qty_update'
                                                                value='<?php echo $row['kebutuhan_qty']; ?>' required>
                                                        </div>
                                                        <div class='mb-3'>
                                                            <label for='harga_satuan' class='form-label'>Harga
                                                                Satuan</label>
                                                            <input type='text' class='form-control' name='harga_satuan'
                                                                id='harga_satuan_update'
                                                                value='<?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?>'
                                                                required>
                                                        </div>
                                                        <div class='mb-3'>
                                                            <label for='total_harga' class='form-label'>Total
                                                                Harga</label>
                                                            <input type='text' class='form-control'
                                                                id='total_harga_update'
                                                                value='<?php echo number_format($row['kebutuhan_qty'] * $row['harga_satuan'], 0, ',', '.'); ?>'
                                                                readonly>
                                                        </div>
                                                        <div class='mb-3'>
                                                            <label for='status' class='form-label'>Status</label>
                                                            <select class='form-select' name='status' required>
                                                                <option value='0'
                                                                    <?php echo ($row['status'] == 0 ? 'selected' : ''); ?>>
                                                                    Menunggu</option>
                                                                <option value='1'
                                                                    <?php echo ($row['status'] == 1 ? 'selected' : ''); ?>>
                                                                    Disetujui</option>
                                                                <option value='2'
                                                                    <?php echo ($row['status'] == 2 ? 'selected' : ''); ?>>
                                                                    Tidak Disetujui</option>
                                                            </select>
                                                        </div>
                                                        <button type='submit' class='btn btn-primary'>Simpan</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Delete -->
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
                                                    <?php echo $row['nama_barang']; ?>?
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
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Permintaan Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label class="form-label">Departemen</label>
                                            <select name="id_departemen" class="form-select" required>
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
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Spesifikasi</label>
                                            <textarea name="spesifikasi" class="form-control" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kebutuhan Qty</label>
                                            <input type="number" name="kebutuhan_qty" id="kebutuhan_qty"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Harga Satuan</label>
                                            <input type="text" name="harga_satuan" id="harga_satuan"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Harga</label>
                                            <input type="text" id="total_harga" class="form-control" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="0">Menunggu</option>
                                                <option value="1">Disetujui</option>
                                                <option value="2">Tidak Disetujui</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="tambahPermintaan"
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
            </div>
            <!-- Footer -->
            <?php
            require('../layouts/footer.php');
            ?>
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

<?php
require('../layouts/assetsFooter.php')
?>


<script>
// Function to format number to currency
function formatRupiah(angka, prefix) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

// Function to calculate and display total price
function calculateTotal(qtyId, priceId, totalId) {
    var qty = document.getElementById(qtyId).value;
    var price = document.getElementById(priceId).value.replace(/[^0-9]/g, '');
    var total = qty * price;
    document.getElementById(totalId).value = formatRupiah(total.toString(), 'Rp. ');
}

// Event listeners for add modal
document.getElementById('kebutuhan_qty').addEventListener('input', function() {
    calculateTotal('kebutuhan_qty', 'harga_satuan', 'total_harga');
});

document.getElementById('harga_satuan').addEventListener('input', function(e) {
    this.value = formatRupiah(this.value, 'Rp. ');
    calculateTotal('kebutuhan_qty', 'harga_satuan', 'total_harga');
});

// Event listeners for update modal
document.getElementById('kebutuhan_qty_update').addEventListener('input', function() {
    calculateTotal('kebutuhan_qty_update', 'harga_satuan_update', 'total_harga_update');
});

document.getElementById('harga_satuan_update').addEventListener('input', function(e) {
    this.value = formatRupiah(this.value, 'Rp. ');
    calculateTotal('kebutuhan_qty_update', 'harga_satuan_update', 'total_harga_update');
});

// Function to strip formatting from price input
function stripFormatting(value) {
    return value.replace(/[^\d]/g, '');
}

// Add event listeners for form submission
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        let hargaSatuanInput = this.querySelector('[name="harga_satuan"]');
        if (hargaSatuanInput) {
            hargaSatuanInput.value = stripFormatting(hargaSatuanInput.value);
        }
    });
});
</script>