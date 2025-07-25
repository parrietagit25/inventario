<?php
include '../includes/header.php';
include '../includes/db.php';

// Movimiento manual
if (isset($_POST['movimiento_producto_id'])) {
    $id = intval($_POST['movimiento_producto_id']);
    $tipo_mov = $_POST['tipo_movimiento'] === 'ingreso' ? 'ingreso' : 'salida';
    $cantidad = intval($_POST['cantidad_mov']);
    $descripcion = $conn->real_escape_string($_POST['descripcion_mov']);
    // Actualizar stock
    if ($tipo_mov === 'ingreso') {
        $conn->query("UPDATE productos SET cantidad = cantidad + $cantidad WHERE id = $id");
    } else {
        $conn->query("UPDATE productos SET cantidad = cantidad - $cantidad WHERE id = $id");
    }
    // Registrar movimiento
    $conn->query("INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, descripcion) VALUES ($id, '$tipo_mov', $cantidad, '$descripcion')");
    header('Location: productos.php');
    exit();
}

// Eliminar producto
if (isset($_POST['eliminar_producto_id'])) {
    $id = intval($_POST['eliminar_producto_id']);
    $res = $conn->query("SELECT foto FROM productos WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['foto']) && file_exists('../img/' . $row['foto'])) {
            unlink('../img/' . $row['foto']);
        }
    }
    $conn->query("DELETE FROM productos WHERE id = $id");
    header('Location: productos.php');
    exit();
}

// Editar producto
if (isset($_POST['editar_producto_id'])) {
    $id = intval($_POST['editar_producto_id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $color = $conn->real_escape_string($_POST['color']);
    $cantidad = intval($_POST['cantidad']);
    $precio = floatval($_POST['precio']);
    $foto_sql = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $res = $conn->query("SELECT foto FROM productos WHERE id = $id");
        if ($res && $row = $res->fetch_assoc()) {
            if (!empty($row['foto']) && file_exists('../img/' . $row['foto'])) {
                unlink('../img/' . $row['foto']);
            }
        }
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreFoto = uniqid('producto_') . '.' . $ext;
        $rutaDestino = '../img/' . $nombreFoto;
        if (!is_dir('../img/')) {
            mkdir('../img/', 0777, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino);
        $foto_sql = ", foto = '$nombreFoto'";
    }
    $sql = "UPDATE productos SET nombre='$nombre', tipo='$tipo', color='$color', cantidad=$cantidad, precio=$precio $foto_sql WHERE id = $id";
    $conn->query($sql);
    header('Location: productos.php');
    exit();
}

// Registrar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_producto'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $color = $conn->real_escape_string($_POST['color']);
    $cantidad = intval($_POST['cantidad']);
    $precio = floatval($_POST['precio']);
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreFoto = uniqid('producto_') . '.' . $ext;
        $rutaDestino = '../img/' . $nombreFoto;
        if (!is_dir('../img/')) {
            mkdir('../img/', 0777, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino);
        $foto = $nombreFoto;
    }
    $sql = "INSERT INTO productos (nombre, tipo, color, cantidad, precio, foto) VALUES ('$nombre', '$tipo', '$color', $cantidad, $precio, '$foto')";
    $conn->query($sql);
    $producto_id = $conn->insert_id;
    // Registrar movimiento automático
    $conn->query("INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, descripcion) VALUES ($producto_id, 'ingreso', $cantidad, 'Registro inicial de producto')");
    header('Location: productos.php');
    exit();
}
// Obtener todos los productos
$productos = $conn->query("SELECT * FROM productos ORDER BY fecha_registro DESC");
?>
<h2 class="mb-4">Productos</h2>
<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistroProducto">
  Registrar nuevo producto
</button>
<!-- Modal de registro de producto -->
<div class="modal fade" id="modalRegistroProducto" tabindex="-1" aria-labelledby="modalRegistroProductoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="registro_producto" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRegistroProductoLabel">Registrar Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select" required>
              <option value="uniforme">Uniforme</option>
              <option value="otro">Otro</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Color</label>
            <input type="text" name="color" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" required>
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
<h3 class="mt-4">Listado de Productos</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Color</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Foto</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
        <th>Código de Barra</th>
      </tr>
    </thead>
    <tbody>
      <?php $productos->data_seek(0); while($prod = $productos->fetch_assoc()): ?>
      <tr>
        <td><?= $prod['id'] ?></td>
        <td><?= htmlspecialchars($prod['nombre']) ?></td>
        <td><?= htmlspecialchars($prod['tipo']) ?></td>
        <td><?= htmlspecialchars($prod['color']) ?></td>
        <td><?= $prod['cantidad'] ?></td>
        <td><?= $prod['precio'] ?></td>
        <td>
          <?php if (!empty($prod['foto'])): ?>
            <img src="../img/<?= htmlspecialchars($prod['foto']) ?>" alt="Foto" style="max-width:60px; max-height:60px;">
          <?php else: ?>
            <span class="text-muted">Sin foto</span>
          <?php endif; ?>
        </td>
        <td><?= $prod['fecha_registro'] ?></td>
        <td>
          <!-- Botón Editar -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $prod['id'] ?>">Editar</button>
          <!-- Botón Eliminar -->
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $prod['id'] ?>">Eliminar</button>
          <!-- Botón Movimiento -->
          <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalMovimiento<?= $prod['id'] ?>">Movimiento</button>
        </td>
        <td>
          <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#modalBarcode<?= $prod['id'] ?>">Generar</button>
        </td>
      </tr>
      <!-- Modal Editar -->
      <div class="modal fade" id="modalEditar<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="editar_producto_id" value="<?= $prod['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel<?= $prod['id'] ?>">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($prod['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Tipo</label>
                  <select name="tipo" class="form-select" required>
                    <option value="uniforme" <?= $prod['tipo'] == 'uniforme' ? 'selected' : '' ?>>Uniforme</option>
                    <option value="otro" <?= $prod['tipo'] == 'otro' ? 'selected' : '' ?>>Otro</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Color</label>
                  <input type="text" name="color" class="form-control" value="<?= htmlspecialchars($prod['color']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Cantidad</label>
                  <input type="number" name="cantidad" class="form-control" value="<?= $prod['cantidad'] ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Precio</label>
                  <input type="number" step="0.01" name="precio" class="form-control" value="<?= $prod['precio'] ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Foto actual</label><br>
                  <?php if (!empty($prod['foto'])): ?>
                    <img src="../img/<?= htmlspecialchars($prod['foto']) ?>" alt="Foto" style="max-width:60px; max-height:60px;">
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
      <div class="modal fade" id="modalEliminar<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="modalEliminarLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="eliminar_producto_id" value="<?= $prod['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel<?= $prod['id'] ?>">Eliminar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este producto?</p>
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
      <div class="modal fade" id="modalMovimiento<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="modalMovimientoLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="movimiento_producto_id" value="<?= $prod['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalMovimientoLabel<?= $prod['id'] ?>">Movimiento de Inventario</h5>
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
                  <input type="number" name="cantidad_mov" class="form-control" required>
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
      <div class="modal fade" id="modalBarcode<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="modalBarcodeLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalBarcodeLabel<?= $prod['id'] ?>">Código de Barras</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
              <img src="barcode.php?id=<?= $prod['id'] ?>&tipo=producto" alt="Código de barras" class="img-fluid mb-2"><br>
              <a href="barcode.php?id=<?= $prod['id'] ?>&tipo=producto" download="barcode_producto_<?= $prod['id'] ?>.png" class="btn btn-outline-primary btn-sm">Descargar</a>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?> 