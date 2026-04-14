<?php
require_once "./Core/Conexion.php"; 

class Convenios {
    private $conn; 

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    public function buscar($termino) {
        $query = "SELECT id_convenio, nombre_empresa, cif, municipio, telefono, mail, nombre_representante 
                  FROM convenios 
                  WHERE nombre_empresa LIKE ? OR cif LIKE ?";
        $stmt = $this->conn->prepare($query);
        $param = "%$termino%";
        $stmt->execute([$param, $param]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function añadirAFavoritos($id_tutor, $id_convenio) {
        $sql = "INSERT INTO mi_listado (id_tutor, id_convenio) VALUES (:id_t, :id_conv)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_t', $id_tutor);
        $stmt->bindParam(':id_conv', $id_convenio);
        return $stmt->execute();
    }

    public function obtenerFavoritos($id_tutor) {
        $sql = "SELECT c.* FROM convenios c
                INNER JOIN mi_listado m ON c.id_convenio = m.id_convenio
                WHERE m.id_tutor = :id_tutor";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function eliminarDeFavoritos($id_tutor, $id_convenio) {
        $query = "DELETE FROM mi_listado WHERE id_tutor = ? AND id_convenio = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_tutor, $id_convenio]);
    }

    public function estaEnUso($id_convenio, $id_ciclo) {
        // Relacionamos las asignaciones con curso_academico a través del id_alumno
        $query = "SELECT COUNT(*) 
                FROM asignaciones a
                INNER JOIN curso_academico ca ON a.id_alumno = ca.id_alumno
                WHERE a.id_convenio = :id_convenio 
                AND ca.id_ciclo = :id_ciclo";
                
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'id_convenio' => $id_convenio,
            'id_ciclo'    => $id_ciclo
        ]);
        
        return $stmt->fetchColumn() > 0;
    }

    public function guardarNuevoConvenioPendiente($datos) {
        $query = "INSERT INTO convenios_nuevos 
                    (nombre_empresa, cif, direccion, municipio, cp, pais, telefono, fax, mail, 
                    nombre_representante, dni_representante, cargo, id_ciclo)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $datos['nombre_empresa'],
            $datos['cif'],
            $datos['direccion'],
            $datos['municipio'],
            $datos['cp'],
            $datos['pais'],
            $datos['telefono'],
            $datos['fax'],
            $datos['mail'],
            $datos['nombre_representante'],
            $datos['dni_representante'],
            $datos['cargo'],
            $datos['id_ciclo']
        ]);
    }

    /**
     * Obtiene solo los convenios nuevos que NO han sido aprobados aún.
     * Al usar el LEFT JOIN, si no hay registro en convenios_aprobados, ca.id_convenio_nuevo será NULL.
     */
    public function listarPendientesDeAprobacion($id_ciclo) {
        $sql = "SELECT cn.* FROM convenios_nuevos cn
                LEFT JOIN convenios_aprobados ca ON cn.id_convenio_nuevo = ca.id_convenio_nuevo
                WHERE cn.id_ciclo = ? AND ca.id_convenio_nuevo IS NULL";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_ciclo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta un registro en la tabla de aprobados para que el convenio 
     * deje de listarse como "pendiente".
     */
    public function registrarAprobacion($id_convenio_nuevo) {
        $sql = "INSERT INTO convenios_aprobados (id_convenio_nuevo) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_convenio_nuevo]);
    }

    public function actualizarConvenioNuevo($id, $datos) {
    try {
        $sql = "UPDATE convenios_nuevos SET 
                nombre_empresa        = :nombre,
                cif                   = :cif,
                direccion             = :direccion,
                municipio             = :municipio,
                cp                    = :cp,
                pais                  = :pais,
                telefono              = :telefono,
                fax                   = :fax,
                mail                  = :email,
                nombre_representante  = :rep_nom,
                dni_representante     = :rep_dni,
                cargo                 = :rep_cargo
                WHERE id_convenio_nuevo = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nombre'    => $datos['nombre_empresa'],
            ':cif'       => $datos['cif'],
            ':direccion' => $datos['direccion'],
            ':municipio' => $datos['municipio'],
            ':cp'        => $datos['cp'],
            ':pais'      => $datos['pais'],
            ':telefono'  => $datos['telefono'],
            ':fax'       => $datos['fax'],
            ':email'     => $datos['email'],
            ':rep_nom'   => $datos['nombre_rep_legal'],
            ':rep_dni'   => $datos['dni_rep_legal'],
            ':rep_cargo' => $datos['cargo_rep_legal'],
            ':id'        => $id
        ]);
    } catch (PDOException $e) {
        // Opcional: error_log($e->getMessage());
        return false;
    }
}

}

?>