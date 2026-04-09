-- ============================================================================
-- SCRIPT DE CREACIÓN DE BASE DE DATOS - POS SALÓN DE BELLEZA
-- Compatible con MySQL 5.2+ / MariaDB
-- ============================================================================

-- 1. Creación de la Base de Datos
CREATE DATABASE IF NOT EXISTS salon_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE salon_pos;

-- ============================================================================
-- TABLA: USUARIOS Y ROLES
-- Descripción: Almacena los usuarios del sistema y sus permisos.
-- ============================================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL COMMENT 'Nombre visible del usuario',
    username VARCHAR(50) NOT NULL UNIQUE COMMENT 'Usuario para login',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Contraseña encriptada con password_hash() de PHP 8',
    rol ENUM('admin', 'cajero', 'estilista') NOT NULL DEFAULT 'cajero' COMMENT 'Rol: admin (todo), cajero (ventas/caja), estilista (agenda/sus clientes)',
    activo TINYINT(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabla maestra de usuarios del sistema';

-- ============================================================================
-- TABLA: CLIENTES
-- Descripción: Información de los clientes y sistema de fidelidad.
-- ============================================================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    rtn_nit VARCHAR(20) COMMENT 'Registro Tributario Nacional o NIT para facturación formal',
    direccion TEXT,
    puntos_acumulados INT DEFAULT 0 COMMENT 'Puntos para canje por servicios o productos',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Base de datos de clientes';

-- ============================================================================
-- TABLA: SERVICIOS
-- Descripción: Catálogo de servicios ofrecidos (Corte, Tinte, etc.).
-- ============================================================================
CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    duracion_minutos INT DEFAULT 60 COMMENT 'Tiempo estimado para agendar',
    comision_porcentaje DECIMAL(5, 2) DEFAULT 0.00 COMMENT 'Porcentaje que gana el estilista (ej. 10.00 = 10%)',
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Catálogo de servicios del salón';

-- ============================================================================
-- TABLA: PRODUCTOS / INVENTARIO
-- Descripción: Control de mercancía física (shampoos, tintes, accesorios).
-- ============================================================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_barras VARCHAR(50),
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    costo_compra DECIMAL(10, 2) NOT NULL COMMENT 'Precio de costo para cálculo de utilidad',
    precio_venta DECIMAL(10, 2) NOT NULL,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 5 COMMENT 'Nivel para activar alerta de reorden',
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Inventario de productos';

-- ============================================================================
-- TABLA: EMPLEADOS (ESTILISTAS)
-- Descripción: Perfiles específicos para personal técnico (puede vincularse a usuario).
-- ============================================================================
CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT, -- Vínculo opcional si el empleado usa el sistema
    nombre VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100),
    telefono VARCHAR(20),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Perfil de empleados y estilistas';

-- ============================================================================
-- TABLA: CITAS / AGENDA
-- Descripción: Reserva de turnos para servicios.
-- ============================================================================
CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    empleado_id INT NOT NULL,
    servicio_id INT NOT NULL,
    fecha_cita DATETIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'finalizada', 'cancelada') DEFAULT 'pendiente',
    notas TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (empleado_id) REFERENCES empleados(id),
    FOREIGN KEY (servicio_id) REFERENCES servicios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Agenda de citas del salón';

-- ============================================================================
-- TABLA: CAJA (APERTURA/CIERRE)
-- Descripción: Control de sesiones de caja por usuario.
-- ============================================================================
CREATE TABLE caja_sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    monto_inicial DECIMAL(10, 2) NOT NULL COMMENT 'Fondo fijo al abrir',
    fecha_apertura DATETIME NOT NULL,
    fecha_cierre DATETIME NULL,
    monto_final_esperado DECIMAL(10, 2) NULL,
    monto_final_real DECIMAL(10, 2) NULL,
    diferencia DECIMAL(10, 2) NULL COMMENT 'Diferencia entre lo esperado y lo real (faltante/sobrante)',
    estado ENUM('abierta', 'cerrada') DEFAULT 'abierta',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Control de apertura y cierre de caja diaria';

-- ============================================================================
-- TABLA: VENTAS / FACTURACIÓN CABECERA
-- Descripción: Encabezado de la factura o ticket generado.
-- ============================================================================
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serie VARCHAR(10) DEFAULT '001' COMMENT 'Serie de facturación',
    numero_factura INT NOT NULL COMMENT 'Correlativo único por serie',
    cliente_id INT,
    usuario_id INT NOT NULL COMMENT 'Vendedor/Cajero',
    empleado_id INT COMMENT 'Estilista principal (si aplica comisión global)',
    subtotal DECIMAL(10, 2) NOT NULL,
    impuesto DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Valor del impuesto (ITBIS/IVA)',
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    tipo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'mixto') DEFAULT 'efectivo',
    estado_pago ENUM('pagado', 'pendiente', 'anulado') DEFAULT 'pagado',
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (empleado_id) REFERENCES empleados(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cabecera de facturas y ventas';

-- ============================================================================
-- TABLA: DETALLE DE VENTA
-- Descripción: Ítems específicos (servicios o productos) dentro de una venta.
-- ============================================================================
CREATE TABLE detalle_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    tipo_item ENUM('servicio', 'producto') NOT NULL,
    item_id INT NOT NULL COMMENT 'ID del servicio o producto',
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL COMMENT 'Precio al momento de la venta (histórico)',
    subtotal DECIMAL(10, 2) NOT NULL,
    comision_empleado DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Monto de comisión generado en esta línea',
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Detalle de ítems por factura';

-- ============================================================================
-- TABLA: MOVIMIENTOS DE INVENTARIO
-- Descripción: Auditoría de entradas y salidas de productos.
-- ============================================================================
CREATE TABLE inventario_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida_venta', 'ajuste', 'merma') NOT NULL,
    cantidad INT NOT NULL,
    motivo TEXT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bitácora de movimientos de inventario';

-- ============================================================================
-- DATOS DE EJEMPLO (SEEDERS)
-- ============================================================================

-- Usuario Admin por defecto (Pass: admin123)
-- Nota: En producción, generar el hash con password_hash('admin123', PASSWORD_DEFAULT) desde PHP.
INSERT INTO usuarios (nombre_completo, username, password_hash, rol) VALUES 
('Administrador General', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Servicios básicos
INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos, comision_porcentaje) VALUES
('Corte de Dama', 'Corte de cabello femenino incluye lavado', 150.00, 45, 10.00),
('Corte de Caballero', 'Corte de cabello masculino', 100.00, 30, 10.00),
('Tinte Completo', 'Aplicación de tinte y matiz', 450.00, 120, 15.00),
('Manicure Gel', 'Uñas en gel con diseño básico', 200.00, 60, 12.00);

-- Productos básicos
INSERT INTO productos (codigo_barras, nombre, costo_compra, precio_venta, stock_actual, stock_minimo) VALUES
('77001', 'Shampoo Keratina 1L', 120.00, 250.00, 20, 5),
('77002', 'Acondicionador Hidratante', 80.00, 180.00, 15, 5),
('77003', 'Aceite de Argán', 150.00, 300.00, 8, 3);

-- Empleado Ejemplo
INSERT INTO empleados (nombre, especialidad) VALUES ('Ana Pérez', 'Colorista y Corte');
