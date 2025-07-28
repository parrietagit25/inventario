<?php
include '../includes/header.php';
include '../includes/db.php';

// Eliminar movimiento
if (isset($_POST['eliminar_movimiento_id'])) {
    $id = intval($_POST['eliminar_movimiento_id']);
    $conn->query("DELETE FROM movimientos_inventario WHERE id = $id");
    header('Location: movimientos.php');
    //exit();
}

// Registrar movimiento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_movimiento'])) {
    $tela_id = intval($_POST['tela_id']);
    $cliente_id = intval($_POST['cliente_id']);
    $producto_id = intval($_POST['producto_id']);
    $precio_producto = floatval($_POST['precio_producto']);
    $cantidad_producto = floatval($_POST['cantidad_producto']);
    $cantidad_yardas = floatval($_POST['cantidad_yardas']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    
    // Descontar yardas de la tela
    $conn->query("UPDATE telas SET cantidad_yardas = cantidad_yardas - $cantidad_yardas WHERE id = $tela_id");
    
    // Registrar movimiento
    $sql = "INSERT INTO movimientos_inventario (tela_id, cliente_id, producto_id, precio_producto, cantidad_producto, cantidad_yardas, descripcion) 
            VALUES ($tela_id, $cliente_id, $producto_id, $precio_producto, $cantidad_producto, $cantidad_yardas, '$descripcion')";
    $conn->query($sql);
    header('Location: movimientos.php');
    //exit();
}

// Obtener datos para los selects
$telas = $conn->query("SELECT id, descripcion, cantidad_yardas, precio_costo FROM telas WHERE stat = 1 ORDER BY descripcion");
$clientes = $conn->query("SELECT id, nombre FROM clientes WHERE status = 1 ORDER BY nombre");
$productos = $conn->query("SELECT id, nombre FROM productos WHERE stat = 1 ORDER BY nombre");



// Obtener movimientos
$movimientos = $conn->query("SELECT m.*, t.descripcion as tela_nombre, t.precio_costo, c.nombre as cliente_nombre, p.nombre as producto_nombre 
                             FROM movimientos_inventario m
                             LEFT JOIN telas t ON m.tela_id = t.id
                             LEFT JOIN clientes c ON m.cliente_id = c.id
                             LEFT JOIN productos p ON m.producto_id = p.id
                             ORDER BY m.fecha DESC");
?>
<h2 class="mb-4">Movimientos</h2>



<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistroMovimiento">
  Registrar nuevo movimiento
</button>

<!-- Modal de registro de movimiento -->
<div class="modal fade" id="modalRegistroMovimiento" tabindex="-1" aria-labelledby="modalRegistroMovimientoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="registro_movimiento" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRegistroMovimientoLabel">Registrar Movimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Tela</label>
                <select name="tela_id" class="form-control select2-tela" required>
                  <option value="">Selecciona una tela</option>
                  <?php $telas->data_seek(0); while($tela = $telas->fetch_assoc()): ?>
                    <option value="<?= $tela['id'] ?>"><?= htmlspecialchars($tela['descripcion']) ?> / <?= number_format($tela['cantidad_yardas'], 2) ?> yardas / $<?= number_format($tela['precio_costo'], 2) ?> por yarda</option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>
                          <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Cliente</label>
                  <select name="cliente_id" class="form-control select2-cliente" required>
                    <option value="">Selecciona un cliente</option>
                    <?php $clientes->data_seek(0); while($cliente = $clientes->fetch_assoc()): ?>
                      <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
          </div>
          <div class="row">
                          <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Producto</label>
                  <select name="producto_id" class="form-control select2-producto" required>
                    <option value="">Selecciona un producto</option>
                    <?php $productos->data_seek(0); while($producto = $productos->fetch_assoc()): ?>
                      <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Precio del Producto</label>
                <input type="number" step="0.01" name="precio_producto" class="form-control" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Cantidad del Producto</label>
                <input type="number" step="0.01" name="cantidad_producto" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Cantidad de Yardas</label>
                <input type="number" step="0.01" name="cantidad_yardas" class="form-control" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"></textarea>
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

<h3 class="mt-4">Listado de Movimientos</h3>
<div class="table-responsive">
  <table id="tabla-movimientos" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Tela</th>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Precio Producto</th>
        <th>Cantidad Producto</th>
        <th>Cantidad Yardas</th>
        <th>Descripción</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $movimientos_array = [];
      while($mov = $movimientos->fetch_assoc()): 
        $movimientos_array[] = $mov;
      ?>
      <tr>
        <td><?= $mov['id'] ?></td>
        <td><?= $mov['fecha'] ?></td>
        <td><?= htmlspecialchars($mov['tela_nombre']) ?></td>
        <td><?= htmlspecialchars($mov['cliente_nombre']) ?></td>
        <td><?= htmlspecialchars($mov['producto_nombre']) ?></td>
        <td>$<?= number_format($mov['precio_producto'], 2) ?></td>
        <td><?= number_format($mov['cantidad_producto'], 2) ?></td>
        <td><?= number_format($mov['cantidad_yardas'], 2) ?></td>
        <td><?= htmlspecialchars($mov['descripcion']) ?></td>
        <td>
          <!-- Botón Detalles -->
          <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalDetalles<?= $mov['id'] ?>">Detalles</button>
          <!-- Botón Eliminar -->
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $mov['id'] ?>">Eliminar</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Modales de Detalles -->
<?php foreach($movimientos_array as $mov): ?>
<!-- Modal Detalles -->
<div class="modal fade" id="modalDetalles<?= $mov['id'] ?>" tabindex="-1" aria-labelledby="modalDetallesLabel<?= $mov['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetallesLabel<?= $mov['id'] ?>">Detalles del Movimiento #<?= $mov['id'] ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="text-primary">Información del Cliente</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Cliente:</strong></td>
                <td><?= htmlspecialchars($mov['cliente_nombre']) ?></td>
              </tr>
            </table>
            
            <h6 class="text-success">Información del Producto</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Producto:</strong></td>
                <td><?= htmlspecialchars($mov['producto_nombre']) ?></td>
              </tr>
              <tr>
                <td><strong>Precio del Producto:</strong></td>
                <td>$<?= number_format($mov['precio_producto'], 2) ?></td>
              </tr>
              <tr>
                <td><strong>Cantidad del Producto:</strong></td>
                <td><?= number_format($mov['cantidad_producto'], 2) ?></td>
              </tr>
              <tr class="table-warning">
                <td><strong>Total Producto:</strong></td>
                <td><strong>$<?= number_format($mov['precio_producto'] * $mov['cantidad_producto'], 2) ?></strong></td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6 class="text-info">Información de la Tela</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Tela:</strong></td>
                <td><?= htmlspecialchars($mov['tela_nombre']) ?></td>
              </tr>
              <tr>
                <td><strong>Cantidad de Yardas:</strong></td>
                <td><?= number_format($mov['cantidad_yardas'], 2) ?></td>
              </tr>
              <tr>
                <td><strong>Precio por Yarda:</strong></td>
                <td>$<?= number_format($mov['precio_costo'] ?? 0, 2) ?></td>
              </tr>
              <tr class="table-warning">
                <td><strong>Total Tela:</strong></td>
                <td><strong>$<?= number_format($mov['cantidad_yardas'] * ($mov['precio_costo'] ?? 0), 2) ?></strong></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-12">
            <h6 class="text-danger">Análisis Financiero</h6>
            <table class="table table-sm">
              <tr class="table-success">
                <td><strong>Total Producto:</strong></td>
                <td><strong>$<?= number_format($mov['precio_producto'] * $mov['cantidad_producto'], 2) ?></strong></td>
              </tr>
              <tr class="table-danger">
                <td><strong>Total Tela:</strong></td>
                <td><strong>$<?= number_format($mov['cantidad_yardas'] * ($mov['precio_costo'] ?? 0), 2) ?></strong></td>
              </tr>
              <tr class="table-primary">
                <td><strong>Margen:</strong></td>
                <td><strong>$<?= number_format(($mov['precio_producto'] * $mov['cantidad_producto']) - ($mov['cantidad_yardas'] * ($mov['precio_costo'] ?? 0)), 2) ?></strong></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="modalEliminar<?= $mov['id'] ?>" tabindex="-1" aria-labelledby="modalEliminarLabel<?= $mov['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="eliminar_movimiento_id" value="<?= $mov['id'] ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEliminarLabel<?= $mov['id'] ?>">Eliminar Movimiento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar este movimiento?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- DataTables y extensiones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<!-- Select2 para autocompletado -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
  
  // Inicializar DataTables solo para la tabla de movimientos
  try {
    $('#tabla-movimientos').DataTable({
      dom: 'Bfrtip',
      buttons: [
        'excelHtml5',
        'csvHtml5',
        'pdfHtml5',
        'print'
      ],
      language: {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":           "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
          "sFirst":    "Primero",
          "sLast":     "Último",
          "sNext":     "Siguiente",
          "sPrevious": "Anterior"
        },
        "oAria": {
          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      }
    });
  } catch (error) {
    console.error('Error initializing DataTables:', error);
  }
  
  // Inicializar Select2 cuando el modal se abre
  $('#modalRegistroMovimiento').on('shown.bs.modal', function () {
    // Inicializar Select2 para cada select
    $('.select2-tela').select2({
      placeholder: 'Selecciona una tela',
      allowClear: true,
      width: '100%',
      dropdownParent: $('#modalRegistroMovimiento')
    });
    
    $('.select2-cliente').select2({
      placeholder: 'Selecciona un cliente',
      allowClear: true,
      width: '100%',
      dropdownParent: $('#modalRegistroMovimiento')
    });
    
    $('.select2-producto').select2({
      placeholder: 'Selecciona un producto',
      allowClear: true,
      width: '100%',
      dropdownParent: $('#modalRegistroMovimiento')
    });
  });
  
  // Destruir Select2 cuando el modal se cierra
  $('#modalRegistroMovimiento').on('hidden.bs.modal', function () {
    $('.select2-tela, .select2-cliente, .select2-producto').select2('destroy');
  });
});
</script>
<?php include '../includes/footer.php'; ?> 