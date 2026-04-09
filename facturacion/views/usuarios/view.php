<?php
/**
 * Vista para ver detalles de usuario
 */
$pageTitle = 'Ver Usuario';
ob_start();
?>

<div class="form-container">
    <h2>👤 Detalles del Usuario</h2>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <div class="form-row" style="margin-bottom: 15px;">
            <div class="form-group">
                <strong>ID:</strong><br>
                <?= htmlspecialchars($usuario['id']) ?>
            </div>

            <div class="form-group">
                <strong>Nombre de Usuario:</strong><br>
                <?= htmlspecialchars($usuario['username']) ?>
            </div>

            <div class="form-group">
                <strong>Rol:</strong><br>
                <span class="badge badge-<?= $usuario['rol'] == 'admin' ? 'danger' : ($usuario['rol'] == 'contador' ? 'warning' : 'info') ?>">
                    <?= ucfirst(htmlspecialchars($usuario['rol'])) ?>
                </span>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <strong>Nombre Completo:</strong><br>
            <?= htmlspecialchars($usuario['nombre_completo']) ?>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <strong>Email:</strong><br>
            <?= htmlspecialchars($usuario['email']) ?>
        </div>

        <div class="form-row" style="margin-bottom: 15px;">
            <div class="form-group">
                <strong>Celular / Teléfono:</strong><br>
                <?= htmlspecialchars($usuario['celular'] ?? '-') ?>
            </div>

            <div class="form-group">
                <strong>Edad:</strong><br>
                <?= htmlspecialchars($usuario['edad'] ?? '-') ?> años
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 15px;">
            <div class="form-group">
                <strong>País:</strong><br>
                <?= htmlspecialchars($usuario['pais'] ?? '-') ?>
            </div>

            <div class="form-group">
                <strong>Departamento / Provincia:</strong><br>
                <?= htmlspecialchars($usuario['departamento'] ?? '-') ?>
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 15px;">
            <div class="form-group">
                <strong>Estado:</strong><br>
                <span class="badge badge-<?= $usuario['estado'] == 1 ? 'success' : 'secondary' ?>">
                    <?= $usuario['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>

            <div class="form-group">
                <strong>Fecha de Creación:</strong><br>
                <?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?>
            </div>
        </div>

        <div class="form-group">
            <strong>Última Actualización:</strong><br>
            <?= date('d/m/Y H:i', strtotime($usuario['updated_at'])) ?>
        </div>
    </div>

    <div class="form-actions" style="margin-top: 20px;">
        <a href="?controller=usuario&action=index" class="btn btn-secondary">← Volver al Listado</a>
        <a href="?controller=usuario&action=edit&id=<?= $usuario['id'] ?>" class="btn btn-info">✏️ Editar Usuario</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
