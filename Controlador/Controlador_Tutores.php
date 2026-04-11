<?php
require_once 'Controlador/Controlador_Convenios.php';
require_once 'Modelo/Tutores.php';
require_once 'Modelo/Alumnos.php';

class Tutores_Controlador {

    public function mostrarPanel() {
        // --- PESTAÑA ACTIVA ---
        $pestanaActiva = $_GET['tab'] ?? 1;
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
        $ordenar = $_POST['ordenar'] ?? '';
$misConveniosIds = array_column($misConvenios, 'id_convenio');
$alumnos = $alumnoModelo->listarPorCiclo($idCicloTutor, $busqueda, $estadoFiltro, $ordenar, $misConveniosIds);

        // --- CARGA DE VISTA ---
        require_once 'Vista/index_vista.php';
    }
    public function agregarAlumno() {
        // 1. Verificar que el ID de ciclo existe en la sesión
        if (!isset($_SESSION['id_ciclo'])) {
            die("Error: No se ha detectado el ciclo formativo en la sesión.");
        }

        $idCiclo = $_SESSION['id_ciclo'];
        $alumnoModelo = new Alumnos();

        // 2. Ejecutar inserción
        $resultado = $alumnoModelo->agregarAlumno(
            trim($_POST['nombre']),
            trim($_POST['apellido1']),
            trim($_POST['apellido2'] ?? ''),
            strtoupper(trim($_POST['dni'])),
            $_POST['sexo'],
            trim($_POST['correo'] ?? ''),
            $idCiclo
        );

        if ($resultado) {
            // Éxito: Redirigir
            header('Location: index.php?tab=2');
            exit();
        } else {
            // ERROR: Si llegas aquí, el modelo devolvió 'false'
            die("Error al insertar en la base de datos. Revisa si el DNI está duplicado.");
        }
    }
public function obtenerAlumno() {
    $idAlumno = $_POST['id_alumno'];
    $alumnoModelo = new Alumnos();
    $alumno = $alumnoModelo->obtenerPorId($idAlumno);
    header('Content-Type: application/json');
    echo json_encode($alumno);
    exit();
}

public function editarAlumno() {
    $alumnoModelo = new Alumnos();

    // Capturamos el checkbox 'enviado'. 
    // Si está marcado, llega como '1'. Si no, no existe en $_POST, así que le asignamos '0'.
    $enviado = isset($_POST['enviado']) ? 1 : 0;

    $alumnoModelo->editarAlumno(
        $_POST['id_alumno'],
        trim($_POST['nombre']),
        trim($_POST['apellido1']),
        trim($_POST['apellido2'] ?? ''),
        strtoupper(trim($_POST['dni'])),
        $_POST['sexo'],
        trim($_POST['correo'] ?? ''),
        $_POST['id_convenio'] ?: null,
        $_POST['fecha_inicio'] ?: null,
        $_POST['fecha_final'] ?: null,
        trim($_POST['horario'] ?? ''),
        $_POST['horas_dia'] ?: null,
        $enviado // <--- PASAMOS EL NUEVO PARÁMETRO AL MODELO
    );
    
    header('Location: index.php?tab=2');
    exit();
}

public function exportarAlumnos() {
    // Verificamos si llegan IDs por POST
    if (isset($_POST['exportar_ids']) && is_array($_POST['exportar_ids'])) {
        $alumnoModelo = new Alumnos();
        
        foreach ($_POST['exportar_ids'] as $idAlumno) {
            // Importante: castear a int para seguridad
            $alumnoModelo->marcarComoEnviado((int)$idAlumno);
        }
        
        // Redirigimos para ver los cambios
        header('Location: index.php?tab=2&status=success');
    } else {
        // Si no llega nada, redirigimos igual para no quedar en blanco
        header('Location: index.php?tab=2&status=error');
    }
    exit();
}

}