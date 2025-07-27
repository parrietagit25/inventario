<?php

$produccion = 1;

if($produccion == 1){
    $host = 'localhost';
    $user = 'uog7nnpvxjdax';
    $pass = 'Chicho1787$$$';
    $db   = 'dbkyfsgcgzyxlb';
    $port = 3306;
}else{
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'govil_inventario';
    $port = 3307;
}
$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
?>
