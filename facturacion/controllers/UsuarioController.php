<?php
/**
 * Controlador para la gestión de usuarios
 */

class UsuarioController {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Listar todos los usuarios
     */
    public function index(): void {
        $usuarios = $this->usuarioModel->getAll();
        include __DIR__ . '/../views/usuarios/list.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'] ?? '',
                'password' => $_POST['password'] ?? '',
                'email' => $_POST['email'] ?? '',
                'nombre_completo' => $_POST['nombre_completo'] ?? '',
                'celular' => $_POST['celular'] ?? '',
                'edad' => !empty($_POST['edad']) ? (int)$_POST['edad'] : null,
                'pais' => $_POST['pais'] ?? '',
                'departamento' => $_POST['departamento'] ?? '',
                'rol' => $_POST['rol'] ?? 'vendedor'
            ];

            // Validaciones
            $errors = [];

            if (empty($data['username'])) {
                $errors[] = 'El nombre de usuario es obligatorio';
            } elseif ($this->usuarioModel->usernameExists($data['username'])) {
                $errors[] = 'El nombre de usuario ya existe';
            }

            if (empty($data['email'])) {
                $errors[] = 'El email es obligatorio';
            } elseif ($this->usuarioModel->emailExists($data['email'])) {
                $errors[] = 'El email ya existe';
            }

            if (empty($data['password'])) {
                $errors[] = 'La contraseña es obligatoria';
            } elseif (strlen($data['password']) < 6) {
                $errors[] = 'La contraseña debe tener al menos 6 caracteres';
            }

            if (empty($data['nombre_completo'])) {
                $errors[] = 'El nombre completo es obligatorio';
            }

            if (!empty($data['edad']) && ($data['edad'] < 18 || $data['edad'] > 100)) {
                $errors[] = 'La edad debe estar entre 18 y 100 años';
            }

            if (empty($errors)) {
                if ($this->usuarioModel->create($data)) {
                    header('Location: ?controller=usuario&action=index&success=created');
                    exit;
                } else {
                    $errors[] = 'Error al crear el usuario. Intente nuevamente.';
                }
            }

            $error_message = implode('<br>', $errors);
            include __DIR__ . '/../views/usuarios/create.php';
        } else {
            include __DIR__ . '/../views/usuarios/create.php';
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            header('Location: ?controller=usuario&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => $_POST['email'] ?? '',
                'nombre_completo' => $_POST['nombre_completo'] ?? '',
                'celular' => $_POST['celular'] ?? '',
                'edad' => !empty($_POST['edad']) ? (int)$_POST['edad'] : null,
                'pais' => $_POST['pais'] ?? '',
                'departamento' => $_POST['departamento'] ?? '',
                'rol' => $_POST['rol'] ?? 'vendedor',
                'password' => $_POST['password'] ?? ''
            ];

            // Validaciones
            $errors = [];

            if (empty($data['email'])) {
                $errors[] = 'El email es obligatorio';
            } elseif ($this->usuarioModel->emailExists($data['email'], $id)) {
                $errors[] = 'El email ya existe';
            }

            if (empty($data['nombre_completo'])) {
                $errors[] = 'El nombre completo es obligatorio';
            }

            if (!empty($data['edad']) && ($data['edad'] < 18 || $data['edad'] > 100)) {
                $errors[] = 'La edad debe estar entre 18 y 100 años';
            }

            if (!empty($data['password']) && strlen($data['password']) < 6) {
                $errors[] = 'La contraseña debe tener al menos 6 caracteres';
            }

            if (empty($errors)) {
                if ($this->usuarioModel->update($id, $data)) {
                    header('Location: ?controller=usuario&action=index&success=updated');
                    exit;
                } else {
                    $errors[] = 'Error al actualizar el usuario. Intente nuevamente.';
                }
            }

            $error_message = implode('<br>', $errors);
            include __DIR__ . '/../views/usuarios/edit.php';
        } else {
            include __DIR__ . '/../views/usuarios/edit.php';
        }
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        
        // No permitir eliminar el usuario actual
        session_start();
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            header('Location: ?controller=usuario&action=index&error=cannot_delete_self');
            exit;
        }

        if ($this->usuarioModel->delete($id)) {
            header('Location: ?controller=usuario&action=index&success=deleted');
        } else {
            header('Location: ?controller=usuario&action=index&error=delete_failed');
        }
        exit;
    }

    /**
     * Ver detalles de un usuario
     */
    public function view(): void {
        $id = (int)($_GET['id'] ?? 0);
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            header('Location: ?controller=usuario&action=index');
            exit;
        }

        include __DIR__ . '/../views/usuarios/view.php';
    }
}
