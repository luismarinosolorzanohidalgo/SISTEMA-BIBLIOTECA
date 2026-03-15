<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$mensaje = '';
$tipoMensaje = ''; // success, danger, warning

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libro_id = $_POST['libro_id'] ?? null;
    $archivo = $_FILES['pdf'] ?? null;

    if ($libro_id && $archivo && $archivo['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($archivo['name']);
        $rutaDestino = 'pdfs/' . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $stmt = $pdo->prepare("UPDATE libros SET ruta_pdf = ? WHERE id = ?");
            $stmt->execute([$nombreArchivo, $libro_id]);

            $mensaje = "PDF subido y ruta actualizada con éxito.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al mover el archivo.";
            $tipoMensaje = "danger";
        }
    } else {
        $mensaje = "Faltan datos o hubo un error al subir el archivo.";
        $tipoMensaje = "warning";
    }
}

$libros = $pdo->query("SELECT id, titulo FROM libros")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0f1d8b3.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .upload-card {
            max-width: 480px;
            width: 100%;
            background: white;
            padding: 30px 35px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .custom-file-label::after {
            content: "Seleccionar";
        }
        .file-name {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="upload-card">
        <h3 class="mb-4 text-center"><i class="fas fa-file-pdf text-danger"></i> Subir PDF para un Libro</h3>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="libro_id" class="form-label">📚 Selecciona un libro:</label>
                <select name="libro_id" id="libro_id" class="form-select" required aria-required="true" aria-describedby="libroHelp">
                    <option value="" disabled selected>-- Elegir libro --</option>
                    <?php foreach ($libros as $libro): ?>
                        <option value="<?= $libro['id'] ?>"><?= htmlspecialchars($libro['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="libroHelp" class="form-text">Elige el libro al que quieres asociar el PDF.</div>
            </div>

            <div class="mb-3">
                <label for="pdf" class="form-label">📎 Archivo PDF:</label>
                <input type="file" name="pdf" id="pdf" accept="application/pdf" class="form-control" required aria-required="true" aria-describedby="pdfHelp">
                <div id="pdfHelp" class="form-text">Solo archivos PDF. Máximo 10MB.</div>
                <div id="fileName" class="file-name"></div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Subir y Guardar</button>
                <a href="panel.php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

    <script>
        // Mostrar nombre del archivo seleccionado
        document.getElementById('pdf').addEventListener('change', function(){
            const fileName = this.files.length ? this.files[0].name : '';
            document.getElementById('fileName').textContent = fileName ? `Archivo seleccionado: ${fileName}` : '';
        });

        // Validación simple HTML5 + Bootstrap
        (function () {
            'use strict'
            const form = document.querySelector('form')
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })()
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
