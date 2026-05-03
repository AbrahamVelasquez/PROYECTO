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

// ── 3. Mapeo de columnas (según plantilla) ────────────────────────────────────
// Col 0: Nº Convenio (ignorado — es ID de BD)
// Col 1: Nombre de la Empresa
// Col 2: CIF
// Col 3: Dirección
// Col 4: Municipio
// Col 5: CP
// Col 6: País
// Col 7: Teléfono
// Col 8: Fax
// Col 9: Mail
// Col 10: Nombre Representante
// Col 11: DNI Representante
// Col 12: Cargo

$conn       = Conexion::getConexion();
$insertados = 0;
$omitidos   = 0;
$errores    = [];

foreach ($rows as $idx => $row) {
    if ($idx === 0) continue; // saltar cabecera

    $idConvenio        = !empty(trim($row[0] ?? '')) ? (int)trim($row[0]) : null;
    $nombre            = trim($row[1] ?? '');

    // Fila vacía → ignorar
    if (empty($nombre)) continue;

    $cif               = strtoupper(trim($row[2]  ?? ''));
    $direccion         = trim($row[3]  ?? '');
    $municipio         = trim($row[4]  ?? '');
    $cp                = trim($row[5]  ?? '');
    $pais              = trim($row[6]  ?? '') ?: 'España';
    $telefono          = trim($row[7]  ?? '');
    $fax               = trim($row[8]  ?? '');
    $mail              = trim($row[9]  ?? '');
    $nombreRep         = trim($row[10] ?? '');
    $dniRep            = strtoupper(trim($row[11] ?? ''));
    $cargo             = trim($row[12] ?? '');

    try {
        if ($idConvenio) {
            // Si viene ID en el Excel, lo usamos (fuerza el AUTO_INCREMENT a ese valor)
            $sql = "INSERT INTO convenios 
                        (id_convenio, nombre_empresa, cif, direccion, municipio, cp, pais, 
                         telefono, fax, mail, nombre_representante, dni_representante, cargo)
                    VALUES 
                        (:id, :nom, :cif, :dir, :mun, :cp, :pais,
                         :tel, :fax, :mail, :nom_rep, :dni_rep, :cargo)";
            $params = [
                ':id'      => $idConvenio,
                ':nom'     => $nombre,
                ':cif'     => $cif      ?: null,
                ':dir'     => $direccion ?: null,
                ':mun'     => $municipio ?: null,
                ':cp'      => $cp       ?: null,
                ':pais'    => $pais,
                ':tel'     => $telefono ?: null,
                ':fax'     => $fax      ?: null,
                ':mail'    => $mail     ?: null,
                ':nom_rep' => $nombreRep ?: null,
                ':dni_rep' => $dniRep   ?: null,
                ':cargo'   => $cargo    ?: null,
            ];
        } else {
            // Sin ID → AUTO_INCREMENT
            $sql = "INSERT INTO convenios 
                        (nombre_empresa, cif, direccion, municipio, cp, pais, 
                         telefono, fax, mail, nombre_representante, dni_representante, cargo)
                    VALUES 
                        (:nom, :cif, :dir, :mun, :cp, :pais,
                         :tel, :fax, :mail, :nom_rep, :dni_rep, :cargo)";
            $params = [
                ':nom'     => $nombre,
                ':cif'     => $cif      ?: null,
                ':dir'     => $direccion ?: null,
                ':mun'     => $municipio ?: null,
                ':cp'      => $cp       ?: null,
                ':pais'    => $pais,
                ':tel'     => $telefono ?: null,
                ':fax'     => $fax      ?: null,
                ':mail'    => $mail     ?: null,
                ':nom_rep' => $nombreRep ?: null,
                ':dni_rep' => $dniRep   ?: null,
                ':cargo'   => $cargo    ?: null,
            ];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
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