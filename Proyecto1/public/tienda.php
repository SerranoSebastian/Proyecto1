<?php
session_start(); 

require_once '../api/config/bd.php';

$bd = new BaseDeDatos();
$conexion = $bd->obtenerConexion();

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    unset($_SESSION['carrito'][$id]);
    header("Location: tienda.php");
    exit();
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if (isset($_GET['agregar'])) {
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente') {
        $id = $_GET['agregar'];
        $_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + 1;
    } else {
        header("Location: login.html");
        exit();
    }
    header("Location: tienda.php");
    exit();
}

// Obtener productos
$stmt = $conexion->prepare("SELECT * FROM productos WHERE stock > 0");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda Xochiahua</title>
    <link rel="stylesheet" href="css/estilo_tienda.css">
</head>
<body>
<header>
    <div class="logo">Tienda<span>Xochiahua</span></div>
    <nav>
    <a href="tienda.php" class="activo">Productos</a>
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente'): ?>
        <a href="cliente/pedidos.php">Mis pedidos</a>
        <span style="margin-left: 10px; font-weight: bold; color: #2d3748;">
            <?= htmlspecialchars($_SESSION['nombre']) ?>
        </span>
        <a href="logout.php">Cerrar sesión</a>
    <?php else: ?>
        <a href="login.html">Iniciar sesión</a>
    <?php endif; ?>
    </nav>
</header>


<main>
    <h1>Productos disponibles</h1>
    <div class="productos-grid">
        <?php foreach ($productos as $producto): ?>
            <div class="producto-card">
                <img src="img/<?= $producto['id'] ?>.jpg" alt="Imagen producto">
                <div class="contenido">
                    <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                    <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                    <div class="precio">$<?= number_format($producto['precio'], 2) ?></div>
                    <div class="acciones">
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente'): ?>
                            <a href="tienda.php?agregar=<?= $producto['id'] ?>" class="btn">Agregar al carrito</a>
                        <?php else: ?>
                            <a href="login.html" class="btn" style="background-color:#e53e3e;">Inicia sesión para comprar</a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <h2 style="text-align:center; margin-top:60px;">🛒 Tu Carrito</h2>

<div class="carrito-container">
<?php if (empty($_SESSION['carrito'])): ?>
    <p class="carrito-vacio">Tu carrito está vacío.</p>
<?php else: ?>
    <form action="../api/index.php?accion=confirmar_pedido" method="POST">
        <div class="carrito-lista">
            <?php
            $total = 0;
            foreach ($_SESSION['carrito'] as $id_producto => $cantidad):
                $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->execute([$id_producto]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                $subtotal = $producto['precio'] * $cantidad;
                $total += $subtotal;
            ?>
            <div class="carrito-item">
                <img src="img/<?= $producto['id'] ?>.jpg" alt="Producto">
                <div class="info">
                    <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                    <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                    <p><strong>Cantidad:</strong> <?= $cantidad ?></p>
                    <p><strong>Subtotal:</strong> $<?= number_format($subtotal, 2) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="carrito-total">
            <p><strong>Total:</strong> $<?= number_format($total, 2) ?></p>
            <button type="submit" class="btn">Confirmar Pedido</button>
        </div>
    </form>
<?php endif; ?>
</div>
</main>

<?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente'): ?>
    <button id="abrirCarrito" class="boton-carrito">
        🛒 Ver Carrito
    </button>
<?php endif; ?>

<div id="panelCarrito" class="carrito-panel">
    <div class="carrito-header">
        <h3>🛍️ Tu Carrito</h3>
        <button id="cerrarCarrito">✖</button>
    </div>
    <div class="carrito-contenido">
        <?php
        if (empty($_SESSION['carrito'])) {
            echo "<p>Tu carrito está vacío.</p>";
        } else {
            $total = 0;
            echo "<ul class='carrito-lista'>";
            foreach ($_SESSION['carrito'] as $id => $cantidad) {
                $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                $subtotal = $producto['precio'] * $cantidad;
                $total += $subtotal;
                echo "<li class='item-carrito'>
                        <img src='img/{$producto['id']}.jpg' alt='producto'>
                        <div class='info'>
                            <h4>{$producto['nombre']}</h4>
                            <p>Cantidad: {$cantidad}</p>
                            <p>Subtotal: $" . number_format($subtotal, 2) . "</p>
                            <a href='tienda.php?eliminar={$producto['id']}' class='btn-eliminar'>Eliminar</a>
                        </div>
                    </li>";
            }
            echo "</ul>";
            echo "<div class='carrito-total'>
                    <p><strong>Total:</strong> $" . number_format($total, 2) . "</p>
                    <form action='../api/index.php?accion=confirmar_pedido' method='POST'>
                        <button type='submit' class='btn'>Confirmar Pedido</button>
                    </form>
                  </div>";
        }
        ?>
    </div>
</div>


<script>
    const abrirBtn = document.getElementById("abrirCarrito");
    const cerrarBtn = document.getElementById("cerrarCarrito");
    const panel = document.getElementById("panelCarrito");

    abrirBtn?.addEventListener("click", () => {
        panel.classList.add("abierto");
    });

    cerrarBtn?.addEventListener("click", () => {
        panel.classList.remove("abierto");
    });
</script>

<footer id="contacto">
            <h3>Contacto</h3>
            <p>tiendaxochiahua@contacto.com.mx<p>
            <p>&copy; 2025 - Not Copyright Intended</p>
</footer>

</body>
</html>

