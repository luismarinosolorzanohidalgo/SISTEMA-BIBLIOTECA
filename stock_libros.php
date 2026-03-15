<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$libros = $pdo->query("SELECT * FROM libros ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Stock de Libros</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(120deg, #1a1a2e, #16213e);
      color: white;
      padding-bottom: 50px;
    }
    .container {
      margin-top: 3rem;
      padding: 2rem;
      background-color: rgba(255,255,255,0.05);
      border-radius: 15px;
      box-shadow: 0 0 30px rgba(0,0,0,0.4);
    }
    h1 {
      text-align: center;
      font-weight: 800;
      margin-bottom: 2rem;
      color: #fff;
    }
    table {
      background-color: rgba(255,255,255,0.03);
    }
    thead th {
      background-color: #2d3436;
      color: #81ecec;
      text-align: center;
    }
    td {
      vertical-align: middle;
      color: #dfe6e9;
      text-align: center;
    }
    .img-thumb {
      width: 60px;
      height: 90px;
      object-fit: cover;
      border-radius: 8px;
    }
    a.volver {
      display: block;
      text-align: center;
      margin-top: 2rem;
      color: #74b9ff;
      text-decoration: none;
      font-weight: 600;
    }
    a.volver:hover {
      color: #0984e3;
    }
    .stock {
      font-weight: bold;
      color: #00cec9;
    }
  </style>
</head>
<body>
<div class="container">
  <h1><i class="fa-solid fa-book"></i> Stock de Libros</h1>

  <div class="table-responsive">
    <table class="table table-bordered table-hover table-dark align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Imagen</th>
          <th>Título</th>
          <th>Autor</th>
          <th>Categoría</th>
          <th>Año</th>
          <th>Stock</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($libros as $libro): ?>
          <tr>
            <td><?= $libro["id"] ?></td>
            <td>
              <?php if (!empty($libro["imagen"]) && file_exists("imagenes_libros/" . $libro["imagen"])): ?>
                <img src="imagenes_libros/<?= htmlspecialchars($libro["imagen"]) ?>" class="img-thumb" alt="Portada del libro">
              <?php else: ?>
                <i class="fa-solid fa-image-slash text-secondary fs-3"></i>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($libro["titulo"]) ?></td>
            <td><?= htmlspecialchars($libro["autor"]) ?></td>
            <td><?= htmlspecialchars($libro["categoria"]) ?></td>
            <td><?= htmlspecialchars($libro["año"]) ?></td>
            <td class="stock"><?= htmlspecialchars($libro["stock"]) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <a href="panel.php" class="volver"><i class="fa-solid fa-arrow-left"></i> Volver al Panel</a>
</div>
</body>
</html>
