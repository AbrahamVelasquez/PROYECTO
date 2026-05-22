<?php

/**
 * Helpers/Seguimiento_Listar.php — Endpoint AJAX: listar archivos de un alumno
 *
 * Devuelve en JSON los nombres de los archivos subidos para un alumno concreto
 * dentro de una carpeta de tipo (plan_formativo, fichas o valoraciones).
 *
 * Llamado desde: el modal de documentos del Paso 4 al abrirse (GET)
 * Responde con: { success: true, archivos: ['archivo1.pdf', ...] }
 *
 * El prefijo del alumno (su apellido + ciclo) se sanea aquí antes de pasarlo
 * al modelo para evitar cualquier intento de path traversal.
 *
 * Seguridad: requiere sesión de tutor activa. Rechaza tipos de carpeta no permitidos.
 */

require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../Modelo/GestorDocumentacion.php';

validarAcceso('tutor');

header('Content-Type: application/json');

$tipo   = $_GET['tipo']   ?? '';
$ciclo  = $_GET['ciclo']  ?? '';
$alumno = $_GET['alumno'] ?? '';

// Solo se permiten estos tres tipos de carpeta
if (!in_array($tipo, ['plan_formativo', 'fichas', 'valoraciones'], true) || empty($ciclo) || empty($alumno)) {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos.']);
    exit;
}

// Saneamos el prefijo para que solo contenga caracteres seguros en rutas de archivo
$prefijoSaneado = preg_replace('/[^A-Za-z0-9_]/', '', $alumno);

if (empty($ciclo) || empty($prefijoSaneado)) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    exit;
}

$lista = GestorDocumentacion::listarFicheros($ciclo, $tipo, $prefijoSaneado);

echo json_encode(['success' => true, 'archivos' => $lista]);
exit;