<?php
require_once "./Core/Conexion.php";

class Alumnos {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    public function listarPorCiclo($idCiclo) {
        $query = "SELECT 
                    a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo,
                    asig.id_asignacion, asig.fecha_inicio, asig.fecha_final, asig.horario, asig.horas_dia,
                    conv.nombre_empresa, conv.id_convenio, conv.direccion, conv.municipio
                  FROM alumnos a
                  LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                  LEFT JOIN convenios conv ON asig.id_convenio = conv.id_convenio
                  WHERE a.id_ciclo = :idCiclo
                  ORDER BY a.apellido1, a.apellido2, a.nombre";

        try {
            $stmt = $this->conn->prepare($query);
            // En PDO pasamos los parámetros directamente en el execute
            $stmt->execute(['idCiclo' => $idCiclo]);
            
            // fetchAll(PDO::FETCH_ASSOC) nos devuelve el array que la vista necesita
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Si hay un error, devolvemos un array vacío para que la vista no explote
            return [];
        }
    }
}