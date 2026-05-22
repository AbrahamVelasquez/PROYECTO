<?php

/**
 * Controlador/Controlador_Convenios_Validos.php
 *
 * Admin — Tabla de convenios oficiales.
 * Gestiona la consulta, edición, importación masiva y descarga
 * de plantilla de los convenios validados.
 */

// Controlador/Controlador_Convenios_Validos.php
// Admin — Tabla de convenios oficiales: consulta, edición, importación y exportación.

require_once 'Modelo/Convenios.php';
require_once 'Modelo/Convenios_Nuevos.php';
require_once 'Modelo/Tutores.php';

class Convenios_Validos_Controlador {

    private $convenio;
    private $conveniosNuevos;
    private $tutoresModelo;

    public function __construct() {
        $this->convenio        = new Convenios();
        $this->conveniosNuevos = new Convenios_Nuevos();
        $this->tutoresModelo   = new Tutores();
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — TABLA CONVENIOS  (Vista: Admin/Sections/Tabla_Convenios.php)
    // ═══════════════════════════════════════════════════════════════════

    public function mostrarConvenios() {
        $busqueda  = $_REQUEST['busqueda'] ?? '';
        $ordenar   = $_REQUEST['ordenar']  ?? 'nombre_empresa';
        $convenios      = $this->convenio->obtenerConvenios($busqueda, $ordenar);
        $todosLosCiclos = $this->tutoresModelo->obtenerTodosLosCiclos();

        $subVista = 'Admin/Sections/Tabla_Convenios.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function eliminarConvenio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_convenio_borrar'])) {
            $num = $_POST['id_convenio_borrar'];
            if ($this->convenio->estaEnUsoGlobal($num)) {
                $_SESSION['error_convenio_en_uso'] = true;
            } else {
                $this->convenio->eliminarConvenio($num);
            }
        }
        $this->mostrarConvenios();
    }

    public function actualizarConvenio() {
        if (isset($_POST['num_convenio']) && (isset($_POST['cif_original']) || isset($_POST['nombre_original']))) {
            $num_convenio    = $_POST['num_convenio'];
            $cif_original    = $_POST['cif_original'];
            $nombre_original = $_POST['nombre_original'];

            $d = [
                'nombre_empresa'         => $_POST['nombre_empresa'],
                'cif'                    => $_POST['cif'],
                'telefono'               => $_POST['telefono']               ?? null,
                'fax'                    => $_POST['fax']                    ?? null,
                'direccion'              => $_POST['direccion']               ?? null,
                'localidad'              => $_POST['localidad']               ?? null,
                'cp'                     => $_POST['cp']                     ?? null,
                'representante'          => $_POST['representante']           ?? null,
                'especialidad'           => $_POST['especialidad']           ?? null ?: null,
                'fecha_alta_renovacion'  => $_POST['fecha_alta_renovacion']  ?? null ?: null,
                'fecha_nueva_renovacion' => $_POST['fecha_nueva_renovacion'] ?? null ?: null,
                'observaciones'          => $_POST['observaciones']          ?? null ?: null,
            ];

            $this->convenio->actualizarConvenio($num_convenio, $d);
            $this->conveniosNuevos->sincronizarConvenioPendiente($cif_original, $nombre_original, $d);

            header("Location: index.php?accion=mostrarConvenios");
            exit();
        }
    }

    public function importarConvenios() {
        require_once 'Helpers/Importar_Convenios.php';
    }

    public function descargarPlantillaConvenios() {
        $ruta = ROOT_PATH . 'Recursos/Importar/plantilla_listadoConvenios.xlsx';
        if (!file_exists($ruta)) { http_response_code(404); exit('Plantilla no encontrada.'); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="plantilla_listadoConvenios.xlsx"');
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: no-cache');
        readfile($ruta);
        exit;
    }

}
