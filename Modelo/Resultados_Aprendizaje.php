<?php

/**
 * Modelo/Resultados_Aprendizaje.php
 *
 * Gestiona la tabla `resultados_aprendizaje`.
 * Permite consultar, insertar y actualizar los RAs de cada módulo por ciclo.
 */

// Modelo/Resultados_Aprendizaje.php
// Gestiona la tabla resultados_aprendizaje.
// Cubre la definición, consulta y edición de RAs por módulo y ciclo.

require_once "./Core/Conexion.php";

class Resultados_Aprendizaje {

    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 3 — PLAN FORMATIVO  (Vista: Steps/Plan_Formativo.php)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Devuelve todos los RAs de los módulos de un ciclo,
     * ordenados por periodo, módulo y número de RA.
     */
    public function obtenerResultadosAprendizaje($idCiclo) {
        $sql = "SELECT ra.id_ra, ra.id_modulo, ra.numero_ra, ra.impartido_empresa, ra.periodo,
                        m.nombre_modulo
                FROM resultados_aprendizaje ra
                INNER JOIN modulos m         ON ra.id_modulo  = m.id_modulo
                INNER JOIN plan_estudios pe  ON ra.id_modulo  = pe.id_modulo
                WHERE pe.id_ciclo = :idCiclo
                ORDER BY ra.periodo, m.nombre_modulo, ra.numero_ra";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['idCiclo' => (int)$idCiclo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Alias para el contexto del tutor.
     * Devuelve los RAs guardados para los módulos de su ciclo.
     */
    public function obtenerRAsPorTutor($idCiclo) {
        $sql = "SELECT ra.*, m.nombre_modulo
                FROM resultados_aprendizaje ra
                JOIN modulos m         ON ra.id_modulo  = m.id_modulo
                JOIN plan_estudios pe  ON m.id_modulo   = pe.id_modulo
                WHERE pe.id_ciclo = ?
                ORDER BY ra.periodo ASC, m.nombre_modulo ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCiclo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guarda el conjunto de RAs de un ciclo: elimina los marcados para borrar
     * e inserta/actualiza los nuevos o modificados.
     * Opera en transacción para garantizar consistencia.
     */
    public function guardarResultadosAprendizaje($idCiclo, $rasNuevos, $raEliminados) {
        try {
            $this->conn->beginTransaction();

            // 1. Eliminar los RAs marcados para borrar
            if (!empty($raEliminados)) {
                $placeholders = implode(',', array_fill(0, count($raEliminados), '?'));
                $this->conn->prepare(
                    "DELETE FROM resultados_aprendizaje
                     WHERE id_ra IN ($placeholders)
                     AND id_modulo IN (
                         SELECT id_modulo FROM plan_estudios WHERE id_ciclo = ?
                     )"
                )->execute(array_merge(array_map('intval', $raEliminados), [(int)$idCiclo]));
            }

            // 2. Insertar o actualizar los RAs nuevos/modificados
            foreach ($rasNuevos as $ra) {
                $idRa    = (int)($ra['id_ra']     ?? 0);
                $idMod   = (int)($ra['id_modulo'] ?? 0);
                $periodo = $ra['periodo']          ?? '1';
                $numero  = (int)preg_replace('/\D/', '', $ra['numero_ra'] ?? '1');
                if ($numero < 1) $numero = 1;
                $empresa = (int)($ra['impartido_empresa'] ?? 0);

                if ($idMod <= 0) continue;

                if ($idRa > 0) {
                    $this->conn->prepare(
                        "UPDATE resultados_aprendizaje
                         SET periodo = ?, numero_ra = ?, impartido_empresa = ?
                         WHERE id_ra = ? AND id_modulo IN (
                             SELECT id_modulo FROM plan_estudios WHERE id_ciclo = ?
                         )"
                    )->execute([$periodo, $numero, $empresa, $idRa, (int)$idCiclo]);
                } else {
                    $this->conn->prepare(
                        "INSERT INTO resultados_aprendizaje (id_modulo, numero_ra, impartido_empresa, periodo)
                         VALUES (?, ?, ?, ?)"
                    )->execute([$idMod, $numero, $empresa, $periodo]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            return false;
        }
    }

} // Llave de la clase
