<?php
include '../includes/header.php';
include '../includes/db.php';

// Registrar movimiento manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_movimiento'])) {
    $tipo_mov = $_POST['tipo_movimiento'] === 'ingreso' ? 'ingreso' : 'salida';
    $cantidad = floatval($_POST['cantidad']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $producto_id = !empty($_POST['producto_id']) ? intval($_POST['producto_id']) : null;
    $tela_id = !empty($_POST['tela_id']) ? intval($_POST['tela_id']) : null;
    // Actualizar stock
    if ($producto_id) {
        if ($tipo_mov === 'ingreso') {
            $conn->query("UPDATE productos SET cantidad = cantidad + $cantidad WHERE id = $producto_id");
        } else {
            $conn->query("UPDATE productos SET cantidad = cantidad - $cantidad WHERE id = $producto_id");
        }
    }
    if ($tela_id) {
        if ($tipo_mov === 'ingreso') {
            $conn->query("UPDATE telas SET metros = metros + $cantidad WHERE id = $tela_id");
        } else {
            $conn->query("UPDATE telas SET metros = metros - $cantidad WHERE id = $tela_id");
        }
    }
    // Registrar movimiento
    $conn->query("INSERT INTO movimientos_inventario (producto_id, tela_id, tipo_movimiento, cantidad, descripcion) VALUES (".
        ($producto_id ? $producto_id : 'NULL').", ".($tela_id ? $tela_id : 'NULL').", '$tipo_mov', $cantidad, '$descripcion')");
    header('Location: movimientos.php');
    exit();
}

// Obtener productos y telas para el formulario
$productos = $conn->query("SELECT id, nombre FROM productos ORDER BY nombre");
$telas = $conn->query("SELECT id, tipo, color FROM telas ORDER BY tipo, color");

// Listar movimientos
$movimientos = $conn->query("SELECT m.*, p.nombre as producto, t.tipo as tipo_tela, t.color as color_tela FROM movimientos_inventario m
    LEFT JOIN productos p ON m.producto_id = p.id
    LEFT JOIN telas t ON m.tela_id = t.id
    ORDER BY m.fecha DESC");
?>
<h2 class="mb-4">Movimientos de Inventario</h2>
<!-- Formulario de registro manual -->
<div class="card mb-4">
  <div class="card-body">
    <form method="post">
      <input type="hidden" name="registro_movimiento" value="1">
      <div class="row mb-3">
        <div class="col-md-3">
          <label class="form-label">Tipo de movimiento</label>
          <select name="tipo_movimiento" class="form-select" required>
            <option value="ingreso">Ingreso</option>
            <option value="salida">Salida</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Producto</label>
          <select name="producto_id" id="selectProducto" class="form-select">
            <option value="">-- Seleccione producto --</option>
            <?php $productos->data_seek(0); while($p = $productos->fetch_assoc()): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tela</label>
          <select name="tela_id" id="selectTela" class="form-select">
            <option value="">-- Seleccione tela --</option>
            <?php $telas->data_seek(0); while($t = $telas->fetch_assoc()): ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['tipo']) ?> (<?= htmlspecialchars($t['color']) ?>)</option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label">Cantidad</label>
          <input type="number" name="cantidad" class="form-control" step="0.01" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-12">
          <label class="form-label">Descripción</label>
          <input type="text" name="descripcion" class="form-control">
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-end">
          <button type="submit" class="btn btn-primary">Registrar movimiento</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Listado de movimientos -->
<h3>Listado de Movimientos</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Producto</th>
        <th>Tela</th>
        <th>Cantidad</th>
        <th>Descripción</th>
      </tr>
    </thead>
    <tbody>
      <?php while($mov = $movimientos->fetch_assoc()): ?>
      <tr>
        <td><?= $mov['fecha'] ?></td>
        <td><?= ucfirst($mov['tipo_movimiento']) ?></td>
        <td><?= $mov['producto'] ? htmlspecialchars($mov['producto']) : '-' ?></td>
        <td><?= $mov['tipo_tela'] ? htmlspecialchars($mov['tipo_tela'].' ('.$mov['color_tela'].')') : '-' ?></td>
        <td><?= $mov['cantidad'] ?></td>
        <td><?= htmlspecialchars($mov['descripcion']) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<script>
// Solo permitir seleccionar producto o tela, no ambos
const selectProducto = document.getElementById('selectProducto');
const selectTela = document.getElementById('selectTela');
selectProducto.addEventListener('change', function() {
  if (this.value) {
    selectTela.disabled = true;
  } else {
    selectTela.disabled = false;
  }
});
selectTela.addEventListener('change', function() {
  if (this.value) {
    selectProducto.disabled = true;
  } else {
    selectProducto.disabled = false;
  }
});
</script>
<?php include '../includes/footer.php'; ?> 