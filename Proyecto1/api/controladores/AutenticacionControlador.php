<?php
class AutenticacionControlador {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function registrar($datos) {
        $nombre = $datos['nombre'];
        $correo = $datos['correo'];
        $contrasena = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
        $rol = 'cliente'; // Todos los registrados son clientes por defecto

        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (:nombre, :correo, :contrasena, :rol)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->bindParam(':rol', $rol);

        if ($stmt->execute()) {
            echo "Registro exitoso. <a href='../public/login.html'>Iniciar sesión</a>";
        } else {
            echo "Error al registrar usuario.";
        }
    }

    public function login($datos) {
        $correo = $datos['correo'];
        $contrasena = $datos['contrasena'];

        $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($contrasena, $usuario['contrasena'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['nombre'] = $usuario['nombre'];

                if ($usuario['rol'] === 'admin') {
                    header("Location: ../public/admin/dashboard.php");
                } else {
                    header("Location: ../public/tienda.php");
                }
                exit();
            }
        }

        echo "Correo o contraseña incorrectos.";
    }
}