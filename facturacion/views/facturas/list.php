<?php
$pageTitle = 'Listado de Facturas';
ob_start();
?>

<div class="page-header">
    <h2>📄 Facturas</h2>
    <a href="?controller=facturacion&action=facturas&action=create" class="btn btn-primary">Nueva Factura</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Número</th>
            <th>Cliente</th>
            <th>Fecha Emisión</th>
            <th>Subtotal</th>
            <th>Impuesto</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($facturas)): ?>
        <tr>
            <td colspan="8">No hay facturas registradas</td>
        </tr>
        <?php else: ?>
            <?php foreach ($facturas as $factura): ?>
            <tr>
                <td><?= htmlspecialchars($factura['numero_factura']) ?></td>
                <td><?= htmlspecialchars($factura['cliente_nombre']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($factura['fecha_emision'])) ?></td>
                <td>$<?= number_format($factura['subtotal'], 2) ?></td>
                <td>$<?= number_format($factura['impuesto'], 2) ?></td>
                <td><strong>$<?= number_format($factura['total'], 2) ?></strong></td>
                <td>
                    <span class="badge badge-<?= $factura['estado'] ?>">
                        <?= ucfirst($factura['estado']) ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="?controller=facturacion&action=facturas&action=view&id=<?= $factura['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                    <?php if ($factura['estado'] === 'pendiente'): ?>
                        <a href="?controller=facturacion&action=facturas&action=pagar&id=<?= $factura['id'] ?>" 
                           class="btn btn-sm btn-success">Pagar</a>
                    <?php endif; ?>
                    <?php if ($factura['estado'] !== 'anulada'): ?>
                        <a href="?controller=facturacion&action=facturas&action=anular&id=<?= $factura['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Está seguro de anular esta factura?')">Anular</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
