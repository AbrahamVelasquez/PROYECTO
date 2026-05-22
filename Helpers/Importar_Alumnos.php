<?php

/**
 * Helpers/Importar_Alumnos.php — Endpoint AJAX: importar alumnos desde Excel
 *
 * Recibe un archivo .xlsx/.xls con el listado de alumnos, lo parsea con
 * PhpSpreadsheet y delega la inserción en BD al modelo Importar.
 *
 * Llamado desde: el botón "Importar Excel" del Paso 2 (Alumnos)
 * Acción:        index.php?accion=importarAlumnos (POST multipart)
 * Responde con:  { success, insertados, omitidos, errores, advertencias }
 *
 * La separación de responsabilidades aquí es clara:
 *   - Este helper: validar el archivo y leerlo como array
 *   - Modelo/Importar: aplicar la lógica de inserción fila por fila
 *
 * Seguridad: requiere sesión de tutor activa. Solo acepta .xlsx y .xls.
 * El ciclo se toma de la sesión, no del POST, para que el tutor no pueda
 * importar alumnos en ciclos que no le pertenecen.
 */

require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Modelo/Importar.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

validarAcceso('tutor');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

// ── 1. Validar que llegó el fichero ──────────────────────────────────────────
if (!isset($_FILES['fichero_alumnos']) || $_FILES['fichero_alumnos']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No se recibió ningún fichero o hubo un error al subirlo.']);
    exit;
}

$fichero = $_FILES['fichero_alumnos'];
$ext     = strtolower(pathinfo($fichero['name'], PATHINFO_EXTENSION));
$idCiclo = (int)($_SESSION['id_ciclo'] ?? 0);

if (!in_array($ext, ['xlsx', 'xls'])) {
    echo json_encode(['success' => false, 'error' => 'El fichero debe ser .xlsx o .xls.']);
    exit;
}

// El ciclo viene de la sesión, no del formulario, para que no sea manipulable
if (!$idCiclo) {
    echo json_encode(['success' => false, 'error' => 'No se detectó el ciclo formativo en la sesión.']);
    exit;
}

// ── 2. Leer el Excel como array de filas ─────────────────────────────────────
try {
    $spreadsheet = IOFactory::load($fichero['tmp_name']);
    $ws          = $spreadsheet->getActiveSheet();
    $rows        = $ws->toArray(null, true, true, false);
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'error' => 'No se pudo leer el fichero: ' . $e->getMessage()]);
    exit;
}

// ── 3. Insertar en BD — toda la lógica vive en el modelo ─────────────────────
$anioInicio = isset($_POST['anio_inicio']) && (int)$_POST['anio_inicio'] > 0
    ? (int)$_POST['anio_inicio']
    : (int)date('Y');

$resultado = Importar::alumnos($rows, $idCiclo, $anioInicio);

echo json_encode($resultado);
exit;