<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("UPDATE archivos SET marcado = 1 WHERE id = ?");
$stmt->execute([$id]);
header('Location: dashboard.php');
exit();