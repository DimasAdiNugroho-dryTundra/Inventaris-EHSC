<?php
include('../lib/phpqrcode/qrlib.php');
$kode = $_GET['id'];
QRcode::png($kode);
?>