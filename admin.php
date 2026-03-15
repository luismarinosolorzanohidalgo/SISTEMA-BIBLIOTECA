<?php
require 'auth.php';

// Solo admins y bibliotecarios pueden acceder
verificar_rol(['admin', 'bibliotecario']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Panel Administrativo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h1>Panel Administrativo</h1>
    <p>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']); ?>. Tu rol es: <?= htmlspecialchars($_SESSION['rol']); ?></p>

    <ul>
        <li><a href="usuarios.php">Gestionar usuarios</a></li>
        <li><a href="libros.php">Gestionar libros</a></li>
        <li><a href="prestamos.php">Gestionar préstamos</a></li>
    </ul>

    <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
</div>
</body>
</html>
