<?php
class BaseDeDatos {
    private $host = "localhost";
    private $nombre_bd = "tienda_db";
    private $usuario = "root";
    private $contrasena = "12345678";
    public $conexion;

    public function obtenerConexion() {
        $this->conexion = null;
        try {
            $this->conexion = new PDO("mysql:host=$this->host;dbname=$this->nombre_bd;charset=utf8", $this->usuario, $this->contrasena);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Error en la conexión: " . $e->getMessage();
        }
        return $this->conexion;
    }
}
