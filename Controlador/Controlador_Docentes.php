<?php

/**
 * Controlador/Controlador_Docentes.php
 *
 * Admin — Personal Docente.
 * Gestiona el alta, edición, eliminación y listado de tutores,
 * incluyendo la creación automática de su cuenta de usuario.
 */

// Controlador/Controlador_Docentes.php
// Admin — Personal Docente: gestión de tutores y asignación de ciclos.

require_once 'Modelo/Tutores.php';

class Docentes_Controlador {

    private $tutoresModelo;

    public function __construct() {
        $this->tutoresModelo = new Tutores();
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — PERSONAL DOCENTE  (Vista: Admin/Sections/Tabla_Tutores.php)
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarTutores() {
        $busqueda     = $_REQUEST['busqueda']     ?? '';
        $ordenar      = $_REQUEST['ordenar']      ?? 'id';
        $filtro_curso = $_REQUEST['filtro_curso'] ?? '';

        $tutores        = $this->tutoresModelo->obtenerTutores($busqueda, $ordenar, $filtro_curso);
        $ciclosLibres   = $this->tutoresModelo->obtenerCiclosLibres();
        $todosLosCiclos = $this->tutoresModelo->obtenerTodosLosCiclos();

        $subVista = 'Admin/Sections/Tabla_Tutores.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function guardarTutor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'guardarTutor') {
            $this->tutoresModelo->guardarTutor([
                'dni'       => $_POST['dni']       ?? '',
                'nombre'    => $_POST['nombre']    ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'email'     => $_POST['email']     ?? '',
                'telefono'  => $_POST['telefono']  ?? '',
                'id_ciclo'  => $_POST['id_ciclo']  ?? '',
            ]);
        }
        $this->mostrarTutores();
    }

    public function actualizarTutor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->tutoresModelo->actualizarTutor([
                'id_tutor'  => $_POST['id_tutor'],
                'nombre'    => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'],
                'email'     => $_POST['email'],
                'telefono'  => $_POST['telefono'],
                'id_ciclo'  => $_POST['id_ciclo'],
            ]);
        }
        $this->mostrarTutores();
    }

    public function eliminarTutor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_tutor'])) {
            $this->tutoresModelo->eliminarTutor($_POST['id_tutor']);
        }
        $this->mostrarTutores();
    }

}
