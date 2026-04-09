<?php
/**
 * Controlador principal para el sistema de facturación
 */

class FacturacionController {
    private Cliente $clienteModel;
    private Producto $productoModel;
    private Factura $facturaModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
        $this->productoModel = new Producto();
        $this->facturaModel = new Factura();
    }

    /**
     * Mostrar dashboard
     */
    public function index(): void {
        $estadisticas = $this->facturaModel->getEstadisticas();
        $facturasRecientes = array_slice($this->facturaModel->getAll(), 0, 10);
        $productosStockBajo = $this->productoModel->getLowStock(10);
        
        include __DIR__ . '/../views/dashboard.php';
    }

    /**
     * Gestión de clientes
     */
    public function clientes(string $action = 'list'): void {
        switch ($action) {
            case 'list':
                $clientes = $this->clienteModel->getAll();
                include __DIR__ . '/../views/clientes/list.php';
                break;
                
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = [
                        'nombre' => $_POST['nombre'] ?? '',
                        'email' => $_POST['email'] ?? '',
                        'telefono' => $_POST['telefono'] ?? '',
                        'direccion' => $_POST['direccion'] ?? '',
                        'ruc_nit' => $_POST['ruc_nit'] ?? ''
                    ];
                    
                    if ($this->clienteModel->create($data)) {
                        header('Location: ?controller=facturacion&action=clientes');
                        exit;
                    }
                }
                include __DIR__ . '/../views/clientes/create.php';
                break;
                
            case 'edit':
                $id = (int)($_GET['id'] ?? 0);
                $cliente = $this->clienteModel->getById($id);
                
                if (!$cliente) {
                    header('Location: ?controller=facturacion&action=clientes');
                    exit;
                }
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = [
                        'nombre' => $_POST['nombre'] ?? '',
                        'email' => $_POST['email'] ?? '',
                        'telefono' => $_POST['telefono'] ?? '',
                        'direccion' => $_POST['direccion'] ?? '',
                        'ruc_nit' => $_POST['ruc_nit'] ?? ''
                    ];
                    
                    if ($this->clienteModel->update($id, $data)) {
                        header('Location: ?controller=facturacion&action=clientes');
                        exit;
                    }
                }
                include __DIR__ . '/../views/clientes/edit.php';
                break;
                
            case 'delete':
                $id = (int)($_GET['id'] ?? 0);
                if ($this->clienteModel->delete($id)) {
                    header('Location: ?controller=facturacion&action=clientes');
                }
                exit;
        }
    }

    /**
     * Gestión de productos
     */
    public function productos(string $action = 'list'): void {
        switch ($action) {
            case 'list':
                $productos = $this->productoModel->getAll();
                include __DIR__ . '/../views/productos/list.php';
                break;
                
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = [
                        'codigo' => $_POST['codigo'] ?? '',
                        'nombre' => $_POST['nombre'] ?? '',
                        'descripcion' => $_POST['descripcion'] ?? '',
                        'precio_unitario' => (float)($_POST['precio_unitario'] ?? 0),
                        'stock' => (int)($_POST['stock'] ?? 0),
                        'categoria' => $_POST['categoria'] ?? ''
                    ];
                    
                    if ($this->productoModel->create($data)) {
                        header('Location: ?controller=facturacion&action=productos');
                        exit;
                    }
                }
                include __DIR__ . '/../views/productos/create.php';
                break;
                
            case 'edit':
                $id = (int)($_GET['id'] ?? 0);
                $producto = $this->productoModel->getById($id);
                
                if (!$producto) {
                    header('Location: ?controller=facturacion&action=productos');
                    exit;
                }
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $data = [
                        'codigo' => $_POST['codigo'] ?? '',
                        'nombre' => $_POST['nombre'] ?? '',
                        'descripcion' => $_POST['descripcion'] ?? '',
                        'precio_unitario' => (float)($_POST['precio_unitario'] ?? 0),
                        'stock' => (int)($_POST['stock'] ?? 0),
                        'categoria' => $_POST['categoria'] ?? ''
                    ];
                    
                    if ($this->productoModel->update($id, $data)) {
                        header('Location: ?controller=facturacion&action=productos');
                        exit;
                    }
                }
                include __DIR__ . '/../views/productos/edit.php';
                break;
                
            case 'delete':
                $id = (int)($_GET['id'] ?? 0);
                if ($this->productoModel->delete($id)) {
                    header('Location: ?controller=facturacion&action=productos');
                }
                exit;
        }
    }

    /**
     * Gestión de facturas
     */
    public function facturas(string $action = 'list'): void {
        switch ($action) {
            case 'list':
                $facturas = $this->facturaModel->getAll();
                include __DIR__ . '/../views/facturas/list.php';
                break;
                
            case 'view':
                $id = (int)($_GET['id'] ?? 0);
                $factura = $this->facturaModel->getFacturaCompleta($id);
                
                if (!$factura) {
                    header('Location: ?controller=facturacion&action=facturas');
                    exit;
                }
                
                include __DIR__ . '/../views/facturas/view.php';
                break;
                
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $clienteId = (int)($_POST['cliente_id'] ?? 0);
                    $notas = $_POST['notas'] ?? '';
                    $impuestoPorcentaje = (float)($_POST['impuesto_porcentaje'] ?? 0.21);
                    $productos = $_POST['productos'] ?? [];
                    
                    $detalles = [];
                    foreach ($productos as $prod) {
                        if ($prod['cantidad'] > 0) {
                            $producto = $this->productoModel->getById((int)$prod['producto_id']);
                            if ($producto) {
                                $detalles[] = [
                                    'producto_id' => (int)$prod['producto_id'],
                                    'cantidad' => (int)$prod['cantidad'],
                                    'precio_unitario' => (float)$prod['precio_unitario'],
                                    'subtotal' => (int)$prod['cantidad'] * (float)$prod['precio_unitario']
                                ];
                            }
                        }
                    }
                    
                    if (!empty($detalles) && $clienteId > 0) {
                        try {
                            $facturaId = $this->facturaModel->crearFactura(
                                $clienteId,
                                $detalles,
                                $impuestoPorcentaje,
                                $notas
                            );
                            header('Location: ?controller=facturacion&action=facturas&action=view&id=' . $facturaId);
                            exit;
                        } catch (Exception $e) {
                            $error = $e->getMessage();
                        }
                    }
                }
                
                $clientes = $this->clienteModel->getAll();
                $productos = $this->productoModel->getAll();
                include __DIR__ . '/../views/facturas/create.php';
                break;
                
            case 'anular':
                $id = (int)($_GET['id'] ?? 0);
                if ($this->facturaModel->anularFactura($id)) {
                    header('Location: ?controller=facturacion&action=facturas');
                }
                exit;
                
            case 'pagar':
                $id = (int)($_GET['id'] ?? 0);
                if ($this->facturaModel->actualizarEstado($id, 'pagada')) {
                    header('Location: ?controller=facturacion&action=facturas');
                }
                exit;
        }
    }

    /**
     * API para obtener productos (AJAX)
     */
    public function apiProducto(): void {
        header('Content-Type: application/json');
        
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'search':
                $term = $_GET['term'] ?? '';
                $productos = $this->productoModel->search($term);
                echo json_encode($productos);
                break;
                
            case 'getById':
                $id = (int)($_GET['id'] ?? 0);
                $producto = $this->productoModel->getById($id);
                echo json_encode($producto ?: []);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
        }
        exit;
    }
}
