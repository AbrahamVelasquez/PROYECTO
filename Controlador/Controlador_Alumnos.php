<?php

// Traemos el Modelo
require_once './Modelo/Alumnos.php';

class Alumnos_Controlador {

    private $alumno; 
    // Este atributo será el objeto
    // con el que accederé a los métodos
    // de verificación e inicio.
    // Por defecto es NULL

    // El constructor para darle valor al atributo.
    public function __construct() {
        $this -> alumno = new Alumnos();
    }

}

?>
