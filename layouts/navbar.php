<?php
require_once('../server/configDB.php');

$query = mysqli_query($conn, "SELECT nama, jabatan, foto FROM user WHERE id_user='$id_user'");
$user = mysqli_fetch_array($query);

// Set default image if no photo is available
$photo_path = "../upload/user/" . $user['foto'];
if (!file_exists($photo_path) || empty($user['foto'])) {
    $photo_path = "../upload/user/default.png";
}
?>
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-xxl">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="ti ti-menu-2 ti-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">

                <span class="ms-2 m-1"><?php echo $user['nama']; ?></span>

                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="<?php echo $photo_path; ?>" alt class="rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="#">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img src="<?php echo $photo_path; ?>" alt="user photo"
                                                class="rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?php echo $user['nama']; ?></h6>
                                        <small class="text-muted"><?php echo $user['jabatan']; ?></small>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
</nav>