<?php
include '../includes/header.php';
include '../includes/db.php';

// Obtener telas con precio y cantidad
$telas = $conn->query("SELECT id, descripcion, cantidad_yardas, precio_costo FROM telas WHERE stat = 1 ORDER BY descripcion ASC");
?>
<h2 class="mb-4">Inventario de Telas</h2>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Descripción</th>
        <th>Cantidad (Yardas)</th>
        <th>Precio Unitario</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while($tela = $telas->fetch_assoc()): ?>
      <tr>
        <td><?= $tela['id'] ?></td>
        <td><?= htmlspecialchars($tela['descripcion']) ?></td>
        <td><?= number_format($tela['cantidad_yardas'], 2) ?></td>
        <td>$<?= number_format($tela['precio_costo'], 2) ?></td>
        <td>$<?= number_format($tela['cantidad_yardas'] * $tela['precio_costo'], 2) ?></td>
      </tr>
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