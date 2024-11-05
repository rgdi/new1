<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM archivos WHERE id = ?");
$stmt->execute([$id]);
$archivo = $stmt->fetch();

if ($archivo) {
    header('Content-Type: ' . $archivo['tipo']);
    readfile($archivo['ruta']);
    exit();
} else {
    echo 'Archivo no encontrado';
}