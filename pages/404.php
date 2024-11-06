<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="stylesheet" href="assets/vendor/css/core.css">
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css">
    <style>
        .misc-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2" style="font-size: 6rem;">404</h1>
            <h2 class="mb-4">Halaman Tidak Ditemukan :(</h2>
            <p class="mb-4 mx-2">Oops! 😖 Halaman yang Anda cari tidak ditemukan di server kami.</p>
            <div class="mt-3">
                <img src="assets/img/illustrations/404.png" alt="404" class="img-fluid" style="max-width: 400px;">
            </div>
            <a href="index.php" class="btn btn-primary mt-4">Kembali ke Beranda</a>
        </div>
    </div>
</body>

</html>