<?php
/**
 * GESTIÓN DE CITAS / AGENDA (citas.php)
 * 
 * Calendario simple para agendar citas de servicios con estilistas.
 */

require_once '../includes/auth.php';
requireLogin();

$pdo = getDBConnection();

// Obtener citas para mostrar (hoy y futuras)
$sql = "SELECT c.id, c.fecha_cita, c.estado, cl.nombre as cliente, e.nombre as estilista, s.nombre as servicio
        FROM citas c
        JOIN clientes cl ON c.cliente_id = cl.id
        JOIN empleados e ON c.empleado_id = e.id
        JOIN servicios s ON c.servicio_id = s.id
        WHERE c.fecha_cita >= CURDATE()
        ORDER BY c.fecha_cita ASC";

$citas = $pdo->query($sql)->fetchAll();

// Listas para formulario
$empleados = $pdo->query("SELECT id, nombre FROM empleados")->fetchAll();
$servicios = $pdo->query("SELECT id, nombre FROM servicios WHERE activo=1")->fetchAll();
$clientes = $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Citas - Salón POS</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { border-bottom: 2px solid #9b59b6; padding-bottom: 10px; display: inline-block;}
        
        .grid-citas { display: grid; gap: 15px; margin-top: 20px; }
        .cita-card { 
            background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 6px; 
            border-left: 5px solid #9b59b6; display: flex; justify-content: space-between; align-items: center;
        }
        .cita-info strong { display: block; font-size: 16px; color: #2c3e50; }
        .cita-info span { color: #7f8c8d; font-size: 14px; }
        
        .badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; color: white; }
        .bg-pending { background: #f39c12; }
        .bg-conf { background: #3498db; }
        .bg-done { background: #2ecc71; }
        
        form { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .form-row { display: flex; gap: 10px; margin-bottom: 10px; }
        select, input { flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #9b59b6; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;}
    </style>
</head>
<body>

<div class="container">
    <h2>📅 Agenda de Citas</h2>
    
    <!-- Formulario Nueva Cita -->
    <form method="POST" action="guardar_cita.php">
        <h3>Nueva Reserva</h3>
        <div class="form-row">
            <select name="cliente_id" required>
                <option value="">Cliente...</option>
                <?php foreach($clientes as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select name="empleado_id" required>
                <option value="">Estilista...</option>
                <?php foreach($empleados as $e): ?>
                    <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <select name="servicio_id" required>
                <option value="">Servicio...</option>
                <?php foreach($servicios as $s): ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="datetime-local" name="fecha_cita" required>
            
            <button type="submit">Agendar</button>
        </div>
    </form>
    
    <hr>
    
    <!-- Listado de Citas -->
    <h3>Próximas Citas</h3>
    <div class="grid-citas">
        <?php foreach($citas as $cita): ?>
        <div class="cita-card">
            <div class="cita-info">
                <strong><?php echo date('d/m/Y H:i', strtotime($cita['fecha_cita'])); ?></strong>
                <span>👤 <?php echo htmlspecialchars($cita['cliente']); ?></span> | 
                <span>💇‍♀️ <?php echo htmlspecialchars($cita['estilista']); ?></span> | 
                <span>✂️ <?php echo htmlspecialchars($cita['servicio']); ?></span>
            </div>
            <div>
                <span class="badge 
                    <?php echo $cita['estado'] === 'pendiente' ? 'bg-pending' : ($cita['estado'] === 'confirmada' ? 'bg-conf' : 'bg-done'); ?>">
                    <?php echo strtoupper($cita['estado']); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(count($citas) === 0): ?>
            <p>No hay citas programadas.</p>
        <?php endif; ?>
    </div>
    
    <br>
    <a href="../dashboard.php">⬅ Volver al Dashboard</a>
</div>

</body>
</html>
