<?php

// Este es el index, mi centro de operaciones,
// aquel que me dirigé, al "Login" al "Logout"
// y a los distintos controladores.
// Seguí en conjunto la lógica de PUFOSA y Protectora_Animales

// Inicio la sesión
session_start();

// El primer if corresponde a cuando quiero cerrar
// la sesión. Si he presionado el botón de Logout
// y, siempre y cuando haya sesión: comprobado con el
// $_SESSION['usuario'], me dirigirá a el fichero
// "Logout.php" el cual me sirve para cerrar la sesión.
if (isset($_POST['btnLogOut']) && isset($_SESSION['usuario'])){

    require_once 'Controlador/Logout.php';
    die();

// En caso de ya existir usuario (Que la comprobación salga correcta)
// que me muestre el controlador de viviendas.
} else if (isset($_SESSION['usuario'])) {

    echo "<form action='index.php' method='POST'>
        <input type='submit' name='btnLogOut' value='Cerrar sesión'> 
    </form>";

    // Intentar aplicar la confirmación
    // return confirm('¿Está seguro que quiere cerrar sesión?')

    if ($_SESSION['rol'] == 'tutor') {
        $controlador = "Tutores";
    } else if ($_SESSION['rol'] == 'admin') {
        $controlador = "Admin";
    }

    require_once 'Controlador/Controlador_' . $controlador . ".php";
     $controlador = $controlador . "_controlador"; // Guardo, ahora, el nombre de la clase, que va variando según
                                                  // el controlador a ir. eje:Animal_controlador  
    $controlador = new $controlador; // Se crea un nuevo objeto de la clase correpondiente
    call_user_func(array($controlador, 'probarRol')); // Para probar que tutor y admin funcionan

// Cuando vengo del login, que me muestre el controlador usuarios
// y haga la comprobación. 
} else if (!empty($_REQUEST['btnLogIn'])) {

    require_once 'Controlador/Controlador_Usuarios.php' ;
    $user = new Usuarios_Controlador();
    $user -> validarUsuario();

} else {
// Este else es el que se mostrará por defecto al abrir
// el proyecto, y me lleva a "Login.php", como su nombre
// indica, mi página de loguearme.

    require_once './Vista/Login.php';
    die(); 
    
}


?>