<?php
include '../includes/header.php';
include '../includes/db.php';

// Obtener clientes y productos para el formulario
$clientes = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre");
$productos = $conn->query("SELECT id, nombre, cantidad, precio FROM productos ORDER BY nombre");

// Registrar orden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_orden'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $productos_orden = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];
    $precios = $_POST['precio'];
    $total = floatval($_POST['total']);
    // Insertar orden
    $conn->query("INSERT INTO ordenes (cliente_id, total) VALUES ($cliente_id, $total)");
    $orden_id = $conn->insert_id;
    // Insertar detalles y descontar inventario
    for ($i = 0; $i < count($productos_orden); $i++) {
        $pid = intval($productos_orden[$i]);
        $cant = intval($cantidades[$i]);
        $precio_unit = floatval($precios[$i]);
        $subtotal = $cant * $precio_unit;
        $conn->query("INSERT INTO detalle_orden (orden_id, producto_id, cantidad, precio_unitario, subtotal) VALUES ($orden_id, $pid, $cant, $precio_unit, $subtotal)");
        // Descontar inventario
        $conn->query("UPDATE productos SET cantidad = cantidad - $cant WHERE id = $pid");
    }
    header('Location: ordenes.php');
    //exit();
}

// Listar órdenes
$ordenes = $conn->query("SELECT o.*, c.nombre as cliente FROM ordenes o JOIN clientes c ON o.cliente_id = c.id ORDER BY o.fecha DESC");
?>
<h2 class="mb-4">Órdenes</h2>
<!-- Formulario de creación de orden -->
<div class="card mb-4">
  <div class="card-body">
    <form method="post" id="formOrden">
      <input type="hidden" name="registro_orden" value="1">
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Cliente</label>
          <select name="cliente_id" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php while($cli = $clientes->fetch_assoc()): ?>
              <option value="<?= $cli['id'] ?>"><?= htmlspecialchars($cli['nombre']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div id="productosContainer">
      <template id="productoTemplate">
  <div class="row align-items-end mb-2 producto-row">
    <div class="col-md-4">
      <label class="form-label">Producto</label>
      <select name="producto_id[]" class="form-select producto-select" required>
        <option value="">Seleccione...</option>
        <?php $productos->data_seek(0); while($prod = $productos->fetch_assoc()): ?>
          <option value="<?= $prod['id'] ?>" data-precio="<?= $prod['precio'] ?>" data-stock="<?= $prod['cantidad'] ?>">
            <?= htmlspecialchars($prod['nombre']) ?> (Stock: <?= $prod['cantidad'] ?>)
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Cantidad</label>
      <input type="number" name="cantidad[]" class="form-control cantidad-input" min="1" value="1" required>
    </div>
    <div class="col-md-2">
      <label class="form-label">Precio</label>
      <input type="number" name="precio[]" class="form-control precio-input" step="0.01" min="0" value="0" required readonly>
    </div>
    <div class="col-md-2">
      <label class="form-label">Subtotal</label>
      <input type="number" class="form-control subtotal-input" value="0" readonly>
    </div>
    <div class="col-md-2">
      <button type="button" class="btn btn-danger btn-remove-producto">Eliminar</button>
    </div>
  </div>
</template>

      </div>
      <button type="button" class="btn btn-secondary mb-2" id="btnAgregarProducto">Agregar producto</button>
      <div class="row mt-3">
        <div class="col-md-4 offset-md-8">
          <div class="input-group">
            <span class="input-group-text">Total</span>
            <input type="number" name="total" id="totalOrden" class="form-control" value="0" readonly>
          </div>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-md-12 text-end">
          <button type="submit" class="btn btn-primary">Registrar Orden</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- Listado de órdenes -->
<h3>Listado de Órdenes</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $modals = '';
      while($orden = $ordenes->fetch_assoc()): ?>
      <tr>
        <td><?= $orden['id'] ?></td>
        <td><?= htmlspecialchars($orden['cliente']) ?></td>
        <td><?= $orden['fecha'] ?></td>
        <td><?= $orden['total'] ?></td>
        <td>
          <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetalle<?= $orden['id'] ?>">Ver detalle</button>
        </td>
      </tr>
      <?php
      // Acumula el HTML del modal en una variable
      $modals .= '
<div class="modal fade" id="modalDetalle'.$orden['id'].'" tabindex="-1" aria-labelledby="modalDetalleLabel'.$orden['id'].'" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalleLabel'.$orden['id'].'">Detalle de Orden #'.$orden['id'].'</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio Unitario</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>';
      $detalles = $conn->query("SELECT d.*, p.nombre FROM detalle_orden d JOIN productos p ON d.producto_id = p.id WHERE d.orden_id = " . $orden['id']);
      while($det = $detalles->fetch_assoc()) {
        $modals .= '<tr>
    <td>'.htmlspecialchars($det['nombre']).'</td>
    <td>'.$det['cantidad'].'</td>
    <td>'.$det['precio_unitario'].'</td>
    <td>'.$det['subtotal'].'</td>
  </tr>';
      }
      $modals .= '</tbody>
        </table>
        <div class="text-end">
          <strong>Total: '.$orden['total'].'</strong>
        </div>
      </div>
    </div>
  </div>
</div>';
      endwhile;
      ?>
    </tbody>
  </table>
</div>
<?= $modals ?>
<script>
document.getElementById('btnAgregarProducto').addEventListener('click', function () {
  const template = document.getElementById('productoTemplate');
  const clone = template.content.cloneNode(true);
  document.getElementById('productosContainer').appendChild(clone);
  actualizarListeners();
  recalcularTodo();
});

function actualizarListeners() {
  document.querySelectorAll('.btn-remove-producto').forEach(btn => {
    btn.onclick = function () {
      if (document.querySelectorAll('.producto-row').length > 1) {
        btn.closest('.producto-row').remove();
        recalcularTodo();
      }
    };
  });

  document.querySelectorAll('.producto-select').forEach(sel => {
    sel.onchange = function () {
      const precio = sel.selectedOptions[0].getAttribute('data-precio') || 0;
      const stock = sel.selectedOptions[0].getAttribute('data-stock') || 0;
      const row = sel.closest('.producto-row');
      const cantidadInput = row.querySelector('.cantidad-input');
      const precioInput = row.querySelector('.precio-input');

      precioInput.value = precio;
      cantidadInput.max = stock;

      if (parseInt(cantidadInput.value) > parseInt(stock)) {
        cantidadInput.value = stock;
      }

      recalcularTodo();
    };
  });

  document.querySelectorAll('.cantidad-input, .precio-input').forEach(inp => {
    inp.oninput = recalcularTodo;
  });
}

function calcularSubtotales() {
  document.querySelectorAll('.producto-row').forEach(row => {
    const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
    const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
    const subtotal = cantidad * precio;
    row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
  });
}

function calcularTotal() {
  let total = 0;
  document.querySelectorAll('.producto-row').forEach((row, idx) => {
    const subtotal = parseFloat(row.querySelector('.subtotal-input').value) || 0;
    console.log(`Subtotal fila ${idx + 1}: ${subtotal}`);
    total += subtotal;
  });
  console.log("Total final calculado:", total);
  document.getElementById('totalOrden').value = total.toFixed(2);
}

function recalcularTodo() {
  calcularSubtotales();
  calcularTotal();
}

// Inicializa
actualizarListeners();
recalcularTodo();
</script>
<script>
window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('btnAgregarProducto').click();
});
</script>
<script>
    document.getElementById('formOrden').addEventListener('submit', function (e) {
  recalcularTodo(); // asegúrate de recalcular
  const total = parseFloat(document.getElementById('totalOrden').value) || 0;
  if (total <= 0) {
    e.preventDefault();
    alert('El total debe ser mayor a cero.');
  }
});

</script>

<?php include '../includes/footer.php'; ?> 