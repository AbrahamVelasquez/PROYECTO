<?php

// Modelo/Convenios.php

require_once __DIR__ . '/../Core/Conexion.php'; 

class Convenios {
    private $conn; 

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    public function buscar($termino) {
        $query = "SELECT num_convenio, nombre_empresa, cif, localidad, telefono, representante 
                  FROM convenios 
                  WHERE nombre_empresa LIKE ? OR cif LIKE ?";
        $stmt = $this->conn->prepare($query);
        $param = "%$termino%";
        $stmt->execute([$param, $param]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

} // Llave de la clase


?>