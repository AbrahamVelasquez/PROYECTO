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

    public function mostrarPanel() {
        $tutores = $this->admin->obtenerTutores();
        
        // Cuando tiene los datos, llama a la Vista: "Muéstralos así"
        require 'Vista/Tabla_Tutores.php';
    }

    

}

?>
