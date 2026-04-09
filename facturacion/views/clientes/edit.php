<?php
/**
 * Vista para editar cliente
 */
$pageTitle = 'Editar Cliente';
ob_start();
?>

<div class="form-container">
    <h2>👥 Editar Cliente</h2>

    <form method="POST" action="?controller=facturacion&action=clientes&action=edit&id=<?= $cliente['id'] ?>">
        <div class="form-group">
            <label for="nombre">Nombre / Razón Social *</label>
            <input type="text" name="nombre" id="nombre" required 
                   value="<?= htmlspecialchars($cliente['nombre']) ?>" 
                   placeholder="Ingrese el nombre completo">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" 
                       value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" 
                       placeholder="correo@ejemplo.com">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" 
                       value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>" 
                       placeholder="+54-11-1234-5678">
            </div>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección</label>
            <textarea name="direccion" id="direccion" rows="3" 
                      placeholder="Dirección completa"><?= htmlspecialchars($cliente['direccion'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="ruc_nit">RUC / NIT / CUIT</label>
            <input type="text" name="ruc_nit" id="ruc_nit" 
                   value="<?= htmlspecialchars($cliente['ruc_nit'] ?? '') ?>" 
                   placeholder="Número de identificación fiscal">
        </div>

        <div class="form-actions">
            <a href="?controller=facturacion&action=clientes" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Actualizar Cliente</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
