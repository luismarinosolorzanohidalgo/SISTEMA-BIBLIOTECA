<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID de préstamo no especificado.");
}

$id_prestamo = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT l.ruta_pdf, p.usuario_id FROM prestamos p JOIN libros l ON p.libro_id = l.id WHERE p.id = ?");
$stmt->execute([$id_prestamo]);
$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prestamo) {
    die("Préstamo no encontrado.");
}

if ($prestamo['usuario_id'] != $_SESSION['usuario_id'] && $_SESSION['rol'] !== 'admin') {
    die("No tienes permiso para ver este PDF.");
}

$ruta_pdf = 'pdfs/' . $prestamo['ruta_pdf'];
if (!file_exists($ruta_pdf)) {
    die("Archivo PDF no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Visor PDF protegido</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background-color: #111;
        }

        iframe {
            width: 100%;
            height: 100vh;
            border: none;
        }

        /* Bloqueo de impresión */
        @media print {
            body {
                display: none !important;
            }
        }
    </style>

    <script>
        // Bloquear clic derecho
        document.addEventListener("contextmenu", event => event.preventDefault());

        // Opcional: bloquear atajos Ctrl+P, Ctrl+S
        document.addEventListener("keydown", function(e) {
            if ((e.ctrlKey && (e.key === 'p' || e.key === 's')) || e.key === 'PrintScreen') {
                e.preventDefault();
                alert("Función desactivada.");
            }
        });
    </script>
</head>
<body>
    <!-- Mostramos el PDF sin controles de barra de herramientas -->
    <iframe src="<?= htmlspecialchars($ruta_pdf) ?>#toolbar=0&navpanes=0&scrollbar=0" allow="fullscreen"></iframe>
</body>
</html>
