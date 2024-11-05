<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdf_file = $_FILES['pdf_file'];
    $grupo_id = $_POST['grupo_id'];
    $subgrupo_id = $_POST['subgrupo_id'];

    // Crear la carpeta si no existe
    $upload_dir = 'uploads/' . $grupo_id . '/' . $subgrupo_id . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Dividir el PDF
    $pdf = new FPDI();
    $pagecount = $pdf->setSourceFile($pdf_file['tmp_name']);
    for ($i = 1; $i <= $pagecount; $i++) {
        $tplidx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplidx);
        $pdf->Output($upload_dir . 'pagina_' . $i . '.pdf', 'F');
    }

    // Redirigir a la página del dashboard
    header('Location: dashboard.php');
    exit();
}
?>

<div class="container mt-4">
    <h2>Dividir PDF</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="pdf_file">Selecciona un PDF:</label>
            <input type="file" name="pdf_file" id="pdf_file" required>
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
        <button type="submit" class="btn btn-primary">Dividir</button>
    </form>
</div>