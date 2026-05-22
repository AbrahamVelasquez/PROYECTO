<?php

/**
 * Convenios/Controlador_Registro.php — Controlador del formulario público de solicitud
 *
 * Recibe los datos del formulario de alta de convenio (Registro.php) y los delega
 * al modelo Convenios::guardarNuevoConvenioPendiente(). El resultado queda en la
 * tabla convenios_nuevos pendiente de aprobación por el admin.
 *
 * Usa __DIR__ para los require porque puede ser invocado desde Procesar.php
 * (cuyo CWD no es la raíz del proyecto).
 *
 * Compatibilidad: acepta tanto 'localidad' como 'municipio' en el POST para
 * mantener la compatibilidad con versiones anteriores del formulario externo.
 *
 * MVC: Controlador de registro público (fuera del sistema de login).
 */

require_once __DIR__ . '/../Core/Conexion.php';

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Modelo/Convenios.php';

class Controlador_Registro {

    private $convenioModelo;

    public function __construct() {
        $this->convenioModelo = new Convenios();
    }

    public function procesarRegistro() {

        // Compatibilidad: municipio → localidad
        if (!isset($_POST['localidad']) && isset($_POST['municipio'])) {
            $_POST['localidad'] = $_POST['municipio'];
        }

        $datos = [
            'nombre_empresa'         => trim($_POST['nombre_empresa']         ?? ''),
            'cif'                    => trim($_POST['cif']                    ?? ''),
            'direccion'              => trim($_POST['direccion']              ?? ''),
            'localidad'              => trim($_POST['localidad']              ?? ''),
            'cp'                     => trim($_POST['cp']                                ?? ''),
            'telefono'               => trim($_POST['telefono']                          ?? ''),
            'fax'                    => trim($_POST['fax']                               ?? ''),
            'representante'          => trim($_POST['representante']          ?? ''),
            'especialidad'           => trim($_POST['id_ciclo']                          ?? ''),
            'fecha_nueva_renovacion' => ($_POST['fecha_nueva_renovacion'] ?? '') ?: null,
        ];

        return $this->convenioModelo->guardarNuevoConvenioPendiente($datos);
    }
}
