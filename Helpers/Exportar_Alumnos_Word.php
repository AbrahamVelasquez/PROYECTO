<?php

/**
 * Helpers/Exportar_Alumnos_Word.php — Genera el anexo 11 (tabla resumen de alumnos) en Word
 *
 * Endpoint POST invocado desde el wizard del tutor (step 2 Alumnos).
 * Acepta dos modos según el parámetro POST:
 *   - Selección manual: los IDs de alumno vienen en exportar_ids[] (checkboxes del tutor).
 *   - Exportar todo:    exportar_todo=1 → consulta server-side de todos los alumnos
 *     del ciclo con estado "Completado", sin depender del DOM paginado.
 *
 * Flujo interno:
 *   1. Validar acceso y recoger IDs.
 *   2. Obtener datos de alumnos y tutor vía Exportar.php (modelo).
 *   3. Marcar asignaciones como enviado=1 (cambia el badge de estado).
 *   4. Cargar la plantilla plantilla_word.docx con PhpWord y rellenar variables
 *      de metadatos (código grupo, ciclo, tutor).
 *   5. Construir la tabla de 12 columnas con PhpWord programáticamente
 *      (fila de cabecera fija + filas de alumnos + relleno hasta 15 filas).
 *   6. Inyectar la tabla en el marcador {{tablaAlumnos}} de la plantilla
 *      y enviar el .docx como descarga.
 *
 * formatearHorarioExportacion() y formatearDiasYHorasExportacion() son locales
 * porque el formato Word (letras de días comprimidas + saltos de línea) difiere
 * del formato Excel usado por Exportar::formatearHorario().
 *
 * MVC: Helper de exportación. Toda la BD pasa por Exportar.php; aquí solo vive
 * la lógica de construcción del documento Word.
 */

require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Modelo/Exportar.php';

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\JcTable;

validarAcceso('tutor');

// ──────────────────────────────────────────────────────────────────────────────
// 1. RECOGER IDs
// ──────────────────────────────────────────────────────────────────────────────
$exportarTodo = isset($_POST['exportar_todo']) && $_POST['exportar_todo'] === '1';

if ($exportarTodo) {
    // Exportar todo: consulta server-side para no depender del DOM paginado
    $idCiclo = (int)($_SESSION['id_ciclo'] ?? 0);
    $ids = Exportar::obtenerIdsAlumnosCompletados($idCiclo);
    if (empty($ids)) {
        http_response_code(400);
        echo 'No hay alumnos con estado Completado en este ciclo.';
        exit;
    }
} else {
    $ids = $_POST['exportar_ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        http_response_code(400);
        echo 'No se recibieron IDs de alumnos.';
        exit;
    }
    $ids = array_map('intval', $ids);
}

// ──────────────────────────────────────────────────────────────────────────────
// 2. CONSULTAS A BD VIA MODELO
// ──────────────────────────────────────────────────────────────────────────────
$alumnos = Exportar::obtenerAlumnosWord($ids);
$tutor   = Exportar::obtenerDatosTutorWord($_SESSION['usuario'] ?? '');

if (!empty($alumnos)) {
    Exportar::marcarAlumnosComoEnviados($ids);
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

function formatearHorarioExportacion(array $al): string {
    $excepciones = trim($al['horario_excepciones'] ?? '');
    if (empty($excepciones)) return $al['horario'] ?? '';

    try {
        $bloques = json_decode($excepciones, true);
        if (!is_array($bloques) || empty($bloques)) return $al['horario'] ?? '';

        $ORDEN = ['L'=>0,'M'=>1,'X'=>2,'J'=>3,'V'=>4,'S'=>5,'D'=>6];
        $partes = [];
        foreach ($bloques as $bloque) {
            if (empty($bloque['dias'])) continue;
            $dias = $bloque['dias'];
            usort($dias, fn($a,$b) => $ORDEN[$a] - $ORDEN[$b]);
            $esConsecutivo = true;
            for ($i = 1; $i < count($dias); $i++) {
                if ($ORDEN[$dias[$i]] !== $ORDEN[$dias[$i-1]] + 1) { $esConsecutivo = false; break; }
            }
            $labelDias = (count($dias) > 1 && $esConsecutivo)
                ? $dias[0] . '-' . $dias[count($dias)-1]
                : implode('', $dias);
            $partes[] = $labelDias . ' ' . $bloque['inicio'] . '-' . $bloque['fin'];
        }
        return implode("\n", $partes);
    } catch (Exception $e) {
        return $al['horario'] ?? '';
    }
}

function formatearDiasYHorasExportacion($al) {
    $excepciones = trim($al['horario_excepciones'] ?? '');
    $horasDia = $al['horas_dia'] ?? 0;
    $ORDEN_DIAS = ['L'=>0, 'M'=>1, 'X'=>2, 'J'=>3, 'V'=>4, 'S'=>5, 'D'=>6];
    if (!empty($excepciones)) {
        $bloques = json_decode($excepciones, true);
        if (is_array($bloques) && !empty($bloques)) {
            $partes = [];
            foreach ($bloques as $bloque) {
                if (empty($bloque['dias']) || empty($bloque['inicio']) || empty($bloque['fin'])) continue;
                $dias = $bloque['dias'];
                usort($dias, fn($a, $b) => ($ORDEN_DIAS[$a] ?? 7) - ($ORDEN_DIAS[$b] ?? 7));
                $esConsecutivo = true;
                for ($i = 1; $i < count($dias); $i++) {
                    if (($ORDEN_DIAS[$dias[$i]] ?? 7) !== ($ORDEN_DIAS[$dias[$i-1]] ?? 7) + 1) {
                        $esConsecutivo = false;
                        break;
                    }
                }
                $labelDias = (count($dias) > 1 && $esConsecutivo)
                    ? $dias[0] . '-' . $dias[count($dias)-1]
                    : implode('', $dias);
                try {
                    $inicioDt = DateTime::createFromFormat('H:i', $bloque['inicio']);
                    $finDt    = DateTime::createFromFormat('H:i', $bloque['fin']);
                    if ($inicioDt && $finDt) {
                        $diff = $inicioDt->diff($finDt);
                        $horasBloque = $diff->h + ($diff->i / 60);
                        $partes[] = $labelDias . ' ' . round($horasBloque, 1) . 'h';
                    } else {
                        $partes[] = $labelDias . ' ' . round($horasDia, 1) . 'h';
                    }
                } catch (Exception $e) {
                    $partes[] = $labelDias . ' ' . round($horasDia, 1) . 'h';
                }
            }
            return implode("\n", $partes);
        }
    }
    if ($horasDia > 0) return 'L-V ' . round($horasDia, 1) . 'h';
    return '';
}

// ──────────────────────────────────────────────────────────────────────────────
// 5. CREAR OBJETO TABLA (CONFIGURACIÓN DE MÁRGENES Y CENTRADO)
// ──────────────────────────────────────────────────────────────────────────────
$table = new Table([
    'unit'             => TblWidth::TWIP,
    'width'            => $anchuraTotal, 
    'alignment'        => JcTable::CENTER, 
    'cellMarginTop'    => 40,
    'cellMarginBottom' => 40,
    'cellMarginLeft'   => 0,
    'cellMarginRight'  => 108,
    'layout'           => 'fixed', 
]);

$fuenteHdr = ['name' => 'Courier New', 'size' => 7, 'color' => 'FFFFFF', 'bold' => true];
$fuenteDat = ['name' => 'Courier New', 'size' => 7, 'color' => '000000'];
$parrafo   = ['spaceAfter' => 0, 'spaceBefore' => 0, 'alignment' => 'center'];

// Cabecera con bordes definidos para que se vea "limpia"
$rowHdr = $table->addRow(300); 
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
        $al['num_convenio'] ?? '',
        trim(($al['direccion'] ?? '') . ($al['localidad'] ? ', ' . $al['localidad'] : '')),
        fmtFecha($al['fecha_inicio'] ?? ''),
        fmtFecha($al['fecha_final'] ?? ''),
        formatearHorarioExportacion($al),
        $al['num_total_horas'] ? $al['num_total_horas'] . 'h' : '',
        formatearDiasYHorasExportacion($al),
        $al['nombre_tutor_empresa'] ?? ''
    ];

    foreach ($datosFila as $index => $valor) {
        $cell = $row->addCell($COLS[$index], [
            'valign' => 'center', 
            'borderSize' => 6, 
            'borderColor' => '000000'
        ]);
        $align = ($index === 0 || $index === 3 || $index === 5 || $index === 11) ? 'left' : 'center';
        if (($index === 8 || $index === 10) && strpos($valor, "\n") !== false) {
            $lineas = explode("\n", $valor);
            foreach ($lineas as $linea) {
                $cell->addText(htmlspecialchars($linea), $fuenteDat, array_merge($parrafo, ['alignment' => $align]));
            }
        } else {
            $cell->addText(htmlspecialchars($valor), $fuenteDat, array_merge($parrafo, ['alignment' => $align]));
        }
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

$numCurso = match(true) {
    str_contains($nombreCurso, 'primero') => '1',
    str_contains($nombreCurso, 'segundo') => '2',
    str_contains($nombreCurso, 'tercero') => '3',
    default                               => '1',
};
$cicloSafe  = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($tutor['nombre_ciclo'] ?? 'CICLO'));
$fechaHoy   = date('dmy'); // ej: 270126
$sufijo     = $exportarTodo ? ' - Todos' : '';
$nombreArchivo = "Tabla resumen - Relación de alumnos (anexo 11) - {$numCurso}{$cicloSafe} - {$fechaHoy}{$sufijo}.docx";

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

$templateProcessor->saveAs('php://output');
exit;