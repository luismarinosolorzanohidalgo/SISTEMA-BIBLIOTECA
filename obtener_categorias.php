<?php
require 'database.php';

try {
    $stmt = $pdo->query("
        SELECT categoria, COUNT(*) AS cantidad
        FROM libros
        GROUP BY categoria
        ORDER BY cantidad DESC
    ");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([]);
    error_log("Error DB: " . $e->getMessage());
}
