<?php

/**
 * Controlador/Controlador_Alumnos.php
 *
 * Paso 2 — Gestión de alumnos.
 * Cubre el alta, edición, firma y exportación de alumnos,
 * así como la importación masiva desde plantilla Excel.
 * También contiene el mostrarAlumnos del panel admin (pendiente
 * de mover a Controlador_Listado_Alumnos).
 */

// Controlador/Controlador_Alumnos.php
// Paso 2 — Gestión de alumnos: alta, edición, firma y exportación.

require_once 'Modelo/Alumnos.php';
require_once 'Modelo/Asignaciones.php';

class Alumnos_Controlador {

    private $alumnoModelo;
    private $asignacionModelo;

    public function __construct() {
        $this->alumnoModelo     = new Alumnos();
        $this->asignacionModelo = new Asignaciones();
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 2 — ALUMNOS  (Vista: Steps/Alumnos.php)
    // ═══════════════════════════════════════════════════════════════════

    public function agregarAlumno() {
        if (!isset($_SESSION['id_ciclo'])) {
            die("Error: No se ha detectado el ciclo formativo en la sesión.");
        }

        $resultado = $this->alumnoModelo->agregarAlumno(
            trim($_POST['nombre']),
            trim($_POST['apellido1']),
            trim($_POST['apellido2'] ?? ''),
            trim($_POST['dni']),
            $_POST['sexo'],
            trim($_POST['correo']    ?? ''),
            trim($_POST['telefono']  ?? ''),
            $_SESSION['id_ciclo'],
            (int)($_POST['anio_inicio'] ?? date('Y'))
        );

        if ($resultado === true) {
            header('Location: index.php?tab=2');
            exit();
        } elseif ($resultado === 'dni_duplicado') {
            header('Location: index.php?tab=2&error=dni_duplicado');
            exit();
        } else {
            die("Error al insertar en la base de datos.");
        }
    }

    public function obtenerAlumno() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if (isset($_POST['verificar_firma'])) {
            $yaFirmado = $this->asignacionModelo->comprobarFirmaExistente((int)$_POST['id_asignacion']);
            echo json_encode(['yaFirmado' => $yaFirmado]);
            exit();
        }

        $idAlumno = isset($_POST['id_alumno']) ? (int)$_POST['id_alumno'] : 0;
        $alumno   = $this->alumnoModelo->obtenerPorId($idAlumno);

        if (!$alumno) {
            echo json_encode(['error' => 'Alumno no encontrado', 'id_recibido' => $idAlumno]);
        } else {
            $alumno['num_convenio'] = $alumno['num_convenio'] ?: null;
            $alumno['yaFirmado']    = $this->asignacionModelo->comprobarFirmaExistente($alumno['id_asignacion'] ?? 0);
            echo json_encode($alumno);
        }
        exit();
    }

    public function editarAlumno() {
        $idAlumno   = $_POST['id_alumno'];
        $idConvenio = $_POST['num_convenio'] ?? '';
        $enviado    = isset($_POST['enviado']) ? 1 : 0;

        if (empty($idConvenio)) {
            $this->asignacionModelo->eliminarAsignacion($idAlumno);
            $res = $this->alumnoModelo->actualizarDatosBasicos(
                $idAlumno,
                trim($_POST['nombre']),
                trim($_POST['apellido1']),
                trim($_POST['apellido2'] ?? ''),
                trim($_POST['dni']),
                $_POST['sexo'],
                trim($_POST['correo']   ?? ''),
                trim($_POST['telefono'] ?? '')
            );
            if ($res === 'dni_duplicado') {
                header('Location: index.php?tab=2&error=dni_duplicado');
                exit();
            }
            header('Location: index.php?tab=2');
            exit();
        }

        $res = $this->alumnoModelo->editarAlumno(
            $idAlumno,
            trim($_POST['nombre']),
            trim($_POST['apellido1']),
            trim($_POST['apellido2'] ?? ''),
            trim($_POST['dni']),
            $_POST['sexo'],
            trim($_POST['correo']   ?? ''),
            trim($_POST['telefono'] ?? ''),
            $idConvenio,
            $_POST['fecha_inicio'] ?: null,
            $_POST['fecha_final']  ?: null,
            trim($_POST['horario']  ?? ''),
            $_POST['horas_dia']       ?: null,
            $_POST['num_total_horas'] ?? null,
            $enviado,
            trim($_POST['nombre_tutor_empresa']  ?? ''),
            trim($_POST['correo_tutor_empresa']  ?? ''),
            trim($_POST['tel_tutor_empresa']     ?? ''),
            trim($_POST['horario_excepciones']   ?? '') ?: null
        );

        if ($res === 'dni_duplicado') {
            header('Location: index.php?tab=2&error=dni_duplicado');
            exit();
        }

        header('Location: index.php?tab=2');
        exit();
    }

    public function firmarAlumno() {
        $id_asignacion = $_POST['id_asignacion'] ?? null;
        $anexo         = $_POST['anexo']         ?? null;

        if ($id_asignacion) {
            $resultado = $this->asignacionModelo->firmarAsignacion($id_asignacion, $anexo);
            if ($resultado) {
                header("Location: index.php?tab=2");
            } else {
                header("Location: index.php?tab=2");
            }
            exit;
        }
    }

    public function importarAlumnos() {
        require_once 'Helpers/Importar_Alumnos.php';
    }

    public function descargarPlantillaAlumnos() {
        $ruta = ROOT_PATH . 'Recursos/Importar/plantilla_listadoAlumnos.xlsx';
        if (!file_exists($ruta)) { http_response_code(404); exit('Plantilla no encontrada.'); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="plantilla_listadoAlumnos.xlsx"');
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: no-cache');
        readfile($ruta);
        exit;
    }

    public function exportarAlumnosWord() {
        require_once 'Helpers/Exportar_Alumnos_Word.php';
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — LISTADO DE ALUMNOS  (se moverá a Controlador_Listado_Alumnos)
    // ═══════════════════════════════════════════════════════════════════

    public function eliminarAlumno() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $idAlumno = (int) ($_POST['id_alumno'] ?? 0);
            if (!$idAlumno) {
                echo json_encode(['ok' => false, 'motivo' => 'id_invalido']);
                exit();
            }

            $resultado = $this->alumnoModelo->eliminarAlumno($idAlumno);

            if ($resultado === 'tiene_asignacion') {
                echo json_encode(['ok' => false, 'motivo' => 'tiene_asignacion']);
            } else {
                echo json_encode(['ok' => (bool) $resultado]);
            }
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'motivo' => 'error_servidor', 'detalle' => $e->getMessage()]);
        }
        exit();
    }

    public function mostrarAlumnos() {
        $alumnos  = $this->alumnoModelo->obtenerAlumnosPendientesFirma();
        $subVista = 'Admin/Sections/Listado_Alumnos.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

}
