<?php
session_start();

// Periksa apakah user sudah login atau belum
if (!isset($_SESSION['id_user'])) {
    header("Location: ../");
    exit();
}
$id_user = $_SESSION['id_user'];

$year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
$_SESSION['selected_year'] = $year; 
?>