<?php
// Controlador/Seguimiento_Descargar.php
// Sirve un fichero de /Documentacion/{ciclo}/{Plan_Formativo|Fichas}/{alumno}/

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
validarAcceso('tutor');

$tipo   = $_GET['tipo']   ?? '';
$ciclo  = $_GET['ciclo']  ?? '';
$alumno = $_GET['alumno'] ?? '';
$nombre = $_GET['nombre'] ?? '';

if (!in_array($tipo, ['plan_formativo', 'fichas'], true) || empty($ciclo) || empty($alumno) || empty($nombre)) {
    http_response_code(400);
    exit('Parámetros inválidos.');
}

$ciclo  = preg_replace('/[^A-Za-z0-9]/', '', $ciclo);
$alumno = preg_replace('/[^A-Za-z0-9_]/', '', $alumno);
$nombre = basename($nombre); // evita path traversal

if (empty($ciclo) || empty($alumno) || empty($nombre)) {
    http_response_code(400);
    exit('Datos inválidos.');
}

$subcarpeta = $tipo === 'plan_formativo' ? 'Plan_Formativo' : 'Fichas';
$ruta = $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Documentacion/' . $ciclo . '/' . $subcarpeta . '/' . $nombre;

if (!file_exists($ruta) || !is_file($ruta)) {
    http_response_code(404);
    exit('Fichero no encontrado.');
}

// Detectar MIME type
$mime = mime_content_type($ruta) ?: 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Content-Length: ' . filesize($ruta));
header('Cache-Control: no-cache');
readfile($ruta);
exit;