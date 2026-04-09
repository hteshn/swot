<?php
/**
 * Vista para crear nuevo usuario
 */
$pageTitle = 'Nuevo Usuario';
ob_start();
?>

<div class="form-container">
    <h2>👤 Nuevo Usuario del Sistema</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" action="?controller=usuario&action=create">
        <div class="form-row">
            <div class="form-group">
                <label for="username">Nombre de Usuario *</label>
                <input type="text" name="username" id="username" required placeholder="nombre.usuario" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" name="password" id="password" required minlength="6" placeholder="Mínimo 6 caracteres">
            </div>
        </div>

        <div class="form-group">
            <label for="nombre_completo">Nombre Completo *</label>
            <input type="text" name="nombre_completo" id="nombre_completo" required placeholder="Ingrese el nombre completo" value="<?= htmlspecialchars($_POST['nombre_completo'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" required placeholder="correo@ejemplo.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="celular">Celular / Teléfono</label>
                <input type="text" name="celular" id="celular" placeholder="+54-11-1234-5678" value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" name="edad" id="edad" min="18" max="100" placeholder="Ej: 30" value="<?= htmlspecialchars($_POST['edad'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="pais">País</label>
                <input type="text" name="pais" id="pais" placeholder="Ej: Argentina" value="<?= htmlspecialchars($_POST['pais'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="departamento">Departamento / Provincia</label>
                <input type="text" name="departamento" id="departamento" placeholder="Ej: Buenos Aires" value="<?= htmlspecialchars($_POST['departamento'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="rol">Rol en el Sistema *</label>
            <select name="rol" id="rol" required>
                <option value="vendedor" <?= ($_POST['rol'] ?? '') == 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                <option value="admin" <?= ($_POST['rol'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="contador" <?= ($_POST['rol'] ?? '') == 'contador' ? 'selected' : '' ?>>Contador</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="?controller=usuario&action=index" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Usuario</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
