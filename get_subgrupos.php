<?php
require_once 'includes/db.php';

$grupoId = $_GET['grupo_id'];

$stmt = $pdo->prepare("SELECT * FROM subgrupos WHERE grupo_id = ?");
$stmt->execute([$grupoId]);
$subgrupos = $stmt->fetchAll();

echo json_encode($subgrupos);