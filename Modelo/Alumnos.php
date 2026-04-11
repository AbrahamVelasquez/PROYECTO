<?php
require_once "./Core/Conexion.php";

class Alumnos {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    public function listarPorCiclo($idCiclo, $busqueda = '', $estadoFiltro = '', $ordenar = '', $misConveniosIds = []) {
        // Base de la consulta - Se añade asig.enviado
        $query = "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo, a.correo,
                            asig.id_asignacion, asig.id_convenio, asig.fecha_inicio, asig.fecha_final, 
                            asig.horario, asig.horas_dia, asig.enviado,
                            conv.nombre_empresa, conv.municipio, conv.direccion,
                            (f.id_firmada IS NOT NULL) as firmado 
                    FROM alumnos a
                    LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                    LEFT JOIN asignaciones_firmadas f ON asig.id_asignacion = f.id_asignacion
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

    // En lo siguente, 0 significa sin asignar, 1 en proceso y 2 completado    
    // Lógica de ordenación mejorada:
    // 1. Prioridad por Estado (Sin asignar > En proceso > Completado)
    // 2. Dentro de Completados: (No enviado/No firmado > Enviado/No firmado > Todo firmado)
    $estado = " ORDER BY
        /* Primero: Estado general */
        CASE 
            WHEN asig.id_convenio IS NULL THEN 0
            WHEN (
                asig.fecha_inicio IS NULL OR asig.fecha_inicio = '0000-00-00' OR
                asig.fecha_final  IS NULL OR asig.fecha_final  = '0000-00-00' OR
                asig.horario      IS NULL OR asig.horario      = ''           OR
                asig.horas_dia    IS NULL OR asig.horas_dia    = 0            OR
                conv.direccion    IS NULL OR conv.direccion    = ''
            ) THEN 1
            ELSE 2
        END ASC,
        
        /* Segundo: Prioridad de gestión de firmas (Solo afecta a los del bloque 2) */
        CASE
            WHEN (asig.enviado = 0 AND (f.id_firmada IS NULL)) THEN 0  -- Pendiente total
            WHEN (asig.enviado = 1 AND (f.id_firmada IS NULL)) THEN 1  -- Solo enviado
            ELSE 2                                                     -- Todo firmado (al final)
        END ASC,

        /* Tercero: Alfabético para desempatar */
        a.apellido1 ASC, a.apellido2 ASC";

    switch ($ordenar) {
        case 'nombre':
            $query .= " ORDER BY a.apellido1, a.apellido2, a.nombre";
            break;

        case 'mis_convenios':
            $query .= " ORDER BY 
                        CASE WHEN conv.id_convenio IS NULL THEN 1 ELSE 0 END ASC, 
                        conv.nombre_empresa ASC, 
                        a.apellido1 ASC, 
                        a.nombre ASC";
            break;

        case 'empresa':
            $query .= " ORDER BY conv.nombre_empresa ASC, a.apellido1";
            break;

        case 'fecha_inicio':
            $query .= " ORDER BY asig.fecha_inicio DESC, a.apellido1";
            break;

        case 'fecha_final':
            $query .= " ORDER BY asig.fecha_final DESC, a.apellido1";
            break;

        case 'estado':
        default:
            // Al ponerlos así juntos, tanto si pides 'estado' como si no pides nada (default),
            // se aplicará la variable $estado que definiste arriba.
            $query .= $estado; 
            break;
    }

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
    $query = "SELECT a.*, 
                     asig.id_asignacion, asig.id_convenio, asig.fecha_inicio, 
                     asig.fecha_final, asig.horario, asig.horas_dia, 
                     IFNULL(asig.enviado, 0) as enviado 
              FROM alumnos a
              LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
              WHERE a.id_alumno = :idAlumno";
    try {
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['idAlumno' => (int)$idAlumno]); // Forzamos entero
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si resultado es false, es que ni siquiera encontró al alumno en la tabla 'alumnos'
        return $resultado ?: null; 
    } catch (PDOException $e) {
        return null;
    }
}

    public function editarAlumno($idAlumno, $nombre, $apellido1, $apellido2, $dni, $sexo, $correo,
                                $idConvenio, $fechaInicio, $fechaFinal, $horario, $horasDia, $enviado = 0) {
        try {
            // 1. Actualizar datos básicos del alumno
            $q1 = "UPDATE alumnos SET nombre=:nombre, apellido1=:apellido1, apellido2=:apellido2,
                    dni=:dni, sexo=:sexo, correo=:correo WHERE id_alumno=:idAlumno";
            $stmt = $this->conn->prepare($q1);
            $stmt->execute([
                'nombre'    => $nombre, 
                'apellido1' => $apellido1, 
                'apellido2' => $apellido2,
                'dni'       => $dni, 
                'sexo'      => $sexo, 
                'correo'    => $correo, 
                'idAlumno'  => $idAlumno
            ]);

            // 2. ¿Tiene ya asignación?
            $qCheck = "SELECT id_asignacion FROM asignaciones WHERE id_alumno = :idAlumno";
            $stmtCheck = $this->conn->prepare($qCheck);
            $stmtCheck->execute(['idAlumno' => $idAlumno]);
            $asignacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($asignacion) {
                // UPDATE asignación existente (incluyendo el campo enviado)
                $q2 = "UPDATE asignaciones SET id_convenio=:idConvenio, fecha_inicio=:fechaInicio,
                        fecha_final=:fechaFinal, horario=:horario, horas_dia=:horasDia, enviado=:enviado
                        WHERE id_alumno=:idAlumno";
            } else {
                // INSERT nueva asignación (incluyendo el campo enviado)
                $q2 = "INSERT INTO asignaciones (id_alumno, id_convenio, fecha_inicio, fecha_final, horario, horas_dia, enviado)
                        VALUES (:idAlumno, :idConvenio, :fechaInicio, :fechaFinal, :horario, :horasDia, :enviado)";
            }

            $stmt2 = $this->conn->prepare($q2);
            $stmt2->execute([
                'idAlumno'    => $idAlumno, 
                'idConvenio'  => $idConvenio ?: null,
                'fechaInicio' => $fechaInicio ?: null, 
                'fechaFinal'  => $fechaFinal ?: null,
                'horario'     => $horario ?: null, 
                'horasDia'    => $horasDia ?: null,
                'enviado'     => $enviado // <--- Nuevo valor capturado
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
        
public function marcarComoEnviado($idAlumno) {
    try {
        // Usamos id_alumno porque es tu clave foránea en la tabla asignaciones
        $sql = "UPDATE asignaciones SET enviado = 1 WHERE id_alumno = :id";
        $stmt = $this->conn->prepare($sql);
        $resultado = $stmt->execute(['id' => (int)$idAlumno]);
        
        // Debug opcional: Si no funciona, podrías verificar si el rowCount es > 0
        return $resultado;
    } catch (PDOException $e) {
        return false;
    }
}

public function firmarAsignacion($idAsignacion) {
    try {
        // Usamos INSERT IGNORE: si el id_asignacion ya existe, 
        // simplemente no hace nada y no devuelve error.
        $sql = "INSERT IGNORE INTO asignaciones_firmadas (id_asignacion) VALUES (:id)";
        
        $stmt = $this->conn->prepare($sql);
        
        // Ejecutamos pasando el ID. 
        // Si todo va bien, devuelve true.
        return $stmt->execute(['id' => $idAsignacion]);

    } catch (PDOException $e) {
        // En caso de error de base de datos, podrías registrar el error:
        // error_log($e->getMessage()); 
        return false;
    }
}

public function comprobarFirmaExistente($idAsig) {
    try {
        $sql = "SELECT COUNT(*) as total FROM asignaciones_firmadas WHERE id_asignacion = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $idAsig]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'] > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// eliminarAsignacion: Para borrar el registro de la tabla de la derecha.
// actualizarDatosBasicos: Para que si el tutor cambia el nombre y a la 
// vez quita el convenio, el nombre se guarde de todos modos.

public function eliminarAsignacion($idAlumno) {
    $sql = "DELETE FROM asignaciones WHERE id_alumno = :id";
    return $this->conn->prepare($sql)->execute(['id' => $idAlumno]);
}

public function actualizarDatosBasicos($id, $nom, $ap1, $ap2, $dni, $sex, $mail) {
    $sql = "UPDATE alumnos SET 
            nombre = :nom, apellido1 = :ap1, apellido2 = :ap2, 
            dni = :dni, sexo = :sex, correo = :mail 
            WHERE id_alumno = :id";
    return $this->conn->prepare($sql)->execute([
        'nom' => $nom, 'ap1' => $ap1, 'ap2' => $ap2, 
        'dni' => $dni, 'sex' => $sex, 'mail' => $mail, 'id' => $id
    ]);
}

}