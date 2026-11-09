<?php
session_start();


if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}


require_once '../config/conexion.php';


$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    if ($busqueda !== '') {
        
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.estado = 1 AND (p.nombre LIKE :busq OR p.código LIKE :busq OR c.nombre LIKE :busq)
                ORDER BY p.id_producto DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['busq' => "%$busqueda%"]);
    } else {
        
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM producto p
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.estado = 1 
                ORDER BY p.id_producto DESC";
        $stmt = $pdo->query($sql);
    }
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar los productos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoStock - Panel Principal</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">TecnoStock</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Hola, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></span>
                
                <a href="../actions/logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <h2>Inventario de Productos</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="productos.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nuevo Producto</a>
                <a href="movimientos.php" class="btn btn-primary"><i class="bi bi-arrow-left-right"></i> Registrar Movimiento</a>
            </div>
        </div>

        
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="dashboard.php" method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre, código o categoría..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-secondary">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Estado / Alerta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $prod): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prod['código']); ?></td>
                                        <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($prod['categoria_nombre']); ?></td>
                                        <td>$<?php echo number_format($prod['precio'], 0, ',', '.'); ?></td>
                                        <td><strong><?php echo $prod['stock_actual']; ?></strong></td>
                                        <td><?php echo $prod['stock_mínimo']; ?></td>
                                        <td>
                                            
                                            <?php if ($prod['stock_actual'] < $prod['stock_mínimo']): ?>
                                                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> Stock Bajo</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Óptimo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="productos.php?id=<?php echo $prod['id_producto']; ?>" class="btn btn-warning btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>
                                            <a href="../actions/producto_desactivar.php?id=<?php echo $prod['id_producto']; ?>" class="btn btn-danger btn-sm" title="Desactivar" onclick="return confirm('¿Estás seguro de desactivar este producto?');"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No se encontraron productos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>