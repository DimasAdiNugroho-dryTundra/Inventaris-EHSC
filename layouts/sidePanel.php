<?php
$jabatan = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : '';
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <?php require('logo.php'); ?>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <?php if ($jabatan === 'operator'): ?>
        <li class="menu-header small">
            <span class="menu-header-text">Data Master</span>
        </li>
        <li class="menu-item">
            <a href="manajemen-user.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                Manajemen User
            </a>
        </li>
        <li class="menu-item">
            <a href="departemen.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building-skyscraper"></i>
                Departemen
            </a>
        </li>
        <li class="menu-item">
            <a href="kategori.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-category"></i>
                Kategori
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'administrasi' || $jabatan === 'petugas kontrol'): ?>
        <li class="menu-header small">
            <span class="menu-header-text">Data Barang</span>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
        <li class="menu-item">
            <a href="permintaanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-hand-grab"></i>
                Permintaan Barang
            </a>
        </li>
        <li class="menu-item">
            <a href="penerimaanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-align-bottom-left"></i>
                Penerimaan Barang
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'administrasi' || $jabatan === 'petugas kontrol'): ?>
        <li class="menu-item">
            <a href="inventaris.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-align-bottom-left"></i>
                Inventaris
            </a>
        </li>
        <li class="menu-item">
            <a href="rangkumanInventaris.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-align-bottom-left"></i>
                Rangkuman Inventaris
            </a>
        </li>
        <li class="menu-item">
            <a href="pilihCawuKontrolBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-align-bottom-left"></i>
                Kontrol Barang
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'administrasi'): ?>
        <li class="menu-item">
            <a href="kerusakanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-align-bottom-left"></i>
                Kerusakan Barang
            </a>
        </li>
        <li class="menu-item">
            <a href="perpindahanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-align-bottom-left"></i>
                Perpindahan Barang
            </a>
        </li>
        <li class="menu-item">
            <a href="kehilanganBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-align-bottom-left"></i>
                Kehilangan Barang
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'administrasi' || $jabatan === 'petugas kontrol'): ?>
        <li class="menu-item">
            <a class="btn d-block gap-2 col-lg-10 mx-auto btn-danger waves-effect waves-light"
                href="../server/sessionDestroy.php">
                Keluar
                <span class="ti-xs ti ti-logout"></span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</aside>