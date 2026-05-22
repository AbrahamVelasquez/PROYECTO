<?php

/**
 * Controlador/Controlador_Seguimiento.php
 *
 * Paso 4 — Seguimiento de documentación.
 * Gestiona la subida, listado, descarga y eliminación de PDFs
 * (planes formativos firmados y fichas de seguimiento).
 */

// Controlador/Controlador_Seguimiento.php
// Paso 4 — Seguimiento de documentación: subida, listado, descarga y eliminación de PDFs.

class Seguimiento_Controlador {

    // ═══════════════════════════════════════════════════════════════════
    // PASO 4 — SEGUIMIENTO  (Vista: Steps/Seguimiento.php)
    // ═══════════════════════════════════════════════════════════════════

    public function listar() {
        require_once 'Helpers/Seguimiento_Listar.php';
    }

    public function subir() {
        require_once 'Helpers/Seguimiento_Subir.php';
    }

    public function eliminar() {
        require_once 'Helpers/Seguimiento_Eliminar.php';
    }

    public function descargar() {
        require_once 'Helpers/Seguimiento_Descargar.php';
    }

}
