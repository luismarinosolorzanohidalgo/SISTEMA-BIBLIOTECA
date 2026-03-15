<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: white;
            font-family: 'Segoe UI', sans-serif;
            animation: fadeIn 1s ease-in-out;
        }

        .card {
            background-color: #1e1e2f;
            color: white;
            border: none;
            border-radius: 1rem;
        }

        .form-control {
            background-color: #2c2c3e;
            border: 1px solid #444;
            color: white;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        .form-label {
            margin-bottom: 0.3rem;
        }

        .input-group-text {
            background-color: #2c2c3e;
            border: 1px solid #444;
            color: #aaa;
        }

        a {
            color: #9dcfff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg p-4 w-100" style="max-width: 400px;">
        <h3 class="mb-4 text-center">Recuperar Contraseña</h3>
        <form action="solicitar_recuperacion.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="tu@email.com">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Solicitar</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php"><i class="bi bi-arrow-left-circle"></i> Volver al inicio de sesión</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
