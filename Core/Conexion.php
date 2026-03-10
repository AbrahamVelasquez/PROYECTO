<?php

class Conexion {

    private static $instancia = null; 
    // Método estatico en el que en $instancia
    // almacenará objetos en la BDD.
    // Y si no hay nada, queda en null
    
    public static function getConexion() { 

        if (self::$instancia === null) {
            
            // El try-catch debe ir dentro del if, cubriendo la conexión.
            try {
 
                self::$instancia = new PDO("mysql:host=localhost;dbname=citye;charset=utf8","root","",
                                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            } catch (PDOException $e) {
                // Si hay un error, el catch lo captura.
                die("Fallo en la conexión: " . $e->getMessage());
                // Usamos die() para detener la ejecución y mostrar el error.
            }
           
        }
        return self::$instancia; // Nos la devuelve. En caso de estar vacía pues nos da un null. 
    }
    
}

?>