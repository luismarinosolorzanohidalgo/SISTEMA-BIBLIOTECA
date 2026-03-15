<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: libros.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->execute([$id]);
$libro = $stmt->fetch();

if (!$libro) {
    header("Location: libros.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Detalle del Libro - <?php echo htmlspecialchars($libro['titulo']); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);
      color: white;
      min-height: 100vh;
      margin: 0;
    }
    nav {
      background: rgba(255 255 255 / 0.15);
      backdrop-filter: blur(15px);
      padding: 1rem 2rem;
      box-shadow: 0 3px 15px rgba(0,0,0,0.25);
    }
    nav a {
      color: #e0e0e0;
      font-weight: 600;
      text-decoration: none;
      margin-right: 2rem;
      transition: color 0.3s ease;
    }
    nav a:hover {
      color: #ffd700;
    }
    .container {
      max-width: 720px;
      background: rgba(255 255 255 / 0.15);
      border-radius: 25px;
      box-shadow: 8px 8px 30px rgba(0,0,0,0.4), -8px -8px 30px rgba(255,255,255,0.15);
      padding: 3rem 3rem 4rem;
      margin: 3rem auto 4rem;
      backdrop-filter: blur(20px);
    }
    h1 {
      font-weight: 800;
      font-size: 2.8rem;
      color: #fff;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
      margin-bottom: 1rem;
      text-align: center;
    }
    .category-badge {
      display: inline-block;
      background: #005f73;
      color: #e0fbfc;
      font-weight: 700;
      font-size: 1rem;
      padding: 0.4rem 1rem;
      border-radius: 20px;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 2rem;
      box-shadow: 0 4px 8px rgb(0 95 115 / 0.6);
    }
    .detail-item {
      font-size: 1.2rem;
      margin-bottom: 1rem;
      color: #d0e8e8;
    }
    .detail-item strong {
      color: #b2f7ff;
    }
    p.description {
      font-size: 1.15rem;
      line-height: 1.6;
      color: #c7e9fb;
      margin-top: 1.5rem;
      text-align: justify;
    }
    .btn-back {
      display: block;
      margin: 2rem auto 0;
      background: #0a9396;
      color: white;
      font-weight: 700;
      border: none;
      border-radius: 30px;
      padding: 0.7rem 2.5rem;
      text-align: center;
      text-decoration: none;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 12px #0a9396aa;
    }
    .btn-back:hover {
      background: #005f73;
      box-shadow: 0 6px 15px #005f73cc;
      color: #ffd700;
    }
    .btn-edit {
      display: block;
      margin: 1rem auto 2rem;
      background: #ffc107;
      color: #000;
      font-weight: 700;
      border: none;
      border-radius: 30px;
      padding: 0.6rem 2rem;
      text-align: center;
      text-decoration: none;
      transition: 0.3s ease;
      box-shadow: 0 4px 12px rgba(255,193,7,0.6);
    }
    .btn-edit:hover {
      background: #ff9800;
      color: white;
    }
    footer {
      text-align: center;
      color: #c7d1d9;
      font-weight: 600;
      margin-top: 4rem;
      font-size: 1rem;
      opacity: 0.8;
    }
  </style>
</head>
<body>
<nav>
  <a href="libros.php"><i class="fa-solid fa-arrow-left"></i> Volver a Libros</a>
  <a href="panel.php"><i class="fa-solid fa-user-cog"></i> Panel</a>
  <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
</nav>

<div class="container" role="main">
  <h1><?php echo htmlspecialchars($libro['titulo']); ?></h1>
  <span class="category-badge"><?php echo htmlspecialchars($libro['categoria']); ?></span>
  <span class="category-badge" style="background:#008080;"><?php echo htmlspecialchars($libro['genero']); ?></span>

  <?php if (!empty($libro['imagen']) && file_exists('imagenes_libros/' . $libro['imagen'])): ?>
    <div style="text-align:center; margin:2rem 0;">
      <img src="imagenes_libros/<?php echo htmlspecialchars($libro['imagen']); ?>" 
           alt="Portada del libro <?php echo htmlspecialchars($libro['titulo']); ?>" 
           style="max-width: 100%; max-height: 300px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); object-fit: cover;">
    </div>
  <?php endif; ?>

 <?php if ($_SESSION['rol'] === 'admin'): ?>
  <div class="d-grid gap-2 mt-4">
    <a href="editar_libro.php?id=<?= $libro['id'] ?>" class="btn btn-warning">
      <i class="fa-solid fa-pen-to-square"></i> Editar Libro
    </a>
    <form action="eliminar_libro.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro? Esta acción no se puede deshacer.')">
      <input type="hidden" name="id" value="<?= $libro['id'] ?>">
      <button type="submit" class="btn btn-danger">
        <i class="fa-solid fa-trash"></i> Eliminar Libro
      </button>
    </form>
  </div>
<?php endif; ?>



  <p class="detail-item"><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
  <p class="detail-item"><strong>Año:</strong> <?php echo htmlspecialchars($libro['año']); ?></p>
  <p class="detail-item"><strong>Descripción:</strong></p>
  <p class="description"><?php echo nl2br(htmlspecialchars($libro['descripcion'])); ?></p>

  <?php
  $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM prestamos WHERE libro_id = ? AND usuario_id = ? AND estado = 'pendiente'");
  $stmtCheck->execute([$libro['id'], $_SESSION['usuario_id']]);
  $yaSolicitado = $stmtCheck->fetchColumn() > 0;
  ?>

  <?php if (!$yaSolicitado): ?>
    <form method="POST" action="pedir_prestamo.php">
      <input type="hidden" name="libro_id" value="<?php echo $libro['id']; ?>">
      <button type="submit" class="btn btn-success mt-4 w-100">
        <i class="fa-solid fa-book-open-reader"></i> Pedir Préstamo
      </button>
    </form>
  <?php else: ?>
    <div class="alert alert-warning mt-4 text-center">
      Ya has solicitado este libro y está pendiente de aprobación.
    </div>
  <?php endif; ?>

  <a href="libros.php" class="btn-back">← Volver a Libros</a>
</div>

<footer>
  &copy; <?php echo date('Y'); ?> Biblioteca - Todos los derechos reservados
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
