<?php
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$builder = new Builder();
$result = $builder->build(
    data: "Testing QrCode",
    labelText: "Label"
);
file_put_contents(__DIR__.'/test2_qr.png', $result->getString());
echo "OK\n";
