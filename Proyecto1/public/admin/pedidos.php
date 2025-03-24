<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

require_once '../../api/config/bd.php';

$bd = new BaseDeDatos();
$conexion = $bd->obtenerConexion();

$sql = "SELECT p.*, u.nombre AS nombre_usuario 
        FROM pedidos p 
        INNER JOIN usuarios u ON p.id_usuario = u.id
        ORDER BY p.fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Pedidos</title>
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
    <h1>Listado de Pedidos</h1>

    <?php if (count($pedidos) === 0): ?>
        <p style="text-align:center; color:#4a5568;">No hay pedidos registrados.</p>
    <?php else: ?>
        <?php foreach ($pedidos as $pedido): ?>
            <div class="card" style="text-align:left;">
                <p><strong>ID Pedido:</strong> <?= $pedido['id'] ?></p>
                <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre_usuario']) ?></p>
                <p><strong>Fecha:</strong> <?= $pedido['fecha'] ?></p>
                <p><strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?></p>

                <h4 style="margin-top: 15px;">Productos comprados:</h4>
                <ul>
                    <?php
                    $stmt_detalle = $conexion->prepare("
                        SELECT d.*, p.nombre 
                        FROM detalles_pedido d
                        INNER JOIN productos p ON d.id_producto = p.id
                        WHERE d.id_pedido = ?
                    ");
                    $stmt_detalle->execute([$pedido['id']]);
                    $detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($detalles as $detalle):
                    ?>
                        <li>
                            <?= htmlspecialchars($detalle['nombre']) ?> –
                            Cantidad: <?= $detalle['cantidad'] ?> –
                            Precio: $<?= number_format($detalle['precio_unitario'], 2) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>
