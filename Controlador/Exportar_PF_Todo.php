<?php

// Controlador/Exportar_PF_Todo.php
// Invocado desde: index.php?controlador=Tutores&accion=exportarTodoPF (POST)
// Recibe: ids_asignacion[] → genera UN SOLO Excel con todos los alumnos en la hoja "datos variables"

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

function formatearHorarioPFT(string $excepciones, string $horarioSimple, string $diasSemana = ''): string {
    $NOMBRES_CORTO = ['L'=>'Lunes','M'=>'Martes','X'=>'Miércoles','J'=>'Jueves','V'=>'Viernes','S'=>'Sábado','D'=>'Domingo'];

    if (empty($excepciones)) {
        if (empty($diasSemana) || empty($horarioSimple)) return $horarioSimple;
        // Convierte "L-V" → "Lunes a Viernes"
        $partesDias = explode('-', $diasSemana);
        if (count($partesDias) === 2) {
            $inicio = $NOMBRES_CORTO[trim($partesDias[0])] ?? trim($partesDias[0]);
            $fin    = $NOMBRES_CORTO[trim($partesDias[1])] ?? trim($partesDias[1]);
            return "$inicio a $fin: $horarioSimple";
        }
        // Si es un solo día (ej: "L")
        $dia = $NOMBRES_CORTO[trim($diasSemana)] ?? $diasSemana;
        return "$dia: $horarioSimple";
    }

    $ORDEN = ['L'=>0,'M'=>1,'X'=>2,'J'=>3,'V'=>4,'S'=>5,'D'=>6];
    $bloques = json_decode($excepciones, true) ?? [];
    $partes  = [];
    foreach ($bloques as $b) {
        if (empty($b['dias'])) continue;
        $dias = $b['dias'];
        usort($dias, fn($a,$b) => $ORDEN[$a] - $ORDEN[$b]);
        $esConsecutivo = true;
        for ($i = 1; $i < count($dias); $i++) {
            if ($ORDEN[$dias[$i]] !== $ORDEN[$dias[$i-1]] + 1) { $esConsecutivo = false; break; }
        }
        $labelDias = (count($dias) > 1 && $esConsecutivo)
            ? $NOMBRES_CORTO[$dias[0]] . ' a ' . $NOMBRES_CORTO[$dias[count($dias)-1]]
            : implode(', ', array_map(fn($d) => $NOMBRES_CORTO[$d], $dias));
        $partes[] = $labelDias . ': ' . $b['inicio'] . '-' . $b['fin'];
    }
    return implode(", ", $partes);
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
                        asig.id_asignacion, asig.num_convenio, asig.horario, asig.horario_excepciones,
                        asig.num_total_horas, asig.horas_dia, asig.dias_semana,
                        asig.fecha_inicio, asig.fecha_final,
                        asig.nombre_tutor_empresa, asig.correo_tutor_empresa, asig.tel_tutor_empresa,
                        conv.nombre_empresa, conv.cif, conv.representante AS email_empresa,
                        conv.telefono AS tel_empresa, conv.direccion, conv.localidad,
                        ci.id_ciclo, ci.nombre_ciclo,
                        cu.id_curso,
                        ca.anio_inicio, ca.anio_fin,
                        f.anexo,
                        t.nombre AS tutor_nombre, t.apellidos AS tutor_apellidos,
                        t.email AS tutor_email, t.telefono AS tutor_tel,
                        cu2.nombre_curso AS nombre_curso_tutor
                 FROM asignaciones asig
                 INNER JOIN alumnos a              ON asig.id_alumno    = a.id_alumno
                 INNER JOIN asignaciones_firmadas f ON asig.id_asignacion = f.id_asignacion
                 LEFT  JOIN convenios conv         ON asig.num_convenio = conv.num_convenio
                 INNER JOIN curso_academico ca     ON a.id_alumno       = ca.id_alumno
                 INNER JOIN ciclos ci              ON ca.id_ciclo       = ci.id_ciclo
                 INNER JOIN cursos cu              ON ci.id_curso       = cu.id_curso
                 LEFT  JOIN tutores t              ON t.id_ciclo        = ci.id_ciclo
                 LEFT  JOIN cursos cu2             ON ci.id_curso       = cu2.id_curso
                 WHERE asig.id_asignacion = ?
                 LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idAsignacion]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) { return null; }
}

// ──────────────────────────────────────────────────────────────────────────────
// 4. MARCAR COMO EXPORTADO SI ESTABA PENDIENTE (exportado = 0)
// ──────────────────────────────────────────────────────────────────────────────
function marcarExportadoSiPendiente(int $idAsignacion): void {
    try {
        $conn = Conexion::getConexion();
        $sql  = "UPDATE asignaciones_firmadas SET exportado = 1
                 WHERE id_asignacion = ? AND exportado = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idAsignacion]);
    } catch (PDOException $e) { /* silencioso */ }
}

// ──────────────────────────────────────────────────────────────────────────────
// 5. CARGAR PLANTILLA
// ──────────────────────────────────────────────────────────────────────────────
$plantilla = __DIR__ . '/../Recursos/Exportar/plantilla_ffe.xlsx';
if (!file_exists($plantilla)) {
    http_response_code(500);
    echo 'Plantilla no encontrada en Recursos/Exportar/plantilla_ffe.xlsx';
    exit;
}

// Obtener datos de todos los alumnos
$todosLosDatos = [];
foreach ($ids as $idAsig) {
    $datos = obtenerDatosAsignacion($idAsig);
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
// 6. RELLENAR HOJA "datos fijos" (datos del ciclo/tutor, iguales para todos)
// ──────────────────────────────────────────────────────────────────────────────
$d0      = $todosLosDatos[0];
$idCiclo = (int)($d0['id_ciclo'] ?? 0);
$ras     = obtenerRAsCicloT($idCiclo);

$anioIni = substr((string)($d0['anio_inicio'] ?? date('Y')), -2);
$anioFin = substr((string)($d0['anio_fin']    ?? date('Y') + 1), -2);

$nombreCurso = strtolower($d0['nombre_curso_tutor'] ?? '');
$abreviatura = match(true) {
    str_contains($nombreCurso, 'primero') => '1º',
    str_contains($nombreCurso, 'segundo') => '2º',
    str_contains($nombreCurso, 'tercero') => '3º',
    default => $nombreCurso,
};

// Periodo dominante de los RAs
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
$wsFijos = $spreadsheet->getSheetByName('datos fijos');

setValorHojaT($wsFijos, 'anio_inicio',  $anioIni);
setValorHojaT($wsFijos, 'anio_fin',     $anioFin);
setValorHojaT($wsFijos, 'regimen',      'General');
setValorHojaT($wsFijos, 'nombre_ciclo', strtoupper($d0['nombre_ciclo'] ?? ''));
setValorHojaT($wsFijos, 'codigo_ciclo', $d0['id_ciclo'] ?? '');
setValorHojaT($wsFijos, 'grado_ciclo',  'superior');
setValorHojaT($wsFijos, 'curso',        $abreviatura);
setValorHojaT($wsFijos, 'cod_curso',    $d0['id_ciclo'] ?? '');

setValorHojaT($wsFijos, 'centro_docente',            'IES CIUDAD ESCOLAR');
setValorHojaT($wsFijos, 'email_centro_docente',       'ies.ciudadescolar@educa.madrid.org');
setValorHojaT($wsFijos, 'telef_centro_docente',       '917341244');
setValorHojaT($wsFijos, 'Tutor_centro_docente',       strtoupper(trim(($d0['tutor_nombre'] ?? '') . ' ' . ($d0['tutor_apellidos'] ?? ''))));
setValorHojaT($wsFijos, 'email_tutor_centro_docente', $d0['tutor_email'] ?? '');
setValorHojaT($wsFijos, 'telef_tutor_centro_docente', $d0['tutor_tel']   ?? '');

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

// ──────────────────────────────────────────────────────────────────────────────
// 7. RELLENAR HOJA "datos variables" — UNA FILA POR ALUMNO (fila 2 en adelante)
// ──────────────────────────────────────────────────────────────────────────────
$wsVar = $spreadsheet->getSheetByName('datos variables');

// Leer cabeceras de la fila 1
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

// Clonar el estilo (bordes, fuente, alineación) de la fila 2 a una fila destino
$copiarEstiloFilaPlantilla = function(int $filaDestino) use ($wsVar, $maxColIdx): void {
    if ($filaDestino === 2) return; // la fila 2 ya tiene estilo de la plantilla
    for ($col = 1; $col <= $maxColIdx; $col++) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $celdaOrigen  = $wsVar->getCell("{$colLetter}2");
        $celdaDestino = $wsVar->getCell("{$colLetter}{$filaDestino}");
        // Clonar border
        $celdaDestino->getStyle()->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
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

$filaActual = 2; // fila 1 = cabeceras

foreach ($todosLosDatos as $d) {

    // Marcar como exportado si aún estaba pendiente
    marcarExportadoSiPendiente((int)$d['id_asignacion']);
    
    // Aplicar estilo de la fila plantilla (fila 2) a esta fila
    $copiarEstiloFilaPlantilla($filaActual);

    $nomAlu  = strtoupper($d['nombre']    ?? '');
    $ape1Alu = strtoupper($d['apellido1'] ?? '');
    $ape2Alu = strtoupper($d['apellido2'] ?? '');

    $tutorEmp   = strtoupper(trim($d['nombre_tutor_empresa'] ?? ''));
    $tutorParts = explode(' ', $tutorEmp, 2);
    $tutorNom   = $tutorParts[0] ?? '';
    $tutorApe   = $tutorParts[1] ?? '';

    $fechaIni = fmtFechaT($d['fecha_inicio'] ?? '');
    $fechaFin = fmtFechaT($d['fecha_final']  ?? '');

    $setVar($filaActual, 'num_convenio',            $d['num_convenio'] ?? '');
    $setVar($filaActual, 'num_anexo',               $d['anexo']        ?? '');
    $setVar($filaActual, 'Alumno',                  trim("$nomAlu $ape1Alu $ape2Alu"));
    $setVar($filaActual, 'nom_alumno',              $nomAlu);
    $setVar($filaActual, 'apellidos_alumno',        trim("$ape1Alu $ape2Alu"));
    $setVar($filaActual, 'ape1_alumno',             $ape1Alu);
    $setVar($filaActual, 'ape2_alumno',             $ape2Alu);
    $setVar($filaActual, 'email_alumno',            $d['correo']   ?? '');
    $setVar($filaActual, 'telef_alumno',            $d['telefono'] ?? '');
    $setVar($filaActual, 'Empresa',                 strtoupper($d['nombre_empresa'] ?? ''));
    $setVar($filaActual, 'cif_nif_empresa',         strtoupper($d['cif']           ?? ''));
    $setVar($filaActual, 'email_empresa',           $d['email_empresa'] ?? '');
    $setVar($filaActual, 'telef_empresa',           $d['tel_empresa']   ?? '');
    $setVar($filaActual, 'tutor_empresa',           $tutorEmp);
    $setVar($filaActual, 'tutor_empresa_nombre',    $tutorNom);
    $setVar($filaActual, 'tutor_empresa_apellidos', $tutorApe);
    $setVar($filaActual, 'email_tutor_empresa',     $d['correo_tutor_empresa'] ?? '');
    $setVar($filaActual, 'telef_tutor_empresa',     $d['tel_tutor_empresa']    ?? '');
    $setVar($filaActual, 'total_horas',             $d['num_total_horas'] ?? '');
    $setVar($filaActual, 'num_periodo_1',           '1');
    $setVar($filaActual, 'fecha_ini',               $fechaIni);
    $setVar($filaActual, 'fecha_fin',               $fechaFin);
    $setVar($filaActual, 'fecha_ini-fecha_fin_1',   ($fechaIni && $fechaFin) ? "$fechaIni - $fechaFin" : '');
    $setVar($filaActual, 'horario_cada_dia_1', formatearHorarioPFT(
        $d['horario_excepciones'] ?? '',
        $d['horario'] ?? '',
        $d['dias_semana'] ?? ''
    ));
    $setVar($filaActual, 'inter_diario',            'Sí');
    $setVar($filaActual, 'inter_semanal',           'Off');   
    $setVar($filaActual, 'inter_mensual',           'Off');   
    $setVar($filaActual, 'inter_otros',             'Off');   
    $setVar($filaActual, 'varias_empresas',         'Off');   
    $setVar($filaActual, 'adaptaciones?',           'no');
    $setVar($filaActual, 'autorizacion_extra?',     'no');

    $filaActual++;
}

// ──────────────────────────────────────────────────────────────────────────────
// 8. ENVIAR COMO DESCARGA
// ──────────────────────────────────────────────────────────────────────────────
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="datos_ffe.xlsx"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
