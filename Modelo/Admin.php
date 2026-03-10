<?php

require_once "./Core/Conexion.php"; // Importa la clase Conexion 

class Admin {

    private $conn; // Para que inicie la conexión asi accedemos a la BD 

    public function __construct() {
        $this -> conn = Conexion::getConexion();  // El modelo obtiene esa conexión
    }
      
    public function probarRol() {
        echo "hola soy admin";
    }


}


?>