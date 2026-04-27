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

        // Ejecución actual (Texto plano para pruebas)
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
            // Validamos que la columna de ordenación exista para evitar errores de SQL
            $columnasPermitidas = ['nombre_empresa', 'cif', 'municipio', 'mail'];
            if (!in_array($ordenar, $columnasPermitidas)) {
                $ordenar = 'nombre_empresa';
            }

            $sql = "SELECT * FROM convenios 
                    WHERE nombre_empresa LIKE :busqueda 
                    OR cif LIKE :busqueda 
                    OR municipio LIKE :busqueda
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

            // 1. Insertamos en la tabla oficial 'convenios' con los datos revisados
            $sql = "INSERT INTO convenios (nombre_empresa, cif, direccion, municipio, cp, pais, telefono, fax, mail, nombre_representante, dni_representante, cargo) 
                    VALUES (:nom, :cif, :dir, :mun, :cp, :pais, :tel, :fax, :mail, :nom_rep, :dni_rep, :cargo)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nom'     => $d['nombre_empresa'],
                ':cif'     => $d['cif'],
                ':dir'     => $d['direccion'],
                ':mun'     => $d['municipio'],
                ':cp'      => $d['cp'],
                ':pais'    => $d['pais'],
                ':tel'     => $d['telefono'],
                ':fax'     => $d['fax'],
                ':mail'    => $d['mail'],
                ':nom_rep' => $d['nombre_representante'],
                ':dni_rep' => $d['dni_representante'],
                ':cargo'   => $d['cargo']
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

            // 2. AHORA SÍ, usamos 'datos' que acabamos de traer de la BD
            $sqlIns = "INSERT INTO convenios (nombre_empresa, cif, direccion, municipio, cp, pais, telefono, fax, mail, nombre_representante, dni_representante, cargo) 
                    VALUES (:nom, :cif, :dir, :mun, :cp, :pais, :tel, :fax, :mail, :nom_rep, :dni_rep, :cargo)";
            
            $stmtIns = $this->conn->prepare($sqlIns);
            $stmtIns->execute([
                ':nom'     => $datos['nombre_empresa'],
                ':cif'     => $datos['cif'],
                ':dir'     => $datos['direccion'],
                ':mun'     => $datos['municipio'],
                ':cp'      => $datos['cp'],
                ':pais'    => $datos['pais'],
                ':tel'     => $datos['telefono'],
                ':fax'     => $datos['fax'],
                ':mail'    => $datos['mail'],
                ':nom_rep' => $datos['nombre_representante'],
                ':dni_rep' => $datos['dni_representante'],
                ':cargo'   => $datos['cargo']
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
                    municipio = :municipio,
                    cp = :cp,
                    pais = :pais,
                    telefono = :telefono,
                    fax = :fax,
                    mail = :mail,
                    nombre_representante = :nombre_representante,
                    dni_representante = :dni_representante,
                    cargo = :cargo
                    WHERE id_convenio_nuevo = :id_convenio_nuevo";
            
            $stmt = $this->conn->prepare($sql);
            
            // Ejecutamos pasando el array de datos que viene del controlador
            return $stmt->execute([
                ':id_convenio_nuevo'    => $datos['id_convenio_nuevo'],
                ':nombre_empresa'       => $datos['nombre_empresa'],
                ':cif'                  => $datos['cif'],
                ':direccion'            => $datos['direccion'],
                ':municipio'            => $datos['municipio'],
                ':cp'                   => $datos['cp'],
                ':pais'                 => $datos['pais'],
                ':telefono'             => $datos['telefono'],
                ':fax'                  => $datos['fax'],
                ':mail'                 => $datos['mail'],
                ':nombre_representante' => $datos['nombre_representante'],
                ':dni_representante'    => $datos['dni_representante'],
                ':cargo'                => $datos['cargo']
            ]);

        } catch (PDOException $e) {
            // Opcional: registrar el error para depuración
            error_log("Error en actualizarConvenioPendiente: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarConvenio($id) {
        try {
            $sql = "DELETE FROM convenios WHERE id_convenio = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error en el Modelo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un convenio en la tabla oficial
     */
    public function actualizarConvenio($id, $d) {
        $sql = "UPDATE convenios SET 
                nombre_empresa = ?, cif = ?, direccion = ?, municipio = ?, 
                cp = ?, pais = ?, mail = ?, telefono = ?, fax = ?,
                nombre_representante = ?, dni_representante = ?, cargo = ?
                WHERE id_convenio = ?";
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $d['nombre_empresa'], $d['cif'], $d['direccion'], $d['municipio'],
            $d['cp'], $d['pais'], $d['mail'], $d['telefono'], $d['fax'],
            $d['nombre_representante'], $d['dni_representante'], $d['cargo'], 
            $id
        ]);
    }

    /**
     * Sincroniza por CIF o por Nombre
     */
    public function sincronizarConvenioPendiente($cifAntiguo, $nombreAntiguo, $d) {
        // La lógica es: Actualiza si el CIF coincide OR el nombre coincide
        $sql = "UPDATE convenios_nuevos SET 
                nombre_empresa = ?, cif = ?, direccion = ?, municipio = ?, 
                cp = ?, pais = ?, mail = ?, telefono = ?, fax = ?,
                nombre_representante = ?, dni_representante = ?, cargo = ?
                WHERE cif = ? OR nombre_empresa = ?";
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $d['nombre_empresa'], 
            $d['cif'], 
            $d['direccion'], 
            $d['municipio'],
            $d['cp'], 
            $d['pais'], 
            $d['mail'], 
            $d['telefono'], 
            $d['fax'],
            $d['nombre_representante'], 
            $d['dni_representante'], 
            $d['cargo'], 
            $cifAntiguo,    // Para el primer ? del WHERE
            $nombreAntiguo  // Para el segundo ? del WHERE (el OR)
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

} // Llave de la clase

?>