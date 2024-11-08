<?php
// File: detail_inventaris.php

require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');

// Get inventory ID from URL
$id_inventaris = $_GET['id'] ?? '';

// Query untuk mengambil data inventaris dan barang yang terkait
$query = "SELECT 
        i.jumlah AS jumlah_awal,
        i.kode_inventaris,
        i.satuan,
        d.nama_departemen, 
        k.nama_kategori,
        CASE 
            WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
            ELSE i.nama_barang 
        END as nama_barang,
        COALESCE((SELECT SUM(kb.jumlah) 
                   FROM kontrol_barang kb 
                   WHERE kb.id_inventaris = i.id_inventaris AND kb.status = 'baik'), 0) AS jumlah_baik,
        COALESCE((SELECT SUM(kb.jumlah) 
                   FROM kontrol_barang kb 
                   WHERE kb.id_inventaris = i.id_inventaris AND kb.status = 'rusak'), 0) AS total_rusak,
        COALESCE((SELECT SUM(kb.jumlah) 
                   FROM kontrol_barang kb 
                   WHERE kb.id_inventaris = i.id_inventaris AND kb.status = 'hilang'), 0) AS total_hilang,
        COALESCE((SELECT SUM(kb.jumlah) 
                   FROM kontrol_barang kb 
                   WHERE kb.id_inventaris = i.id_inventaris AND kb.status = 'pindah'), 0) AS total_pindah
    FROM inventaris i
    JOIN departemen d ON i.id_departemen = d.id_departemen
    JOIN kategori k ON i.id_kategori = k.id_kategori
    LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
    WHERE i.id_inventaris = '$id_inventaris'";

$result = mysqli_query($conn, $query);
$inventaris = mysqli_fetch_assoc($result);

// Cek apakah hasil query valid
if (!$inventaris) {
    echo "Data inventaris tidak ditemukan.";
    exit;
}

// Hitung total kontrol
$total_kontrol = $inventaris['jumlah_baik'] + $inventaris['total_rusak'] + $inventaris['total_hilang'] + $inventaris['total_pindah'];
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Detail Inventaris</h5>
                                    <button onclick="window.history.back()" class="btn btn-secondary btn-sm">
                                        <i class="ti ti-arrow-left"></i> Kembali
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Informasi Utama -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="200">Kode Inventaris</th>
                                                    <td>:
                                                        <?php echo $inventaris['kode_inventaris'] ?? 'Tidak tersedia'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Nama Barang</th>
                                                    <td>: <?php echo $inventaris['nama_barang']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Departemen</th>
                                                    <td>: <?php echo $inventaris['nama_departemen']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Kategori</th>
                                                    <td>: <?php echo $inventaris['nama_kategori']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Jumlah Awal</th>
                                                    <td>:
                                                        <?php echo $inventaris['jumlah_awal'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Tabel Status Barang -->
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">Status Barang</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr class="text-center">
                                                            <th>Barang Baik</th>
                                                            <th>Barang Rusak</th>
                                                            <th>Barang Hilang</th>
                                                            <th>Barang Pindah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="text-center">
                                                            <td class="text-success">
                                                                <?php echo $inventaris['jumlah_baik'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                            </td>
                                                            <td class="text-danger">
                                                                <?php echo $inventaris['total_rusak'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                            </td>
                                                            <td class="text-warning">
                                                                <?php echo $inventaris['total_hilang'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                            </td>
                                                            <td class="text-info">
                                                                <?php echo $inventaris['total_pindah'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Keterangan Kontrol -->
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">Keterangan Kontrol</h6>
                                            <div class="alert 
                                                <?php
                                                if ($total_kontrol == 0) {
                                                    echo 'alert-danger">Tidak ada barang yang dikontrol.';
                                                } elseif ($total_kontrol < $inventaris['jumlah_awal']) {
                                                    echo 'alert-warning">Sebagian barang telah dikontrol.';
                                                } else {
                                                    echo 'alert-success">Semua barang telah dikontrol.';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Jumlah Terkontrol -->
                                    <div class=" row">
                                                <div class="col-12">
                                                    <h6 class="mb-3">Jumlah Terkontrol</h6>
                                                    <p>
                                                        <?php echo 'Jumlah Terkontrol: ' . $total_kontrol . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php require('../layouts/footer.php'); ?>
                                </div>
                            </div>
                        </div>
                        <?php require('../layouts/assetsFooter.php'); ?>
                    </div>