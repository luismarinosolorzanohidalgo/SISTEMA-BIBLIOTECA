<?php
session_start();
require 'database.php';

// Solo admins
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("
    SELECT p.id, u.nombre AS usuario, l.titulo AS libro, p.fecha_prestamo, p.fecha_devolucion, p.estado
    FROM prestamos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN libros l ON p.libro_id = l.id
    WHERE p.estado = 'pendiente'
    ORDER BY p.fecha_prestamo DESC
");
$solicitudes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Solicitudes de Préstamo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #f0f4f8;
      min-height: 100vh;
      padding-top: 60px;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.05);
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
      backdrop-filter: blur(10px);
    }

    h2 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 2rem;
      color: #ffb347;
      text-shadow: 0 0 8px rgba(255, 179, 71, 0.5);
    }

    table {
      color: #fff;
    }

    .table thead {
      background-color: rgba(255,255,255,0.1);
      color: #ffcc70;
    }

    .btn {
      border-radius: 30px;
      font-weight: 600;
      padding: 6px 18px;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .btn-success {
      background-color: #28a745;
      border: none;
    }

    .btn-danger {
      background-color: #dc3545;
      border: none;
    }

    .btn-secondary {
      background: linear-gradient(135deg, #ccc, #999);
      color: #111;
      border: none;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .alert {
      border-radius: 10px;
      font-size: 0.95rem;
    }

    .btn-back {
      display: inline-block;
      background: linear-gradient(135deg, #00c9ff, #92fe9d);
      color: #111;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 30px;
      text-decoration: none;
      font-size: 1rem;
      transition: 0.3s ease all;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .btn-back:hover {
      background: linear-gradient(135deg, #92fe9d, #00c9ff);
      transform: scale(1.05);
      box-shadow: 0 6px 16px rgba(0,0,0,0.5);
    }

    .btn-back i {
      margin-right: 6px;
    }

    @media (max-width: 576px) {
      .table-responsive { font-size: 0.85rem; }
      h2 { font-size: 1.5rem; }
    }
  </style>
</head>
<body>

<div class="container mt-4">
  <h2><i class="fa-solid fa-book-reader"></i> Solicitudes de Préstamo</h2>

  <!-- Mensajes -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
      <?php
        $msg = htmlspecialchars($_GET['success']);
        if ($msg === 'solicitud_aprobado') echo "✅ La solicitud fue aprobada correctamente.";
        elseif ($msg === 'solicitud_rechazado') echo "❌ La solicitud fue rechazada.";
        else echo $msg;
      ?>
    </div>
  <?php elseif (isset($_GET['alert'])): ?>
    <div class="alert alert-warning">
      <?php
        $msg = htmlspecialchars($_GET['alert']);
        if ($msg === 'accion_invalida') echo "⚠️ Acción inválida.";
        elseif ($msg === 'prestamo_no_encontrado') echo "⚠️ No se encontró la solicitud.";
        elseif ($msg === 'prestamo_no_pendiente') echo "⚠️ La solicitud no está pendiente.";
        elseif ($msg === 'datos_invalidos') echo "⚠️ Datos inválidos enviados.";
        else echo $msg;
      ?>
    </div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Libro</th>
          <th>Fecha Préstamo</th>
          <th>Fecha Devolución</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($solicitudes as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['usuario']) ?></td>
            <td><?= htmlspecialchars($s['libro']) ?></td>
            <td><?= $s['fecha_prestamo'] ?></td>
            <td><?= $s['fecha_devolucion'] ?></td>
            <td>
              <form method="post" action="procesar_solicitud.php" class="d-inline">
                <input type="hidden" name="prestamo_id" value="<?= $s['id'] ?>">
                <button type="submit" name="accion" value="aprobar" class="btn btn-success btn-sm me-1">Aprobar</button>
                <button type="submit" name="accion" value="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (count($solicitudes) === 0): ?>
          <tr>
            <td colspan="5" class="text-center text-warning">No hay solicitudes pendientes.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <a href="index.php" class="btn-back mt-4"><i class="fa-solid fa-arrow-left"></i> Volver al Panel</a>
</div>

</body>
</html>
