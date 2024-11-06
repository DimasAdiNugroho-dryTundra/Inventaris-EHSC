<?php
require('../vendor/autoload.php');

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Output\{ImageOutput, PngOutput};

$data = 'Dimas';

$qrcode = new QRCode(new PngOutput());
$imageData = $qrcode->render($data);

file_put_contents('qrcode.png', $imageData);
?>


<img src="qrcode.png" alt="">