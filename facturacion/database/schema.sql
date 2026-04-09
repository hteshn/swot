-- Script de creación de la base de datos para el sistema de facturación

CREATE DATABASE IF NOT EXISTS facturacion_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE facturacion_db;

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    direccion TEXT,
    ruc_nit VARCHAR(20),
    estado TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    categoria VARCHAR(50),
    estado TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_nombre (nombre),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB;

-- Tabla de facturas
CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(50) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    fecha_emision DATETIME NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    impuesto DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'pagada', 'cancelada', 'anulada') DEFAULT 'pendiente',
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    INDEX idx_numero_factura (numero_factura),
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_fecha_emision (fecha_emision),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- Tabla de detalles de factura
CREATE TABLE IF NOT EXISTS factura_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    INDEX idx_factura_id (factura_id),
    INDEX idx_producto_id (producto_id)
) ENGINE=InnoDB;

-- Tabla de usuarios (para el sistema)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    celular VARCHAR(20),
    edad INT,
    pais VARCHAR(50),
    departamento VARCHAR(100),
    rol ENUM('admin', 'vendedor', 'contador') DEFAULT 'vendedor',
    estado TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Insertar datos de ejemplo

-- Usuarios
INSERT INTO usuarios (username, password, email, nombre_completo, rol) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@facturacion.com', 'Administrador del Sistema', 'admin'),
('vendedor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor1@facturacion.com', 'Juan Vendedor', 'vendedor');

-- Clientes
INSERT INTO clientes (nombre, email, telefono, direccion, ruc_nit) VALUES
('Empresa ABC S.A.', 'contacto@empresaabc.com', '+54-11-1234-5678', 'Av. Principal 123, Ciudad', '30-12345678-9'),
('Carlos Gómez', 'carlos.gomez@email.com', '+54-11-8765-4321', 'Calle Secundaria 456, Ciudad', '20-12345678-0'),
('Tienda El Sol', 'ventas@tiendaelsol.com', '+54-11-5555-5555', 'Plaza Central 789, Ciudad', '30-87654321-0');

-- Productos
INSERT INTO productos (codigo, nombre, descripcion, precio_unitario, stock, categoria) VALUES
('PROD-001', 'Laptop HP ProBook 450', 'Laptop empresarial 15.6 pulgadas, Intel i5, 8GB RAM, 256GB SSD', 850.00, 25, 'Electrónica'),
('PROD-002', 'Mouse Logitech MX Master 3', 'Mouse inalámbrico ergonómico de alto rendimiento', 99.99, 50, 'Accesorios'),
('PROD-003', 'Teclado Mecánico Corsair K95', 'Teclado mecánico RGB con switches Cherry MX', 189.99, 30, 'Accesorios'),
('PROD-004', 'Monitor Dell UltraSharp 27"', 'Monitor 4K USB-C Hub, ajuste de altura', 549.99, 15, 'Electrónica'),
('PROD-005', 'Silla Ergonómica Herman Miller', 'Silla de oficina ergonómica premium', 1250.00, 10, 'Mobiliario');
