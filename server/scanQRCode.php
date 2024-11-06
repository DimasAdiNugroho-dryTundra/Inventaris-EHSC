<?php
// File: server/scanQRCode.php

require_once __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Detector\QRDetector;
use chillerlan\QRCode\Decoder\Decoder;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['qr_image'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method or no file uploaded'
    ]);
    exit;
}

try {
    // Validate uploaded file
    if ($_FILES['qr_image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed');
    }

    // Check if file is an image
    $check = getimagesize($_FILES['qr_image']['tmp_name']);
    if ($check === false) {
        throw new Exception('File is not a valid image');
    }

    // Read the image file
    $image = file_get_contents($_FILES['qr_image']['tmp_name']);

    // Set up QR code options
    $options = new QROptions([
        'eccLevel' => QRCode::ECC_L,
    ]);

    // Create detector instance
    $detector = new QRDetector($options);

    // Detect and decode QR code
    $decoder = new Decoder($options);
    $result = $decoder->decode($detector->detect($image));

    if ($result !== null) {
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } else {
        throw new Exception('Could not read QR code');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>