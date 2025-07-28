<?php 
session_start(); 

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario GOVIL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/govil/pages/dashboard.php">GOVIL Inventario</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/govil/pages/telas.php">Telas</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/clientes.php">Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/movimientos.php">Movimientos</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/inventario.php">Inventario</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/reportes.php">Reportes</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="nav-link">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
          </span>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/govil/pages/logout.php" title="Cerrar Sesión">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container"> 