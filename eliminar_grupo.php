<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_grupo = $_POST['id_grupo'];
    
    try {
        // Eliminar archivos del grupo
        $stmt = $pdo->prepare("SELECT ruta FROM archivos WHERE grupo_id = ?");
        $stmt->execute([$id_grupo]);
        $archivos = $stmt->fetchAll();
        
        foreach ($archivos as $archivo) {
            unlink($archivo['ruta']);
        }
        
        // Eliminar grupo
        $stmt = $pdo->prepare("DELETE FROM grupos WHERE id = ?");
        $stmt->execute([$id_grupo]);
        
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        echo "Error al eliminar el grupo: " . $e->getMessage();
    }
}

// Obtener grupo por ID
$stmt = $pdo->prepare("SELECT * FROM grupos WHERE id = ?");
$stmt->execute([$_GET['id_grupo']]);
$grupo = $stmt->fetch();

if (!$grupo) {
    header('Location: dashboard.php');
    exit();
}
?>

<div class="container mt-4">
    <h2>Eliminar Grupo</h2>
    
    <p>¿Estás seguro de eliminar el grupo "<strong><?php echo htmlspecialchars($grupo['nombre']); ?></strong>"?</p>
    
    <form method="POST" action="">
        <input type="hidden" name="id_grupo" value="<?php echo $grupo['id']; ?>">
        
        <button type="submit" class="btn btn-danger">Eliminar Grupo</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
    </form >
</div>

<?php require_once 'includes/footer.php'; ?>