
<div class="container mt-5">
    <h2>Generador de Hash de Contraseñas</h2>

    <!-- Formulario para generar hash -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Crear Hash de Contraseña</h5>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="password" name="password" 
                                   value="<?= htmlspecialchars($_POST['password'] ?? 'admin123') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Hash</button>
                    </div>
                    <div class="col-md-6">
                        <?php if (isset($_POST['password'])): ?>
                            <div class="alert alert-success">
                                <strong>Hash generado:</strong><br>
                                <code><?= password_hash($_POST['password'], PASSWORD_DEFAULT) ?></code>
                            </div>
                            <div class="alert alert-info">
                                <strong>SQL para insertar:</strong><br>
                                <code>INSERT INTO usuarios (usuario, pass) VALUES ('admin', '<?= password_hash($_POST['password'], PASSWORD_DEFAULT) ?>');</code>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Información del Sistema de Login</h5>
        </div>
        <div class="card-body">
            <h6>Credenciales de prueba:</h6>
            <ul>
                <li><strong>Usuario:</strong> admin</li>
                <li><strong>Contraseña:</strong> admin123</li>
            </ul>
            
            <h6>SQL para crear la tabla usuarios:</h6>
            <pre><code>CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);</code></pre>
            
            <h6>SQL para insertar usuario de prueba:</h6>
            <pre><code>INSERT INTO usuarios (usuario, pass) VALUES ('admin', '<?= password_hash('admin123', PASSWORD_DEFAULT) ?>');</code></pre>
        </div>
    </div>
</div>
