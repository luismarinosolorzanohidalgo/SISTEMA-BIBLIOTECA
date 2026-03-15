<?php
session_start();
require 'database.php';

// Solo permitir acceso a administradores
if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Obtener totales
$totalLibros = $pdo->query("SELECT COUNT(*) FROM libros")->fetchColumn();
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalPrestamos = $pdo->query("SELECT COUNT(*) FROM prestamos")->fetchColumn();
$totalSolicitudes = $pdo->query("SELECT COUNT(*) FROM solicitudes_recuperacion")->fetchColumn();
$totalRestablecimientos = $pdo->query("SELECT COUNT(*) FROM restablecimientos")->fetchColumn();
$totalHistorial = $pdo->query("SELECT COUNT(*) FROM historial_restables")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel Administrativo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: #f0f4f8;
      min-height: 100vh;
      padding-top: 70px;
    }

    header {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      background: rgba(20, 35, 50, 0.85);
      backdrop-filter: blur(10px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.8rem 2rem;
      z-index: 1000;
      box-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: #ffb347;
      text-shadow: 0 0 5px #ffb347aa;
    }

    nav button {
      background: transparent;
      border: 2px solid transparent;
      color: #f0f4f8;
      font-weight: 600;
      padding: 0.5rem 1.2rem;
      border-radius: 30px;
      margin-left: 0.5rem;
      transition: 0.3s ease;
      cursor: pointer;
    }

    nav button:hover {
      background: #ffb347;
      color: #111;
      box-shadow: 0 0 10px #ffb347;
    }

    main {
      padding: 2rem;
      max-width: 1200px;
      margin: auto;
    }

    h1 {
      text-align: center;
      font-size: 2.8rem;
      color: #fff;
      margin-bottom: 1.5rem;
      text-shadow: 1px 1px 8px rgba(0,0,0,0.5);
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
    }

    .card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
      border: 1px solid rgba(255,255,255,0.15);
      box-shadow: 0 8px 30px rgba(0,0,0,0.4);
      cursor: pointer;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.6);
    }

    .card i {
      font-size: 3.5rem;
      color: #ffb347;
      margin-bottom: 1rem;
      text-shadow: 0 0 6px #ffb347aa;
    }

    .card h2 {
      font-size: 2.5rem;
      color: #fff;
      margin: 0.5rem 0;
    }

    .card p {
      font-size: 1.1rem;
      color: #dcdde1;
      font-weight: 500;
    }

    footer {
      text-align: center;
      color: #8fabc5;
      font-size: 0.9rem;
      margin-top: 3rem;
      padding-bottom: 1rem;
    }

    @media(max-width: 480px) {
      .logo {
        font-size: 1.3rem;
      }
      h1 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="logo">Panel Admin</div>
  <nav>
    <button onclick="location.href='panel.php'">Panel</button>
    <button onclick="location.href='libros.php'">Libros</button>
    <button onclick="location.href='usuarios.php'">Usuarios</button>
    <button onclick="location.href='logout.php'" style="background:#e74c3c; color:white;">Salir</button>
  </nav>
</header>

<main>
  <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></h1>

  <div class="grid">
    <div class="card" onclick="location.href='libros.php'">
      <i class="fa-solid fa-book"></i>
      <h2><?php echo $totalLibros; ?></h2>
      <p>Libros registrados</p>
    </div>

    <div class="card" onclick="location.href='usuarios.php'">
      <i class="fa-solid fa-users"></i>
      <h2><?php echo $totalUsuarios; ?></h2>
      <p>Usuarios registrados</p>
    </div>

    <div class="card" onclick="location.href='prestamos.php'">
      <i class="fa-solid fa-book-reader"></i>
      <h2><?php echo $totalPrestamos; ?></h2>
      <p>Préstamos activos</p>
    </div>

    <div class="card" onclick="location.href='admin_prestamos.php'">
      <i class="fa-solid fa-envelope-open-text"></i>
      <h2><?php echo $totalSolicitudes; ?></h2>
      <p>Solicitudes recibidas</p>
    </div>

    <div class="card" onclick="location.href='admin/restablecimientos.php'">
      <i class="fa-solid fa-unlock-keyhole"></i>
      <h2><?php echo $totalRestablecimientos; ?></h2>
      <p>Restablecimientos pendientes</p>
    </div>

    <div class="card" onclick="location.href='historial_restables.php'">
      <i class="fa-solid fa-clock-rotate-left"></i>
      <h2><?php echo $totalHistorial; ?></h2>
      <p>Historial de cambios</p>
    </div>
  </div>
</main>

<footer>
  &copy; <?php echo date('Y'); ?> Biblioteca | Panel Administrativo
</footer>

</body>
</html>
