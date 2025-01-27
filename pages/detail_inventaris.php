<?php
require('../server/sessionHandler.php');
require_once('../server/configDB.php');
require('../layouts/header.php');

$id_inventaris = $_GET['id'] ?? '';

// Query untuk mengambil data inventaris dan barang yang terkait
$query = "SELECT 
            i.jumlah_awal,
            i.kode_inventaris,
            i.satuan,
            i.tanggal_perolehan,
            i.jumlah_akhir,
            i.sumber_inventaris,
            d.nama_departemen, 
            k.nama_kategori,
            r.nama_ruangan,
            CASE 
                WHEN pb.nama_barang IS NOT NULL THEN pb.nama_barang 
                ELSE i.nama_barang 
            END as nama_barang
        FROM inventaris i
        JOIN departemen d ON i.id_departemen = d.id_departemen
        JOIN kategori k ON i.id_kategori = k.id_kategori
        JOIN ruangan r ON i.id_ruangan = r.id_ruangan
        LEFT JOIN penerimaan_barang pb ON i.id_penerimaan = pb.id_penerimaan
        WHERE i.id_inventaris = '$id_inventaris'";  

$result = mysqli_query($conn, $query);
$inventaris = mysqli_fetch_assoc($result);

// Cek apakah hasil query valid
if (!$inventaris) {
    echo "Data inventaris tidak ditemukan.";
    exit;
}

// Query untuk mengambil data kontrol barang dari cawu 1, 2, dan 3
$cawu_queries = [
    "SELECT tahun_kontrol, SUM(jumlah_baik) AS jumlah_baik, SUM(jumlah_rusak) AS total_rusak, 
            SUM(jumlah_pindah) AS total_pindah, SUM(jumlah_hilang) AS total_hilang
     FROM kontrol_barang_cawu_satu
     WHERE id_inventaris = '$id_inventaris'
     GROUP BY tahun_kontrol",

    "SELECT tahun_kontrol, SUM(jumlah_baik) AS jumlah_baik, SUM(jumlah_rusak) AS total_rusak, 
            SUM(jumlah_pindah) AS total_pindah, SUM(jumlah_hilang) AS total_hilang
     FROM kontrol_barang_cawu_dua
     WHERE id_inventaris = '$id_inventaris'
     GROUP BY tahun_kontrol",

    "SELECT tahun_kontrol, SUM(jumlah_baik) AS jumlah_baik, SUM(jumlah_rusak) AS total_rusak, 
            SUM(jumlah_pindah) AS total_pindah, SUM(jumlah_hilang) AS total_hilang
     FROM kontrol_barang_cawu_tiga
     WHERE id_inventaris = '$id_inventaris'
     GROUP BY tahun_kontrol"
];

$cawu_data = [];
foreach ($cawu_queries as $query) {
    $result = mysqli_query($conn, $query);
    $cawu_data[] = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
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
                                                    <th>Tanggal Perolehan</th>
                                                    <td>: <?php echo $inventaris['tanggal_perolehan']; ?></td>
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
                                                    <th>Ruangan</th>
                                                    <td>: <?php echo $inventaris['nama_ruangan']; ?></td>
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
                                                <tr>
                                                    <th>Jumlah Akhir</th>
                                                    <td>:
                                                        <?php echo $inventaris['jumlah_akhir'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Sumber Inventaris</th>
                                                    <td>: <?php echo $inventaris['sumber_inventaris']; ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <!-- QR Code -->
                                            <div class="mb-4">
                                                <h6>QR Code</h6>
                                                <?php if (isset($inventaris['kode_inventaris'])): ?>
                                                <img src="../server/generateQRCode.php?id=<?php echo $inventaris['kode_inventaris']; ?>"
                                                    alt="QR Code" class="img-fluid" style="max-width: 200px;">
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
                                                <?php else: ?>
                                                <p>QR Code tidak tersedia</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabel Status Barang untuk Cawu 1 -->
                                    <h6 class="mb-3">Status Barang Cawu 1</h6>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr class="text-center">
                                                    <th>Tahun</th>
                                                    <th>Barang Baik</th>
                                                    <th>Barang Rusak</th>
                                                    <th>Barang Hilang</th>
                                                    <th>Barang Pindah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($cawu_data[0])): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data untuk Cawu 1.
                                                    </td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($cawu_data[0] as $row): ?>
                                                <tr class="text-center">
                                                    <td><?php echo $row['tahun_kontrol']; ?></td>
                                                    <td class="text-success">
                                                        <?php echo $row['jumlah_baik'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-danger">
                                                        <?php echo $row['total_rusak'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-info">
                                                        <?php echo $row['total_pindah'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-warning">
                                                        <?php echo $row['total_hilang'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Tabel Status Barang untuk Cawu 2 -->
                                    <h6 class="mb-3">Status Barang Cawu 2</h6>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr class="text-center">
                                                    <th>Tahun</th>
                                                    <th>Barang Baik</th>
                                                    <th>Barang Rusak</th>
                                                    <th>Barang Pindah</th>
                                                    <th>Barang Hilang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($cawu_data[1])): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data untuk Cawu 2.
                                                    </td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($cawu_data[1] as $row): ?>
                                                <tr class="text-center">
                                                    <td><?php echo $row['tahun_kontrol']; ?></td>
                                                    <td class="text-success">
                                                        <?php echo $row['jumlah_baik'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-danger">
                                                        <?php echo $row['total_rusak'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-info">
                                                        <?php echo $row['total_pindah'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-warning">
                                                        <?php echo $row['total_hilang'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Tabel Status Barang untuk Cawu 3 -->
                                    <h6 class="mb-3">Status Barang Cawu 3</h6>
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr class="text-center">
                                                    <th>Tahun</th>
                                                    <th>Barang Baik</th>
                                                    <th>Barang Rusak</th>
                                                    <th>Barang Pindah</th>
                                                    <th>Barang Hilang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($cawu_data[2])): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data untuk Cawu 3.
                                                    </td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($cawu_data[2] as $row): ?>
                                                <tr class="text-center">
                                                    <td><?php echo $row['tahun_kontrol']; ?></td>
                                                    <td class="text-success">
                                                        <?php echo $row['jumlah_baik'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-danger">
                                                        <?php echo $row['total_rusak'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-info">
                                                        <?php echo $row['total_pindah'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                    <td class="text-warning">
                                                        <?php echo $row['total_hilang'] . ' ' . ($inventaris['satuan'] ?? 'unit'); ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php require('../layouts/footer.php'); ?>
            </div>
            <?php require('../layouts/assetsFooter.php'); ?>
        </div>
    </div>
</div>