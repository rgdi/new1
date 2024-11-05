<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_grupo = $_POST['id_grupo'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    
    if (empty($nombre)) {
        $mensaje = "El nombre del grupo es requerido";
    } else {
        try {
            // Actualizar en la base de datos
            $stmt = $pdo->prepare("UPDATE grupos SET nombre = ?, descripcion = ? WHERE id = ?");
            $stmt->execute([$nombre, $descripcion, $id_grupo]);
            
            $mensaje = "Grupo actualizado exitosamente";
            header("refresh:2;url=dashboard.php");
        } catch (Exception $e) {
            $mensaje = "Error al actualizar el grupo: " . $e->getMessage();
        }
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
    <h2>Editar Grupo</h2>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="id_grupo" value="<?php echo $grupo['id']; ?>">
        
        <div class="form-group">
            <label for="nombre">Nombre del Grupo:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($grupo['nombre']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($grupo['descripcion']); ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Actualizar Grupo</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>