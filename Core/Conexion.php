<?php

/**
 * Core/Conexion.php — Singleton de conexión a la base de datos
 *
 * Proporciona una única instancia PDO compartida en toda la aplicación.
 * Cualquier modelo que necesite ejecutar consultas obtiene la conexión
 * llamando a Conexion::getConexion().
 *
 * El patrón Singleton garantiza que no se abran múltiples conexiones
 * en la misma petición, independientemente de cuántos modelos se carguen.
 *
 * MVC: Infraestructura de acceso a datos — no forma parte del flujo MVC
 * directamente, pero todos los Modelos dependen de este componente.
 */

class Conexion {

    // La instancia se guarda aquí entre llamadas. null hasta que se use por primera vez.
    private static $instancia = null;

    /**
     * Devuelve la conexión PDO activa. La crea solo la primera vez que se llama.
     * Si la conexión falla, muestra la página de error 500 y detiene la ejecución.
     */
    public static function getConexion() {

        if (self::$instancia === null) {
            try {
                self::$instancia = new PDO(
                    "mysql:host=localhost;dbname=citye;charset=utf8",
                    "root",
                    "",
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

            } catch (PDOException $e) {
                http_response_code(500);

                // Resolvemos la ruta dinámica para incluir el 500.php sin importar
                // el nombre de la carpeta del proyecto en localhost
                $partesRuta      = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                $carpetaProyecto = (!empty($partesRuta) && $_SERVER['HTTP_HOST'] === 'localhost')
                    ? '/' . $partesRuta[0]
                    : '';

                include $_SERVER['DOCUMENT_ROOT'] . $carpetaProyecto . '/Errores/500.php';
                exit();
            }
        }

        return self::$instancia;
    }

} // Llave de la clase

?>