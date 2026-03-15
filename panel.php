<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$total_libros = $pdo->query("SELECT COUNT(*) FROM libros")->fetchColumn();
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_prestamos = $pdo->query("SELECT COUNT(*) FROM prestamos")->fetchColumn();
$total_categorias = $pdo->query("SELECT COUNT(DISTINCT categoria) FROM libros")->fetchColumn();
$stmt2 = $pdo->query("SELECT COUNT(*) FROM restablecimientos WHERE estado = 'pendiente'");
$total_restablecimientos = $stmt2->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel Biblioteca</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary: #3b82f6;
      --primary-dark: #1e3a8a;
      --accent: #facc15;
      --bg: #f9fafb;
      --dark-bg: #1e293b;
      --glass-bg: rgba(255, 255, 255, 0.1);
      --glass-border: rgba(255, 255, 255, 0.2);
      --shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #6366f1, #3b82f6);
      color: #fff;
      min-height: 100vh;
      display: flex;
    }

    aside {
      width: 260px;
      background: var(--primary-dark);
      padding: 2rem 1.5rem;
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100vh;
    }

    aside h2 {
      font-size: 1.8rem;
      margin-bottom: 2rem;
      text-align: center;
      font-weight: bold;
    }

    aside a {
      color: #fff;
      text-decoration: none;
      font-size: 1.05rem;
      padding: 0.9rem 1rem;
      border-radius: 12px;
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: 0.3s ease;
    }

    aside a:hover {
      background: var(--primary);
      color: var(--accent);
    }

    .mode-toggle {
      margin-top: auto;
      background: none;
      border: 2px solid #fff;
      border-radius: 8px;
      padding: 0.4rem 0.6rem;
      color: #fff;
      font-size: 1.5rem;
      cursor: pointer;
      transition: 0.3s ease;
      align-self: center;
    }

    .mode-toggle:hover {
      color: var(--accent);
      border-color: var(--accent);
    }

    main {
      margin-left: 260px;
      padding: 3rem 2.5rem;
      flex: 1;
    }

    .welcome-container {
      text-align: center;
      margin-bottom: 3rem;
    }

    .welcome-container h1 {
      font-size: 2.4rem;
      font-weight: bold;
      border-right: 3px solid var(--accent);
      white-space: nowrap;
      overflow: hidden;
      animation: typing 3s steps(40, end), blink 0.8s step-end infinite;
    }

    .welcome-container p {
      margin-top: 0.5rem;
      color: #e0e0e0;
      font-size: 1.1rem;
    }

    @keyframes typing {
      from { width: 0 }
      to { width: 100% }
    }

    @keyframes blink {
      50% { border-color: transparent; }
    }

    .stats-cards {
      display: flex;
      gap: 2rem;
      flex-wrap: wrap;
      justify-content: center;
    }

    .stat-card-link {
      text-decoration: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card-link:hover .stat-card {
      transform: translateY(-8px);
      box-shadow: var(--shadow);
    }

    .stat-card {
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      backdrop-filter: blur(14px);
      width: 220px;
      padding: 2rem;
      border-radius: 20px;
      color: #fff;
      text-align: center;
    }

    .stat-icon {
      font-size: 2.8rem;
      margin-bottom: 0.6rem;
      color: var(--accent);
    }

    .stat-number {
      font-size: 2.4rem;
      font-weight: bold;
    }

    .stat-label {
      font-size: 1rem;
      opacity: 0.85;
    }

    #chart-container {
      background: rgba(0,0,0,0.1);
      border-radius: 20px;
      padding: 2rem;
      margin-top: 3rem;
      max-width: 900px;
      margin-left: auto;
      margin-right: auto;
    }

    footer {
      text-align: center;
      margin-top: 3rem;
      font-size: 0.95rem;
      color: #ddd;
    }

    @media (max-width: 768px) {
      aside {
        position: relative;
        width: 100%;
        height: auto;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        padding: 1rem;
      }

      main {
        margin-left: 0;
        padding: 2rem 1rem;
      }

      .stat-card {
        width: 100%;
        max-width: 320px;
      }

      .welcome-container h1 {
        font-size: 1.6rem;
      }

      .welcome-container p {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
<aside>
  <h2>Biblioteca</h2>
  <a href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
  <a href="libros.php"><i class="fa-solid fa-book"></i> Libros</a>
  <a href="usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a>
  <a href="prestamos.php"><i class="fa-solid fa-book-reader"></i> Préstamos</a>
  <a href="perfil.php"><i class="fa-solid fa-user"></i> Perfil</a>
  <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
  <button class="mode-toggle" id="modeToggle"><i class="fa-regular fa-moon"></i></button>
</aside>

<main>
  <div class="welcome-container">
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION["nombre"] ?? 'Usuario') ?>!</h1>
    <p>Administra tu biblioteca de forma fácil y moderna</p>
  </div>

  <section class="stats-cards">
    <a href="libros.php" class="stat-card-link">
      <div class="stat-card">
        <i class="fa-solid fa-book stat-icon"></i>
        <div class="stat-number"><?= $total_libros ?></div>
        <div class="stat-label">Libros</div>
      </div>
    </a>

    <a href="usuarios.php" class="stat-card-link">
      <div class="stat-card">
        <i class="fa-solid fa-users stat-icon"></i>
        <div class="stat-number"><?= $total_usuarios ?></div>
        <div class="stat-label">Usuarios</div>
      </div>
    </a>

    <a href="categorias.php" class="stat-card-link">
      <div class="stat-card">
        <i class="fa-solid fa-list stat-icon"></i>
        <div class="stat-number"><?= $total_categorias ?></div>
        <div class="stat-label">Categorías</div>
      </div>
    </a>

    <a href="mis_prestamos.php" class="stat-card-link">
      <div class="stat-card">
        <i class="fa-solid fa-book-reader stat-icon"></i>
        <div class="stat-number">
          <?php
            $usuario_id = $_SESSION['usuario_id'];
            $stmt_prestamos = $pdo->prepare("SELECT COUNT(*) FROM prestamos WHERE usuario_id = ?");
            $stmt_prestamos->execute([$usuario_id]);
            echo $stmt_prestamos->fetchColumn();
          ?>
        </div>
        <div class="stat-label">Mis Préstamos</div>
      </div>
    </a>
  </section>

  <section id="chart-container">
    <canvas id="prestamosChart"></canvas>
  </section>

  <footer>
    Biblioteca Super Max © 2025
  </footer>
</main>

<script>
  const modeToggle = document.getElementById('modeToggle');
  const body = document.body;
  modeToggle.addEventListener('click', () => {
    body.classList.toggle('dark');
    modeToggle.innerHTML = body.classList.contains('dark')
      ? '<i class="fa-regular fa-sun"></i>'
      : '<i class="fa-regular fa-moon"></i>';
  });

  const ctx = document.getElementById('prestamosChart').getContext('2d');
  const prestamosChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
      datasets: [{
        label: 'Préstamos por mes',
        data: [12, 19, 10, 15, 20, 25],
        backgroundColor: 'rgba(255, 255, 255, 0.3)',
        borderColor: '#fff',
        borderWidth: 1,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: '#fff'
          }
        },
        x: {
          ticks: {
            color: '#fff'
          }
        }
      },
      plugins: {
        legend: {
          labels: {
            color: '#fff'
          }
        }
      }
    }
  });
</script>
</body>
</html>
