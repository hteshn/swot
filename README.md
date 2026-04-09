# POS de Facturación para Salón de Belleza

Sistema completo desarrollado en **PHP 8** y **MySQL/MariaDB**, diseñado específicamente para la gestión de salones de belleza, peluquerías y spas.

## 📋 Características Incluidas ("Todo Completo")

Este sistema incluye las siguientes funcionalidades solicitadas:

- ✅ **Servicios**: Catálogo de servicios (corte, tinte, uñas, etc.) con precios y comisiones.
- ✅ **Productos / Inventario**: Control de stock, entradas, salidas y alertas de stock mínimo.
- ✅ **Clientes**: Historial, datos de contacto y sistema de puntos/beneficios.
- ✅ **Citas/Agenda**: Programación de turnos por estilista y servicio.
- ✅ **Empleados y comisiones**: Registro de estilistas y cálculo automático de comisiones por servicio.
- ✅ **Caja**: Apertura y cierre de caja, control de sesiones.
- ✅ **Facturación**: Generación de facturas/tickets con serie, número correlativo, impuestos (ITBIS/IVA).
- ✅ **Pagos**: Soporte para efectivo, tarjeta, transferencias y pagos mixtos.
- ✅ **Reportes**: Ventas por día, reportes filtrados por fecha.
- ✅ **Usuarios y roles**: Admin (acceso total), Cajero (ventas), Estilista (agenda).

## 🚀 Instalación

### 1. Requisitos
- Servidor Web (Apache/Nginx)
- PHP 8.0 o superior
- MySQL 5.2+ o MariaDB
- phpMyAdmin (opcional, para gestionar la BD)

### 2. Base de Datos
1. Abra **phpMyAdmin** o su cliente MySQL favorito.
2. Cree una base de datos llamada `salon_pos`.
3. Importe el archivo `database_schema.sql` incluido en la raíz.
   - Este script crea todas las tablas necesarias e inserta datos de ejemplo.
   - **Usuario por defecto**: `admin` | **Contraseña**: `admin123`

### 3. Configuración
1. Edite el archivo `config/db.php`.
2. Actualice las constantes con sus credenciales de base de datos:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'salon_pos');
   define('DB_USER', 'su_usuario');
   define('DB_PASS', 'su_contraseña');
   ```

### 4. Ejecución
- Copie todos los archivos a su carpeta pública (ej. `htdocs` en XAMPP o `/var/www/html`).
- Acceda desde el navegador: `http://localhost/su_carpeta/`.

## 📂 Estructura de Archivos

```
/workspace
├── config/
│   └── db.php              # Conexión a BD
├── includes/
│   └── auth.php            # Funciones de seguridad y login
├── ventas/
│   ├── nueva_venta.php     # Punto de venta (POS)
│   └── procesar_venta.php  # Lógica de guardado
├── clientes/
│   └── listado.php         # Gestión de clientes
├── productos/
│   └── inventario.php      # Control de stock
├── agenda/
│   └── citas.php           # Calendario de citas
├── reportes/
│   └── ventas.php          # Reportes administrativos
├── dashboard.php           # Panel principal
├── index.php               # Login
├── logout.php              # Cerrar sesión
└── database_schema.sql     # Script de instalación BD
```

## 🔒 Seguridad
- Uso de **PDO** con sentencias preparadas para evitar inyección SQL.
- Contraseñas encriptadas con `password_hash()` de PHP 8.
- Protección de sesiones contra fijación (`session_regenerate_id`).
- Control de acceso basado en roles (Middleware simple en `auth.php`).

## 📝 Notas
- El sistema está diseñado para ser **monousuario concurrente** (varios usuarios pueden entrar, pero la caja se maneja por sesión).
- Para producción, se recomienda implementar HTTPS y mover las credenciales a variables de entorno.

---
*Desarrollado con código comentado detalladamente para facilitar su mantenimiento y ampliación.*
