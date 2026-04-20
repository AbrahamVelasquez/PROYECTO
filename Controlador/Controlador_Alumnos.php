<?php

////////////////////////////////////////////////
// Este fichero, por ahora, no se está usando //
////////////////////////////////////////////////

// Controlador/Controlador_Alumnos.php

require_once './Modelo/Alumnos.php';

class Alumnos_Controlador {

    private $modeloAlumno;

    public function __construct() {
        $this->modeloAlumno = new Alumnos();
    }

    public function mostrarListado($idCiclo) {
        // 1. Obtenemos los datos del modelo
        $alumnos = $this->modeloAlumno->listarPorCiclo($idCiclo);

        // 2. Cargamos la vista (el archivo HTML que tenías)
        // Las variables creadas aquí estarán disponibles en la vista
        require_once './Vista/tabla_alumnos.php';
    }

} // Llave de la clase

?>