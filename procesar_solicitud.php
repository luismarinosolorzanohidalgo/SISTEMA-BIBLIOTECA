<?php
session_start();
require 'database.php';

// Verificar si es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prestamo_id'], $_POST['accion'])) {
    $prestamo_id = (int) $_POST['prestamo_id'];
    $accion = $_POST['accion'];

    if ($accion === 'aprobar') {
        $nuevo_estado = 'aprobado';
    } elseif ($accion === 'rechazar') {
        $nuevo_estado = 'rechazado';
    } else {
        header("Location: admin_prestamos.php?alert=accion_invalida");
        exit;
    }

    // Verificar que la solicitud exista y esté pendiente
    $stmtCheck = $pdo->prepare("SELECT estado FROM prestamos WHERE id = ?");
    $stmtCheck->execute([$prestamo_id]);
    $prestamo = $stmtCheck->fetch();

    if (!$prestamo) {
        header("Location: admin_prestamos.php?alert=prestamo_no_encontrado");
        exit;
    }

    if ($prestamo['estado'] !== 'pendiente') {
        header("Location: admin_prestamos.php?alert=prestamo_no_pendiente");
        exit;
    }

    // Actualizar estado
    $stmtUpdate = $pdo->prepare("UPDATE prestamos SET estado = ? WHERE id = ?");
    $stmtUpdate->execute([$nuevo_estado, $prestamo_id]);

    header("Location: admin_prestamos.php?success=solicitud_{$nuevo_estado}");
    exit;
} else {
    header("Location: admin_prestamos.php?alert=datos_invalidos");
    exit;
}
