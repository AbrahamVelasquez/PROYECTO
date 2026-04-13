<?php
require_once 'Controlador/Controlador_Convenios.php';
require_once 'Modelo/Tutores.php';
require_once 'Modelo/Alumnos.php';

class Tutores_Controlador {

public function mostrarPanel() {
        // --- PESTAÑA ACTIVA ---
        $pestanaActiva = $_GET['tab'] ?? 1;

        // --- GESTIÓN DE PERFIL DEL TUTOR ---
        $tutorModelo = new Tutores();
        $perfil = $tutorModelo->obtenerDatosPerfil($_SESSION['usuario']);

        $nombreTutor = $perfil ? ($perfil['nombre'] . " " . $perfil['apellidos']) : $_SESSION['usuario'];
        $correoTutor = $perfil['email'] ?? '';
        $telTutor = $perfil['telefono'] ?? '';
        $cicloTutor = $perfil['nombre_ciclo'] ?? 'Sin Ciclo';
        $cursoTutor = $perfil['nombre_curso'] ?? 'Sin Curso';
        $idCicloTutor = $perfil['id_ciclo'] ?? 0;
        $_SESSION['id_ciclo'] = $idCicloTutor; // Aseguramos que el ID esté en sesión para el registro

        // --- GESTIÓN DE CONVENIOS ---
        $convControlador = new Convenios_Controlador();
        $data = $convControlador->gestionar();
        
        $convenios = $data['busqueda'];
        $misConvenios = $data['favoritos'];
        
        // REGLA: Solo mostramos los convenios nuevos que NO estén en la tabla de aprobados
        $convModelo = new Convenios();
        $conveniosProceso = $convModelo->listarPendientesDeAprobacion($idCicloTutor);

        // --- RESTO DEL CÓDIGO (Alumnos, etc.) ---
        $busqueda = $_REQUEST['busqueda'] ?? '';
        $estadoFiltro = $_REQUEST['estado'] ?? '';

        $alumnoModelo = new Alumnos();
        $ordenar = $_POST['ordenar'] ?? '';
        $misConveniosIds = array_column($misConvenios, 'id_convenio');
        $alumnos = $alumnoModelo->listarPorCiclo($idCicloTutor, $busqueda, $estadoFiltro, $ordenar, $misConveniosIds);
        $alumnosFirmados = $alumnoModelo->listarAlumnosFirmados($idCicloTutor);

        // --- CARGA DE VISTA ---
        require_once 'Vista/index_vista.php';
    }
    
    public function agregarAlumno() {
    if (!isset($_SESSION['id_ciclo'])) {
        die("Error: No se ha detectado el ciclo formativo en la sesión.");
    }

    $idCiclo = $_SESSION['id_ciclo'];
    $alumnoModelo = new Alumnos();

    // Capturamos años si vinieran del formulario, si no, el modelo pondrá los actuales
    $anioInicio = $_POST['anio_inicio'] ?? date('Y');
    $anioFin = $_POST['anio_fin'] ?? (date('Y') + 1);

    $resultado = $alumnoModelo->agregarAlumno(
        trim($_POST['nombre']),
        trim($_POST['apellido1']),
        trim($_POST['apellido2'] ?? ''),
        strtoupper(trim($_POST['dni'])),
        $_POST['sexo'],
        trim($_POST['correo'] ?? ''),
        trim($_POST['telefono'] ?? ''),
        $idCiclo
        // Aquí podrías pasar $anioInicio y $anioFin si actualizaste la firma del método en el modelo
    );

    if ($resultado) {
        header('Location: index.php?tab=2');
        exit();
    } else {
        die("Error al insertar en la base de datos. Revisa si el DNI está duplicado.");
    }
}

    
public function obtenerAlumno() {
    $alumnoModelo = new Alumnos();

    if (isset($_POST['verificar_firma'])) {
        $idAsig = (int)$_POST['id_asignacion'];
        $yaFirmado = $alumnoModelo->comprobarFirmaExistente($idAsig);
        header('Content-Type: application/json');
        echo json_encode(['yaFirmado' => $yaFirmado]);
        exit();
    }

    $idAlumno = isset($_POST['id_alumno']) ? (int)$_POST['id_alumno'] : 0;
    $alumno = $alumnoModelo->obtenerPorId($idAlumno);

    header('Content-Type: application/json');
    
    if (!$alumno) {
        echo json_encode(['error' => 'Alumno no encontrado', 'id_recibido' => $idAlumno]);
    } else {
        // --- Verificar si ya está firmado ---
        $alumno['yaFirmado'] = $alumnoModelo->comprobarFirmaExistente($alumno['id_asignacion'] ?? 0);
        // -------------------------------------------------------
        echo json_encode($alumno);
    }
    exit();
}

public function editarAlumno() {
    $alumnoModelo = new Alumnos();

    $idAlumno = $_POST['id_alumno'];
    $idConvenio = $_POST['id_convenio'];
    $enviado = isset($_POST['enviado']) ? 1 : 0;

    if (empty($idConvenio)) {
        $alumnoModelo->eliminarAsignacion($idAlumno);
        $alumnoModelo->actualizarDatosBasicos(
            $idAlumno,
            trim($_POST['nombre']),
            trim($_POST['apellido1']),
            trim($_POST['apellido2'] ?? ''),
            strtoupper(trim($_POST['dni'])),
            $_POST['sexo'],
            trim($_POST['correo'] ?? ''),
            trim($_POST['telefono'] ?? '') // <-- AÑADE ESTO SI TU MODELO LO PIDE
        );
        header('Location: index.php?tab=2&res=limpiado');
        exit();
    }

    // Lógica normal de edición
    $alumnoModelo->editarAlumno(
        $idAlumno,
        trim($_POST['nombre']),
        trim($_POST['apellido1']),
        trim($_POST['apellido2'] ?? ''),
        strtoupper(trim($_POST['dni'])),
        $_POST['sexo'],
        trim($_POST['correo'] ?? ''),
        trim($_POST['telefono'] ?? ''), // <-- AQUÍ VA EL 8º ARGUMENTO (TELÉFONO)
        $idConvenio,
        $_POST['fecha_inicio'] ?: null,
        $_POST['fecha_final'] ?: null,
        trim($_POST['horario'] ?? ''),
        $_POST['horas_dia'] ?: null,
        $enviado
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

public function firmarAlumno() {
    // Validamos que vengan los datos necesarios
    if (isset($_POST['id_asignacion']) && isset($_POST['enviado_estado'])) {
        $idAsig = (int)$_POST['id_asignacion'];
        $enviado = (int)$_POST['enviado_estado'];

        // Doble check de seguridad
        if ($enviado === 1) {
            // No necesitas require_once de nuevo porque ya está arriba en el fichero
            $alumnoModelo = new Alumnos(); 
            $resultado = $alumnoModelo->firmarAsignacion($idAsig);
            
            if ($resultado) {
                header("Location: index.php?tab=2&res=firmado_ok");
            } else {
                header("Location: index.php?tab=2&res=error_db");
            }
        } else {
            header("Location: index.php?tab=2&res=error_no_enviado");
        }
        exit();
    }
}

public function devolverAlumnoAEnvio() {
    // Cambiamos $_POST por $_REQUEST para que acepte tanto POST como GET
    $idAlumno = $_REQUEST['id_alumno'] ?? null;

    if ($idAlumno) {
        $modelo = new Alumnos();
        $resultado = $modelo->devolverAlumnoAEnvio($idAlumno);

        if ($resultado) {
            header("Location: index.php?controlador=Tutores&accion=mostrarPanel&tab=3&res=devuelto_ok");
        } else {
            header("Location: index.php?controlador=Tutores&accion=mostrarPanel&tab=3&res=error_bd");
        }
    } else {
        header("Location: index.php?controlador=Tutores&accion=mostrarPanel&tab=3");
    }
    exit();
}

public function marcarComoExportado() {
    // Limpiamos cualquier salida previa para asegurar un JSON puro
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');

    $id = filter_input(INPUT_POST, 'id_asignacion', FILTER_VALIDATE_INT);
    $response = ['success' => false];

    if ($id) {
        try {
            require_once __DIR__ . '/../Modelo/Alumnos.php';
            $modelo = new Alumnos(); 
            
            $resultado = $modelo->actualizarEstadoExportacion($id, 1);
            
            if ($resultado === true) {
                $response['success'] = true;
            } else {
                // Si el modelo devuelve un array con error, lo capturamos
                $response['error'] = is_array($resultado) ? $resultado['error'] : "No se encontró el registro o ya está exportado";
            }
        } catch (Exception $e) {
            $response['error'] = "Excepción: " . $e->getMessage();
        }
    } else {
        $response['error'] = "ID de asignación no válido o ausente";
    }

    echo json_encode($response);
    exit;
}

public function guardarNuevoConvenio() {
    $convControlador = new Convenios_Controlador();
    // Llamamos al método que creamos en el controlador de convenios
    $convControlador->guardarNuevoConvenioPendiente();
}

public function aprobarNuevo() {
    $convControlador = new Convenios_Controlador();
    $convControlador->aprobarNuevo();
}

}