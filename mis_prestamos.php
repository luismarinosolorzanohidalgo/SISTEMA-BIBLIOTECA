<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Restringir acceso a administradores
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Acceso Denegado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container mt-5">
        <div class="alert alert-danger text-center shadow-sm rounded-3 p-4">
            <h4><i class="fas fa-ban text-danger me-2"></i>No tienes permiso para ingresar aquí.</h4>
            <p>Esta sección es solo para usuarios normales.</p>
            <a href="panel.php" class="btn btn-primary mt-3 px-4 py-2 fw-semibold">Volver al Panel</a>
        </div>
    </div>
    </body>
    </html>
    HTML;
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("
    SELECT p.*, l.titulo 
    FROM prestamos p 
    JOIN libros l ON p.libro_id = l.id 
    WHERE p.usuario_id = ?
    ORDER BY p.fecha_prestamo DESC
");
$stmt->execute([$usuario_id]);
$prestamos = $stmt->fetchAll();

$estadoFiltro = $_GET['estado'] ?? 'todos';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Mis Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <style>
        /* Fuentes y base */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            transition: background-color 0.3s, color 0.3s;
            min-height: 100vh;
            padding-bottom: 50px;
        }

        body.dark-mode {
            background-color: #121212;
            color: #e1e1e1;
        }

        /* Contenedor principal */
        .container {
            max-width: 900px;
        }

        /* Título con icono */
        h2 {
            font-weight: 700;
            font-size: 2.2rem;
            color: #0d6efd;
        }
        body.dark-mode h2 {
            color: #66b2ff;
        }

        /* Botón modo oscuro */
        button.btn-outline-secondary {
            border-width: 2px;
            transition: background-color 0.3s, color 0.3s;
        }
        button.btn-outline-secondary:hover {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        body.dark-mode button.btn-outline-secondary:hover {
            background-color: #66b2ff;
            border-color: #66b2ff;
        }

        /* Alertas */
        .alert {
            font-size: 1.1rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgb(0 0 0 / 0.1);
        }
        body.dark-mode .alert-success {
            background-color: #198754;
            color: white;
        }
        body.dark-mode .alert-warning {
            background-color: #ffc107;
            color: #212529;
        }

        /* Botones filtro */
        .btn-group .btn {
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 30px;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-group .btn.active, .btn-group .btn:hover {
            background-color: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd !important;
            box-shadow: 0 4px 12px rgb(13 110 253 / 0.3);
        }
        body.dark-mode .btn-group .btn.active, 
        body.dark-mode .btn-group .btn:hover {
            background-color: #66b2ff !important;
            border-color: #66b2ff !important;
            color: #121212 !important;
        }

        /* Tabla */
        table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgb(0 0 0 / 0.1);
            transition: background-color 0.3s, color 0.3s;
        }

        thead {
            background: linear-gradient(90deg, #0d6efd, #0b5ed7);
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
        }

        body.dark-mode thead {
            background: linear-gradient(90deg, #66b2ff, #3b82f6);
            color: #121212;
        }

        tbody tr:hover {
            background-color: #e9f0ff;
            cursor: default;
            transition: background-color 0.2s;
        }
        body.dark-mode tbody tr:hover {
            background-color: #2a2a2a;
        }

        tbody td {
            vertical-align: middle;
            font-size: 0.95rem;
        }

        /* Badges */
        .badge {
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.4em 0.75em;
            border-radius: 20px;
            text-transform: capitalize;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.12);
            transition: background-color 0.3s, color 0.3s;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .bg-success {
            background-color: #198754 !important;
        }
        .bg-info {
            background-color: #0dcaf0 !important;
        }
        .bg-secondary {
            background-color: #6c757d !important;
        }

        /* Botón PDF */
        .btn-pdf {
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 6px 14px;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-pdf.btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .btn-pdf.btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 6px 14px rgb(220 53 69 / 0.6);
        }
        body.dark-mode .btn-pdf.btn-outline-danger {
            border-color: #f87171;
            color: #f87171;
        }
        body.dark-mode .btn-pdf.btn-outline-danger:hover {
            background-color: #f87171;
            color: #121212;
            box-shadow: 0 6px 14px rgb(248 113 113 / 0.6);
        }

        /* Tabla modo oscuro */
        body.dark-mode table {
            background-color: #1f1f1f;
            color: #e0e0e0;
        }

        /* Tooltip personalizado */
        [data-bs-toggle="tooltip"] {
            cursor: pointer;
        }

        /* Enlaces */
        a {
            text-decoration: none;
            transition: color 0.3s;
        }
        a:hover {
            color: #0d6efd;
        }
        body.dark-mode a:hover {
            color: #66b2ff;
        }

        /* Botón volver */
        .btn-secondary {
            border-radius: 30px;
            font-weight: 600;
            padding: 8px 20px;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #495057;
            color: white;
        }
        body.dark-mode .btn-secondary:hover {
            background-color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-book-reader me-3"></i>Mis Préstamos</h2>
        <button onclick="toggleModo()" class="btn btn-outline-secondary" aria-label="Alternar modo claro y oscuro" title="Alternar modo claro y oscuro"><i class="fas fa-adjust fa-lg"></i></button>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php elseif (isset($_GET['alert'])): ?>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div><?= htmlspecialchars($_GET['alert']) ?></div>
        </div>
    <?php endif; ?>

    <!-- Filtros de estado -->
    <div class="mb-4 text-center">
        <div class="btn-group" role="group" aria-label="Filtros de estado de préstamos">
            <a href="?estado=todos" class="btn btn-outline-primary <?= $estadoFiltro === 'todos' ? 'active' : '' ?>">Todos</a>
            <a href="?estado=pendiente" class="btn btn-outline-warning <?= $estadoFiltro === 'pendiente' ? 'active' : '' ?>">Pendientes</a>
            <a href="?estado=aprobado" class="btn btn-outline-success <?= $estadoFiltro === 'aprobado' ? 'active' : '' ?>">Aprobados</a>
            <a href="?estado=devuelto" class="btn btn-outline-info <?= $estadoFiltro === 'devuelto' ? 'active' : '' ?>">Devueltos</a>
        </div>
    </div>

    <!-- Tabla de préstamos -->
    <div class="table-responsive shadow-sm rounded-4">
        <table class="table align-middle mb-0 <?= isset($_COOKIE['modo']) && $_COOKIE['modo'] === 'oscuro' ? 'table-dark-mode' : '' ?>">
            <thead>
                <tr>
                    <th><i class="fas fa-book"></i> Título</th>
                    <th><i class="fas fa-calendar-alt"></i> Fecha Préstamo</th>
                    <th><i class="fas fa-calendar-check"></i> Fecha Devolución</th>
                    <th><i class="fas fa-info-circle"></i> Estado</th>
                    <th><i class="fas fa-file-pdf"></i> Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $prestamo): ?>
                    <?php if ($estadoFiltro === 'todos' || $prestamo['estado'] === $estadoFiltro): ?>
                        <?php
                            $estado = $prestamo['estado'];
                            $badge = match($estado) {
                                'pendiente' => 'warning',
                                'aprobado' => 'success',
                                'devuelto' => 'info',
                                default => 'secondary'
                            };
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($prestamo['titulo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($prestamo['fecha_devolucion'])) ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($estado) ?></span></td>
                            <td>
                                <?php if ($estado === 'aprobado'): ?>
                                    <a href="acceso_pdf.php?id=<?= $prestamo['id'] ?>" class="btn btn-outline-danger btn-pdf" data-bs-toggle="tooltip" data-bs-placement="top" title="Acceder al PDF protegido">
                                        <i class="fas fa-lock me-1"></i> Ver PDF
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small">No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if (empty($prestamos) || count(array_filter($prestamos, fn($p) => $estadoFiltro === 'todos' || $p['estado'] === $estadoFiltro)) === 0): ?>
                <tr>
                    <td colspan="5" class="text-center fst-italic text-muted py-4">
                        <i class="fas fa-info-circle me-2"></i> No se encontraron préstamos para el filtro seleccionado.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <a href="panel.php" class="btn btn-secondary px-4 py-2"><i class="fas fa-arrow-left me-2"></i> Volver al Panel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tooltip Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    // Toggle modo claro/oscuro con cookie
    function toggleModo() {
        document.body.classList.toggle("dark-mode");
        const modo = document.body.classList.contains("dark-mode") ? "oscuro" : "claro";
        document.cookie = "modo=" + modo + ";path=/;max-age=" + 60*60*24*30; // 30 días
    }

    if (document.cookie.includes("modo=oscuro")) {
        document.body.classList.add("dark-mode");
    }
</script>
</body>
</html>
