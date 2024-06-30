<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

use qrcodegenerator\QRCode\Output\QRImage;
use qrcodegenerator\QRCode\Output\QRImageOptions;
use qrcodegenerator\QRCode\QRCode;

$qrImageOptions = new QRImageOptions();
$qrImageOptions->pixelSize = 10;
//$qrImageOptions->cachefile = 'example_image.png';

$im = (new QRCode('https://www.youtube.com/watch?v=DLzxrzFCyOs&t=43s', new QRImage($qrImageOptions)))->output();

echo '<img src="'.$im.'" />';
