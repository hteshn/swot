# Sistema de Facturación PHP 8 + MySQL

Sistema completo de facturación desarrollado en PHP 8 y MySQL con una arquitectura MVC simple.

## 📋 Características

- **Gestión de Clientes**: CRUD completo de clientes
- **Gestión de Productos**: Control de inventario con alertas de stock bajo
- **Gestión de Usuarios**: 
  - Administración de usuarios del sistema
  - Datos personales: celular, edad, país, departamento
  - Roles: administrador, vendedor, contador
  - Validación de credenciales
- **Facturación**: 
  - Creación de facturas con múltiples productos
  - Cálculo automático de impuestos
  - Estados de factura (pendiente, pagada, cancelada, anulada)
  - Generación de números de factura consecutivos
  - Impresión de facturas
- **Dashboard**: Estadísticas y resumen del negocio
- **Diseño Responsivo**: Interfaz moderna y adaptable a móviles

## 🚀 Requisitos

- PHP 8.0 o superior
- MySQL 5.7 o superior (o MariaDB)
- Servidor web (Apache, Nginx, etc.)
- Extensiones PHP: PDO, pdo_mysql

## 📦 Instalación

### 1. Clonar o copiar el proyecto

```bash
cd /workspace/facturacion
```

### 2. Configurar la base de datos

Editar el archivo `config/database.php` con tus credenciales:

```php
return [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'facturacion_db',
    'username' => 'root',
    'password' => 'tu_password',
    // ...
];
```

### 3. Crear la base de datos

Ejecutar el script SQL en MySQL:

```bash
mysql -u root -p < database/schema.sql
```

O importar el archivo `database/schema.sql` desde phpMyAdmin u otra herramienta.

**Nota**: Si ya tienes una base de datos existente y solo quieres agregar los nuevos campos de usuarios, ejecuta:

```bash
mysql -u root -p facturacion_db < database/migration_usuarios.sql
```

### 4. Configurar el servidor web

#### Apache

Crear un archivo `.htaccess` en la raíz:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?$1 [L,QSA]
```

O configurar el VirtualHost:

```apache
<VirtualHost *:80>
    DocumentRoot /workspace/facturacion/public
    <Directory /workspace/facturacion/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name localhost;
    root /workspace/facturacion/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. Acceder al sistema

Abrir el navegador y navegar a: `http://localhost`

## 📁 Estructura del Proyecto

```
facturacion/
├── config/
│   ├── database.php          # Configuración de BD
│   └── Database.php          # Clase de conexión PDO
├── controllers/
│   └── FacturacionController.php  # Controlador principal
├── models/
│   ├── Cliente.php           # Modelo de clientes
│   ├── Producto.php          # Modelo de productos
│   └── Factura.php           # Modelo de facturas
├── views/
│   ├── layout.php            # Plantilla principal
│   ├── dashboard.php         # Vista del dashboard
│   ├── clientes/
│   │   ├── list.php
│   │   ├── create.php
│   │   └── edit.php
│   ├── productos/
│   │   ├── list.php
│   │   ├── create.php
│   │   └── edit.php
│   └── facturas/
│       ├── list.php
│       ├── view.php
│       └── create.php
├── public/
│   ├── css/
│   │   └── styles.css        # Estilos CSS
│   └── js/
│       └── app.js            # JavaScript
├── database/
│   └── schema.sql            # Script de creación de BD
└── index.php                 # Punto de entrada
```

## 🔐 Usuarios por Defecto

El script de instalación incluye estos usuarios de prueba:

| Usuario | Password | Rol |
|---------|----------|-----|
| admin | password | Administrador |
| vendedor1 | password | Vendedor |

**⚠️ Importante**: Cambiar las contraseñas en producción.

## 💡 Uso Básico

### 1. Dashboard
- Visualiza estadísticas generales
- Lista de facturas recientes
- Alertas de stock bajo

### 2. Clientes
- Registrar nuevos clientes
- Editar información
- Eliminar clientes (soft delete)

### 3. Productos
- Gestionar catálogo de productos
- Controlar stock
- Clasificar por categorías

### 4. Facturas
- Crear nuevas facturas
- Agregar múltiples productos
- Calcular impuestos automáticamente
- Imprimir facturas
- Marcar como pagadas
- Anular facturas (revierte stock)

## 🔧 Personalización

### Cambiar porcentaje de impuesto por defecto

Editar en `views/facturas/create.php`:
```html
<input type="number" value="21" ... >  <!-- Cambiar 21 por tu porcentaje -->
```

### Modificar formato de moneda

Editar en `public/js/app.js`:
```javascript
currency: 'ARS'  // Cambiar por tu moneda local
```

## 🛡️ Seguridad

- Consultas preparadas (PDO) para prevenir SQL Injection
- Validación de datos de entrada
- Escape de salida con `htmlspecialchars()`
- Soft delete para mantener integridad referencial

## 📝 Notas

- Este sistema usa soft delete (no elimina registros físicamente)
- Las facturas anuladas revierten automáticamente el stock
- Los números de factura son consecutivos por mes (FAC-YYYYMM-NNNNNN)

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📄 Licencia

Este proyecto es de código abierto.

## 🆘 Soporte

Para problemas o preguntas, revisar la documentación o contactar al administrador del sistema.

---

**Desarrollado con PHP 8 + MySQL** 🚀
