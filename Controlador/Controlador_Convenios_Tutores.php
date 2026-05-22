<?php

/**
 * Controlador/Controlador_Convenios_Tutores.php
 *
 * Paso 1 — Convenios del tutor.
 * Gestiona búsqueda de convenios, favoritos (mi listado personal)
 * y el registro/edición/aprobación de convenios nuevos.
 */

// Controlador/Controlador_Convenios_Tutores.php
// Paso 1 — Convenios del tutor: búsqueda, favoritos y registro de nuevos convenios.

require_once 'Modelo/Convenios.php';

class Convenios_Tutores_Controlador {

    private $convenio;

    public function __construct() {
        $this->convenio = new Convenios();
    }


    // ═══════════════════════════════════════════════════════════════════
    // CARGA DE DATOS  (llamado desde mostrarPanel del tutor)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Gestiona búsqueda, favoritos y convenios en proceso.
     * Devuelve un array con los datos para la vista del Paso 1.
     */
    public function gestionar() {
        if (!isset($_SESSION['id_tutor'])) {
            header("Location: login.php");
            exit();
        }

        $id_tutor_actual = $_SESSION['id_tutor'];
        $id_ciclo_actual = $_SESSION['id_ciclo'] ?? null;
        $terminoBusqueda = $_REQUEST['busqueda_convenio'] ?? $_GET['busqueda'] ?? '';
        $resultadosBusqueda = [];

        if (isset($_POST['btnFavorito'])) {
            $resultado = $this->convenio->añadirAFavoritos($id_tutor_actual, $_POST['num_convenio_fav']);
            if ($resultado === "duplicado") $_SESSION['error_duplicado'] = true;
            header("Location: index.php?tab=1");
            exit();
        }

        if (trim($terminoBusqueda) !== '') {
            $resultadosBusqueda = $this->convenio->buscar($terminoBusqueda);
        }

        if (isset($_POST['btnEliminarFav'])) {
            $numConvenio     = $_POST['num_convenio_eliminar'];
            $id_ciclo_actual = $_SESSION['id_ciclo'];

            if ($this->convenio->estaEnUso($numConvenio, $id_ciclo_actual)) {
                $_SESSION['error_convenio'] = 'No puedes quitarlo de favoritos porque tienes alumnos de tu ciclo asignados a él.';
            } else {
                $this->convenio->eliminarDeFavoritos($id_tutor_actual, $numConvenio);
            }
            header("Location: index.php?tab=1");
            exit();
        }

        $misFavoritos     = $this->convenio->obtenerFavoritos($id_tutor_actual) ?: [];
        $conveniosProceso = $id_ciclo_actual
            ? $this->convenio->listarPendientesDeAprobacion($id_ciclo_actual)
            : [];

        return [
            'busqueda_convenio' => $resultadosBusqueda,
            'favoritos'         => $misFavoritos,
            'proceso'           => $conveniosProceso,
        ];
    }


    // ═══════════════════════════════════════════════════════════════════
    // ACCIONES  (registro y gestión de convenios nuevos)
    // ═══════════════════════════════════════════════════════════════════

    public function guardarNuevoConvenio() {
        $datos = [
            'nombre_empresa'         => trim($_POST['nombre_empresa']),
            'cif'                    => trim($_POST['cif']),
            'direccion'              => trim($_POST['direccion']),
            'localidad'              => trim($_POST['localidad']),
            'cp'                     => trim($_POST['cp']),
            'telefono'               => trim($_POST['telefono']        ?? ''),
            'fax'                    => trim($_POST['fax']             ?? ''),
            'representante'          => trim($_POST['representante'] ?? ''),
            'especialidad'           => $_POST['id_ciclo'],
            'fecha_nueva_renovacion' => $_POST['fecha_nueva_renovacion'] ?? null ?: null,
        ];

        $exito = $this->convenio->guardarNuevoConvenioPendiente($datos);

        if ($exito) {
            if (isset($_SESSION['usuario'])) {
                header("Location: index.php?tab=1");
                exit();
            } else {
                return true;
            }
        }
    }

    public function aprobarNuevo() {
        if (isset($_POST['id_convenio_nuevo'])) {
            $exito = $this->convenio->registrarAprobacion($_POST['id_convenio_nuevo']);
            if ($exito) {
                $_SESSION['mensaje_exito'] = "Convenio marcado como aprobado.";
            } else {
                $_SESSION['error_convenio'] = "No se pudo procesar la aprobación.";
            }
        }
        header("Location: index.php?tab=1");
        exit();
    }

    public function editarConvenioNuevo() {
        if (!isset($_POST['id_convenio_nuevo'])) {
            header('Location: index.php?tab=1');
            exit();
        }

        $datos = [
            'nombre_empresa'         => trim($_POST['nombre_empresa']),
            'cif'                    => trim($_POST['cif']),
            'direccion'              => trim($_POST['direccion']),
            'localidad'              => trim($_POST['localidad']),
            'cp'                     => trim($_POST['cp']),
            'telefono'               => trim($_POST['telefono']        ?? ''),
            'fax'                    => trim($_POST['fax']             ?? ''),
            'representante'          => trim($_POST['representante'] ?? ''),
            'fecha_nueva_renovacion' => $_POST['fecha_nueva_renovacion'] ?? null ?: null,
            'observaciones'          => trim($_POST['observaciones']   ?? '') ?: null,
        ];

        $exito = $this->convenio->actualizarConvenioNuevo($_POST['id_convenio_nuevo'], $datos);

        if ($exito) {
            $_SESSION['mensaje_exito'] = "Convenio actualizado correctamente.";
        } else {
            $_SESSION['error_convenio'] = "Error al actualizar los datos del convenio.";
        }

        header('Location: index.php?tab=1');
        exit();
    }

    public function eliminarConvenioNuevo() {
        $id = $_POST['id_convenio_nuevo'] ?? null;
        if ($id) {
            $exito = $this->convenio->eliminarConvenioNuevo($id);
            if ($exito) {
                $_SESSION['mensaje_exito'] = "Solicitud eliminada correctamente.";
            } else {
                $_SESSION['error_convenio'] = "No se pudo eliminar la solicitud.";
            }
        }
        header('Location: index.php?tab=1');
        exit();
    }

}
