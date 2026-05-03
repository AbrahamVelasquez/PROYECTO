<?php

// Controlador/Exportar_PF_Todo.php
// Invocado desde: index.php?controlador=Tutores&accion=exportarTodoPF (POST)
// Recibe: ids_asignacion[] → genera un Excel por cada uno y los devuelve en ZIP

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

validarAcceso('tutor');

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
// 2. HELPERS
// ──────────────────────────────────────────────────────────────────────────────
function setValorHojaT(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $key, $value): void {
    $maxRow = $ws->getHighestRow();
    for ($row = 1; $row <= $maxRow; $row++) {
        if ($ws->getCell("A{$row}")->getValue() === $key) {
            $ws->getCell("B{$row}")->setValue($value);
            return;
        }
    }
}

function fmtFechaT(string $fecha): string {
    if (empty($fecha)) return '';
    try { return (new DateTime($fecha))->format('d/m/Y'); }
    catch (Exception $e) { return $fecha; }
}

function obtenerRAsCicloT(int $idCiclo): array {
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

// ──────────────────────────────────────────────────────────────────────────────
// 3. OBTENER DATOS DE CADA ASIGNACIÓN
// ──────────────────────────────────────────────────────────────────────────────
function obtenerDatosAsignacion(int $idAsignacion): ?array {
    try {
        $conn = Conexion::getConexion();
        $sql  = "SELECT a.nombre, a.apellido1, a.apellido2, a.correo, a.telefono,
                        asig.id_asignacion, asig.id_convenio, asig.horario,
                        asig.num_total_horas, asig.horas_dia,
                        asig.fecha_inicio, asig.fecha_final,
                        asig.nombre_tutor_empresa, asig.correo_tutor_empresa, asig.tel_tutor_empresa,
                        conv.nombre_empresa, conv.cif, conv.mail AS email_empresa,
                        conv.telefono AS tel_empresa, conv.direccion, conv.municipio,
                        ci.id_ciclo, ci.nombre_ciclo,
                        cu.id_curso,
                        ca.anio_inicio, ca.anio_fin,
                        f.anexo,
                        t.nombre AS tutor_nombre, t.apellidos AS tutor_apellidos,
                        t.email AS tutor_email, t.telefono AS tutor_tel,
                        cu2.nombre_curso AS nombre_curso_tutor
                 FROM asignaciones asig
                 INNER JOIN alumnos a         ON asig.id_alumno    = a.id_alumno
                 INNER JOIN asignaciones_firmadas f ON asig.id_asignacion = f.id_asignacion
                 LEFT  JOIN convenios conv    ON asig.id_convenio  = conv.id_convenio
                 INNER JOIN curso_academico ca ON a.id_alumno      = ca.id_alumno
                 INNER JOIN ciclos ci          ON ca.id_ciclo       = ci.id_ciclo
                 INNER JOIN cursos cu          ON ci.id_curso       = cu.id_curso
                 LEFT  JOIN tutores t          ON t.id_ciclo        = ci.id_ciclo
                 LEFT  JOIN cursos cu2         ON ci.id_curso       = cu2.id_curso
                 WHERE asig.id_asignacion = ?
                 LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idAsignacion]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) { return null; }
}

// ──────────────────────────────────────────────────────────────────────────────
// 4. GENERAR UN EXCEL PARA UNA ASIGNACIÓN
// ──────────────────────────────────────────────────────────────────────────────
function generarExcelAsignacion(array $d, string $plantilla): string {
    $spreadsheet = IOFactory::load($plantilla);

    // Años
    $anioIni = substr((string)($d['anio_inicio'] ?? date('Y')), -2);
    $anioFin = substr((string)($d['anio_fin']    ?? date('Y') + 1), -2);

    // Curso
    $nombreCurso = strtolower($d['nombre_curso_tutor'] ?? '');
    $abreviatura = match(true) {
        str_contains($nombreCurso, 'primero') => '1º',
        str_contains($nombreCurso, 'segundo') => '2º',
        str_contains($nombreCurso, 'tercero') => '3º',
        default => $nombreCurso,
    };
    $cursoTexto = $abreviatura;
    $idCurso    = (int)($d['id_curso'] ?? 1);

    // Alumno
    $nomAlu  = strtoupper($d['nombre']    ?? '');
    $ape1Alu = strtoupper($d['apellido1'] ?? '');
    $ape2Alu = strtoupper($d['apellido2'] ?? '');

    // Tutor empresa
    $tutorEmp   = strtoupper(trim($d['nombre_tutor_empresa'] ?? ''));
    $tutorParts = explode(' ', $tutorEmp, 2);
    $tutorNom   = $tutorParts[0] ?? '';
    $tutorApe   = $tutorParts[1] ?? '';

    $fechaIni = fmtFechaT($d['fecha_inicio'] ?? '');
    $fechaFin = fmtFechaT($d['fecha_final']  ?? '');

    $idCiclo = (int)($d['id_ciclo'] ?? 0);
    $ras     = obtenerRAsCicloT($idCiclo);

    // Deducir el periodo dominante de los RAs (el más frecuente)
    $contadorPeriodos = [];
    foreach ($ras as $ra) {
        $p = (string)$ra['periodo'];
        $contadorPeriodos[$p] = ($contadorPeriodos[$p] ?? 0) + 1;
    }

    // El periodo con más RAs es el seleccionado, el resto van a Off
    $periodoDominante = '';
    if (!empty($contadorPeriodos)) {
        arsort($contadorPeriodos);
        $periodoDominante = array_key_first($contadorPeriodos);
    }

    $periodosActivos = [];
    if ($periodoDominante !== '') {
        $periodosActivos[$periodoDominante] = true;
    }

    // ── Hoja datos fijos ──────────────────────────────────────────────────────
    $wsFijos = $spreadsheet->getSheetByName('datos fijos');
    setValorHojaT($wsFijos, 'anio_inicio', $anioIni);
    setValorHojaT($wsFijos, 'anio_fin',    $anioFin);
    setValorHojaT($wsFijos, 'regimen',     'General');
    setValorHojaT($wsFijos, 'nombre_ciclo', strtoupper($d['nombre_ciclo'] ?? ''));
    setValorHojaT($wsFijos, 'codigo_ciclo', $d['id_ciclo'] ?? '');
    setValorHojaT($wsFijos, 'grado_ciclo',  'superior');
    setValorHojaT($wsFijos, 'curso',        $cursoTexto);
    setValorHojaT($wsFijos, 'cod_curso',    $d['id_ciclo'] ?? '');

    setValorHojaT($wsFijos, 'centro_docente',              'IES CIUDAD ESCOLAR');
    setValorHojaT($wsFijos, 'email_centro_docente',         'ies.ciudadescolar@educa.madrid.org');
    setValorHojaT($wsFijos, 'telef_centro_docente',         '917341244');
    setValorHojaT($wsFijos, 'Tutor_centro_docente',         strtoupper(trim(($d['tutor_nombre'] ?? '') . ' ' . ($d['tutor_apellidos'] ?? ''))));
    setValorHojaT($wsFijos, 'email_tutor_centro_docente',   $d['tutor_email'] ?? '');
    setValorHojaT($wsFijos, 'telef_tutor_centro_docente',   $d['tutor_tel']   ?? '');

    for ($i = 1; $i <= 14; $i++) {
        $ra = $ras[$i - 1] ?? null;
        setValorHojaT($wsFijos, "num_periodo_mod_ras_{$i}", $ra ? $ra['periodo']       : null);
        setValorHojaT($wsFijos, "nombre_modulo_{$i}",        $ra ? $ra['nombre_modulo'] : null);
        setValorHojaT($wsFijos, "codigo_modulo_{$i}",         $ra ? $ra['id_modulo']    : null);
        setValorHojaT($wsFijos, "listado_ras_{$i}",           $ra ? $ra['numero_ra']    : null);
        setValorHojaT($wsFijos, "ras_{$i}_intregro_emp",
            $ra ? ($ra['impartido_empresa'] == 1 ? 'si' : 'no') : null
        );
    }

    setValorHojaT($wsFijos, 'periodo_1º', !empty($periodosActivos['1']) ? 'Sí' : 'Off');
    setValorHojaT($wsFijos, 'periodo_2º', !empty($periodosActivos['2']) ? 'Sí' : 'Off');
    setValorHojaT($wsFijos, 'periodo_3º', !empty($periodosActivos['3']) ? 'Sí' : 'Off');

    // ── Hoja datos variables ──────────────────────────────────────────────────
    $wsVar     = $spreadsheet->getSheetByName('datos variables');
    $maxColStr = $wsVar->getHighestColumn();
    $maxColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxColStr);
    $headers   = [];
    for ($col = 1; $col <= $maxColIdx; $col++) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $header    = $wsVar->getCell("{$colLetter}1")->getValue();
        if ($header !== null && $header !== '') $headers[$header] = $col;
    }

    $setVar = function(string $name, $value) use ($wsVar, $headers): void {
        if (!isset($headers[$name])) return;
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($headers[$name]);
        $wsVar->getCell("{$colLetter}2")->setValue($value);
    };

    $setVar('num_convenio',            $d['id_convenio']  ?? '');
    $setVar('num_anexo',               $d['anexo']        ?? '');
    $setVar('Alumno',                  trim("$nomAlu $ape1Alu $ape2Alu"));
    $setVar('nom_alumno',              $nomAlu);
    $setVar('apellidos_alumno',        trim("$ape1Alu $ape2Alu"));
    $setVar('ape1_alumno',             $ape1Alu);
    $setVar('ape2_alumno',             $ape2Alu);
    $setVar('email_alumno',            $d['correo']   ?? '');
    $setVar('telef_alumno',            $d['telefono'] ?? '');
    $setVar('Empresa',                 strtoupper($d['nombre_empresa'] ?? ''));
    $setVar('cif_nif_empresa',         strtoupper($d['cif']           ?? ''));
    $setVar('email_empresa',           $d['email_empresa'] ?? '');
    $setVar('telef_empresa',           $d['tel_empresa']   ?? '');
    $setVar('tutor_empresa',           $tutorEmp);
    $setVar('tutor_empresa_nombre',    $tutorNom);
    $setVar('tutor_empresa_apellidos', $tutorApe);
    $setVar('email_tutor_empresa',     $d['correo_tutor_empresa'] ?? '');
    $setVar('telef_tutor_empresa',     $d['tel_tutor_empresa']    ?? '');
    $setVar('total_horas',             $d['num_total_horas'] ?? '');
    $setVar('num_periodo_1',           '1');
    $setVar('fecha_ini',               $fechaIni);
    $setVar('fecha_fin',               $fechaFin);
    $setVar('fecha_ini-fecha_fin_1',   ($fechaIni && $fechaFin) ? "$fechaIni - $fechaFin" : '');
    $setVar('horario_cada_dia_1',      $d['horario'] ?? '');
    $setVar('inter_diario',            'Sí');
    $setVar('adaptaciones?',           'no');
    $setVar('autorizacion_extra?',     'no');

    // Guardar en temporal
    $tmpFile = tempnam(sys_get_temp_dir(), 'pf_') . '.xlsx';
    $writer  = new Xlsx($spreadsheet);
    $writer->save($tmpFile);
    return $tmpFile;
}

// ──────────────────────────────────────────────────────────────────────────────
// 5. CONSTRUIR Y ENVIAR
// ──────────────────────────────────────────────────────────────────────────────
$plantilla = __DIR__ . '/../Recursos/Exportar/plantilla_ffe.xlsx';
if (!file_exists($plantilla)) {
    http_response_code(500);
    echo 'Plantilla no encontrada en Recursos/Exportar/plantilla_ffe.xlsx';
    exit;
}

$archivosGenerados = []; // ['nombre_archivo' => 'ruta_tmp']

foreach ($ids as $idAsig) {
    $datos = obtenerDatosAsignacion($idAsig);
    if (!$datos) continue;

    $tmpPath  = generarExcelAsignacion($datos, $plantilla);
    $ape1Safe = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', strtoupper($datos['apellido1'] ?? 'ALU')));
    $nomSafe  = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', strtoupper($datos['nombre']    ?? '')));
    $nombreArchivo = "PlanFormativo_{$ape1Safe}_{$nomSafe}.xlsx";

    $archivosGenerados[$nombreArchivo] = $tmpPath;
}

if (empty($archivosGenerados)) {
    http_response_code(404);
    echo 'No se pudo generar ningún plan formativo.';
    exit;
}

// ── Un solo fichero → descarga directa ───────────────────────────────────────
if (count($archivosGenerados) === 1) {
    $nombre = array_key_first($archivosGenerados);
    $ruta   = $archivosGenerados[$nombre];

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $nombre . '"');
    header('Content-Length: ' . filesize($ruta));
    header('Cache-Control: no-cache');
    readfile($ruta);
    unlink($ruta);
    exit;
}

// ── Varios ficheros → ZIP ─────────────────────────────────────────────────────
$zipTmp = tempnam(sys_get_temp_dir(), 'planes_') . '.zip';
$zip    = new ZipArchive();
$zip->open($zipTmp, ZipArchive::CREATE | ZipArchive::OVERWRITE);

foreach ($archivosGenerados as $nombre => $ruta) {
    $zip->addFile($ruta, $nombre);
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="PlanesFormativos_' . date('Ymd') . '.zip"');
header('Content-Length: ' . filesize($zipTmp));
header('Cache-Control: no-cache');
readfile($zipTmp);

// Limpiar temporales
unlink($zipTmp);
foreach ($archivosGenerados as $ruta) {
    if (file_exists($ruta)) unlink($ruta);
}
exit;