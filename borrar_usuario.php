<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: usuarios.php");
    exit;
}

$id = (int)$_GET['id'];

// Aquí podrías agregar lógica para evitar que un usuario se borre a sí mismo
if ($id === $_SESSION['usuario_id']) {
    // Opcional: impedir borrarse a uno mismo
    header("Location: usuarios.php?error=1");
    exit;
}

// Borrar usuario
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

header("Location: usuarios.php?deleted=1");
exit;
