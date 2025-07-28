<?php
include '../includes/header.php';
include '../includes/db.php';

// Eliminar cliente
if (isset($_POST['eliminar_cliente_id'])) {
    $id = intval($_POST['eliminar_cliente_id']);
    $conn->query("DELETE FROM clientes WHERE id = $id");
    header('Location: clientes.php');
    //exit();
}

// Editar cliente
if (isset($_POST['editar_cliente_id'])) {
    $id = intval($_POST['editar_cliente_id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $email = $conn->real_escape_string($_POST['email']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
    $sql = "UPDATE clientes SET nombre='$nombre', telefono='$telefono', direccion='$direccion', email='$email', descripcion='$descripcion', status=$status WHERE id = $id";
    $conn->query($sql);
    header('Location: clientes.php');
    //exit();
}

// Registrar cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_cliente'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $email = $conn->real_escape_string($_POST['email']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
    $sql = "INSERT INTO clientes (nombre, telefono, direccion, email, descripcion, status) VALUES ('$nombre', '$telefono', '$direccion', '$email', '$descripcion', $status)";
    $conn->query($sql);
    header('Location: clientes.php');
    //exit();
}
// Obtener todos los clientes
$clientes = $conn->query("SELECT * FROM clientes ORDER BY id DESC");
?>
<h2 class="mb-4">Clientes</h2>
<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistroCliente">
  Registrar nuevo cliente
</button>
<!-- Modal de registro de cliente -->
<div class="modal fade" id="modalRegistroCliente" tabindex="-1" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="registro_cliente" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRegistroClienteLabel">Registrar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <input type="text" name="descripcion" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
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
<h3 class="mt-4">Listado de Clientes</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Teléfono</th>
        <th>Dirección</th>
        <th>Email</th>
        <th>Descripción</th>
        <th>Status</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php $clientes->data_seek(0); while($cli = $clientes->fetch_assoc()): ?>
      <tr>
        <td><?= $cli['id'] ?></td>
        <td><?= htmlspecialchars($cli['nombre']) ?></td>
        <td><?= htmlspecialchars($cli['telefono']) ?></td>
        <td><?= htmlspecialchars($cli['direccion']) ?></td>
        <td><?= htmlspecialchars($cli['email']) ?></td>
        <td><?= htmlspecialchars($cli['descripcion']) ?></td>
        <td><?= $cli['status'] == 1 ? 'Activo' : 'Inactivo' ?></td>
        <td>
          <!-- Botón Editar -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $cli['id'] ?>">Editar</button>
          <!-- Botón Eliminar -->
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $cli['id'] ?>">Eliminar</button>
        </td>
      </tr>
      <!-- Modal Editar -->
      <div class="modal fade" id="modalEditar<?= $cli['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $cli['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="editar_cliente_id" value="<?= $cli['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel<?= $cli['id'] ?>">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Nombre</label>
                  <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($cli['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Teléfono</label>
                  <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($cli['telefono']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Dirección</label>
                  <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($cli['direccion']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cli['email']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Descripción</label>
                  <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($cli['descripcion']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-control">
                    <option value="1" <?= $cli['status'] == 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= $cli['status'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Fecha Log</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($cli['fecha_log']) ?>" readonly>
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
      <div class="modal fade" id="modalEliminar<?= $cli['id'] ?>" tabindex="-1" aria-labelledby="modalEliminarLabel<?= $cli['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="eliminar_cliente_id" value="<?= $cli['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel<?= $cli['id'] ?>">Eliminar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este cliente?</p>
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