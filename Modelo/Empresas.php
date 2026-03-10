<?php

require_once "./Core/Conexion.php"; // Importa la clase Conexion 

class Empresas {

    private $id_empresa;
    private $nombre_empresa;
    private $direccion;
    private $email;
    private $telefono;
    private $responsable;
    private $conn; // Para que inicie la conexión asi accedemos a la BD 

    public function __construct() {
        $this -> conn = Conexion::getConexion();  // El modelo obtiene esa conexión
    }

}


?>