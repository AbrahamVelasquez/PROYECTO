<?php
// Controlador/Exportar_Alumnos_Word.php

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\JcTable;

validarAcceso('tutor');

// ──────────────────────────────────────────────────────────────────────────────
// 1. RECOGER IDs
// ──────────────────────────────────────────────────────────────────────────────
$ids = $_POST['exportar_ids'] ?? [];
if (empty($ids) || !is_array($ids)) {
    http_response_code(400);
    echo 'No se recibieron IDs de alumnos.';
    exit;
}
$ids = array_map('intval', $ids);

// ──────────────────────────────────────────────────────────────────────────────
// 2. CONSULTAS A BD
// ──────────────────────────────────────────────────────────────────────────────
function obtenerAlumnosWord(array $ids): array {
    try {
        $conn = Conexion::getConexion();
        $ph   = implode(',', array_fill(0, count($ids), '?'));
        $sql  = "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo,
                        asig.id_convenio, asig.horario, asig.num_total_horas, asig.horas_dia,
                        asig.fecha_inicio, asig.fecha_final, asig.nombre_tutor_empresa,
                        conv.nombre_empresa, conv.municipio, conv.direccion
                 FROM alumnos a
                 LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                 LEFT JOIN convenios conv    ON asig.id_convenio = conv.id_convenio
                 WHERE a.id_alumno IN ($ph)
                 ORDER BY a.apellido1 ASC, a.apellido2 ASC, a.nombre ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { return []; }
}

function obtenerDatosTutorWord(): array {
    try {
        $conn = Conexion::getConexion();
        $sql  = "SELECT t.nombre, t.apellidos, t.dni,
                        ci.id_ciclo, ci.nombre_ciclo,
                        cu.nombre_curso
                 FROM tutores t
                 INNER JOIN usuarios u  ON t.id_usuario = u.id_usuario
                 INNER JOIN ciclos ci   ON t.id_ciclo   = ci.id_ciclo
                 INNER JOIN cursos cu   ON ci.id_curso   = cu.id_curso
                 WHERE u.username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION['usuario'] ?? '']);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) { return []; }
}

function marcarAlumnosComoEnviados(array $ids): bool {
    try {
        $conn = Conexion::getConexion();
        $ph   = implode(',', array_fill(0, count($ids), '?'));
        $sql  = "UPDATE asignaciones SET enviado = 1 WHERE id_alumno IN ($ph)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($ids);
    } catch (PDOException $e) { return false; }
}

$alumnos     = obtenerAlumnosWord($ids);
$tutor       = obtenerDatosTutorWord();

if (!empty($alumnos)) {
    marcarAlumnosComoEnviados($ids);
}

// Procesamiento de metadatos
$codigoGrupo = $tutor['id_ciclo'] ?? ($_SESSION['id_ciclo'] ?? '');
$nombreCurso = strtolower(trim($tutor['nombre_curso'] ?? ''));
$abreviatura = match(true) {
    str_contains($nombreCurso, 'primero') => '1º',
    str_contains($nombreCurso, 'segundo') => '2º',
    str_contains($nombreCurso, 'tercero') => '3º',
    default                               => $nombreCurso,
};
$cicloFormativo = strtoupper(trim("$abreviatura {$tutor['nombre_ciclo']}"));
$nombreTutor    = strtoupper(trim(($tutor['nombre'] ?? '') . ' ' . ($tutor['apellidos'] ?? '')));
$dniTutor       = strtoupper($tutor['dni'] ?? '');

// ──────────────────────────────────────────────────────────────────────────────
// 3. HELPERS Y CONFIGURACIÓN DE TABLA
// ──────────────────────────────────────────────────────────────────────────────
function fmtFecha(string $f): string {
    if (empty($f) || $f === '0000-00-00') return '';
    [$y, $m, $d] = explode('-', $f);
    return "$d/$m/" . substr($y, -2);
}

$COLS = [2448, 671, 1489, 1800, 1080, 2340, 900, 900, 1440, 900, 1080, 1396];
$anchuraTotal = array_sum($COLS);

// ──────────────────────────────────────────────────────────────────────────────
// 4. PROCESAR PLANTILLA
// ──────────────────────────────────────────────────────────────────────────────
$rutaPlantilla = __DIR__ . '/../Recursos/Exportar/plantilla_word.docx';
if (!file_exists($rutaPlantilla)) {
    die("Error: No se encuentra la plantilla en Recursos/Exportar/plantilla_word.docx");
}

$templateProcessor = new TemplateProcessor($rutaPlantilla);

// Inyectar metadatos en la plantilla
$templateProcessor->setValue('codigoGrupo', htmlspecialchars($codigoGrupo));
$templateProcessor->setValue('cicloFormativo', htmlspecialchars($cicloFormativo));
$templateProcessor->setValue('nombreTutor', htmlspecialchars($nombreTutor));
$templateProcessor->setValue('dniTutor', htmlspecialchars($dniTutor));

// ──────────────────────────────────────────────────────────────────────────────
// 5. CREAR OBJETO TABLA (CONFIGURACIÓN DE MÁRGENES Y CENTRADO)
// ──────────────────────────────────────────────────────────────────────────────
$table = new Table([
    'unit'             => TblWidth::TWIP,
    'width'            => $anchuraTotal, // Asegúrate de que $COLS sume lo que cabe en el ancho de página
    'alignment'        => JcTable::CENTER, // Esto centra la tabla respecto a los márgenes
    'cellMarginTop'    => 40,
    'cellMarginBottom' => 40,
    'cellMarginLeft'   => 0,
    'cellMarginRight'  => 108,
    'layout'           => 'fixed', // Fuerza a que respete los anchos de columna que definiste
]);

$fuenteHdr = ['name' => 'Courier New', 'size' => 7, 'color' => 'FFFFFF', 'bold' => true];
$fuenteDat = ['name' => 'Courier New', 'size' => 7, 'color' => '000000'];
$parrafo   = ['spaceAfter' => 0, 'spaceBefore' => 0, 'alignment' => 'center'];

// Cabecera con bordes definidos para que se vea "limpia"
$rowHdr = $table->addRow(300); // Un poco más de altura para la cabecera
$cabeceras = [
    'APELLIDOS, NOMBRE ALUMNO', 'SEXO', 'DNI', 'NOMBRE EMPRESA', 'Nº CONVENIO', 
    'DIRECCIÓN CENTRO DE TRABAJO / MUNICIPIO', 'FECHA INICIO', 'FECHA FINAL', 
    'HORARIO', 'Nº TOTAL HORAS', 'DÍAS / Nº HORAS/ DÍA', 'TUTOR EMPRESA'
];

foreach ($cabeceras as $index => $texto) {
    $cell = $rowHdr->addCell($COLS[$index], [
        'bgColor' => '000000', 
        'valign' => 'center',
        'borderSize' => 6, 
        'borderColor' => '000000'
    ]);
    $cell->addText(htmlspecialchars($texto), $fuenteHdr, $parrafo);
}

// Filas de alumnos
foreach ($alumnos as $al) {
    $row = $table->addRow(284);
    $datosFila = [
        trim(($al['apellido1'] ?? '') . ' ' . ($al['apellido2'] ?? '') . ', ' . ($al['nombre'] ?? '')),
        $al['sexo'] ?? '',
        $al['dni'] ?? '',
        $al['nombre_empresa'] ?? '',
        $al['id_convenio'] ? str_pad((string)$al['id_convenio'], 4, '0', STR_PAD_LEFT) : '',
        trim(($al['direccion'] ?? '') . ($al['municipio'] ? ', ' . $al['municipio'] : '')),
        fmtFecha($al['fecha_inicio'] ?? ''),
        fmtFecha($al['fecha_final'] ?? ''),
        $al['horario'] ?? '',
        $al['num_total_horas'] ? $al['num_total_horas'] . 'h' : '',
        $al['horas_dia'] ? $al['horas_dia'] . 'h' : '',
        $al['nombre_tutor_empresa'] ?? ''
    ];

    foreach ($datosFila as $index => $valor) {
        $cell = $row->addCell($COLS[$index], [
            'valign' => 'center', 
            'borderSize' => 6, 
            'borderColor' => '000000'
        ]);
        $align = ($index === 0 || $index === 3 || $index === 5 || $index === 11) ? 'left' : 'center';
        $cell->addText(htmlspecialchars($valor), $fuenteDat, array_merge($parrafo, ['alignment' => $align]));
    }
}

// Relleno hasta 15 filas
$faltantes = max(0, 15 - count($alumnos));
for ($i = 0; $i < $faltantes; $i++) {
    $row = $table->addRow(284);
    foreach ($COLS as $ancho) {
        $row->addCell($ancho, ['borderSize' => 6, 'borderColor' => '000000']);
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// 6. INYECTAR TABLA Y DESCARGAR
// ──────────────────────────────────────────────────────────────────────────────


$templateProcessor->setComplexBlock('tablaAlumnos', $table);

$nombreArchivo = 'RelacionAlumnos_' . date('Ymd') . '.docx';

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

$templateProcessor->saveAs('php://output');
exit;