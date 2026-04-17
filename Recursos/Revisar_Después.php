<?php

// Se puede aplicar

class Tutores_Controlador {   

    private $tutorModelo;
    private $alumnoModelo;
    private $convenioModelo;

    public function __construct() {
        $this->tutorModelo = new Tutores();
        $this->alumnoModelo = new Alumnos();
        $this->convenioModelo = new Convenios();
    }

}

?>