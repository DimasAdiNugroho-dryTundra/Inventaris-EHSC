<?php
// File: KehilanganBarang.php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudKehilanganBarang.php'); 
require('../layouts/header.php');
?>

<style>
.w-50px {
    width: 50px !important;
}

.w-80px {
    width: 80px !important;
}

.w-100px {
    width: 100px !important;
}

.w-120px {
    width: 120px !important;
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
                            <h2 class="mb-1">Manajemen Kehilangan Barang</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Manajemen Kehilangan Barang</li>
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
                            Data Kehilangan Barang
                            <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahKehilanganModal">Tambah Kehilangan</button>
                            <?php endif; ?>
                        </h4>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <form method="POST" class="d-flex">
                                    <input type="text" class="form-control me-2" name="search"
                                        placeholder="Cari Kehilangan barang..." value="<?php echo $search; ?>">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex justify-content-end align-items-center">
                                    <label for="limit" class="label me-2">Tampilkan:</label>
                                    <select id="limit" class="select2 form-select" onchange="location = this.value;">
                                        <option value="KehilanganBarang.php?limit=5"
                                            <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                        <option value="KehilanganBarang.php?limit=10"
                                            <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                        <option value="KehilanganBarang.php?limit=20"
                                            <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap" style="max-height: 340px;">
                            <table class="table table-hover table-sm small">
                                <thead class="table-light small">
                                    <tr>
                                        <th class="text-center align-middle w-50px">No</th>
                                        <th class="text-center align-middle w-100px">Kode</th>
                                        <th class="text-center align-middle w-150px">Barang</th>
                                        <th class="text-center align-middle w-120px">Ruangan</th>
                                        <th class="text-center align-middle w-120px">Tanggal</th>
                                        <th class="text-center align-middle w-80px">Cawu</th>
                                        <th class="text-center align-middle w-100px">Jumlah</th>
                                        <th class="text-center align-middle w-120px">Sumber</th>
                                        <th class="text-center align-middle w-120px">Petugas</th>
                                        <th class="text-center align-middle w-150px">Keterangan</th>
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
                                        <td class="text-center align-middle"><?php echo $row['kode_inventaris']; ?></td>
                                        <td class="text-center align-middle">
                                            <?php echo $row['nama_barang'] . " - " . $row['merk']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['nama_ruangan']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['tanggal_kehilangan']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['cawu']; ?></td>
                                        <td class="text-center align-middle"><?php echo $row['jumlah_kehilangan']; ?>
                                        <td class="text-center align-middle"><?php echo $row['sumber_inventaris']; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['nama_petugas']; ?></td>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $row['keterangan']; ?></td>
                                        <td class="text-center align-middle">
                                            <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_kehilangan_barang']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_kehilangan_barang']; ?>">Hapus</button>
                                            <?php endif; ?>
                                            <a href="../report/printLaporanKehilanganBarang.php?id=<?php echo $row['id_kehilangan_barang']; ?>"
                                                class="btn btn-primary btn-sm">Cetak</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Edit/Update -->
                        <?php
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="modal fade" id="modal-update-<?php echo $row['id_kehilangan_barang']; ?>"
                            tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modalUpdateLabel">Edit Kehilangan Barang</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_kehilangan_barang"
                                                value="<?php echo $row['id_kehilangan_barang']; ?>">
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
                                                <label class="form-label">Merk</label>
                                                <input type="text" class="form-control bg-light" name="merk"
                                                    value="<?php echo $row['merk']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ruangan</label>
                                                <input type="text" class="form-control bg-light" name="ruangan"
                                                    value="<?php echo $row['nama_ruangan']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sumber Inventaris</label>
                                                <input type="text" class="form-control bg-light"
                                                    name="sumber_inventaris"
                                                    value="<?php echo $row['sumber_inventaris']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Kehilangan</label>
                                                <input type="date" class="form-control bg-light"
                                                    name="tanggal_kehilangan"
                                                    value="<?php echo $row['tanggal_kehilangan']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cawu</label>
                                                <input type="text" class="form-control bg-light" name="cawu"
                                                    value="<?php echo $row['cawu']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Kehilangan</label>
                                                <input type="number" class="form-control bg-light"
                                                    name="jumlah_kehilangan"
                                                    value="<?php echo $row['jumlah_kehilangan']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sumber Inventaris</label>
                                                <input type="text" class="form-control bg-light"
                                                    name="sumber_inventaris"
                                                    value="<?php echo $row['sumber_inventaris']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Petugas Kontrol</label>
                                                <input type="text" class="form-control bg-light" name="nama_petugas"
                                                    value="<?php echo $row['nama_petugas']; ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control" name="keterangan"
                                                    required><?php echo $row['keterangan']; ?></textarea>
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
                        <div class="modal fade" id="modal-delete-<?php echo $row['id_kehilangan_barang']; ?>"
                            tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus kehilangan barang
                                        "<?php echo $row['nama_barang']; ?>"?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <a href="kehilanganBarang.php?delete=<?php echo $row['id_kehilangan_barang']; ?>"
                                            class="btn btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- Modal Tambah -->
                        <div class="modal fade" id="tambahKehilanganModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Kehilangan Barang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label class="form-label">Data Kehilangan Barang</label>
                                                <select name="id_inventaris" class="form-select" required
                                                    onchange="fillKehilanganData(this)">
                                                    <option value="">Pilih Data Kehilangan Barang</option>
                                                    <?php
                                                    $barang = getBarangHilang($conn);
                                                    while ($row = mysqli_fetch_assoc($barang)) {
                                                        echo "<option value='{$row['id_inventaris']}'
                                                                data-nama-barang='{$row['nama_barang']}'
                                                                data-merk='{$row['merk']}'
                                                                data-ruangan='{$row['nama_ruangan']}' 
                                                                data-sumber='{$row['sumber_inventaris']}'
                                                                data-departemen='{$row['nama_departemen']}'  
                                                                data-tanggal='{$row['tanggal_kontrol']}'
                                                                data-cawu='{$row['cawu']}'
                                                                data-jumlah='{$row['jumlah_hilang']}'
                                                                data-nama-petugas='{$row['nama_petugas']}'>
                                                                {$row['kode_inventaris']} - {$row['nama_barang']} - {$row['merk']} - Ruang {$row['nama_ruangan']} - 
                                                                {$row['sumber_inventaris']} - {$row['cawu']} - " . date('Y', strtotime($row['tanggal_kontrol'])) . " - 
                                                                {$row['jumlah_hilang']} {$row['satuan']}
                                                            </option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Barang</label>
                                                <input type="text" class="form-control bg-light" name="nama_barang"
                                                    id="nama_barang" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Merk</label>
                                                <input type="text" class="form-control bg-light" name="merk" id="merk"
                                                    readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Departemen</label>
                                                <input type="text" class="form-control bg-light" name="departemen"
                                                    id="departemen" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ruangan</label>
                                                <input type="text" class="form-control bg-light" name="ruangan"
                                                    id="ruangan" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Kehilangan</label>
                                                <input type="date" class="form-control bg-light"
                                                    name="tanggal_kehilangan" id="tanggal_kehilangan" required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cawu</label>
                                                <input type="text" class="form-control bg-light" name="cawu" id="cawu"
                                                    required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Kehilangan</label>
                                                <input type="number" class="form-control bg-light"
                                                    name="jumlah_kehilangan" id="jumlah_kehilangan" required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sumber Inventaris</label>
                                                <input type="text" class="form-control bg-light"
                                                    name="sumber_inventaris" id="sumber_inventaris" required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Petugas Kontrol</label>
                                                <input type="text" class="form-control bg-light" name="nama_petugas"
                                                    id="nama_petugas" required readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control" name="keterangan" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="tambahKehilangan"
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
            </div>
            <?php require('../layouts/footer.php'); ?>
            <div class="content-backdrop fade"></div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>

<script>
function fillKehilanganData(select) {
    const selectedOption = select.options[select.selectedIndex];

    const namaBarang = selectedOption.getAttribute('data-nama-barang');
    const merk = selectedOption.getAttribute('data-merk');
    const ruangan = selectedOption.getAttribute('data-ruangan');
    const sumberInventaris = selectedOption.getAttribute('data-sumber');
    const departemen = selectedOption.getAttribute('data-departemen');
    const tanggal = selectedOption.getAttribute('data-tanggal');
    const cawu = selectedOption.getAttribute('data-cawu');
    const jumlah = selectedOption.getAttribute('data-jumlah');
    const namaPetugas = selectedOption.getAttribute('data-nama-petugas');

    document.getElementById('nama_barang').value = namaBarang;
    document.getElementById('merk').value = merk;
    document.getElementById('ruangan').value = ruangan;
    document.getElementById('sumber_inventaris').value = sumberInventaris;
    document.getElementById('departemen').value = departemen;
    document.getElementById('tanggal_kehilangan').value = tanggal;
    document.getElementById('cawu').value = cawu;
    document.getElementById('jumlah_kehilangan').value = jumlah;
    document.getElementById('nama_petugas').value = namaPetugas;
}

// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'barang': 'Silahkan pilih data barang yang hilang!',
        'keterangan': 'Kolom keterangan wajib diisi!'
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

// Fungsi untuk validasi form manual
function validasiFormManual() {
    const formManual = document.querySelectorAll('.needs-validation');
    if (formManual) {
        formManual.forEach(form => {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
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
        });
    }
}

// Event listener saat modal tambah dibuka
document.getElementById('tambahKehilanganModal').addEventListener('show.bs.modal', function() {
    // Reset form saat modal dibuka
    const form = this.querySelector('form');
    if (form) form.reset();

    // Terapkan validasi
    setTimeout(terapkanValidasi, 100);
});

// Event listener saat modal edit dibuka
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


<?php require('../layouts/assetsFooter.php'); ?>