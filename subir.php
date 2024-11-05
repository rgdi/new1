<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener grupos
$stmt = $pdo->prepare("SELECT * FROM grupos WHERE created_by = ?");
$stmt->execute([$_SESSION['user_id']]);
$grupos = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['grupo']) && !empty($_FILES['archivos']['name'][0])) {
        $grupo = $_POST['grupo'];
        
        // Crear directorio si no existe
        $uploadDir = "uploads/$grupo/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $success = true;
        $messages = [];

        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['archivos']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['archivos']['name'][$key];
                $fileSize = $_FILES['archivos']['size'][$key];
                $fileType = $_FILES['archivos']['type'][$key];
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmp_name, $filePath)) {
                    // Obtener grupo_id
                    $stmtGrupo = $pdo->prepare("SELECT id FROM grupos WHERE nombre = ? AND created_by = ?");
                    $stmtGrupo->execute([$grupo, $_SESSION['user_id']]);
                    $grupoId = $stmtGrupo->fetchColumn();

                    // Guardar en base de datos
                    $stmt = $pdo->prepare("INSERT INTO archivos (nombre, ruta, tipo, tamano, grupo_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$fileName, $filePath, $fileType, $fileSize, $grupoId, $_SESSION['user_id']])) {
                        $messages[] = "Archivo $fileName subido correctamente.";
                    } else {
                        $success = false;
                        $messages[] = "Error al guardar $fileName en la base de datos.";
                    }
                } else {
                    $success = false;
                    $messages[] = "Error al mover el archivo $fileName.";
                }
            } else {
                $success = false;
                $messages[] = "Error en la subida del archivo.";
            }
        }
    } else {
        $success = false;
        $messages[] = "Por favor selecciona un grupo y al menos un archivo.";
    }
}
?>

<div class="container mt-4">
    <h2>Subir Archivos</h2>
    
    <?php if (isset($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="grupo">Selecciona un Grupo:</label>
            <select name="grupo" id="grupo" class="form-control" required>
                <option value="">-- Selecciona un grupo --</option>
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?php echo htmlspecialchars($grupo['nombre']); ?>">
                        <?php echo htmlspecialchars($grupo['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="archivos">Selecciona los Archivos:</label>
            <input type="file" name="archivos[]" id="archivos" class="form-control" multiple required>
        </div>
        
        <button type="submit" class="btn btn-primary">Subir Archivos</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>