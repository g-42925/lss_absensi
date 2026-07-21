<?php
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;

$builder = Builder::create()
    ->writer(new PngWriter())
    ->writerOptions([])
    ->data('Test Data')
    ->encoding(new Encoding('UTF-8'))
    // ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    ->size(300)
    ->margin(10)
    // ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
    ->labelText('Test Label');

$result = $builder->build();
file_put_contents(__DIR__ . '/test_qr.png', $result->getString());
echo "OK\n";
