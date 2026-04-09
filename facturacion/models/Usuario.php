<?php
/**
 * Modelo Usuario
 */

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM usuarios WHERE estado = 1 ORDER BY nombre_completo ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuario por ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ? AND estado = 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Buscar usuarios por nombre, email o username
     */
    public function search(string $term): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios 
             WHERE estado = 1 AND (nombre_completo LIKE ? OR email LIKE ? OR username LIKE ?)
             ORDER BY nombre_completo ASC"
        );
        $searchTerm = "%{$term}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nuevo usuario
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (username, password, email, nombre_completo, celular, edad, pais, departamento, rol, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"
        );
        
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $data['username'],
            $passwordHash,
            $data['email'],
            $data['nombre_completo'],
            $data['celular'] ?? null,
            $data['edad'] ?? null,
            $data['pais'] ?? null,
            $data['departamento'] ?? null,
            $data['rol'] ?? 'vendedor'
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update(int $id, array $data): bool {
        // Si se proporciona contraseña, actualizarla
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare(
                "UPDATE usuarios 
                 SET email = ?, nombre_completo = ?, celular = ?, edad = ?, pais = ?, departamento = ?, rol = ?, password = ?
                 WHERE id = ?"
            );
            
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            return $stmt->execute([
                $data['email'],
                $data['nombre_completo'],
                $data['celular'] ?? null,
                $data['edad'] ?? null,
                $data['pais'] ?? null,
                $data['departamento'] ?? null,
                $data['rol'] ?? 'vendedor',
                $passwordHash,
                $id
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE usuarios 
                 SET email = ?, nombre_completo = ?, celular = ?, edad = ?, pais = ?, departamento = ?, rol = ?
                 WHERE id = ?"
            );
            
            return $stmt->execute([
                $data['email'],
                $data['nombre_completo'],
                $data['celular'] ?? null,
                $data['edad'] ?? null,
                $data['pais'] ?? null,
                $data['departamento'] ?? null,
                $data['rol'] ?? 'vendedor',
                $id
            ]);
        }
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Validar credenciales de usuario
     */
    public function validateCredentials(string $username, string $password): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE username = ? AND estado = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }

    /**
     * Verificar si el username ya existe
     */
    public function usernameExists(string $username, int $excludeId = null): bool {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? AND id != ? AND estado = 1");
            $stmt->execute([$username, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? AND estado = 1");
            $stmt->execute([$username]);
        }
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si el email ya existe
     */
    public function emailExists(string $email, int $excludeId = null): bool {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ? AND estado = 1");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND estado = 1");
            $stmt->execute([$email]);
        }
        
        return $stmt->fetchColumn() > 0;
    }
}
