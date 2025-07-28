<?php
include '../includes/header.php';
include '../includes/db.php';

// Eliminar tela
if (isset($_POST['eliminar_tela_id'])) {
    $id = intval($_POST['eliminar_tela_id']);
    $conn->query("DELETE FROM telas WHERE id = $id");
    header('Location: telas.php');
    //exit();
}

// Editar tela
if (isset($_POST['editar_tela_id'])) {
    $id = intval($_POST['editar_tela_id']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $cantidad_yardas = floatval($_POST['cantidad_yardas']);
    $precio_costo = floatval($_POST['precio_costo']);
    $stat = isset($_POST['stat']) ? intval($_POST['stat']) : 1;
    $sql = "UPDATE telas SET descripcion='$descripcion', cantidad_yardas=$cantidad_yardas, precio_costo=$precio_costo, stat=$stat WHERE id = $id";
    $conn->query($sql);
    header('Location: telas.php');
    //exit();
}

// Registrar tela
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_tela'])) {
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $cantidad_yardas = floatval($_POST['cantidad_yardas']);
    $precio_costo = floatval($_POST['precio_costo']);
    $stat = isset($_POST['stat']) ? intval($_POST['stat']) : 1;
    $sql = "INSERT INTO telas (descripcion, cantidad_yardas, precio_costo, stat) VALUES ('$descripcion', $cantidad_yardas, $precio_costo, $stat)";
    $conn->query($sql);
    header('Location: telas.php');
    //exit();
}
// Obtener todas las telas
$telas = $conn->query("SELECT * FROM telas ORDER BY fecha_log DESC");
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
      <form method="post">
        <input type="hidden" name="registro_tela" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRegistroTelaLabel">Registrar Tela</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <input type="text" name="descripcion" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Cantidad (Yardas)</label>
            <input type="number" step="0.01" name="cantidad_yardas" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Precio de Costo</label>
            <input type="number" step="0.01" name="precio_costo" class="form-control" required>
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
<h3 class="mt-4">Listado de Telas</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Descripción</th>
        <th>Cantidad (Yardas)</th>
        <th>Precio de Costo</th>
        <th>Status</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php $telas->data_seek(0); while($tela = $telas->fetch_assoc()): ?>
      <tr>
        <td><?= $tela['id'] ?></td>
        <td><?= htmlspecialchars($tela['descripcion']) ?></td>
        <td><?= number_format($tela['cantidad_yardas'], 2) ?></td>
        <td>$<?= number_format($tela['precio_costo'], 2) ?></td>
        <td><?= $tela['stat'] == 1 ? 'Activo' : 'Inactivo' ?></td>
        <td><?= $tela['fecha_log'] ?></td>
        <td>
          <!-- Botón Editar -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $tela['id'] ?>">Editar</button>
          <!-- Botón Eliminar -->
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $tela['id'] ?>">Eliminar</button>
        </td>
      </tr>
      <!-- Modal Editar -->
      <div class="modal fade" id="modalEditar<?= $tela['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $tela['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <input type="hidden" name="editar_tela_id" value="<?= $tela['id'] ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel<?= $tela['id'] ?>">Editar Tela</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Descripción</label>
                  <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($tela['descripcion']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Cantidad (Yardas)</label>
                  <input type="number" step="0.01" name="cantidad_yardas" class="form-control" value="<?= $tela['cantidad_yardas'] ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Precio de Costo</label>
                  <input type="number" step="0.01" name="precio_costo" class="form-control" value="<?= $tela['precio_costo'] ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select name="stat" class="form-control">
                    <option value="1" <?= $tela['stat'] == 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= $tela['stat'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Fecha Registro</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($tela['fecha_log']) ?>" readonly>
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
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

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
<?php include '../includes/footer.php'; ?> 