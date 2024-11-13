<?php
require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');


// Ambil tahun dan cawu dari POST
$cawu = isset($_POST['cawu']) ? intval($_POST['cawu']) : 1;
$year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

$currentYear = date('Y');
$years = range($currentYear - 5, $currentYear + 5); // Tahun dari 5 tahun lalu hingga 5 tahun ke depan

// Tentukan nama file berdasarkan cawu yang dipilih
$crudFile = '';
if ($cawu == 1) {
    $crudFile = 'crudKontrolBarangCawuSatu.php';
} elseif ($cawu == 2) {
    $crudFile = 'crudKontrolBarangCawuDua.php';
} elseif ($cawu == 3) {
    $crudFile = 'crudKontrolBarangCawuTiga.php';
}

// Tentukan nama field ID kontrol barang berdasarkan cawu yang dipilih
$cawuIdField = '';
if ($cawu == 1) {
    $cawuIdField = 'id_kontrol_barang_cawu_satu';
} elseif ($cawu == 2) {
    $cawuIdField = 'id_kontrol_barang_cawu_dua';
} elseif ($cawu == 3) {
    $cawuIdField = 'id_kontrol_barang_cawu_tiga';
}

// Include file CRUD yang sesuai
require("../server/$crudFile");

?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- Header -->
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


                    <!-- Alert Messages -->
                    <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                            <i class="ti ti-ban"></i>
                        </span>
                        <?php echo $_SESSION['error_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                        <span class="alert-icon rounded">
                            <i class="ti ti-check"></i>
                        </span>
                        <?php echo $_SESSION['success_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); endif; ?>

                    <div class="card">
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge bg-label-primary p-2 me-2">
                                    <i class="ti ti-user"></i>
                                </div>
                                <h5 class="mb-0">Petugas: <?php echo $_SESSION['nama']; ?></h5>
                            </div>
                        </div>
                        <!-- Dropdown Cawu dan Tahun -->
                        <div class="row p-3">
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="ti ti-info-circle me-2"></i>
                                    Untuk mengatur data kontrol, silahkan memilih cawu dan tahun terlebih dahulu.
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label for="cawu" class="form-label">Cawu</label>
                                            <select id="cawu" name="cawu" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="1" <?php if ($cawu == 1) echo 'selected'; ?>>Cawu 1
                                                </option>
                                                <option value="2" <?php if ($cawu == 2) echo 'selected'; ?>>Cawu 2
                                                </option>
                                                <option value="3" <?php if ($cawu == 3) echo 'selected'; ?>>Cawu 3
                                                </option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="year" value="<?php echo $year; ?>">
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label for="year" class="form-label">Tahun</label>
                                            <select id="year" name="year" class="form-select"
                                                onchange="this.form.submit()">
                                                <?php foreach ($years as $yr): ?>
                                                <option value="<?php echo $yr; ?>"
                                                    <?php if ($year == $yr) echo 'selected'; ?>>
                                                    <?php echo $yr; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="cawu" value="<?php echo $cawu; ?>">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php require('tampilKontrolBarang.php'); ?>
                        <!-- akhir -->
                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php require('../layouts/assetsFooter.php') ?>