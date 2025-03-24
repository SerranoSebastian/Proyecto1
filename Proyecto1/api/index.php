<?php
session_start();
require_once 'config/bd.php';
require_once 'controladores/AutenticacionControlador.php';

$bd = new BaseDeDatos();
$conexion = $bd->obtenerConexion();

$accion = $_GET['accion'] ?? '';

$autenticador = new AutenticacionControlador($conexion);

if ($accion === 'registrar') {
    $autenticador->registrar($_POST);
} elseif ($accion === 'login') {
    $autenticador->login($_POST);
} else {
    echo "Acción no válida";
}

require_once 'controladores/ProductoControlador.php';
$productoControlador = new ProductoControlador($conexion);

if ($accion === 'editar_producto') {
    $productoControlador->editarProducto($_POST);
}
require_once 'controladores/PedidoControlador.php';
$pedidoControlador = new PedidoControlador($conexion);

if ($accion === 'confirmar_pedido') {
    $pedidoControlador->crearPedido($_SESSION['usuario_id'], $_SESSION['carrito']);
}

if ($accion === 'agregar_producto') {
    $productoControlador->agregarProducto($_POST, $_FILES);
}

if ($accion === 'eliminar_producto') {
    $productoControlador->eliminarProducto($_GET['id']);
}
