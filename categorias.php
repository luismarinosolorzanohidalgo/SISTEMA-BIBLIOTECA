<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Obtener categorías distintas desde la base de datos
$sql = "SELECT DISTINCT categoria FROM libros ORDER BY categoria";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Categorías de Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    /* Copiar todo el CSS que tienes en libros.php (sin cambios) */
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0a9396, #005f73);
      color: #e0fbfc;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    nav {
      background: rgba(0, 0, 0, 0.25);
      backdrop-filter: blur(15px);
      padding: 1rem 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
      display: flex;
      gap: 1.5rem;
      align-items: center;
    }
    nav a {
      color: #e0fbfc;
      font-weight: 600;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    nav a:hover {
      color: #ffd700;
    }
    .catalog-title {
      font-size: 3rem;
      font-weight: 900;
      text-align: center;
      margin: 2rem auto 3rem;
      color: #caf0f8;
      text-shadow: 0 0 15px #90e0ef, 0 0 30px #0077b6;
      animation: glow 2.5s ease-in-out infinite alternate;
      user-select: none;
    }
    @keyframes glow {
      0% {
        text-shadow:
          0 0 10px #90e0ef,
          0 0 20px #0077b6,
          0 0 30px #00b4d8,
          0 0 40px #48cae4;
        color: #caf0f8;
      }
      100% {
        text-shadow:
          0 0 20px #90e0ef,
          0 0 30px #0077b6,
          0 0 40px #00b4d8,
          0 0 50px #48cae4;
        color: #ade8f4;
      }
    }
    .container {
      flex-grow: 1;
      max-width: 1100px;
      margin: 0 auto 4rem;
      padding: 2rem;
      background: rgba(255 255 255 / 0.15);
      border-radius: 25px;
      box-shadow:
        8px 8px 30px rgba(0,0,0,0.5),
        -8px -8px 30px rgba(255,255,255,0.15);
      backdrop-filter: blur(20px);
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
    }
    /* Tarjeta para categoría similar a libro, pero simplificada */
    .card-category {
      background: rgba(0, 95, 115, 0.7);
      border-radius: 20px;
      box-shadow:
        8px 8px 15px rgba(0, 0, 0, 0.6),
        -8px -8px 15px rgba(72, 177, 203, 0.7);
      padding: 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      color: #e0fbfc;
      user-select: none;
      font-weight: 700;
      font-size: 1.6rem;
      text-align: center;
      text-shadow: 1px 1px 2px #001f27;
    }
    .card-category:hover {
      transform: scale(1.1);
      box-shadow:
        12px 12px 30px rgba(0, 0, 0, 0.7),
        -12px -12px 30px rgba(72, 177, 203, 0.9);
      color: #ffd700;
    }
    .back-btn {
      display: inline-block;
      margin-top: 2rem;
      background: #ffb347;
      color: #1a1a1a;
      padding: 0.6rem 1.5rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      transition: 0.3s ease;
    }
    .back-btn:hover {
      background: #ffc566;
    }
    footer {
      text-align: center;
      padding: 1.5rem;
      color: #caf0f8;
      font-weight: 600;
      opacity: 0.8;
      background: rgba(0, 95, 115, 0.2);
      user-select: none;
    }
  </style>
</head>
<body>
  <nav>
    <a href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
    <a href="usuarios.php"><i class="fa-solid fa-users"></i> Usuarios</a>
    <a href="panel.php"><i class="fa-solid fa-user-cog"></i> Panel</a>
    <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
  </nav>

  <h1 class="catalog-title" aria-label="Título Catálogo de Categorías animado">CATEGORÍAS DE LIBROS</h1>

  <main class="container" role="main" aria-label="Listado de categorías de libros">
    <?php if (empty($categorias)): ?>
      <p style="grid-column: 1/-1; text-align:center; font-size:1.2rem; color:#caf0f8;">No hay categorías disponibles.</p>
    <?php else: ?>
      <?php foreach ($categorias as $cat): ?>
        <article 
          class="card-category" 
          onclick="window.location='libros.php?categoria=<?php echo urlencode($cat); ?>'"
          tabindex="0" 
          role="link" 
          aria-label="Ver libros de la categoría <?php echo htmlspecialchars($cat); ?>"
          onkeypress="if(event.key==='Enter'){window.location='libros.php?categoria=<?php echo urlencode($cat); ?>'}"
        >
          <?php echo htmlspecialchars($cat); ?>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
    <div style="text-align: center; grid-column: 1/-1;">
      <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>
    </div>
  </main>

  <footer>
    &copy; <?php echo date('Y'); ?> Biblioteca. Todos los derechos reservados.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
