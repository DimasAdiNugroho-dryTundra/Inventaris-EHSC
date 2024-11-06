<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../server/crudManajemenUser.php');
require('../layouts/header.php');
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <!-- navbar -->

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
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                        <!-- Hapus pesan setelah ditampilkan -->
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

                        <div class="table-responsive text-nowrap" style="max-height: 340px;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Jabatan</th>
                                        <th>Hak Akses</th>
                                        <th>Foto</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['nama']; ?></td>
                                        <td><?php echo $row['username']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['jabatan']; ?></td>
                                        <td><?php echo $row['hak_akses']; ?></td>
                                        <td><img src="../upload/user/<?php echo $row['foto']; ?>" alt="User Photo"
                                                style="width: 50px; height: 50px;"></td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-update-<?php echo $row['id_user']; ?>">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-<?php echo $row['id_user']; ?>">Delete</button>

                                        </td>
                                    </tr>
                                    <?php
                                        // Modal Update
                                    echo "
                                        <div class='modal fade' id='modal-update-" . $row['id_user'] . "' tabindex='-1' aria-labelledby='modalUpdateLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-lg modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h4 class='modal-title' id='modalUpdateLabel'>Edit User</h4>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <form method='post' action='manajemen-user.php' enctype='multipart/form-data'>
                                                            <input type='hidden' name='action' value='update'>
                                                            <input type='hidden' name='id_user' value='" . $row['id_user'] . "'>
                                                            <div class='mb-3'>
                                                                <label for='username' class='form-label'>Username</label>
                                                                <input type='text' class='form-control' name='username' value='" . $row['username'] . "' required>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label for='email' class='form-label'>Email</label>
                                                                <input type='email' class='form-control' name='email' value='" . $row['email'] . "' required>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label for='jabatan' class='form-label'>Jabatan</label>
                                                                <input type='text' class='form-control' name='jabatan' value='" . $row['jabatan'] . "' required>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label for='password' class='form-label'>Password</label>
                                                                <input type='password' class='form-control' name='password'>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label for='foto' class='form-label'>Foto (max 5MB, 1:1 aspect ratio)</label>
                                                                <input type='file' name='foto' class='form-control' accept='image/*'>
                                                            </div>
                                                            <button type='submit' class='btn btn-primary'>Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                    
                                    // modal validasi hapus
                                    echo"
                                        <div class='modal fade' id='modal-delete-" . $row['id_user'] . "' tabindex='-1'
                                            aria-labelledby='modalDeleteLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h5 class='modal-title' id='modalDeleteLabel'>Konfirmasi Hapus</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal'
                                                            aria-label='Close'></button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        Apakah Anda yakin ingin menghapus user " . $row['nama'] . "?
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary'
                                                            data-bs-dismiss='modal'>Batal</button>
                                                        <form method='get' action='manajemen-user.php'>
                                                            <input type='hidden' name='delete' value='" . $row['id_user'] . "'>
                                                            <button type='submit' class='btn btn-danger'>Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                    } ?>
                                </tbody>
                            </table>
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
                                    <form method="POST" action="manajemen-user.php" enctype="multipart/form-data">
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
                                            <input type="text" name="jabatan" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hak Akses</label>
                                            <input type="number" name="hak_akses" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Foto (max 5MB)</label>
                                            <input type="file" name="foto" class="form-control" accept="image/*"
                                                required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="tambahUser"
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
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>

<!-- Drag Target Area To SlideIn Menu On Small Screens -->
<div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->

<?php
require('../layouts/assetsFooter.php')
    ?>