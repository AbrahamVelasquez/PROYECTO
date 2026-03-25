<?php
require_once 'Controlador/Controlador_Convenios.php';
require_once 'Modelo/Tutores.php';
require_once 'Modelo/Alumnos.php';

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

        $nombreTutor = $perfil ? ($perfil['nombre'] . " " . $perfil['apellidos']) : $_SESSION['usuario'];
        $cicloTutor = $perfil['nombre_ciclo'] ?? 'Sin Ciclo';
        $cursoTutor = $perfil['nombre_curso'] ?? 'Sin Curso';
        $idCicloTutor = $perfil['id_ciclo'] ?? 0;
        $_SESSION['id_ciclo'] = $idCicloTutor;

        // --- NUEVO: CAPTURA DE FILTROS PARA ALUMNOS ---
        $busqueda = $_POST['busqueda'] ?? '';
        $estadoFiltro = $_POST['estado'] ?? '';

        // --- GESTIÓN DE ALUMNOS (CON FILTROS) ---
        $alumnoModelo = new Alumnos();
        // Pasamos las variables de filtro al método
        $alumnos = $alumnoModelo->listarPorCiclo($idCicloTutor, $busqueda, $estadoFiltro);

        // --- CARGA DE VISTA ---
        require_once 'Vista/index_vista.php';
    }
    public function agregarAlumno() {
        $idCiclo = $_SESSION['id_ciclo'];

        $alumnoModelo = new Alumnos();
        $alumnoModelo->agregarAlumno(
            trim($_POST['nombre']),
            trim($_POST['apellido1']),
            trim($_POST['apellido2'] ?? ''),
            strtoupper(trim($_POST['dni'])),
            $_POST['sexo'],
            trim($_POST['correo'] ?? ''),
            $idCiclo
        );

        // Recarga el panel con el listado actualizado
        $this->mostrarPanel();
    }
}