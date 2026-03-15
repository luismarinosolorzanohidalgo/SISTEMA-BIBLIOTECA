<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Obtener nombre de la imagen antes de eliminar
    $stmt = $pdo->prepare("SELECT imagen FROM libros WHERE id = ?");
    $stmt->execute([$id]);
    $libro = $stmt->fetch();

    // Eliminar libro de la base de datos
    $delete = $pdo->prepare("DELETE FROM libros WHERE id = ?");
    $delete->execute([$id]);

    // Eliminar imagen física si existe
    if ($libro && !empty($libro['imagen']) && file_exists('imagenes_libros/' . $libro['imagen'])) {
        unlink('imagenes_libros/' . $libro['imagen']);
    }

    header("Location: libros.php?eliminado=1");
    exit();
}
?>
