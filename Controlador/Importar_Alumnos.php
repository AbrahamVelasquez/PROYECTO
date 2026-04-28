<?php

// Controlador/Importar_Alumnos.php
// Invocado desde: index.php?controlador=Tutores&accion=importarAlumnos (POST)

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

validarAcceso('tutor');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

// ── 1. Validar que llegó el fichero ──────────────────────────────────────────
if (!isset($_FILES['fichero_alumnos']) || $_FILES['fichero_alumnos']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No se recibió ningún fichero o hubo un error al subirlo.']);
    exit;
}

$fichero  = $_FILES['fichero_alumnos'];
$ext      = strtolower(pathinfo($fichero['name'], PATHINFO_EXTENSION));
$idCiclo  = (int)($_SESSION['id_ciclo'] ?? 0);

if (!in_array($ext, ['xlsx', 'xls'])) {
    echo json_encode(['success' => false, 'error' => 'El fichero debe ser .xlsx o .xls.']);
    exit;
}

if (!$idCiclo) {
    echo json_encode(['success' => false, 'error' => 'No se detectó el ciclo formativo en la sesión.']);
    exit;
}

// ── 2. Leer el Excel ─────────────────────────────────────────────────────────
try {
    $spreadsheet = IOFactory::load($fichero['tmp_name']);
    $ws          = $spreadsheet->getActiveSheet();
    $rows        = $ws->toArray(null, true, true, false); // array indexado por 0
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'error' => 'No se pudo leer el fichero: ' . $e->getMessage()]);
    exit;
}

// ── 3. Procesar filas ─────────────────────────────────────────────────────────
// Cabeceras esperadas (fila 0): Nombre | Apellido(s) | Dirección de correo
// Empezamos desde fila 1 (índice 1) saltando la cabecera

$conn       = Conexion::getConexion();
$anioActual = (int)date('Y');

$insertados = 0;
$omitidos   = 0;
$errores    = [];

foreach ($rows as $idx => $row) {
    if ($idx === 0) continue; // saltar cabecera

    $nombre   = trim($row[0] ?? '');
    $apellidos = trim($row[1] ?? '');
    $correo   = trim($row[2] ?? '');

    // Fila vacía → ignorar
    if (empty($nombre) && empty($apellidos)) continue;

    // Separar apellidos: primer espacio separa apellido1 de apellido2
    $partes    = explode(' ', $apellidos, 2);
    $apellido1 = strtoupper(trim($partes[0] ?? ''));
    $apellido2 = strtoupper(trim($partes[1] ?? ''));
    $nombre    = strtoupper($nombre);

    if (empty($apellido1)) {
        $errores[] = "Fila " . ($idx + 1) . ": apellido vacío, omitida.";
        $omitidos++;
        continue;
    }

    try {
        $conn->beginTransaction();

        // Insertar en alumnos (sin DNI ni sexo, quedan NULL)
        $sql1 = "INSERT INTO alumnos (nombre, apellido1, apellido2, correo) 
                 VALUES (:nombre, :apellido1, :apellido2, :correo)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([
            'nombre'    => $nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'correo'    => $correo ?: null,
        ]);

        $lastId = $conn->lastInsertId();

        // Insertar en curso_academico
        $sql2 = "INSERT INTO curso_academico (id_alumno, id_ciclo, anio_inicio, anio_fin)
                 VALUES (:idAlumno, :idCiclo, :inicio, :fin)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([
            'idAlumno' => $lastId,
            'idCiclo'  => $idCiclo,
            'inicio'   => $anioActual,
            'fin'      => $anioActual + 1,
        ]);

        $conn->commit();
        $insertados++;

    } catch (\PDOException $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        $errores[] = "Fila " . ($idx + 1) . " ($nombre $apellido1): " . $e->getMessage();
        $omitidos++;
    }
}

// ── 4. Respuesta ─────────────────────────────────────────────────────────────
echo json_encode([
    'success'    => true,
    'insertados' => $insertados,
    'omitidos'   => $omitidos,
    'errores'    => $errores,
]);
exit;