<?php
/**
 * CONFIGURACIÓN DE BASE DE DATOS (db.php)
 * 
 * Este archivo centraliza la conexión a MySQL/MariaDB.
 * Utiliza PDO para garantizar seguridad contra inyección SQL y compatibilidad con PHP 8.
 */

// Definición de constantes de configuración
// Se recomienda mover estos datos a un archivo .env en producción
define('DB_HOST', 'localhost');
define('DB_NAME', 'salon_pos');
define('DB_USER', 'root');      // Cambiar por usuario real
define('DB_PASS', '');          // Cambiar por contraseña real
define('DB_CHARSET', 'utf8mb4');

/**
 * Función para obtener la conexión PDO
 * @return PDO Objeto de conexión a base de datos
 * @throws Exception Si falla la conexión
 */
function getDBConnection() {
    try {
        // DSN (Data Source Name): Define el driver, host, nombre de BD y charset
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        // Opciones de configuración de PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements nativos
        ];

        // Crear instancia de PDO
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
    } catch (PDOException $e) {
        // Manejo de errores de conexión
        // En producción, no mostrar el error completo al usuario final
        error_log("Error de conexión a BD: " . $e->getMessage());
        throw new Exception("No se pudo conectar a la base de datos.");
    }
}
?>
