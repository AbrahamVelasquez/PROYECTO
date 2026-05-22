<?php

/**
 * Modelo/Usuarios.php — Autenticación y gestión de sesión
 *
 * Valida las credenciales del formulario de login contra la base de datos
 * y, si son correctas, abre la sesión con todos los datos necesarios para
 * que el resto de la aplicación funcione sin hacer queries adicionales.
 *
 * La consulta une `usuarios` con `tutores` para obtener el id_ciclo del tutor
 * en el mismo SELECT del login, evitando una segunda consulta en mostrarPanel.
 *
 * MVC: Modelo de autenticación. Solo interactúa con las tablas `usuarios`
 * y `tutores`. La sesión que genera es consumida por todos los controladores.
 */

require_once "./Core/Conexion.php";

class Usuarios {

    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    /**
     * Puebla $_SESSION con los datos del usuario autenticado.
     * El id_ciclo se guarda aquí para que todos los controladores del tutor
     * puedan usarlo directamente sin tener que volver a consultar la BD.
     */
    public function set_session($usr, $rol, $id_usr, $id_tutor, $id_ciclo) {
        $_SESSION['usuario']    = $usr;
        $_SESSION['rol']        = $rol;
        $_SESSION['id_usuario'] = $id_usr;
        $_SESSION['id_tutor']   = $id_tutor;
        $_SESSION['id_ciclo']   = $id_ciclo;
    }

    /**
     * Valida usuario y contraseña. Si son correctos abre la sesión y redirige
     * al index.php; si no, redirige con un mensaje de error en la URL.
     *
     * El LEFT JOIN con tutores es necesario porque los admins no tienen fila en
     * esa tabla — con INNER JOIN el login del admin fallaría siempre.
     */
    public function validarDatos() {
        $user_post     = strtolower(trim($_POST['usuario']));
        $password_post = $_POST['contrasena'];

        $sql = "SELECT u.*, t.id_tutor, t.id_ciclo
                FROM usuarios u
                LEFT JOIN tutores t ON u.id_usuario = t.id_usuario
                WHERE u.username = :usuario";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario', $user_post);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        // Credenciales incorrectas — volvemos al login con mensaje visible
        if (!$fila || $password_post !== $fila['password']) {
            header("Location: index.php?mensaje=El usuario y/o la contraseña son incorrectos.");
            die();
        }

        $this->set_session(
            $fila['username'],
            $fila['rol'],
            $fila['id_usuario'],
            $fila['id_tutor'],
            $fila['id_ciclo']
        );

        header("Location: index.php");
        exit();
    }

} // Llave de la clase

?>