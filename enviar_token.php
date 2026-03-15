<?php
require 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $token = bin2hex(random_bytes(32));
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $pdo->prepare("INSERT INTO tokens_recuperacion (usuario_id, token, expiracion) VALUES (?, ?, ?)")
            ->execute([$usuario['id'], $token, $expiracion]);

        $enlace = "http://tusitio.com/restablecer_contraseña.php?token=$token";

        // Enviar correo (usa tu servidor real de correo aquí)
        $asunto = "Recuperación de contraseña";
        $mensaje = "Haz clic en el siguiente enlace para restablecer tu contraseña:\n\n$enlace\n\nEste enlace expira en 1 hora.";
        $cabeceras = "From: no-responder@tusitio.com";

        mail($email, $asunto, $mensaje, $cabeceras);
    }

    $_SESSION['mensaje'] = "Si el correo existe en nuestra base de datos, te enviaremos un enlace.";
    header("Location: recuperar_contraseña.php");
}
