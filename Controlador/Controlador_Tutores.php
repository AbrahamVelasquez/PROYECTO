<?php

// Traemos el Modelo
require_once './Modelo/Tutores.php';

class Tutores_Controlador {

    private $tutor; 
    // Este atributo será el objeto
    // con el que accederé a los métodos
    // de verificación e inicio.
    // Por defecto es NULL

    // El constructor para darle valor al atributo.
    public function __construct() {
        $this -> tutor = new Tutores();
    }

    public function probarRol() {
        $this -> tutor -> probarRol();
    }

}

?>
