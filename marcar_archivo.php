<?php
require_once 'includes/db.php';

$archivoId = $_GET['archivo_id'];

$stmt = $pdo->prepare("UPDATE archivos SET marcado = 1 WHERE id = ?");
$stmt->execute([$archivoId]);

echo json_encode(['success' => true]);