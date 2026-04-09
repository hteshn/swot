<?php
/**
 * Vista para editar usuario
 */
$pageTitle = 'Editar Usuario';
ob_start();
?>

<div class="form-container">
    <h2>👤 Editar Usuario del Sistema</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" action="?controller=usuario&action=edit&id=<?= $usuario['id'] ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" name="username" id="username" disabled value="<?= htmlspecialchars($usuario['username']) ?>" placeholder="nombre.usuario">
                <small style="color: #6c757d;">El nombre de usuario no se puede modificar</small>
            </div>

            <div class="form-group">
                <label for="password">Nueva Contraseña (dejar vacío si no desea cambiarla)</label>
                <input type="password" name="password" id="password" minlength="6" placeholder="Mínimo 6 caracteres">
            </div>
        </div>

        <div class="form-group">
            <label for="nombre_completo">Nombre Completo *</label>
            <input type="text" name="nombre_completo" id="nombre_completo" required 
                   value="<?= htmlspecialchars($usuario['nombre_completo']) ?>" 
                   placeholder="Ingrese el nombre completo">
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" required 
                   value="<?= htmlspecialchars($usuario['email']) ?>" 
                   placeholder="correo@ejemplo.com">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="celular">Celular / Teléfono</label>
                <input type="text" name="celular" id="celular" 
                       value="<?= htmlspecialchars($usuario['celular'] ?? '') ?>" 
                       placeholder="+54-11-1234-5678">
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" name="edad" id="edad" min="18" max="100" 
                       value="<?= htmlspecialchars($usuario['edad'] ?? '') ?>" 
                       placeholder="Ej: 30">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="pais">País</label>
                <input type="text" name="pais" id="pais" 
                       value="<?= htmlspecialchars($usuario['pais'] ?? '') ?>" 
                       placeholder="Ej: Argentina">
            </div>

            <div class="form-group">
                <label for="departamento">Departamento / Provincia</label>
                <input type="text" name="departamento" id="departamento" 
                       value="<?= htmlspecialchars($usuario['departamento'] ?? '') ?>" 
                       placeholder="Ej: Buenos Aires">
            </div>
        </div>

        <div class="form-group">
            <label for="rol">Rol en el Sistema *</label>
            <select name="rol" id="rol" required>
                <option value="vendedor" <?= $usuario['rol'] == 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="contador" <?= $usuario['rol'] == 'contador' ? 'selected' : '' ?>>Contador</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="?controller=usuario&action=index" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Actualizar Usuario</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
