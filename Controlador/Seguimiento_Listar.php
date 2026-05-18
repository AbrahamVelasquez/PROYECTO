<?php
// Controlador/Seguimiento_Listar.php
// Ruta: /Documentacion/{ciclo}/{Plan_Formativo|Fichas}/  — ficheros filtrados por prefijo alumno

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
validarAcceso('tutor');

header('Content-Type: application/json');

$tipo   = $_GET['tipo']   ?? '';
$ciclo  = $_GET['ciclo']  ?? '';
$alumno = $_GET['alumno'] ?? ''; // prefijo: GARCIA_LOPEZ_DAW2

if (!in_array($tipo, ['plan_formativo', 'fichas'], true) || empty($ciclo) || empty($alumno)) {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos.']);
    exit;
}

$ciclo  = preg_replace('/[^A-Za-z0-9]/', '', $ciclo);
$prefijo = preg_replace('/[^A-Za-z0-9_]/', '', $alumno);

if (empty($ciclo) || empty($prefijo)) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos.']);
    exit;
}

$subcarpeta = $tipo === 'plan_formativo' ? 'Plan_Formativo' : 'Fichas';
$ruta = $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Documentacion/' . $ciclo . '/' . $subcarpeta . '/';

if (!is_dir($ruta)) {
    echo json_encode(['success' => true, 'archivos' => []]);
    exit;
}

// Devolver solo los ficheros que empiecen por el prefijo del alumno
$archivos = array_values(array_filter(
    scandir($ruta),
    fn($f) => !in_array($f, ['.', '..'])
           && is_file($ruta . $f)
           && stripos($f, $prefijo) === 0
));

echo json_encode(['success' => true, 'archivos' => $archivos]);
exit;
