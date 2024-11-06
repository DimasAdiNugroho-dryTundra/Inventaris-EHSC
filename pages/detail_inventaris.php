<?php
// File: detail_inventaris.php

require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');

// Get inventory ID from URL
$id_inventaris = $_GET['id'] ?? '';

// Query untuk mengambil data inventaris dan barang yang terkait
$query = "SELECT 
            i.*, 
            d.nama_departemen, 
            k.nama_kategori,
            CASE 
                WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
                ELSE i.nama_barang 
            END as nama_barang,
            -- Hitung jumlah barang rusak
            (SELECT COALESCE(SUM(kr.jumlah), 0)
             FROM kerusakan_barang kr
             JOIN kontrol_barang kb ON kr.id_kontrol_barang = kb.id_kontrol_barang
             WHERE kb.id_invetaris = i.id_inventaris) as total_rusak,
            -- Hitung jumlah barang hilang
            (SELECT COALESCE(SUM(kh.jumlah), 0)
             FROM kehilangan_barang kh
             JOIN kontrol_barang kb ON kh.id_kontrol_barang = kb.id_kontrol_barang
             WHERE kb.id_invetaris = i.id_inventaris) as total_hilang,
            -- Hitung jumlah barang pindah
            (SELECT COALESCE(SUM(pp.jumlah), 0)
             FROM perpindahan_barang pp
             JOIN kontrol_barang kb ON pp.id_kontrol_barang = kb.id_kontrol_barang
             WHERE kb.id_invetaris = i.id_inventaris) as total_pindah
          FROM inventaris i
          JOIN departemen d ON i.id_departemen = d.id_departemen
          JOIN kategori k ON i.id_kategori = k.id_kategori
          LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
          WHERE i.id_inventaris = '$id_inventaris'";

$result = mysqli_query($conn, $query);
$inventaris = mysqli_fetch_assoc($result);

// Hitung jumlah barang baik
$total_tidak_baik = $inventaris['total_rusak'] + $inventaris['total_hilang'] + $inventaris['total_pindah'];
$jumlah_baik = $inventaris['jumlah'] - $total_tidak_baik;
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php require('../layouts/sidePanel.php'); ?>

        <div class="layout-page">
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
                                                    <td>: <?php echo $inventaris['kode_inventaris']; ?></td>
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
                                            </table>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <!-- QR Code section tetap sama -->
                                            <div class="mb-4">
                                                <h6>QR Code</h6>
                                                <?php
                                                echo '<img src="../server/generateQRCode.php?id=' . $inventaris['kode_inventaris'] . '" alt="QR Code" class="img-fluid" style="max-width: 200px;">';
                                                ?>
                                                <div class="mt-2">
                                                    <small
                                                        class="text-muted"><?php echo htmlspecialchars($inventaris['kode_inventaris']); ?></small>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="../report/printQRCode.php?id=<?php echo $id_inventaris; ?>"
                                                        class="btn btn-primary">
                                                        <i class="ti ti-printer"></i> Cetak QR Code
                                                    </a>
                                                </div>
                                            </div>
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
                                                            <th>Total Barang</th>
                                                            <th>Barang Baik</th>
                                                            <th>Barang Rusak</th>
                                                            <th>Barang Hilang</th>
                                                            <th>Barang Pindah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="text-center">
                                                            <td><?php echo $inventaris['jumlah'] . ' ' . $inventaris['satuan']; ?>
                                                            </td>
                                                            <td class="text-success">
                                                                <?php echo $jumlah_baik . ' ' . $inventaris['satuan']; ?>
                                                            </td>
                                                            <td class="text-danger">
                                                                <?php echo $inventaris['total_rusak'] . ' ' . $inventaris['satuan']; ?>
                                                            </td>
                                                            <td class="text-warning">
                                                                <?php echo $inventaris['total_hilang'] . ' ' . $inventaris['satuan']; ?>
                                                            </td>
                                                            <td class="text-info">
                                                                <?php echo $inventaris['total_pindah'] . ' ' . $inventaris['satuan']; ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php require('../layouts/footer.php'); ?>
            </div>
        </div>
    </div>

    <?php require('../layouts/assetsFooter.php'); ?>