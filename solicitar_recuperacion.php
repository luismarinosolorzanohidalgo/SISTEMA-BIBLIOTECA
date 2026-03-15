<?php
// solicitar_recuperacion.php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // Verificar si el usuario existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Registrar la solicitud solo si no hay una solicitud pendiente
        $verificar = $pdo->prepare("SELECT id FROM restablecimientos WHERE usuario_id = ? AND estado = 'pendiente'");
        $verificar->execute([$usuario['id']]);
        if (!$verificar->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO restablecimientos (usuario_id) VALUES (?)");
            $stmt->execute([$usuario['id']]);
        }
    }

    // Redirigir siempre (aunque el email no exista) por seguridad
    header('Location: login.php?solicitud=ok');
    exit;
}
?>
