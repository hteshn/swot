<?php
/**
 * Modelo Producto
 */

class Producto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todos los productos
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM productos WHERE estado = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener producto por ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM productos WHERE id = ? AND estado = 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener producto por código
     */
    public function getByCodigo(string $codigo): ?array {
        $stmt = $this->db->prepare("SELECT * FROM productos WHERE codigo = ? AND estado = 1");
        $stmt->execute([$codigo]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Buscar productos por nombre, código o categoría
     */
    public function search(string $term): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM productos 
             WHERE estado = 1 AND (nombre LIKE ? OR codigo LIKE ? OR categoria LIKE ?)
             ORDER BY nombre ASC"
        );
        $searchTerm = "%{$term}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStock(int $threshold = 10): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM productos 
             WHERE estado = 1 AND stock <= ?
             ORDER BY stock ASC"
        );
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nuevo producto
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO productos (codigo, nombre, descripcion, precio_unitario, stock, categoria, estado)
             VALUES (?, ?, ?, ?, ?, ?, 1)"
        );
        return $stmt->execute([
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['precio_unitario'],
            $data['stock'] ?? 0,
            $data['categoria'] ?? null
        ]);
    }

    /**
     * Actualizar producto
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE productos 
             SET codigo = ?, nombre = ?, descripcion = ?, precio_unitario = ?, stock = ?, categoria = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['precio_unitario'],
            $data['stock'] ?? 0,
            $data['categoria'] ?? null,
            $id
        ]);
    }

    /**
     * Eliminar producto (soft delete)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE productos SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Actualizar stock
     */
    public function updateStock(int $id, int $cantidad): bool {
        $stmt = $this->db->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$cantidad, $id]);
    }
}
