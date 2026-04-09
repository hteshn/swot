<?php
/**
 * GESTIÓN DE CLIENTES (listado.php)
 * 
 * Muestra la lista de clientes registrados con opción de búsqueda.
 */

require_once '../includes/auth.php';
requireLogin();

$pdo = getDBConnection();

// Búsqueda opcional
$search = $_GET['q'] ?? '';
$sql = "SELECT * FROM clientes WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (nombre LIKE :search OR telefono LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY nombre ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes - Salón POS</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #5c6bc0; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn { padding: 8px 12px; text-decoration: none; color: white; border-radius: 4px; display: inline-block;}
        .btn-new { background: #2ecc71; }
        .btn-edit { background: #f39c12; }
        input[type="text"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>👥 Listado de Clientes</h2>
        <a href="nuevo.php" class="btn btn-new">+ Nuevo Cliente</a>
    </div>
    
    <!-- Buscador -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="q" placeholder="Buscar por nombre, teléfono..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Buscar</button>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Puntos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($clientes as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                <td><?php echo htmlspecialchars($c['email']); ?></td>
                <td><?php echo $c['puntos_acumulados']; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $c['id']; ?>" class="btn btn-edit">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(count($clientes) === 0): ?>
            <tr><td colspan="6" style="text-align:center;">No se encontraron clientes.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <br>
    <a href="../dashboard.php">⬅ Volver al Dashboard</a>
</div>
</body>
</html>
