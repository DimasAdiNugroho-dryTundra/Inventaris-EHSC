<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../server/crudDepartemen.php'); 
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
                            <h2 class="mb-1">Pilih Caturwulan</h2>
                        </div>
                        <div class="mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Caturwulan</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="row row-cols-1 row-cols-md-3 g-6 mb-12">
                        <div class="col">
                            <div class="card h-100 text-center">
                                <img class="card-img-top" src="../upload/user/1.png" alt="Card image cap" />
                                <div class="card-body">
                                    <h5 class="card-title">Caturwulan Ke-1</h5>
                                    <p class="card-text">
                                        This is a longer card with supporting text below as a natural lead-in to
                                        additional content.
                                        This content is a little bit longer.
                                    </p>
                                    <a href="kontrolBarangCawuSatu.php" class="btn btn-primary">Pilih</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100 text-center">
                                <img class="card-img-top" src="../upload/user/1.png" alt="Card image cap" />
                                <div class="card-body">
                                    <h5 class="card-title">Caturwulan Ke-2</h5>
                                    <p class="card-text">
                                        This is a longer card with supporting text below as a natural lead-in to
                                        additional content.
                                        This content is a little bit longer.
                                    </p>
                                    <a href="kontrolBarangCawuDua.php" class="btn btn-primary">Pilih</a>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100 text-center">
                                <img class="card-img-top" src="../upload/user/1.png" alt="Card image cap" />
                                <div class="card-body">
                                    <h5 class="card-title">Caturwulan Ke-3</h5>
                                    <p class="card-text">
                                        This is a longer card with supporting text below as a natural lead-in to
                                        additional content.
                                    </p>
                                    <a href="kontrolBarangCawuTiga.php" class="btn btn-primary">Pilih</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php
            require('../layouts/footer.php');
            ?>
            </div>
            <!-- Footer -->

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