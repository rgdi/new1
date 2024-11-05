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
    
    if (empty($username) || empty($password)) {
        $mensaje = "Por favor, ingrese su usuario y contraseña";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit();
            } else {
                $mensaje = "Usuario o contraseña incorrectos";
            }
        } catch (Exception $e) {
            $mensaje = "Error al iniciar sesión: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-4">
    <h2>Iniciar Sesión</h2>
    
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
        
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>