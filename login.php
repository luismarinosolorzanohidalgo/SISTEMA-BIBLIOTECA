<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND rol = ?");
    $stmt->execute([$email, $rol]);
    $usuario = $stmt->fetch();

    if ($usuario && $password === $usuario['password']) { // Recuerda usar password_verify en producción
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        header("Location: panel.php");
        exit;
    } else {
        $error = "Correo, contraseña o rol incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Iniciar Sesión</title>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<style>
  body {
    background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .login-container {
    background: rgba(255 255 255 / 0.15);
    padding: 2.5rem 3rem;
    border-radius: 25px;
    box-shadow:
      8px 8px 30px rgba(0,0,0,0.4),
      -8px -8px 30px rgba(255,255,255,0.15);
    backdrop-filter: blur(20px);
    width: 100%;
    max-width: 400px;
    color: white;
  }
  h2 {
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
  }
  label {
    font-weight: 600;
  }
  input.form-control, select.form-control {
    background: rgba(255 255 255 / 0.2);
    border: none;
    color: white;
  }
  input.form-control:focus, select.form-control:focus {
    background: rgba(255 255 255 / 0.3);
    color: white;
    box-shadow: 0 0 10px #4ca1af;
  }
  .btn-primary {
    background: #0a9396;
    border: none;
    width: 100%;
    padding: 0.7rem;
    font-weight: 700;
    border-radius: 30px;
    transition: background-color 0.3s ease;
  }
  .btn-primary:hover {
    background: #005f73;
  }
  .alert-danger {
    background: #b00020cc;
    border: none;
    font-weight: 600;
    text-align: center;
  }
  .text-link {
    color: #a0e7e5;
    cursor: pointer;
    text-decoration: underline;
  }
  .options {
    margin-top: 1rem;
    display: flex;
    justify-content: space-between;
    font-weight: 600;
  }
  .options a {
    color: #a0e7e5;
    text-decoration: none;
  }
  .options a:hover {
    color: #ffd700;
  }
</style>
</head>
<body>

<div class="login-container" role="main" aria-labelledby="loginTitle">
  <h2 id="loginTitle"><i class="fa-solid fa-lock"></i> Iniciar Sesión</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="mb-3">
      <label for="email" class="form-label">Correo electrónico</label>
      <input type="email" id="email" name="email" class="form-control" required autocomplete="username" aria-required="true" />
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" aria-required="true" />
    </div>

    <div class="mb-4">
      <label for="rol" class="form-label">Selecciona tu rol</label>
      <select id="rol" name="rol" class="form-control" required aria-required="true">
        <option value="" disabled selected>-- Elige un rol --</option>
        <option value="admin">Administrador</option>
        <option value="usuario">Usuario</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary" aria-label="Iniciar sesión">Entrar</button>
  </form>

  <div class="options">
    <a href="olvidaste_contraseña.php" class="text-link" aria-label="Olvidé mi contraseña">¿Olvidaste tu contraseña?</a>
    <a href="registro.php" aria-label="Registrarse">Registrarse</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
