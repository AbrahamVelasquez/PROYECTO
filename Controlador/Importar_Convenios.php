<?php

// Controlador/Importar_Convenios.php
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

// ── 3. Mapeo de columnas (plantilla v19) ──────────────────────────────────────
// Col 0: Nº CONV.                          → num_convenio (PK)
// Col 1: NOMBRE DE EMPRESA                 → nombre_empresa
// Col 2: CIF                               → cif
// Col 3: DIRECCION                         → direccion
// Col 4: LOCALIDAD                         → localidad
// Col 5: CP                                → cp
// Col 6: TELEFONO                          → telefono
// Col 7: FAX                               → fax
// Col 8: REPRESENTANTE                     → representante
// Col 9: ESPECIALIDAD                      → especialidad (FK id_ciclo, busca por nombre)
// Col 10: FECHA DE ALTA O RENOVACION CONV  → fecha_alta_renovacion
// Col 11: FECHA NUEVA RENOVACIÓN           → fecha_nueva_renovacion
// Col 12: OBSERVACIONES                    → observaciones

$conn       = Conexion::getConexion();
$insertados = 0;
$omitidos   = 0;
$errores    = [];

// Cache de ciclos (nombre_ciclo + id_curso) → id_ciclo
$ciclosCache = [];
$stmtCiclos  = $conn->query("SELECT c.id_ciclo, c.nombre_ciclo, c.id_curso FROM ciclos c");
foreach ($stmtCiclos->fetchAll(PDO::FETCH_ASSOC) as $c) {
    // Clave: "daw|2", "smr|1", etc.
    $key = strtolower(trim($c['nombre_ciclo'])) . '|' . $c['id_curso'];
    $ciclosCache[$key] = $c['id_ciclo'];
}

// Cache de cursos: número ordinal/texto → id_curso
// Acepta: "1", "2", "primero", "segundo", "1º", "2º", etc.
$cursosMap = [];
$stmtCursos = $conn->query("SELECT id_curso, nombre_curso FROM cursos");
foreach ($stmtCursos->fetchAll(PDO::FETCH_ASSOC) as $cu) {
    $cursosMap[$cu['id_curso']]                           = $cu['id_curso']; // "1"→1
    $cursosMap[strtolower($cu['nombre_curso'])]           = $cu['id_curso']; // "primero"→1
    $cursosMap[$cu['id_curso'] . 'º']                     = $cu['id_curso']; // "1º"→1
    $cursosMap[$cu['id_curso'] . 'o']                     = $cu['id_curso']; // "1o"→1
}

// Resuelve "DAW 2º", "TEAS 2", "SMR Primero", "DAW" → id_ciclo
// Devuelve null si no encuentra coincidencia.
function resolverEspecialidad(string $texto, array $ciclosCache, array $cursosMap): ?int {
    $texto = trim($texto);
    if ($texto === '') return null;

    // Separar última palabra como posible curso
    $partes    = preg_split('/\s+/', $texto);
    $ultimaPal = strtolower(array_pop($partes));       // ej: "2º", "segundo", "2"
    $ultimaNorm = rtrim($ultimaPal, 'º°o');             // quitar sufijo ordinal → "2"

    $idCurso  = $cursosMap[$ultimaPal]  ?? $cursosMap[$ultimaNorm] ?? null;
    $nombreCiclo = strtolower(implode(' ', $partes));   // resto → nombre del ciclo

    if ($idCurso !== null && $nombreCiclo !== '') {
        $key = $nombreCiclo . '|' . $idCurso;
        if (isset($ciclosCache[$key])) return $ciclosCache[$key];
    }

    // Sin curso separado: buscar solo por nombre (coge el primero que coincida)
    $nombreSolo = strtolower($texto);
    foreach ($ciclosCache as $key => $id) {
        [$nom] = explode('|', $key);
        if ($nom === $nombreSolo) return $id;
    }

    return null;
}

// Convierte "DD.MM.YY" o "DD.MM.YYYY" o "DD/MM/YYYY" a "YYYY-MM-DD", o null si vacío
function parsearFecha($valor): ?string {
    $v = trim((string)$valor);
    if ($v === '' || $v === '0') return null;
    // Detectar separador
    $sep = str_contains($v, '.') ? '.' : (str_contains($v, '/') ? '/' : '-');
    $partes = explode($sep, $v);
    if (count($partes) !== 3) return null;
    [$d, $m, $a] = $partes;
    if (strlen($a) === 2) $a = '20' . $a;
    if (!checkdate((int)$m, (int)$d, (int)$a)) return null;
    return sprintf('%04d-%02d-%02d', (int)$a, (int)$m, (int)$d);
}

foreach ($rows as $idx => $row) {
    if ($idx === 0) continue; // saltar cabecera

    $nombre = trim($row[1] ?? '');
    if (empty($nombre)) continue;

    $numConvenio   = trim($row[0]  ?? '') ?: null;
    $cif           = strtoupper(trim($row[2]  ?? '')) ?: null;
    $direccion     = trim($row[3]  ?? '') ?: null;
    $localidad     = trim($row[4]  ?? '') ?: null;
    $cp            = trim($row[5]  ?? '') ?: null;
    $telefono      = trim($row[6]  ?? '') ?: null;
    $fax           = trim($row[7]  ?? '') ?: null;
    $representante = trim($row[8]  ?? '') ?: null;
    $espTexto      = trim($row[9]  ?? '');
    $especialidad  = resolverEspecialidad($espTexto, $ciclosCache, $cursosMap);
    $fechaAlta     = parsearFecha($row[10] ?? '');
    $fechaNueva    = parsearFecha($row[11] ?? '');
    $observaciones = trim($row[12] ?? '') ?: null;

    // Si no viene num_convenio, generamos el siguiente disponible
    if ($numConvenio === null) {
        $stmtMax = $conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
        $stmtMax->execute();
        $numConvenio = (string)((int)$stmtMax->fetchColumn() + 1);
    }

    try {
        $sql = "INSERT INTO convenios 
                    (num_convenio, nombre_empresa, cif, direccion, localidad, cp,
                     telefono, fax, representante, especialidad,
                     fecha_alta_renovacion, fecha_nueva_renovacion, observaciones)
                VALUES 
                    (:num_conv, :nom, :cif, :dir, :loc, :cp,
                     :tel, :fax, :rep, :esp,
                     :fecha_alta, :fecha_nueva, :obs)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
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
        $errores[] = "Fila " . ($idx + 1) . " ($nombre): " . $e->getMessage();
        $omitidos++;
    }
}

echo json_encode([
    'success'    => true,
    'insertados' => $insertados,
    'omitidos'   => $omitidos,
    'errores'    => $errores,
]);
exit;