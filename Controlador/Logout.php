<?php

// Controlador/Logout.php

// Al ser incluido este fichero, lo que hace
// comprobar si hay sesión o no, en caso que 
// haya se encarga de cerrarla y devolverte  
// al "index.php" el cual, a su vez, entrará 
// en el else que me lleva al "Login.php" 
// al todavía no tener el array $_SESSION 
// creado con datos. 

// Y en caso de intentar acceder a este 
// fichero sin tener una sesión, le muestro
// una opción para regresar al "index.php"
// (En el fondo el "Login.php") 

if(isset($_SESSION['usuario'])){
  
    session_unset();
    session_destroy();
    header("Location: index.php");
    
}

?>
