<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

include '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $password = $_POST['password'];
    
    // Buscar usuario en la base de datos
    $sql = "SELECT id, usuario, pass FROM usuarios WHERE usuario = '$usuario'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña
        if (password_verify($password, $user['pass'])) {
            // Login exitoso
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['usuario'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Contraseña incorrecta';
        }
    } else {
        $error = 'Usuario no encontrado';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Inventario GOVIL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="text-center mb-4 mt-5">
        <img src="../img/logo.png" alt="Logo GOVIL" class="img-fluid" style="max-height: 120px;">
      </div>
      <div class="card">
        <div class="card-body">
          <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>
          
          <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>
          
          <form method="post">
            <div class="mb-3">
              <label for="usuario" class="form-label">Usuario</label>
              <input type="text" class="form-control" id="usuario" name="usuario" required 
                     value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 