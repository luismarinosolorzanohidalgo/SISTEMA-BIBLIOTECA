<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Solo admins verán botones especiales
$esAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';

// Parámetros de búsqueda y filtro
$titulo = $_GET['titulo'] ?? '';
$autor = $_GET['autor'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$sql = "SELECT * FROM libros WHERE 1=1";
$params = [];

if ($titulo !== '') {
    $sql .= " AND titulo LIKE :titulo";
    $params[':titulo'] = "%$titulo%";
}
if ($autor !== '') {
    $sql .= " AND autor LIKE :autor";
    $params[':autor'] = "%$autor%";
}
if ($categoria !== '') {
    $sql .= " AND categoria = :categoria";
    $params[':categoria'] = $categoria;
}

$sql .= " ORDER BY titulo";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$libros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <title>Catálogo de Libros</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #023e8a, #0077b6);
      color: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    nav {
      background: rgba(0, 0, 0, 0.7);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.4);
    }
    nav a {
      color: #fff;
      font-weight: 600;
      margin-right: 1.2rem;
      text-decoration: none;
    }
    nav a:hover {
      color: #ffd60a;
    }
    .title-section {
      text-align: center;
      margin: 2rem 0 1rem;
    }
    .title-section h1 {
      font-size: 3rem;
      font-weight: bold;
      color: #90e0ef;
      text-shadow: 2px 2px 10px #0077b6;
    }
    .admin-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 2rem;
    }
    .admin-buttons a {
      padding: 0.8rem 1.8rem;
      border-radius: 30px;
      background: #00b4d8;
      color: #fff;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .admin-buttons a:hover {
      background: #48cae4;
      color: #001d3d;
    }
    .container-libros {
      max-width: 1200px;
      margin: 0 auto 4rem;
      padding: 0 2rem;
      display: grid;
      gap: 2rem;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    .card-libro {
      background: #03045e;
      padding: 1.2rem;
      border-radius: 20px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.5);
      transition: transform 0.3s;
      cursor: pointer;
    }
    .card-libro:hover {
      transform: translateY(-5px);
    }
    .card-libro img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 15px;
      margin-bottom: 1rem;
    }
    .card-libro h3 {
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #caf0f8;
    }
    .card-libro p {
      margin: 0.2rem 0;
      font-size: 0.95rem;
      color: #ade8f4;
    }
    .footer {
      text-align: center;
      padding: 1rem;
      background: rgba(0, 0, 0, 0.4);
      color: #90e0ef;
      margin-top: auto;
    }
  </style>
</head>
<body>
  <nav>
    <div>
      <a href="panel.php"><i class="fa-solid fa-house"></i> Panel</a>
      <a href="perfil.php"><i class="fa-solid fa-user"></i> Perfil</a>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </div>
  </nav>

  <section class="title-section">
    <h1>📚 Catálogo de Libros</h1>
  </section>

  <?php if ($esAdmin): ?>
  <div class="admin-buttons">
    <a href="agregar_libro.php"><i class="fa-solid fa-plus"></i> Agregar Libro</a>
    <a href="stock_libros.php"><i class="fa-solid fa-boxes-stacked"></i> Ver Stock</a>
  </div>
  <?php endif; ?>

  <section class="container-libros">
    <?php if (empty($libros)): ?>
      <p style="color:white; text-align:center; grid-column: 1 / -1;">No se encontraron libros.</p>
    <?php else: ?>
      <?php foreach ($libros as $libro): ?>
        <div class="card-libro" onclick="window.location='detalle_libro.php?id=<?= $libro['id'] ?>'">
          <img src="imagenes_libros/<?= htmlspecialchars($libro['imagen'] ?: 'default.png') ?>" alt="Portada del libro">
          <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
          <p><strong>Autor:</strong> <?= htmlspecialchars($libro['autor']) ?></p>
          <p><strong>Año:</strong> <?= htmlspecialchars($libro['año']) ?></p>
          <p><strong>Categoría:</strong> <?= htmlspecialchars($libro['categoria']) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
<a href="index.php" class="btn-flotante" title="Volver al inicio">
  <i class="fa-solid fa-arrow-left"></i>
</a>

<style>
  .btn-flotante {
    position: fixed;
    bottom: 20px;
    left: 20px;
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: #fff;
    font-size: 1.2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 242, 254, 0.35);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    z-index: 1000;
  }

  .btn-flotante:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 242, 254, 0.55);
  }
</style>
  <div class="footer">
    © <?= date('Y') ?> Biblioteca LEO — Todos los derechos reservados.
  </div>
</body>
</html>
