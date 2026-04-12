<?php
session_start();

// 1. GESTIÓN DE LOGOUT
if (isset($_POST['btnLogOut']) && isset($_SESSION['usuario'])) {
    require_once 'Controlador/Logout.php';
    exit();
}

// 2. SESIÓN ACTIVA
else if (isset($_SESSION['usuario'])) {

    // GUARDAR NUEVO CONVENIO (viene de Registro_Convenio.php)
    if (isset($_POST['accion']) && $_POST['accion'] === 'guardarNuevoConvenio') {
        require_once 'Controlador/Controlador_Convenios.php';
        $ctrlConv = new Convenios_Controlador();
        $ctrlConv->guardarNuevoConvenio();
        exit();
    }

    // Determinamos el controlador según el Rol
    if ($_SESSION['rol'] == 'tutor') {
        $nomControlador = "Tutores";
    } else if ($_SESSION['rol'] == 'admin') {
        $nomControlador = "Admin";
    }

    // Si no viene acción, definimos una por defecto para que no explote
    $accionDefault = "mostrarPanel";

    // Capturamos la acción real (Prioridad a lo que venga por el formulario)
    $accion = $_REQUEST['accion'] ?? $accionDefault;

    // Carga dinámica del controlador
    $rutaControlador = 'Controlador/Controlador_' . $nomControlador . ".php";

    if (file_exists($rutaControlador)) {
        require_once $rutaControlador;
        $nombreClase = $nomControlador . "_Controlador";
        $controlador = new $nombreClase();

        if (method_exists($controlador, $accion)) {
            call_user_func(array($controlador, $accion));
        } else {
            die("Error: La acción [{$accion}] no existe en el controlador [{$nombreClase}].");
        }
    } else {
        die("Error: No se encontró el archivo del controlador en: {$rutaControlador}");
    }
}

// 3. PROCESO DE LOGIN
else if (!empty($_REQUEST['btnLogIn'])) {
    require_once 'Controlador/Controlador_Usuarios.php';
    $user = new Usuarios_Controlador();
    $user->validarUsuario();
}

// 4. PANTALLA INICIAL
else {
    require_once './Vista/Login.php';
    exit();
}