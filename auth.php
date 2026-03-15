<?php
session_start();

function verificar_rol($roles_permitidos = []) {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
        header('Location: login.php');
        exit;
    }
    if (!in_array($_SESSION['rol'], $roles_permitidos)) {
        // Usuario no autorizado para esta página
        header('HTTP/1.1 403 Forbidden');
        echo "<h1>403 - Acceso denegado</h1>";
        echo "<p>No tienes permisos para ver esta página.</p>";
        exit;
    }
}
?>
