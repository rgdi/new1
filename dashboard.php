<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener grupos
$stmt = $pdo->prepare("SELECT * FROM grupos WHERE created_by = ?");
$stmt->execute([$_SESSION['user_id']]);
$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Panel de Control</h2>
    
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Grupos</h5>
                    <ul class="list-group">
                        <?php if (empty($grupos)): ?>
                            <li class="list-group-item">No se encontraron grupos.</li>
                        <?php else: ?>
                            <?php foreach ($grupos as $grupo): ?>
                                <li class="list-group-item">
                                    <a href="?grupo=<?php echo urlencode($grupo['nombre']); ?>">
                                        <?php echo htmlspecialchars($grupo['nombre']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="crear_grupo.php" class="btn btn-primary mt-3">Nuevo Grupo</a>
                    <a href="subir.php" class="btn btn-secondary mt-3">Subir Archivos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Archivos Recientes</h5>
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
                                <?php
                                $stmt = $pdo->prepare("SELECT * FROM archivos WHERE usuario_id = ? ORDER BY created_at DESC LIMIT 10");
                                $stmt->execute([$_SESSION['user_id']]);
                                while ($archivo = $stmt->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($archivo['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($archivo['tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($archivo['tamano']); ?></td>
                                    <td><?php echo htmlspecialchars($archivo['created_at']); ?></td>
                                    <td>
                                        <a href="descargar.php?id=<?php echo $archivo['id']; ?>" class="btn btn-sm btn-primary">Descargar</a>
                                        <a href="eliminar_archivo.php?id=<?php echo $archivo['id']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
