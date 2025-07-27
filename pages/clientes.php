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
    $sql = "UPDATE clientes SET nombre='$nombre', telefono='$telefono', direccion='$direccion' WHERE id = $id";
    $conn->query($sql);
    header('Location: clientes.php');
    //exit();
}

// Registrar cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_cliente'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $sql = "INSERT INTO clientes (nombre, telefono, direccion) VALUES ('$nombre', '$telefono', '$direccion')";
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