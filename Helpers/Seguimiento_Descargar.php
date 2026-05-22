<?php

/**
 * Helpers/Seguimiento_Descargar.php — Endpoint: descargar un archivo de documentación
 *
 * Sirve un archivo PDF como descarga forzada (Content-Disposition: attachment).
 * Obtiene la ruta física real a través del modelo para que no sea el navegador
 * quien construya la ruta — evitamos exponer la estructura de carpetas del servidor.
 *
 * Llamado desde: el botón de descarga en el modal de documentos del Paso 4 (GET)
 * Responde con: el binario del archivo con las cabeceras de descarga,
 *               o HTTP 400/404 si los parámetros son inválidos o el archivo no existe.
 *
 * Seguridad: requiere sesión de tutor activa. El modelo aplica basename()
 * sobre el nombre del archivo para prevenir path traversal.
 */

require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../Modelo/GestorDocumentacion.php';

validarAcceso('tutor');

$tipo   = $_GET['tipo']   ?? '';
$ciclo  = $_GET['ciclo']  ?? '';
$alumno = $_GET['alumno'] ?? '';
$nombre = $_GET['nombre'] ?? '';

if (!in_array($tipo, ['plan_formativo', 'fichas', 'valoraciones'], true) || empty($ciclo) || empty($nombre)) {
    http_response_code(400);
    exit('Parámetros inválidos.');
}

$rutaReal = GestorDocumentacion::obtenerRutaDescarga($ciclo, $tipo, $nombre);

if (!$rutaReal) {
    http_response_code(404);
    exit('El fichero no existe o ha sido eliminado.');
}

// Limpiamos cualquier output previo antes de enviar el binario
if (ob_get_length()) ob_clean();

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($rutaReal) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($rutaReal));

readfile($rutaReal);
exit;