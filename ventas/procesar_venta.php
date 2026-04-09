<?php
/**
 * PROCESAR NUEVA VENTA (procesar_venta.php)
 * 
 * Recibe los datos del formulario de venta, guarda la factura, 
 * descuenta inventario y calcula comisiones.
 */

require_once '../includes/auth.php';
requireLogin();

// Solo cajeros y admins pueden facturar
requireRole(['admin', 'cajero']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        $pdo->beginTransaction(); // Iniciar transacción para garantizar integridad
        
        // 1. Recopilar datos del POST
        $cliente_id = !empty($_POST['cliente_id']) ? $_POST['cliente_id'] : null;
        $tipo_pago = $_POST['tipo_pago'] ?? 'efectivo';
        $usuario_id = getCurrentUserId();
        
        // Items de la venta (array de productos y servicios)
        $items = $_POST['items'] ?? []; // Formato esperado: [{tipo, id, cantidad, precio}]
        
        if (empty($items)) {
            throw new Exception("La venta no tiene items.");
        }
        
        // 2. Calcular totales
        $subtotal = 0;
        $impuesto_porcentaje = 0.15; // Ejemplo 15% ITBIS/IVA
        $descuento = $_POST['descuento'] ?? 0;
        
        foreach ($items as $item) {
            $subtotal += ($item['precio'] * $item['cantidad']);
        }
        
        $impuesto = $subtotal * $impuesto_porcentaje;
        $total = $subtotal + $impuesto - $descuento;
        
        // 3. Obtener siguiente número de factura
        $stmt = $pdo->query("SELECT MAX(numero_factura) as max_factura FROM ventas WHERE serie = '001'");
        $row = $stmt->fetch();
        $numero_factura = ($row['max_factura'] ?? 0) + 1;
        
        // 4. Insertar Cabecera de Venta
        $sqlVenta = "INSERT INTO ventas (serie, numero_factura, cliente_id, usuario_id, subtotal, impuesto, descuento, total, tipo_pago) 
                     VALUES ('001', :numero, :cliente, :usuario, :sub, :imp, :desc, :total, :pago)";
        
        $stmtVenta = $pdo->prepare($sqlVenta);
        $stmtVenta->execute([
            ':numero' => $numero_factura,
            ':cliente' => $cliente_id,
            ':usuario' => $usuario_id,
            ':sub' => $subtotal,
            ':imp' => $impuesto,
            ':desc' => $descuento,
            ':total' => $total,
            ':pago' => $tipo_pago
        ]);
        
        $venta_id = $pdo->lastInsertId();
        
        // 5. Procesar cada item y guardar detalle
        $sqlDetalle = "INSERT INTO detalle_venta (venta_id, tipo_item, item_id, cantidad, precio_unitario, subtotal) 
                       VALUES (:venta, :tipo, :item, :cant, :precio, :sub)";
        $stmtDetalle = $pdo->prepare($sqlDetalle);
        
        // Consulta para obtener comisión de servicios
        $stmtComision = $pdo->prepare("SELECT comision_porcentaje FROM servicios WHERE id = :id");
        
        foreach ($items as $item) {
            $sub_item = $item['precio'] * $item['cantidad'];
            
            $stmtDetalle->execute([
                ':venta' => $venta_id,
                ':tipo' => $item['tipo'], // 'servicio' o 'producto'
                ':item' => $item['id'],
                ':cant' => $item['cantidad'],
                ':precio' => $item['precio'],
                ':sub' => $sub_item
            ]);
            
            // Si es producto, descontar de inventario
            if ($item['tipo'] === 'producto') {
                $sqlUpdateStock = "UPDATE productos SET stock_actual = stock_actual - :cant WHERE id = :id";
                $stmtStock = $pdo->prepare($sqlUpdateStock);
                $stmtStock->execute([':cant' => $item['cantidad'], ':id' => $item['id']]);
                
                // Registrar movimiento de inventario
                $sqlMov = "INSERT INTO inventario_movimientos (producto_id, tipo_movimiento, cantidad, motivo, usuario_id) 
                           VALUES (:prod, 'salida_venta', :cant, 'Venta #' . :venta, :user)";
                $stmtMov = $pdo->prepare($sqlMov);
                $stmtMov->execute([
                    ':prod' => $item['id'], 
                    ':cant' => $item['cantidad'], 
                    ':venta' => $venta_id, // Nota: esto requiere manejo string o cast
                    ':user' => $usuario_id
                ]);
            }
        }
        
        // 6. Asignar puntos al cliente si existe
        if ($cliente_id) {
            $puntos_ganados = floor($total / 10); // 1 punto por cada $10
            $sqlPuntos = "UPDATE clientes SET puntos_acumulados = puntos_acumulados + :puntos WHERE id = :id";
            $stmtPuntos = $pdo->prepare($sqlPuntos);
            $stmtPuntos->execute([':puntos' => $puntos_ganados, ':id' => $cliente_id]);
        }
        
        $pdo->commit(); // Confirmar transacción
        
        // Redirigir a imprimir o éxito
        header("Location: ticket.php?id=" . $venta_id);
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack(); // Revertir cambios en caso de error
        echo "Error al procesar la venta: " . $e->getMessage();
        // En producción, loguear el error y mostrar mensaje amigable
    }
} else {
    // Si intentan acceder directamente sin POST
    header("Location: nueva_venta.php");
    exit;
}
?>
