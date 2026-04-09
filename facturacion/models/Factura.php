<?php
/**
 * Modelo Factura
 */

class Factura {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todas las facturas
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT f.*, c.nombre as cliente_nombre, c.ruc_nit as cliente_ruc
             FROM facturas f
             INNER JOIN clientes c ON f.cliente_id = c.id
             ORDER BY f.fecha_emision DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Obtener factura por ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre as cliente_nombre, c.email as cliente_email, 
                    c.telefono as cliente_telefono, c.direccion as cliente_direccion, c.ruc_nit as cliente_ruc
             FROM facturas f
             INNER JOIN clientes c ON f.cliente_id = c.id
             WHERE f.id = ?"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener factura por número
     */
    public function getByNumero(string $numero): ?array {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre as cliente_nombre, c.email as cliente_email, 
                    c.telefono as cliente_telefono, c.direccion as cliente_direccion, c.ruc_nit as cliente_ruc
             FROM facturas f
             INNER JOIN clientes c ON f.cliente_id = c.id
             WHERE f.numero_factura = ?"
        );
        $stmt->execute([$numero]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener detalles de una factura
     */
    public function getDetalles(int $facturaId): array {
        $stmt = $this->db->prepare(
            "SELECT fd.*, p.nombre as producto_nombre, p.codigo as producto_codigo
             FROM factura_detalles fd
             INNER JOIN productos p ON fd.producto_id = p.id
             WHERE fd.factura_id = ?"
        );
        $stmt->execute([$facturaId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener factura completa con detalles
     */
    public function getFacturaCompleta(int $id): ?array {
        $factura = $this->getById($id);
        if ($factura) {
            $factura['detalles'] = $this->getDetalles($id);
        }
        return $factura;
    }

    /**
     * Buscar facturas por cliente o número
     */
    public function search(string $term): array {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre as cliente_nombre, c.ruc_nit as cliente_ruc
             FROM facturas f
             INNER JOIN clientes c ON f.cliente_id = c.id
             WHERE f.numero_factura LIKE ? OR c.nombre LIKE ? OR c.ruc_nit LIKE ?
             ORDER BY f.fecha_emision DESC"
        );
        $searchTerm = "%{$term}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener facturas por estado
     */
    public function getByEstado(string $estado): array {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre as cliente_nombre
             FROM facturas f
             INNER JOIN clientes c ON f.cliente_id = c.id
             WHERE f.estado = ?
             ORDER BY f.fecha_emision DESC"
        );
        $stmt->execute([$estado]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nueva factura
     */
    public function crearFactura(int $clienteId, array $detalles, float $impuestoPorcentaje = 0.21, ?string $notas = null): int {
        $this->db->beginTransaction();
        
        try {
            // Generar número de factura
            $numeroFactura = $this->generarNumeroFactura();
            
            // Calcular totales
            $subtotal = 0;
            foreach ($detalles as $detalle) {
                $subtotal += $detalle['subtotal'];
            }
            
            $impuesto = $subtotal * $impuestoPorcentaje;
            $total = $subtotal + $impuesto;
            
            // Insertar factura
            $stmt = $this->db->prepare(
                "INSERT INTO facturas (numero_factura, cliente_id, fecha_emision, subtotal, impuesto, total, estado, notas)
                 VALUES (?, ?, NOW(), ?, ?, ?, 'pendiente', ?)"
            );
            $stmt->execute([
                $numeroFactura,
                $clienteId,
                $subtotal,
                $impuesto,
                $total,
                $notas
            ]);
            
            $facturaId = (int)$this->db->lastInsertId();
            
            // Insertar detalles y actualizar stock
            $stmtDetalle = $this->db->prepare(
                "INSERT INTO factura_detalles (factura_id, producto_id, cantidad, precio_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );
            
            $stmtStock = $this->db->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
            
            foreach ($detalles as $detalle) {
                $stmtDetalle->execute([
                    $facturaId,
                    $detalle['producto_id'],
                    $detalle['cantidad'],
                    $detalle['precio_unitario'],
                    $detalle['subtotal']
                ]);
                
                // Actualizar stock
                $stmtStock->execute([$detalle['cantidad'], $detalle['producto_id']]);
            }
            
            $this->db->commit();
            return $facturaId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar estado de factura
     */
    public function actualizarEstado(int $id, string $estado): bool {
        $estadosValidos = ['pendiente', 'pagada', 'cancelada', 'anulada'];
        if (!in_array($estado, $estadosValidos)) {
            throw new InvalidArgumentException("Estado no válido");
        }
        
        $stmt = $this->db->prepare("UPDATE facturas SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    /**
     * Anular factura
     */
    public function anularFactura(int $id): bool {
        $this->db->beginTransaction();
        
        try {
            // Actualizar estado de factura
            $this->actualizarEstado($id, 'anulada');
            
            // Revertir stock
            $detalles = $this->getDetalles($id);
            $stmtStock = $this->db->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
            
            foreach ($detalles as $detalle) {
                $stmtStock->execute([$detalle['cantidad'], $detalle['producto_id']]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Generar número de factura único
     */
    private function generarNumeroFactura(): string {
        $year = date('Y');
        $month = date('m');
        
        $stmt = $this->db->prepare(
            "SELECT numero_factura FROM facturas 
             WHERE numero_factura LIKE ?
             ORDER BY numero_factura DESC LIMIT 1"
        );
        $prefix = "FAC-{$year}{$month}-";
        $stmt->execute([$prefix . '%']);
        $result = $stmt->fetch();
        
        if ($result) {
            $lastNumber = (int)substr($result['numero_factura'], -6);
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Obtener estadísticas de facturación
     */
    public function getEstadisticas(?string $fechaInicio = null, ?string $fechaFin = null): array {
        $where = "";
        $params = [];
        
        if ($fechaInicio && $fechaFin) {
            $where = "WHERE fecha_emision BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        }
        
        $stmt = $this->db->prepare(
            "SELECT 
                COUNT(*) as total_facturas,
                SUM(total) as total_facturado,
                AVG(total) as promedio_factura,
                SUM(CASE WHEN estado = 'pagada' THEN total ELSE 0 END) as total_pagado,
                SUM(CASE WHEN estado = 'pendiente' THEN total ELSE 0 END) as total_pendiente
             FROM facturas $where"
        );
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
