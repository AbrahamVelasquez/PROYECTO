<?php

/**
 * Controlador/Controlador_Listado_Alumnos.php
 *
 * Admin — Listado de alumnos pendientes de firma.
 * Muestra los alumnos enviados sin firma todavía y gestiona
 * la firma desde el panel del admin.
 */

// Controlador/Controlador_Listado_Alumnos.php
// Admin — Listado de alumnos pendientes de firma y gestión de firma admin.

require_once 'Modelo/Alumnos.php';
require_once 'Modelo/Asignaciones.php';

class Listado_Alumnos_Controlador {

    private $alumnoModelo;
    private $asignacionModelo;

    public function __construct() {
        $this->alumnoModelo     = new Alumnos();
        $this->asignacionModelo = new Asignaciones();
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — LISTADO DE ALUMNOS  (Vista: Admin/Sections/Listado_Alumnos.php)
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarAlumnos() {
        $alumnos  = $this->alumnoModelo->obtenerAlumnosPendientesFirma();
        $subVista = 'Admin/Sections/Listado_Alumnos.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function firmarAlumnoAdmin() {
        if (isset($_POST['id_asignacion'])) {
            $this->asignacionModelo->firmarAsignacionAdmin(
                $_POST['id_asignacion'],
                $_POST['anexo'] ?? null
            );
        }
        $this->mostrarAlumnos();
    }

}
