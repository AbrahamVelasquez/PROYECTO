<?php

// Controlador/Exportar_PF.php
// Instalación requerida (una sola vez en la raíz del proyecto):
//   composer require phpoffice/phpspreadsheet

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

validarAcceso('tutor');

// ──────────────────────────────────────────────────────────────
// HELPERS
// ──────────────────────────────────────────────────────────────

/** Busca la key en col A y escribe value en col B. */
function setValorHoja(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $key, $value): void {
    $maxRow = $ws->getHighestRow();
    for ($row = 1; $row <= $maxRow; $row++) {
        if ($ws->getCell("A{$row}")->getValue() === $key) {
            $ws->getCell("B{$row}")->setValue($value);
            return;
        }
    }
}

/** Formatea fecha ISO (YYYY-MM-DD) → DD/MM/YYYY. */
function fmtFecha(string $fecha): string {
    if (empty($fecha)) return '';
    try { return (new DateTime($fecha))->format('d/m/Y'); }
    catch (Exception $e) { return $fecha; }
}

/** Devuelve los RAs del ciclo del tutor desde la BD. */
function obtenerRAsCiclo(int $idCiclo): array {
    if (!$idCiclo) return [];
    try {
        $conn = Conexion::getConexion();
        $sql  = "SELECT ra.id_ra, ra.id_modulo, ra.numero_ra, ra.impartido_empresa, ra.periodo,
                        m.nombre_modulo
                 FROM resultados_aprendizaje ra
                 JOIN modulos m ON ra.id_modulo = m.id_modulo
                 JOIN plan_estudios pe ON m.id_modulo = pe.id_modulo
                 WHERE pe.id_ciclo = ?
                 ORDER BY ra.periodo ASC, m.nombre_modulo ASC, ra.numero_ra ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idCiclo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { return []; }
}

// ──────────────────────────────────────────────────────────────
// RECOGER DATOS DEL POST
// ──────────────────────────────────────────────────────────────
$p = $_POST;

// Años (2 o 4 dígitos → siempre 2)
$anioIni = (string)($p['anio_inicio'] ?? date('y'));
$anioFin = (string)($p['anio_fin']    ?? (int)date('y') + 1);
if (strlen($anioIni) === 4) $anioIni = substr($anioIni, -2);
if (strlen($anioFin) === 4) $anioFin = substr($anioFin, -2);

// Curso
$idCurso    = (int)($p['curso_selector'] ?? 1);
$cursoTexto = match($idCurso) { 1 => '1º', 2 => '2º', 3 => '3º', default => (string)$idCurso };

// ── Alumno ────────────────────────────────────────────────────
// El campo nombre_completo viene como "APELLIDO1 APELLIDO2, NOMBRE"
// (formato generado por PF_Tabla: apellido1 . " " . apellido2 . ", " . nombre)
$nombreCompleto = trim($p['nombre_completo'] ?? '');
$nomAlu = ''; $ape1Alu = ''; $ape2Alu = '';

if (str_contains($nombreCompleto, ',')) {
    // Formato "APELLIDOS, NOMBRE"
    [$apellidosParte, $nombreParte] = explode(',', $nombreCompleto, 2);
    $nomAlu = strtoupper(trim($nombreParte));
    $apeParts = explode(' ', trim($apellidosParte));
    $ape1Alu  = strtoupper($apeParts[0] ?? '');
    $ape2Alu  = strtoupper($apeParts[1] ?? '');
} else {
    // Formato "NOMBRE APELLIDO1 APELLIDO2"
    $partes  = explode(' ', $nombreCompleto);
    $nomAlu  = strtoupper($partes[0] ?? '');
    $ape1Alu = strtoupper($partes[1] ?? '');
    $ape2Alu = strtoupper($partes[2] ?? '');
}

$apellidosAlu = trim("$ape1Alu $ape2Alu");

// ── Empresa / tutor empresa ───────────────────────────────────
$tutorEmp   = strtoupper(trim($p['tutor_empresa'] ?? ''));
$tutorParts = explode(' ', $tutorEmp, 2);
$tutorNom   = $tutorParts[0] ?? '';
$tutorApe   = $tutorParts[1] ?? '';

// ── Fechas ────────────────────────────────────────────────────
$fechaIni = fmtFecha($p['fecha_inicio'] ?? '');
$fechaFin = fmtFecha($p['fecha_final']  ?? '');

// ── Periodo seleccionado (nuevo select en PF_Edicion) ─────────
$periodoSeleccionado = (string)($p['periodo_planificacion'] ?? '1');

// ── Intervalos ────────────────────────────────────────────────
$interDiario  = !empty($p['inter_diario'])  ? 'Sí' : 'Off';
$interSemanal = !empty($p['inter_semanal']) ? 'Sí' : 'Off';
$interMensual = !empty($p['inter_mensual']) ? 'Sí' : 'Off';
$interOtros   = !empty($p['inter_otros'])   ? 'Sí' : 'Off';
$interVarias  = !empty($p['inter_varias'])  ? 'Sí' : 'Off';

// ── RAs desde BD ─────────────────────────────────────────────
$idCiclo = (int)($_SESSION['id_ciclo'] ?? 0);
$ras     = obtenerRAsCiclo($idCiclo);

// Periodos activos: basados en el select, no en los RAs
$periodo1 = $periodoSeleccionado === '1' ? 'Sí' : 'Off';
$periodo2 = $periodoSeleccionado === '2' ? 'Sí' : 'Off';
$periodo3 = $periodoSeleccionado === '3' ? 'Sí' : 'Off';

// ──────────────────────────────────────────────────────────────
// CARGAR PLANTILLA
// ──────────────────────────────────────────────────────────────
$plantilla = __DIR__ . '/../Recursos/Exportar/plantilla_ffe.xlsx';

if (!file_exists($plantilla)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se encontró la plantilla en Recursos/Exportar/plantilla_ffe.xlsx']);
    exit;
}

$spreadsheet = IOFactory::load($plantilla);

// ──────────────────────────────────────────────────────────────
// HOJA "datos fijos"
// ──────────────────────────────────────────────────────────────
$wsFijos = $spreadsheet->getSheetByName('datos fijos');

setValorHoja($wsFijos, 'anio_inicio', $anioIni);
setValorHoja($wsFijos, 'anio_fin',    $anioFin);
setValorHoja($wsFijos, 'regimen',     $p['regimen'] ?? 'General');

setValorHoja($wsFijos, 'nombre_ciclo', strtoupper($p['nombre_ciclo'] ?? ''));
setValorHoja($wsFijos, 'codigo_ciclo', $p['codigo_ciclo'] ?? '');
setValorHoja($wsFijos, 'grado_ciclo',  'superior');
setValorHoja($wsFijos, 'curso',        $cursoTexto);
setValorHoja($wsFijos, 'cod_curso',    $idCurso);

setValorHoja($wsFijos, 'centro_docente',              $p['centro_nombre']        ?? 'IES CIUDAD ESCOLAR');
setValorHoja($wsFijos, 'email_centro_docente',         $p['centro_correo']        ?? 'ies.ciudadescolar@educa.madrid.org');
setValorHoja($wsFijos, 'telef_centro_docente',         $p['centro_tel']           ?? '917341244');
setValorHoja($wsFijos, 'Tutor_centro_docente',         strtoupper($p['tutor_centro_nombre']  ?? ''));
setValorHoja($wsFijos, 'email_tutor_centro_docente',   $p['tutor_centro_correo']  ?? '');
setValorHoja($wsFijos, 'telef_tutor_centro_docente',   $p['tutor_centro_tel']     ?? '');

// RAs (hasta 14)
for ($i = 1; $i <= 14; $i++) {
    $ra = $ras[$i - 1] ?? null;
    setValorHoja($wsFijos, "num_periodo_mod_ras_{$i}", $ra ? $ra['periodo']       : null);
    setValorHoja($wsFijos, "nombre_modulo_{$i}",        $ra ? $ra['nombre_modulo'] : null);
    setValorHoja($wsFijos, "codigo_modulo_{$i}",         $ra ? $ra['id_modulo']    : null);
    setValorHoja($wsFijos, "listado_ras_{$i}",           $ra ? $ra['numero_ra']    : null);
    setValorHoja($wsFijos, "ras_{$i}_intregro_emp",
        $ra ? ($ra['impartido_empresa'] == 1 ? 'si' : 'no') : null
    );
}

// Periodos según el select
setValorHoja($wsFijos, 'periodo_1º', $periodo1);
setValorHoja($wsFijos, 'periodo_2º', $periodo2);
setValorHoja($wsFijos, 'periodo_3º', $periodo3);

// ──────────────────────────────────────────────────────────────
// HOJA "datos variables"
// ──────────────────────────────────────────────────────────────
$wsVar = $spreadsheet->getSheetByName('datos variables');

// Mapa cabecera → índice de columna
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

$setVar('num_convenio',            $p['id_convenio']    ?? '');
$setVar('num_anexo',               $p['anexo']          ?? '');
$setVar('Alumno',                  trim("$nomAlu $apellidosAlu"));
$setVar('nom_alumno',              $nomAlu);
$setVar('apellidos_alumno',        $apellidosAlu);
$setVar('ape1_alumno',             $ape1Alu);
$setVar('ape2_alumno',             $ape2Alu);
$setVar('email_alumno',            $p['email_alumno']   ?? '');
$setVar('telef_alumno',            $p['tel_alumno']     ?? '');

$setVar('Empresa',                 strtoupper($p['nombre_empresa'] ?? ''));
$setVar('cif_nif_empresa',         strtoupper($p['nif_empresa']    ?? ''));
$setVar('email_empresa',           $p['email_empresa']  ?? '');
$setVar('telef_empresa',           $p['tel_empresa']    ?? '');
$setVar('tutor_empresa',           $tutorEmp);
$setVar('tutor_empresa_nombre',    $tutorNom);
$setVar('tutor_empresa_apellidos', $tutorApe);
$setVar('email_tutor_empresa',     $p['email_tutor_emp'] ?? '');
$setVar('telef_tutor_empresa',     $p['tel_tutor_emp']   ?? '');

$setVar('total_horas',             $p['horas_totales']  ?? '');
$setVar('num_periodo_1',           $periodoSeleccionado);  // ← periodo del select
$setVar('fecha_ini',               $fechaIni);
$setVar('fecha_fin',               $fechaFin);
$setVar('fecha_ini-fecha_fin_1',   ($fechaIni && $fechaFin) ? "$fechaIni - $fechaFin" : '');
$setVar('horario_cada_dia_1',      $p['horario']        ?? '');

// Intervalos (Y → AC)
$setVar('inter_diario',   $interDiario);
$setVar('inter_semanal',  $interSemanal);
$setVar('inter_mensual',  $interMensual);
$setVar('inter_otros',    $interOtros);
$setVar('varias_empresas',$interVarias);

// Medidas
$setVar('adaptaciones?',       (($p['adaptaciones'] ?? 'NO') === 'SI') ? 'si' : 'no');
$setVar('autorizacion_extra?', (($p['autorizacion']  ?? 'NO') === 'SI') ? 'si' : 'no');

// ──────────────────────────────────────────────────────────────
// HOJA "constantes" — NO SE TOCA
// ──────────────────────────────────────────────────────────────

// ──────────────────────────────────────────────────────────────
// ENVIAR COMO DESCARGA
// ──────────────────────────────────────────────────────────────
$ape1Safe      = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $ape1Alu));
$nomSafe       = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nomAlu));
$nombreArchivo = "PlanFormativo_{$ape1Safe}_{$nomSafe}_" . date('Ymd') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;