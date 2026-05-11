<?php
// Controlador/Seguimiento_Subir.php
// Ruta destino: /Documentacion/{ciclo}/{alumno}/{Plan_Formativo|Fichas}/

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
validarAcceso('tutor');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

$tipo   = $_POST['tipo']   ?? '';
$ciclo  = $_POST['ciclo']  ?? '';
$alumno = $_POST['alumno'] ?? '';

if (!in_array($tipo, ['plan_formativo', 'fichas'], true) || empty($ciclo) || empty($alumno)) {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos.']);
    exit;
}

$ciclo  = preg_replace('/[^A-Za-z0-9]/', '', $ciclo);
$alumno = preg_replace('/[^A-Za-z0-9_]/', '', $alumno);

if (empty($ciclo) || empty($alumno)) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    exit;
}

if (!isset($_FILES['fichero']) || $_FILES['fichero']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['fichero']['error'] ?? 'sin fichero';
    echo json_encode(['success' => false, 'error' => "Error en la subida (código: $err)."]);
    exit;
}

$subcarpeta = $tipo === 'plan_formativo' ? 'Plan_Formativo' : 'Fichas';
$baseDoc   = $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Documentacion/';
$rutaDest  = $baseDoc . $ciclo . '/' . $alumno . '/' . $subcarpeta . '/';

// Crear carpetas solo si no existen
if (!is_dir($rutaDest)) {
    if (!mkdir($rutaDest, 0755, true)) {
        echo json_encode(['success' => false, 'error' => 'No se pudo crear la carpeta destino.']);
        exit;
    }
}

// Sanear nombre y evitar sobreescritura
$nombreOriginal = basename($_FILES['fichero']['name']);
$nombreSeguro   = preg_replace('/[^A-Za-z0-9._\-]/', '_', $nombreOriginal);
if (empty($nombreSeguro)) $nombreSeguro = 'documento_' . time();

$destino = $rutaDest . $nombreSeguro;
if (file_exists($destino)) {
    $info         = pathinfo($nombreSeguro);
    $nombreSeguro = ($info['filename'] ?? 'doc') . '_' . time() . '.' . ($info['extension'] ?? 'bin');
    $destino      = $rutaDest . $nombreSeguro;
}

if (!move_uploaded_file($_FILES['fichero']['tmp_name'], $destino)) {
    echo json_encode(['success' => false, 'error' => 'No se pudo mover el fichero al destino.']);
    exit;
}

echo json_encode(['success' => true, 'nombre' => $nombreSeguro]);
exit;
