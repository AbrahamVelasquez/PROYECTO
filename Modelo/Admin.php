<?php

// Modelo/Admin.php

require_once "./Core/Conexion.php"; // Importa la clase Conexion 

class Admin {

    private $conn; // Para que inicie la conexión asi accedemos a la BD 

    public function __construct() {
        $this -> conn = Conexion::getConexion();  // El modelo obtiene esa conexión
    }
    
    public function obtenerTutores($busqueda = '', $ordenar = 'id', $filtro_curso = '') {
        $params = [];
        $sql = "SELECT t.*, c.nombre_ciclo, cur.nombre_curso
                FROM tutores t
                LEFT JOIN ciclos c ON t.id_ciclo = c.id_ciclo
                LEFT JOIN cursos cur ON c.id_curso = cur.id_curso
                WHERE 1=1";

        // Filtro de búsqueda
        if (!empty($busqueda)) {
            $sql .= " AND (t.nombre LIKE :busq OR t.apellidos LIKE :busq OR t.dni LIKE :busq)";
            $params[':busq'] = '%' . $busqueda . '%';
        }

        // Filtro de curso
        if (!empty($filtro_curso)) {
            $sql .= " AND cur.nombre_curso = :curso";
            $params[':curso'] = $filtro_curso;
        }

        // --- ORDENACIÓN POR DEFECTO: ID ---
        switch ($ordenar) {
            case 'apellidos':
                $sql .= " ORDER BY t.apellidos ASC";
                break;
            case 'ciclo':
                $sql .= " ORDER BY cur.nombre_curso ASC, c.nombre_ciclo ASC";
                break;
            case 'id':
            default:
                $sql .= " ORDER BY t.id_tutor ASC"; // El más antiguo primero
                // Si prefieres ver los recién creados arriba, usa: ORDER BY t.id_tutor DESC
                break;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        // Corregimos 'id' por 'id_tutor' que es el nombre real en tu tabla
        $sql = "SELECT t.*, c.nombre_ciclo, cur.nombre_curso 
                FROM tutores t
                LEFT JOIN ciclos c ON t.id_ciclo = c.id_ciclo
                LEFT JOIN cursos cur ON c.id_curso = cur.id_curso
                WHERE t.id_tutor = :id";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ASEGÚRATE DE QUE ESTO ESTÉ AQUÍ DENTRO
    public function obtenerCiclosLibres() {
        $sql = "SELECT c.id_ciclo, c.nombre_ciclo, cur.nombre_curso 
                FROM ciclos c
                INNER JOIN cursos cur ON c.id_curso = cur.id_curso
                WHERE c.id_ciclo NOT IN (SELECT id_ciclo FROM tutores)
                ORDER BY cur.nombre_curso ASC, c.nombre_ciclo ASC";
                
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Crea la cuenta en la tabla usuarios y devuelve el ID generado */
    private function crearCuentaUsuario($nombre) {
        // 1. Limpiamos el nombre para el username
        $nombreLimpio = mb_strtolower(str_replace(' ', '', $nombre));
        $nuevoUsername = $nombreLimpio . "_tutor";

        // 2. Generamos la contraseña base
        $nuevaPassword = mb_substr($nombreLimpio, 0, 3) . "123";

        //////////******* FUNCIONALIDAD FUTURA ********////////////
        // Hasheamos para que sea seguro y quepa en tu varchar(255)
        // $passHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        // --- INSERCIÓN EN TABLA USUARIOS ---
        $sqlUser = "INSERT INTO usuarios (username, password, rol) VALUES (:user, :pass, 'tutor')";
        $stmtUser = $this->conn->prepare($sqlUser);

        // Ejecución actual (Texto plano para PROYECTO)
        $stmtUser->execute([
            ':user' => $nuevoUsername,
            ':pass' => $nuevaPassword
        ]);

        ///////**** DESCOMENTAR PARA QUE FUNCIONE EL HASH *****/////////
        /*
        $stmtUser->execute([
            ':user' => $nuevoUsername,
            ':pass' => $passHash
        ]);
        */

        return $this->conn->lastInsertId();
    }

    /* Función principal que coordina el proceso de guardado */
    public function guardarTutor($datos) {
        try {
            $this->conn->beginTransaction();

            // PASO 1: Crear usuario (delegamos la lógica)
            $idUsuario = $this->crearCuentaUsuario($datos['nombre']);

            // PASO 2: Insertar datos del tutor
            $sqlTutor = "INSERT INTO tutores (id_usuario, dni, nombre, apellidos, email, telefono, id_ciclo) 
                        VALUES (:id_u, :dni, :nom, :ape, :email, :tel, :id_c)";
            
            $stmtTutor = $this->conn->prepare($sqlTutor);
            $stmtTutor->execute([
                ':id_u'   => $idUsuario,
                ':dni'    => $datos['dni'],
                ':nom'    => $datos['nombre'],
                ':ape'    => $datos['apellidos'],
                ':email'  => $datos['email'],
                ':tel'    => $datos['telefono'],
                ':id_c'   => $datos['id_ciclo']
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error al crear tutor: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodosLosCiclos() {
        $sql = "SELECT c.id_ciclo, c.nombre_ciclo, cur.nombre_curso, t.id_tutor as ocupado_por
                FROM ciclos c 
                JOIN cursos cur ON c.id_curso = cur.id_curso 
                LEFT JOIN tutores t ON c.id_ciclo = t.id_ciclo
                ORDER BY cur.nombre_curso ASC, c.nombre_ciclo ASC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarTutor($datos) {
        $sql = "UPDATE tutores SET 
                nombre = :nom, 
                apellidos = :ape, 
                email = :email, 
                telefono = :tel, 
                id_ciclo = :id_c 
                WHERE id_tutor = :id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nom'   => $datos['nombre'],
            ':ape'   => $datos['apellidos'],
            ':email' => $datos['email'],
            ':tel'   => $datos['telefono'],
            ':id_c'  => $datos['id_ciclo'],
            ':id'    => $datos['id_tutor']
        ]);
    }

    public function eliminarTutor($id_tutor) {
        try {
            $this->conn->beginTransaction();

            // 1. Obtenemos el id_usuario antes de borrar al tutor
            $sqlId = "SELECT id_usuario FROM tutores WHERE id_tutor = :id";
            $stmtId = $this->conn->prepare($sqlId);
            $stmtId->execute([':id' => $id_tutor]);
            $tutor = $stmtId->fetch(PDO::FETCH_ASSOC);

            if ($tutor) {
                // 2. Borramos al tutor
                $sqlTutor = "DELETE FROM tutores WHERE id_tutor = :id";
                $this->conn->prepare($sqlTutor)->execute([':id' => $id_tutor]);

                // 3. Borramos el usuario asociado
                $sqlUser = "DELETE FROM usuarios WHERE id_usuario = :id_u";
                $this->conn->prepare($sqlUser)->execute([':id_u' => $tutor['id_usuario']]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error al eliminar tutor: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerConvenios($busqueda = '', $ordenar = 'nombre_empresa') {
        try {
            $columnasPermitidas = ['nombre_empresa', 'cif', 'localidad', 'num_convenio'];
            if (!in_array($ordenar, $columnasPermitidas)) {
                $ordenar = 'nombre_empresa';
            }

            $sql = "SELECT * FROM convenios 
                    WHERE nombre_empresa LIKE :busqueda 
                    OR cif LIKE :busqueda 
                    OR localidad LIKE :busqueda
                    ORDER BY $ordenar ASC";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':busqueda' => "%$busqueda%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerConvenios: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerConveniosPendientes() {
        // Seleccionamos los datos de la empresa de convenios_nuevos
        // Uniendo con convenios_aprobados donde validado = 0
        $sql = "SELECT cn.*, ca.id_convenio_aprobado, ca.fecha_aprobacion 
                FROM convenios_nuevos cn
                INNER JOIN convenios_aprobados ca ON cn.id_convenio_nuevo = ca.id_convenio_nuevo
                WHERE ca.validado = 0
                ORDER BY ca.fecha_aprobacion DESC";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function procesarValidacionManual($d) {
        try {
            $this->conn->beginTransaction();

            // 1. Generar num_convenio automático (siguiente número disponible)
            $stmtMax = $this->conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
            $stmtMax->execute();
            $maxNum = (int)$stmtMax->fetchColumn();
            $nuevoNum = (string)($maxNum + 1);

            // 2. Insertamos en la tabla oficial 'convenios'
            $sql = "INSERT INTO convenios (num_convenio, nombre_empresa, cif, direccion, localidad, cp, telefono, fax, representante, especialidad, fecha_alta_renovacion, fecha_nueva_renovacion, observaciones) 
                    VALUES (:num_conv, :nom, :cif, :dir, :loc, :cp, :tel, :fax, :rep, :esp, CURDATE(), :fecha_nueva, :obs)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
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

            // 2. Marcamos como validado en convenios_aprobados para que desaparezca de pendientes
            $sqlUpd = "UPDATE convenios_aprobados SET validado = 1 WHERE id_convenio_nuevo = :id";
            $stmtUpd = $this->conn->prepare($sqlUpd);
            $stmtUpd->execute([':id' => $d['id_convenio_nuevo']]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en procesarValidacionManual: " . $e->getMessage());
            return false;
        }
    } 

    public function validarConvenio($id) {
        try {
            $this->conn->beginTransaction();

            // 1. PRIMERO BUSCAMOS LOS DATOS porque el botón de la tabla no los envía, solo envía el ID
            $sqlSel = "SELECT * FROM convenios_nuevos WHERE id_convenio_nuevo = :id";
            $stmtSel = $this->conn->prepare($sqlSel);
            $stmtSel->execute([':id' => $id]);
            $datos = $stmtSel->fetch(PDO::FETCH_ASSOC);

            if (!$datos) {
                throw new Exception("No se encontraron datos para el convenio ID: $id");
            }

            // 2. Generar num_convenio automático (siguiente número disponible)
            $stmtMax = $this->conn->prepare("SELECT MAX(CAST(num_convenio AS UNSIGNED)) FROM convenios");
            $stmtMax->execute();
            $maxNum = (int)$stmtMax->fetchColumn();
            $nuevoNum = (string)($maxNum + 1);

            // 3. Insertamos en la tabla oficial 'convenios'
            $sqlIns = "INSERT INTO convenios (num_convenio, nombre_empresa, cif, direccion, localidad, cp, telefono, fax, representante, especialidad, fecha_alta_renovacion, fecha_nueva_renovacion, observaciones) 
                    VALUES (:num_conv, :nom, :cif, :dir, :loc, :cp, :tel, :fax, :rep, :esp, CURDATE(), :fecha_nueva, :obs)";
            
            $stmtIns = $this->conn->prepare($sqlIns);
            $stmtIns->execute([
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

            // 3. Marcamos como validado
            $sqlUpd = "UPDATE convenios_aprobados SET validado = 1 WHERE id_convenio_nuevo = :id";
            $stmtUpd = $this->conn->prepare($sqlUpd);
            $stmtUpd->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en validarConvenio: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarConvenioPendiente($datos) {
        try {
            $sql = "UPDATE convenios_nuevos SET 
                    nombre_empresa = :nombre_empresa,
                    cif = :cif,
                    direccion = :direccion,
                    localidad = :localidad,
                    cp = :cp,
                    telefono = :telefono,
                    fax = :fax,
                    representante = :representante,
                    especialidad = :especialidad,
                    fecha_nueva_renovacion = :fecha_nueva_renovacion,
                    observaciones = :observaciones
                    WHERE id_convenio_nuevo = :id_convenio_nuevo";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
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

    public function eliminarConvenio($num_convenio) {
        try {
            $sql = "DELETE FROM convenios WHERE num_convenio = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $num_convenio]);
        } catch (PDOException $e) {
            error_log("Error en el Modelo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un convenio en la tabla oficial
     */
    public function actualizarConvenio($num_convenio, $d) {
        $sql = "UPDATE convenios SET 
                nombre_empresa = ?, cif = ?, direccion = ?, localidad = ?, 
                cp = ?, telefono = ?, fax = ?,
                representante = ?,
                especialidad = ?,
                fecha_alta_renovacion = ?,
                fecha_nueva_renovacion = ?,
                observaciones = ?
                WHERE num_convenio = ?";
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $d['nombre_empresa'], $d['cif'], $d['direccion'], $d['localidad'],
            $d['cp'], $d['telefono'], $d['fax'],
            $d['representante'],
            $d['especialidad']           ?? null,
            $d['fecha_alta_renovacion']  ?? null,
            $d['fecha_nueva_renovacion'] ?? null,
            $d['observaciones']          ?? null,
            $num_convenio
        ]);
    }

    /**
     * Sincroniza por CIF o por Nombre
     */
    public function sincronizarConvenioPendiente($cifAntiguo, $nombreAntiguo, $d) {
        $sql = "UPDATE convenios_nuevos SET 
                nombre_empresa = ?, cif = ?, direccion = ?, localidad = ?, 
                cp = ?, telefono = ?, fax = ?,
                representante = ?
                WHERE cif = ? OR nombre_empresa = ?";
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $d['nombre_empresa'], 
            $d['cif'], 
            $d['direccion'], 
            $d['localidad'],
            $d['cp'], 
            $d['telefono'], 
            $d['fax'],
            $d['representante'],
            $cifAntiguo,
            $nombreAntiguo
        ]);
    }

    public function borrarRegistroPendienteYOficial($id) {
        try {
            $this->conn->beginTransaction();

            // 1. Borrar de convenios_nuevos
            $sql1 = "DELETE FROM convenios_nuevos WHERE id_convenio_nuevo = ?";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->execute([$id]);

            // 2. Borrar de convenios_aprobados (usando el mismo ID si es relacional)
            // Nota: Si usas otro campo para enlazar, búscalo antes de borrar
            $sql2 = "DELETE FROM convenios_aprobados WHERE id_convenio_nuevo = ?";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function obtenerAlumnosPendientesFirma() {
        $sql = "
            SELECT
                al.id_alumno,
                al.nombre,
                al.apellido1,
                al.apellido2,
                al.dni,
                al.sexo,
                al.correo,
                asig.id_asignacion,
                asig.num_convenio,
                asig.fecha_inicio,
                asig.fecha_final,
                asig.horario,
                asig.horario_excepciones,
                asig.horas_dia,
                asig.num_total_horas,
                conv.nombre_empresa,
                conv.direccion,
                conv.localidad,
                ci.nombre_ciclo,
                ci.grado,
                cu.nombre_curso
            FROM asignaciones asig
            JOIN alumnos al         ON asig.id_alumno    = al.id_alumno
            JOIN convenios conv     ON asig.num_convenio = conv.num_convenio
            JOIN curso_academico ca ON ca.id_alumno      = al.id_alumno
            JOIN ciclos ci          ON ca.id_ciclo       = ci.id_ciclo
            JOIN cursos cu          ON ci.id_curso       = cu.id_curso
            WHERE asig.enviado = 1
            AND asig.id_asignacion NOT IN (
                SELECT id_asignacion FROM asignaciones_firmadas
            )
            ORDER BY ci.nombre_ciclo ASC, al.apellido1 ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function firmarAsignacion($id_asignacion, $anexo) {
        $sql = "INSERT INTO asignaciones_firmadas (id_asignacion, anexo) VALUES (:id, :anexo)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id_asignacion, ':anexo' => $anexo]);
    }

} // Llave de la clase

?>