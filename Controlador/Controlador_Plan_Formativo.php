<?php

/**
 * Controlador/Controlador_Plan_Formativo.php
 *
 * Paso 3 — Plan Formativo.
 * Gestiona los Resultados de Aprendizaje, la exportación del plan
 * y la reversión del estado de firma/exportación de asignaciones.
 */

// Controlador/Controlador_Plan_Formativo.php
// Paso 3 — Plan Formativo: gestión de RAs, exportación y reversión de firmas.

require_once 'Modelo/Alumnos.php';
require_once 'Modelo/Asignaciones.php';
require_once 'Modelo/Modulos.php';
require_once 'Modelo/Resultados_Aprendizaje.php';

class Plan_Formativo_Controlador {

    private $alumnoModelo;
    private $asignacionModelo;
    private $modulosModelo;
    private $rasModelo;

    public function __construct() {
        $this->alumnoModelo     = new Alumnos();
        $this->asignacionModelo = new Asignaciones();
        $this->modulosModelo    = new Modulos();
        $this->rasModelo        = new Resultados_Aprendizaje();
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 3 — PLAN FORMATIVO  (Vista: Steps/Plan_Formativo.php)
    // ═══════════════════════════════════════════════════════════════════

    public function guardarRA() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        try {
            $resultado = $this->rasModelo->guardarResultadosAprendizaje(
                $_SESSION['id_ciclo'] ?? 0,
                json_decode($_POST['ra_nuevos']   ?? '[]', true) ?? [],
                json_decode($_POST['ra_eliminar'] ?? '[]', true) ?? []
            );
            echo json_encode(['success' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function obtenerRAs() {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        try {
            $ras = $this->rasModelo->obtenerRAsPorTutor($_SESSION['id_ciclo'] ?? 0) ?: [];
            echo json_encode($ras, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function exportarExcelPF() {
        require_once 'Helpers/Exportar_PF.php';
    }

    public function exportarTodoPF() {
        require_once 'Helpers/Exportar_PF_Todo.php';
    }

    public function devolverAlumnoAEnvio() {
        $idAlumno = $_REQUEST['id_alumno'] ?? null;

        if ($idAlumno) {
            $resultado = $this->asignacionModelo->devolverAlumnoAEnvio($idAlumno);
            if ($resultado) {
                header("Location: index.php?tab=3");
            } else {
                header("Location: index.php?tab=3");
            }
        } else {
            header("Location: index.php?tab=3");
        }
        exit();
    }

    public function marcarComoExportado() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_asignacion'])) {
                $idAsignacion = intval($_POST['id_asignacion']);

                if (isset($_POST['anexo']) && $_POST['anexo'] !== '') {
                    $this->asignacionModelo->actualizarAnexo($idAsignacion, $_POST['anexo']);
                }

                $resultado = $this->alumnoModelo->actualizarTodoYExportar($idAsignacion, $_POST);

                echo json_encode($resultado
                    ? ['success' => true]
                    : ['success' => false, 'error' => 'No se pudo actualizar la base de datos']
                );
            } else {
                echo json_encode(['success' => false, 'error' => 'Faltan parámetros']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function cambiarEstadoExportacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        $idsAsignacion = json_decode($_POST['ids_asignacion'] ?? '[]', true);
        $nuevoEstado   = isset($_POST['nuevo_estado']) ? (int)$_POST['nuevo_estado'] : 0;

        if (empty($idsAsignacion)) {
            echo json_encode(['success' => false, 'error' => 'No se recibieron IDs para actualizar']);
            return;
        }

        $resultado = $this->asignacionModelo->reiniciarEstadoExportacion($idsAsignacion, $nuevoEstado);
        echo json_encode($resultado
            ? ['success' => true]
            : ['success' => false, 'error' => 'Error al actualizar la base de datos']
        );
    }

}
