<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: libros.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->execute([$id]);
$libro = $stmt->fetch();

if (!$libro) {
    echo "Libro no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Libro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      color: white;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      margin-top: 3rem;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 0 30px rgba(0,0,0,0.4);
    }
    label {
      font-weight: 600;
    }
    input, select, textarea {
      border-radius: 10px !important;
    }
    .btn-primary {
      background: #ffd700;
      border: none;
      color: #333;
      font-weight: 700;
    }
    .btn-primary:hover {
      background: #ffc300;
    }
  </style>
</head>
<body>
<div class="container">
  <h2 class="text-center mb-4">Editar Libro</h2>
  <form action="procesar_edicion_libro.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $libro['id'] ?>">
    <input type="hidden" name="imagen_actual" value="<?= $libro['imagen'] ?>">

    <div class="mb-3">
      <label for="titulo">Título</label>
      <input type="text" class="form-control" name="titulo" required value="<?= htmlspecialchars($libro['titulo']) ?>">
    </div>

    <div class="mb-3">
      <label for="autor">Autor</label>
      <input type="text" class="form-control" name="autor" required value="<?= htmlspecialchars($libro['autor']) ?>">
    </div>

    <div class="mb-3">
      <label for="año">Año</label>
      <input type="number" class="form-control" name="año" required value="<?= $libro['año'] ?>">
    </div>

    <div class="mb-3">
      <label for="categoria">Categoría</label>
      <input type="text" class="form-control" name="categoria" required value="<?= htmlspecialchars($libro['categoria']) ?>">
    </div>

    <div class="mb-3">
      <label for="genero">Género</label>
      <input type="text" class="form-control" name="genero" required value="<?= htmlspecialchars($libro['genero']) ?>">
    </div>

    <div class="mb-3">
      <label for="descripcion">Descripción</label>
      <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($libro['descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="imagen">Portada (opcional)</label><br>
      <?php if (!empty($libro['imagen']) && file_exists('imagenes_libros/' . $libro['imagen'])): ?>
        <img src="imagenes_libros/<?= $libro['imagen'] ?>" alt="Portada" style="max-height:120px; border-radius:10px; margin-bottom:1rem;">
      <?php endif; ?>
      <input type="file" class="form-control mt-2" name="imagen">
    </div>

    <button type="submit" class="btn btn-primary w-100 mt-3">Guardar Cambios</button>
    <a href="detalle_libro.php?id=<?= $libro['id'] ?>" class="btn btn-secondary w-100 mt-2">Cancelar</a>
  </form>
</div>
</body>
</html>
