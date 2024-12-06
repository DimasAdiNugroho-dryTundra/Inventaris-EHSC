<?php
// File: server/generateQRCode.php
include('../lib/phpqrcode/qrlib.php');
// Ambil kode barang dari parameter URL
$kode = $_GET['id'];

// Outputs image directly into browser, as PNG stream
QRcode::png($kode);
?>