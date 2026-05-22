<?php

/**
 * Helpers/Seguimiento_Eliminar.php — Endpoint AJAX: eliminar un archivo de documentación
 *
 * Borra físicamente un archivo del disco a partir del ciclo, tipo y nombre recibidos.
 * El modelo aplica basename() sobre el nombre antes de construir la ruta,
 * lo que previene cualquier intento de path traversal ("../../etc/passwd").
 *
 * Llamado desde: el botón de eliminar en el modal de documentos del Paso 4
 * Responde con: { success: true } o { success: false, error: '...' }
 *
 * Seguridad: requiere sesión de tutor activa, solo acepta POST,
 * y valida el tipo de carpeta antes de actuar.
 */

require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../Modelo/GestorDocumentacion.php';

validarAcceso('tutor');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

$tipo   = $_POST['tipo']   ?? '';
$ciclo  = $_POST['ciclo']  ?? '';
$alumno = $_POST['alumno'] ?? '';
$nombre = $_POST['nombre'] ?? '';

if (!in_array($tipo, ['plan_formativo', 'fichas', 'valoraciones'], true) || empty($ciclo) || empty($nombre)) {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos.']);
    exit;
}

$eliminado = GestorDocumentacion::eliminarFichero($ciclo, $tipo, $nombre);

if ($eliminado) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el fichero o no existe.']);
}
exit;