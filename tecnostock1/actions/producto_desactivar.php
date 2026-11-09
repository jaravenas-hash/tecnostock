<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}
require_once '../config/conexion.php';

$id = $_GET['id'] ?? '';
$activar = $_GET['activar'] ?? 0;
$nuevoEstado = $activar == 1 ? 1 : 0;

if (!empty($id)) {
    $stmt = $pdo->prepare("UPDATE productos SET estado = :estado WHERE id_producto = :id");
    $stmt->execute([':estado' => $nuevoEstado, ':id' => $id]);
    $msg = $nuevoEstado == 1 ? "Producto activado." : "Producto desactivado correctamente.";
    header("Location: ../views/productos.php?msg=" . urlencode($msg));
    exit();
}
header("Location: ../views/productos.php");
exit();