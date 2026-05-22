<?php

/**
 * Modelo/Importar.php — Inserción masiva desde Excel en la base de datos
 *
 * Recibe los datos ya parseados como array de filas (por los helpers de importación)
 * y los inserta fila a fila con validación básica y manejo de errores por fila.
 *
 * Dos métodos estáticos según el tipo de importación:
 *   - convenios(): inserta en tabla `convenios`. Resuelve el texto de especialidad
 *     (ej: "DAW 2º") a un id_ciclo usando caché de BD para evitar N queries.
 *   - alumnos(): inserta en `alumnos` + `curso_academico` en transacción por fila.
 *     Separa automáticamente el apellido compuesto en apellido1/apellido2.
 *
 * Ambos métodos devuelven un resumen con contadores y array de errores por fila
 * para que el frontend pueda informar al usuario del resultado sin recargar la página.
 *
 * El num_convenio se precalcula antes del bucle para evitar una query MAX() por fila.
 * Si la fila no trae num_convenio, se asigna el siguiente disponible.
 *
 * MVC: Modelo de importación. Separa la lectura del Excel (helpers) de la inserción
 * en BD (este modelo) para que la lógica de BD no dependa de PhpSpreadsheet.
 */

require_once __DIR__ . '/../Core/Conexion.php';

class Importar {

    /**
     * Inserta filas de convenios en la tabla `convenios`.
     * Precarga ciclos y cursos en caché al inicio para no repetir queries en cada fila.
     * El primer elemento del array ($rows[0]) se considera cabecera y se salta siempre.
     */
    public static function convenios(array $rows): array {
        $conn       = Conexion::getConexion();
        $insertados = 0;
        $omitidos   = 0;
        $errores    = [];

        // Cache de ciclos (nombre_ciclo + id_curso) → id_ciclo
        $ciclosCache = [];
        $stmtCiclos  = $conn->query("SELECT c.id_ciclo, c.nombre_ciclo, c.id_curso FROM ciclos c");
        foreach ($stmtCiclos->fetchAll(PDO::FETCH_ASSOC) as $c) {
            $key = strtolower(trim($c['nombre_ciclo'])) . '|' . $c['id_curso'];
            $ciclosCache[$key] = $c['id_ciclo'];
        }

        // Cache de cursos
        $cursosMap = [];
        $stmtCursos = $conn->query("SELECT id_curso, nombre_curso FROM cursos");
        foreach ($stmtCursos->fetchAll(PDO::FETCH_ASSOC) as $cu) {
            $cursosMap[$cu['id_curso']]                 = $cu['id_curso'];
            $cursosMap[strtolower($cu['nombre_curso'])] = $cu['id_curso'];
            $cursosMap[$cu['id_curso'] . 'º']           = $cu['id_curso'];
            $cursosMap[$cu['id_curso'] . 'o']           = $cu['id_curso'];
        }

        // Precalcular el siguiente num_convenio disponible (evita N queries dentro del bucle)
        $stmtMax = $conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
        $stmtMax->execute();
        $siguienteNum = (int)$stmtMax->fetchColumn() + 1;

        foreach ($rows as $idx => $row) {
            if ($idx === 0) continue; // Saltar cabecera

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
            $especialidad  = self::resolverEspecialidad($espTexto, $ciclosCache, $cursosMap);
            $fechaAlta     = self::parsearFecha($row[10] ?? '');
            $fechaNueva    = self::parsearFecha($row[11] ?? '');
            $observaciones = trim($row[12] ?? '') ?: null;

            // Si no viene num_convenio, usamos el contador precalculado antes del bucle
            if ($numConvenio === null) {
                $numConvenio = (string)$siguienteNum++;
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

        return [
            'success'    => true,
            'insertados' => $insertados,
            'omitidos'   => $omitidos,
            'errores'    => $errores
        ];
    }

    /**
     * Procesa e importa el array de filas de alumnos en la base de datos
     */
    public static function alumnos(array $rows, int $idCiclo, int $anioInicio = 0): array {
        $conn       = Conexion::getConexion();
        $anioInicio = $anioInicio > 0 ? $anioInicio : (int)date('Y');
        $insertados = 0;
        $omitidos   = 0;
        $errores    = [];

        // Precalcular el siguiente num_convenio disponible (evita N queries dentro del bucle)
        $stmtMax = $conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
        $stmtMax->execute();
        $siguienteNum = (int)$stmtMax->fetchColumn() + 1;

        foreach ($rows as $idx => $row) {
            if ($idx === 0) continue; // Saltar cabecera

            $nombre   = trim($row[0] ?? '');
            $apellidos = trim($row[1] ?? '');
            $correo   = trim($row[2] ?? '');

            if (empty($nombre) && empty($apellidos)) continue;

            // Separar apellidos
            $partes    = explode(' ', $apellidos, 2);
            $apellido1 = trim($partes[0] ?? '');
            $apellido2 = trim($partes[1] ?? '');
            $nombre    = trim($nombre);

            if (empty($apellido1)) {
                $errores[] = "Fila " . ($idx + 1) . ": apellido vacío, omitida.";
                $omitidos++;
                continue;
            }

            try {
                $conn->beginTransaction();

                // Insertar en alumnos
                $sql1 = "INSERT INTO alumnos (nombre, apellido1, apellido2, correo) 
                         VALUES (:nombre, :apellido1, :apellido2, :correo)";
                $stmt1 = $conn->prepare($sql1);
                $stmt1->execute([
                    'nombre'    => $nombre,
                    'apellido1' => $apellido1,
                    'apellido2' => $apellido2,
                    'correo'    => $correo ?: null,
                ]);

                $lastId = $conn->lastInsertId();

                // Insertar en curso_academico
                $sql2 = "INSERT INTO curso_academico (id_alumno, id_ciclo, anio_inicio, anio_fin)
                         VALUES (:idAlumno, :idCiclo, :inicio, :fin)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->execute([
                    'idAlumno' => $lastId,
                    'idCiclo'  => $idCiclo,
                    'inicio'   => $anioInicio,
                    'fin'      => $anioInicio + 1,
                ]);

                $conn->commit();
                $insertados++;

            } catch (\PDOException $e) {
                if ($conn->inTransaction()) $conn->rollBack();
                $errores[] = "Fila " . ($idx + 1) . " ($nombre $apellido1): " . $e->getMessage();
                $omitidos++;
            }
        }

        return [
            'success'    => true,
            'insertados' => $insertados,
            'omitidos'   => $omitidos,
            'errores'    => $errores
        ];
    }

    // ── MÉTODOS INTERNOS AUXILIARES ──────────────────────────────────────────

    private static function resolverEspecialidad(string $texto, array $ciclosCache, array $cursosMap): ?int {
        $texto = trim($texto);
        if ($texto === '') return null;

        $partes    = preg_split('/\s+/', $texto);
        $ultimaPal = strtolower(array_pop($partes));       
        $ultimaNorm = rtrim($ultimaPal, 'º°o');             

        $idCurso  = $cursosMap[$ultimaPal]  ?? $cursosMap[$ultimaNorm] ?? null;
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

    private static function parsearFecha($valor): ?string {
        $v = trim((string)$valor);
        if ($v === '' || $v === '0') return null;
        
        $sep = str_contains($v, '.') ? '.' : (str_contains($v, '/') ? '/' : '-');
        $partes = explode($sep, $v);
        if (count($partes) !== 3) return null;
        [$d, $m, $a] = $partes;
        if (strlen($a) === 2) $a = '20' . $a;
        if (!checkdate((int)$m, (int)$d, (int)$a)) return null;
        return sprintf('%04d-%02d-%02d', (int)$a, (int)$m, (int)$d);
    }
}