<?php
$pageTitle = 'Crear Nueva Factura';
ob_start();
?>

<div class="form-container">
    <h2>📄 Crear Nueva Factura</h2>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="facturaForm">
        <div class="form-row">
            <div class="form-group">
                <label for="cliente_id">Cliente *</label>
                <select name="cliente_id" id="cliente_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id'] ?>">
                        <?= htmlspecialchars($cliente['nombre']) ?> 
                        <?php if ($cliente['ruc_nit']): ?> (<?= htmlspecialchars($cliente['ruc_nit']) ?>) <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="impuesto_porcentaje">Impuesto (%) *</label>
                <input type="number" name="impuesto_porcentaje" id="impuesto_porcentaje" 
                       step="0.01" min="0" max="100" value="21" required>
            </div>
        </div>

        <div class="form-group">
            <label>Productos</label>
            <div id="productosContainer">
                <div class="producto-row">
                    <select name="productos[0][producto_id]" class="producto-select" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>" 
                                data-precio="<?= $producto['precio_unitario'] ?>"
                                data-stock="<?= $producto['stock'] ?>"
                                data-nombre="<?= htmlspecialchars($producto['nombre']) ?>">
                            <?= htmlspecialchars($producto['nombre']) ?> - 
                            $<?= number_format($producto['precio_unitario'], 2) ?> 
                            (Stock: <?= $producto['stock'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="productos[0][cantidad]" class="cantidad-input" 
                           placeholder="Cantidad" min="1" value="1" required>
                    <input type="number" name="productos[0][precio_unitario]" class="precio-input" 
                           placeholder="Precio" step="0.01" min="0" readonly>
                    <button type="button" class="btn btn-sm btn-danger remove-product" disabled>×</button>
                </div>
            </div>
            <button type="button" id="addProduct" class="btn btn-secondary">+ Agregar Producto</button>
        </div>

        <div class="form-group">
            <label for="notas">Notas</label>
            <textarea name="notas" id="notas" rows="3" placeholder="Notas adicionales..."></textarea>
        </div>

        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span id="subtotalDisplay">$0.00</span>
            </div>
            <div class="total-row">
                <span>Impuesto:</span>
                <span id="impuestoDisplay">$0.00</span>
            </div>
            <div class="total-row total">
                <span>Total:</span>
                <span id="totalDisplay">$0.00</span>
            </div>
        </div>

        <div class="form-actions">
            <a href="?controller=facturacion&action=facturas" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Factura</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 1;
    
    // Actualizar precio cuando se selecciona un producto
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select')) {
            const row = e.target.closest('.producto-row');
            const selectedOption = e.target.options[e.target.selectedIndex];
            const precioInput = row.querySelector('.precio-input');
            
            if (selectedOption.dataset.precio) {
                precioInput.value = selectedOption.dataset.precio;
            } else {
                precioInput.value = '';
            }
            calculateTotals();
        }
        
        if (e.target.classList.contains('cantidad-input') || e.target.classList.contains('precio-input')) {
            calculateTotals();
        }
    });
    
    // Agregar nuevo producto
    document.getElementById('addProduct').addEventListener('click', function() {
        const container = document.getElementById('productosContainer');
        const newRow = document.createElement('div');
        newRow.className = 'producto-row';
        newRow.innerHTML = `
            <select name="productos[${productIndex}][producto_id]" class="producto-select" required>
                <option value="">Seleccione un producto</option>
                ${Array.from(document.querySelector('.producto-select').options)
                    .filter(opt => opt.value !== '')
                    .map(opt => `<option value="${opt.value}" 
                            data-precio="${opt.dataset.precio}"
                            data-stock="${opt.dataset.stock}"
                            data-nombre="${opt.dataset.nombre}">${opt.text}</option>`)
                    .join('')}
            </select>
            <input type="number" name="productos[${productIndex}][cantidad]" class="cantidad-input" 
                   placeholder="Cantidad" min="1" value="1" required>
            <input type="number" name="productos[${productIndex}][precio_unitario]" class="precio-input" 
                   placeholder="Precio" step="0.01" min="0" readonly>
            <button type="button" class="btn btn-sm btn-danger remove-product">×</button>
        `;
        container.appendChild(newRow);
        productIndex++;
    });
    
    // Eliminar producto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            const rows = document.querySelectorAll('.producto-row');
            if (rows.length > 1) {
                e.target.closest('.producto-row').remove();
                calculateTotals();
            }
        }
    });
    
    // Calcular totales
    function calculateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('.producto-row').forEach(row => {
            const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
            const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
            subtotal += cantidad * precio;
        });
        
        const impuestoPorcentaje = parseFloat(document.getElementById('impuesto_porcentaje').value) || 0;
        const impuesto = subtotal * (impuestoPorcentaje / 100);
        const total = subtotal + impuesto;
        
        document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('impuestoDisplay').textContent = '$' + impuesto.toFixed(2);
        document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
    }
    
    // Recalcular al cambiar el porcentaje de impuesto
    document.getElementById('impuesto_porcentaje').addEventListener('change', calculateTotals);
});
</script>

<style>
.producto-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 10px;
    margin-bottom: 10px;
    align-items: center;
}

.totals-section {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
}

.total-row.total {
    font-size: 1.2em;
    font-weight: bold;
    border-top: 2px solid #333;
    margin-top: 10px;
    padding-top: 10px;
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
