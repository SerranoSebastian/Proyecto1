<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

require_once '../../api/config/bd.php';

$bd = new BaseDeDatos();
$conexion = $bd->obtenerConexion();

$sql = "SELECT * FROM productos";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Productos</title>
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
    <h1>Gestión de Productos</h1>
    <h2>Agregar nuevo producto</h2>
<form action="../../api/index.php?accion=agregar_producto" method="POST" enctype="multipart/form-data">
    <input type="text" name="nombre" placeholder="Nombre" required><br><br>
    <textarea name="descripcion" placeholder="Descripción" required></textarea><br><br>
    <input type="number" step="0.01" name="precio" placeholder="Precio" required><br><br>
    <input type="number" name="stock" placeholder="Stock" required><br><br>
    <label>Imagen:</label>
    <input type="file" name="imagen" accept="image/*" required><br><br>
    <button type="submit">Agregar Producto</button>
</form>
<hr><br>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <form action="../../api/index.php?accion=editar_producto" method="POST">
                        <td>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>">
                        </td>
                        <td>
                            <textarea name="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>">
                        </td>
                        <td>
                            <input type="number" name="stock" value="<?= $producto['stock'] ?>">
                        </td>
                        <td>
                            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                            <button type="submit">Guardar</button>
                        </td>
                        <td>
                            <a href="../../api/index.php?accion=eliminar_producto&id=<?= $producto['id'] ?>" class="btn-eliminar">Eliminar</a>
                        </td>
                    </form>
                    
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
