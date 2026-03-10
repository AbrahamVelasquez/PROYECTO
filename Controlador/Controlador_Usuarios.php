<?php

// Traemos el Modelo
require_once './Modelo/Usuarios.php';

class Usuarios_Controlador {

    private $usuario; 
    // Este atributo será el objeto
    // con el que accederé a los métodos
    // de verificación e inicio.
    // Por defecto es NULL

    // El constructor para darle valor al atributo.
    public function __construct() {
        $this -> usuario = new Usuarios();
    }

    // Valido el usuario 
    public function validarUsuario() {
        $this -> usuario -> validarDatos();
    }

}

?>
