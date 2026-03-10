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
        
    public function set_session($usr, $rol) {
        $_SESSION['usuario'] = $usr;
        $_SESSION['rol'] = $rol;
    }
    
    public function validarDatos() {
        // Datos del formulario
        $user_post = strtolower(trim($_POST['usuario'])); 
        // Lo pongo en minúsculas y le quito espacios al inicio y final
        $password_post = $_POST['contrasena'];

        // Buscar el usuario en la base de datos
        $sql = "SELECT *
                FROM usuarios
                WHERE username
                LIKE :usuario";
        $stmt = $this -> conn -> prepare($sql);
        $stmt -> bindParam(':usuario', $user_post);
        $stmt -> execute();
        $usuarios = $stmt -> fetchAll();

        // Si no hay nada en el array, vacío, es que 
        // no hay ningún usuario con ese nombre, por 
        // ende necesitamos que nos regrese al index 
        // diciendo que el usuario no es válido
        if (count($usuarios) == 0) {

            header("Location: index.php?mensaje=El usuario no es válido");
            die();

        // De lo contrario, que pase a comprobar la contraseña
        } else {
            
            foreach ($usuarios as $fila) { // Recorro el array y compruebo la contraseña

                $password_bbdd = $fila['password'];
                $rol_bbdd = $fila['rol'];
                // Contraseña de la base de datos correspondiente al usuario.
                
                if ($password_post !== $password_bbdd) { 
                // En caso de no ser la misma que me lleve al index diciendo que no es correcta

                    header("Location: index.php?mensaje=La contraseña no es correcta");
                    die();

                }
            }
        }

        // En caso de ser correcto (tanto usuario como contraseña)
        // LLamamos al método para crear la sesión y la cookie

        $this -> set_session($user_post, $rol_bbdd);
        header("Location: index.php");

    }

}


?>