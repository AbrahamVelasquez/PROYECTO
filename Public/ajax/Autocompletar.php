<?php

/**
 * Public/ajax/Autocompletar.php
 *
 * Endpoint AJAX — sugerencias para el search bar dropdown.
 * GET ?tipo=convenio&q=...
 * GET ?tipo=alumno&q=...
 */

session_start();

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode([]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$tipo = $_GET['tipo'] ?? '';
$q    = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit();
}

// Public/ajax/ → dos niveles hasta la raíz del proyecto
require_once __DIR__ . '/../../Core/Conexion.php';

try {
    $conn  = Conexion::getConexion();
    $param = '%' . $q . '%';

    if ($tipo === 'convenio') {
        $stmt = $conn->prepare(
            "SELECT num_convenio, nombre_empresa, cif, localidad
               FROM convenios
              WHERE nombre_empresa LIKE :p OR cif LIKE :p2
           ORDER BY nombre_empresa
              LIMIT 10"
        );
        $stmt->execute([':p' => $param, ':p2' => $param]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sugerencias = array_map(fn($f) => [
            'valor'    => $f['nombre_empresa'],
            'etiqueta' => $f['nombre_empresa'],
            'sublabel' => $f['cif'] . ' · ' . $f['localidad'],
        ], $filas);

    } elseif ($tipo === 'alumno') {
        $idCiclo = $_SESSION['id_ciclo'] ?? null;
        if (!$idCiclo) { echo json_encode([]); exit(); }

        $stmt = $conn->prepare(
            "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni
               FROM alumnos a
               JOIN curso_academico ca ON a.id_alumno = ca.id_alumno
              WHERE ca.id_ciclo = :ciclo
                AND (
                      CONCAT(a.apellido1, ' ', a.apellido2, ', ', a.nombre) LIKE :p
                   OR a.dni  LIKE :p2
                   OR a.nombre   LIKE :p3
                   OR a.apellido1 LIKE :p4
                   OR a.apellido2 LIKE :p5
                )
           ORDER BY a.apellido1, a.apellido2, a.nombre
              LIMIT 10"
        );
        $stmt->execute([
            ':ciclo' => $idCiclo,
            ':p'     => $param, ':p2' => $param,
            ':p3'    => $param, ':p4' => $param, ':p5' => $param,
        ]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sugerencias = array_map(fn($f) => [
            'valor'    => $f['apellido1'] . ' ' . $f['apellido2'] . ', ' . $f['nombre'],
            'etiqueta' => $f['apellido1'] . ' ' . $f['apellido2'] . ', ' . $f['nombre'],
            'sublabel' => $f['dni'] ?? 'Sin DNI',
        ], $filas);

    } elseif ($tipo === 'tutor') {
        $stmt = $conn->prepare(
            "SELECT t.id_tutor, t.nombre, t.apellidos, t.dni
               FROM tutores t
              WHERE t.nombre LIKE :p OR t.apellidos LIKE :p2 OR t.dni LIKE :p3
           ORDER BY t.apellidos, t.nombre
              LIMIT 10"
        );
        $stmt->execute([':p' => $param, ':p2' => $param, ':p3' => $param]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sugerencias = array_map(fn($f) => [
            'valor'    => $f['apellidos'] . ' ' . $f['nombre'],
            'etiqueta' => $f['apellidos'] . ' ' . $f['nombre'],
            'sublabel' => $f['dni'] ?? '',
        ], $filas);

    } else {
        $sugerencias = [];
    }

    echo json_encode($sugerencias);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([]);
}
