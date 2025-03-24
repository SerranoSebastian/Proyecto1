<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Panel</title>
    <link rel="stylesheet" href="../css/estilo_admin.css">
</head>
<body>

<header class="admin-header">
    <div class="logo">Tienda<span>Xochiahua</span></div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="productos.php">Productos</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="../logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="admin-container">
    <h1>Panel de Administración</h1>
    <div class="dashboard-grid">
        <div class="card">
            <h3>Productos</h3>
            <p>Gestionar inventario de productos</p>
            <a href="productos.php" class="btn">Ver productos</a>
        </div>
        <div class="card">
            <h3>Pedidos</h3>
            <p>Revisar historial de pedidos</p>
            <a href="pedidos.php" class="btn">Ver pedidos</a>
        </div>
    </div>
</main>
<footer id="contacto">
            <h3>Contacto empresarial</h3>
            <p>oficinacentral@contacto.com.mx<p>
            <p>&copy; 2025 - Not Copyright Intended</p>
</footer>
</body>
</html>
