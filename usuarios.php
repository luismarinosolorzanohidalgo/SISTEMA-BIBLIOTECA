<?php
session_start();
require 'database.php';

// Validar sesión y rol de administrador
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Obtener rol del usuario actual
$stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION["usuario_id"]]);
$usuarioActual = $stmt->fetch();

if (!$usuarioActual || $usuarioActual["rol"] !== "admin") {
    header("Location: index.php");
    exit;
}

// Obtener todos los usuarios
$stmt = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY nombre");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Usuarios - Biblioteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet"/>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #43cea2, #185a9d);
            min-height: 100vh;
            margin: 0;
            padding-bottom: 3rem;
            color: #222;
        }
        nav {
            background-color: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            padding: 1rem 2rem;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }
        nav a {
            color: white;
            font-weight: 600;
            text-decoration: none;
            margin-right: 2rem;
            transition: color 0.3s ease;
        }
        nav a:hover {
            color: #ffdd57;
        }
        .container {
            max-width: 1140px;
            margin: 3rem auto 4rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            padding: 2rem 3rem;
        }
        h1 {
            text-align: center;
            margin-bottom: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: 0.1em;
            user-select: none;
        }
        .btn-back {
            display: block;
            width: max-content;
            margin: 2rem auto 0;
            padding: 0.6rem 2.2rem;
            font-weight: 600;
            background: #185a9d;
            color: white;
            border-radius: 30px;
            box-shadow: 0 6px 15px rgba(24, 90, 157, 0.6);
            border: none;
            transition: background-color 0.3s ease;
            text-align: center;
            text-decoration: none;
            user-select: none;
        }
        .btn-back:hover {
            background: #0f3a64;
            color: #ffdd57;
        }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 15px;
            border: 1px solid #ccc;
            padding: 5px 10px;
        }
        table.dataTable thead {
            background: #185a9d;
            color: white;
            user-select: none;
        }
        table.dataTable tbody tr:hover {
            background-color: #e2f0ff;
        }
        .btn-action {
            margin-right: 0.3rem;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            padding: 0;
            text-align: center;
            line-height: 36px;
        }
        .btn-action.view { background: #3498db; color: white; }
        .btn-action.edit { background: #f39c12; color: white; }
        .btn-action.delete { background: #e74c3c; color: white; }
        .btn-action:hover {
            opacity: 0.85;
            text-decoration: none;
            color: white !important;
        }
        footer {
            text-align: center;
            color: white;
            font-weight: 500;
            margin-top: 3rem;
            font-size: 0.9rem;
            opacity: 0.7;
            user-select: none;
        }
    </style>
</head>
<body>
<nav role="navigation" aria-label="Navegación principal">
    <a href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
    <a href="libros.php"><i class="fa-solid fa-book"></i> Libros</a>
    <a href="panel.php"><i class="fa-solid fa-user-cog"></i> Panel</a>
    <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
</nav>

<div class="container" role="main">
    <h1>Usuarios Registrados (<?= count($usuarios); ?>)</h1>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" role="alert" aria-live="polite">
            Usuario eliminado correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert" aria-live="assertive">
            No puedes eliminar tu propia cuenta.
        </div>
    <?php endif; ?>

    <?php if (count($usuarios) === 0): ?>
        <p class="text-center">No hay usuarios registrados aún.</p>
    <?php else: ?>
        <table id="usuariosTable" class="table table-striped table-hover" aria-describedby="tablaUsuarios" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                    <td>
                        <a href="detalle_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-action view" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalle" aria-label="Ver detalle usuario <?= $usuario['nombre'] ?>">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-action edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar usuario" aria-label="Editar usuario <?= $usuario['nombre'] ?>">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="borrar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-action delete" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar usuario" aria-label="Eliminar usuario <?= $usuario['nombre'] ?>"
                           onclick="return confirm('¿Estás seguro que deseas eliminar al usuario <?= addslashes(htmlspecialchars($usuario['nombre'])) ?>? Esta acción no se puede deshacer.')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
</div>

<footer>
    &copy; <?= date('Y'); ?> Biblioteca - Todos los derechos reservados
</footer>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#usuariosTable').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            responsive: true,
            lengthMenu: [5, 10, 25, 50],
            pageLength: 10,
            order: [[1, "asc"]]
        });

        // Activar tooltips de Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
</body>
</html>
