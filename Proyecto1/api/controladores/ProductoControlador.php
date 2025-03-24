<?php
class ProductoControlador {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function editarProducto($datos) {
        $id = $datos['id'];
        $nombre = $datos['nombre'];
        $descripcion = $datos['descripcion'];
        $precio = $datos['precio'];
        $stock = $datos['stock'];

        $sql = "UPDATE productos 
                SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock 
                WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header("Location: ../public/admin/productos.php");
            exit();
        } else {
            echo "Error al editar producto.";
        }
    }

    public function agregarProducto($datos, $archivo)
    {
        $nombre = $datos['nombre'];
        $descripcion = $datos['descripcion'];
        $precio = $datos['precio'];
        $stock = $datos['stock'];
    
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock) 
                VALUES (:nombre, :descripcion, :precio, :stock)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock);
    
        if (!$stmt->execute()) {
            echo "Error al guardar el producto.";
            return;
        }
    
        // Obtener ID recien insertado
        $id = $this->conexion->lastInsertId();
    
        // Se renombra la imagen para que coincida con el id del producto
        $extension = pathinfo($archivo['imagen']['name'], PATHINFO_EXTENSION);
        $nombreImagen = "" . $id . "." . strtolower($extension);
        $rutaDestino = dirname(__DIR__, 2) . "/public/img/" . $nombreImagen;
    
        // Mover la imagen
        if (!move_uploaded_file($archivo['imagen']['tmp_name'], $rutaDestino)) {
            echo "Error al subir la imagen.";
            return;
        }
    
        // Actualizar producto con nombre de imagen
        $sqlImagen = "UPDATE productos SET imagen = :imagen WHERE id = :id";
        $stmt = $this->conexion->prepare($sqlImagen);
        $stmt->bindParam(':imagen', $nombreImagen);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    
        header("Location: ../public/admin/productos.php");
        exit();
    }
    


public function eliminarProducto($id)
{
    // Obtener nombre de imagen
    $stmt = $this->conexion->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagen = $producto['imagen'];

    // Eliminar archivo físico
    if ($imagen && file_exists("../../public/img/" . $imagen)) {
        unlink("../../public/img/" . $imagen);
    }

    // Eliminar de base de datos
    $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: ../public/admin/productos.php");
        exit();
    } else {
        echo "Error al eliminar producto.";
    }
}

}
