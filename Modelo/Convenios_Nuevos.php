<?php

/**
 * Modelo/Convenios_Nuevos.php
 *
 * Gestiona las tablas `convenios_nuevos` y `convenios_aprobados`.
 * Cubre el flujo de registro externo:
 *   solicitud tutor → aprobación tutor → validación admin → convenio oficial.
 */

// Modelo/Convenios_Nuevos.php
// Gestiona las tablas convenios_nuevos y convenios_aprobados.
// Cubre el flujo completo: registro pendiente → aprobación → validación oficial.

require_once "./Core/Conexion.php";

class Convenios_Nuevos {

    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — CONVENIOS PENDIENTES  (Vista: Admin/Sections/Tabla_Convenios_Pendientes.php)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Devuelve los convenios nuevos aprobados por el tutor pero aún no
     * validados/trasladados a la tabla oficial por el admin (validado = 0).
     */
    public function obtenerConveniosPendientes() {
        $stmt = $this->conn->prepare(
            "SELECT cn.*, ca.id_convenio_aprobado, ca.fecha_aprobacion
             FROM convenios_nuevos cn
             INNER JOIN convenios_aprobados ca ON cn.id_convenio_nuevo = ca.id_convenio_nuevo
             WHERE ca.validado = 0
             ORDER BY ca.fecha_aprobacion DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Valida manualmente un convenio pendiente: inserta en la tabla oficial
     * con los datos revisados por el admin y marca como validado.
     */
    public function procesarValidacionManual($d) {
        try {
            $this->conn->beginTransaction();

            $stmtMax = $this->conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
            $stmtMax->execute();
            $nuevoNum = (string)((int)$stmtMax->fetchColumn() + 1);

            $this->conn->prepare(
                "INSERT INTO convenios
                    (num_convenio, nombre_empresa, cif, direccion, localidad, cp,
                     telefono, fax, representante, especialidad,
                     fecha_alta_renovacion, fecha_nueva_renovacion, observaciones)
                 VALUES (:num_conv, :nom, :cif, :dir, :loc, :cp,
                         :tel, :fax, :rep, :esp, CURDATE(), :fecha_nueva, :obs)"
            )->execute([
                ':num_conv'    => $nuevoNum,
                ':nom'         => $d['nombre_empresa'],
                ':cif'         => $d['cif'],
                ':dir'         => $d['direccion'],
                ':loc'         => $d['localidad'],
                ':cp'          => $d['cp'],
                ':tel'         => $d['telefono'],
                ':fax'         => $d['fax'],
                ':rep'         => $d['representante'],
                ':esp'         => $d['especialidad']           ?? null,
                ':fecha_nueva' => $d['fecha_nueva_renovacion'] ?? null,
                ':obs'         => $d['observaciones']          ?? null,
            ]);

            $this->conn->prepare(
                "UPDATE convenios_aprobados SET validado = 1 WHERE id_convenio_nuevo = :id"
            )->execute([':id' => $d['id_convenio_nuevo']]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en procesarValidacionManual: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valida automáticamente con los datos tal como están en convenios_nuevos,
     * sin edición previa por parte del admin.
     */
    public function validarConvenio($id) {
        try {
            $this->conn->beginTransaction();

            $stmtSel = $this->conn->prepare(
                "SELECT * FROM convenios_nuevos WHERE id_convenio_nuevo = :id"
            );
            $stmtSel->execute([':id' => $id]);
            $datos = $stmtSel->fetch(PDO::FETCH_ASSOC);

            if (!$datos) throw new Exception("No se encontraron datos para el convenio ID: $id");

            $stmtMax = $this->conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
            $stmtMax->execute();
            $nuevoNum = (string)((int)$stmtMax->fetchColumn() + 1);

            $this->conn->prepare(
                "INSERT INTO convenios
                    (num_convenio, nombre_empresa, cif, direccion, localidad, cp,
                     telefono, fax, representante, especialidad,
                     fecha_alta_renovacion, fecha_nueva_renovacion, observaciones)
                 VALUES (:num_conv, :nom, :cif, :dir, :loc, :cp,
                         :tel, :fax, :rep, :esp, CURDATE(), :fecha_nueva, :obs)"
            )->execute([
                ':num_conv'    => $nuevoNum,
                ':nom'         => $datos['nombre_empresa'],
                ':cif'         => $datos['cif'],
                ':dir'         => $datos['direccion'],
                ':loc'         => $datos['localidad'],
                ':cp'          => $datos['cp'],
                ':tel'         => $datos['telefono'],
                ':fax'         => $datos['fax'],
                ':rep'         => $datos['representante'],
                ':esp'         => $datos['especialidad']           ?? null,
                ':fecha_nueva' => $datos['fecha_nueva_renovacion'] ?? null,
                ':obs'         => $datos['observaciones']          ?? null,
            ]);

            $this->conn->prepare(
                "UPDATE convenios_aprobados SET validado = 1 WHERE id_convenio_nuevo = :id"
            )->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en validarConvenio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guarda cambios sobre un convenio pendiente sin validarlo todavía.
     */
    public function actualizarConvenioPendiente($datos) {
        try {
            return $this->conn->prepare(
                "UPDATE convenios_nuevos SET
                    nombre_empresa         = :nombre_empresa,
                    cif                    = :cif,
                    direccion              = :direccion,
                    localidad              = :localidad,
                    cp                     = :cp,
                    telefono               = :telefono,
                    fax                    = :fax,
                    representante          = :representante,
                    especialidad           = :especialidad,
                    fecha_nueva_renovacion = :fecha_nueva_renovacion,
                    observaciones          = :observaciones
                 WHERE id_convenio_nuevo   = :id_convenio_nuevo"
            )->execute([
                ':id_convenio_nuevo'      => $datos['id_convenio_nuevo'],
                ':nombre_empresa'         => $datos['nombre_empresa'],
                ':cif'                    => $datos['cif'],
                ':direccion'              => $datos['direccion'],
                ':localidad'              => $datos['localidad'],
                ':cp'                     => $datos['cp'],
                ':telefono'               => $datos['telefono'],
                ':fax'                    => $datos['fax'],
                ':representante'          => $datos['representante'],
                ':especialidad'           => $datos['especialidad']           ?? null,
                ':fecha_nueva_renovacion' => $datos['fecha_nueva_renovacion'] ?? null,
                ':observaciones'          => $datos['observaciones']          ?? null,
            ]);
        } catch (PDOException $e) {
            error_log("Error en actualizarConvenioPendiente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sincroniza los datos de un convenio oficial editado hacia su registro
     * pendiente correspondiente (búsqueda por CIF o nombre anterior).
     */
    public function sincronizarConvenioPendiente($cifAntiguo, $nombreAntiguo, $d) {
        return $this->conn->prepare(
            "UPDATE convenios_nuevos SET
                nombre_empresa = ?, cif = ?, direccion = ?, localidad = ?,
                cp = ?, telefono = ?, fax = ?, representante = ?
             WHERE cif = ? OR nombre_empresa = ?"
        )->execute([
            $d['nombre_empresa'], $d['cif'],        $d['direccion'], $d['localidad'],
            $d['cp'],             $d['telefono'],   $d['fax'],       $d['representante'],
            $cifAntiguo,          $nombreAntiguo,
        ]);
    }

    /**
     * Borra en cascada un convenio de ambas tablas (convenios_nuevos + convenios_aprobados).
     */
    public function borrarRegistroPendienteYOficial($id) {
        try {
            $this->conn->beginTransaction();
            $this->conn->prepare("DELETE FROM convenios_nuevos    WHERE id_convenio_nuevo = ?")->execute([$id]);
            $this->conn->prepare("DELETE FROM convenios_aprobados WHERE id_convenio_nuevo = ?")->execute([$id]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

} // Llave de la clase
