<?php

/**
 * Modelo/Modulos.php
 *
 * Gestiona la tabla `modulos` y su relación con `plan_estudios`.
 * Proporciona los módulos vinculados a un ciclo formativo concreto.
 */

// Modelo/Modulos.php
// Gestiona la tabla modulos y su relación con plan_estudios.
// Proporciona los módulos disponibles para un ciclo formativo.

require_once "./Core/Conexion.php";

class Modulos {

    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 3 — PLAN FORMATIVO  (Vista: Steps/Plan_Formativo.php)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Devuelve los módulos del plan de estudios de un ciclo concreto.
     * Usado para construir el selector de módulos al editar RAs.
     */
    public function obtenerModulosPorCiclo($idCiclo) {
        $sql = "SELECT m.id_modulo, m.nombre_modulo
                FROM modulos m
                INNER JOIN plan_estudios pe ON m.id_modulo = pe.id_modulo
                WHERE pe.id_ciclo = :idCiclo
                ORDER BY m.nombre_modulo";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['idCiclo' => (int)$idCiclo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Alias semántico de obtenerModulosPorCiclo para el contexto del tutor.
     * Obtiene los módulos vinculados al ciclo del tutor activo.
     */
    public function obtenerModulosPorTutor($idCiclo) {
        $sql = "SELECT m.id_modulo, m.nombre_modulo
                FROM modulos m
                JOIN plan_estudios pe ON m.id_modulo = pe.id_modulo
                WHERE pe.id_ciclo = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCiclo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} // Llave de la clase
