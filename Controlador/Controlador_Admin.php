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

} // Admin_Controlador