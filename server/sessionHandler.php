<?php
require_once('configDB.php');
session_start();

// Periksa apakah user sudah login atau belum
if (!isset($_SESSION['id_user'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['id_user'];
$query = "SELECT hak_akses FROM user WHERE id_user = '$id_user'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($user['hak_akses'] == 0) {
    session_destroy();
    header("Location: ../login.php?error=akun_non_aktif");
    exit();
}

$year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
$_SESSION['selected_year'] = $year; 
?>