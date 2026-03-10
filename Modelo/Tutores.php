<?php

require_once "./Core/Conexion.php"; // Importa la clase Conexion 

class Tutores {

    private $id_usuario;
    private $id_tutor;
    private $id_curso;
    private $dni;
    private $nombre;
    private $apellidos;
    private $email;
    private $telefono;
    private $conn; // Para que inicie la conexión asi accedemos a la BD 

    public function __construct() {
        $this -> conn = Conexion::getConexion();  // El modelo obtiene esa conexión
    }

    public function probarRol() {
        echo "hola soy tutor";
    }


}


?>