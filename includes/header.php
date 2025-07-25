<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario GOVIL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/govil/pages/dashboard.php">GOVIL Inventario</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="/govil/pages/telas.php">Telas</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/clientes.php">Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/ordenes.php">Órdenes</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/movimientos.php">Movimientos</a></li>
        <li class="nav-item"><a class="nav-link" href="/govil/pages/inventario.php">Inventario</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container"> 