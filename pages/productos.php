<?php
include '../includes/header.php';
include '../includes/db.php';

// Eliminar producto
if (isset($_POST['eliminar_producto_id'])) {
    $id = intval($_POST['eliminar_producto_id']);
    $conn->query("DELETE FROM productos WHERE id = $id");
    header('Location: productos.php');
    //exit();
}

// Editar producto
if (isset($_POST['editar_producto_id'])) {
    $id = intval($_POST['editar_producto_id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $stat = isset($_POST['stat']) ? intval($_POST['stat']) : 1;
    $sql = "UPDATE productos SET nombre='$nombre', stat=$stat WHERE id = $id";
    $conn->query($sql);
    header('Location: productos.php');
    //exit();
}

// Registrar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_producto'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $stat = isset($_POST['stat']) ? intval($_POST['stat']) : 1;
    $sql = "INSERT INTO productos (nombre, stat) VALUES ('$nombre', $stat)";
    $conn->query($sql);
    header('Location: productos.php');
    //exit();
}
// Obtener todos los productos
$productos = $conn->query("SELECT * FROM productos ORDER BY fecha_log DESC");
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
      <form method="post">
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
            <label class="form-label">Status</label>
            <select name="stat" class="form-control">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
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
        <th>Status</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php $productos->data_seek(0); while($prod = $productos->fetch_assoc()): ?>
      <tr>
        <td><?= $prod['id'] ?></td>
        <td><?= htmlspecialchars($prod['nombre']) ?></td>
        <td><?= $prod['stat'] == 1 ? 'Activo' : 'Inactivo' ?></td>
        <td><?= $prod['fecha_log'] ?></td>
        <td>
          <!-- Botón Editar -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $prod['id'] ?>">Editar</button>
          <!-- Botón Eliminar -->
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $prod['id'] ?>">Eliminar</button>
        </td>
      </tr>
      <!-- Modal Editar -->
      <div class="modal fade" id="modalEditar<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
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
                  <label class="form-label">Status</label>
                  <select name="stat" class="form-control">
                    <option value="1" <?= $prod['stat'] == 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= $prod['stat'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Fecha Registro</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($prod['fecha_log']) ?>" readonly>
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
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?> 

<!-- DataTables y extensiones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
  $('table.table').DataTable({
    dom: 'Bfrtip',
    buttons: [
      'excelHtml5',
      'csvHtml5',
      'pdfHtml5',
      'print'
    ],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    }
  });
});
</script> 