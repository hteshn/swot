<?php
/**
 * PUNTO DE VENTA / FACTURACIÓN (nueva_venta.php)
 * 
 * Interfaz principal para realizar ventas. Permite agregar servicios y productos,
 * seleccionar cliente y método de pago. Usa JavaScript para cálculos en tiempo real.
 */

require_once '../includes/auth.php';
requireLogin();
requireRole(['admin', 'cajero']);

$pdo = getDBConnection();

// Obtener listas para los selects
$servicios = $pdo->query("SELECT id, nombre, precio FROM servicios WHERE activo = 1")->fetchAll();
$productos = $pdo->query("SELECT id, nombre, precio_venta, stock_actual FROM productos WHERE activo = 1 AND stock_actual > 0")->fetchAll();
$clientes = $pdo->query("SELECT id, nombre, telefono FROM clientes ORDER BY nombre LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Venta - Salón POS</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #5c6bc0; padding-bottom: 10px; }
        
        /* Grid layout */
        .row { display: flex; gap: 20px; margin-bottom: 20px; }
        .col { flex: 1; }
        
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;}
        
        button { cursor: pointer; padding: 10px 15px; border: none; border-radius: 4px; }
        .btn-add { background: #2ecc71; color: white; }
        .btn-save { background: #5c6bc0; color: white; font-size: 16px; width: 100%; }
        .btn-remove { background: #e74c3c; color: white; padding: 5px 10px; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f8f9fa; }
        
        .totals { text-align: right; margin-top: 20px; font-size: 18px; }
        .total-final { font-size: 24px; color: #2c3e50; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>🛒 Nueva Venta / Facturación</h2>
    
    <form id="ventaForm" action="procesar_venta.php" method="POST">
        
        <!-- Selección de Cliente -->
        <div class="row">
            <div class="col">
                <label>Cliente:</label>
                <select name="cliente_id" id="cliente_id">
                    <option value="">-- Cliente Mostrador --</option>
                    <?php foreach($clientes as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label>Método de Pago:</label>
                <select name="tipo_pago" required>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta Crédito/Débito</option>
                    <option value="transferencia">Transferencia Bancaria</option>
                    <option value="mixto">Mixto</option>
                </select>
            </div>
        </div>

        <!-- Agregar Items -->
        <div class="row" style="background: #eee; padding: 15px; border-radius: 4px;">
            <div class="col">
                <label>Agregar Servicio:</label>
                <select id="sel_servicio">
                    <option value="">-- Seleccione --</option>
                    <?php foreach($servicios as $s): ?>
                        <option value="<?php echo $s['id']; ?>" data-precio="<?php echo $s['precio']; ?>">
                            <?php echo htmlspecialchars($s['nombre']); ?> - $<?php echo $s['precio']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label>Agregar Producto:</label>
                <select id="sel_producto">
                    <option value="">-- Seleccione --</option>
                    <?php foreach($productos as $p): ?>
                        <option value="<?php echo $p['id']; ?>" data-precio="<?php echo $p['precio_venta']; ?>" data-stock="<?php echo $p['stock_actual']; ?>">
                            <?php echo htmlspecialchars($p['nombre']); ?> (Stock: <?php echo $p['stock_actual']; ?>) - $<?php echo $p['precio_venta']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col" style="flex: 0 0 100px; display:flex; align-items:flex-end;">
                <button type="button" class="btn-add" onclick="agregarItem()">+ Agregar</button>
            </div>
        </div>

        <!-- Tabla de Items -->
        <table id="tablaVenta">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Cant.</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los items se agregan aquí con JS -->
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totals">
            <div>Subtotal: $<span id="lbl_subtotal">0.00</span></div>
            <div>Impuesto (15%): $<span id="lbl_impuesto">0.00</span></div>
            <div>Descuento: $<input type="number" id="descuento" name="descuento" value="0" min="0" style="width: 80px; display:inline;" onchange="calcularTotales()"></div>
            <div class="total-final">TOTAL: $<span id="lbl_total">0.00</span></div>
        </div>

        <br>
        <button type="submit" class="btn-save">💾 CONFIRMAR Y FACTURAR</button>
    </form>
</div>

<script>
    // Array para almacenar los items de la venta
    let itemsVenta = [];

    function agregarItem() {
        const selServ = document.getElementById('sel_servicio');
        const selProd = document.getElementById('sel_producto');
        
        // Verificar si se seleccionó servicio o producto
        if (selServ.value) {
            const option = selServ.options[selServ.selectedIndex];
            addItemToRow('servicio', selServ.value, option.text.split(' - ')[0], parseFloat(option.dataset.precio));
            selServ.value = "";
        } else if (selProd.value) {
            const option = selProd.options[selProd.selectedIndex];
            addItemToRow('producto', selProd.value, option.text.split(' (')[0], parseFloat(option.dataset.precio));
            selProd.value = "";
        } else {
            alert("Seleccione un servicio o producto");
        }
    }

    function addItemToRow(tipo, id, nombre, precio) {
        // Buscar si ya existe para aumentar cantidad
        const existing = itemsVenta.find(i => i.tipo === tipo && i.id === id);
        if (existing) {
            existing.cantidad++;
        } else {
            itemsVenta.push({ tipo, id, nombre, precio, cantidad: 1 });
        }
        renderTable();
    }

    function removeItem(index) {
        itemsVenta.splice(index, 1);
        renderTable();
    }

    function renderTable() {
        const tbody = document.querySelector('#tablaVenta tbody');
        tbody.innerHTML = '';
        
        itemsVenta.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.tipo.toUpperCase()}</td>
                <td>${item.nombre}</td>
                <td><input type="number" min="1" value="${item.cantidad}" style="width:50px" onchange="updateCantidad(${index}, this.value)"></td>
                <td>$${item.precio.toFixed(2)}</td>
                <td>$${(item.precio * item.cantidad).toFixed(2)}</td>
                <td><button type="button" class="btn-remove" onclick="removeItem(${index})">X</button></td>
            `;
            tbody.appendChild(tr);
        });
        
        calcularTotales();
    }

    function updateCantidad(index, val) {
        itemsVenta[index].cantidad = parseInt(val);
        if(itemsVenta[index].cantidad < 1) itemsVenta[index].cantidad = 1;
        renderTable();
    }

    function calcularTotales() {
        let subtotal = 0;
        itemsVenta.forEach(i => subtotal += (i.precio * i.cantidad));
        
        const impuesto = subtotal * 0.15;
        const descuento = parseFloat(document.getElementById('descuento').value) || 0;
        const total = subtotal + impuesto - descuento;
        
        document.getElementById('lbl_subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('lbl_impuesto').innerText = impuesto.toFixed(2);
        document.getElementById('lbl_total').innerText = total.toFixed(2);
    }

    // Preparar datos para enviar al submit
    document.getElementById('ventaForm').addEventListener('submit', function(e) {
        if (itemsVenta.length === 0) {
            e.preventDefault();
            alert("La venta está vacía");
            return;
        }
        
        // Crear inputs ocultos para enviar el array de items
        itemsVenta.forEach((item, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `items[${index}][tipo]`;
            input.value = item.tipo;
            this.appendChild(input);
            
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = `items[${index}][id]`;
            input2.value = item.id;
            this.appendChild(input2);
            
            const input3 = document.createElement('input');
            input3.type = 'hidden';
            input3.name = `items[${index}][cantidad]`;
            input3.value = item.cantidad;
            this.appendChild(input3);
            
            const input4 = document.createElement('input');
            input4.type = 'hidden';
            input4.name = `items[${index}][precio]`;
            input4.value = item.precio;
            this.appendChild(input4);
        });
    });
</script>

</body>
</html>
