<?php

/**
 * Controlador/Controlador_Convenios_Pendientes.php
 *
 * Admin — Convenios pendientes de validación.
 * Gestiona la revisión, aprobación y eliminación de los convenios
 * solicitados por los tutores aún no trasladados a la tabla oficial.
 */

// Controlador/Controlador_Convenios_Pendientes.php
// Admin — Convenios pendientes de validación: revisión, aprobación y eliminación.

require_once 'Modelo/Convenios_Nuevos.php';
require_once 'Modelo/Tutores.php';

class Convenios_Pendientes_Controlador {

    private $conveniosNuevos;
    private $tutoresModelo;

    public function __construct() {
        $this->conveniosNuevos = new Convenios_Nuevos();
        $this->tutoresModelo   = new Tutores();
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — CONVENIOS PENDIENTES  (Vista: Admin/Sections/Tabla_Convenios_Pendientes.php)
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarConveniosPendientes() {
        $pendientes     = $this->conveniosNuevos->obtenerConveniosPendientes();
        $todosLosCiclos = $this->tutoresModelo->obtenerTodosLosCiclos();

        $subVista = 'Admin/Sections/Tabla_Convenios_Pendientes.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function validarConvenio() {
        if (isset($_POST['nombre_empresa'])) {
            $datos = [
                'id_convenio_nuevo'      => $_POST['id_convenio_nuevo'],
                'nombre_empresa'         => $_POST['nombre_empresa'],
                'cif'                    => $_POST['cif'],
                'direccion'              => $_POST['direccion'],
                'localidad'              => $_POST['localidad']              ?? null,
                'cp'                     => $_POST['cp'],
                'telefono'               => $_POST['telefono']               ?? null,
                'fax'                    => $_POST['fax']                    ?? null,
                'representante'          => $_POST['representante']          ?? null,
                'especialidad'           => $_POST['especialidad']           ?? null,
                'num_convenio'           => $_POST['num_convenio']           ?? null,
                'fecha_alta_renovacion'  => $_POST['fecha_alta_renovacion']  ?? null,
                'fecha_nueva_renovacion' => $_POST['fecha_nueva_renovacion'] ?? null,
                'observaciones'          => $_POST['observaciones']          ?? null,
            ];

            if (isset($_POST['solo_guardar'])) {
                $this->conveniosNuevos->actualizarConvenioPendiente($datos);
            } else {
                $this->conveniosNuevos->procesarValidacionManual($datos);
            }
        } else if (isset($_POST['id_convenio_nuevo'])) {
            $this->conveniosNuevos->validarConvenio($_POST['id_convenio_nuevo']);
        }

        $this->mostrarConveniosPendientes();
    }

    public function eliminarConvenioCompleto() {
        if (isset($_REQUEST['id'])) {
            $exito = $this->conveniosNuevos->borrarRegistroPendienteYOficial($_REQUEST['id']);
            if ($exito) {
                header("Location: index.php?accion=mostrarConveniosPendientes");
            } else {
                echo "Error al intentar eliminar el registro.";
            }
            exit();
        }
    }

}
