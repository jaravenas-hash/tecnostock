<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
require_once '../config/conexion.php';

// Obtener categorías para el selector (select)
$stmt_cat = $pdo->query("SELECT * FROM categoria WHERE estado = 1");
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Variables para edición (si viene un ?id=...)
$id_producto = $_GET['id'] ?? null;
$codigo = '';
$nombre = '';
$descripcion = '';
$precio = '';
$stock_actual = '';
$stock_minimo = '';
$id_categoria = '';
$modo_edicion = false;

if ($id_producto) {
    $modo_edicion = true;
    $stmt = $pdo->prepare("SELECT * FROM producto WHERE id_producto = :id");
    $stmt->execute(['id' => $id_producto]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prod) {
        $codigo = $prod['código'];
        $nombre = $prod['nombre'];
        $descripcion = $prod['descripción'];
        $precio = $prod['precio'];
        $stock_actual = $prod['stock_actual'];
        $stock_minimo = $prod['stock_mínimo'];
        $id_categoria = $prod['id_categoria'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoStock - <?php echo $modo_edicion ? 'Editar' : 'Nuevo'; ?> Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">TecnoStock - Gestión de Productos</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Volver al Dashboard</a>
        </div>
    </nav>

    <div class="container col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="mb-4"><?php echo $modo_edicion ? 'Editar Producto' : 'Registrar Nuevo Producto'; ?></h3>
                
                <form action="../actions/producto_guardar.php" method="POST">
                    <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">

                    <div class="mb-3">
                        <label class="form-label">Código del Producto</label>
                        <input type="text" class="form-control" name="codigo" value="<?php echo htmlspecialchars($codigo); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="id_categoria" required>
                            <option value="">Seleccione una categoría...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($id_categoria == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="2"><?php echo htmlspecialchars($descripcion); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio ($)</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="precio" value="<?php echo htmlspecialchars($precio); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Actual</label>
                            <input type="number" min="0" class="form-control" name="stock_actual" value="<?php echo htmlspecialchars($stock_actual); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Mínimo</label>
                            <input type="number" min="0" class="form-control" name="stock_minimo" value="<?php echo htmlspecialchars($stock_minimo); ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100"><?php echo $modo_edicion ? 'Actualizar Producto' : 'Guardar Producto'; ?></button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>