<?php
$pageTitle = 'Ver Factura #' . ($factura['numero_factura'] ?? '');
ob_start();
?>

<div class="invoice-view">
    <div class="invoice-header">
        <h2>📄 Factura: <?= htmlspecialchars($factura['numero_factura']) ?></h2>
        <span class="badge badge-<?= $factura['estado'] ?>"><?= ucfirst($factura['estado']) ?></span>
    </div>

    <div class="invoice-info-grid">
        <div class="invoice-section">
            <h3>Información del Cliente</h3>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($factura['cliente_nombre']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($factura['cliente_email'] ?? 'N/A') ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($factura['cliente_telefono'] ?? 'N/A') ?></p>
            <p><strong>Dirección:</strong> <?= htmlspecialchars($factura['cliente_direccion'] ?? 'N/A') ?></p>
            <p><strong>RUC/NIT:</strong> <?= htmlspecialchars($factura['cliente_ruc'] ?? 'N/A') ?></p>
        </div>

        <div class="invoice-section">
            <h3>Información de la Factura</h3>
            <p><strong>Fecha de Emisión:</strong> <?= date('d/m/Y H:i', strtotime($factura['fecha_emision'])) ?></p>
            <p><strong>Estado:</strong> <?= ucfirst($factura['estado']) ?></p>
            <?php if ($factura['notas']): ?>
            <p><strong>Notas:</strong> <?= htmlspecialchars($factura['notas']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <h3>Detalle de Productos</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factura['detalles'] as $detalle): ?>
            <tr>
                <td><?= htmlspecialchars($detalle['producto_codigo']) ?></td>
                <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                <td><?= $detalle['cantidad'] ?></td>
                <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                <td>$<?= number_format($factura['subtotal'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Impuesto:</strong></td>
                <td>$<?= number_format($factura['impuesto'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                <td><strong>$<?= number_format($factura['total'], 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="actions-bar">
        <a href="?controller=facturacion&action=facturas" class="btn btn-secondary">← Volver al listado</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir Factura</button>
        <?php if ($factura['estado'] === 'pendiente'): ?>
            <a href="?controller=facturacion&action=facturas&action=pagar&id=<?= $factura['id'] ?>" class="btn btn-success">Marcar como Pagada</a>
        <?php endif; ?>
        <?php if ($factura['estado'] !== 'anulada'): ?>
            <a href="?controller=facturacion&action=facturas&action=anular&id=<?= $factura['id'] ?>" 
               class="btn btn-danger"
               onclick="return confirm('¿Está seguro de anular esta factura?')">Anular Factura</a>
        <?php endif; ?>
    </div>
</div>

<style>
@media print {
    .navbar, .actions-bar, .footer { display: none !important; }
    .invoice-view { max-width: 100% !important; }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
