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

$tahun_sekarang = date('Y');
$jam = date('H');

// Query untuk menghitung jumlah kerusakan barang yang belum dilaporkan
$queryKerusakan = "SELECT 
                    (
                        COALESCE((SELECT SUM(jumlah_rusak) FROM kontrol_barang_cawu_satu WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) +
                        COALESCE((SELECT SUM(jumlah_rusak) FROM kontrol_barang_cawu_dua WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) +
                        COALESCE((SELECT SUM(jumlah_rusak) FROM kontrol_barang_cawu_tiga WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) -
                        COALESCE((SELECT SUM(jumlah_kerusakan) FROM kerusakan_barang WHERE YEAR(tanggal_kerusakan) = $tahun_sekarang), 0)
                    ) as total_belum_dilaporkan";
$resultKerusakan = mysqli_query($conn, $queryKerusakan);
$rowKerusakan = mysqli_fetch_assoc($resultKerusakan);
$totalKerusakanBelumDilaporkan = $rowKerusakan['total_belum_dilaporkan'];

// Query untuk menghitung jumlah perpindahan barang yang belum dilaporkan
$queryPerpindahan = "SELECT 
                    (
                        COALESCE((SELECT SUM(jumlah_pindah) FROM kontrol_barang_cawu_satu WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) +
                        COALESCE((SELECT SUM(jumlah_pindah) FROM kontrol_barang_cawu_dua WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) +
                        COALESCE((SELECT SUM(jumlah_pindah) FROM kontrol_barang_cawu_tiga WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) -
                        COALESCE((SELECT SUM(jumlah_perpindahan) FROM perpindahan_barang WHERE YEAR(tanggal_perpindahan) = $tahun_sekarang), 0)
                    ) as total_belum_dilaporkan";
$resultPerpindahan = mysqli_query($conn, $queryPerpindahan);
$rowPerpindahan = mysqli_fetch_assoc($resultPerpindahan);
$totalPerpindahanBelumDilaporkan = $rowPerpindahan['total_belum_dilaporkan'];

// Query untuk menghitung jumlah kehilangan barang yang belum dilaporkan
$queryKehilangan = "SELECT 
                    (
                        COALESCE((SELECT SUM(jumlah_hilang) FROM kontrol_barang_cawu_satu WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) +
                        COALESCE((SELECT SUM(jumlah_hilang) FROM kontrol_barang_cawu_dua WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) +
                        COALESCE((SELECT SUM(jumlah_hilang) FROM kontrol_barang_cawu_tiga WHERE YEAR(tanggal_kontrol) = $tahun_sekarang), 0) -
                        COALESCE((SELECT SUM(jumlah_kehilangan) FROM kehilangan_barang WHERE YEAR(tanggal_kehilangan) = $tahun_sekarang), 0)
                    ) as total_belum_dilaporkan";
$resultKehilangan = mysqli_query($conn, $queryKehilangan);
$rowKehilangan = mysqli_fetch_assoc($resultKehilangan);
$totalKehilanganBelumDilaporkan = $rowKehilangan['total_belum_dilaporkan'];

$query_kontrol = "
    (SELECT 
        'Cawu 1' as periode,
        SUM(jumlah_baik) as baik,
        SUM(jumlah_rusak) as rusak,
        SUM(jumlah_hilang) as hilang,
        SUM(jumlah_pindah) as pindah
    FROM kontrol_barang_cawu_satu 
    WHERE YEAR(tanggal_kontrol) = $tahun_sekarang)
    UNION ALL
    (SELECT 
        'Cawu 2' as periode,
        SUM(jumlah_baik) as baik,
        SUM(jumlah_rusak) as rusak,
        SUM(jumlah_hilang) as hilang,
        SUM(jumlah_pindah) as pindah
    FROM kontrol_barang_cawu_dua
    WHERE YEAR(tanggal_kontrol) = $tahun_sekarang)
    UNION ALL
    (SELECT 
        'Cawu 3' as periode,
        SUM(jumlah_baik) as baik,
        SUM(jumlah_rusak) as rusak,
        SUM(jumlah_hilang) as hilang,
        SUM(jumlah_pindah) as pindah
    FROM kontrol_barang_cawu_tiga
    WHERE YEAR(tanggal_kontrol) = $tahun_sekarang)
    ORDER BY periode
";

$result = mysqli_query($conn, $query_kontrol);
$data_kontrol = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data_kontrol[] = $row;
}
?>


<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php
        require('../layouts/sidePanel.php');
        ?>
        <div class="layout-page">
            <?php require('../layouts/navbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <!-- User Card -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="user-avatar-section text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <img class="img-fluid rounded my-4"
                                            src="../upload/user/<?php echo ($user['foto']); ?>" height="100" width="100"
                                            alt="User avatar" />
                                        <div class="user-info text-center mb-3">
                                            <h4 class="mb-2">Selamat Datang, <?php echo ($user['nama']); ?>!</h4>
                                            <span
                                                class="badge bg-label-primary"><?php echo ($user['jabatan']); ?></span>
                                        </div>
                                        <?php
                                        $ucapan = '';
                                        if ($jam >= 5 && $jam < 12) {
                                            $ucapan = "Selamat Pagi";
                                        } elseif ($jam >= 12 && $jam < 15) {
                                            $ucapan = "Selamat Siang";
                                        } elseif ($jam >= 15 && $jam < 18) {
                                            $ucapan = "Selamat Sore";
                                        } else {
                                            $ucapan = "Selamat Malam";
                                        }
                                        ?>
                                        <div class="welcome-message text-center">
                                            <p class="mb-0"><?php echo $ucapan; ?> dan selamat bekerja!</p>
                                            <!-- <p class="text-muted mt-2">
                                                    "Sistem Informasi Inventaris siap membantu Anda mengelola dan
                                                    memantau
                                                    aset dengan lebih efisien. Jika ada pertanyaan atau masalah,
                                                    jangan ragu untuk menghubungi admin."
                                                </p> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-md-3 g-6 mb-12">
                        <!-- Card Inventaris -->
                        <div class="col-sm-6 col-lg-3">
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

                        <!-- Card Kerusakan -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <div class="card-body d-flex justify-content-between align-items-center p-4">
                                    <div class="card-title mb-0">
                                        <h5 class="mb-1"><?php echo $totalKerusakanBelumDilaporkan; ?></h5>
                                        <p class="mb-0">Kerusakan Belum Dilaporkan</p>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-danger rounded p-2">
                                            <i class="ti ti-alert-octagon ti-26px"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Perpindahan -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <div class="card-body d-flex justify-content-between align-items-center p-4">
                                    <div class="card-title mb-0">
                                        <h5 class="mb-1"><?php echo $totalPerpindahanBelumDilaporkan; ?></h5>
                                        <p class="mb-0">Perpindahan Belum Dilaporkan</p>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-warning rounded p-2">
                                            <i class="ti ti-arrows-exchange ti-26px"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Kehilangan -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <div class="card-body d-flex justify-content-between align-items-center p-4">
                                    <div class="card-title mb-0">
                                        <h5 class="mb-1"><?php echo $totalKehilanganBelumDilaporkan; ?></h5>
                                        <p class="mb-0">Kehilangan Belum Dilaporkan</p>
                                    </div>
                                    <div class="card-icon">
                                        <span class="badge bg-label-danger rounded p-2">
                                            <i class="ti ti-alert-circle ti-26px"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card mb-6">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="card-title m-0">
                                        <h5 class="mb-1">Status Inventaris</h5>
                                        <p class="card-subtitle">Ringkasan Status Inventaris
                                            <?php echo $tahun_sekarang; ?>
                                        </p>
                                    </div>
                                    <!-- <div class="dropdown">
                                        <button
                                            class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1"
                                            type="button" id="inventoryReportsTabsId" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="inventoryReportsTabsId">
                                            <a class="dropdown-item" href="javascript:void(0);">Lihat Detail</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Cetak Laporan</a>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-tabs widget-nav-tabs pb-8 gap-4 mx-1 d-flex flex-nowrap"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);"
                                                class="nav-link btn active d-flex flex-column align-items-center justify-content-center"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-tersedia-id"
                                                aria-controls="navs-tersedia-id" aria-selected="true">
                                                <div class="badge bg-label-success rounded p-2">
                                                    <i class="ti ti-checkbox ti-md"></i>
                                                </div>
                                                <h6 class="tab-widget-title mb-0 mt-2">Baik</h6>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);"
                                                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-rusak-id"
                                                aria-controls="navs-rusak-id" aria-selected="false">
                                                <div class="badge bg-label-warning rounded p-2">
                                                    <i class="ti ti-alert-triangle ti-md"></i>
                                                </div>
                                                <h6 class="tab-widget-title mb-0 mt-2">Rusak</h6>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);"
                                                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-hilang-id"
                                                aria-controls="navs-hilang-id" aria-selected="false">
                                                <div class="badge bg-label-danger rounded p-2">
                                                    <i class="ti ti-question-mark ti-md"></i>
                                                </div>
                                                <h6 class="tab-widget-title mb-0 mt-2">Hilang</h6>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);"
                                                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-pindah-id"
                                                aria-controls="navs-pindah-id" aria-selected="false">
                                                <div class="badge bg-label-info rounded p-2">
                                                    <i class="ti ti-arrows-right-left ti-md"></i>
                                                </div>
                                                <h6 class="tab-widget-title mb-0 mt-2">Pindah</h6>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content p-0 ms-0 ms-sm-2">
                                        <div class="tab-pane fade show active" id="navs-tersedia-id" role="tabpanel">
                                            <div id="chartKondisiBaik"></div>
                                        </div>
                                        <div class="tab-pane fade" id="navs-rusak-id" role="tabpanel">
                                            <div id="chartKondisiRusak"></div>
                                        </div>
                                        <div class="tab-pane fade" id="navs-hilang-id" role="tabpanel">
                                            <div id="chartKondisiHilang"></div>
                                        </div>
                                        <div class="tab-pane fade" id="navs-pindah-id" role="tabpanel">
                                            <div id="chartKondisiPindah"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- Content wrapper -->
                </div>
                <?php
                require('../layouts/footer.php');
                ?>
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

        <script>
            // Konversi data PHP ke JavaScript
            const dataKontrol = <?php echo json_encode($data_kontrol); ?>;

            // Fungsi untuk memformat data untuk grafik
            function formatDataForChart(data, kondisi) {
                return data.map(item => ({
                    x: item.periode,
                    y: parseInt(item[kondisi]) || 0
                }));
            }

            // Fungsi untuk inisialisasi grafik
            function initializeCharts() {
                const commonOptions = {
                    chart: {
                        height: 350,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: [20, 20, 20, 20],
                            columnWidth: '45%',
                            distributed: true,
                        }
                    },
                    colors: ['#696cff', '#03c3ec', '#71dd37'],
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return Math.round(val) || '0';
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#697a8d"]
                        }
                    },
                    xaxis: {
                        categories: ['Cawu 1', 'Cawu 2', 'Cawu 3'],
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Barang'
                        },
                        labels: {
                            formatter: function (value) {
                                return Math.round(value);
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f0f0f0',
                        padding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0
                        }
                    }
                };

                // Warna gradasi untuk setiap batang
                const colorsBaik = ['#4caf50', '#81c784', '#a5d6a7'];
                const colorsRusak = ['#ffeb3b', '#fff176', '#fff9c4'];
                const colorsHilang = ['#f44336', '#e57373', '#ef9a9a'];
                const colorsPindah = ['#2196f3', '#64b5f6', '#bbdefb'];

                // Inisialisasi grafik untuk kondisi baik
                const chartKondisiBaik = new ApexCharts(document.querySelector("#chartKondisiBaik"), {
                    ...commonOptions,
                    colors: colorsBaik,
                    series: [{
                        name: 'Baik',
                        data: formatDataForChart(dataKontrol, 'baik')
                    }]
                });

                // Inisialisasi grafik untuk kondisi rusak
                const chartKondisiRusak = new ApexCharts(document.querySelector("#chartKondisiRusak"), {
                    ...commonOptions,
                    colors: colorsRusak,
                    series: [{
                        name: 'Rusak',
                        data: formatDataForChart(dataKontrol, 'rusak')
                    }]
                });

                // Inisialisasi grafik untuk kondisi hilang
                const chartKondisiHilang = new ApexCharts(document.querySelector("#chartKondisiHilang"), {
                    ...commonOptions,
                    colors: colorsHilang,
                    series: [{
                        name: 'Hilang',
                        data: formatDataForChart(dataKontrol, 'hilang')
                    }]
                });

                // Inisialisasi grafik untuk kondisi pindah
                const chartKondisiPindah = new ApexCharts(document.querySelector("#chartKondisiPindah"), {
                    ...commonOptions,
                    colors: colorsPindah,
                    series: [{
                        name: 'Pindah',
                        data: formatDataForChart(dataKontrol, 'pindah')
                    }]
                });

                // Render semua grafik
                chartKondisiBaik.render();
                chartKondisiRusak.render();
                chartKondisiHilang.render();
                chartKondisiPindah.render();
            }

            // Panggil fungsi saat dokumen siap
            document.addEventListener('DOMContentLoaded', function () {
                initializeCharts();
            });
        </script>