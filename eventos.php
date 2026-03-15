<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener lista de eventos
$stmt = $pdo->query("SELECT * FROM eventos ORDER BY fecha, hora");
$eventos = $stmt->fetchAll();

// Procesar inscripción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento_id'])) {
    $evento_id = (int)$_POST['evento_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Verificar que no esté ya inscrito
    $check = $pdo->prepare("SELECT * FROM inscripciones WHERE evento_id = ? AND usuario_id = ?");
    $check->execute([$evento_id, $usuario_id]);
    if ($check->rowCount() === 0) {
        // Insertar inscripción
        $insert = $pdo->prepare("INSERT INTO inscripciones (evento_id, usuario_id) VALUES (?, ?)");
        $insert->execute([$evento_id, $usuario_id]);
        $mensaje = "Inscripción realizada con éxito.";
    } else {
        $mensaje = "Ya estás inscrito en este evento.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Eventos - Biblioteca</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h1>Eventos</h1>
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Título</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Lugar</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eventos as $evento): ?>
                <tr>
                    <td><?= htmlspecialchars($evento['titulo']) ?></td>
                    <td><?= htmlspecialchars($evento['fecha']) ?></td>
                    <td><?= htmlspecialchars(substr($evento['hora'], 0, 5)) ?></td>
                    <td><?= htmlspecialchars($evento['lugar']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="evento_id" value="<?= $evento['id'] ?>" />
                            <button type="submit" class="btn btn-primary btn-sm">Inscribirse</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="panel.php" class="btn btn-secondary mt-3">Volver al Panel</a>
</div>
</body>
</html>
