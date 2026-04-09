<?php
$pageTitle = 'Listado de Usuarios';
ob_start();
?>

<div class="page-header">
    <h2>👤 Usuarios del Sistema</h2>
    <a href="?controller=usuario&action=create" class="btn btn-primary">Nuevo Usuario</a>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] == 'created'): ?>
    <div class="alert alert-success">Usuario creado exitosamente</div>
<?php elseif (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
    <div class="alert alert-success">Usuario actualizado exitosamente</div>
<?php elseif (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
    <div class="alert alert-success">Usuario eliminado exitosamente</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'cannot_delete_self'): ?>
    <div class="alert alert-warning">No puedes eliminar tu propio usuario</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] == 'delete_failed'): ?>
    <div class="alert alert-danger">Error al eliminar el usuario</div>
<?php endif; ?>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Nombre Completo</th>
            <th>Email</th>
            <th>Celular</th>
            <th>Edad</th>
            <th>País</th>
            <th>Departamento</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($usuarios)): ?>
        <tr>
            <td colspan="9">No hay usuarios registrados</td>
        </tr>
        <?php else: ?>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= $usuario['id'] ?></td>
                <td><?= htmlspecialchars($usuario['username']) ?></td>
                <td><?= htmlspecialchars($usuario['nombre_completo']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['celular'] ?? '-') ?></td>
                <td><?= htmlspecialchars($usuario['edad'] ?? '-') ?></td>
                <td><?= htmlspecialchars($usuario['pais'] ?? '-') ?></td>
                <td><?= htmlspecialchars($usuario['departamento'] ?? '-') ?></td>
                <td>
                    <span class="badge badge-<?= $usuario['rol'] == 'admin' ? 'danger' : ($usuario['rol'] == 'contador' ? 'warning' : 'info') ?>">
                        <?= ucfirst(htmlspecialchars($usuario['rol'])) ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="?controller=usuario&action=view&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
                    <a href="?controller=usuario&action=edit&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-info">Editar</a>
                    <a href="?controller=usuario&action=delete&id=<?= $usuario['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('¿Está seguro de eliminar este usuario?')">Eliminar</a>
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
