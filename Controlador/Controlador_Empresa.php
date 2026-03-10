<?php

// Traemos el Modelo
require_once './Modelo/Empresas.php';

class Empresas_Controlador {

    private $empresa; 
    // Este atributo será el objeto
    // con el que accederé a los métodos
    // de verificación e inicio.
    // Por defecto es NULL

    // El constructor para darle valor al atributo.
    public function __construct() {
        $this -> empresa = new Empresas();
    }

}

?>
