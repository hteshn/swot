<?php
/**
 * REPORTE DE VENTAS (ventas.php)
 * 
 * Reporte general de ventas con filtros por fecha.
 * Visible solo para administradores.
 */

require_once '../includes/auth.php';
requireRole(['admin']); // Solo admin puede ver reportes globales

$pdo = getDBConnection();

// Filtros de fecha
$fecha_inicio = $_GET['inicio'] ?? date('Y-m-01'); // Inicio del mes actual por defecto
$fecha_fin = $_GET['fin'] ?? date('Y-m-d'); // Hoy por defecto

// Consulta principal con JOINs para obtener detalles legibles
$sql = "SELECT v.id, v.numero_factura, v.fecha_venta, v.total, v.tipo_pago, 
               u.nombre_completo as vendedor, cl.nombre as cliente
        FROM ventas v
        LEFT JOIN usuarios u ON v.usuario_id = u.id
        LEFT JOIN clientes cl ON v.cliente_id = cl.id
        WHERE DATE(v.fecha_venta) BETWEEN :inicio AND :fin
        ORDER BY v.fecha_venta DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
$ventas = $stmt->fetchAll();

// Calcular totales del reporte
$total_ingresos = array_sum(array_column($ventas, 'total'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas - Salón POS</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { color: #2c3e50; }
        
        .filters { background: #ecf0f1; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end;}
        .form-group { display: flex; flex-direction: column; }
        label { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        input { padding: 8px; border: 1px solid #bdc3c7; border-radius: 4px; }
        button { padding: 8px 15px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #34495e; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        
        .total-box { 
            margin-top: 20px; text-align: right; 
            font-size: 20px; font-weight: bold; color: #27ae60; 
            border-top: 3px solid #27ae60; padding-top: 10px;
        }
        
        @media print {
            .filters, .no-print { display: none; }
            body { background: white; }
            .container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between;">
        <h2>📈 Reporte de Ventas</h2>
        <button onclick="window.print()" class="no-print">🖨️ Imprimir</button>
    </div>
    
    <!-- Filtros -->
    <form method="GET" class="filters no-print">
        <div class="form-group">
            <label>Desde:</label>
            <input type="date" name="inicio" value="<?php echo $fecha_inicio; ?>">
        </div>
        <div class="form-group">
            <label>Hasta:</label>
            <input type="date" name="fin" value="<?php echo $fecha_fin; ?>">
        </div>
        <button type="submit">Filtrar</button>
    </form>
    
    <p>Mostrando ventas del <strong><?php echo $fecha_inicio; ?></strong> al <strong><?php echo $fecha_fin; ?></strong></p>
    
    <table>
        <thead>
            <tr>
                <th># Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Pago</th>
                <th style="text-align:right;">Monto Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($ventas as $v): ?>
            <tr>
                <td>FAC-<?php echo str_pad($v['numero_factura'], 6, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($v['fecha_venta'])); ?></td>
                <td><?php echo htmlspecialchars($v['cliente'] ?? 'Mostrador'); ?></td>
                <td><?php echo htmlspecialchars($v['vendedor']); ?></td>
                <td><?php echo strtoupper($v['tipo_pago']); ?></td>
                <td style="text-align:right;">$<?php echo number_format($v['total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(count($ventas) === 0): ?>
            <tr><td colspan="6" style="text-align:center;">No hay ventas en este periodo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="total-box">
        Total Ingresos: $<?php echo number_format($total_ingresos, 2); ?>
    </div>
    
    <br>
    <a href="../dashboard.php" class="no-print">⬅ Volver al Dashboard</a>
</div>

</body>
</html>
