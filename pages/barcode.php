<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo 'ID inválido';
    exit;
}

$id = intval($_GET['id']);
$code = 'TELA-' . $id; // Puedes personalizar el código

header('Content-Type: image/png');
$generator = new BarcodeGeneratorPNG();
echo $generator->getBarcode($code, $generator::TYPE_CODE_128, 3, 80); 