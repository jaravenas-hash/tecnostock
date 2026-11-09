<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../views/login.php');
    exit();
}
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'] ?? null;
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];
    $id_categoria = $_POST['id_categoria'];

    if (!empty($codigo) && !empty($nombre) && $precio >= 0 && $stock_actual >= 0 && $stock_minimo >= 0) {
        if ($id_producto) {
            // Actualizar producto existente
            $sql = "UPDATE producto SET código = :codigo, nombre = :nombre, descripción = :descripcion, precio = :precio, stock_actual = :stock_actual, stock_mínimo = :stock_minimo, id_categoria = :id_categoria WHERE id_producto = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'stock_actual' => $stock_actual,
                'stock_minimo' => $stock_minimo,
                'id_categoria' => $id_categoria,
                'id' => $id_producto
            ]);
        } else {
            // Insertar nuevo producto
            $sql = "INSERT INTO producto (código, nombre, descripción, precio, stock_actual, stock_mínimo, id_categoria, estado) VALUES (:codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, :id_categoria, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'stock_actual' => $stock_actual,
                'stock_minimo' => $stock_minimo,
                'id_categoria' => $id_categoria
            ]);
        }
    }
    header('Location: ../views/dashboard.php');
    exit();
}