<?php
$pageTitle = 'Listado de Productos';
ob_start();
?>

<div class="page-header">
    <h2>📦 Productos</h2>
    <a href="?controller=facturacion&action=productos&action=create" class="btn btn-primary">Nuevo Producto</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Categoría</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productos)): ?>
        <tr>
            <td colspan="7">No hay productos registrados</td>
        </tr>
        <?php else: ?>
            <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?= htmlspecialchars($producto['codigo']) ?></td>
                <td><?= htmlspecialchars($producto['nombre']) ?></td>
                <td><?= htmlspecialchars(substr($producto['descripcion'] ?? '', 0, 50)) ?>...</td>
                <td>$<?= number_format($producto['precio_unitario'], 2) ?></td>
                <td class="<?= $producto['stock'] <= 10 ? 'warning' : '' ?>">
                    <?= $producto['stock'] ?>
                </td>
                <td><?= htmlspecialchars($producto['categoria'] ?? '-') ?></td>
                <td class="actions">
                    <a href="?controller=facturacion&action=productos&action=edit&id=<?= $producto['id'] ?>" class="btn btn-sm btn-info">Editar</a>
                    <a href="?controller=facturacion&action=productos&action=delete&id=<?= $producto['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</a>
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
