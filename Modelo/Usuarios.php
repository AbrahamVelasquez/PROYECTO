<?php

require_once "./Core/Conexion.php"; // Importa la clase Conexion 

class Usuarios {

    private $id_usuario;
    private $username;
    private $password;
    private $rol;
    private $conn; // Para que inicie la conexión asi accedemos a la BD 

    public function __construct() {
        $this -> conn = Conexion::getConexion();  // El modelo obtiene esa conexión
    }
        
    public function set_session($usr, $rol, $id_usr, $id_tutor) { // Añadimos id_tutor
        $_SESSION['usuario'] = $usr;
        $_SESSION['rol'] = $rol;
        $_SESSION['id_usuario'] = $id_usr;
        $_SESSION['id_tutor'] = $id_tutor; // <--- ESTO ES VITAL
        $_SESSION['id_ciclo']= $datosUsuario['id_ciclo'];
    }

    public function validarDatos() {
        $user_post = strtolower(trim($_POST['usuario'])); 
        $password_post = $_POST['contrasena'];

        // Modificamos la consulta para traer el id_tutor también
        $sql = "SELECT u.*, t.id_tutor 
                FROM usuarios u
                LEFT JOIN tutores t ON u.id_usuario = t.id_usuario
                WHERE u.username = :usuario";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario', $user_post);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch directo, más limpio

        if (!$fila) {
            header("Location: index.php?mensaje=El usuario no es válido");
            die();
        }

        if ($password_post !== $fila['password']) { 
            header("Location: index.php?mensaje=La contraseña no es correcta");
            die();
        }

        // Pasamos el id_tutor que hemos sacado del JOIN
        $this->set_session($user_post, $fila['rol'], $fila['id_usuario'], $fila['id_tutor']);
        header("Location: index.php");
    }

}


?>