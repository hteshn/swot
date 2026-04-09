<?php
/**
 * SISTEMA DE AUTENTICACIÓN Y SEGURIDAD (auth.php)
 * 
 * Maneja el inicio de sesión, verificación de roles y protección de páginas.
 * Compatible con PHP 8 y funciones de hash modernas.
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/**
 * Verifica las credenciales del usuario
 * @param string $username Nombre de usuario
 * @param string $password Contraseña en texto plano
 * @return array|false Retorna datos del usuario si es válido, false si no
 */
function login($username, $password) {
    try {
        $pdo = getDBConnection();
        
        // Preparar consulta para evitar inyección SQL
        $sql = "SELECT id, nombre_completo, username, password_hash, rol FROM usuarios 
                WHERE username = :username AND activo = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        $usuario = $stmt->fetch();
        
        // Verificar si existe y la contraseña es correcta
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // Regenerar ID de sesión para prevenir fijación de sesión
            session_regenerate_id(true);
            
            // Guardar datos en sesión
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nombre_completo'];
            $_SESSION['user_role'] = $usuario['rol'];
            $_SESSION['logged_in'] = true;
            
            return $usuario;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error en login: " . $e->getMessage());
        return false;
    }
}

/**
 * Cierra la sesión del usuario actual
 */
function logout() {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit;
}

/**
 * Verifica si el usuario ha iniciado sesión
 * Si no, redirige al login
 */
function requireLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: index.php");
        exit;
    }
}

/**
 * Verifica si el usuario tiene el rol necesario
 * @param array $rolesAllowed Array de roles permitidos (ej. ['admin', 'cajero'])
 */
function requireRole($rolesAllowed) {
    requireLogin();
    
    if (!in_array($_SESSION['user_role'], $rolesAllowed)) {
        // Si no tiene permiso, mostrar error o redirigir
        die("Acceso denegado: No tienes permisos para ver esta página.");
    }
}

/**
 * Obtiene el ID del usuario actual
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtiene el rol del usuario actual
 * @return string|null
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}
?>
