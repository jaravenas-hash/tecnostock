<?php
session_start();
// Si ya inició sesión, lo mandamos directo al dashboard de forma segura
if (isset($_SESSION['id_usuario'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoStock - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">TecnoStock</h3>
                        
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">Correo o contraseña incorrectos.</div>
                        <?php endif; ?>

                        <form action="../actions/login_action.php" method="POST">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                            <div class="mb-3">
                                <label for="clave" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="clave" name="clave" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                        <div class="mt-3 text-muted text-center small">
                            Credenciales de prueba:<br>admin@tecnostock.cl / 1234
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>