<?php
session_start();
require 'database.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo los administradores pueden acceder a esta sección.'); window.location.href='panel.php';</script>";
    exit;
}

// Actualizar contraseña PDF si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_prestamo'], $_POST['nueva_password'])) {
    $id_prestamo = $_POST['id_prestamo'];
    $nueva_password = trim($_POST['nueva_password']);

    if (!empty($nueva_password)) {
        $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE prestamos SET password_pdf = ? WHERE id = ?");
        $update->execute([$hashed_password, $id_prestamo]);
        $mensaje = "Contraseña actualizada para el préstamo ID $id_prestamo.";
    }
}

// Obtener todos los préstamos
$stmt = $pdo->query("SELECT p.id, l.titulo, u.nombre, p.fecha_prestamo, p.fecha_devolucion, p.estado, p.password_pdf 
                     FROM prestamos p 
                     JOIN libros l ON p.libro_id = l.id 
                     JOIN usuarios u ON p.usuario_id = u.id 
                     ORDER BY p.fecha_prestamo DESC");
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Préstamos - Biblioteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1f1c2c, #928dab);
            color: #fff;
            padding-top: 80px;
            min-height: 100vh;
        }

        header {
            position: fixed;
            top: 0; left: 0; width: 100%;
            background: rgba(30, 30, 30, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .logo {
            font-weight: bold;
            font-size: 1.6rem;
            color: #fbbf24;
            text-shadow: 0 0 6px #fbbf24aa;
        }

        nav button {
            background: transparent;
            border: 2px solid transparent;
            color: #fff;
            font-weight: 600;
            padding: 0.4rem 1rem;
            margin-left: 0.5rem;
            border-radius: 30px;
            transition: 0.3s ease;
        }

        nav button:hover {
            background-color: #fbbf24;
            color: #111;
        }

        .container {
            max-width: 1100px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.5);
            border-radius: 20px;
            overflow: hidden;
        }

        .table thead {
            background-color: #fbbf24;
            color: #111;
        }

        .badge-activo {
            background-color: #22c55e;
        }
        .badge-devuelto {
            background-color: #3b82f6;
        }
        .badge-vencido {
            background-color: #ef4444;
        }

        footer {
            margin-top: 3rem;
            color: #ccc;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Biblioteca - Admin</div>
    <nav>
        <button onclick="location.href='panel.php'">Panel</button>
        <button onclick="location.href='libros.php'">Libros</button>
        <button onclick="location.href='usuarios.php'">Usuarios</button>
        <button onclick="location.href='logout.php'" style="background:#e11d48;">Cerrar sesión</button>
    </nav>
</header>

<main class="container mt-4">
    <h1 class="text-center"><i class="fa-solid fa-book-reader"></i> Historial de Préstamos</h1>

    <?php if (isset($mensaje)): ?>
        <div class="alert alert-success mt-3"><?= htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <?php if (count($prestamos) > 0): ?>
        <div class="card p-4 mt-3">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-white align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Usuario</th>
                            <th>Préstamo</th>
                            <th>Devolución</th>
                            <th>Estado</th>
                            <th>Contraseña PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $p): ?>
                        <tr>
                            <td><?= $p['id']; ?></td>
                            <td><?= htmlspecialchars($p['titulo']); ?></td>
                            <td><?= htmlspecialchars($p['nombre']); ?></td>
                            <td><?= $p['fecha_prestamo']; ?></td>
                            <td><?= $p['fecha_devolucion']; ?></td>
                            <td><span class="badge <?= match($p['estado']) {
                                'activo' => 'badge-activo',
                                'devuelto' => 'badge-devuelto',
                                'vencido' => 'badge-vencido',
                                default => 'bg-secondary'
                            }; ?>"><?= ucfirst($p['estado']); ?></span></td>
                            <td>
                                <form method="POST" class="d-flex">
                                    <input type="hidden" name="id_prestamo" value="<?= $p['id']; ?>">
                                    <input type="text" name="nueva_password" placeholder="Nueva contraseña" class="form-control form-control-sm me-2" required>
                                    <button type="submit" class="btn btn-warning btn-sm">Guardar</button>
                                </form>
                                <?php if ($p['password_pdf']): ?>
                                    <small class="text-success mt-1 d-block">✔ Contraseña asignada</small>
                                <?php else: ?>
                                    <small class="text-danger mt-1 d-block">✘ No asignada</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
<a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>

<style>
  .btn-back {
    display: inline-block;
    background: linear-gradient(135deg, #00c9ff, #92fe9d);
    color: #111;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 1rem;
    transition: 0.3s ease all;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    margin-top: 1.5rem;
  }

  .btn-back i {
    margin-right: 8px;
  }

  .btn-back:hover {
    background: linear-gradient(135deg, #92fe9d, #00c9ff);
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0,0,0,0.5);
    color: #000;
  }
</style>
            </div>
        </div>
        
    <?php else: ?>
        <p class="text-center mt-4 fs-5">No hay préstamos registrados.</p>
    <?php endif; ?>
</main>

<footer class="py-4">
    &copy; <?= date('Y'); ?> Biblioteca | Panel de administración
</footer>

</body>
</html>
