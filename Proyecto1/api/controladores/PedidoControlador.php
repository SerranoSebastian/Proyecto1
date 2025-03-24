<?php
class PedidoControlador {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function crearPedido($id_usuario, $carrito) {
        if (empty($carrito)) {
            echo "El carrito está vacío.";
            return;
        }

        $this->conexion->beginTransaction();

        try {
            // Calcular total
            $total = 0;
            foreach ($carrito as $id => $cantidad) {
                $stmt = $this->conexion->prepare("SELECT precio, stock FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$producto || $producto['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente para producto ID $id");
                }

                $total += $producto['precio'] * $cantidad;
            }

            // Crear pedido
            $stmt = $this->conexion->prepare("INSERT INTO pedidos (id_usuario, total) VALUES (?, ?)");
            $stmt->execute([$id_usuario, $total]);
            $id_pedido = $this->conexion->lastInsertId();

            // Insertar detalles y actualizar stock
            foreach ($carrito as $id => $cantidad) {
                $stmt = $this->conexion->prepare("SELECT precio, stock FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                $precio_unitario = $producto['precio'];

                // Insertar detalle
                $detalle = $this->conexion->prepare("
                    INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario)
                    VALUES (?, ?, ?, ?)
                ");
                $detalle->execute([$id_pedido, $id, $cantidad, $precio_unitario]);

                // Actualizar stock
                $nuevo_stock = $producto['stock'] - $cantidad;
                $actualizar = $this->conexion->prepare("UPDATE productos SET stock = ? WHERE id = ?");
                $actualizar->execute([$nuevo_stock, $id]);
            }

            $this->conexion->commit();
            unset($_SESSION['carrito']);
            echo "Pedido realizado exitosamente. <a href='../public/tienda.php'>Volver a la tienda</a>";
        } catch (Exception $e) {
            $this->conexion->rollBack();
            echo "Error al realizar el pedido: " . $e->getMessage();
        }
    }
}