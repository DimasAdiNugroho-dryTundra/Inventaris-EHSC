<?php
// File: detail_inventaris.php

require('../server/sessionHandler.php');
require('../server/configDB.php');
require('../layouts/header.php');

// Get inventory ID from URL
$id_inventaris = $_GET['id'] ?? '';

// Fetch inventory data
$query = "SELECT i.*, d.nama_departemen, k.nama_kategori, 
          CASE 
              WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
              ELSE i.nama_barang 
          END as nama_barang
          FROM inventaris i
          JOIN departemen d ON i.id_departemen = d.id_departemen
          JOIN kategori k ON i.id_kategori = k.id_kategori
          LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
          WHERE i.id_inventaris = '$id_inventaris'";

$result = mysqli_query($conn, $query);
$inventaris = mysqli_fetch_assoc($result);
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
                                    <div class="row">
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
                                                <tr>
                                                    <th>Jumlah</th>
                                                    <td>:
                                                        <?php echo $inventaris['jumlah'] . ' ' . $inventaris['satuan']; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <div class="mb-4">
                                                <h6>QR Code</h6>
                                                <?php
                                                // Generate QR Code dari kode_inventaris
                                                echo '<img src="../server/generateQRCode.php?id=' . $inventaris['kode_inventaris'] . '" alt="QR Code" class="img-fluid" style="max-width: 200px;">';
                                                ?>
                                                <div class="mt-2">
                                                    <small
                                                        class="text-muted"><?php echo htmlspecialchars($inventaris['kode_inventaris']); ?></small>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="../server/printQRCode.php?id=<?php echo $id_inventaris; ?>"
                                                        class="btn btn-primary">
                                                        <i class="ti ti-printer"></i> Cetak QR Code
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Form Scan QR Code -->
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="mb-0">Scan QR Code</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <form id="qrScanForm" enctype="multipart/form-data">
                                                            <div class="mb-3">
                                                                <label for="qr_image" class="form-label">Upload QR Code
                                                                    Image</label>
                                                                <input type="file" class="form-control" id="qr_image"
                                                                    name="qr_image" accept="image/*">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="ti ti-scan"></i> Scan QR Code
                                                            </button>
                                                        </form>
                                                        <div id="scanResult" class="mt-3"></div>
                                                    </div>
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
    </div>

    <?php require('../layouts/assetsFooter.php'); ?>