<?php
include 'includes/db.php';

// Verificar si se han subido archivos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivos'])) {
    $carpeta = $_POST['carpeta'];
    $grupo = $_POST['grupo'];
    $clasificacion = $_POST['clasificacion'];
    $mensajes = [];

    foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
        $nombreArchivo = $_FILES['archivos']['name'][$key];
        $rutaTemporal = $_FILES['archivos']['tmp_name'][$key];
        $rutaDestino = "uploads/$carpeta/$grupo/$nombreArchivo";

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
            // Guardar información en la base de datos
            $stmt = $pdo->prepare("INSERT INTO archivos (nombre, tipo, ruta, carpeta, grupo, clasificacion, fecha_subida) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $tipoArchivo = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            if ($stmt->execute([$nombreArchivo, $tipoArchivo, $rutaDestino, $carpeta, $grupo, $clasificacion])) {
                $mensajes[] = "Archivo '$nombreArchivo' subido con éxito.";
            } else {
                $mensajes[] = "Error al guardar información de '$nombreArchivo' en la base de datos.";
            }
        } else {
            $mensajes[] = "Error al subir el archivo: '$nombreArchivo'.";
        }
    }

    // Mostrar mensajes de confirmación
    foreach ($mensajes as $mensaje) {
        echo "<p>$mensaje</p>";
    }
    echo '<a href="dashboard.php">Volver al Dashboard</a>';
} else {
    echo "No se han subido archivos.";
}
?>