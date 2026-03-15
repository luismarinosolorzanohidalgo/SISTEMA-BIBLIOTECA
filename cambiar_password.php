<?php
require 'database.php';
session_start();

if ($_SESSION['rol'] !== 'admin') {
    exit('Acceso denegado');
}

if (isset($_POST['prestamo_id'], $_POST['nueva_password'])) {
    $id = $_POST['prestamo_id'];
    $password_hash = password_hash($_POST['nueva_password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE prestamos SET password_pdf = ? WHERE id = ?");
    $stmt->execute([$password_hash, $id]);
    
    header("Location: prestamos.php?success=Contraseña actualizada");
    exit;
}
?>
