<?php

// Controlador/Controlador_Admin.php

require_once './Modelo/Admin.php';

class Admin_Controlador {

    private $admin; 

    public function __construct() {
        $this->admin = new Admin();
    }

    // Acción por defecto: muestra el "Home" del admin
    public function mostrarPanel() {
        $subVista = '../Components/Dashboard_Sections.php'; // Una pequeña vista con los botones
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    // Acción para ver la tabla de tutores
    public function mostrarTutores() {
        $busqueda = $_POST['busqueda'] ?? '';
        $ordenar = $_POST['ordenar'] ?? 'id';
        $filtro_curso = $_POST['filtro_curso'] ?? '';

        $admin = new Admin();
        $tutores = $admin->obtenerTutores($busqueda, $ordenar, $filtro_curso);
        $ciclosLibres = $this->admin->obtenerCiclosLibres(); // <--- Preparar aquí
        $todosLosCiclos = $this->admin->obtenerTodosLosCiclos();

        $subVista = '../Sections/Tabla_Tutores.php';
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

            $admin = new Admin();
            $admin->guardarTutor($datos);
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
            
            $admin = new Admin();
            $admin->actualizarTutor($datos);
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
        $busqueda = $_POST['busqueda'] ?? '';
        // Cambia 'nombre' por 'nombre_empresa' o como se llame en tu BD
        $ordenar = $_POST['ordenar'] ?? 'nombre_empresa'; 
        
        $convenios = $this->admin->obtenerConvenios($busqueda, $ordenar);
        
        $subVista = '../Sections/Tabla_Convenios.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function mostrarConveniosPendientes() {
        // Obtenemos los convenios aprobados pero no agregados todavía
        $pendientes = $this->admin->obtenerConveniosPendientes();
        
        // Definimos la nueva vista
        $subVista = '../Sections/Tabla_Convenios_Pendientes.php';
        require 'Vista/Admin/Dashboard_Admin.php';
    }

    public function validarConvenio() {
        if (isset($_POST['nombre_empresa'])) {
            $datos = [
                'id_convenio_nuevo'    => $_POST['id_convenio_nuevo'],
                'nombre_empresa'       => $_POST['nombre_empresa'],
                'cif'                  => $_POST['cif'],
                'direccion'            => $_POST['direccion'],
                'municipio'            => $_POST['municipio'],
                'cp'                   => $_POST['cp'],
                'pais'                 => $_POST['pais'],
                'telefono'             => $_POST['telefono'],
                'fax'                  => $_POST['fax'],
                'mail'                 => $_POST['mail'],
                'nombre_representante' => $_POST['nombre_representante'],
                'dni_representante'    => $_POST['dni_representante'],
                'cargo'                => $_POST['cargo']
            ];

            // --- EL CAMBIO ESTÁ AQUÍ ---
            if (isset($_POST['solo_guardar'])) {
                // Solo actualizamos el borrador en la tabla de pendientes
                $this->admin->actualizarConvenioPendiente($datos);
            } else {
                // Si no hay 'solo_guardar', entonces validamos y movemos a la tabla definitiva
                $this->admin->procesarValidacionManual($datos);
            }
            // ---------------------------
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
        // Verificamos que los datos mínimos existan
        if (isset($_POST['id_convenio']) && (isset($_POST['cif_original']) || isset($_POST['nombre_original']) )) {
            
            $id_convenio = $_POST['id_convenio'];
            $cif_original = $_POST['cif_original']; // CIF antiguo para rastrear el registro
            $nombre_original = $_POST['nombre_original']; // Capturamos el nombre previo
            
            $datosActualizados = [
                'nombre_empresa'      => $_POST['nombre_empresa'],
                'cif'                 => $_POST['cif'],
                'telefono'            => $_POST['telefono'],
                'mail'                => $_POST['mail'],
                'fax'                 => $_POST['fax'],
                'direccion'           => $_POST['direccion'],
                'municipio'           => $_POST['municipio'],
                'cp'                  => $_POST['cp'],
                'pais'                => $_POST['pais'],
                'nombre_representante'=> $_POST['nombre_representante'],
                'dni_representante'   => $_POST['dni_representante'],
                'cargo'               => $_POST['cargo']
            ];

            // 1. Instanciamos el modelo si no está disponible globalmente
            // $admin = new Admin(); 

            // 2. Actualizamos la tabla 'convenios' (la oficial)
            $this->admin->actualizarConvenio($id_convenio, $datosActualizados);

            // 3. Sincronizamos con la tabla 'convenios_nuevos' por si existe borrador con ese CIF o Nombre
            $this->admin->sincronizarConvenioPendiente($cif_original, $nombre_original, $datosActualizados);

            // 4. Redirección
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

} // Llave de la clase