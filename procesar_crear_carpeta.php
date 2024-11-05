<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];

    // Aquí puedes agregar lógica para verificar si la carpeta ya existe

    // Inserta la nueva carpeta en la base de datos
    $stmt = $pdo->prepare("INSERT INTO carpetas (nombre, usuario_id) VALUES (?, ?)");
    $stmt->execute([$nombre, $_SESSION['usuario_id']]);

    // Redirige a dashboard
    header("Location: dashboard.php");
    exit();
}
?>