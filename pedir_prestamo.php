<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['libro_id']) && is_numeric($_POST['libro_id'])) {
    $libro_id = (int) $_POST['libro_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Verificar que el libro exista
    $stmtLibro = $pdo->prepare("SELECT COUNT(*) FROM libros WHERE id = ?");
    $stmtLibro->execute([$libro_id]);
    if ($stmtLibro->fetchColumn() == 0) {
        // Libro no válido
        header("Location: libros.php?error=libro_no_encontrado");
        exit;
    }

    // Comprobar si ya hay una solicitud pendiente para este libro y usuario
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM prestamos WHERE libro_id = ? AND usuario_id = ? AND estado = 'pendiente'");
    $stmtCheck->execute([$libro_id, $usuario_id]);
    if ($stmtCheck->fetchColumn() > 0) {
        header("Location: detalle_libro.php?id=" . $libro_id . "&alert=ya_solicitado");
        exit;
    }

    // Fechas para préstamo y devolución
    $fecha_prestamo = date('Y-m-d');
    $fecha_devolucion = date('Y-m-d', strtotime('+14 days'));
    $estado = 'pendiente'; // Estado inicial de la solicitud

    // Insertar solicitud de préstamo con estado pendiente
    $stmt = $pdo->prepare("INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, fecha_devolucion, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$libro_id, $usuario_id, $fecha_prestamo, $fecha_devolucion, $estado]);

    header("Location: mis_prestamos.php?success=solicitud_enviada");
    exit;

} else {
    header("Location: libros.php?error=acceso_invalido");
    exit;
}
