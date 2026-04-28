<?php

// Controlador/Exportar_Alumnos_Word.php
// Invocado desde: index.php?controlador=Tutores&accion=exportarAlumnosWord (POST)

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Table as TableStyle;

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
        $conn  = Conexion::getConexion();
        $ph    = implode(',', array_fill(0, count($ids), '?'));
        $sql   = "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo,
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
        $sql  = "SELECT u.nombre, u.apellidos, u.dni,
                        ci.nombre_ciclo, ci.id_ciclo
                 FROM usuarios u
                 LEFT JOIN ciclos ci ON u.id_ciclo = ci.id_ciclo
                 WHERE u.usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION['usuario'] ?? '']);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) { return []; }
}

$alumnos     = obtenerAlumnosWord($ids);
$tutor       = obtenerDatosTutorWord();
$codigoGrupo = $tutor['id_ciclo']     ?? ($_SESSION['id_ciclo'] ?? '');
$ciclo       = strtoupper($tutor['nombre_ciclo'] ?? '');
$nombreTutor = trim(($tutor['nombre'] ?? '') . ' ' . ($tutor['apellidos'] ?? ''));
$dniTutor    = $tutor['dni'] ?? '';

// ──────────────────────────────────────────────────────────────────────────────
// 3. HELPERS
// ──────────────────────────────────────────────────────────────────────────────
function fmtFecha(string $f): string {
    if (empty($f) || $f === '0000-00-00') return '';
    [$y, $m, $d] = explode('-', $f);
    return "$d/$m/" . substr($y, -2);
}

// Anchuras en twips (= DXA del XML original)
$COLS = [2448, 671, 1489, 1800, 1080, 2340, 900, 900, 1440, 900, 1080, 1396];

// ──────────────────────────────────────────────────────────────────────────────
// 4. DOCUMENTO
// ──────────────────────────────────────────────────────────────────────────────
$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Courier New');
$phpWord->setDefaultFontSize(8);

// Página A4 apaisada
$section = $phpWord->addSection([
    'orientation'  => 'landscape',
    'paperSize'    => 'A4',
    'marginTop'    => 1418,
    'marginBottom' => 997,
    'marginLeft'   => 1616,
    'marginRight'  => 395,
]);

// ── 4a. Línea de metadatos superior ──────────────────────────────────────────
$pMeta = $section->addTextRun([
    'lineHeight'   => 1.5,
    'spaceAfter'   => 0,
]);

$pMeta->addText('CÓDIGO GRUPO:',    ['name' => 'Courier New', 'size' => 9, 'bold' => true]);
$pMeta->addText(" $codigoGrupo    ", ['name' => 'Courier New', 'size' => 9]);
$pMeta->addText('CICLO FORMATIVO:', ['name' => 'Courier New', 'size' => 9, 'bold' => true]);
$pMeta->addText(" $ciclo       ",   ['name' => 'Courier New', 'size' => 9]);
$pMeta->addText('TUTOR/A',          ['name' => 'Courier New', 'size' => 9, 'bold' => true]);
$pMeta->addText(": $nombreTutor       ", ['name' => 'Courier New', 'size' => 9]);
$pMeta->addText('DNI TUTOR/A:',     ['name' => 'Courier New', 'size' => 9, 'bold' => true]);
$pMeta->addText(" $dniTutor",       ['name' => 'Courier New', 'size' => 9]);

$section->addTextBreak(1);

// ── 4b. Estilos de tabla ──────────────────────────────────────────────────────
$anchuraTotal = array_sum($COLS); // 16444 twips

$estiloTabla = [
    'unit'         => TblWidth::TWIP,
    'width'        => $anchuraTotal,
    'alignment'    => JcTable::START,
    'cellMarginTop'    => 0,
    'cellMarginBottom' => 0,
    'cellMarginLeft'   => 108,
    'cellMarginRight'  => 108,
];

$table = $section->addTable($estiloTabla);

// Bordes reutilizables
$bordeNegro  = ['borderSize' => 4, 'borderColor' => '000000'];
$bordeBlanco = ['borderSize' => 4, 'borderColor' => 'FFFFFF'];

// Estilo de párrafo en celda (sin márgenes extra)
$parCelda = ['spaceAfter' => 0, 'spaceBefore' => 0];

// ── Helper: añadir celda de cabecera ─────────────────────────────────────────
$addHdrCell = function(
    \PhpOffice\PhpWord\Element\Row $row,
    string $texto,
    int $ancho,
    string $align = 'center'
) use ($bordeNegro, $bordeBlanco, $parCelda): void {
    $cell = $row->addCell($ancho, [
        'bgColor' => '000000',
        'valign'  => 'center',
        'borderTopSize'     => $bordeNegro['borderSize'],
        'borderTopColor'    => $bordeNegro['borderColor'],
        'borderBottomSize'  => $bordeNegro['borderSize'],
        'borderBottomColor' => $bordeNegro['borderColor'],
        'borderLeftSize'    => $bordeBlanco['borderSize'],
        'borderLeftColor'   => $bordeBlanco['borderColor'],
        'borderRightSize'   => $bordeBlanco['borderSize'],
        'borderRightColor'  => $bordeBlanco['borderColor'],
    ]);
    $cell->addText(
        htmlspecialchars($texto),
        ['name' => 'Courier New', 'size' => 7, 'color' => 'FFFFFF'],
        array_merge($parCelda, ['alignment' => $align])
    );
};

// Primera celda de cabecera tiene borde izquierdo negro
$addHdrCellFirst = function(
    \PhpOffice\PhpWord\Element\Row $row,
    string $texto,
    int $ancho
) use ($bordeNegro, $bordeBlanco, $parCelda): void {
    $cell = $row->addCell($ancho, [
        'bgColor' => '000000',
        'valign'  => 'center',
        'borderTopSize'     => $bordeNegro['borderSize'],
        'borderTopColor'    => $bordeNegro['borderColor'],
        'borderBottomSize'  => $bordeNegro['borderSize'],
        'borderBottomColor' => $bordeNegro['borderColor'],
        'borderLeftSize'    => $bordeNegro['borderSize'],
        'borderLeftColor'   => $bordeNegro['borderColor'],
        'borderRightSize'   => $bordeBlanco['borderSize'],
        'borderRightColor'  => $bordeBlanco['borderColor'],
    ]);
    $cell->addText(
        htmlspecialchars($texto),
        ['name' => 'Courier New', 'size' => 7, 'color' => 'FFFFFF'],
        array_merge($parCelda, ['alignment' => 'center'])
    );
};

// Helper: celda de datos
$addDatCell = function(
    \PhpOffice\PhpWord\Element\Row $row,
    string $texto,
    int $ancho,
    string $align = 'left'
) use ($bordeNegro, $parCelda): void {
    $cell = $row->addCell($ancho, [
        'valign'            => 'center',
        'borderTopSize'     => $bordeNegro['borderSize'],
        'borderTopColor'    => $bordeNegro['borderColor'],
        'borderBottomSize'  => $bordeNegro['borderSize'],
        'borderBottomColor' => $bordeNegro['borderColor'],
        'borderLeftSize'    => $bordeNegro['borderSize'],
        'borderLeftColor'   => $bordeNegro['borderColor'],
        'borderRightSize'   => $bordeNegro['borderSize'],
        'borderRightColor'  => $bordeNegro['borderColor'],
    ]);
    $cell->addText(
        htmlspecialchars($texto),
        ['name' => 'Courier New', 'size' => 7],
        array_merge($parCelda, ['alignment' => $align])
    );
};

// ── 4c. Fila de cabecera ──────────────────────────────────────────────────────
$rowHdr = $table->addRow();
$addHdrCellFirst($rowHdr, 'APELLIDOS, NOMBRE ALUMNO',                  $COLS[0]);
$addHdrCell($rowHdr, 'SEXO',                                           $COLS[1]);
$addHdrCell($rowHdr, 'DNI',                                            $COLS[2]);
$addHdrCell($rowHdr, 'NOMBRE EMPRESA',                                 $COLS[3]);
$addHdrCell($rowHdr, 'Nº CONVENIO',                                    $COLS[4]);
$addHdrCell($rowHdr, 'DIRECCIÓN CENTRO DE TRABAJO/ MUNICIPIO',         $COLS[5]);
$addHdrCell($rowHdr, 'FECHA INICIO',                                   $COLS[6]);
$addHdrCell($rowHdr, 'FECHA FINAL',                                    $COLS[7]);
$addHdrCell($rowHdr, 'HORARIO',                                        $COLS[8]);
$addHdrCell($rowHdr, 'Nº TOTAL HORAS',                                 $COLS[9]);
$addHdrCell($rowHdr, 'DÍAS/ Nº HORAS/DÍA',                            $COLS[10]);
$addHdrCell($rowHdr, 'TUTOR EMPRESA',                                  $COLS[11]);

// ── 4d. Filas de alumnos ──────────────────────────────────────────────────────
$MIN_FILAS = 15;

foreach ($alumnos as $al) {
    $nombre  = trim(($al['apellido1'] ?? '') . ' ' . ($al['apellido2'] ?? '') . ', ' . ($al['nombre'] ?? ''));
    $numConv = $al['id_convenio'] ? str_pad((string)$al['id_convenio'], 4, '0', STR_PAD_LEFT) : '';
    $dir     = trim(($al['direccion'] ?? '') . ($al['municipio'] ? ', ' . $al['municipio'] : ''));
    $hDia    = $al['horas_dia']       ? $al['horas_dia'] . 'h'       : '';
    $hTotal  = $al['num_total_horas'] ? $al['num_total_horas'] . 'h' : '';

    $rowDat = $table->addRow(284);
    $addDatCell($rowDat, $nombre,                          $COLS[0]);
    $addDatCell($rowDat, $al['sexo'] ?? '',                $COLS[1], 'center');
    $addDatCell($rowDat, $al['dni']  ?? '',                $COLS[2], 'center');
    $addDatCell($rowDat, $al['nombre_empresa'] ?? '',      $COLS[3]);
    $addDatCell($rowDat, $numConv,                         $COLS[4], 'center');
    $addDatCell($rowDat, $dir,                             $COLS[5]);
    $addDatCell($rowDat, fmtFecha($al['fecha_inicio'] ?? ''), $COLS[6], 'center');
    $addDatCell($rowDat, fmtFecha($al['fecha_final']  ?? ''), $COLS[7], 'center');
    $addDatCell($rowDat, $al['horario'] ?? '',             $COLS[8]);
    $addDatCell($rowDat, $hTotal,                          $COLS[9],  'center');
    $addDatCell($rowDat, $hDia,                            $COLS[10], 'center');
    $addDatCell($rowDat, $al['nombre_tutor_empresa'] ?? '', $COLS[11]);
}

// ── 4e. Filas vacías de relleno ───────────────────────────────────────────────
$faltantes = max(0, $MIN_FILAS - count($alumnos));
for ($i = 0; $i < $faltantes; $i++) {
    $rowVacia = $table->addRow(284);
    foreach ($COLS as $ancho) {
        $addDatCell($rowVacia, '', $ancho);
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// 5. ENVIAR COMO DESCARGA
// ──────────────────────────────────────────────────────────────────────────────
$nombre = 'RelacionAlumnos_' . date('Ymd') . '.docx';

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');
exit;