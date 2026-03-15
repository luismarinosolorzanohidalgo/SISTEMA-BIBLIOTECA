<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $email, $password]);

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Biblioteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Roboto', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1d1f27, #2d3040);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .register-box {
            background: #2c2f3f;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.6);
            width: 100%;
            max-width: 420px;
            animation: fadeIn 1s ease-in-out;
        }
        .register-box h3 {
            margin-bottom: 25px;
            text-align: center;
            font-weight: bold;
        }
        .form-control {
            background-color: #1f212b;
            border: 1px solid #444;
            color: #fff;
            transition: 0.3s ease;
        }
        .form-control:focus {
            border-color: #00c896;
            box-shadow: 0 0 5px rgba(0,200,150,0.6);
            background-color: #1f212b;
        }
        .input-group-text {
            background-color: #1f212b;
            border: 1px solid #444;
            color: #aaa;
        }
        .btn-register {
            background: linear-gradient(135deg, #007bff, #0056d2);
            border: none;
            font-weight: bold;
            transition: 0.3s ease-in-out;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #0056d2, #003da8);
        }
        a {
            color: #00c896;
        }
        a:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h3><i class="fas fa-user-plus me-2"></i>Crear Cuenta</h3>
        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Tu nombre" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" id="email" placeholder="correo@ejemplo.com" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-register w-100 mt-2"><i class="fas fa-user-plus me-1"></i>Registrarse</button>
        </form>
        <p class="text-center mt-3">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>
