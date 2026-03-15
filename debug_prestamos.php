<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    die("No tienes permisos para acceder aquí.");
}

$stmt = $pdo->query("
    SELECT p.id, p.libro_id, p.usuario_id, p.fecha_prestamo, p.fecha_devolucion, p.estado, u.nombre AS usuario, l.titulo AS libro
    FROM prestamos p
    LEFT JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN libros l ON p.libro_id = l.id
    ORDER BY p.fecha_prestamo DESC
");
$prestamos = $stmt->fetchAll();

echo "<h2>Tabla 'prestamos' completa</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>Libro ID</th><th>Usuario ID</th><th>Usuario</th><th>Libro</th><th>Fecha Préstamo</th><th>Fecha Devolución</th><th>Estado</th></tr>";
foreach ($prestamos as $p) {
    echo "<tr>";
    echo "<td>{$p['id']}</td>";
    echo "<td>{$p['libro_id']}</td>";
    echo "<td>{$p['usuario_id']}</td>";
    echo "<td>" . htmlspecialchars($p['usuario']) . "</td>";
    echo "<td>" . htmlspecialchars($p['libro']) . "</td>";
    echo "<td>{$p['fecha_prestamo']}</td>";
    echo "<td>{$p['fecha_devolucion']}</td>";
    echo "<td>{$p['estado']}</td>";
    echo "</tr>";
}
echo "</table>";
?>
