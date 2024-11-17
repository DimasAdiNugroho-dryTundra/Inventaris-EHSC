<?php
require('../server/sessionHandler.php');
require('../layouts/header.php');
?>

<body>
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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h1>halo</h1>
                                <p>Jabatan: <?php echo $_SESSION['jabatan']; ?></p>
                            </div>
                        </div>
                        <div class="row row-cols-1 row-cols-md-3 g-6 mb-12">
                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1 me-2">86%</h5>
                                            <p class="mb-0">CPU Usage</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-primary rounded p-2">
                                                <i class="ti ti-cpu ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1 me-2">1.24gb</h5>
                                            <p class="mb-0">Memory Usage</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-success rounded p-2">
                                                <i class="ti ti-server ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1 me-2">0.2%</h5>
                                            <p class="mb-0">Downtime Ratio</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-danger rounded p-2">
                                                <i class="ti ti-chart-pie-2 ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1 me-2">128</h5>
                                            <p class="mb-0">Issues Found</p>
                                        </div>
                                        <div class="card-icon">
                                            <span class="badge bg-label-warning rounded p-2">
                                                <i class="ti ti-alert-octagon ti-26px"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <div class="card-title m-0">
                                            <h5 class="mb-1">Earning Reports</h5>
                                            <p class="card-subtitle">Yearly Earnings Overview</p>
                                        </div>
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1"
                                                type="button" id="earningReportsTabsId" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="earningReportsTabsId">
                                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-tabs widget-nav-tabs pb-8 gap-4 mx-1 d-flex flex-nowrap"
                                            role="tablist">
                                            <li class="nav-item">
                                                <a href="javascript:void(0);"
                                                    class="nav-link btn active d-flex flex-column align-items-center justify-content-center"
                                                    role="tab" data-bs-toggle="tab" data-bs-target="#navs-orders-id"
                                                    aria-controls="navs-orders-id" aria-selected="true">
                                                    <div class="badge bg-label-secondary rounded p-2">
                                                        <i class="ti ti-shopping-cart ti-md"></i>
                                                    </div>
                                                    <h6 class="tab-widget-title mb-0 mt-2">Orders</h6>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0);"
                                                    class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                                    role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-id"
                                                    aria-controls="navs-sales-id" aria-selected="false">
                                                    <div class="badge bg-label-secondary rounded p-2">
                                                        <i class="ti ti-chart-bar ti-md"></i>
                                                    </div>
                                                    <h6 class="tab-widget-title mb-0 mt-2">Sales</h6>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0);"
                                                    class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                                    role="tab" data-bs-toggle="tab" data-bs-target="#navs-profit-id"
                                                    aria-controls="navs-profit-id" aria-selected="false">
                                                    <div class="badge bg-label-secondary rounded p-2">
                                                        <i class="ti ti-currency-dollar ti-md"></i>
                                                    </div>
                                                    <h6 class="tab-widget-title mb-0 mt-2">Profit</h6>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0);"
                                                    class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                                    role="tab" data-bs-toggle="tab" data-bs-target="#navs-income-id"
                                                    aria-controls="navs-income-id" aria-selected="false">
                                                    <div class="badge bg-label-secondary rounded p-2">
                                                        <i class="ti ti-chart-pie-2 ti-md"></i>
                                                    </div>
                                                    <h6 class="tab-widget-title mb-0 mt-2">Income</h6>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0);"
                                                    class="nav-link btn d-flex align-items-center justify-content-center disabled"
                                                    role="tab" data-bs-toggle="tab" aria-selected="false">
                                                    <div class="badge bg-label-secondary rounded p-2"><i
                                                            class="ti ti-plus ti-md"></i></div>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content p-0 ms-0 ms-sm-2">
                                            <div class="tab-pane fade show active" id="navs-orders-id" role="tabpanel">
                                                <div id="earningReportsTabsOrders"></div>
                                            </div>
                                            <div class="tab-pane fade" id="navs-sales-id" role="tabpanel">
                                                <div id="earningReportsTabsSales"></div>
                                            </div>
                                            <div class="tab-pane fade" id="navs-profit-id" role="tabpanel">
                                                <div id="earningReportsTabsProfit"></div>
                                            </div>
                                            <div class="tab-pane fade" id="navs-income-id" role="tabpanel">
                                                <div id="earningReportsTabsIncome"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php
                    require('../layouts/footer.php');
                    ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
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
</body>

</html>