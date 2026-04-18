<?php
// Convenios/Controlador_Registro.php

// Usamos __DIR__ para subir un nivel y entrar en la carpeta Controlador de la raíz
require_once __DIR__ . '/../Controlador/Controlador_Convenios.php';

class Controlador_Registro {
    private $convControlador;

    public function __construct() {
        $this->convControlador = new Convenios_Controlador();
    }

    public function procesarRegistro() {
        $this->convControlador->guardarNuevoConvenioPendiente();
    }
}