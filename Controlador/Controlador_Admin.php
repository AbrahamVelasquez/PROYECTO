<?php

// Traemos el Modelo
require_once './Modelo/Admin.php';

class Admin_Controlador {

    private $admin; 
    // Este atributo será el objeto
    // con el que accederé a los métodos
    // de verificación e inicio.
    // Por defecto es NULL

    // El constructor para darle valor al atributo.
    public function __construct() {
        $this -> admin = new Admin();
    }

    public function probarRol() {
        $this -> admin -> probarRol();
    }

}

?>
