<?php
include '../includes/header.php';
include '../includes/db.php';

// Obtener datos para los filtros
$clientes = $conn->query("SELECT id, nombre FROM clientes WHERE status = 1 ORDER BY nombre");
$telas = $conn->query("SELECT id, descripcion FROM telas WHERE stat = 1 ORDER BY descripcion");

// Procesar filtros
$where_conditions = [];
$params = [];

if (isset($_GET['cliente_id']) && !empty($_GET['cliente_id'])) {
    $where_conditions[] = "m.cliente_id = " . intval($_GET['cliente_id']);
}

if (isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde'])) {
    $fecha_desde = $conn->real_escape_string($_GET['fecha_desde']);
    $where_conditions[] = "DATE(m.fecha) >= '$fecha_desde'";
}

if (isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta'])) {
    $fecha_hasta = $conn->real_escape_string($_GET['fecha_hasta']);
    $where_conditions[] = "DATE(m.fecha) <= '$fecha_hasta'";
}

if (isset($_GET['tela_id']) && !empty($_GET['tela_id'])) {
    $where_conditions[] = "m.tela_id = " . intval($_GET['tela_id']);
}

$where_clause = "";
if (!empty($where_conditions)) {
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
}

// Obtener movimientos con filtros
$sql = "SELECT 
            m.id,
            m.fecha,
            c.nombre as cliente_nombre,
            p.nombre as producto_nombre,
            m.precio_producto,
            m.cantidad_producto,
            (m.precio_producto * m.cantidad_producto) as total_producto,
            t.descripcion as tela_nombre,
            m.cantidad_yardas,
            t.precio_costo,
            (m.cantidad_yardas * t.precio_costo) as total_tela,
            ((m.precio_producto * m.cantidad_producto) - (m.cantidad_yardas * t.precio_costo)) as margen,
            m.descripcion
        FROM movimientos_inventario m
        LEFT JOIN clientes c ON m.cliente_id = c.id
        LEFT JOIN productos p ON m.producto_id = p.id
        LEFT JOIN telas t ON m.tela_id = t.id
        $where_clause
        ORDER BY m.fecha DESC";

$movimientos = $conn->query($sql);
?>

<h2 class="mb-4">Reportes de Movimientos</h2>

<!-- Filtros -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Filtros</h5>
  </div>
  <div class="card-body">
    <form method="get" class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Cliente</label>
        <select name="cliente_id" class="form-control select2-filtro">
          <option value="">Todos los clientes</option>
          <?php $clientes->data_seek(0); while($cliente = $clientes->fetch_assoc()): ?>
            <option value="<?= $cliente['id'] ?>" <?= (isset($_GET['cliente_id']) && $_GET['cliente_id'] == $cliente['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cliente['nombre']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Fecha Desde</label>
        <input type="date" name="fecha_desde" class="form-control" value="<?= $_GET['fecha_desde'] ?? '' ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">Fecha Hasta</label>
        <input type="date" name="fecha_hasta" class="form-control" value="<?= $_GET['fecha_hasta'] ?? '' ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tela</label>
        <select name="tela_id" class="form-control select2-filtro">
          <option value="">Todas las telas</option>
          <?php $telas->data_seek(0); while($tela = $telas->fetch_assoc()): ?>
            <option value="<?= $tela['id'] ?>" <?= (isset($_GET['tela_id']) && $_GET['tela_id'] == $tela['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($tela['descripcion']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
        <a href="reportes.php" class="btn btn-secondary">Limpiar</a>
      </div>
    </form>
  </div>
</div>

<!-- Tabla de Reportes -->
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Reporte de Movimientos</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="tabla-reportes" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Precio Producto</th>
            <th>Cantidad Producto</th>
            <th>Total Producto</th>
            <th>Tela</th>
            <th>Cantidad Yardas</th>
            <th>Precio Yarda</th>
            <th>Total Tela</th>
            <th>Margen</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody>
          <?php while($mov = $movimientos->fetch_assoc()): ?>
          <tr>
            <td><?= $mov['id'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
            <td><?= htmlspecialchars($mov['cliente_nombre']) ?></td>
            <td><?= htmlspecialchars($mov['producto_nombre']) ?></td>
            <td>$<?= number_format($mov['precio_producto'], 2) ?></td>
            <td><?= number_format($mov['cantidad_producto'], 2) ?></td>
            <td>$<?= number_format($mov['total_producto'], 2) ?></td>
            <td><?= htmlspecialchars($mov['tela_nombre']) ?></td>
            <td><?= number_format($mov['cantidad_yardas'], 2) ?></td>
            <td>$<?= number_format($mov['precio_costo'] ?? 0, 2) ?></td>
            <td>$<?= number_format($mov['total_tela'], 2) ?></td>
            <td>$<?= number_format($mov['margen'], 2) ?></td>
            <td><?= htmlspecialchars($mov['descripcion']) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

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
  // Inicializar Select2 para filtros
  $('.select2-filtro').select2({
    placeholder: 'Selecciona una opción',
    allowClear: true,
    width: '100%'
  });
  
  // Inicializar DataTables para reportes
  $('#tabla-reportes').DataTable({
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
    },
    pageLength: 25,
    order: [[1, 'desc']] // Ordenar por fecha descendente
  });
});
</script>

<?php include '../includes/footer.php'; ?> 