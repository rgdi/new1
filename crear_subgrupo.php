<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_subgrupo = $_POST['nombre_subgrupo'];
    $grupo_id = $_POST['grupo_id'];

    // Crear la carpeta si no existe
    $upload_dir = 'uploads/' . $grupo_id . '/' . $nombre_subgrupo;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Guardar en la base de datos
    $stmt = $pdo->prepare("INSERT INTO subgrupos (nombre, grupo_id) VALUES (?, ?)");
    $stmt->execute([$nombre_subgrupo, $grupo_id]);

    // Redirigir a la página del dashboard
    header('Location: dashboard.php');
    exit();
}
?>

<div class="container mt-4">
    <h2>Crear Subgrupo</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="nombre_subgrupo">Nombre del Subgrupo:</label>
            <input type="text" name="nombre_subgrupo" id="nombre_subgrupo" required>
        </div>
        <div class="form-group">
            <label for="grupo_id">Grupo:</label>
            <select name="grupo_id" id="grupo_id" required>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM grupos WHERE created_by = ?");
                $stmt->execute([$_SESSION['user_id']]);
                while ($grupo = $stmt->fetch()):
                ?>
                <option value="<?php echo htmlspecialchars($grupo['nombre']); ?>"><?php echo htmlspecialchars($grupo['nombre']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Subgrupo</button>
    </form>
</div>