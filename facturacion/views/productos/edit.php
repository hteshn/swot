<?php
/**
 * Vista para editar producto
 */
$pageTitle = 'Editar Producto';
ob_start();
?>

<div class="form-container">
    <h2>📦 Editar Producto</h2>

    <form method="POST" action="?controller=facturacion&action=productos&action=edit&id=<?= $producto['id'] ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="codigo">Código *</label>
                <input type="text" name="codigo" id="codigo" required 
                       value="<?= htmlspecialchars($producto['codigo']) ?>" 
                       placeholder="Ej: PROD-001">
            </div>

            <div class="form-group">
                <label for="categoria">Categoría</label>
                <input type="text" name="categoria" id="categoria" 
                       value="<?= htmlspecialchars($producto['categoria'] ?? '') ?>" 
                       placeholder="Ej: Electrónica">
            </div>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Producto *</label>
            <input type="text" name="nombre" id="nombre" required 
                   value="<?= htmlspecialchars($producto['nombre']) ?>" 
                   placeholder="Nombre descriptivo del producto">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3" 
                      placeholder="Descripción detallada del producto"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="precio_unitario">Precio Unitario *</label>
                <input type="number" name="precio_unitario" id="precio_unitario" 
                       step="0.01" min="0" required 
                       value="<?= number_format($producto['precio_unitario'], 2, '.', '') ?>">
            </div>

            <div class="form-group">
                <label for="stock">Stock Actual *</label>
                <input type="number" name="stock" id="stock" min="0" 
                       value="<?= $producto['stock'] ?>" required>
                <small>Stock actual: <?= $producto['stock'] ?> unidades</small>
            </div>
        </div>

        <div class="form-actions">
            <a href="?controller=facturacion&action=productos" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Actualizar Producto</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
