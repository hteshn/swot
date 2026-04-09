<?php
/**
 * Vista para crear nuevo producto
 */
$pageTitle = 'Nuevo Producto';
ob_start();
?>

<div class="form-container">
    <h2>📦 Nuevo Producto</h2>

    <form method="POST" action="?controller=facturacion&action=productos&action=create">
        <div class="form-row">
            <div class="form-group">
                <label for="codigo">Código *</label>
                <input type="text" name="codigo" id="codigo" required placeholder="Ej: PROD-001">
            </div>

            <div class="form-group">
                <label for="categoria">Categoría</label>
                <input type="text" name="categoria" id="categoria" placeholder="Ej: Electrónica">
            </div>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Producto *</label>
            <input type="text" name="nombre" id="nombre" required placeholder="Nombre descriptivo del producto">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3" placeholder="Descripción detallada del producto"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="precio_unitario">Precio Unitario *</label>
                <input type="number" name="precio_unitario" id="precio_unitario" 
                       step="0.01" min="0" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="stock">Stock Inicial *</label>
                <input type="number" name="stock" id="stock" min="0" value="0" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="?controller=facturacion&action=productos" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
