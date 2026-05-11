<?php
// Controlador/Seguimiento_Eliminar.php
// Ruta: /Documentacion/{ciclo}/{alumno}/{Plan_Formativo|Fichas}/{nombre}

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
$nombre = $_POST['nombre'] ?? '';

if (!in_array($tipo, ['plan_formativo', 'fichas'], true) || empty($ciclo) || empty($alumno) || empty($nombre)) {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos.']);
    exit;
}

$ciclo  = preg_replace('/[^A-Za-z0-9]/', '', $ciclo);
$alumno = preg_replace('/[^A-Za-z0-9_]/', '', $alumno);
$nombre = basename($nombre); // Evita path traversal

if (empty($ciclo) || empty($alumno) || empty($nombre)) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    exit;
}

$subcarpeta = $tipo === 'plan_formativo' ? 'Plan_Formativo' : 'Fichas';
$ruta = $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Documentacion/' . $ciclo . '/' . $alumno . '/' . $subcarpeta . '/' . $nombre;

if (!file_exists($ruta) || !is_file($ruta)) {
    echo json_encode(['success' => false, 'error' => 'Fichero no encontrado.']);
    exit;
}

if (!unlink($ruta)) {
    echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el fichero.']);
    exit;
}

echo json_encode(['success' => true]);
exit;
