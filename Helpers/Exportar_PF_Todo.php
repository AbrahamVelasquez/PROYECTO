<?php

/**
 * Helpers/Exportar_PF_Todo.php — Genera un FFE en Excel con múltiples alumnos
 *
 * Variante batch de Exportar_PF.php: recibe un array de ids_asignacion[] en POST
 * y escribe una fila por alumno en la hoja "datos variables", en lugar de una sola.
 *
 * La hoja "datos fijos" se rellena una vez con los metadatos del primer alumno
 * (ciclo, tutor, RAs) — se asume que todos comparten el mismo ciclo y tutor.
 *
 * Para cada fila adicional (filaActual > 2), copiarEstiloFilaPlantilla() clona
 * el estilo de la fila 2 (bordes, fuente, alineación) para que todas las filas
 * tengan el mismo aspecto que la plantilla, ya que PhpSpreadsheet no hereda
 * estilos al insertar filas nuevas.
 *
 * Tras escribir cada fila, llama a Exportar::marcarExportadoSiPendiente() para
 * actualizar el estado en BD solo si aún no estaba exportado (condición AND exportado=0).
 *
 * El fichero de salida siempre se llama datos_ffe.xlsx (nombre genérico para batch).
 *
 * MVC: Helper de exportación batch. Toda la BD pasa por Exportar.php.
 */

require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Modelo/Exportar.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

validarAcceso('tutor');

// ──────────────────────────────────────────────────────────────
// FUNCIÓN PARA FORMATEAR NOMBRES (primera letra mayúscula, resto minúscula)
// ──────────────────────────────────────────────────────────────

function formatearNombreExcel($texto) {
    if (empty($texto)) return '';
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = mb_convert_case($texto, MB_CASE_TITLE, 'UTF-8');
    return $texto;
}

// ──────────────────────────────────────────────────────────────────────────────
// 1. RECOGER IDs
// ──────────────────────────────────────────────────────────────────────────────
$ids = $_POST['ids_asignacion'] ?? [];
if (empty($ids) || !is_array($ids)) {
    http_response_code(400);
    echo 'No se recibieron IDs.';
    exit;
}
$ids = array_map('intval', $ids);

// ──────────────────────────────────────────────────────────────────────────────
// HELPER LOCAL — solo setValorHoja (fmtFecha y formatearHorario viven en Exportar.php)
// ──────────────────────────────────────────────────────────────────────────────

function setValorHoja(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $key, $value): void {
    $maxRow = $ws->getHighestRow();
    for ($row = 1; $row <= $maxRow; $row++) {
        if ($ws->getCell("A{$row}")->getValue() === $key) {
            $ws->getCell("B{$row}")->setValue($value);
            return;
        }
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// 2. CONSULTAS Y PREPARACIÓN DE DATOS (VÍA MODELO)
// ──────────────────────────────────────────────────────────────────────────────
$todosLosDatos = [];
foreach ($ids as $idAsig) {
    $datos = Exportar::obtenerDatosAsignacion($idAsig);
    if ($datos) {
        $todosLosDatos[] = $datos;
    }
}

if (empty($todosLosDatos)) {
    http_response_code(404);
    echo 'No se pudo obtener datos de ninguna asignación.';
    exit;
}

// ──────────────────────────────────────────────────────────────────────────────
// 3. CARGAR PLANTILLA Y RELLENAR HOJA "datos fijos"
// ──────────────────────────────────────────────────────────────────────────────
$plantilla = __DIR__ . '/../Recursos/Exportar/plantilla_ffe.xlsx';
if (!file_exists($plantilla)) {
    http_response_code(500);
    echo 'Plantilla no encontrada en Recursos/Exportar/plantilla_ffe.xlsx';
    exit;
}

$d0      = $todosLosDatos[0];
$idCiclo = (int)($d0['id_ciclo'] ?? 0);
$ras     = Exportar::obtenerRAsCiclo($idCiclo);

$anioIni = substr((string)($d0['anio_inicio'] ?? date('Y')), -2);
$anioFin = substr((string)($d0['anio_fin']    ?? date('Y') + 1), -2);

$nombreCurso = strtolower($d0['nombre_curso_tutor'] ?? '');
$abreviatura = match(true) {
    str_contains($nombreCurso, 'primero') => '1º',
    str_contains($nombreCurso, 'segundo') => '2º',
    str_contains($nombreCurso, 'tercero') => '3º',
    default => $nombreCurso,
};

$contadorPeriodos = [];
foreach ($ras as $ra) {
    $p = (string)$ra['periodo'];
    $contadorPeriodos[$p] = ($contadorPeriodos[$p] ?? 0) + 1;
}
$periodoDominante = '';
if (!empty($contadorPeriodos)) {
    arsort($contadorPeriodos);
    $periodoDominante = array_key_first($contadorPeriodos);
}
$periodosActivos = $periodoDominante !== '' ? [$periodoDominante => true] : [];

$spreadsheet = IOFactory::load($plantilla);
$wsFijos     = $spreadsheet->getSheetByName('datos fijos');

setValorHoja($wsFijos, 'anio_inicio',  $anioIni);
setValorHoja($wsFijos, 'anio_fin',     $anioFin);
setValorHoja($wsFijos, 'regimen',      'General');
setValorHoja($wsFijos, 'nombre_ciclo', strtoupper($d0['nombre_ciclo'] ?? ''));
setValorHoja($wsFijos, 'codigo_ciclo', $d0['id_ciclo'] ?? '');
setValorHoja($wsFijos, 'grado_ciclo',  'superior');
setValorHoja($wsFijos, 'curso',        $abreviatura);
setValorHoja($wsFijos, 'cod_curso',    $d0['id_ciclo'] ?? '');

setValorHoja($wsFijos, 'centro_docente',            'IES CIUDAD ESCOLAR');
setValorHoja($wsFijos, 'email_centro_docente',       'ies.ciudadescolar@educa.madrid.org');
setValorHoja($wsFijos, 'telef_centro_docente',       '917341244');
setValorHoja($wsFijos, 'Tutor_centro_docente',       formatearNombreExcel(trim(($d0['tutor_nombre'] ?? '') . ' ' . ($d0['tutor_apellidos'] ?? ''))));
setValorHoja($wsFijos, 'email_tutor_centro_docente', $d0['tutor_email'] ?? '');
setValorHoja($wsFijos, 'telef_tutor_centro_docente', $d0['tutor_tel']   ?? '');

for ($i = 1; $i <= 14; $i++) {
    $ra = $ras[$i - 1] ?? null;
    setValorHoja($wsFijos, "num_periodo_mod_ras_{$i}", $ra ? $ra['periodo']       : null);
    setValorHoja($wsFijos, "nombre_modulo_{$i}",        $ra ? $ra['nombre_modulo'] : null);
    setValorHoja($wsFijos, "codigo_modulo_{$i}",        $ra ? $ra['id_modulo']     : null);
    setValorHoja($wsFijos, "listado_ras_{$i}",          $ra ? $ra['numero_ra']     : null);
    setValorHoja($wsFijos, "ras_{$i}_intregro_emp",
        $ra ? ($ra['impartido_empresa'] == 1 ? 'si' : 'no') : null
    );
}

setValorHoja($wsFijos, 'periodo_1º', !empty($periodosActivos['1']) ? 'Sí' : 'Off');
setValorHoja($wsFijos, 'periodo_2º', !empty($periodosActivos['2']) ? 'Sí' : 'Off');
setValorHoja($wsFijos, 'periodo_3º', !empty($periodosActivos['3']) ? 'Sí' : 'Off');

// ──────────────────────────────────────────────────────────────────────────────
// 4. RELLENAR HOJA "datos variables"
// ──────────────────────────────────────────────────────────────────────────────
$wsVar     = $spreadsheet->getSheetByName('datos variables');
$maxColStr = $wsVar->getHighestColumn();
$maxColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxColStr);
$headers   = [];
for ($col = 1; $col <= $maxColIdx; $col++) {
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
    $header    = $wsVar->getCell("{$colLetter}1")->getValue();
    if ($header !== null && $header !== '') {
        $headers[$header] = $col;
    }
}

$setVar = function(int $fila, string $name, $value) use ($wsVar, $headers): void {
    if (!isset($headers[$name])) return;
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($headers[$name]);
    $wsVar->getCell("{$colLetter}{$fila}")->setValue($value);
};

$copiarEstiloFilaPlantilla = function(int $filaDestino) use ($wsVar, $maxColIdx): void {
    if ($filaDestino === 2) return;
    for ($col = 1; $col <= $maxColIdx; $col++) {
        $colLetter    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $celdaOrigen  = $wsVar->getCell("{$colLetter}2");
        $celdaDestino = $wsVar->getCell("{$colLetter}{$filaDestino}");
        $celdaDestino->getStyle()->applyFromArray([
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'font'      => [
                'name' => $celdaOrigen->getStyle()->getFont()->getName(),
                'size' => $celdaOrigen->getStyle()->getFont()->getSize(),
            ],
            'alignment' => [
                'horizontal' => $celdaOrigen->getStyle()->getAlignment()->getHorizontal(),
                'vertical'   => $celdaOrigen->getStyle()->getAlignment()->getVertical(),
                'wrapText'   => $celdaOrigen->getStyle()->getAlignment()->getWrapText(),
            ],
        ]);
    }
};

$filaActual = 2;

foreach ($todosLosDatos as $d) {
    Exportar::marcarExportadoSiPendiente((int)$d['id_asignacion']);

    $copiarEstiloFilaPlantilla($filaActual);

    // Formatear nombres para Excel
    $nomAluExcel  = formatearNombreExcel($d['nombre']    ?? '');
    $ape1AluExcel = formatearNombreExcel($d['apellido1'] ?? '');
    $ape2AluExcel = formatearNombreExcel($d['apellido2'] ?? '');
    
    $tutorEmpOriginal = trim($d['nombre_tutor_empresa'] ?? '');
    $tutorParts = explode(' ', $tutorEmpOriginal, 2);
    $tutorNom   = $tutorParts[0] ?? '';
    $tutorApe   = $tutorParts[1] ?? '';
    $tutorNomExcel = formatearNombreExcel($tutorNom);
    $tutorApeExcel = formatearNombreExcel($tutorApe);
    $tutorEmpExcel = trim("$tutorNomExcel $tutorApeExcel");

    $fechaIni = Exportar::fmtFecha($d['fecha_inicio'] ?? '');
    $fechaFin = Exportar::fmtFecha($d['fecha_final']  ?? '');

    $setVar($filaActual, 'num_convenio',            $d['num_convenio'] ?? '');
    $setVar($filaActual, 'num_anexo',               $d['anexo']        ?? '');
    $setVar($filaActual, 'Alumno',                  trim("$nomAluExcel $ape1AluExcel $ape2AluExcel"));
    $setVar($filaActual, 'nom_alumno',              $nomAluExcel);
    $setVar($filaActual, 'apellidos_alumno',        trim("$ape1AluExcel $ape2AluExcel"));
    $setVar($filaActual, 'ape1_alumno',             $ape1AluExcel);
    $setVar($filaActual, 'ape2_alumno',             $ape2AluExcel);
    $setVar($filaActual, 'email_alumno',            $d['correo']   ?? '');
    $setVar($filaActual, 'telef_alumno',            $d['telefono'] ?? '');
    $setVar($filaActual, 'Empresa',                 strtoupper($d['nombre_empresa'] ?? ''));
    $setVar($filaActual, 'cif_nif_empresa',         strtoupper($d['cif']           ?? ''));
    $setVar($filaActual, 'email_empresa',           $d['email_empresa'] ?? '');
    $setVar($filaActual, 'telef_empresa',           $d['tel_empresa']   ?? '');
    $setVar($filaActual, 'tutor_empresa',           $tutorEmpExcel);
    $setVar($filaActual, 'tutor_empresa_nombre',    $tutorNomExcel);
    $setVar($filaActual, 'tutor_empresa_apellidos', $tutorApeExcel);
    $setVar($filaActual, 'email_tutor_empresa',     $d['correo_tutor_empresa'] ?? '');
    $setVar($filaActual, 'telef_tutor_empresa',     $d['tel_tutor_empresa']    ?? '');
    $setVar($filaActual, 'total_horas',             $d['num_total_horas'] ?? '');
    $setVar($filaActual, 'num_periodo_1',           $periodoDominante ?: '1');
    $setVar($filaActual, 'fecha_ini',               $fechaIni);
    $setVar($filaActual, 'fecha_fin',               $fechaFin);
    $setVar($filaActual, 'fecha_ini-fecha_fin_1',   ($fechaIni && $fechaFin) ? "$fechaIni - $fechaFin" : '');
    $setVar($filaActual, 'horario_cada_dia_1', Exportar::formatearHorario(
        $d['horario_excepciones'] ?? '',
        $d['horario']     ?? '',
        $d['dias_semana'] ?? ''
    ));
    $setVar($filaActual, 'inter_diario',        'Sí');
    $setVar($filaActual, 'inter_semanal',       'Off');
    $setVar($filaActual, 'inter_mensual',       'Off');
    $setVar($filaActual, 'inter_otros',         'Off');
    $setVar($filaActual, 'varias_empresas',     'Off');
    $setVar($filaActual, 'adaptaciones?',       'no');
    $setVar($filaActual, 'autorizacion_extra?', 'no');

    $filaActual++;
}

// ──────────────────────────────────────────────────────────────────────────────
// 5. ENVIAR COMO DESCARGA
// ──────────────────────────────────────────────────────────────────────────────
if (ob_get_length()) ob_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="datos_ffe.xlsx"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;