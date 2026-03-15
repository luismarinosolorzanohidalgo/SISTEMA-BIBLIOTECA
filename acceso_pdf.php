<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$idPrestamo = $_GET['id'] ?? null;

if (!$idPrestamo) {
    echo "ID de préstamo no válido.";
    exit;
}

$stmt = $pdo->prepare("SELECT l.titulo FROM prestamos p JOIN libros l ON p.libro_id = l.id WHERE p.id = ? AND p.usuario_id = ?");
$stmt->execute([$idPrestamo, $_SESSION['usuario_id']]);
$libro = $stmt->fetch();

if (!$libro) {
    echo "No tienes acceso a este préstamo.";
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT password_pdf FROM prestamos WHERE id = ?");
    $stmt->execute([$idPrestamo]);
    $prestamo = $stmt->fetch();

    if ($prestamo && password_verify($password, $prestamo['password_pdf'])) {
        header("Location: visor_pdf.php?id=" . $idPrestamo);
        exit;
    } else {
        $error = "❌ Contraseña incorrecta.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ver Libro - Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            padding: 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.25);
            max-width: 420px;
            width: 100%;
            padding: 30px 25px;
        }
        h3 {
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
        }
        label {
            font-weight: 600;
            color: #dcdcdc;
        }
        input.form-control {
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: 1.5px solid transparent;
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        input.form-control:focus {
            outline: none;
            border-color: #8e44ad;
            background: rgba(255,255,255,0.35);
            color: #222;
            box-shadow: 0 0 10px #9b59b6;
        }
        .btn-success {
            background: #9b59b6;
            border: none;
            width: 100%;
            padding: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .btn-success:hover {
            background: #8e44ad;
        }
        .btn-secondary {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        .btn-secondary:hover {
            background: #555;
            color: #fff;
        }
        .alert-danger {
            background: rgba(255, 99, 71, 0.85);
            border: none;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(255, 99, 71, 0.7);
            margin-bottom: 20px;
            color: #fff;
        }
        .fa-unlock {
            margin-right: 8px;
        }
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .card {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="card shadow-lg">
        <h3>🔐 Ingrese la contraseña para ver:<br><strong><?= htmlspecialchars($libro['titulo']) ?></strong></h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label for="password" class="form-label">Contraseña de acceso</label>
                <input type="password" id="password" name="password" class="form-control" required autofocus placeholder="Introduce la contraseña">
            </div>
            <button type="submit" class="btn btn-success w-100"><i class="fas fa-unlock"></i> Acceder al PDF</button>
            <a href="mis_prestamos.php" class="btn btn-secondary mt-3 w-100 d-block text-center text-decoration-none">Cancelar</a>
        </form>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
