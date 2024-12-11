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

$current_year = date('Y');
$query_kontrol = "
    (SELECT 
        'Cawu 1' as periode,
        SUM(jumlah_baik) as baik,
        SUM(jumlah_rusak) as rusak,
        SUM(jumlah_hilang) as hilang,
        SUM(jumlah_pindah) as pindah
    FROM kontrol_barang_cawu_satu 
    WHERE YEAR(tanggal_kontrol) = $current_year)
    UNION ALL
    (SELECT 
        'Cawu 2' as periode,
        SUM(jumlah_baik) as baik,
        SUM(jumlah_rusak) as rusak,
        SUM(jumlah_hilang) as hilang,
        SUM(jumlah_pindah) as pindah
    FROM kontrol_barang_cawu_dua
    WHERE YEAR(tanggal_kontrol) = $current_year)
    UNION ALL
    (SELECT 
        'Cawu 3' as periode,
        SUM(jumlah_baik) as baik,
        SUM(jumlah_rusak) as rusak,
        SUM(jumlah_hilang) as hilang,
        SUM(jumlah_pindah) as pindah
    FROM kontrol_barang_cawu_tiga
    WHERE YEAR(tanggal_kontrol) = $current_year)
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
                    <class="row row-cols-1 row-cols-md-3 g-6 mb-12">
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

                        <!-- Card Perpindahan -->
                        <div class="col-sm-6 col-lg-3">
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

                        <!-- Card Kehilangan -->
                        <div class="col-sm-6 col-lg-3">
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

                        <div class="col-lg-12">
                            <div class="card mb-6">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="card-title m-0">
                                        <h5 class="mb-1">Laporan Inventaris</h5>
                                        <p class="card-subtitle">Ringkasan Status Inventaris
                                            <?php echo $current_year; ?>
                                        </p>
                                    </div>
                                    <div class="dropdown">
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
                                    </div>
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

                        <div class="content-backdrop fade"></div>
                </div>
                <?php
                require('../layouts/footer.php');
                ?>
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
                    borderRadius: 8,
                    columnWidth: '45%',
                    distributed: true,
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val || '0';
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

        // Grafik Kondisi Baik
        const chartBaik = new ApexCharts(document.querySelector("#chartKondisiBaik"), {
            ...commonOptions,
            series: [{
                name: 'Barang Baik',
                data: formatDataForChart(dataKontrol, 'baik')
            }],
            colors: ['#28C76F', '#1CAB5E', '#119D4D'], // Gradasi warna hijau
        });

        // Grafik Kondisi Rusak
        const chartRusak = new ApexCharts(document.querySelector("#chartKondisiRusak"), {
            ...commonOptions,
            series: [{
                name: 'Barang Rusak',
                data: formatDataForChart(dataKontrol, 'rusak')
            }],
            colors: ['#FF9F43', '#FF8619', '#FF6B00'], // Gradasi warna oranye
        });

        // Grafik Kondisi Hilang
        const chartHilang = new ApexCharts(document.querySelector("#chartKondisiHilang"), {
            ...commonOptions,
            series: [{
                name: 'Barang Hilang',
                data: formatDataForChart(dataKontrol, 'hilang')
            }],
            colors: ['#EA5455', '#E42728', '#C81E1F'], // Gradasi warna merah
        });

        // Grafik Kondisi Pindah
        const chartPindah = new ApexCharts(document.querySelector("#chartKondisiPindah"), {
            ...commonOptions,
            series: [{
                name: 'Barang Pindah',
                data: formatDataForChart(dataKontrol, 'pindah')
            }],
            colors: ['#7367F0', '#5E52EC', '#483BE8'], // Gradasi warna ungu
        });

        // Render semua grafik
        chartBaik.render();
        chartRusak.render();
        chartHilang.render();
        chartPindah.render();
    }

    // Panggil fungsi saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
    });
    </script>