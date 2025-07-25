<?php
include '../includes/header.php';
include '../includes/db.php';

// Obtener productos
$productos = $conn->query("SELECT id, nombre, color, cantidad, 'Producto' as tipo FROM productos");
// Obtener telas
$telas = $conn->query("SELECT id, tipo as nombre, color, metros as cantidad, 'Tela' as tipo FROM telas");

// Unir ambos en un solo array
$inventario = [];
while($p = $productos->fetch_assoc()) {
    $inventario[] = $p;
}
while($t = $telas->fetch_assoc()) {
    $inventario[] = $t;
}
?>
<h2 class="mb-4">Inventario General</h2>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Tipo</th>
        <th>Nombre/Tipo</th>
        <th>Color</th>
        <th>Cantidad Actual</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($inventario as $item): ?>
      <tr>
        <td><?= $item['tipo'] ?></td>
        <td><?= htmlspecialchars($item['nombre']) ?></td>
        <td><?= htmlspecialchars($item['color']) ?></td>
        <td><?= $item['cantidad'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?> 