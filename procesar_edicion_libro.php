<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $año = trim($_POST['año']);
    $categoria = trim($_POST['categoria']);
    $genero = trim($_POST['genero']);
    $descripcion = trim($_POST['descripcion']);

    // Imagen
    $nombreImagen = $_POST['imagen_actual'];
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = $_FILES['imagen'];
        $ext = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreImagen = uniqid('libro_') . "." . $ext;
        move_uploaded_file($imagen['tmp_name'], "imagenes_libros/" . $nombreImagen);
    }

    $stmt = $pdo->prepare("UPDATE libros SET titulo = ?, autor = ?, año = ?, categoria = ?, genero = ?, descripcion = ?, imagen = ? WHERE id = ?");
    $stmt->execute([$titulo, $autor, $año, $categoria, $genero, $descripcion, $nombreImagen, $id]);

    header("Location: detalle_libro.php?id=" . $id);
    exit();
}
?>
