<?php
include '../includes/header.php';
include '../includes/db.php';

// Eliminar tela
if (isset($_POST['eliminar_tela_id'])) {
    $id = intval($_POST['eliminar_tela_id']);
    // Obtener foto para eliminar archivo
    $res = $conn->query("SELECT foto FROM telas WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['foto']) && file_exists('../img/telas/' . $row['foto'])) {
            unlink('../img/telas/' . $row['foto']);
        }
    }
    $conn->query("DELETE FROM telas WHERE id = $id");
    header('Location: telas.php');
    exit();
}

// Editar tela
if (isset($_POST['editar_tela_id'])) {
    $id = intval($_POST['editar_tela_id']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $color = $conn->real_escape_string($_POST['color']);
    $metros = floatval($_POST['metros']);
    $costo = floatval($_POST['costo']);
    $foto_sql = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Eliminar foto anterior
        $res = $conn->query("SELECT foto FROM telas WHERE id = $id");
        if ($res && $row = $res->fetch_assoc()) {
            if (!empty($row['foto']) && file_exists('../img/telas/' . $row['foto'])) {
                unlink('../img/telas/' . $row['foto']);
            }
        }
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreFoto = uniqid('tela_') . '.' . $ext;
        $rutaDestino = '../img/telas/' . $nombreFoto;
        if (!is_dir('../img/telas/')) {
            mkdir('../img/telas/', 0777, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino);
        $foto_sql = ", foto = '$nombreFoto'";
    }
    $sql = "UPDATE telas SET tipo='$tipo', color='$color', metros=$metros, costo=$costo $foto_sql WHERE id = $id";
    $conn->query($sql);
    header('Location: telas.php');
    exit();
}

// Movimiento manual
if (isset($_POST['movimiento_tela_id'])) {
    $id = intval($_POST['movimiento_tela_id']);
    $tipo_mov = $_POST['tipo_movimiento'] === 'ingreso' ? 'ingreso' : 'salida';
    $cantidad = floatval($_POST['cantidad_mov']);
    $descripcion = $conn->real_escape_string($_POST['descripcion_mov']);
    // Actualizar stock
    if ($tipo_mov === 'ingreso') {
        $conn->query("UPDATE telas SET metros = metros + $cantidad WHERE id = $id");
    } else {
        $conn->query("UPDATE telas SET metros = metros - $cantidad WHERE id = $id");
    }
    // Registrar movimiento
    $conn->query("INSERT INTO movimientos_inventario (tela_id, tipo_movimiento, cantidad, descripcion) VALUES ($id, '$tipo_mov', $cantidad, '$descripcion')");
    header('Location: telas.php');
    exit();
}

// Registrar tela
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_tela'])) {
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $color = $conn->real_escape_string($_POST['color']);
    $metros = floatval($_POST['metros']);
    $costo = floatval($_POST['costo']);
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreFoto = uniqid('tela_') . '.' . $ext;
        $rutaDestino = '../img/telas/' . $nombreFoto;
        if (!is_dir('../img/telas/')) {
            mkdir('../img/telas/', 0777, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino);
        $foto = $nombreFoto;
    }
    $sql = "INSERT INTO telas (tipo, color, metros, costo, foto) VALUES ('$tipo', '$color', $metros, $costo, '$foto')";
    $conn->query($sql);
    $tela_id = $conn->insert_id;
    // Registrar movimiento automático
    $conn->query("INSERT INTO movimientos_inventario (tela_id, tipo_movimiento, cantidad, descripcion) VALUES ($tela_id, 'ingreso', $metros, 'Registro inicial de tela')");
    header('Location: telas.php');
    exit();
}
// Obtener todas las telas
$telas = $conn->query("SELECT * FROM telas ORDER BY fecha_ingreso DESC");
?>
<h2 class="mb-4">Telas</h2>
<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistroTela">
  Registrar nueva tela
</button>
<!-- Modal de registro de tela -->
<div class="modal fade" id="modalRegistroTela" tabindex="-1" aria-labelledby="modalRegistroTelaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="registro_tela" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRegistroTelaLabel">Registrar Tela</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tipo de tela</label>
            <input type="text" name="tipo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Color</label>
            <input type="text" name="color" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" step="0.01" name="metros" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Costo</label>
            <input type="number" step="0.01" name="costo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Foto</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Registrar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<h3 class="mt-4">Listado de Telas</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Color</th>
        <th>Cantidad</th>
        <th>Costo</th>
        <th>Foto</th>
        <th>Fecha Ingreso</th>
        <th>Acciones</th>
        <th>Código de Barra</th>
      </tr>
    </thead>
    <tbody>
      <?php $telas->data_seek(0); while($tela = $telas->fetch_assoc()): ?>
      <tr>
        <td><?= $tela['id'] ?></td>
        <td><?= htmlspecialchars($tela['tipo']) ?></td>
        <td><?= htmlspecialchars($tela['color']) ?></td>
        <td><?= $tela['metros'] ?></td>
        <td><?= $tela['costo'] ?></td>
        <td>
          <?php if (!empty($tela['foto'])): ?>
            <img src="../img/telas/<?= htmlspecialchars($tela['foto']) ?>" alt="Foto" style="max-width:60px; max-height:60px;">
          <?php else: ?>
            <span class="text-muted">Sin foto</span>
          <?php endif; ?>
        </td>
        <td><?= $tela['fecha_ingreso'] ?></td>
        <td>
          <!-- Botón Editar -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $tela['id'] ?>">Editar</button>
          <!-- Botón Eliminar -->
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $tela['id'] ?>">Eliminar</button>
          <!-- Botón Movimiento -->
          <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalMovimiento<?= $tela['id'] ?>">Movimiento</button>
        </td>
        <td>
          <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#modalBarcode<?= $tela['id'] ?>">Generar</button>
        </td>
      </tr>
      <!-- Modal Editar -->
      <div class="modal fade" id="modalEditar<?= $tela['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $tela['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="editar_tela_id" value="<?= $tela['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel<?= $tela['id'] ?>">Editar Tela</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Tipo de tela</label>
                  <input type="text" name="tipo" class="form-control" value="<?= htmlspecialchars($tela['tipo']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Color</label>
                  <input type="text" name="color" class="form-control" value="<?= htmlspecialchars($tela['color']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Cantidad</label>
                  <input type="number" step="0.01" name="metros" class="form-control" value="<?= $tela['metros'] ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Costo</label>
                  <input type="number" step="0.01" name="costo" class="form-control" value="<?= $tela['costo'] ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Foto actual</label><br>
                  <?php if (!empty($tela['foto'])): ?>
                    <img src="../img/telas/<?= htmlspecialchars($tela['foto']) ?>" alt="Foto" style="max-width:60px; max-height:60px;">
                  <?php else: ?>
                    <span class="text-muted">Sin foto</span>
                  <?php endif; ?>
                </div>
                <div class="mb-3">
                  <label class="form-label">Nueva foto (opcional)</label>
                  <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Modal Eliminar -->
      <div class="modal fade" id="modalEliminar<?= $tela['id'] ?>" tabindex="-1" aria-labelledby="modalEliminarLabel<?= $tela['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="eliminar_tela_id" value="<?= $tela['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel<?= $tela['id'] ?>">Eliminar Tela</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta tela?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Modal Movimiento -->
      <div class="modal fade" id="modalMovimiento<?= $tela['id'] ?>" tabindex="-1" aria-labelledby="modalMovimientoLabel<?= $tela['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="movimiento_tela_id" value="<?= $tela['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalMovimientoLabel<?= $tela['id'] ?>">Movimiento de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Tipo de movimiento</label>
                  <select name="tipo_movimiento" class="form-select" required>
                    <option value="ingreso">Ingreso</option>
                    <option value="salida">Salida</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Cantidad</label>
                  <input type="number" step="0.01" name="cantidad_mov" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Descripción</label>
                  <input type="text" name="descripcion_mov" class="form-control">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Registrar movimiento</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Modal Código de Barras -->
      <div class="modal fade" id="modalBarcode<?= $tela['id'] ?>" tabindex="-1" aria-labelledby="modalBarcodeLabel<?= $tela['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalBarcodeLabel<?= $tela['id'] ?>">Código de Barras</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
              <img src="barcode.php?id=<?= $tela['id'] ?>" alt="Código de barras" class="img-fluid mb-2"><br>
              <a href="barcode.php?id=<?= $tela['id'] ?>" download="barcode_tela_<?= $tela['id'] ?>.png" class="btn btn-outline-primary btn-sm">Descargar</a>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?> 