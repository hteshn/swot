<?php
/**
 * DASHBOARD PRINCIPAL (dashboard.php)
 * 
 * Panel de control principal que muestra resumen y menú de navegación.
 * Adaptable según el rol del usuario.
 */

require_once 'includes/auth.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener datos básicos para el dashboard
$pdo = getDBConnection();
$user_role = $_SESSION['user_role'];
$user_name = $_SESSION['user_name'];

// Contadores rápidos (solo si es admin o cajero)
$total_ventas_hoy = 0;
$monto_ventas_hoy = 0;

if ($user_role !== 'estilista') {
    $stmt = $pdo->query("SELECT COUNT(*) as count, SUM(total) as total FROM ventas WHERE DATE(fecha_venta) = CURDATE()");
    $stats = $stmt->fetch();
    $total_ventas_hoy = $stats['count'];
    $monto_ventas_hoy = $stats['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Salón POS</title>
    <style>
        /* Estilos generales */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f5f6fa; display: flex; }
        
        /* Barra lateral (Sidebar) */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: #bdc3c7;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover { color: white; background-color: #34495e; }
        .sidebar .logout { position: absolute; bottom: 20px; width: 100%; box-sizing: border-box; background: #c0392b; }
        
        /* Contenido principal */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }
        
        /* Header superior */
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Tarjetas de estadísticas */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 5px solid #5c6bc0;
        }
        .card h3 { margin: 0; color: #7f8c8d; font-size: 14px; }
        .card p { font-size: 24px; font-weight: bold; color: #2c3e50; margin: 10px 0 0 0; }
        
        /* Accesos rápidos */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .action-btn {
            background: white;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            border: 1px solid #eee;
        }
        .action-btn:hover { transform: translateY(-3px); border-color: #5c6bc0; }
        .action-btn span { font-size: 30px; display: block; margin-bottom: 10px; }
        
    </style>
</head>
<body>

    <!-- Menú Lateral -->
    <div class="sidebar">
        <h2>💇 Salón POS</h2>
        <a href="dashboard.php">📊 Dashboard</a>
        
        <?php if ($user_role === 'admin' || $user_role === 'cajero'): ?>
            <a href="ventas/nueva_venta.php">🛒 Nueva Venta</a>
            <a href="ventas/historial.php">📜 Historial Ventas</a>
            <a href="caja/apertura.php">💰 Caja</a>
        <?php endif; ?>
        
        <a href="clientes/listado.php">👥 Clientes</a>
        <a href="servicios/listado.php">💇 Servicios</a>
        <a href="productos/inventario.php">📦 Productos</a>
        
        <?php if ($user_role === 'admin' || $user_role === 'estilista'): ?>
            <a href="agenda/citas.php">📅 Agenda Citas</a>
        <?php endif; ?>
        
        <?php if ($user_role === 'admin'): ?>
            <a href="usuarios/listado.php">👤 Usuarios</a>
            <a href="reportes/ventas.php">📈 Reportes</a>
        <?php endif; ?>
        
        <a href="logout.php" class="logout">🚪 Cerrar Sesión</a>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="header">
            <h1>Bienvenido, <?php echo htmlspecialchars($user_name); ?></h1>
            <span style="background:#eee; padding:5px 10px; border-radius:4px; font-size:14px;">
                Rol: <strong><?php echo strtoupper($_SESSION['user_role']); ?></strong>
            </span>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="cards-container">
            <?php if ($user_role !== 'estilista'): ?>
            <div class="card">
                <h3>Ventas Hoy</h3>
                <p><?php echo $total_ventas_hoy; ?></p>
            </div>
            <div class="card" style="border-left-color: #2ecc71;">
                <h3>Ingresos Hoy</h3>
                <p>$<?php echo number_format($monto_ventas_hoy, 2); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="card" style="border-left-color: #e67e22;">
                <h3>Fecha</h3>
                <p><?php echo date('d/m/Y'); ?></p>
            </div>
        </div>

        <!-- Botones de Acción Rápida -->
        <h2>Accesos Rápidos</h2>
        <div class="quick-actions">
            <?php if ($user_role === 'admin' || $user_role === 'cajero'): ?>
            <a href="ventas/nueva_venta.php" class="action-btn">
                <span>🛒</span> Facturar
            </a>
            <?php endif; ?>
            
            <a href="clientes/nuevo.php" class="action-btn">
                <span>👤</span> Nuevo Cliente
            </a>
            
            <a href="agenda/citas.php" class="action-btn">
                <span>📅</span> Agendar Cita
            </a>
            
            <a href="productos/inventario.php" class="action-btn">
                <span>📦</span> Inventario
            </a>
        </div>
    </div>

</body>
</html>
