<?php

/**
 * Modelo/Tutores.php — Perfil de tutor y gestión del personal docente
 *
 * Tiene dos responsabilidades bien diferenciadas:
 *
 *   1. Perfil del tutor autenticado (usado en mostrarPanel):
 *      Devuelve nombre, ciclo y curso del tutor activo para poblar la cabecera
 *      del panel y determinar qué datos de alumnos/convenios cargar.
 *
 *   2. CRUD del personal docente (usado por el panel de Admin):
 *      Alta, edición, eliminación y listado de tutores. Al crear un tutor se
 *      genera automáticamente su cuenta de usuario con credenciales por defecto.
 *      Al eliminar un tutor se borra también su cuenta de usuario en cascada.
 *
 * MVC: Modelo. Gestiona las tablas `tutores`, `usuarios`, `ciclos` y `cursos`.
 * No contiene lógica de presentación — devuelve arrays para que los
 * controladores pasen a las vistas.
 */

require_once "./Core/Conexion.php";

class Tutores {

    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }


    // ═══════════════════════════════════════════════════════════════════
    // PERFIL DEL TUTOR  (usado por Controlador_Tutores → mostrarPanel)
    // ═══════════════════════════════════════════════════════════════════

    public function obtenerDatosPerfil($username) {
        $sql = "SELECT t.id_ciclo, t.nombre, t.apellidos, t.email, t.telefono,
                    cur.nombre_curso, cic.nombre_ciclo
                FROM tutores t
                INNER JOIN usuarios u   ON t.id_usuario = u.id_usuario
                INNER JOIN ciclos cic   ON t.id_ciclo   = cic.id_ciclo
                INNER JOIN cursos cur   ON cic.id_curso  = cur.id_curso
                WHERE u.username = :username";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // ═══════════════════════════════════════════════════════════════════
    // GESTIÓN DE DOCENTES  (usado por Admin → sección Personal Docente)
    // ═══════════════════════════════════════════════════════════════════

    public function obtenerTutores($busqueda = '', $ordenar = 'id', $filtro_curso = '') {
        $params = [];
        $sql = "SELECT t.*, c.nombre_ciclo, cur.nombre_curso
                FROM tutores t
                LEFT JOIN ciclos c   ON t.id_ciclo  = c.id_ciclo
                LEFT JOIN cursos cur ON c.id_curso   = cur.id_curso
                WHERE 1=1";

        if (!empty($busqueda)) {
            $sql .= " AND (t.nombre LIKE :busq OR t.apellidos LIKE :busq OR t.dni LIKE :busq
                         OR CONCAT(t.apellidos, ' ', t.nombre) LIKE :busq)";
            $params[':busq'] = '%' . $busqueda . '%';
        }

        if (!empty($filtro_curso)) {
            $sql .= " AND cur.nombre_curso = :curso";
            $params[':curso'] = $filtro_curso;
        }

        switch ($ordenar) {
            case 'apellidos':
                $sql .= " ORDER BY t.apellidos ASC";
                break;
            case 'ciclo':
                $sql .= " ORDER BY cur.nombre_curso ASC, c.nombre_ciclo ASC";
                break;
            case 'id':
            default:
                $sql .= " ORDER BY t.id_tutor ASC";
                break;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT t.*, c.nombre_ciclo, cur.nombre_curso
                FROM tutores t
                LEFT JOIN ciclos c   ON t.id_ciclo  = c.id_ciclo
                LEFT JOIN cursos cur ON c.id_curso   = cur.id_curso
                WHERE t.id_tutor = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerCiclosLibres() {
        $sql = "SELECT c.id_ciclo, c.nombre_ciclo, cur.nombre_curso
                FROM ciclos c
                INNER JOIN cursos cur ON c.id_curso = cur.id_curso
                WHERE c.id_ciclo NOT IN (SELECT id_ciclo FROM tutores)
                ORDER BY cur.nombre_curso ASC, c.nombre_ciclo ASC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosLosCiclos() {
        $sql = "SELECT c.id_ciclo, c.nombre_ciclo, cur.nombre_curso, t.id_tutor AS ocupado_por
                FROM ciclos c
                JOIN cursos cur  ON c.id_curso  = cur.id_curso
                LEFT JOIN tutores t ON c.id_ciclo = t.id_ciclo
                ORDER BY cur.nombre_curso ASC, c.nombre_ciclo ASC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un tutor nuevo con su cuenta de usuario asociada en una transacción.
     * Primero inserta en `usuarios` (obteniendo el ID generado), luego inserta
     * en `tutores` usando ese ID como FK. Si cualquier paso falla, deshace todo.
     */
    public function guardarTutor($datos) {
        try {
            $this->conn->beginTransaction();

            $idUsuario = $this->crearCuentaUsuario($datos['nombre']);

            $sqlTutor = "INSERT INTO tutores (id_usuario, dni, nombre, apellidos, email, telefono, id_ciclo)
                         VALUES (:id_u, :dni, :nom, :ape, :email, :tel, :id_c)";

            $this->conn->prepare($sqlTutor)->execute([
                ':id_u'  => $idUsuario,
                ':dni'   => $datos['dni'],
                ':nom'   => $datos['nombre'],
                ':ape'   => $datos['apellidos'],
                ':email' => $datos['email'],
                ':tel'   => $datos['telefono'],
                ':id_c'  => $datos['id_ciclo'],
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            error_log("Error al crear tutor: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarTutor($datos) {
        $sql = "UPDATE tutores
                SET nombre    = :nom,
                    apellidos = :ape,
                    email     = :email,
                    telefono  = :tel,
                    id_ciclo  = :id_c
                WHERE id_tutor = :id";

        return $this->conn->prepare($sql)->execute([
            ':nom'   => $datos['nombre'],
            ':ape'   => $datos['apellidos'],
            ':email' => $datos['email'],
            ':tel'   => $datos['telefono'],
            ':id_c'  => $datos['id_ciclo'],
            ':id'    => $datos['id_tutor'],
        ]);
    }

    /**
     * Elimina un tutor y su cuenta de usuario en una sola transacción.
     * Primero obtiene el id_usuario del tutor para poder borrar ambas filas,
     * ya que la tabla `tutores` guarda la FK pero no hay CASCADE en la BD.
     */
    public function eliminarTutor($id_tutor) {
        try {
            $this->conn->beginTransaction();

            $stmtId = $this->conn->prepare("SELECT id_usuario FROM tutores WHERE id_tutor = :id");
            $stmtId->execute([':id' => $id_tutor]);
            $tutor = $stmtId->fetch(PDO::FETCH_ASSOC);

            if ($tutor) {
                $this->conn->prepare("DELETE FROM tutores  WHERE id_tutor  = :id")->execute([':id'   => $id_tutor]);
                $this->conn->prepare("DELETE FROM usuarios WHERE id_usuario = :id_u")->execute([':id_u' => $tutor['id_usuario']]);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            error_log("Error al eliminar tutor: " . $e->getMessage());
            return false;
        }
    }

    // ── Privado: crea la cuenta en `usuarios` y devuelve el ID generado ──

    private function crearCuentaUsuario($nombre) {
        $nombreLimpio  = mb_strtolower(str_replace(' ', '', $nombre));
        $nuevoUsername = $nombreLimpio . "_tutor";
        $nuevaPassword = mb_substr($nombreLimpio, 0, 3) . "123";

        // PENDIENTE: activar hash de contraseña cuando se implemente el cambio de clave
        // $passHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        $sqlUser = "INSERT INTO usuarios (username, password, rol) VALUES (:user, :pass, 'tutor')";
        $this->conn->prepare($sqlUser)->execute([
            ':user' => $nuevoUsername,
            ':pass' => $nuevaPassword,
            // ':pass' => $passHash,  // descomentar cuando se active el hash
        ]);

        return $this->conn->lastInsertId();
    }

} // Llave de la clase
