<?php
/**
 * Modelo Cliente
 */

class Cliente {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todos los clientes
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM clientes WHERE estado = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener cliente por ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ? AND estado = 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Buscar clientes por nombre o email
     */
    public function search(string $term): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM clientes 
             WHERE estado = 1 AND (nombre LIKE ? OR email LIKE ? OR ruc_nit LIKE ?)
             ORDER BY nombre ASC"
        );
        $searchTerm = "%{$term}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nuevo cliente
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO clientes (nombre, email, telefono, direccion, ruc_nit, estado)
             VALUES (?, ?, ?, ?, ?, 1)"
        );
        return $stmt->execute([
            $data['nombre'],
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ruc_nit'] ?? null
        ]);
    }

    /**
     * Actualizar cliente
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE clientes 
             SET nombre = ?, email = ?, telefono = ?, direccion = ?, ruc_nit = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['nombre'],
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ruc_nit'] ?? null,
            $id
        ]);
    }

    /**
     * Eliminar cliente (soft delete)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE clientes SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
