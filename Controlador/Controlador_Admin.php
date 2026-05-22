<?php

/**
 * Controlador/Controlador_Admin.php
 *
 * Dispatcher principal del rol admin.
 * Carga el panel de inicio y delega cada sección al controlador específico.
 *
 * Secciones gestionadas:
 *   Personal Docente    → Controlador_Docentes
 *   Convenios Válidos   → Controlador_Convenios_Validos
 *   Conv. Pendientes    → Controlador_Convenios_Pendientes
 *   Listado Alumnos     → Controlador_Listado_Alumnos
 */

// Controlador/Controlador_Admin.php
// Dispatcher principal del admin. Carga el panel y delega cada acción
// al controlador de sección correspondiente.

require_once 'Controlador/Controlador_Docentes.php';
require_once 'Controlador/Controlador_Convenios_Validos.php';
require_once 'Controlador/Controlador_Convenios_Pendientes.php';
require_once 'Controlador/Controlador_Listado_Alumnos.php';

class Admin_Controlador {

    private $docentesCtrl;
    private $conveniosValidosCtrl;
    private $conveniosPendientesCtrl;
    private $listadoAlumnosCtrl;

    public function __construct() {
        $this->docentesCtrl             = new Docentes_Controlador();
        $this->conveniosValidosCtrl     = new Convenios_Validos_Controlador();
        $this->conveniosPendientesCtrl  = new Convenios_Pendientes_Controlador();
        $this->listadoAlumnosCtrl       = new Listado_Alumnos_Controlador();
    }

    // ═══════════════════════════════════════════════════════════════════
    // PANEL PRINCIPAL
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarPanel() {
        $subVista = 'Admin/Components/Dashboard_Sections.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    // ═══════════════════════════════════════════════════════════════════
    // PERSONAL DOCENTE  → Controlador_Docentes
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarTutores()   { $this->docentesCtrl->mostrarTutores(); }
    public function guardarTutor()     { $this->docentesCtrl->guardarTutor(); }
    public function actualizarTutor()  { $this->docentesCtrl->actualizarTutor(); }
    public function eliminarTutor()    { $this->docentesCtrl->eliminarTutor(); }

    // ═══════════════════════════════════════════════════════════════════
    // CONVENIOS VÁLIDOS  → Controlador_Convenios_Validos
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarConvenios()            { $this->conveniosValidosCtrl->mostrarConvenios(); }
    public function eliminarConvenio()            { $this->conveniosValidosCtrl->eliminarConvenio(); }
    public function actualizarConvenio()          { $this->conveniosValidosCtrl->actualizarConvenio(); }
    public function importarConvenios()           { $this->conveniosValidosCtrl->importarConvenios(); }
    public function descargarPlantillaConvenios() { $this->conveniosValidosCtrl->descargarPlantillaConvenios(); }

    // ═══════════════════════════════════════════════════════════════════
    // CONVENIOS PENDIENTES  → Controlador_Convenios_Pendientes
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarConveniosPendientes() { $this->conveniosPendientesCtrl->mostrarConveniosPendientes(); }
    public function validarConvenio()            { $this->conveniosPendientesCtrl->validarConvenio(); }
    public function eliminarConvenioCompleto()   { $this->conveniosPendientesCtrl->eliminarConvenioCompleto(); }

    // ═══════════════════════════════════════════════════════════════════
    // LISTADO DE ALUMNOS  → Controlador_Listado_Alumnos
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarAlumnos()   { $this->listadoAlumnosCtrl->mostrarAlumnos(); }
    public function firmarAlumnoAdmin() { $this->listadoAlumnosCtrl->firmarAlumnoAdmin(); }

} // Llave de la clase
