<?php

/**
 * Controlador/Controlador_Tutores.php
 *
 * Dispatcher principal del rol tutor.
 * Carga el panel reuniendo los datos de todos los pasos y delega
 * cada acción al controlador de paso correspondiente.
 *
 * Pasos gestionados:
 *   Paso 1 — Convenios      → Controlador_Convenios_Tutores
 *   Paso 2 — Alumnos        → Controlador_Alumnos
 *   Paso 3 — Plan Formativo → Controlador_Plan_Formativo
 *   Paso 4 — Seguimiento    → Controlador_Seguimiento
 */

// Controlador/Controlador_Tutores.php
// Dispatcher principal del tutor. Carga el panel y delega cada acción
// al controlador de paso correspondiente.

require_once 'Controlador/Controlador_Convenios_Tutores.php';
require_once 'Controlador/Controlador_Alumnos.php';
require_once 'Controlador/Controlador_Plan_Formativo.php';
require_once 'Controlador/Controlador_Seguimiento.php';
require_once 'Modelo/Tutores.php';
require_once 'Modelo/Alumnos.php';
require_once 'Modelo/Modulos.php';
require_once 'Modelo/Resultados_Aprendizaje.php';
require_once 'Modelo/Convenios.php';

class Tutores_Controlador {

    private $convTutoresCtrl;
    private $alumnosCtrl;
    private $planFormativoCtrl;
    private $seguimientoCtrl;

    public function __construct() {
        $this->convTutoresCtrl   = new Convenios_Tutores_Controlador();
        $this->alumnosCtrl       = new Alumnos_Controlador();
        $this->planFormativoCtrl = new Plan_Formativo_Controlador();
        $this->seguimientoCtrl   = new Seguimiento_Controlador();
    }


    // ═══════════════════════════════════════════════════════════════════
    // CARGA DEL PANEL  (reúne datos de todos los pasos)
    // ═══════════════════════════════════════════════════════════════════

    // Si bien ya existen las variables en el constructor,
    // por alguna razón se cambiaron y daba error al Javascript
    // de cambiar de paso "Steps". Por ende, se decidió así.
    public function mostrarPanel() {
        $pestanaActiva = $_GET['tab'] ?? 1;

        // --- Perfil del tutor ---
        $tutorModelo  = new Tutores();
        $perfil       = $tutorModelo->obtenerDatosPerfil($_SESSION['usuario']);

        $nombreTutor  = $perfil ? ($perfil['nombre'] . " " . $perfil['apellidos']) : $_SESSION['usuario'];
        $correoTutor  = $perfil['email']        ?? '';
        $telTutor     = $perfil['telefono']     ?? '';
        $cicloTutor   = $perfil['nombre_ciclo'] ?? 'Sin Ciclo';
        $cursoTutor   = $perfil['nombre_curso'] ?? 'Sin Curso';
        $idCicloTutor = $perfil['id_ciclo']     ?? 0;
        $_SESSION['id_ciclo'] = $idCicloTutor;

        // --- Paso 1: Convenios ---
        $data             = $this->convTutoresCtrl->gestionar();
        $convenios        = $data['busqueda_convenio'];
        $misConvenios     = $data['favoritos'];
        $convModelo       = new Convenios();
        $conveniosProceso = $convModelo->listarPendientesDeAprobacion($idCicloTutor);

        // --- Paso 2: Alumnos ---
        $busqueda        = $_REQUEST['busqueda']  ?? '';
        $estadoFiltro    = $_REQUEST['estado']    ?? '';
        $ordenar         = $_REQUEST['ordenar']   ?? '';
        $alumnoModelo    = new Alumnos();
        $misConveniosIds = array_column($misConvenios, 'num_convenio');
        $alumnos         = $alumnoModelo->listarPorCiclo($idCicloTutor, $busqueda, $estadoFiltro, $ordenar, $misConveniosIds);
        $alumnosFirmados = $alumnoModelo->listarAlumnosFirmados($idCicloTutor);

        // --- Paso 3: Plan Formativo ---
        $modulosModelo = new Modulos();
        $rasModelo     = new Resultados_Aprendizaje();
        $modulosCiclo  = $modulosModelo->obtenerModulosPorTutor($idCicloTutor);
        $rasExistentes = $rasModelo->obtenerRAsPorTutor($idCicloTutor);

        require_once 'Vista/Tutores/Dashboard_Tutores.php';
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 1 — CONVENIOS  → Controlador_Convenios_Tutores
    // ═══════════════════════════════════════════════════════════════════

    public function guardarNuevoConvenio()   { $this->convTutoresCtrl->guardarNuevoConvenio(); }
    public function aprobarNuevo()           { $this->convTutoresCtrl->aprobarNuevo(); }
    public function editarConvenioNuevo()    { $this->convTutoresCtrl->editarConvenioNuevo(); }
    public function eliminarConvenioNuevo()  { $this->convTutoresCtrl->eliminarConvenioNuevo(); }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 2 — ALUMNOS  → Controlador_Alumnos
    // ═══════════════════════════════════════════════════════════════════

    public function agregarAlumno()             { $this->alumnosCtrl->agregarAlumno(); }
    public function obtenerAlumno()             { $this->alumnosCtrl->obtenerAlumno(); }
    public function editarAlumno()              { $this->alumnosCtrl->editarAlumno(); }
    public function eliminarAlumno()            { $this->alumnosCtrl->eliminarAlumno(); }
    public function firmarAlumno()              { $this->alumnosCtrl->firmarAlumno(); }
    public function importarAlumnos()           { $this->alumnosCtrl->importarAlumnos(); }
    public function descargarPlantillaAlumnos() { $this->alumnosCtrl->descargarPlantillaAlumnos(); }
    public function exportarAlumnosWord()       { $this->alumnosCtrl->exportarAlumnosWord(); }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 3 — PLAN FORMATIVO  → Controlador_Plan_Formativo
    // ═══════════════════════════════════════════════════════════════════

    public function guardarRA()                { $this->planFormativoCtrl->guardarRA(); }
    public function obtenerRAs()               { $this->planFormativoCtrl->obtenerRAs(); }
    public function exportarExcelPF()          { $this->planFormativoCtrl->exportarExcelPF(); }
    public function exportarTodoPF()           { $this->planFormativoCtrl->exportarTodoPF(); }
    public function devolverAlumnoAEnvio()     { $this->planFormativoCtrl->devolverAlumnoAEnvio(); }
    public function marcarComoExportado()      { $this->planFormativoCtrl->marcarComoExportado(); }
    public function cambiarEstadoExportacion() { $this->planFormativoCtrl->cambiarEstadoExportacion(); }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 4 — SEGUIMIENTO  → Controlador_Seguimiento
    // ═══════════════════════════════════════════════════════════════════

    public function seguimientoListar()    { $this->seguimientoCtrl->listar(); }
    public function seguimientoSubir()     { $this->seguimientoCtrl->subir(); }
    public function seguimientoEliminar()  { $this->seguimientoCtrl->eliminar(); }
    public function seguimientoDescargar() { $this->seguimientoCtrl->descargar(); }

}
