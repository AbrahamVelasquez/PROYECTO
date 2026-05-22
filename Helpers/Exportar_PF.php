<?php

/**
 * Helpers/Exportar_PF.php — Genera el Plan Formativo (FFE) en Excel para un alumno
 *
 * Endpoint POST invocado desde el modal de plan formativo del wizard del tutor.
 * Recibe todos los campos del formulario en $_POST y produce un .xlsx listo
 * para descargar, con el nombre datos_ffe_{apellido1}_{apellido2}_{nombre}.xlsx.
 *
 * La plantilla plantilla_ffe.xlsx tiene dos hojas:
 *   - "datos fijos": campos comunes a todo el grupo (ciclo, tutor, RAs, curso académico).
 *     Se escriben buscando la clave en columna A y poniendo el valor en columna B.
 *   - "datos variables": una fila por alumno con cabeceras en la fila 1.
 *     setVar() localiza la columna por nombre de cabecera y escribe en la fila 2.
 *
 * El periodo de planificación (1º/2º/3º) se calcula automáticamente como el periodo
 * dominante entre los RAs del ciclo (el que aparece más veces), no lo elige el tutor.
 *
 * normalizarParaArchivo() transliteral caracteres con tilde para nombres de archivo
 * sin depender de iconv (falla en algunos entornos Windows con tildes).
 *
 * fmtFecha() y formatearHorario() viven en Exportar.php y se reutilizan aquí.
 * setValorHoja() es local porque solo la necesita este helper y Exportar_PF_Todo.php.
 *
 * MVC: Helper de exportación individual. Toda la BD pasa por Exportar.php.
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

// ──────────────────────────────────────────────────────────────
// HELPER LOCAL — solo setValorHoja (fmtFecha y formatearHorario viven en Exportar.php)
// ──────────────────────────────────────────────────────────────

function setValorHoja(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $key, $value): void {
    $maxRow = $ws->getHighestRow();
    for ($row = 1; $row <= $maxRow; $row++) {
        if ($ws->getCell("A{$row}")->getValue() === $key) {
            $ws->getCell("B{$row}")->setValue($value);
            return;
        }
    }
}

// ──────────────────────────────────────────────────────────────
// RECOGER DATOS DEL POST
// ──────────────────────────────────────────────────────────────
$p = $_POST;

$anioIni = (string)($p['anio_inicio'] ?? date('y'));
$anioFin = (string)($p['anio_fin']    ?? (int)date('y') + 1);
if (strlen($anioIni) === 4) $anioIni = substr($anioIni, -2);
if (strlen($anioFin) === 4) $anioFin = substr($anioFin, -2);

$idCurso    = (int)($p['curso_selector'] ?? 1);
$cursoTexto = match($idCurso) { 1 => '1º', 2 => '2º', 3 => '3º', default => (string)$idCurso };

$nombreCompleto = trim($p['nombre_completo'] ?? '');
$nomAlu = ''; $ape1Alu = ''; $ape2Alu = '';

if (str_contains($nombreCompleto, ',')) {
    [$apellidosParte, $nombreParte] = explode(',', $nombreCompleto, 2);
    $nomAlu   = trim($nombreParte);
    $apeParts = explode(' ', trim($apellidosParte));
    $ape1Alu  = $apeParts[0] ?? '';
    $ape2Alu  = $apeParts[1] ?? '';
} else {
    $partes  = explode(' ', $nombreCompleto);
    $nomAlu  = $partes[0] ?? '';
    $ape1Alu = $partes[1] ?? '';
    $ape2Alu = $partes[2] ?? '';
}
$apellidosAlu = trim("$ape1Alu $ape2Alu");

$tutorEmp   = trim($p['tutor_empresa'] ?? '');
$tutorParts = explode(' ', $tutorEmp, 2);
$tutorNom   = $tutorParts[0] ?? '';
$tutorApe   = $tutorParts[1] ?? '';

// Formatear tutor empresa para Excel
$tutorNomExcel = formatearNombreExcel($tutorNom);
$tutorApeExcel = formatearNombreExcel($tutorApe);
$tutorEmpExcel = trim("$tutorNomExcel $tutorApeExcel");

$fechaIni = Exportar::fmtFecha($p['fecha_inicio'] ?? '');
$fechaFin = Exportar::fmtFecha($p['fecha_final']  ?? '');

// Calcular periodo dominante automáticamente desde los RAs (igual que Exportar_PF_Todo)

$interDiario  = !empty($p['inter_diario'])  ? 'Sí' : 'Off';
$interSemanal = !empty($p['inter_semanal']) ? 'Sí' : 'Off';
$interMensual = !empty($p['inter_mensual']) ? 'Sí' : 'Off';
$interOtros   = !empty($p['inter_otros'])   ? 'Sí' : 'Off';
$interVarias  = !empty($p['inter_varias'])  ? 'Sí' : 'Off';

// ── CONSULTA A BD VÍA MODELO ──────────────────────────────────────────────────
$idCiclo = (int)($_SESSION['id_ciclo'] ?? 0);
$ras     = Exportar::obtenerRAsCiclo($idCiclo);

$periodoSeleccionado = (string)($p['periodo_planificacion'] ?? '1');

$periodo1 = $periodoSeleccionado === '1' ? 'Sí' : 'Off';
$periodo2 = $periodoSeleccionado === '2' ? 'Sí' : 'Off';
$periodo3 = $periodoSeleccionado === '3' ? 'Sí' : 'Off';

// ──────────────────────────────────────────────────────────────
// CARGAR Y RELLENAR PLANTILLA
// ──────────────────────────────────────────────────────────────
$plantilla = __DIR__ . '/../Recursos/Exportar/plantilla_ffe.xlsx';

if (!file_exists($plantilla)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se encontró la plantilla en Recursos/Exportar/plantilla_ffe.xlsx']);
    exit;
}

$spreadsheet = IOFactory::load($plantilla);
$wsFijos     = $spreadsheet->getSheetByName('datos fijos');

setValorHoja($wsFijos, 'anio_inicio', $anioIni);
setValorHoja($wsFijos, 'anio_fin',    $anioFin);
setValorHoja($wsFijos, 'regimen',     $p['regimen'] ?? 'General');

setValorHoja($wsFijos, 'nombre_ciclo', strtoupper($p['nombre_ciclo'] ?? ''));
setValorHoja($wsFijos, 'codigo_ciclo', $p['codigo_ciclo'] ?? '');
setValorHoja($wsFijos, 'grado_ciclo',  'superior');
setValorHoja($wsFijos, 'curso',        $cursoTexto);
setValorHoja($wsFijos, 'cod_curso',    $idCurso);

setValorHoja($wsFijos, 'centro_docente',            $p['centro_nombre']       ?? 'IES CIUDAD ESCOLAR');
setValorHoja($wsFijos, 'email_centro_docente',       $p['centro_correo']       ?? 'ies.ciudadescolar@educa.madrid.org');
setValorHoja($wsFijos, 'telef_centro_docente',       $p['centro_tel']          ?? '917341244');
setValorHoja($wsFijos, 'Tutor_centro_docente',       formatearNombreExcel($p['tutor_centro_nombre'] ?? ''));
setValorHoja($wsFijos, 'email_tutor_centro_docente', $p['tutor_centro_correo'] ?? '');
setValorHoja($wsFijos, 'telef_tutor_centro_docente', $p['tutor_centro_tel']    ?? '');

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

setValorHoja($wsFijos, 'periodo_1º', $periodo1);
setValorHoja($wsFijos, 'periodo_2º', $periodo2);
setValorHoja($wsFijos, 'periodo_3º', $periodo3);

$wsVar     = $spreadsheet->getSheetByName('datos variables');
$headers   = [];
$maxColStr = $wsVar->getHighestColumn();
$maxColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxColStr);
for ($col = 1; $col <= $maxColIdx; $col++) {
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
    $header    = $wsVar->getCell("{$colLetter}1")->getValue();
    if ($header !== null && $header !== '') {
        $headers[$header] = $col;
    }
}

$setVar = function(string $name, $value) use ($wsVar, $headers): void {
    if (!isset($headers[$name])) return;
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($headers[$name]);
    $wsVar->getCell("{$colLetter}2")->setValue($value);
};

// Formatear nombre del alumno para Excel
$nomAluExcel = formatearNombreExcel($nomAlu);
$ape1AluExcel = formatearNombreExcel($ape1Alu);
$ape2AluExcel = formatearNombreExcel($ape2Alu);
$apellidosAluExcel = trim("$ape1AluExcel $ape2AluExcel");

$setVar('num_convenio',            $p['num_convenio']    ?? '');
$setVar('num_anexo',               $p['anexo']           ?? '');
$setVar('Alumno',                  trim("$nomAluExcel $apellidosAluExcel"));
$setVar('nom_alumno',              $nomAluExcel);
$setVar('apellidos_alumno',        $apellidosAluExcel);
$setVar('ape1_alumno',             $ape1AluExcel);
$setVar('ape2_alumno',             $ape2AluExcel);
$setVar('email_alumno',            $p['email_alumno']    ?? '');
$setVar('telef_alumno',            $p['tel_alumno']      ?? '');

$setVar('Empresa',                 strtoupper($p['nombre_empresa'] ?? ''));
$setVar('cif_nif_empresa',         strtoupper($p['nif_empresa']   ?? ''));
$setVar('email_empresa',           $p['email_empresa']   ?? '');
$setVar('telef_empresa',           $p['tel_empresa']     ?? '');
$setVar('tutor_empresa',           $tutorEmpExcel);
$setVar('tutor_empresa_nombre',    $tutorNomExcel);
$setVar('tutor_empresa_apellidos', $tutorApeExcel);
$setVar('email_tutor_empresa',     $p['email_tutor_emp'] ?? '');
$setVar('telef_tutor_empresa',     $p['tel_tutor_emp']   ?? '');

$setVar('total_horas',             $p['horas_totales']   ?? '');
$setVar('num_periodo_1',           $periodoSeleccionado);
$setVar('fecha_ini',               $fechaIni);
$setVar('fecha_fin',               $fechaFin);
$setVar('fecha_ini-fecha_fin_1',   ($fechaIni && $fechaFin) ? "$fechaIni - $fechaFin" : '');

$setVar('horario_cada_dia_1', Exportar::formatearHorario(
    trim($p['horario_excepciones'] ?? ''),
    $p['horario']     ?? '',
    $p['dias_semana'] ?? ''
));

$setVar('inter_diario',        $interDiario);
$setVar('inter_semanal',       $interSemanal);
$setVar('inter_mensual',       $interMensual);
$setVar('inter_otros',         $interOtros);
$setVar('varias_empresas',     $interVarias);
$setVar('adaptaciones?',       (($p['adaptaciones'] ?? 'NO') === 'SI') ? 'si' : 'no');
$setVar('autorizacion_extra?', (($p['autorizacion']  ?? 'NO') === 'SI') ? 'si' : 'no');

// ──────────────────────────────────────────────────────────────
// ENVIAR COMO DESCARGA
// ──────────────────────────────────────────────────────────────

// Transliteración robusta sin depender de iconv (falla con tildes en algunos sistemas)
function normalizarParaArchivo(string $str): string {
    $str = mb_strtolower($str, 'UTF-8');
    $str = strtr($str, [
        'á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
        'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
        'ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o',
        'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
        'ñ'=>'n','ç'=>'c',
    ]);
    return preg_replace('/[^a-z0-9]/', '', $str);
}

$partes = array_filter([
    normalizarParaArchivo($ape1Alu),
    normalizarParaArchivo($ape2Alu),
    normalizarParaArchivo($nomAlu),
]);
$nombreArchivo = 'datos_ffe_' . implode('_', $partes) . '.xlsx';
if (ob_get_length()) ob_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;