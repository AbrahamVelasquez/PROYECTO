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
        // Mapeamos id_ciclo (GET/POST) → especialidad, que es el nombre
        // del campo en convenios_nuevos v19. El formulario sigue usando
        // id_ciclo externamente, pero el modelo espera especialidad.
        if (isset($_POST['id_ciclo'])) {
            $_POST['especialidad'] = $_POST['id_ciclo'];
        }

        // Compatibilidad: si por alguna razón llega municipio en vez de localidad
        if (!isset($_POST['localidad']) && isset($_POST['municipio'])) {
            $_POST['localidad'] = $_POST['municipio'];
        }

        $this->convControlador->guardarNuevoConvenioPendiente();
    }
}