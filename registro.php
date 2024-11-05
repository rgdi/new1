<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    
    if (empty($username) || empty($password) || empty($email)) {
        $mensaje = "Por favor, ingrese todos los campos";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                $mensaje = "El usuario ya existe";
            } else {
                $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)");
                $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $email]);
                
                $mensaje = "Registro exitoso";
                header("refresh:2;url= login.php");
            }
        } catch (Exception $e) {
            $mensaje = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-4">
    <h2>Registro</h2>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Usuario:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>