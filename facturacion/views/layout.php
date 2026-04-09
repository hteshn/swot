<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistema de Facturación' ?></title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <h1>📋 Sistema de Facturación</h1>
        </div>
        <ul class="navbar-menu">
            <li><a href="?controller=facturacion&action=index">Dashboard</a></li>
            <li><a href="?controller=facturacion&action=clientes">Clientes</a></li>
            <li><a href="?controller=facturacion&action=productos">Productos</a></li>
            <li><a href="?controller=facturacion&action=facturas">Facturas</a></li>
            <li><a href="?controller=facturacion&action=facturas&action=create">Nueva Factura</a></li>
            <li><a href="?controller=usuario&action=index">Usuarios</a></li>
        </ul>
    </nav>

    <main class="container">
        <?= $content ?? '' ?>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Sistema de Facturación - PHP 8 + MySQL</p>
    </footer>

    <script src="public/js/app.js"></script>
</body>
</html>
