<?php

// Modelo/Usuarios.php

require_once "./Core/Conexion.php"; 

class Usuarios {

    private $conn; 

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }
        
    // Corregimos los parámetros para incluir el id_ciclo
    public function set_session($usr, $rol, $id_usr, $id_tutor, $id_ciclo) { 
        $_SESSION['usuario'] = $usr;
        $_SESSION['rol'] = $rol;
        $_SESSION['id_usuario'] = $id_usr;
        $_SESSION['id_tutor'] = $id_tutor; 
        $_SESSION['id_ciclo'] = $id_ciclo; // <--- Ahora sí funcionará
    }

    public function validarDatos() {
        $user_post = strtolower(trim($_POST['usuario'])); 
        $password_post = $_POST['contrasena'];

        // MODIFICACIÓN: Traemos también el id_ciclo desde la tabla tutores
        $sql = "SELECT u.*, t.id_tutor, t.id_ciclo 
                FROM usuarios u
                LEFT JOIN tutores t ON u.id_usuario = t.id_usuario
                WHERE u.username = :usuario";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario', $user_post);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fila) {
            header("Location: index.php?mensaje=El usuario no es válido");
            die();
        }

        // Nota: Si usas password_hash en el futuro, aquí iría password_verify
        if ($password_post !== $fila['password']) { 
            header("Location: index.php?mensaje=La contraseña no es correcta");
            die();
        }

        // Pasamos todos los datos necesarios a la sesión, incluyendo el id_ciclo
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