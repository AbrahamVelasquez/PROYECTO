<?php
session_start();

// 1. GESTIÓN DE LOGOUT
if (isset($_POST['btnLogOut']) && isset($_SESSION['usuario'])) {
    require_once 'Controlador/Logout.php';
    exit();
}

// 2. SESIÓN ACTIVA
else if (isset($_SESSION['usuario'])) {

    // Determinamos el controlador según el Rol
    if ($_SESSION['rol'] == 'tutor') {
        $nomControlador = "Tutores";
    } else if ($_SESSION['rol'] == 'admin') {
        $nomControlador = "Admin";
    } else {
        die("Error: Rol no reconocido.");
    }

    // Capturamos la acción (por defecto mostrarPanel)
    $accion = $_REQUEST['accion'] ?? "mostrarPanel";

    // Carga dinámica del controlador
    $rutaControlador = 'Controlador/Controlador_' . $nomControlador . ".php";

    if (file_exists($rutaControlador)) {
        require_once $rutaControlador;
        $nombreClase = $nomControlador . "_Controlador";
        $controlador = new $nombreClase();

        // Verificamos si la acción existe en el controlador (sea mostrarPanel o guardarNuevoConvenio)
        if (method_exists($controlador, $accion)) {
            $controlador->$accion(); // Ejecución más limpia
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