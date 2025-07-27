<?php
$id = isset($_GET['id']) ? $_GET['id'] : '';
if (!$id) {
    die('ID no especificado');
}
$barcode_url = "https://bwipjs-api.metafloor.com/?bcid=code128&text=" . urlencode($id);

// Obtener la imagen del servicio externo
$image = file_get_contents($barcode_url);
if ($image === false) {
    die('No se pudo obtener la imagen del código de barras.');
}

header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="barcode_tela_' . $id . '.png"');
echo $image;
exit; 