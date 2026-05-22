<?php

/**
 * Modelo/Alumnos.php — Gestión completa de alumnos y sus asignaciones
 *
 * Es el modelo más complejo del sistema. Maneja el ciclo de vida completo
 * de un alumno: alta, edición, asignación a convenio, firma y exportación.
 *
 * Responsabilidades principales:
 *   - Paso 2: listado con filtros avanzados, alta, edición y eliminación
 *   - Paso 3: listado de alumnos firmados para el Plan Formativo
 *   - Paso 3: transacción de exportación (actualiza alumnos + convenios + asignaciones)
 *   - Admin:  listado de alumnos enviados pendientes de firma
 *
 * La lógica de ordenación del listado principal (listarPorCiclo) es deliberadamente
 * compleja: prioriza alumnos sin asignar, luego en proceso, luego firmados.
 * Dentro de cada grupo, el orden secundario gestiona el flujo de firmas.
 *
 * MVC: Modelo. Gestiona las tablas `alumnos`, `asignaciones`, `asignaciones_firmadas`,
 * `curso_academico`, `convenios`, `ciclos` y `cursos`.
 */

require_once "./Core/Conexion.php";

class Alumnos {

    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    // ═══════════════════════════════════════════════════════════════════
    // PASO 2 — ALUMNOS  (Vista: Steps/Alumnos.php)
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Lista todos los alumnos de un ciclo con sus asignaciones.
     * Aplica filtros de búsqueda, estado y ordenación.
     * Usado en el Paso 2 — Alumnos para poblar la tabla principal.
     */
    public function listarPorCiclo($idCiclo, $busqueda = '', $estadoFiltro = '', $ordenar = '', $misConveniosIds = []) {
        // Base de la consulta - Se añade asig.enviado
        $query = "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo, a.correo,
                                asig.id_asignacion, asig.num_convenio, asig.fecha_inicio, asig.fecha_final, 
                                asig.horario, asig.horario_excepciones, asig.horas_dia, asig.num_total_horas, asig.enviado,
                                conv.nombre_empresa, conv.localidad, conv.direccion,
                                (f.id_firmada IS NOT NULL) as firmado 
                        FROM alumnos a
                        INNER JOIN curso_academico ca ON a.id_alumno = ca.id_alumno
                        LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                        LEFT JOIN asignaciones_firmadas f ON asig.id_asignacion = f.id_asignacion
                        LEFT JOIN convenios conv ON asig.num_convenio = conv.num_convenio
                        WHERE ca.id_ciclo = :idCiclo";

        // Filtro por texto (Nombre, Apellidos o DNI)
        if (!empty($busqueda)) {
            $query .= " AND (a.nombre LIKE :busq OR a.apellido1 LIKE :busq OR a.apellido2 LIKE :busq OR a.dni LIKE :busq
                         OR CONCAT(a.apellido1, ' ', COALESCE(a.apellido2,''), ', ', a.nombre) LIKE :busq)";
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
                WHEN asig.num_convenio IS NULL THEN 0
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
                            CASE WHEN conv.num_convenio IS NULL THEN 1 ELSE 0 END ASC, 
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

    /**
     * Devuelve los datos completos de un alumno (datos personales + asignación).
     * Usado al abrir el modal de edición del Paso 2.
     */
    public function obtenerPorId($idAlumno) {
        $query = "SELECT a.*,
                        asig.id_asignacion, asig.num_convenio, asig.fecha_inicio,
                        asig.fecha_final, asig.horario, asig.horas_dia, asig.num_total_horas,
                        IFNULL(asig.enviado, 0) as enviado,
                        asig.nombre_tutor_empresa, asig.correo_tutor_empresa, asig.tel_tutor_empresa,
                        asig.horario_excepciones
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

    /**
     * Da de alta un alumno nuevo en las tablas `alumnos` y `curso_academico`.
     * Opera en transacción para garantizar que ambas inserciones sean atómicas.
     */
    public function agregarAlumno($nombre, $apellido1, $apellido2, $dni = '', $sexo  = '', $correo, $telefono  = '', $idCiclo, $anioInicio = null) {
        // Verificar DNI duplicado ANTES de abrir la transacción para evitar
        // que la excepción de integridad referencial escape al manejador global.
        if (!empty($dni)) {
            $chk = $this->conn->prepare("SELECT COUNT(*) FROM alumnos WHERE dni = :dni");
            $chk->execute([':dni' => $dni]);
            if ((int)$chk->fetchColumn() > 0) {
                return 'dni_duplicado';
            }
        }

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

            // 2. Insertar en curso_academico con el año elegido (o el actual por defecto)
            $anioInicio = $anioInicio ? (int)$anioInicio : (int)date('Y');
            $query2 = "INSERT INTO curso_academico (id_alumno, id_ciclo, anio_inicio, anio_fin)
                    VALUES (:idAlumno, :idCiclo, :inicio, :fin)";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->execute([
                'idAlumno' => $lastId,
                'idCiclo'  => $idCiclo,
                'inicio'   => $anioInicio,
                'fin'      => $anioInicio + 1
            ]);

            $this->conn->commit();
            return true;

        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                try { $this->conn->rollBack(); } catch (\Throwable $r) {}
            }
            $msg = $e->getMessage();
            $code = (string)$e->getCode();
            $mysqlCode = method_exists($e, 'errorInfo') ? (int)(($e->errorInfo ?? [])[1] ?? 0) : 0;
            if ($code === '23000' || $mysqlCode === 1062 || str_contains($msg, '1062') || str_contains($msg, "key 'dni'")) {
                return 'dni_duplicado';
            }
            return false;
        }
    }

    /**
     * Actualiza los datos personales del alumno y su asignación a convenio.
     * Si ya tiene asignación hace UPDATE; si no, hace INSERT.
     * Opera en transacción cruzando las tablas `alumnos` y `asignaciones`.
     */
    public function editarAlumno($idAlumno, $nombre, $apellido1, $apellido2, $dni  = '', $sexo  = '', $correo, $telefono  = '',
                                $idConvenio, $fechaInicio, $fechaFinal, $horario, $horasDia, $horasTotales = null, $enviado = 0,
                                $nombreTutorEmpresa = null, $correoTutorEmpresa = null, $telTutorEmpresa = null,
                                $horarioExcepciones = null) {
        // Pre-check: DNI ya usado por OTRO alumno distinto al que editamos
        if (!empty($dni)) {
            $chk = $this->conn->prepare("SELECT COUNT(*) FROM alumnos WHERE dni = :dni AND id_alumno != :id");
            $chk->execute([':dni' => $dni, ':id' => $idAlumno]);
            if ((int)$chk->fetchColumn() > 0) {
                return 'dni_duplicado';
            }
        }

        try {
            $this->conn->beginTransaction();

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
                $q2 = "UPDATE asignaciones SET num_convenio=:idConvenio, fecha_inicio=:fechaInicio,
                        fecha_final=:fechaFinal, horario=:horario, horas_dia=:horasDia, num_total_horas=:horasTotales,
                        enviado=:enviado, nombre_tutor_empresa=:nombreTutorEmpresa, correo_tutor_empresa=:correoTutorEmpresa,
                        tel_tutor_empresa=:telTutorEmpresa, horario_excepciones=:horarioExcepciones
                        WHERE id_alumno=:idAlumno";
            } else {
                // INSERT nueva asignación
                $q2 = "INSERT INTO asignaciones (id_alumno, num_convenio, fecha_inicio, fecha_final, horario, horas_dia, num_total_horas, enviado, nombre_tutor_empresa, correo_tutor_empresa, tel_tutor_empresa, horario_excepciones)
                        VALUES (:idAlumno, :idConvenio, :fechaInicio, :fechaFinal, :horario, :horasDia, :horasTotales, :enviado, :nombreTutorEmpresa, :correoTutorEmpresa, :telTutorEmpresa, :horarioExcepciones)";
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
                'horarioExcepciones'   => $horarioExcepciones ?: null,
            ]);

            $this->conn->commit(); // Si todo salió bien, guardamos cambios
            return true;

        } catch (\PDOException $e) {
            if ($this->conn->inTransaction()) {
                try { $this->conn->rollBack(); } catch (\Throwable $r) {}
            }
            $mysqlCode = (int)($e->errorInfo[1] ?? 0);
            if ((string)$e->getCode() === '23000' || $mysqlCode === 1062) {
                return 'dni_duplicado';
            }
            return false;
        }
    }



    // eliminarAsignacion y actualizarDatosBasicos trabajan juntos cuando el tutor
    // quita el convenio de un alumno: primero se borran los datos de asignación
    // y luego se actualizan solo los datos personales del alumno.


    /**
     * Actualiza solo los datos personales del alumno (sin tocar la asignación).
     * Se usa cuando el tutor quita el convenio y solo guarda nombre/DNI/etc.
     */
    public function actualizarDatosBasicos($id, $nom, $ap1, $ap2, $dni, $sex, $mail, $tel) {
        // Pre-check: DNI ya usado por OTRO alumno
        if (!empty($dni)) {
            $chk = $this->conn->prepare("SELECT COUNT(*) FROM alumnos WHERE dni = :dni AND id_alumno != :id");
            $chk->execute([':dni' => $dni, ':id' => $id]);
            if ((int)$chk->fetchColumn() > 0) {
                return 'dni_duplicado';
            }
        }

        $sql = "UPDATE alumnos SET nombre=:nom, apellido1=:ap1, apellido2=:ap2,
                    dni=:dni, sexo=:sex, correo=:mail, telefono=:tel
                WHERE id_alumno=:id";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'nom'=>$nom, 'ap1'=>$ap1, 'ap2'=>$ap2, 'dni'=>$dni,
                'sex'=>$sex, 'mail'=>$mail, 'tel'=>$tel, 'id'=>$id
            ]);
        } catch (\PDOException $e) {
            $mysqlCode = (int)($e->errorInfo[1] ?? 0);
            if ((string)$e->getCode() === '23000' || $mysqlCode === 1062) {
                return 'dni_duplicado';
            }
            return false;
        }
    }


    // ═══════════════════════════════════════════════════════════════════
    // PASO 3 — PLAN FORMATIVO  (Vista: Steps/Plan_Formativo.php)
    // También utilizado por Paso 4 — Seguimiento
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Lista los alumnos que tienen la asignación firmada.
     * Fuente de datos para el Paso 3 (Plan Formativo) y el Paso 4 (Seguimiento).
     */
    public function listarAlumnosFirmados($idCiclo) {

        $sql = "SELECT a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.correo, a.telefono,
                        f.id_asignacion,
                        asig.num_convenio,
                        asig.horario,
                        asig.horario_excepciones,
                        asig.num_total_horas,
                        asig.fecha_inicio,
                        asig.fecha_final,
                        conv.nombre_empresa,
                        conv.cif AS nif_empresa,
                        asig.correo_tutor_empresa AS email_empresa,
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
                LEFT JOIN convenios conv ON asig.num_convenio = conv.num_convenio
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


    /**
     * Operación de exportación del plan formativo: actualiza datos del alumno,
     * del convenio, de la asignación y marca como exportado en una sola transacción.
     * Es el método más complejo del modelo — cruza cuatro tablas.
     */
    public function actualizarTodoYExportar($idAsignacion, $datos) {
        try {
            $this->conn->beginTransaction();

            // 1. Obtener IDs relacionados
            $stmt = $this->conn->prepare("SELECT id_alumno, num_convenio FROM asignaciones WHERE id_asignacion = ?");
            $stmt->execute([$idAsignacion]);
            $relaciones = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$relaciones) return false;

            $idAlu = $relaciones['id_alumno'];
            $idConv = $relaciones['num_convenio'];

            // Solo actualizar datos si vienen del formulario de edición individual
            $tieneFormData = isset($datos['email_alumno']) || isset($datos['nombre_empresa']);

            if ($tieneFormData) {
                // 2. Actualizar ALUMNOS
                $sqlAlu = "UPDATE alumnos SET correo = ?, telefono = ? WHERE id_alumno = ?";
                $this->conn->prepare($sqlAlu)->execute([$datos['email_alumno'] ?? null, $datos['tel_alumno'] ?? null, $idAlu]);

                // 3. Actualizar CONVENIOS (nombre, cif y telefono; representante es el nombre del rep. de empresa, no el correo)
                $sqlConv = "UPDATE convenios SET nombre_empresa = ?, cif = ?, telefono = ? WHERE num_convenio = ?";
                $this->conn->prepare($sqlConv)->execute([$datos['nombre_empresa'] ?? null, $datos['nif_empresa'] ?? null, $datos['tel_empresa'] ?? null, $idConv]);

                // 4. Actualizar ASIGNACIONES (Tutor empresa)
                $sqlAsig = "UPDATE asignaciones SET 
                                nombre_tutor_empresa = ?, 
                                correo_tutor_empresa = ?, 
                                tel_tutor_empresa = ?, 
                                horario = ?, 
                                num_total_horas = ?,
                                fecha_inicio = ?,
                                fecha_final = ?,
                                dias_semana = ?
                            WHERE id_asignacion = ?";

                $this->conn->prepare($sqlAsig)->execute([
                    $datos['tutor_empresa']                              ?? null,
                    $datos['email_empresa'] ?? $datos['email_tutor_emp'] ?? null,
                    $datos['tel_tutor_emp']   ?? null,
                    $datos['horario']         ?? null,
                    $datos['horas_totales'] !== '' ? ($datos['horas_totales'] ?? null) : null,
                    $datos['fecha_inicio']    ?? null,
                    $datos['fecha_final']     ?? null,
                    $datos['dias_semana']     ?? null,
                    $idAsignacion
                ]);
            }

            // 5. Marcar como exportado
            if (empty($datos['solo_borrador'])) {
                // Si viene anexo en el POST lo actualizamos, si no, dejamos el que ya hay en BD
                if (isset($datos['anexo']) && $datos['anexo'] !== '') {
                    $sqlExp = "UPDATE asignaciones_firmadas SET exportado = 1, anexo = ? WHERE id_asignacion = ?";
                    $this->conn->prepare($sqlExp)->execute([$datos['anexo'], $idAsignacion]);
                } else {
                    $sqlExp = "UPDATE asignaciones_firmadas SET exportado = 1 WHERE id_asignacion = ?";
                    $this->conn->prepare($sqlExp)->execute([$idAsignacion]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            return false;
        }
    }
    
    // Obtiene los módulos vinculados directamente al ID del ciclo

    // Obtiene los RAs guardados para los módulos de un ciclo específico
   
    // ═══════════════════════════════════════════════════════════════════
    // ADMIN — LISTADO DE ALUMNOS  (Vista: Admin/Sections/Listado_Alumnos.php)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Alumnos con asignación enviada pero sin firma todavía.
     * Usado por el panel del admin para gestionar la firma masiva.
     */
    /**
     * Lista los alumnos enviados pero aún sin firma para el panel del admin.
     * Cruza alumnos, asignaciones, convenios, ciclos y cursos en un solo SELECT.
     */
    /**
     * Elimina un alumno SOLO si no tiene asignación activa.
     * Las tablas hijas (curso_academico, etc.) se borran por CASCADE en la BD.
     */
    public function eliminarAlumno($idAlumno) {
        $check = $this->conn->prepare(
            "SELECT COUNT(*) FROM asignaciones WHERE id_alumno = :id"
        );
        $check->execute([':id' => $idAlumno]);
        if ((int) $check->fetchColumn() > 0) {
            return 'tiene_asignacion';
        }

        $stmt = $this->conn->prepare("DELETE FROM alumnos WHERE id_alumno = :id");
        $stmt->execute([':id' => $idAlumno]);
        return $stmt->rowCount() > 0;
    }

    public function obtenerAlumnosPendientesFirma() {
        $sql = "SELECT
                    al.id_alumno, al.nombre, al.apellido1, al.apellido2,
                    al.dni, al.sexo, al.correo,
                    asig.id_asignacion, asig.num_convenio,
                    asig.fecha_inicio, asig.fecha_final,
                    asig.horario, asig.horario_excepciones,
                    asig.horas_dia, asig.num_total_horas,
                    conv.nombre_empresa, conv.direccion, conv.localidad,
                    ci.nombre_ciclo, ci.grado, cu.nombre_curso
                FROM asignaciones asig
                JOIN alumnos al         ON asig.id_alumno    = al.id_alumno
                JOIN convenios conv     ON asig.num_convenio = conv.num_convenio
                JOIN curso_academico ca ON ca.id_alumno      = al.id_alumno
                JOIN ciclos ci          ON ca.id_ciclo       = ci.id_ciclo
                JOIN cursos cu          ON ci.id_curso       = cu.id_curso
                WHERE asig.enviado = 1
                AND asig.id_asignacion NOT IN (SELECT id_asignacion FROM asignaciones_firmadas)
                ORDER BY ci.nombre_ciclo ASC, al.apellido1 ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Firma de asignación desde el panel de admin.
     * A diferencia de la firma del tutor (INSERT IGNORE), aquí el admin
     * siempre registra una firma nueva con anexo obligatorio.
     */


    private function calcularHorasDia($inicio, $fin) {
        $inicioDt = DateTime::createFromFormat('H:i', $inicio);
        $finDt    = DateTime::createFromFormat('H:i', $fin);
        if ($inicioDt && $finDt) {
            $diff = $inicioDt->diff($finDt);
            return $diff->h + ($diff->i / 60);
        }
        return 0;
    }

    public function formatearDiasYHoras($horarioSimple, $horarioExcepcionesJson, $horasDiaDefault = null) {
        $resultados = [];
        if (!empty($horarioExcepcionesJson)) {
            $bloques = json_decode($horarioExcepcionesJson, true);
            if (is_array($bloques) && !empty($bloques)) {
                $ORDEN = ['L'=>0,'M'=>1,'X'=>2,'J'=>3,'V'=>4,'S'=>5,'D'=>6];
                foreach ($bloques as $bloque) {
                    if (empty($bloque['dias']) || empty($bloque['inicio']) || empty($bloque['fin'])) continue;
                    $dias = $bloque['dias'];
                    usort($dias, fn($a, $b) => ($ORDEN[$a] ?? 7) - ($ORDEN[$b] ?? 7));
                    $esConsecutivo = true;
                    for ($i = 1; $i < count($dias); $i++) {
                        $prev = $ORDEN[$dias[$i-1]] ?? -1;
                        $curr = $ORDEN[$dias[$i]] ?? -1;
                        if ($curr !== $prev + 1) { $esConsecutivo = false; break; }
                    }
                    $labelDias = (count($dias) > 1 && $esConsecutivo)
                        ? $dias[0] . '-' . $dias[count($dias)-1]
                        : implode('', $dias);
                    $horas = $this->calcularHorasDia($bloque['inicio'], $bloque['fin']);
                    $resultados[] = "$labelDias " . round($horas, 1) . "h";
                }
                return implode(', ', $resultados);
            }
        }
        if (!empty($horarioSimple) && !empty($horasDiaDefault)) {
            return 'L-V ' . round($horasDiaDefault, 1) . 'h';
        }
        return '';
    }

} // Llave de la clase

?>