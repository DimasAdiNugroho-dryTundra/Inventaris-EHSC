<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <?php require('logo.php'); ?>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <?php if ($jabatan === 'operator' || $jabatan === 'staff'): ?>
        <li class="menu-header small">
            <span class="menu-header-text">Data Master</span>
        </li>
        <li class="menu-item <?php echo ($current_page == 'manajemenUser.php') ? 'active' : ''; ?>">
            <a href="manajemenUser.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                Manajemen User
            </a>
        </li>
        <li class="menu-item <?php echo ($current_page == 'departemen.php') ? 'active' : ''; ?>">
            <a href="departemen.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building"></i>
                Departemen
            </a>
        </li>
        <li class="menu-item <?php echo ($current_page == 'kategori.php') ? 'active' : ''; ?>">
            <a href="kategori.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-tag"></i>
                Kategori
            </a>
        </li>
        <li class="menu-item <?php echo ($current_page == 'ruangan.php') ? 'active' : ''; ?>">
            <a href="ruangan.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-door"></i>
                Ruangan
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'staff' || $jabatan === 'administrasi' || $jabatan === 'petugas kontrol'): ?>
        <li class="menu-header small">
            <span class="menu-header-text">Data Barang</span>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'staff' || $jabatan === 'administrasi'): ?>
        <li class="menu-item <?php echo ($current_page == 'permintaanBarang.php') ? 'active' : ''; ?>">
            <a href="permintaanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-pencil-plus"></i>
                Permintaan Barang
            </a>
        </li>
        <li class="menu-item <?php echo ($current_page == 'penerimaanBarang.php') ? 'active' : ''; ?>">
            <a href="penerimaanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-basket-check"></i>
                Penerimaan Barang
            </a>
        </li>
        <li class="menu-item <?php echo ($current_page == 'inventaris.php') ? 'active' : ''; ?>">
            <a href="inventaris.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-archive"></i>
                Inventaris
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'petugas kontrol'): ?>
        <li class="menu-item <?php echo ($current_page == 'pilihCawuKontrolBarang.php') ? 'active' : ''; ?>">
            <a href="pilihCawuKontrolBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-checkbox"></i>
                Kontrol Barang
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'staff' || $jabatan === 'administrasi' || $jabatan === 'petugas kontrol'): ?>
        <li class="menu-item <?php echo ($current_page == 'hasilKontrolBarang.php') ? 'active' : ''; ?>">
            <a href="hasilKontrolBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                Hasil Kontrol Barang
            </a>
        </li>
        <?php endif; ?>

        <?php if ($jabatan === 'operator' || $jabatan === 'staff' || $jabatan === 'administrasi'): ?>
        <li class="menu-item <?php echo ($current_page == 'kerusakanBarang.php') ? 'active' : ''; ?>">
            <a href="kerusakanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-heart-broken"></i>
                Kerusakan Barang
            </a>
        </li>
        <li class="menu-item <?php echo ($current_page == 'perpindahanBarang.php') ? 'active' : ''; ?>">
            <a href="perpindahanBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-replace"></i>
                Perpindahan Barang
            </a>
        </li>
        <li class="menu-item <?php echo ($current_page == 'kehilanganBarang.php') ? 'active' : ''; ?>">
            <a href="kehilanganBarang.php" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-off"></i>
                Kehilangan Barang
            </a>
        </li>
        <?php endif; ?>
    </ul>
</aside>