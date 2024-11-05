<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];

    $stmt = $pdo->prepare("SELECT * FROM archivos WHERE nombre LIKE ? AND usuario_id = ?");
    $stmt->execute(['%' . $nombre . '%', $_SESSION['user_id']]);
    $archivos = $stmt->fetchAll();
}
?>

<div class="container mt-4">
    <h2>Buscar Archivos</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <?php if (isset($archivos)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Tamaño</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archivos as $archivo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($archivo['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($archivo['tipo']); ?></td>
                    <td><?php echo number_format($archivo['tamano'] / 1024, 2) . ' KB'; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($archivo['created_at'])); ?></td>
                    <td>
                        <a href="descargar.php?id=<?php echo $archivo['id']; ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="vista_archivo.php?id=<?php echo $archivo['id']; ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="eliminar_archivo.php?id=<?php echo $archivo['id']; ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('¿Está seguro de eliminar este archivo?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>