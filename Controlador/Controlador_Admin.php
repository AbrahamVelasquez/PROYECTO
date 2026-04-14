<?php
require_once './Modelo/Admin.php';

class Admin_Controlador {

    private $admin; 

    public function __construct() {
        $this->admin = new Admin();
    }

    // Acción por defecto: muestra el "Home" del admin
    public function mostrarPanel() {
        $subVista = 'Dashboard_Home.php'; // Una pequeña vista con los botones
        require 'Vista/Vista_Admin.php';
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

        $subVista = 'Tabla_Tutores.php';
        require 'Vista/Vista_Admin.php';
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
        
        $subVista = 'Tabla_Convenios.php';
        require 'Vista/Vista_Admin.php';
    }

    public function mostrarConveniosPendientes() {
        // Obtenemos los convenios aprobados pero no agregados todavía
        $pendientes = $this->admin->obtenerConveniosPendientes();
        
        // Definimos la nueva vista
        $subVista = 'Tabla_Convenios_Pendientes.php';
        require 'Vista/Vista_Admin.php';
    }

    // Busca en tu Controlador_Admin.php y REEMPLAZA las funciones repetidas por esta:
public function validarConvenio() {
    // 1. Caso: Viene del MODAL (Edición manual)
    // Detectamos esto porque el modal envía el campo 'nombre_empresa'
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
        
        // Llamamos a la función del modelo que procesa el array de datos
        $this->admin->procesarValidacionManual($datos);
    } 
    // 2. Caso: Viene del BOTÓN RÁPIDO de la tabla
    // Solo recibimos el ID, así que usamos los datos que ya están en la BD
    else if (isset($_POST['id_convenio_nuevo'])) {
        $id = $_POST['id_convenio_nuevo'];
        $this->admin->validarConvenio($id);
    }

    // Al terminar cualquiera de los dos procesos, refrescamos la vista
    $this->mostrarConveniosPendientes();
}

} // Admin_Controlador