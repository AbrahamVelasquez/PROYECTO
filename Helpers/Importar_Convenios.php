<?php

/**
 * Helpers/Importar_Convenios.php — Endpoint AJAX: importar convenios desde Excel
 *
 * Recibe un archivo .xlsx/.xls con el listado de convenios y los inserta en la
 * tabla `convenios`. A diferencia de Importar_Alumnos, toda la lógica de
 * procesamiento vive aquí (no en un modelo separado) porque las reglas de
 * validación y resolución de ciclos son específicas de este flujo.
 *
 * Llamado desde: el botón "Importar Excel" del panel Admin — Convenios Válidos
 * Acción:        index.php?accion=importarConvenios (POST multipart)
 * Responde con:  { success, insertados, omitidos, errores, advertencias }
 *
 * Flujo interno:
 *   1. Valida el archivo recibido (.xlsx/.xls)
 *   2. Lee el Excel como array de filas con PhpSpreadsheet
 *   3. Precarga en caché los ciclos y cursos de BD (evita N queries por fila)
 *   4. Por cada fila: comprueba duplicado → valida campos → resuelve ciclo → inserta
 *   5. Devuelve un resumen con contadores y mensajes de error/advertencia
 *
 * La función resolverEspecialidad() acepta formatos como "DAW 2º", "TEAS 2",
 * "SMR Primero", normalizando la entrada antes de buscar en la caché de ciclos.
 *
 * Seguridad: requiere sesión de admin activa. Solo acepta .xlsx y .xls.
 */

// Invocado desde: index.php?accion=importarConvenios (POST)

require_once __DIR__ . '/../Core/Conexion.php';
require_once __DIR__ . '/../Seguridad/Control_Accesos.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

validarAcceso('admin');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

// ── 1. Validar fichero ────────────────────────────────────────────────────────
if (!isset($_FILES['fichero_convenios']) || $_FILES['fichero_convenios']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No se recibió ningún fichero o hubo un error al subirlo.']);
    exit;
}

$fichero = $_FILES['fichero_convenios'];
$ext     = strtolower(pathinfo($fichero['name'], PATHINFO_EXTENSION));

if (!in_array($ext, ['xlsx', 'xls'])) {
    echo json_encode(['success' => false, 'error' => 'El fichero debe ser .xlsx o .xls.']);
    exit;
}

// ── 2. Leer Excel ─────────────────────────────────────────────────────────────
try {
    $spreadsheet = IOFactory::load($fichero['tmp_name']);
    $ws          = $spreadsheet->getActiveSheet();
    $rows        = $ws->toArray(null, true, true, false);
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'error' => 'No se pudo leer el fichero: ' . $e->getMessage()]);
    exit;
}

// ── 3. Definición de columnas (plantilla v19) ─────────────────────────────────
// Posición → [ etiqueta amigable, ¿obligatoria (NOT NULL en BD)? ]
$COLS = [
    0  => ['Nº Convenio',             false],
    1  => ['Nombre de empresa',       true ],   // NOT NULL
    2  => ['CIF',                     false],
    3  => ['Dirección',               false],
    4  => ['Localidad',               false],
    5  => ['CP',                      false],
    6  => ['Teléfono',                false],
    7  => ['Fax',                     false],
    8  => ['Representante',           false],
    9  => ['Especialidad',            true ],   // NOT NULL (FK ciclos)
    10 => ['Fecha Alta/Renovación',   false],
    11 => ['Fecha Nueva Renovación',  false],
    12 => ['Observaciones',           false],
];

// ── 4. Validar cabecera del Excel ─────────────────────────────────────────────
// Avisamos si columnas obligatorias están ausentes o desplazadas
$headerRow       = $rows[0] ?? [];
$colsFaltantesCab = [];

foreach ($COLS as $pos => $info) {
    [$label, $requerida] = $info;
    if (!$requerida) continue;
    $celda = trim((string)($headerRow[$pos] ?? ''));
    if ($celda === '') {
        $colsFaltantesCab[] = $label . " (columna " . ($pos + 1) . ")";
    }
}

// ── 5. Preparar caché de ciclos y cursos ──────────────────────────────────────
$conn = Conexion::getConexion();

$ciclosCache = [];
$stmtCiclos  = $conn->query("SELECT c.id_ciclo, c.nombre_ciclo, c.id_curso FROM ciclos c");
foreach ($stmtCiclos->fetchAll(PDO::FETCH_ASSOC) as $c) {
    $key = strtolower(trim($c['nombre_ciclo'])) . '|' . $c['id_curso'];
    $ciclosCache[$key] = $c['id_ciclo'];
}

$cursosMap    = [];
$cursosNombres = []; // id_curso → "1º", "2º", etc.
$stmtCursos   = $conn->query("SELECT id_curso, nombre_curso FROM cursos");
foreach ($stmtCursos->fetchAll(PDO::FETCH_ASSOC) as $cu) {
    $cursosMap[$cu['id_curso']]                 = $cu['id_curso'];
    $cursosMap[strtolower($cu['nombre_curso'])] = $cu['id_curso'];
    $cursosMap[$cu['id_curso'] . 'º']           = $cu['id_curso'];
    $cursosMap[$cu['id_curso'] . 'o']           = $cu['id_curso'];
    $cursosNombres[$cu['id_curso']]             = $cu['id_curso'] . 'º';
}

// ── 6. Funciones auxiliares ───────────────────────────────────────────────────

// Resuelve "DAW 2º", "TEAS 2", "SMR Primero" → id_ciclo  o  null
function resolverEspecialidad(string $texto, array $ciclosCache, array $cursosMap): ?int {
    $texto = trim($texto);
    if ($texto === '') return null;

    $partes    = preg_split('/\s+/', $texto);
    $ultimaPal = strtolower(array_pop($partes));
    $ultimaNorm = rtrim($ultimaPal, 'º°o');

    $idCurso     = $cursosMap[$ultimaPal] ?? $cursosMap[$ultimaNorm] ?? null;
    $nombreCiclo = strtolower(implode(' ', $partes));

    if ($idCurso !== null && $nombreCiclo !== '') {
        $key = $nombreCiclo . '|' . $idCurso;
        if (isset($ciclosCache[$key])) return $ciclosCache[$key];
    }

    $nombreSolo = strtolower($texto);
    foreach ($ciclosCache as $key => $id) {
        [$nom] = explode('|', $key);
        if ($nom === $nombreSolo) return $id;
    }

    return null;
}

// Genera mensaje amigable cuando el ciclo tiene datos pero no se encuentra en BD
function mensajeCicloNoEncontrado(string $texto, array $ciclosCache, array $cursosNombres): string {
    $partes      = preg_split('/\s+/', trim($texto));
    $ultimaPal   = count($partes) > 1 ? strtolower(array_pop($partes)) : null;
    $nombreBuscar = strtolower(implode(' ', $partes));
    if ($nombreBuscar === '') $nombreBuscar = strtolower($texto);

    $encontrados = [];
    foreach ($ciclosCache as $key => $id) {
        [$nom, $idCurso] = explode('|', $key);
        if ($nom === $nombreBuscar
            || str_contains($nom, $nombreBuscar)
            || str_contains($nombreBuscar, $nom)) {
            $etiqueta     = $cursosNombres[$idCurso] ?? $idCurso . 'º';
            $encontrados[] = strtoupper($nom) . ' ' . $etiqueta;
        }
    }

    if (!empty($encontrados)) {
        sort($encontrados);
        return "El ciclo «{$texto}» no se encontró. "
             . "Nombre similar en BD: " . implode(', ', $encontrados) . ". "
             . "Revisa el nombre exacto y el curso.";
    }

    return "El ciclo «{$texto}» no existe en la base de datos. "
         . "Revisa el nombre de la especialidad.";
}

// Convierte "DD.MM.YY" / "DD/MM/YYYY" → "YYYY-MM-DD", o null
function parsearFecha($valor): ?string {
    $v = trim((string)$valor);
    if ($v === '' || $v === '0') return null;
    $sep    = str_contains($v, '.') ? '.' : (str_contains($v, '/') ? '/' : '-');
    $partes = explode($sep, $v);
    if (count($partes) !== 3) return null;
    [$d, $m, $a] = $partes;
    if (strlen($a) === 2) $a = '20' . $a;
    if (!checkdate((int)$m, (int)$d, (int)$a)) return null;
    return sprintf('%04d-%02d-%02d', (int)$a, (int)$m, (int)$d);
}

// ── 7. Procesar filas ─────────────────────────────────────────────────────────
$insertados   = 0;
$omitidos     = 0;
$errores      = []; // ciclo con nombre incorrecto (rojo en UI)
$advertencias = []; // campos obligatorios vacíos  (ámbar en UI)

// Advertencia de cabecera (una sola vez, al principio)
if (!empty($colsFaltantesCab)) {
    $advertencias[] = "El Excel parece tener columnas obligatorias ausentes o desplazadas: "
                    . implode(', ', $colsFaltantesCab) . ". "
                    . "Comprueba que estás usando la plantilla correcta.";
}

// Precargar num_convenios existentes (evita N queries dentro del bucle)
$stmtExistentes = $conn->query("SELECT num_convenio FROM convenios");
$conveniosExistentes = array_flip($stmtExistentes->fetchAll(PDO::FETCH_COLUMN));

// Precalcular siguiente num_convenio para no repetir la query en cada fila
$stmtMax = $conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
$stmtMax->execute();
$siguienteNum = (int)$stmtMax->fetchColumn() + 1;

foreach ($rows as $idx => $row) {
    if ($idx === 0) continue; // saltar cabecera

    $nombre = trim($row[1] ?? '');
    if ($nombre === '') continue; // fila completamente vacía

    $numConvenio   = trim($row[0]  ?? '') ?: null;
    $cif           = strtoupper(trim($row[2]  ?? '')) ?: null;
    $direccion     = trim($row[3]  ?? '') ?: null;
    $localidad     = trim($row[4]  ?? '') ?: null;
    $cp            = trim($row[5]  ?? '') ?: null;
    $telefono      = trim($row[6]  ?? '') ?: null;
    $fax           = trim($row[7]  ?? '') ?: null;
    $representante = trim($row[8]  ?? '') ?: null;
    $espTexto      = trim($row[9]  ?? '');
    $fechaAlta     = parsearFecha($row[10] ?? '');
    $fechaNueva    = parsearFecha($row[11] ?? '');
    $observaciones = trim($row[12] ?? '') ?: null;

    // ── 1. Comprobar si ya existe (prioridad máxima) ─────────────────────────
    // Se comprueba ANTES de validar campos para que el admin sepa que ya existe
    // aunque su Excel tenga campos vacíos o el ciclo mal escrito.
    if ($numConvenio !== null && isset($conveniosExistentes[$numConvenio])) {
        $errores[] = "El convenio «{$nombre}» (Nº {$numConvenio}) ya existe en la base de datos y no se ha importado.";
        $omitidos++;
        continue;
    }

    // ── 2. Validar campos obligatorios ────────────────────────────────────────
    $camposFaltantes = [];
    $errorCiclo      = null;
    $especialidad    = null;

    if ($nombre === '') {
        $camposFaltantes[] = 'Nombre de empresa';
    }

    if ($espTexto === '') {
        $camposFaltantes[] = 'Especialidad';
    } else {
        $especialidad = resolverEspecialidad($espTexto, $ciclosCache, $cursosMap);
        if ($especialidad === null) {
            $errorCiclo = mensajeCicloNoEncontrado($espTexto, $ciclosCache, $cursosNombres);
        }
    }

    // ── Acumular mensajes y decidir si insertar ───────────────────────────────
    $hayProblema = !empty($camposFaltantes) || $errorCiclo !== null;

    if (!empty($camposFaltantes)) {
        $advertencias[] = "Datos obligatorios vacíos: " . implode(', ', $camposFaltantes) . ".";
    }
    if ($errorCiclo !== null) {
        $errores[] = $errorCiclo;
    }
    if ($hayProblema) {
        $omitidos++;
        continue;
    }

    // ── Comprobación defensiva final ─────────────────────────────────────────
    // (No debería llegar aquí con campos vacíos, pero lo garantizamos)
    if ($nombre === '' || $especialidad === null) {
        $faltanDef = [];
        if ($nombre === '')       $faltanDef[] = 'Nombre de empresa';
        if ($especialidad === null) $faltanDef[] = 'Especialidad';
        $advertencias[] = "Datos obligatorios vacíos: " . implode(', ', $faltanDef) . ".";
        $omitidos++;
        continue;
    }

    // ── Generar num_convenio si no viene ─────────────────────────────────────
    if ($numConvenio === null) {
        $numConvenio = (string)$siguienteNum++;
    }

    // ── Insertar ──────────────────────────────────────────────────────────────
    try {
        $sql = "INSERT INTO convenios
                    (num_convenio, nombre_empresa, cif, direccion, localidad, cp,
                     telefono, fax, representante, especialidad,
                     fecha_alta_renovacion, fecha_nueva_renovacion, observaciones)
                VALUES
                    (:num_conv, :nom, :cif, :dir, :loc, :cp,
                     :tel, :fax, :rep, :esp,
                     :fecha_alta, :fecha_nueva, :obs)";

        $conn->prepare($sql)->execute([
            ':num_conv'    => $numConvenio,
            ':nom'         => $nombre,
            ':cif'         => $cif,
            ':dir'         => $direccion,
            ':loc'         => $localidad,
            ':cp'          => $cp,
            ':tel'         => $telefono,
            ':fax'         => $fax,
            ':rep'         => $representante,
            ':esp'         => $especialidad,
            ':fecha_alta'  => $fechaAlta,
            ':fecha_nueva' => $fechaNueva,
            ':obs'         => $observaciones,
        ]);
        $insertados++;

    } catch (\PDOException $e) {
        $mysqlCode = (int)($e->errorInfo[1] ?? 0);
        if ($mysqlCode === 1062) {
            // Clave duplicada — el convenio ya existe
            $ref = $numConvenio ? " (Nº {$numConvenio})" : '';
            $errores[] = "El convenio «{$nombre}»{$ref} ya existe en la base de datos y no se ha vuelto a importar.";
        } elseif ($mysqlCode === 1048) {
            // Campo NOT NULL vacío (no debería llegar aquí, pero por si acaso)
            preg_match("/Column '(.+?)' cannot be null/", $e->getMessage(), $m);
            $col = isset($m[1]) ? " («{$m[1]}»)" : '';
            $errores[] = "El convenio «{$nombre}» tiene un campo obligatorio vacío{$col}. Revisa el Excel.";
        } else {
            $errores[] = "No se pudo importar «{$nombre}»: " . $e->getMessage();
        }
        $omitidos++;
    }
}

echo json_encode([
    'success'      => true,
    'insertados'   => $insertados,
    'omitidos'     => $omitidos,
    'errores'      => $errores,
    'advertencias' => $advertencias,
]);
exit;
