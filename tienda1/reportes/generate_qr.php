<?php
require __DIR__ . '/../vendor/autoload.php';


use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function generarQRCode($data, $filePath) {
    $qrCode = QrCode::create($data)
        ->setSize(120)
        ->setMargin(10);

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    file_put_contents($filePath, $result->getString());
}

