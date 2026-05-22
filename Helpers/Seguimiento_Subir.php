<?php

/**
 * Helpers/Seguimiento_Subir.php — Endpoint AJAX: subir un archivo de documentación
 *
 * Recibe un archivo via multipart/form-data (POST) y lo guarda en la carpeta
 * correcta del ciclo y tipo indicados. La creación de carpetas, el saneado del
 * nombre y la resolución de duplicados se delegan al GestorDocumentacion.
 *
 * Llamado desde: el modal de documentos del Paso 4 al pulsar "Subir"
 * Responde con: { success: true, nombre: 'archivo_guardado.pdf' }
 *              o { success: false, error: '...' }
 *
 * Seguridad: requiere sesión de tutor activa, solo acepta POST,
 * y valida que el tipo de carpeta sea uno de los tres permitidos.
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

if (!in_array($tipo, ['plan_formativo', 'fichas', 'valoraciones'], true) || empty($ciclo) || empty($alumno)) {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos.']);
    exit;
}

// Verificamos que el archivo llegó sin errores de subida antes de procesar
if (!isset($_FILES['fichero']) || $_FILES['fichero']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['fichero']['error'] ?? 'sin fichero';
    echo json_encode(['success' => false, 'error' => "Error en la subida (código: $err)."]);
    exit;
}

// El modelo se encarga de crear la carpeta si no existe, sanear el nombre y mover el archivo
$resultado = GestorDocumentacion::guardarFichero($ciclo, $tipo, $_FILES['fichero']);

echo json_encode($resultado);
exit;