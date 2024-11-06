<?php
// Tampilkan error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek ekstensi GD
if (!extension_loaded('gd')) {
    die('GD extension is not loaded!');
}

// Path to library
include "../lib/phpqrcode/qrlib.php";

// Temporary file path
$tempDir = "../temp/";

// Buat folder temp jika belum ada
if (!file_exists($tempDir)) {
    mkdir($tempDir);
}

$fileName = 'test_qr.png';
$filePath = $tempDir . $fileName;

// Generate QR Code as file
QRcode::png('Dimas', $filePath);

// Display the image with HTML
echo '<img src="../temp/' . $fileName . '" />';

// Print status
echo '<br>QR Code has been generated and saved to: ' . $filePath;
?>