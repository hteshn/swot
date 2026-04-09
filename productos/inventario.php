<?php
/**
 * GESTIÓN DE INVENTARIO (inventario.php)
 * 
 * Muestra el listado de productos con alertas de stock bajo.
 */

require_once '../includes/auth.php';
requireLogin();

$pdo = getDBConnection();

// Consulta productos y verifica si están por debajo del mínimo
$sql = "SELECT *, 
        CASE WHEN stock_actual <= stock_minimo THEN 1 ELSE 0 END as alerta_stock
        FROM productos 
        WHERE activo = 1 
        ORDER BY nombre ASC";

$productos = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - Salón POS</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { border-bottom: 2px solid #e67e22; padding-bottom: 10px; display: inline-block;}
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #e67e22; color: white; }
        
        /* Alerta de stock bajo */
        .stock-bajo { background-color: #ffebee; color: #c62828; font-weight: bold; }
        .stock-ok { color: #2ecc71; }
        
        .btn-new { background: #2ecc71; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; color: white; }
        .bg-red { background: #e74c3c; }
        .bg-green { background: #2ecc71; }
    </style>
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>📦 Inventario de Productos</h2>
        <a href="#" class="btn-new">+ Nuevo Producto</a>
    </div>
    
    <p>Gestione el stock de productos cosméticos y de venta.</p>
    
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Precio Venta</th>
                <th>Stock Actual</th>
                <th>Stock Mín.</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $p): ?>
            <tr class="<?php echo $p['alerta_stock'] ? 'stock-bajo' : ''; ?>">
                <td><?php echo htmlspecialchars($p['codigo_barras'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td>$<?php echo number_format($p['precio_venta'], 2); ?></td>
                <td><?php echo $p['stock_actual']; ?></td>
                <td><?php echo $p['stock_minimo']; ?></td>
                <td>
                    <?php if($p['alerta_stock']): ?>
                        <span class="badge bg-red">⚠️ Reordenar</span>
                    <?php else: ?>
                        <span class="badge bg-green">OK</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br>
    <a href="../dashboard.php">⬅ Volver al Dashboard</a>
</div>

</body>
</html>
