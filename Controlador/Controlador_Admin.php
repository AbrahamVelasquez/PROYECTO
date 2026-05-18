<?php

// Controlador/Controlador_Admin.php

require_once './Modelo/Admin.php';

class Admin_Controlador {

    private $admin; 

    public function __construct() {
        // Inicializamos el modelo una sola vez para usarlo en todos los métodos
        $this->admin = new Admin();
    }

    // Acción por defecto: muestra el "Home" del admin
    public function mostrarPanel() {
        $subVista = 'Admin/Components/Dashboard_Sections.php'; // Una pequeña vista con los botones
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    // Acción para ver la tabla de tutores
    public function mostrarTutores() {
        $busqueda     = $_REQUEST['busqueda'] ?? '';
        $ordenar      = $_REQUEST['ordenar'] ?? 'id';
        $filtro_curso = $_REQUEST['filtro_curso'] ?? '';

        // $admin = new Admin(); // <-- ELIMINADO: Ya usamos $this->admin
        $tutores = $this->admin->obtenerTutores($busqueda, $ordenar, $filtro_curso);
        $ciclosLibres = $this->admin->obtenerCiclosLibres(); // <--- Preparar aquí
        $todosLosCiclos = $this->admin->obtenerTodosLosCiclos();

        $subVista = 'Admin/Sections/Tabla_Tutores.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function guardarTutor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'guardarTutor') {
            
            $datos = [
                'dni'       => $_POST['dni'] ?? '',
                'nombre'    => $_POST['nombre'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'email'     => $_POST['email'] ?? '',
                'telefono'  => $_POST['telefono'] ?? '',
                'id_ciclo'  => $_POST['id_ciclo'] ?? ''
            ];

            // $admin = new Admin(); // <-- ELIMINADO
            $this->admin->guardarTutor($datos);
        }
        
        // Al terminar, volvemos a la tabla
        $this->mostrarTutores();
    }

    public function actualizarTutor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'id_tutor'  => $_POST['id_tutor'],
                'nombre'    => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'],
                'email'     => $_POST['email'],
                'telefono'  => $_POST['telefono'],
                'id_ciclo'  => $_POST['id_ciclo']
            ];
            
            // $admin = new Admin(); // <-- ELIMINADO
            $this->admin->actualizarTutor($datos);
        }
        $this->mostrarTutores();
    }

    public function eliminarTutor() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_tutor'])) {
            $this->admin->eliminarTutor($_POST['id_tutor']);
        }
        // Redirigimos a la tabla actualizada
        $this->mostrarTutores();
    }

    // En Controlador_Admin.php
    public function mostrarConvenios() {
        $busqueda = $_REQUEST['busqueda'] ?? '';
        // Cambia 'nombre' por 'nombre_empresa' o como se llame en tu BD
        $ordenar  = $_REQUEST['ordenar'] ?? 'nombre_empresa';      

        $convenios = $this->admin->obtenerConvenios($busqueda, $ordenar);
        
        $subVista = 'Admin/Sections/Tabla_Convenios.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function mostrarConveniosPendientes() {
        // Obtenemos los convenios aprobados pero no validados todavía
        $pendientes = $this->admin->obtenerConveniosPendientes();
        
        // Definimos la nueva vista
        $subVista = 'Admin/Sections/Tabla_Convenios_Pendientes.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function validarConvenio() {
        if (isset($_POST['nombre_empresa'])) {
            $datos = [
                'id_convenio_nuevo'      => $_POST['id_convenio_nuevo'],
                'nombre_empresa'         => $_POST['nombre_empresa'],
                'cif'                    => $_POST['cif'],
                'direccion'              => $_POST['direccion'],
                'localidad'              => $_POST['localidad']              ?? null,
                'cp'                     => $_POST['cp'],
                'telefono'               => $_POST['telefono']               ?? null,
                'fax'                    => $_POST['fax']                    ?? null,
                'representante'          => $_POST['representante']          ?? null,
                'especialidad'           => $_POST['especialidad']           ?? null,
                'num_convenio'           => $_POST['num_convenio']           ?? null,
                'fecha_alta_renovacion'  => $_POST['fecha_alta_renovacion']  ?? null,
                'fecha_nueva_renovacion' => $_POST['fecha_nueva_renovacion'] ?? null,
                'observaciones'          => $_POST['observaciones']          ?? null,
            ];

            if (isset($_POST['solo_guardar'])) {
                $this->admin->actualizarConvenioPendiente($datos);
            } else {
                $this->admin->procesarValidacionManual($datos);
            }
        } 
        else if (isset($_POST['id_convenio_nuevo'])) {
            $id = $_POST['id_convenio_nuevo'];
            $this->admin->validarConvenio($id);
        }

        $this->mostrarConveniosPendientes();
    }

    public function eliminarConvenio() {
        // 1. Ejecutamos el borrado si llega el ID
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_convenio_borrar'])) {
            $id = $_POST['id_convenio_borrar'];
            $this->admin->eliminarConvenio($id);
        }

        // 2. Después de borrar, redirigimos a la tabla para que se vea el cambio
        $this->mostrarConvenios(); 
    }

    /**
     * Procesa la actualización de un convenio y sincroniza con pendientes
     */
    public function actualizarConvenio() {
        if (isset($_POST['num_convenio']) && (isset($_POST['cif_original']) || isset($_POST['nombre_original']))) {
            
            $num_convenio    = $_POST['num_convenio'];
            $cif_original    = $_POST['cif_original'];
            $nombre_original = $_POST['nombre_original'];
            
            $datosActualizados = [
                'nombre_empresa'         => $_POST['nombre_empresa'],
                'cif'                    => $_POST['cif'],
                'telefono'               => $_POST['telefono']               ?? null,
                'fax'                    => $_POST['fax']                    ?? null,
                'direccion'              => $_POST['direccion']               ?? null,
                'localidad'              => $_POST['localidad']               ?? null,
                'cp'                     => $_POST['cp']                     ?? null,
                'representante'          => $_POST['representante']           ?? null,
                'especialidad'           => $_POST['especialidad']           ?? null ?: null,
                'fecha_alta_renovacion'  => $_POST['fecha_alta_renovacion']  ?? null ?: null,
                'fecha_nueva_renovacion' => $_POST['fecha_nueva_renovacion'] ?? null ?: null,
                'observaciones'          => $_POST['observaciones']          ?? null ?: null,
            ];

            $this->admin->actualizarConvenio($num_convenio, $datosActualizados);
            $this->admin->sincronizarConvenioPendiente($cif_original, $nombre_original, $datosActualizados);

            header("Location: index.php?accion=mostrarConvenios&res=success");
            exit();
        }
    }

    public function eliminarConvenioCompleto() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // Ejecutamos la eliminación en cadena
            $exito = $this->admin->borrarRegistroPendienteYOficial($id);
            
            if ($exito) {
                header("Location: index.php?accion=mostrarConveniosPendientes&msg=eliminado");
            } else {
                echo "Error al intentar eliminar el registro.";
            }
            exit();
        }
    }

    public function importarConvenios() {
        require_once 'Controlador/Importar_Convenios.php';
    }

    public function descargarPlantillaConvenios() {
        $ruta = ROOT_PATH . 'Recursos/Importar/plantilla_listadoConvenios.xlsx';
        if (!file_exists($ruta)) { http_response_code(404); exit('Plantilla no encontrada.'); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="plantilla_listadoConvenios.xlsx"');
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: no-cache');
        readfile($ruta);
        exit;
    }

    public function mostrarAlumnos() {
        $alumnos  = $this->admin->obtenerAlumnosPendientesFirma();
        $subVista = 'Admin/Sections/Listado_Alumnos.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function firmarAlumnoAdmin() {
        if (isset($_POST['id_asignacion'])) {
            $this->admin->firmarAsignacion($_POST['id_asignacion'], $_POST['anexo']);
        }
        $this->mostrarAlumnos();
    }

} // Llave de la clase

?>