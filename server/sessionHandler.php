<?php
session_start();

// Periksa apakah user sudah login atau belum
if (!isset($_SESSION['id_user'])) {
    header("Location: ../");
    exit();
}
?>