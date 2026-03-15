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

$stmt = $pdo->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: usuarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Detalle del Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #121212;
            color: #f1f1f1;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 4rem auto;
            background: #1e1e1e;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.6);
            animation: fadeIn 0.6s ease-in-out;
        }

        .info-label {
            font-weight: 600;
            margin-top: 1rem;
            color: #bbbbbb;
        }

        .info-value {
            font-size: 1.1rem;
            color: #ffffff;
        }

        h1 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #00d2ff;
            text-shadow: 1px 1px #000;
        }

        .btn-back {
            margin-top: 2rem;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="container">
    <h1><i class="fa-solid fa-user"></i> Detalles del Usuario</h1>
    <div class="info-label">ID:</div>
    <div class="info-value"><?= htmlspecialchars($usuario['id']) ?></div>

    <div class="info-label">Nombre:</div>
    <div class="info-value"><?= htmlspecialchars($usuario['nombre']) ?></div>

    <div class="info-label">Correo:</div>
    <div class="info-value"><?= htmlspecialchars($usuario['email']) ?></div>

    <div class="info-label">Rol:</div>
    <div class="info-value"><?= htmlspecialchars($usuario['rol']) ?></div>

    <a href="usuarios.php" class="btn btn-outline-light btn-back"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

</body>
</html>
