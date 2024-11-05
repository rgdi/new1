<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM archivos WHERE id = ?");
$stmt->execute([$id]);
header('Location: dashboard.php');
exit();