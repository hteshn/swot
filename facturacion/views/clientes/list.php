<?php
$pageTitle = 'Listado de Clientes';
ob_start();
?>

<div class="page-header">
    <h2>👥 Clientes</h2>
    <a href="?controller=facturacion&action=clientes&action=create" class="btn btn-primary">Nuevo Cliente</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>RUC/NIT</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($clientes)): ?>
        <tr>
            <td colspan="6">No hay clientes registrados</td>
        </tr>
        <?php else: ?>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= $cliente['id'] ?></td>
                <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                <td><?= htmlspecialchars($cliente['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($cliente['telefono'] ?? '-') ?></td>
                <td><?= htmlspecialchars($cliente['ruc_nit'] ?? '-') ?></td>
                <td class="actions">
                    <a href="?controller=facturacion&action=clientes&action=edit&id=<?= $cliente['id'] ?>" class="btn btn-sm btn-info">Editar</a>
                    <a href="?controller=facturacion&action=clientes&action=delete&id=<?= $cliente['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('¿Está seguro de eliminar este cliente?')">Eliminar</a>
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
