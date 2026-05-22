<?php

/**
 * Modelo/Asignaciones.php
 *
 * Gestiona las tablas `asignaciones` y `asignaciones_firmadas`.
 * Cubre el ciclo completo de una asignación:
 *   creación → envío → firma → exportación → reversión.
 */

// Modelo/Asignaciones.php
// Gestiona las tablas asignaciones y asignaciones_firmadas.
// Cubre el ciclo completo: asignación → envío → firma → exportación.

require_once "./Core/Conexion.php";

class Asignaciones {

    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 2 — ALUMNOS  (asignación y firma del tutor)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Borra la asignación de un alumno (cuando el tutor le quita el convenio).
     */
    public function eliminarAsignacion($idAlumno) {
        $sql = "DELETE FROM asignaciones WHERE id_alumno = :id";
        return $this->conn->prepare($sql)->execute(['id' => $idAlumno]);
    }

    /**
     * Firma la asignación del tutor: INSERT IGNORE + actualización opcional de anexo.
     */
    public function firmarAsignacion($idAsignacion, $anexo = null) {
        try {
            $this->conn->prepare(
                "INSERT IGNORE INTO asignaciones_firmadas (id_asignacion) VALUES (:id)"
            )->execute(['id' => $idAsignacion]);

            if (!empty($anexo)) {
                $this->conn->prepare(
                    "UPDATE asignaciones_firmadas SET anexo = :anexo WHERE id_asignacion = :id"
                )->execute(['anexo' => $anexo, 'id' => $idAsignacion]);
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Comprueba si una asignación ya tiene firma registrada.
     */
    public function comprobarFirmaExistente($idAsig) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total FROM asignaciones_firmadas WHERE id_asignacion = :id"
            );
            $stmt->execute(['id' => $idAsig]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 3 — PLAN FORMATIVO  (exportación y reversión)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Revierte un alumno al estado "enviado": borra su firma y resetea enviado = 0.
     */
    public function devolverAlumnoAEnvio($idAlumno) {
        try {
            $this->conn->beginTransaction();

            $this->conn->prepare(
                "DELETE FROM asignaciones_firmadas WHERE id_asignacion = (
                    SELECT id_asignacion FROM asignaciones WHERE id_alumno = :id LIMIT 1
                )"
            )->execute(['id' => $idAlumno]);

            $this->conn->prepare(
                "UPDATE asignaciones SET enviado = 0 WHERE id_alumno = :id"
            )->execute(['id' => $idAlumno]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Actualiza el número de anexo en asignaciones_firmadas.
     */
    public function actualizarAnexo($idAsignacion, $anexo) {
        try {
            return $this->conn->prepare(
                "UPDATE asignaciones_firmadas SET anexo = ? WHERE id_asignacion = ?"
            )->execute([
                ($anexo !== '' && $anexo !== null) ? (int)$anexo : null,
                (int)$idAsignacion,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Cambia el estado de exportación (exportado = 0/1) para un conjunto de asignaciones.
     * Usado por "Revertir exportación" en Plan Formativo.
     */
    public function reiniciarEstadoExportacion($ids, $estado) {
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->conn->prepare(
                "UPDATE asignaciones_firmadas
                 SET exportado = ?
                 WHERE id_asignacion IN ($placeholders)"
            );
            return $stmt->execute(array_merge([$estado], $ids));
        } catch (PDOException $e) {
            error_log("Error en reiniciarEstadoExportacion: " . $e->getMessage());
            return false;
        }
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — LISTADO DE ALUMNOS  (firma desde el panel de admin)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Firma de asignación desde el panel de admin.
     * A diferencia de la firma del tutor (INSERT IGNORE), aquí el admin
     * registra una firma nueva con anexo obligatorio.
     */
    public function firmarAsignacionAdmin($id_asignacion, $anexo) {
        try {
            return $this->conn->prepare(
                "INSERT INTO asignaciones_firmadas (id_asignacion, anexo) VALUES (:id, :anexo)"
            )->execute([':id' => $id_asignacion, ':anexo' => $anexo]);
        } catch (PDOException $e) {
            error_log("Error en firmarAsignacionAdmin: " . $e->getMessage());
            return false;
        }
    }

} // Llave de la clase
