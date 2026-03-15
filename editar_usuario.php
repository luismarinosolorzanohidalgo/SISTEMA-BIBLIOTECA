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

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? '';

    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";
    if (!in_array($rol, ['admin', 'usuario'])) $errors[] = "Rol inválido.";

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetchColumn() > 0) $errors[] = "El correo ya está en uso.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
        $stmt->execute([$nombre, $email, $rol, $id]);
        $success = true;
        $usuario['nombre'] = $nombre;
        $usuario['email'] = $email;
        $usuario['rol'] = $rol;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Editar Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #121212;
            color: #f1f1f1;
            font-family: 'Segoe UI', sans-serif;
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

        h1 {
            font-weight: 700;
            color: #00d2ff;
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #ccc;
        }

        .form-control {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #555;
        }

        .form-select {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #555;
        }

        .btn-primary {
            background-color: #00d2ff;
            border: none;
        }

        .alert {
            border-radius: 10px;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="container">
    <h1><i class="fa-solid fa-user-pen"></i> Editar Usuario</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Usuario actualizado correctamente.</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required value="<?= htmlspecialchars($usuario['nombre']) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select id="rol" name="rol" class="form-select" required>
                <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
        <a href="usuarios.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
    </form>
</div>

</body>
</html>
