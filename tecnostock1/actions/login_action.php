<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if (!empty($correo) && !empty($clave)) {
        $sql = "SELECT * FROM usuario WHERE correo = :correo AND estado = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            header('Location: ../views/dashboard.php');
            exit();
        }
    }
    
    // Si falla, regresa al login con error
    header('Location: ../views/login.php?error=1');
    exit();
}