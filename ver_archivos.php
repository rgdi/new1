<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM archivos WHERE usuario_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$archivos = $stmt->fetchAll();

?>

<div class="container mt-4">
    <h2>Archivos</h2>
    
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Tamaño</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($archivos as $archivo): ?>
                <tr>
                    <td><button class="btn btn-link" onclick="window.open('<?php echo $archivo['ruta']; ?>', '_blank')"> <?php echo htmlspecialchars($archivo['nombre']); ?></button></td>
                    <td><?php echo htmlspecialchars($archivo['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($archivo['tamano']); ?> bytes</td>
                    <td>
                        <button class="btn btn-primary" onclick="marcarArchivo(<?php echo $archivo['id']; ?>)">Marcar</button>
                        <button class="btn btn-danger" onclick="eliminarArchivo(<?php echo $archivo['id']; ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function marcarArchivo(archivoId) {
    fetch(`marcar_archivo.php?archivo_id=${archivoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Archivo marcado correctamente');
            } else {
                alert('Error al marcar archivo');
            }
        });
}

function eliminarArchivo(archivoId) {
    if (confirm('¿Estás seguro de eliminar el archivo?')) {
        fetch(`eliminar_archivo.php?archivo_id=${archivoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Archivo eliminado correctamente');
                    window.location.reload();
                } else {
                    alert('Error al eliminar archivo');
                }
            });
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>