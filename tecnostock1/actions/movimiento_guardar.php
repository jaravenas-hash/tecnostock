<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../views/login.php');
    exit();
}
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    $cantidad = intval($_POST['cantidad'] ?? 0);
    $observacion = trim($_POST['observacion'] ?? '');
    $id_usuario = $_SESSION['id_usuario'];

    if ($id_producto && $tipo && $cantidad > 0) {
        try {
           
            $pdo->beginTransaction();

            
            $stmt_prod = $pdo->prepare("SELECT stock_actual FROM producto WHERE id_producto = :id");
            $stmt_prod->execute(['id' => $id_producto]);
            $producto = $stmt_prod->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                throw new Exception("Producto no encontrado.");
            }

            $stock_actual = intval($producto['stock_actual']);
            $nuevo_stock = $stock_actual;

            if ($tipo === 'ENTRADA') {
                $nuevo_stock += $cantidad;
            } elseif ($tipo === 'SALIDA') {
                $nuevo_stock -= $cantidad;
                
                if ($nuevo_stock < 0) {
                    $pdo->rollBack();
                    header('Location: ../views/movimientos.php?error=stock_insuficiente');
                    exit();
                }
            }

            
            $stmt_update = $pdo->prepare("UPDATE producto SET stock_actual = :nuevo_stock WHERE id_producto = :id");
            $stmt_update->execute([
                'nuevo_stock' => $nuevo_stock,
                'id' => $id_producto
            ]);

            
            $stmt_mov = $pdo->prepare("INSERT INTO movimiento (tipo, cantidad, observación, id_producto, id_usuario) VALUES (:tipo, :cantidad, :observacion, :id_producto, :id_usuario)");
            $stmt_mov->execute([
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'observacion' => $observacion,
                'id_producto' => $id_producto,
                'id_usuario' => $id_usuario
            ]);

            
            $pdo->commit();
            header('Location: ../views/dashboard.php');
            exit();

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            header('Location: ../views/movimientos.php?error=bd');
            exit();
        }
    }
}
header('Location: ../views/movimientos.php');
exit();