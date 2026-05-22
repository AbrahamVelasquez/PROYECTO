<?php

/**
 * Modelo/Exportar.php — Capa de datos para todos los helpers de exportación
 *
 * Centraliza las consultas SQL necesarias para generar los documentos de FCT:
 *   - Word (anexos de alumnos): obtenerAlumnosWord(), obtenerDatosTutorWord(),
 *     marcarAlumnosComoEnviados()
 *   - Excel Plan Formativo: obtenerRAsCiclo(), obtenerDatosAsignacion(),
 *     marcarExportadoSiPendiente(), obtenerIdsAlumnosCompletados()
 *   - Formato compartido: fmtFecha() y formatearHorario() están aquí para que
 *     Exportar_PF.php y Exportar_PF_Todo.php no dupliquen la misma lógica.
 *
 * obtenerDatosAsignacion() es la consulta más densa del proyecto: une 8 tablas
 * para obtener en una sola llamada todos los campos que necesita la plantilla PF
 * (alumno, asignación, convenio, ciclo, curso académico, firma y tutor).
 *
 * MVC: Modelo de exportación. Ningún helper de exportación toca la BD directamente;
 * todo pasa por aquí para mantener la separación entre lógica de documentos y BD.
 */

require_once __DIR__ . '/../Core/Conexion.php';

class Exportar {

    // ── MÉTODOS PARA EXPORTACIÓN A WORD ────────────────────────────────────────

    public static function obtenerAlumnosWord(array $ids): array {
        try {
            $conn = Conexion::getConexion();
            $ph   = implode(',', array_fill(0, count($ids), '?'));
            $sql  = "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo,
                            asig.num_convenio, asig.horario, asig.num_total_horas, asig.horas_dia,
                            asig.fecha_inicio, asig.fecha_final, asig.nombre_tutor_empresa,
                            asig.horario_excepciones,
                            conv.nombre_empresa, conv.localidad, conv.direccion
                     FROM alumnos a
                     LEFT JOIN asignaciones asig ON a.id_alumno  = asig.id_alumno
                     LEFT JOIN convenios conv    ON asig.num_convenio = conv.num_convenio
                     WHERE a.id_alumno IN ($ph)
                     ORDER BY a.apellido1 ASC, a.apellido2 ASC, a.nombre ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute($ids);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public static function obtenerDatosTutorWord(string $username): array {
        try {
            $conn = Conexion::getConexion();
            $sql  = "SELECT t.nombre, t.apellidos, t.dni,
                            ci.id_ciclo, ci.nombre_ciclo,
                            cu.nombre_curso
                     FROM tutores t
                     INNER JOIN usuarios u  ON t.id_usuario = u.id_usuario
                     INNER JOIN ciclos ci   ON t.id_ciclo   = ci.id_ciclo
                     INNER JOIN cursos cu   ON ci.id_curso  = cu.id_curso
                     WHERE u.username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) { return []; }
    }

    public static function marcarAlumnosComoEnviados(array $ids): bool {
        try {
            $conn = Conexion::getConexion();
            $ph   = implode(',', array_fill(0, count($ids), '?'));
            $sql  = "UPDATE asignaciones SET enviado = 1 WHERE id_alumno IN ($ph)";
            $stmt = $conn->prepare($sql);
            return $stmt->execute($ids);
        } catch (PDOException $e) { return false; }
    }

    // ── MÉTODOS PARA EXPORTACIÓN A EXCEL (PF) ──────────────────────────────────

    public static function obtenerRAsCiclo(int $idCiclo): array {
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

    public static function obtenerDatosAsignacion(int $idAsignacion): ?array {
        try {
            $conn = Conexion::getConexion();
            $sql  = "SELECT a.nombre, a.apellido1, a.apellido2, a.correo, a.telefono,
                            asig.id_asignacion, asig.num_convenio, asig.horario, asig.horario_excepciones,
                            asig.num_total_horas, asig.horas_dia, asig.dias_semana,
                            asig.fecha_inicio, asig.fecha_final,
                            asig.nombre_tutor_empresa, asig.correo_tutor_empresa, asig.tel_tutor_empresa,
                            conv.nombre_empresa, conv.cif, asig.correo_tutor_empresa AS email_empresa,
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

    public static function marcarExportadoSiPendiente(int $idAsignacion): void {
        try {
            $conn = Conexion::getConexion();
            $sql  = "UPDATE asignaciones_firmadas SET exportado = 1
                     WHERE id_asignacion = ? AND exportado = 0";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$idAsignacion]);
        } catch (PDOException $e) { /* silencioso */ }
    }
    // ── MÉTODOS DE FORMATO COMPARTIDOS ─────────────────────────────────────────
    // Centralizados aquí para evitar duplicación entre Exportar_PF y Exportar_PF_Todo

    /**
     * Formatea una fecha Y-m-d a d/m/Y para mostrar en documentos.
     */
    public static function fmtFecha(string $fecha): string {
        if (empty($fecha)) return '';
        try { return (new DateTime($fecha))->format('d/m/Y'); }
        catch (Exception $e) { return $fecha; }
    }

    /**
     * Formatea el horario para exportación a Excel.
     * Si hay excepciones (JSON de bloques horarios), las expande.
     * Si no, combina los días simples con el horario.
     */
    public static function formatearHorario(string $excepciones, string $horarioSimple, string $diasSemana = ''): string {
        $NOMBRES = ['L'=>'Lunes','M'=>'Martes','X'=>'Miércoles','J'=>'Jueves','V'=>'Viernes','S'=>'Sábado','D'=>'Domingo'];
        $ORDEN   = ['L'=>0,'M'=>1,'X'=>2,'J'=>3,'V'=>4,'S'=>5,'D'=>6];

        if (empty($excepciones)) {
            if (empty($diasSemana) || empty($horarioSimple)) return $horarioSimple;
            $partes = explode('-', $diasSemana);
            if (count($partes) === 2) {
                $ini = $NOMBRES[trim($partes[0])] ?? trim($partes[0]);
                $fin = $NOMBRES[trim($partes[1])] ?? trim($partes[1]);
                return "$ini a $fin: $horarioSimple";
            }
            return ($NOMBRES[trim($diasSemana)] ?? $diasSemana) . ": $horarioSimple";
        }

        $bloques = json_decode($excepciones, true) ?? [];
        $partes  = [];
        foreach ($bloques as $b) {
            if (empty($b['dias'])) continue;
            $dias = $b['dias'];
            usort($dias, fn($a, $b) => $ORDEN[$a] - $ORDEN[$b]);
            $esConsec = true;
            for ($i = 1; $i < count($dias); $i++) {
                if ($ORDEN[$dias[$i]] !== $ORDEN[$dias[$i-1]] + 1) { $esConsec = false; break; }
            }
            $label = ($esConsec && count($dias) > 1)
                ? $NOMBRES[$dias[0]] . ' a ' . $NOMBRES[$dias[count($dias)-1]]
                : implode(', ', array_map(fn($d) => $NOMBRES[$d], $dias));
            $partes[] = $label . ': ' . $b['inicio'] . '-' . $b['fin'];
        }
        return implode(', ', $partes);
    }



    /**
     * Obtiene los IDs de todos los alumnos con estado COMPLETADO de un ciclo.
     * Usado por exportar_todo para no depender del DOM paginado.
     * Un alumno es "completado" si tiene convenio, dirección, fechas y horario.
     */
    public static function obtenerIdsAlumnosCompletados(int $idCiclo): array {
        try {
            $conn = Conexion::getConexion();
            $sql  = "SELECT a.id_alumno
                     FROM alumnos a
                     INNER JOIN curso_academico ca ON a.id_alumno = ca.id_alumno
                     INNER JOIN asignaciones asig  ON a.id_alumno = asig.id_alumno
                     WHERE ca.id_ciclo = ?
                     AND asig.num_convenio   IS NOT NULL AND asig.num_convenio   != ''
                     AND asig.fecha_inicio   IS NOT NULL AND asig.fecha_inicio   != ''
                     AND asig.fecha_final    IS NOT NULL AND asig.fecha_final    != ''
                     AND asig.horario        IS NOT NULL AND asig.horario        != ''
                     ORDER BY a.apellido1 ASC, a.apellido2 ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$idCiclo]);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_alumno');
        } catch (PDOException $e) { return []; }
    }

}