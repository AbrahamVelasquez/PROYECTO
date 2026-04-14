<?php

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

} // admin

?>