<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST["titulo"];
    $autor = $_POST["autor"];
    $año = $_POST["año"];
    $categoria = $_POST["categoria"];
    $genero = $_POST["genero"];
    $descripcion = $_POST["descripcion"];
    $imagen = "";

    if (!empty($_FILES["imagen"]["name"])) {
        $targetDir = "imagenes_libros/";
        $imagen = basename($_FILES["imagen"]["name"]);
        $targetFilePath = $targetDir . $imagen;
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $targetFilePath);
    }

    $stmt = $pdo->prepare("INSERT INTO libros (titulo, autor, año, categoria, genero, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$titulo, $autor, $año, $categoria, $genero, $descripcion, $imagen])) {
        $mensaje = "Libro agregado correctamente.";
    } else {
        $mensaje = "Error al agregar libro.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Agregar Libro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: white;
      padding-bottom: 50px;
    }
    .container {
      background-color: rgba(255,255,255,0.05);
      padding: 3rem;
      margin-top: 3rem;
      border-radius: 20px;
      box-shadow: 0 0 30px rgba(0,0,0,0.4);
      backdrop-filter: blur(15px);
    }
    h1 {
      text-align: center;
      margin-bottom: 2rem;
      font-weight: 700;
      color: #fff;
    }
    .btn-custom {
      background-color: #00b894;
      border: none;
      padding: 0.8rem 2rem;
      font-weight: 600;
      color: #fff;
      border-radius: 30px;
      transition: background-color 0.3s ease;
    }
    .btn-custom:hover {
      background-color: #019875;
    }
    .form-label {
      font-weight: 600;
    }
    .alert {
      text-align: center;
    }
    a.volver {
      display: block;
      text-align: center;
      margin-top: 2rem;
      color: #81ecec;
      text-decoration: none;
    }
    a.volver:hover {
      color: #00cec9;
    }
  </style>
</head>
<body>

<div class="container">
  <h1><i class="fa-solid fa-plus"></i> Agregar Nuevo Libro</h1>

  <?php if ($mensaje): ?>
    <div class="alert alert-info"><?php echo $mensaje; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" name="titulo" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Autor</label>
      <input type="text" name="autor" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Año</label>
      <input type="number" name="año" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Categoría</label>
      <input type="text" name="categoria" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Género</label>
      <input type="text" name="genero" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Imagen del libro</label>
      <input type="file" name="imagen" class="form-control">
    </div>
    <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-save"></i> Guardar</button>
  </form>

  <a href="libros.php" class="volver"><i class="fa-solid fa-arrow-left"></i> Volver a Libros</a>
</div>

</body>
</html>
