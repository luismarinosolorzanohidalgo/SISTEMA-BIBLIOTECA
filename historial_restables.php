<?php
session_start();
require 'database.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM historial_restables ORDER BY fecha_restablecimiento DESC");
$historial = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Restablecimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e1e1e;
            color: #fff;
        }
        .btn-panel {
            position: fixed;
            top: 20px;
            left: 20px;
        }
        .container {
            animation: slideIn 0.8s ease-in-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<a href="index.php" class="btn btn-outline-light btn-sm btn-panel"><i class="fas fa-arrow-left"></i> Volver al Panel</a>

<div class="container py-5">
    <h2 class="mb-4 text-center"><i class="fas fa-history"></i> Historial de Restablecimientos</h2>
    <?php if (count($historial) > 0): ?>
    <table class="table table-dark table-hover text-center align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Correo</th>
                <th>Fecha Restablecida</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($historial as $h): ?>
            <tr>
                <td><?= $h['id'] ?></td>
                <td><?= htmlspecialchars($h['email']) ?></td>
                <td><?= $h['fecha_restablecimiento'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info text-center">Aún no hay restablecimientos registrados.</div>
    <?php endif; ?>
</div>
</body>
</html>
