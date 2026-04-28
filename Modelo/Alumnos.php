<?php

// Modelo/Alumnos.php

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
                                asig.horario, asig.horas_dia, asig.num_total_horas, asig.enviado,
                                conv.nombre_empresa, conv.municipio, conv.direccion,
                                (f.id_firmada IS NOT NULL) as firmado 
                        FROM alumnos a
                        INNER JOIN curso_academico ca ON a.id_alumno = ca.id_alumno
                        LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                        LEFT JOIN asignaciones_firmadas f ON asig.id_asignacion = f.id_asignacion
                        LEFT JOIN convenios conv ON asig.id_convenio = conv.id_convenio
                        WHERE ca.id_ciclo = :idCiclo";

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

    public function obtenerPorId($idAlumno) {
        $query = "SELECT a.*,
                        asig.id_asignacion, asig.id_convenio, asig.fecha_inicio,
                        asig.fecha_final, asig.horario, asig.horas_dia, asig.num_total_horas,
                        IFNULL(asig.enviado, 0) as enviado,
                        asig.nombre_tutor_empresa, asig.correo_tutor_empresa, asig.tel_tutor_empresa
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

    public function agregarAlumno($nombre, $apellido1, $apellido2, $dni, $sexo, $correo, $telefono, $idCiclo) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar en alumnos (sin id_ciclo)
            $query1 = "INSERT INTO alumnos (nombre, apellido1, apellido2, dni, sexo, correo, telefono) 
                    VALUES (:nombre, :apellido1, :apellido2, :dni, :sexo, :correo, :telefono)";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->execute([
                'nombre'    => $nombre,
                'apellido1' => $apellido1,
                'apellido2' => $apellido2,
                'dni'       => $dni,
                'sexo'      => $sexo,
                'correo'    => $correo,
                'telefono'  => $telefono
            ]);

            $lastId = $this->conn->lastInsertId();

            // 2. Insertar en curso_academico (Aquí definimos el ciclo y años por defecto o actuales)
            $anioActual = (int)date('Y');
            $query2 = "INSERT INTO curso_academico (id_alumno, id_ciclo, anio_inicio, anio_fin) 
                    VALUES (:idAlumno, :idCiclo, :inicio, :fin)";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->execute([
                'idAlumno' => $lastId,
                'idCiclo'  => $idCiclo,
                'inicio'   => $anioActual,
                'fin'      => $anioActual + 1
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) { 
            $this->conn->rollBack();
            return false; 
        }
    }

    public function editarAlumno($idAlumno, $nombre, $apellido1, $apellido2, $dni, $sexo, $correo, $telefono,
                                $idConvenio, $fechaInicio, $fechaFinal, $horario, $horasDia, $horasTotales = null, $enviado = 0,
                                $nombreTutorEmpresa = null, $correoTutorEmpresa = null, $telTutorEmpresa = null) {
        try {
            $this->conn->beginTransaction(); // Iniciamos transacción por seguridad

            // 1. Actualizar datos personales
            $q1 = "UPDATE alumnos SET nombre=:nombre, apellido1=:apellido1, apellido2=:apellido2,
                    dni=:dni, sexo=:sexo, correo=:correo, telefono=:telefono WHERE id_alumno=:idAlumno";
            $stmt = $this->conn->prepare($q1);
            $stmt->execute([
                'nombre'    => $nombre, 
                'apellido1' => $apellido1, 
                'apellido2' => $apellido2,
                'dni'       => $dni, 
                'sexo'      => $sexo, 
                'correo'    => $correo, 
                'telefono'  => $telefono, 
                'idAlumno'  => $idAlumno
            ]);

            // 2. ¿Tiene ya asignación?
            $qCheck = "SELECT id_asignacion FROM asignaciones WHERE id_alumno = :idAlumno";
            $stmtCheck = $this->conn->prepare($qCheck);
            $stmtCheck->execute(['idAlumno' => $idAlumno]);
            $asignacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($asignacion) {
                // UPDATE asignación existente
                $q2 = "UPDATE asignaciones SET id_convenio=:idConvenio, fecha_inicio=:fechaInicio,
                        fecha_final=:fechaFinal, horario=:horario, horas_dia=:horasDia, num_total_horas=:horasTotales, 
                        enviado=:enviado, nombre_tutor_empresa=:nombreTutorEmpresa, correo_tutor_empresa=:correoTutorEmpresa,
                        tel_tutor_empresa=:telTutorEmpresa
                        WHERE id_alumno=:idAlumno";
            } else {
                // INSERT nueva asignación
                $q2 = "INSERT INTO asignaciones (id_alumno, id_convenio, fecha_inicio, fecha_final, horario, horas_dia, num_total_horas, enviado, nombre_tutor_empresa, correo_tutor_empresa, tel_tutor_empresa)
                        VALUES (:idAlumno, :idConvenio, :fechaInicio, :fechaFinal, :horario, :horasDia, :horasTotales, :enviado, :nombreTutorEmpresa, :correoTutorEmpresa, :telTutorEmpresa)";
            }

            $stmt2 = $this->conn->prepare($q2);
            $stmt2->execute([
                'idAlumno'             => $idAlumno,
                'idConvenio'           => $idConvenio ?: null,
                'fechaInicio'          => $fechaInicio ?: null,
                'fechaFinal'           => $fechaFinal ?: null,
                'horario'              => $horario ?: null,
                'horasDia'             => $horasDia ?: null,
                'horasTotales'         => $horasTotales ?: null,
                'enviado'              => $enviado,
                'nombreTutorEmpresa'   => $nombreTutorEmpresa ?: null,
                'correoTutorEmpresa'   => $correoTutorEmpresa ?: null,
                'telTutorEmpresa'      => $telTutorEmpresa ?: null,
            ]);

            $this->conn->commit(); // Si todo salió bien, guardamos cambios
            return true;

        } catch (PDOException $e) { 
            $this->conn->rollBack(); // Si algo falla, deshacemos todo
            return false; 
        }
    }

    public function firmarAsignacion($idAsignacion, $anexo = null) {
        try {
            // 1. Aseguramos que la asignación esté en la tabla de firmas
            // Usamos INSERT IGNORE por si ya existe, para que no dé error
            $sqlInsert = "INSERT IGNORE INTO asignaciones_firmadas (id_asignacion) VALUES (:id)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->execute(['id' => $idAsignacion]);

            // 2. Si el tutor ha escrito un anexo, lo guardamos/actualizamos
            if (!empty($anexo)) {
                $sqlUpdate = "UPDATE asignaciones_firmadas SET anexo = :anexo WHERE id_asignacion = :id";
                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    'anexo' => $anexo,
                    'id' => $idAsignacion
                ]);
            }

            return true;

        } catch (PDOException $e) {
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

    public function actualizarDatosBasicos($id, $nom, $ap1, $ap2, $dni, $sex, $mail, $tel) {
        $sql = "UPDATE alumnos SET nombre=:nom, apellido1=:ap1, apellido2=:ap2, 
                    dni=:dni, sexo=:sex, correo=:mail, telefono=:tel 
                WHERE id_alumno=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'nom'=>$nom, 'ap1'=>$ap1, 'ap2'=>$ap2, 'dni'=>$dni, 
            'sex'=>$sex, 'mail'=>$mail, 'tel'=>$tel, 'id'=>$id
        ]);
    }

    public function listarAlumnosFirmados($idCiclo) {

        $sql = "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.correo, a.telefono,
                        f.id_asignacion,
                        asig.id_convenio,
                        asig.horario,
                        asig.num_total_horas,
                        asig.fecha_inicio,
                        asig.fecha_final,
                        conv.nombre_empresa, 
                        conv.cif AS nif_empresa,
                        conv.mail AS email_empresa, 
                        conv.telefono AS telefono_empresa,
                        asig.nombre_tutor_empresa, 
                        asig.correo_tutor_empresa, 
                        asig.tel_tutor_empresa,
                        ci.id_ciclo,
                        ci.nombre_ciclo,
                        cu.id_curso,
                        f.exportado,
                        f.anexo,
                        ca.anio_inicio,
                        ca.anio_fin
                FROM alumnos a
                INNER JOIN curso_academico ca ON a.id_alumno = ca.id_alumno
                INNER JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                INNER JOIN asignaciones_firmadas f ON asig.id_asignacion = f.id_asignacion
                LEFT JOIN convenios conv ON asig.id_convenio = conv.id_convenio
                INNER JOIN ciclos ci ON ca.id_ciclo = ci.id_ciclo
                INNER JOIN cursos cu ON ci.id_curso = cu.id_curso
                WHERE ca.id_ciclo = :idCiclo
                ORDER BY a.apellido1 ASC";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['idCiclo' => $idCiclo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Es bueno añadir un log si falla la consulta
            // error_log($e->getMessage());
            return [];
        }
    }

    public function devolverAlumnoAEnvio($idAlumno) {
        try {
            $this->conn->beginTransaction();

            // 1. Borrar de firmadas (Relación 1:1 con asignaciones)
            $sqlDel = "DELETE FROM asignaciones_firmadas WHERE id_asignacion = (
                        SELECT id_asignacion FROM asignaciones WHERE id_alumno = :id LIMIT 1
                    )";
            $stmtDel = $this->conn->prepare($sqlDel);
            $stmtDel->execute(['id' => $idAlumno]);

            // 2. Resetear el estado de envío
            $sqlUpd = "UPDATE asignaciones SET enviado = 0 WHERE id_alumno = :id";
            $stmtUpd = $this->conn->prepare($sqlUpd);
            $stmtUpd->execute(['id' => $idAlumno]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            return false;
        }
    }

    public function actualizarTodoYExportar($idAsignacion, $datos) {
        try {
            $this->conn->beginTransaction();

            // 1. Obtener IDs relacionados
            $stmt = $this->conn->prepare("SELECT id_alumno, id_convenio FROM asignaciones WHERE id_asignacion = ?");
            $stmt->execute([$idAsignacion]);
            $relaciones = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$relaciones) return false;

            $idAlu = $relaciones['id_alumno'];
            $idConv = $relaciones['id_convenio'];

            // Solo actualizar datos si vienen del formulario de edición individual
            $tieneFormData = isset($datos['email_alumno']) || isset($datos['nombre_empresa']);

            if ($tieneFormData) {
                // 2. Actualizar ALUMNOS
                $sqlAlu = "UPDATE alumnos SET correo = ?, telefono = ? WHERE id_alumno = ?";
                $this->conn->prepare($sqlAlu)->execute([$datos['email_alumno'] ?? null, $datos['tel_alumno'] ?? null, $idAlu]);

                // 3. Actualizar CONVENIOS
                $sqlConv = "UPDATE convenios SET nombre_empresa = ?, cif = ?, mail = ?, telefono = ? WHERE id_convenio = ?";
                $this->conn->prepare($sqlConv)->execute([$datos['nombre_empresa'] ?? null, $datos['nif_empresa'] ?? null, $datos['email_empresa'] ?? null, $datos['tel_empresa'] ?? null, $idConv]);

                // 4. Actualizar ASIGNACIONES (Tutor empresa)
                $sqlAsig = "UPDATE asignaciones SET 
                                nombre_tutor_empresa = ?, 
                                correo_tutor_empresa = ?, 
                                tel_tutor_empresa = ?, 
                                horario = ?, 
                                num_total_horas = ?,
                                fecha_inicio = ?,
                                fecha_final = ?
                            WHERE id_asignacion = ?";

                $this->conn->prepare($sqlAsig)->execute([
                    $datos['tutor_empresa']   ?? null,
                    $datos['email_tutor_emp'] ?? null,
                    $datos['tel_tutor_emp']   ?? null,
                    $datos['horario']         ?? null,
                    $datos['horas_totales'] !== '' ? ($datos['horas_totales'] ?? null) : null,
                    $datos['fecha_inicio']    ?? null,
                    $datos['fecha_final']     ?? null,
                    $idAsignacion
                ]);
            }

            // 5. Marcar como exportado
            if (empty($datos['solo_borrador'])) {
                $sqlExp = "UPDATE asignaciones_firmadas SET exportado = 1, anexo = ? WHERE id_asignacion = ?";
                $this->conn->prepare($sqlExp)->execute([$datos['anexo'] ?? null, $idAsignacion]);
            } else {
                // Solo actualiza el anexo si viene informado, sin tocar exportado
                if (!empty($datos['anexo'])) {
                    $sqlExp = "UPDATE asignaciones_firmadas SET anexo = ? WHERE id_asignacion = ?";
                    $this->conn->prepare($sqlExp)->execute([$datos['anexo'], $idAsignacion]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            return false;
        }
    }
    
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

    public function obtenerResultadosAprendizaje($idCiclo) {
        // Obtenemos los RAs de los módulos que pertenecen al ciclo del tutor
        $sql = "SELECT ra.id_ra, ra.id_modulo, ra.numero_ra, ra.impartido_empresa, ra.periodo,
                        m.nombre_modulo
                FROM resultados_aprendizaje ra
                INNER JOIN modulos m ON ra.id_modulo = m.id_modulo
                INNER JOIN plan_estudios pe ON ra.id_modulo = pe.id_modulo
                WHERE pe.id_ciclo = :idCiclo
                ORDER BY ra.periodo, m.nombre_modulo, ra.numero_ra";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['idCiclo' => (int)$idCiclo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function guardarResultadosAprendizaje($idCiclo, $rasNuevos, $raEliminados) {
        try {
            $this->conn->beginTransaction();

            // 1. Eliminar los RAs marcados para borrar (que pertenecen a módulos del ciclo)
            if (!empty($raEliminados)) {
                $placeholders = implode(',', array_fill(0, count($raEliminados), '?'));
                $sqlDel = "DELETE FROM resultados_aprendizaje 
                           WHERE id_ra IN ($placeholders)
                           AND id_modulo IN (
                               SELECT id_modulo FROM plan_estudios WHERE id_ciclo = ?
                           )";
                $params = array_merge(array_map('intval', $raEliminados), [(int)$idCiclo]);
                $this->conn->prepare($sqlDel)->execute($params);
            }

            // 2. Insertar/actualizar los RAs nuevos
            foreach ($rasNuevos as $ra) {
                $idRa    = (int)($ra['id_ra'] ?? 0);
                $idMod   = (int)($ra['id_modulo'] ?? 0);
                $periodo = $ra['periodo'] ?? '1';
                // numero_ra viene como "RA5" → extraemos el número
                $numRaw  = $ra['numero_ra'] ?? '1';
                $numero  = (int)preg_replace('/\D/', '', $numRaw);
                if ($numero < 1) $numero = 1;
                $empresa = (int)($ra['impartido_empresa'] ?? 0);

                if ($idMod <= 0) continue;

                if ($idRa > 0) {
                    // UPDATE
                    $sqlUpd = "UPDATE resultados_aprendizaje 
                               SET periodo = ?, numero_ra = ?, impartido_empresa = ?
                               WHERE id_ra = ? AND id_modulo IN (
                                   SELECT id_modulo FROM plan_estudios WHERE id_ciclo = ?
                               )";
                    $this->conn->prepare($sqlUpd)->execute([$periodo, $numero, $empresa, $idRa, (int)$idCiclo]);
                } else {
                    // INSERT
                    $sqlIns = "INSERT INTO resultados_aprendizaje (id_modulo, numero_ra, impartido_empresa, periodo)
                               VALUES (?, ?, ?, ?)";
                    $this->conn->prepare($sqlIns)->execute([$idMod, $numero, $empresa, $periodo]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            return false;
        }
    }

    public function actualizarAnexo($idAsignacion, $anexo) {
        try {
            $sql = "UPDATE asignaciones_firmadas SET anexo = ? WHERE id_asignacion = ?";
            return $this->conn->prepare($sql)->execute([
                ($anexo !== '' && $anexo !== null) ? (int)$anexo : null,
                (int)$idAsignacion
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtiene los módulos vinculados directamente al ID del ciclo
    public function obtenerModulosPorTutor($idCiclo) {
        $sql = "SELECT m.id_modulo, m.nombre_modulo 
                FROM modulos m 
                JOIN plan_estudios pe ON m.id_modulo = pe.id_modulo 
                WHERE pe.id_ciclo = ?";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCiclo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene los RAs guardados para los módulos de un ciclo específico
    public function obtenerRAsPorTutor($idCiclo) {
        $sql = "SELECT ra.*, m.nombre_modulo 
                FROM resultados_aprendizaje ra 
                JOIN modulos m ON ra.id_modulo = m.id_modulo 
                JOIN plan_estudios pe ON m.id_modulo = pe.id_modulo 
                WHERE pe.id_ciclo = ?
                ORDER BY ra.periodo ASC, m.nombre_modulo ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCiclo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
/*
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
*/

} // Llave de la clase

?>