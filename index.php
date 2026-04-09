<?php
/**
 * PANTALLA DE INICIO DE SESIÓN (index.php)
 * 
 * Punto de entrada principal. Muestra el formulario de login o redirige al dashboard.
 */

require_once 'includes/auth.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Por favor ingrese usuario y contraseña.";
    } else {
        $usuario = login($username, $password);
        
        if ($usuario) {
            // Login exitoso
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Salón POS</title>
    <!-- Estilos CSS simples para el login -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #666; }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Para que el padding no afecte el ancho */
        }
        button {
            width: 100%;
            padding: 0.8rem;
            background-color: #5c6bc0;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover { background-color: #3f51b5; }
        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 0.8rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>💇 Salón POS</h2>
        <p style="text-align:center; color:#666;">Sistema de Facturación</p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Ingresar</button>
        </form>
        
        <div class="footer">
            <p>Usuario demo: <strong>admin</strong> | Pass: <strong>admin123</strong></p>
        </div>
    </div>
</body>
</html>
