<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudManajemenUser.php');
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

.table-sm img {
    object-fit: cover;
    border-radius: 4px;
}

.table-sm .img-thumbnail {
    padding: 0.25rem;
    border-radius: 0.25rem;
    border: 1px solid #ddd;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.075);
}

.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    transform: scale(1.05);
}
</style>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php require('../layouts/sidePanel.php'); ?>

            <div class="layout-page">
                <?php require('../layouts/navbar.php'); ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h2 class="mb-1">Manajemen User</h2>
                            </div>
                            <div class="mt-3">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb breadcrumb-style1">
                                        <li class="breadcrumb-item">
                                            <a href="dashboard.php">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item active">Manajemen User</li>
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                                <span class="alert-icon rounded">
                                    <i class="ti ti-check"></i>
                                </span>
                                <?php echo $_SESSION['success_message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                            <?php endif; ?>

                            <h4 class="card-header d-flex justify-content-between align-items-center">
                                Data User
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#tambahUserModal">Tambah User</button>
                            </h4>

                            <!-- Form Pencarian dan Pagination -->
                            <div class="row p-3">
                                <div class="col-md-6">
                                    <form method="POST" class="d-flex">
                                        <input type="text" class="form-control me-2" name="search"
                                            placeholder="Cari nama user..." value="<?php echo $search; ?>">
                                        <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form class="d-flex justify-content-end align-items-center">
                                        <label for="limit" class="label me-2">Tampilkan:</label>
                                        <select id="limit" class="select2 form-select"
                                            onchange="location = this.value;">
                                            <option value="manajemenUser.php?limit=5"
                                                <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                                            <option value="manajemenUser.php?limit=10"
                                                <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                                            <option value="manajemenUser.php?limit=20"
                                                <?php if ($limit == 20) echo 'selected'; ?>>20</option>
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive text-nowrap" style="max-height: 340px;">
                                <table class="table table-hover table-sm small">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center align-middle w-50px">No</th>
                                            <th class="text-center align-middle w-150px">Nama</th>
                                            <th class="text-center align-middle w-120px">Username</th>
                                            <th class="text-center align-middle w-150px">Email</th>
                                            <th class="text-center align-middle w-100px">Jabatan</th>
                                            <th class="text-center align-middle w-100px">Hak Akses</th>
                                            <th class="text-center align-middle w-80px">Foto</th>
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
                                            <td class="text-center align-middle"><?php echo $row['nama']; ?></td>
                                            <td class="text-center align-middle"><?php echo $row['username']; ?></td>
                                            <td class="text-center align-middle"><?php echo $row['email']; ?></td>
                                            <td class="text-center align-middle"><?php echo $row['jabatan']; ?></td>
                                            <td class="text-center align-middle">
                                                <?php echo $row['hak_akses'] == 1 ? 'Aktif' : 'Non Aktif'; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if ($row['foto']) { ?>
                                                <div class="d-flex justify-content-center">
                                                    <img src="../upload/user/<?php echo $row['foto']; ?>"
                                                        class="img-thumbnail cursor-pointer" width="50" height="50"
                                                        style="object-fit: cover;" data-bs-toggle="modal"
                                                        data-bs-target="#modal-foto-<?php echo $row['id_user']; ?>">
                                                </div>

                                                <!-- Modal untuk foto -->
                                                <div class="modal fade" id="modal-foto-<?php echo $row['id_user']; ?>"
                                                    tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Foto User</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="../upload/user/<?php echo $row['foto']; ?>"
                                                                    class="img-fluid" style="max-height: 500px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } else { ?>
                                                <div class="text-center">Tidak ada foto</div>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modal-update-<?php echo $row['id_user']; ?>">Edit</button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal-delete-<?php echo $row['id_user']; ?>">Hapus</button>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="modal-update-<?php echo $row['id_user']; ?>"
                                            tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="modalUpdateLabel">Edit User</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="manajemenUser.php"
                                                            enctype="multipart/form-data">
                                                            <input type="hidden" name="action" value="update">
                                                            <input type="hidden" name="id_user"
                                                                value="<?php echo $row['id_user']; ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label">Nama</label>
                                                                <input type="text" class="form-control" name="nama"
                                                                    value="<?php echo $row['nama']; ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Username</label>
                                                                <input type="text" class="form-control" name="username"
                                                                    value="<?php echo $row['username']; ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" class="form-control" name="email"
                                                                    value="<?php echo $row['email']; ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Jabatan</label>
                                                                <select name="jabatan" class="form-select" required>
                                                                    <option value="">Pilih Jabatan</option>
                                                                    <option value="operator"
                                                                        <?php if ($row['jabatan'] == 'operator') echo 'selected'; ?>>
                                                                        Operator</option>
                                                                    <option value="staff"
                                                                        <?php if ($row['jabatan'] == 'staff') echo 'selected'; ?>>
                                                                        Staff</option>
                                                                    <option value="administrasi"
                                                                        <?php if ($row['jabatan'] == 'administrasi') echo 'selected'; ?>>
                                                                        Administrasi</option>
                                                                    <option value="petugas kontrol"
                                                                        <?php if ($row['jabatan'] == 'petugas kontrol') echo 'selected'; ?>>
                                                                        Petugas Kontrol</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Hak Akses</label>
                                                                <select name="hak_akses" class="form-select" required>
                                                                    <option value="">Pilih Hak Akses</option>
                                                                    <option value="1"
                                                                        <?php if ($row['hak_akses'] == 1) echo 'selected'; ?>>
                                                                        Aktif</option>
                                                                    <option value="0"
                                                                        <?php if ($row['hak_akses'] == 0) echo 'selected'; ?>>
                                                                        Non Aktif</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Password (Kosongkan jika tidak
                                                                    ingin mengubah)</label>
                                                                <input type="password" class="form-control"
                                                                    name="password">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Foto (max 2MB - JPG, JPEG,
                                                                    PNG)</label>
                                                                <input type="file" name="foto" class="form-control"
                                                                    accept="image/*">
                                                                <?php if ($row['foto']) { ?>
                                                                <div class="mt-2 text-center">
                                                                    <div class="card"
                                                                        style="display: inline-block; width: 200px;">
                                                                        <img src="../upload/user/<?php echo $row['foto']; ?>"
                                                                            class="card-img-top" alt="Foto User">
                                                                    </div>
                                                                </div>
                                                                <?php } ?>
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


                                        <!-- Modal Delete -->
                                        <div class="modal fade" id="modal-delete-<?php echo $row['id_user']; ?>"
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
                                                        Apakah Anda yakin ingin menghapus user
                                                        "<?php echo $row['nama']; ?>"?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <form method="get" action="manajemenUser.php">
                                                            <input type="hidden" name="delete"
                                                                value="<?php echo $row['id_user']; ?>">
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
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
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>"
                                            aria-label="Previous">
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

                        <!-- Modal Tambah User -->
                        <div class="modal fade" id="tambahUserModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="manajemenUser.php" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="nama" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" name="username" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Password</label>
                                                <input type="password" name="password" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jabatan</label>
                                                <select name="jabatan" class="form-select" required>
                                                    <option value="">Pilih Jabatan</option>
                                                    <option value="operator">Operator</option>
                                                    <option value="operator">Staff</option>
                                                    <option value="administrasi">Administrasi</option>
                                                    <option value="petugas kontrol">Petugas Kontrol</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Hak Akses</label>
                                                <select name="hak_akses" class="form-select" required>
                                                    <option value="">Pilih Hak Akses</option>
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Non Aktif</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Foto (max 2MB - JPG, JPEG, PNG)</label>
                                                <input type="file" name="foto" class="form-control" accept="image/*"
                                                    required oninvalid="setCustomValidity('Silahkan pilih foto!')"
                                                    oninput="setCustomValidity('')">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="tambahUser"
                                                    class="btn btn-primary">Tambah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php require('../layouts/footer.php'); ?>

                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>

    <div class="drag-target"></div>
    </div>

    <?php require('../layouts/assetsFooter.php') ?>
</body>


<script>
// Fungsi untuk mendapatkan pesan validasi
function getPesanValidasi(labelText, jenisInput) {
    labelText = labelText.replace(/[:\s]+$/, '').toLowerCase();

    const pesanKhusus = {
        'nama': 'Silahkan masukan nama!',
        'username': 'Silahkan masukan username!',
        'password': 'Silahkan masukan password!',
        'email': 'Silahkan masukan email yang valid, contoh: example@example.com!',
        'jabatan': 'Silahkan pilih jabatan!',
        'hak akses': 'Silahkan pilih hak akses!',
        'foto': 'Silahkan pilih foto!'
    };

    return pesanKhusus[labelText] ||
        (jenisInput === 'select' ? `Silahkan pilih ${labelText}` : `Silahkan masukan ${labelText}`);
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
document.getElementById('tambahUserModal').addEventListener('show.bs.modal', function() {
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