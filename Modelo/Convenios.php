<?php

/**
 * Modelo/Convenios.php — Gestión de convenios oficiales y favoritos del tutor
 *
 * Centraliza todas las operaciones sobre convenios en sus dos vertientes:
 *
 *   Paso 1 (tutor):
 *     - Búsqueda libre por nombre o CIF
 *     - Gestión del listado personal ("favoritos") de cada tutor (tabla mi_listado)
 *     - Registro y seguimiento de convenios nuevos pendientes de validación
 *     - Comprobación de uso antes de eliminar un favorito
 *
 *   Admin — Tabla Convenios:
 *     - Listado completo con filtro y ordenación
 *     - Edición y eliminación (con comprobación de uso global)
 *     - Actualización de datos de un convenio oficial
 *
 * MVC: Modelo. Gestiona principalmente las tablas `convenios`, `mi_listado`,
 * `convenios_nuevos` y `convenios_aprobados`.
 */

require_once __DIR__ . '/../Core/Conexion.php';

class Convenios {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 1 — BÚSQUEDA DE CONVENIOS  (Resultados de búsqueda)
    // ═══════════════════════════════════════════════════════════════════
    public function buscar($termino) {
        $query = "SELECT num_convenio, nombre_empresa, cif, localidad, telefono, representante 
                  FROM convenios 
                  WHERE nombre_empresa LIKE ? OR cif LIKE ?";
        $stmt = $this->conn->prepare($query);
        $param = "%$termino%";
        $stmt->execute([$param, $param]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 1 — MI LISTADO PERSONAL  (Favoritos del tutor)
    // ═══════════════════════════════════════════════════════════════════
    public function añadirAFavoritos($id_tutor, $num_convenio) {
        try {
            $sql = "INSERT INTO mi_listado (id_tutor, num_convenio) VALUES (:id_t, :num_conv)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_t', $id_tutor);
            $stmt->bindParam(':num_conv', $num_convenio);
            return $stmt->execute();
        } catch (PDOException $e) {
            // El código 23000 es para violaciones de integridad (como duplicados)
            if ($e->getCode() == 23000) {
                return "duplicado"; 
            }
            return false;
        }
    }

    public function obtenerFavoritos($id_tutor) {
        $sql = "SELECT c.* FROM convenios c
                INNER JOIN mi_listado m ON c.num_convenio = m.num_convenio
                WHERE m.id_tutor = :id_tutor";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function eliminarDeFavoritos($id_tutor, $num_convenio) {
        $query = "DELETE FROM mi_listado WHERE id_tutor = ? AND num_convenio = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_tutor, $num_convenio]);
    }

    public function estaEnUso($num_convenio, $id_ciclo) {
        // Relacionamos las asignaciones con curso_academico a través del id_alumno
        $query = "SELECT COUNT(*) 
                FROM asignaciones a
                INNER JOIN curso_academico ca ON a.id_alumno = ca.id_alumno
                WHERE a.num_convenio = :num_convenio 
                AND ca.id_ciclo = :id_ciclo";
                
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'num_convenio' => $num_convenio,
            'id_ciclo'     => $id_ciclo
        ]);
        
        return $stmt->fetchColumn() > 0;
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 1 — CONVENIOS EN PROCESO / REGISTRO
    // Usado también por Admin → Convenios Pendientes
    // ═══════════════════════════════════════════════════════════════════
    public function guardarNuevoConvenioPendiente($datos) {
        $query = "INSERT INTO convenios_nuevos 
                    (nombre_empresa, cif, direccion, localidad, cp, telefono, fax,
                    representante, especialidad, fecha_nueva_renovacion)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $datos['nombre_empresa'],
            $datos['cif'],
            $datos['direccion'],
            $datos['localidad'],
            $datos['cp'],
            $datos['telefono'],
            $datos['fax'],
            $datos['representante'],
            $datos['especialidad'],
            $datos['fecha_nueva_renovacion'] ?? null,
        ]);
    }

    /**
     * Obtiene solo los convenios nuevos que NO han sido aprobados aún.
     * Al usar el LEFT JOIN, si no hay registro en convenios_aprobados, ca.id_convenio_nuevo será NULL.
     */
    public function listarPendientesDeAprobacion($especialidad) {
        $sql = "SELECT cn.* FROM convenios_nuevos cn
                LEFT JOIN convenios_aprobados ca ON cn.id_convenio_nuevo = ca.id_convenio_nuevo
                WHERE cn.especialidad = ? AND ca.id_convenio_nuevo IS NULL";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$especialidad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta un registro en la tabla de aprobados para que el convenio 
     * deje de listarse como "pendiente".
     */
    public function registrarAprobacion($id_convenio_nuevo) {
        $sql = "INSERT INTO convenios_aprobados (id_convenio_nuevo, fecha_aprobacion) VALUES (?, CURDATE())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_convenio_nuevo]);
    }

    public function actualizarConvenioNuevo($id, $datos) {
        $sql = "UPDATE convenios_nuevos SET 
                nombre_empresa = ?, 
                cif = ?, 
                direccion = ?, 
                localidad = ?, 
                cp = ?, 
                telefono = ?, 
                fax = ?, 
                representante = ?,
                fecha_nueva_renovacion = ?,
                observaciones = ?
                WHERE id_convenio_nuevo = ?";
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $datos['nombre_empresa'],
            $datos['cif'],
            $datos['direccion'],
            $datos['localidad'],
            $datos['cp'],
            $datos['telefono'],
            $datos['fax'],
            $datos['representante'],
            $datos['fecha_nueva_renovacion'] ?? null,
            $datos['observaciones']          ?? null,
            $id
        ]);
    }

    public function eliminarConvenioNuevo($id) {
        try {
            $sql = "DELETE FROM convenios_nuevos WHERE id_convenio_nuevo = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }


    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — TABLA CONVENIOS  (Vista: Admin/Sections/Tabla_Convenios.php)
    // ═══════════════════════════════════════════════════════════════════

    public function obtenerConvenios($busqueda = '', $ordenar = 'nombre_empresa') {
        try {
            $columnasPermitidas = ['nombre_empresa', 'cif', 'localidad', 'num_convenio'];
            if (!in_array($ordenar, $columnasPermitidas)) $ordenar = 'nombre_empresa';

            $sql = "SELECT * FROM convenios
                    WHERE nombre_empresa LIKE :busqueda
                    OR cif              LIKE :busqueda
                    OR localidad        LIKE :busqueda
                    ORDER BY $ordenar ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':busqueda' => "%$busqueda%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerConvenios: " . $e->getMessage());
            return [];
        }
    }

    public function eliminarConvenio($num_convenio) {
        try {
            return $this->conn->prepare("DELETE FROM convenios WHERE num_convenio = :id")
                               ->execute([':id' => $num_convenio]);
        } catch (PDOException $e) {
            error_log("Error en eliminarConvenio: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarConvenio($num_convenio, $d) {
        $stmt = $this->conn->prepare(
            "UPDATE convenios SET
                nombre_empresa         = ?, cif            = ?, direccion              = ?,
                localidad              = ?, cp             = ?, telefono               = ?,
                fax                    = ?, representante  = ?, especialidad           = ?,
                fecha_alta_renovacion  = ?, fecha_nueva_renovacion = ?, observaciones = ?
             WHERE num_convenio = ?"
        );
        return $stmt->execute([
            $d['nombre_empresa'],        $d['cif'],                   $d['direccion'],
            $d['localidad'],             $d['cp'],                    $d['telefono'],
            $d['fax'],                   $d['representante'],          $d['especialidad']           ?? null,
            $d['fecha_alta_renovacion'] ?? null,                       $d['fecha_nueva_renovacion'] ?? null,
            $d['observaciones']         ?? null,
            $num_convenio,
        ]);
    }

    /**
     * Comprueba si un convenio tiene alumnos asignados en cualquier ciclo.
     * Usado por el admin antes de eliminar un convenio de la tabla oficial.
     */
    public function estaEnUsoGlobal($num_convenio): bool {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM asignaciones WHERE num_convenio = ?"
        );
        $stmt->execute([$num_convenio]);
        return $stmt->fetchColumn() > 0;
    }


} // Llave de la clase


?>
