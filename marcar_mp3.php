<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mp3_file = $_FILES['mp3_file'];
    $grupo_id = $_POST['grupo_id'];
    $subgrupo_id = $_POST['subgrupo_id'];

    // Crear la carpeta si no existe
    $upload_dir = 'uploads/' . $grupo_id . '/' . $subgrupo_id . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Marcar el archivo MP3
    $stmt = $pdo->prepare("INSERT INTO marcados (nombre, ruta, grupo_id, subgrupo_id, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$mp3_file['name'], $upload_dir . $mp3_file['name'], $grupo_id, $subgrupo_id, $_SESSION['user_id']]);

    // Redirigir a la página del dashboard
    header('Location: dashboard.php');
    exit();
}
?>

<div class="container mt-4">
    <h2>Marcar MP3</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="mp3_file">Selecciona un MP3:</label>
            <input type="file" name="mp3_file" id="mp3_file" required>
        </div>
        <div class="form-group">
            <label for="grupo_id">Grupo:</label>
            <select name="grupo_id" id="grupo_id" required>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM grupos WHERE created_by = ?");
                $stmt->execute([$_SESSION['user_id']]);
                while ($grupo = $stmt->fetch()):
                ?>
                <option value="<?php echo $grupo['id']; ?>"><?php echo htmlspecialchars($grupo['nombre']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subgrupo_id">Subgrupo:</label>
            <select name="subgrupo_id" id="subgrupo_id" required>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM subgrupos WHERE grupo_id = ?");
                $stmt->execute([$grupo_id]);
                while ($subgrupo = $stmt->fetch()):
                ?>
                <option value="<?php echo $subgrupo['id']; ?>"><?php echo htmlspecialchars($subgrupo['nombre']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Marcar</button>
    </form>
</div>