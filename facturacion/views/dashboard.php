<?php
$pageTitle = 'Dashboard - Sistema de Facturación';
ob_start();
?>

<div class="dashboard">
    <h2>📊 Dashboard</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Facturas</h3>
            <p class="stat-number"><?= $estadisticas['total_facturas'] ?? 0 ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Facturado</h3>
            <p class="stat-number">$<?= number_format($estadisticas['total_facturado'] ?? 0, 2) ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Promedio por Factura</h3>
            <p class="stat-number">$<?= number_format($estadisticas['promedio_factura'] ?? 0, 2) ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Pagado</h3>
            <p class="stat-number success">$<?= number_format($estadisticas['total_pagado'] ?? 0, 2) ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Pendiente</h3>
            <p class="stat-number warning">$<?= number_format($estadisticas['total_pendiente'] ?? 0, 2) ?></p>
        </div>
    </div>

    <?php if (!empty($productosStockBajo)): ?>
    <div class="alert alert-warning">
        <h4>⚠️ Productos con Stock Bajo</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Stock Actual</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productosStockBajo as $producto): ?>
                <tr>
                    <td><?= htmlspecialchars($producto['codigo']) ?></td>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td class="warning"><?= $producto['stock'] ?></td>
                    <td><?= htmlspecialchars($producto['categoria']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="recent-invoices">
        <h3>📄 Facturas Recientes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($facturasRecientes)): ?>
                <tr>
                    <td colspan="5">No hay facturas registradas</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($facturasRecientes as $factura): ?>
                    <tr>
                        <td><?= htmlspecialchars($factura['numero_factura']) ?></td>
                        <td><?= htmlspecialchars($factura['cliente_nombre']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($factura['fecha_emision'])) ?></td>
                        <td>$<?= number_format($factura['total'], 2) ?></td>
                        <td>
                            <span class="badge badge-<?= $factura['estado'] ?>">
                                <?= ucfirst($factura['estado']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
