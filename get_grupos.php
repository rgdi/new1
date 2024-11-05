<?php
require_once 'includes/db.php';

$carpetaId = $_GET['carpeta_id'];

$stmt = $pdo->prepare("SELECT * FROM grupos WHERE carpeta_id = ?");
$stmt->execute([$carpetaId]);
$grupos = $stmt->fetchAll();

echo json_encode($grupos);