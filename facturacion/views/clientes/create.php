<?php
/**
 * Vista para crear nuevo cliente
 */
$pageTitle = 'Nuevo Cliente';
ob_start();
?>

<div class="form-container">
    <h2>👥 Nuevo Cliente</h2>

    <form method="POST" action="?controller=facturacion&action=clientes&action=create">
        <div class="form-group">
            <label for="nombre">Nombre / Razón Social *</label>
            <input type="text" name="nombre" id="nombre" required placeholder="Ingrese el nombre completo">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="correo@ejemplo.com">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" placeholder="+54-11-1234-5678">
            </div>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección</label>
            <textarea name="direccion" id="direccion" rows="3" placeholder="Dirección completa"></textarea>
        </div>

        <div class="form-group">
            <label for="ruc_nit">RUC / NIT / CUIT</label>
            <input type="text" name="ruc_nit" id="ruc_nit" placeholder="Número de identificación fiscal">
        </div>

        <div class="form-actions">
            <a href="?controller=facturacion&action=clientes" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Cliente</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
