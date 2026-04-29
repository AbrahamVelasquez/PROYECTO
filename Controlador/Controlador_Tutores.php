<?php

// Controlador/Controlador_Tutores.php

require_once 'Controlador/Controlador_Convenios.php';
require_once 'Modelo/Tutores.php';
require_once 'Modelo/Alumnos.php';
require_once 'Modelo/Convenios.php'; // Añadido para que funcione el objeto Convenios

class Tutores_Controlador {

    // --- VARIABLES DE CLASE (Propiedades) ---
    private $alumnoModelo;
    private $tutorModelo;
    private $convModelo;
    private $convControlador;

    public function __construct() {
        // Inicializamos los objetos una sola vez para evitar errores de "null"
        $this->alumnoModelo = new Alumnos(); 
        $this->tutorModelo = new Tutores();
        $this->convModelo = new Convenios();
        $this->convControlador = new Convenios_Controlador();
    }

    // Si bien ya existen las variables en el constructor,
    // por alguna razón se cambiaron y daba error al Javascript
    // de cambiar de de paso "Steps". Por ende, se decidió 
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
        
        $convenios = $data['busqueda_convenio'];
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
        // Pedimos los datos al modelo
        $modulosCiclo = $alumnoModelo->obtenerModulosPorTutor($idCicloTutor);
        $rasExistentes = $alumnoModelo->obtenerRAsPorTutor($idCicloTutor);

        // --- CARGA DE VISTA ---
        require_once 'Vista/Tutores/Dashboard_Tutores.php';
    }
    
    public function agregarAlumno() {
        if (!isset($_SESSION['id_ciclo'])) {
            die("Error: No se ha detectado el ciclo formativo en la sesión.");
        }

        $idCiclo = $_SESSION['id_ciclo'];
        // $alumnoModelo = new Alumnos(); // <-- ELIMINADO

        // Capturamos años si vinieran del formulario, si no, el modelo pondrá los actuales
        $anioInicio = $_POST['anio_inicio'] ?? date('Y');
        $anioFin = $_POST['anio_fin'] ?? (date('Y') + 1);

        $resultado = $this->alumnoModelo->agregarAlumno(
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
        // 1. Limpiar cualquier eco previo para que el JSON sea puro
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if (isset($_POST['verificar_firma'])) {
            $idAsig = (int)$_POST['id_asignacion'];
            $yaFirmado = $this->alumnoModelo->comprobarFirmaExistente($idAsig);
            echo json_encode(['yaFirmado' => $yaFirmado]);
            exit();
        }

        $idAlumno = isset($_POST['id_alumno']) ? (int)$_POST['id_alumno'] : 0;
        
        // Obtenemos los datos del modelo
        $alumno = $this->alumnoModelo->obtenerPorId($idAlumno);

        if (!$alumno) {
            echo json_encode(['error' => 'Alumno no encontrado', 'id_recibido' => $idAlumno]);
        } else {
            // IMPORTANTE: Nos aseguramos de que id_convenio sea un entero o null, no un string vacío
            $alumno['id_convenio'] = $alumno['id_convenio'] ? (int)$alumno['id_convenio'] : null;
            
            // Verificar firma
            $alumno['yaFirmado'] = $this->alumnoModelo->comprobarFirmaExistente($alumno['id_asignacion'] ?? 0);
            
            echo json_encode($alumno);
        }
        exit();
    }

    public function editarAlumno() {
        // $alumnoModelo = new Alumnos(); // <-- ELIMINADO

        $idAlumno = $_POST['id_alumno'];
        $idConvenio = $_POST['id_convenio'];
        $enviado = isset($_POST['enviado']) ? 1 : 0;

        if (empty($idConvenio)) {
            $this->alumnoModelo->eliminarAsignacion($idAlumno);
            $this->alumnoModelo->actualizarDatosBasicos(
                $idAlumno,
                trim($_POST['nombre']),
                trim($_POST['apellido1']),
                trim($_POST['apellido2'] ?? ''),
                strtoupper(trim($_POST['dni'])),
                $_POST['sexo'],
                trim($_POST['correo'] ?? ''),
                trim($_POST['telefono'] ?? '') 
            );
            header('Location: index.php?tab=2&res=limpiado');
            exit();
        }

        // Lógica normal de edición
        $this->alumnoModelo->editarAlumno(
            $idAlumno,
            trim($_POST['nombre']),
            trim($_POST['apellido1']),
            trim($_POST['apellido2'] ?? ''),
            strtoupper(trim($_POST['dni'])),
            $_POST['sexo'],
            trim($_POST['correo'] ?? ''),
            trim($_POST['telefono'] ?? ''),
            $idConvenio,
            $_POST['fecha_inicio'] ?: null,
            $_POST['fecha_final'] ?: null,
            trim($_POST['horario'] ?? ''),
            $_POST['horas_dia'] ?: null,
            $_POST['num_total_horas'] ?? null,
            $enviado,
            trim($_POST['nombre_tutor_empresa'] ?? ''),
            trim($_POST['correo_tutor_empresa'] ?? ''),
            trim($_POST['tel_tutor_empresa'] ?? '')
        );
        
        header('Location: index.php?tab=2');
        exit();
    }

    public function firmarAlumno() {
        $id_asignacion = $_POST['id_asignacion'] ?? null;
        $anexo = $_POST['anexo'] ?? null; // Recogemos el anexo del modal

        if ($id_asignacion) {

            // Usamos la variable $alumnoModelo directamente para llamar a la función
            $resultado = $this -> alumnoModelo->firmarAsignacion($id_asignacion, $anexo);
            
            if ($resultado) {
                header("Location: index.php?controlador=Tutores&tab=2&msg=firmado");
                exit; // Siempre es bueno poner exit después de un header Location
            } else {
                // Aquí podrías redirigir con un mensaje de error si falla la DB
                header("Location: index.php?controlador=Tutores&tab=2&msg=error");
                exit;
            }
        }
    }

    public function devolverAlumnoAEnvio() {
        // Cambiamos $_POST por $_REQUEST para que acepte tanto POST como GET
        $idAlumno = $_REQUEST['id_alumno'] ?? null;

        if ($idAlumno) {
            // $modelo = new Alumnos(); // <-- ELIMINADO
            $resultado = $this->alumnoModelo->devolverAlumnoAEnvio($idAlumno);

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
        // 1. Limpiar cualquier salida previa (evita el error de "position 1")
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_asignacion'])) {
                $idAsignacion = intval($_POST['id_asignacion']);

                // Guardar el anexo si viene informado
                if (isset($_POST['anexo']) && $_POST['anexo'] !== '') {
                    $this->alumnoModelo->actualizarAnexo($idAsignacion, $_POST['anexo']);
                }
                
                // Pasamos todo el $_POST al modelo
                $resultado = $this->alumnoModelo->actualizarTodoYExportar($idAsignacion, $_POST);

                if ($resultado) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'No se pudo actualizar la base de datos']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Faltan parámetros']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit(); // Importante para que no se imprima nada más después
    }

    public function guardarRA() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        try {
            $idCiclo     = $_SESSION['id_ciclo'] ?? 0;
            $rasNuevos   = json_decode($_POST['ra_nuevos']  ?? '[]', true) ?? [];
            $raEliminados = json_decode($_POST['ra_eliminar'] ?? '[]', true) ?? [];

            $resultado = $this->alumnoModelo->guardarResultadosAprendizaje($idCiclo, $rasNuevos, $raEliminados);
            echo json_encode(['success' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function obtenerRAs() {
        // 1. Limpiar cualquier eco o espacio previo
        if (ob_get_level()) ob_end_clean(); 
        
        header('Content-Type: application/json; charset=utf-8');

        try {
            $idCicloTutor = $_SESSION['id_ciclo'] ?? 0;
            $ras = $this -> alumnoModelo->obtenerRAsPorTutor($idCicloTutor);

            // 2. Si no hay datos, enviamos un array vacío legal
            if (!$ras) $ras = [];

            echo json_encode($ras, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit; // 3. IMPORTANTE: Detener todo aquí
    }

    public function guardarNuevoConvenio() {
        // Llamamos al método que creamos en el controlador de convenios
        $this->convControlador->guardarNuevoConvenioPendiente();
    }

    public function aprobarNuevo() {
        $this->convControlador->aprobarNuevo();
    }

    public function editarConvenioNuevo() {
        // Llamamos al método que procesa la edición en el controlador de convenios
        $this->convControlador->editarConvenioNuevo();
    }

    public function eliminarConvenioNuevo() {
        $id = $_POST['id_convenio_nuevo'] ?? null;
        if ($id) {
            // $convModelo = new Convenios(); // <-- ELIMINADO
            $exito = $this->convModelo->eliminarConvenioNuevo($id);
            
            if ($exito) {
                $_SESSION['mensaje_exito'] = "Solicitud eliminada correctamente.";
            } else {
                $_SESSION['error_convenio'] = "No se pudo eliminar la solicitud.";
            }
        }
        header('Location: index.php?tab=1');
        exit();
    }

    public function exportarExcelPF() {
        require_once 'Controlador/Exportar_PF.php';
    }

    public function exportarAlumnosWord() {
        require_once 'Controlador/Exportar_Alumnos_Word.php';
    }

    public function importarAlumnos() {
        require_once 'Controlador/Importar_Alumnos.php';
    }

    public function descargarPlantillaAlumnos() {
        $ruta = ROOT_PATH . 'Recursos/Importar/plantilla_listadoAlumnos.xlsx';
        if (!file_exists($ruta)) {
            http_response_code(404);
            exit('Plantilla no encontrada.');
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="plantilla_listadoAlumnos.xlsx"');
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: no-cache');
        readfile($ruta);
        exit;
    }

    public function exportarTodoPF() {
        require_once 'Controlador/Exportar_PF_Todo.php';
    }

    public function cambiarEstadoExportacion() {
        // Verificamos seguridad básica y método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        // Decodificamos el JSON que enviamos desde el JS
        $idsRaw = $_POST['ids_asignacion'] ?? '[]';
        $idsAsignacion = json_decode($idsRaw, true);
        $nuevoEstado = isset($_POST['nuevo_estado']) ? (int)$_POST['nuevo_estado'] : 0;

        if (empty($idsAsignacion)) {
            echo json_encode(['success' => false, 'error' => 'No se recibieron IDs para actualizar']);
            return;
        }
        
        $resultado = $this->alumnoModelo->reiniciarEstadoExportacion($idsAsignacion, $nuevoEstado);

        if ($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar la base de datos']);
        }
    }

/*
    public function exportarAlumnos() {
        // Verificamos si llegan IDs por POST
        if (isset($_POST['exportar_ids']) && is_array($_POST['exportar_ids'])) {
            // $alumnoModelo = new Alumnos(); // <-- ELIMINADO

            foreach ($_POST['exportar_ids'] as $idAlumno) {
                // Importante: castear a int para seguridad
                $this->alumnoModelo->marcarComoEnviado((int)$idAlumno);
            }

            // Redirigimos para ver los cambios
            header('Location: index.php?tab=2&status=success');
        } else {
            // Si no llega nada, redirigimos igual para no quedar en blanco
            header('Location: index.php?tab=2&status=error');
        }
        exit();
    }
*/

} // Llave de la clase

?>