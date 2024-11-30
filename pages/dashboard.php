<?php
require('../server/sessionHandler.php');
require('../layouts/header.php');

// Ambil data pengguna berdasarkan session
$queryUser = "SELECT * FROM user WHERE id_user = '$id_user'";
$resultUser = mysqli_query($conn, $queryUser);
$user = mysqli_fetch_assoc($resultUser);

// Query untuk menghitung jumlah inventaris yang tersedia
$queryInventarisTersedia = "SELECT COUNT(*) as total_inventaris FROM inventaris WHERE jumlah_akhir > 0";
$resultInventarisTersedia = mysqli_query($conn, $queryInventarisTersedia);
$rowInventarisTersedia = mysqli_fetch_assoc($resultInventarisTersedia);
$totalInventarisTersedia = $rowInventarisTersedia['total_inventaris'];

// Query untuk menghitung jumlah kerusakan barang
$queryKerusakan = "SELECT COUNT(*) as total_kerusakan FROM kerusakan_barang";
$resultKerusakan = mysqli_query($conn, $queryKerusakan);
$rowKerusakan = mysqli_fetch_assoc($resultKerusakan);
$totalKerusakan = $rowKerusakan['total_kerusakan'];

// Query untuk menghitung jumlah perpindahan barang
$queryPerpindahan = "SELECT COUNT(*) as total_perpindahan FROM perpindahan_barang";
$resultPerpindahan = mysqli_query($conn, $queryPerpindahan);
$rowPerpindahan = mysqli_fetch_assoc($resultPerpindahan);
$totalPerpindahan = $rowPerpindahan['total_perpindahan'];

// Query untuk menghitung jumlah kehilangan barang
$queryKehilangan = "SELECT COUNT(*) as total_kehilangan FROM kehilangan_barang";
$resultKehilangan = mysqli_query($conn, $queryKehilangan);
$rowKehilangan = mysqli_fetch_assoc($resultKehilangan);
$totalKehilangan = $rowKehilangan['total_kehilangan'];
?>


<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php
            require('../layouts/sidePanel.php');
            ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <?php require('../layouts/navbar.php'); ?>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row row-cols-1 row-cols-md-3 g-6 mb-12">
                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1"><?php echo $totalInventarisTersedia; ?></h5>
                                            <p class="mb-0">Inventaris Tersedia</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-success rounded p-2">
                                                <i class="ti ti-box ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1"><?php echo $totalKerusakan; ?></h5>
                                            <p class="mb-0">Kerusakan Barang</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-danger rounded p-2">
                                                <i class="ti ti-alert-octagon ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1"><?php echo $totalPerpindahan; ?></h5>
                                            <p class="mb-0">Perpindahan Barang</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-warning rounded p-2">
                                                <i class="ti ti-arrows-exchange ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1"><?php echo $totalKehilangan; ?></h5>
                                            <p class="mb-0">Kehilangan Barang</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-danger rounded p-2">
                                                <i class="ti ti-alert-circle ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Card -->
                            <div class="col-lg-12">
                                <div class="card mb-6">
                                    <div class="card-body pt-12">
                                        <div class="user-avatar-section">
                                            <div class="d-flex align-items-center flex-column">
                                                <img class="img-fluid rounded mb-4"
                                                    src="../upload/user/<?php echo ($user['foto']); ?>" height="120"
                                                    width="120" alt="User avatar" />
                                                <div class="user-info text-center">
                                                    <h5><?php echo ($user['nama']); ?></h5>
                                                    <span
                                                        class="badge bg-label-secondary"><?php echo ($user['jabatan']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h5 class="pb-4 border-bottom mb-4">Details</h5>
                                        <div class="info-container">
                                            <ul class="list-unstyled mb-6">
                                                <li class="mb-2">
                                                    <span class="h6">Username:</span>
                                                    <span><?php echo ($user['username']); ?></span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="h6">Email:</span>
                                                    <span><?php echo ($user['email']); ?></span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="h6">Status:</span>
                                                    <span><?php echo $user['hak_akses'] == 0 ? 'Aktif' : 'Non Aktif'; ?></span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="h6">Jabatan:</span>
                                                    <span><?php echo ($user['jabatan']); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- / Content -->

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
</body>

</html>