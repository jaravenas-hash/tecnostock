<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
require_once '../config/conexion.php';

// Obtener productos activos para el selector
$stmt_prod = $pdo->query("SELECT id_producto, código, nombre, stock_actual FROM producto WHERE estado = 1");
$productos = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoStock - Registrar Movimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">TecnoStock - Movimientos</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Volver al Dashboard</a>
        </div>
    </nav>

    <div class="container col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="mb-4">Registrar Entrada / Salida</h3>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                            if ($_GET['error'] == 'stock_insuficiente') {
                                echo 'Error: La cantidad de salida excede el stock actual (el stock no puede quedar bajo cero).';
                            } else {
                                echo 'Ocurrió un error al registrar el movimiento.';
                            }
                        ?>
                    </div>
                <?php endif; ?>

                <form action="../actions/movimiento_guardar.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <select class="form-select" name="id_producto" required>
                            <option value="">Seleccione un producto...</option>
                            <?php foreach ($productos as $prod): ?>
                                <option value="<?php echo $prod['id_producto']; ?>">
                                    <?php echo htmlspecialchars($prod['código'] . ' - ' . $prod['nombre'] . ' (Stock actual: ' . $prod['stock_actual'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Movimiento</label>
                        <select class="form-select" name="tipo" required>
                            <option value="ENTRADA">Entrada (Aumenta stock)</option>
                            <option value="SALIDA">Salida (Disminuye stock)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" min="1" class="form-control" name="cantidad" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observación</label>
                        <textarea class="form-control" name="observacion" rows="2" placeholder="Motivo del movimiento..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Registrar Movimiento</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>