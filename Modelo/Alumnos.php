<?php
require_once "./Core/Conexion.php";

class Alumnos {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    public function listarPorCiclo($idCiclo, $busqueda = '', $estadoFiltro = '') {
        // Base de la consulta
        $query = "SELECT 
                    a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo, a.correo,
                    asig.id_asignacion, asig.fecha_inicio, asig.fecha_final, asig.horario, asig.horas_dia,
                    conv.nombre_empresa, conv.id_convenio, conv.direccion, conv.municipio
                  FROM alumnos a
                  LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                  LEFT JOIN convenios conv ON asig.id_convenio = conv.id_convenio
                  WHERE a.id_ciclo = :idCiclo";

        // Filtro por texto (Nombre, Apellidos o DNI)
        if (!empty($busqueda)) {
            $query .= " AND (a.nombre LIKE :busq OR a.apellido1 LIKE :busq OR a.apellido2 LIKE :busq OR a.dni LIKE :busq)";
        }

        // Filtro por Estado (Misma lógica que usas en la Vista)
        if ($estadoFiltro === 'SIN ASIGNAR') {
            $query .= " AND asig.id_asignacion IS NULL";
        } elseif ($estadoFiltro === 'COMPLETADO') {
            $query .= " AND asig.id_asignacion IS NOT NULL 
                        AND asig.fecha_inicio IS NOT NULL 
                        AND asig.horario IS NOT NULL 
                        AND conv.direccion IS NOT NULL";
        } elseif ($estadoFiltro === 'EN PROCESO') {
            $query .= " AND asig.id_asignacion IS NOT NULL 
                        AND (asig.fecha_inicio IS NULL OR asig.horario IS NULL OR conv.direccion IS NULL)";
        }

        $query .= " ORDER BY a.apellido1, a.apellido2, a.nombre";

        try {
            $stmt = $this->conn->prepare($query);
            
            // Preparamos los parámetros dinámicamente
            $params = ['idCiclo' => $idCiclo];
            if (!empty($busqueda)) {
                $params['busq'] = '%' . $busqueda . '%';
            }

            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
}