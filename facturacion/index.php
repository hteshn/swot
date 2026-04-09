<?php
/**
 * Punto de entrada principal - Router del sistema de facturación
 * 
 * Uso: Colocar este archivo en la raíz del proyecto y configurar el servidor web
 * para que todas las peticiones pasen por este archivo.
 */

// Habilitar reporte de errores (desactivar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir ruta base
define('BASE_PATH', __DIR__);

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/config/',
        BASE_PATH . '/models/',
        BASE_PATH . '/controllers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Iniciar sesión
session_start();

// Router simple
$controller = $_GET['controller'] ?? 'facturacion';
$action = $_GET['action'] ?? 'index';
$subAction = $_GET['subaction'] ?? null;

try {
    // Cargar controlador
    switch ($controller) {
        case 'facturacion':
            $controllerInstance = new FacturacionController();
            
            // Manejar acciones
            switch ($action) {
                case 'index':
                    $controllerInstance->index();
                    break;
                    
                case 'clientes':
                    $controllerInstance->clientes($subAction ?: 'list');
                    break;
                    
                case 'productos':
                    $controllerInstance->productos($subAction ?: 'list');
                    break;
                    
                case 'facturas':
                    $controllerInstance->facturas($subAction ?: 'list');
                    break;
                    
                case 'api_producto':
                    $controllerInstance->apiProducto();
                    break;
                    
                default:
                    throw new Exception("Acción no válida: {$action}");
            }
            break;
            
        case 'usuario':
            $controllerInstance = new UsuarioController();
            
            // Manejar acciones
            switch ($action) {
                case 'index':
                    $controllerInstance->index();
                    break;
                    
                case 'create':
                    $controllerInstance->create();
                    break;
                    
                case 'edit':
                    $controllerInstance->edit();
                    break;
                    
                case 'delete':
                    $controllerInstance->delete();
                    break;
                    
                case 'view':
                    $controllerInstance->view();
                    break;
                    
                default:
                    throw new Exception("Acción no válida: {$action}");
            }
            break;
            
        default:
            throw new Exception("Controlador no válido: {$controller}");
    }
    
} catch (Exception $e) {
    // Manejo de errores
    http_response_code(500);
    echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Error</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .error { background: #fee2e2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 4px; }
        h1 { color: #991b1b; }
        pre { background: #f5f5f5; padding: 15px; overflow-x: auto; border-radius: 4px; }
    </style>
</head>
<body>
    <div class='error'>
        <h1>❌ Error</h1>
        <p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>
        <p><strong>Línea:</strong> " . htmlspecialchars($e->getLine()) . "</p>
        <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
    </div>
</body>
</html>";
}
