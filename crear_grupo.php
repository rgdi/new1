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
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    
    if (empty($nombre)) {
        $mensaje = "El nombre del grupo es requerido";
    } else {
        try {
            // Crear la carpeta física
            $upload_dir = 'uploads/' . sanitize_filename($nombre);
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Insertar en la base de datos
            $stmt = $pdo->prepare("INSERT INTO grupos (nombre, descripcion, created_by) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $_SESSION['user_id']]);
            
            $mensaje = "Grupo creado exitosamente";
            header("refresh:2;url=dashboard.php");
        } catch (Exception $e) {
            $mensaje = "Error al crear el grupo: " . $e->getMessage();
        }
    }
}

function sanitize_filename($filename) {
    // Elimina caracteres especiales y espacios
    return preg_replace('/[^a-zA-Z0-9-_]/', '_', $filename);
}
?>

<div class="container mt-4">
    <h2>Crear Nuevo Grupo</h2>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="nombre">Nombre del Grupo:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Crear Grupo</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>