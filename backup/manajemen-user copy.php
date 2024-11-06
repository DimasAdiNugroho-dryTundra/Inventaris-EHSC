<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');

// Fetch data user untuk ditampilkan di tabel
$query = "SELECT * FROM user";
$result = mysqli_query($conn, $query);

// Proses penambahan user
if (isset($_POST['tambahUser'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $email = $_POST['email'];
    $jabatan = $_POST['jabatan'];
    $foto = ''; // asumsikan belum ada upload file
    $hak_akses = $_POST['hak_akses'];

    $query = "INSERT INTO user (nama, username, password, email, jabatan, foto, hak_akses) 
              VALUES ('$nama', '$username', '$password', '$email', '$jabatan', '$foto', '$hak_akses')";
    mysqli_query($conn, $query);
    header("Location: manajemen-user.php");
}

// Proses pengeditan user
if (isset($_POST['editUser'])) {
    $id_user = $_POST['id_user'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $jabatan = $_POST['jabatan'];
    $hak_akses = $_POST['hak_akses'];

    $query = "UPDATE user SET nama='$nama', username='$username', email='$email', jabatan='$jabatan', hak_akses='$hak_akses' WHERE id_user='$id_user'";
    mysqli_query($conn, $query);
    header("Location: manajemen-user.php");
}

// Proses penghapusan user
if (isset($_GET['delete'])) {
    $id_user = $_GET['delete'];
    $query = "DELETE FROM user WHERE id_user='$id_user'";
    mysqli_query($conn, $query);
    header("Location: manajemen-user.php");
}
?>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php require('../layouts/sidePanel.php'); ?>

            <div class="layout-page">
                <?php require('../layouts/navbar.php'); ?>

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Manajemen User</h3>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#tambahUserModal">Tambah User</button>
                        </div>

                        <div class="card">
                            <h5 class="card-header">Data User</h5>
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Jabatan</th>
                                            <th>Hak Akses</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td><?php echo $row['nama']; ?></td>
                                                <td><?php echo $row['username']; ?></td>
                                                <td><?php echo $row['email']; ?></td>
                                                <td><?php echo $row['jabatan']; ?></td>
                                                <td><?php echo $row['hak_akses']; ?></td>
                                                <td>
                                                    <a href="manajemen-user.php?edit=<?php echo $row['id_user']; ?>"
                                                        class="btn btn-sm btn-warning">Edit</a>
                                                    <a href="manajemen-user.php?delete=<?php echo $row['id_user']; ?>"
                                                        class="btn btn-sm btn-danger">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php
                        // Jika tombol Edit diklik, ambil data user berdasarkan ID dan isi ke modal
                        if (isset($_GET['edit'])) {
                            $id_user = $_GET['edit'];
                            $query = "SELECT * FROM user WHERE id_user='$id_user'";
                            $result = mysqli_query($conn, $query);
                            $user = mysqli_fetch_assoc($result);
                            ?>
                            <!-- Modal Edit User -->
                            <div class="modal fade" id="editUserModal" tabindex="-1" aria-modal="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="manajemen-user.php">
                                                <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text" name="nama" class="form-control"
                                                        value="<?php echo $user['nama']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" name="username" class="form-control"
                                                        value="<?php echo $user['username']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="<?php echo $user['email']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jabatan</label>
                                                    <input type="text" name="jabatan" class="form-control"
                                                        value="<?php echo $user['jabatan']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Hak Akses</label>
                                                    <input type="number" name="hak_akses" class="form-control"
                                                        value="<?php echo $user['hak_akses']; ?>" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="editUser"
                                                        class="btn btn-primary">Simpan</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

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
                                        <form method="POST" action="manajemen-user.php">
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
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->