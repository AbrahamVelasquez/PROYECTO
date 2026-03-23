<?php
require_once 'Controlador/Controlador_Convenios.php';
require_once 'Modelo/Tutores.php'; // Importamos el modelo

class Tutores_Controlador {
public function mostrarPanel() {
    $convControlador = new Convenios_Controlador();
    $data = $convControlador->gestionar();
    $convenios = $data['busqueda'];
    $misConvenios = $data['favoritos'];

    $tutorModelo = new Tutores();
    $perfil = $tutorModelo->obtenerDatosPerfil($_SESSION['usuario']);

    // Variables limpias para la vista
    $nombreTutor = $perfil ? ($perfil['nombre'] . " " . $perfil['apellidos']) : $_SESSION['usuario'];
    $cicloTutor = $perfil['nombre_ciclo'] ?? 'Sin Ciclo';
    $cursoTutor = $perfil['nombre_curso'] ?? 'Sin Curso';

    require_once 'Vista/index_vista.php';
}
}