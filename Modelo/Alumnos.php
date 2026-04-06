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
        } 
        elseif ($estadoFiltro === 'COMPLETADO') {
            $query .= " AND asig.id_asignacion IS NOT NULL 
                        AND asig.fecha_inicio IS NOT NULL AND asig.fecha_inicio != '0000-00-00'
                        AND asig.horario IS NOT NULL AND asig.horario != ''
                        AND conv.direccion IS NOT NULL AND conv.direccion != ''";
        } 
        elseif ($estadoFiltro === 'EN PROCESO') {
            $query .= " AND asig.id_asignacion IS NOT NULL 
                        AND (
                            asig.fecha_inicio IS NULL OR asig.fecha_inicio = '0000-00-00' 
                            OR asig.horario IS NULL OR asig.horario = '' 
                            OR conv.direccion IS NULL OR conv.direccion = ''
                        )";
        }

        $query .= " ORDER BY (
            CASE 
                WHEN asig.id_asignacion IS NULL THEN 1 
                WHEN (asig.fecha_inicio IS NULL OR asig.fecha_inicio = '0000-00-00' 
                    OR asig.horario IS NULL OR asig.horario = '' 
                    OR conv.direccion IS NULL OR conv.direccion = '') THEN 2 
                ELSE 3 
            END
        ) ASC, a.apellido1 ASC, a.apellido2 ASC, a.nombre ASC";

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
    public function agregarAlumno($nombre, $apellido1, $apellido2, $dni, $sexo, $correo, $idCiclo) {
        $query = "INSERT INTO alumnos (nombre, apellido1, apellido2, dni, sexo, correo, id_ciclo) 
                VALUES (:nombre, :apellido1, :apellido2, :dni, :sexo, :correo, :idCiclo)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'nombre'    => $nombre,
                'apellido1' => $apellido1,
                'apellido2' => $apellido2,
                'dni'       => $dni,
                'sexo'      => $sexo,
                'correo'    => $correo,
                'idCiclo'   => $idCiclo
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

public function obtenerPorId($idAlumno) {
    $query = "SELECT a.*, asig.id_asignacion, asig.id_convenio, asig.fecha_inicio, 
                     asig.fecha_final, asig.horario, asig.horas_dia
              FROM alumnos a
              LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
              WHERE a.id_alumno = :idAlumno";
    try {
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['idAlumno' => $idAlumno]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

public function editarAlumno($idAlumno, $nombre, $apellido1, $apellido2, $dni, $sexo, $correo,
                              $idConvenio, $fechaInicio, $fechaFinal, $horario, $horasDia) {
    try {
        // 1. Actualizar datos del alumno
        $q1 = "UPDATE alumnos SET nombre=:nombre, apellido1=:apellido1, apellido2=:apellido2,
                dni=:dni, sexo=:sexo, correo=:correo WHERE id_alumno=:idAlumno";
        $stmt = $this->conn->prepare($q1);
        $stmt->execute([
            'nombre' => $nombre, 'apellido1' => $apellido1, 'apellido2' => $apellido2,
            'dni' => $dni, 'sexo' => $sexo, 'correo' => $correo, 'idAlumno' => $idAlumno
        ]);

        // 2. ¿Tiene ya asignación?
        $qCheck = "SELECT id_asignacion FROM asignaciones WHERE id_alumno = :idAlumno";
        $stmtCheck = $this->conn->prepare($qCheck);
        $stmtCheck->execute(['idAlumno' => $idAlumno]);
        $asignacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($asignacion) {
            // UPDATE asignación existente
            $q2 = "UPDATE asignaciones SET id_convenio=:idConvenio, fecha_inicio=:fechaInicio,
                   fecha_final=:fechaFinal, horario=:horario, horas_dia=:horasDia
                   WHERE id_alumno=:idAlumno";
        } else {
            // INSERT nueva asignación
            $q2 = "INSERT INTO asignaciones (id_alumno, id_convenio, fecha_inicio, fecha_final, horario, horas_dia)
                   VALUES (:idAlumno, :idConvenio, :fechaInicio, :fechaFinal, :horario, :horasDia)";
        }

        $stmt2 = $this->conn->prepare($q2);
        $stmt2->execute([
            'idAlumno' => $idAlumno, 'idConvenio' => $idConvenio ?: null,
            'fechaInicio' => $fechaInicio ?: null, 'fechaFinal' => $fechaFinal ?: null,
            'horario' => $horario ?: null, 'horasDia' => $horasDia ?: null
        ]);

        return true;
    } catch (PDOException $e) {
        return false;
    }
}
}