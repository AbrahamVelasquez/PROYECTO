<?php

////////////////////////////////////////////////
// Este fichero, por ahora, no se está usando //
////////////////////////////////////////////////

require_once "./Core/Conexion.php"; // Importa la clase Conexion 

class Alumnos {

    private $id_alumno;
    private $id_curso;
    private $nombre;
    private $apellidos;
    private $email;
    private $telefono;
    private $conn; // Para que inicie la conexión asi accedemos a la BD 

    public function __construct() {
        $this -> conn = Conexion::getConexion();  // El modelo obtiene esa conexión
    }

}


?>