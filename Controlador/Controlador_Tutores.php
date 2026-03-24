<?php
require_once 'Controlador/Controlador_Convenios.php';
require_once 'Modelo/Tutores.php';
require_once 'Modelo/Alumnos.php'; // 1. Importamos el nuevo modelo de Alumnos

class Tutores_Controlador {

    public function mostrarPanel() {
        // --- GESTIÓN DE CONVENIOS ---
        $convControlador = new Convenios_Controlador();
        $data = $convControlador->gestionar();
        $convenios = $data['busqueda'];
        $misConvenios = $data['favoritos'];

        // --- GESTIÓN DE PERFIL DEL TUTOR ---
        $tutorModelo = new Tutores();
        $perfil = $tutorModelo->obtenerDatosPerfil($_SESSION['usuario']);

        // Variables limpias para la cabecera
        $nombreTutor = $perfil ? ($perfil['nombre'] . " " . $perfil['apellidos']) : $_SESSION['usuario'];
        $cicloTutor = $perfil['nombre_ciclo'] ?? 'Sin Ciclo';
        $cursoTutor = $perfil['nombre_curso'] ?? 'Sin Curso';
        
        // 2. EXTRAEMOS EL ID DEL CICLO (Es vital que tu SELECT en Modelo/Tutores.php traiga el id_ciclo)
        $idCicloTutor = $perfil['id_ciclo'] ?? 0;

        // --- GESTIÓN DE ALUMNOS ---
        // 3. Instanciamos el modelo de Alumnos y recuperamos la lista
        $alumnoModelo = new Alumnos();
        $alumnos = $alumnoModelo->listarPorCiclo($idCicloTutor);

        // --- CARGA DE VISTA ---
        // Al cargar la vista aquí, todas las variables ($convenios, $nombreTutor, $alumnos) 
        // están disponibles automáticamente dentro de index_vista.php o tabla_alumnos.php
        require_once 'Vista/index_vista.php';
    }
}