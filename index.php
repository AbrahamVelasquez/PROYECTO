<?php

/**
 * index.php — Punto de entrada de la aplicación (Front Controller)
 *
 * Todo pasa por aquí. Este archivo actúa como router principal:
 * comprueba el estado de la sesión y deriva la ejecución al
 * controlador correcto según el rol del usuario o la acción solicitada.
 *
 * Flujo en orden de prioridad:
 *   1. Logout       → destruye la sesión y redirige al login
 *   2. Sesión activa → carga el controlador del rol (Tutores o Admin)
 *   3. Submit login → delega credenciales a Controlador_Usuarios
 *   4. Sin sesión   → muestra el formulario de login
 *
 * MVC: actúa como intermediario entre el navegador y los Controladores.
 * No contiene lógica de negocio — solo enrutamiento y comprobaciones de sesión.
 */

ob_start();
session_start();

define('ROOT_PATH', __DIR__ . '/');

// Cargamos el Enrutador antes que nada — gestiona errores críticos de forma centralizada
require_once 'Core/Enrutador.php';

// ── 1. GESTIÓN DE LOGOUT ──────────────────────────────────────────────────────
// Se acepta tanto el botón del formulario (btnLogOut) como el parámetro directo (LogOut)
if ((isset($_REQUEST['btnLogOut']) || isset($_REQUEST['LogOut'])) && isset($_SESSION['usuario'])) {
    require_once 'Controlador/Logout.php';
    exit();
}

// ── 2. SESIÓN ACTIVA ──────────────────────────────────────────────────────────
// Si el usuario ya está identificado, cargamos su controlador según el rol
else if (isset($_SESSION['usuario'])) {

    if ($_SESSION['rol'] == 'tutor') {
        $nomControlador = "Tutores";
    } else if ($_SESSION['rol'] == 'admin') {
        $nomControlador = "Admin";
    } else {
        // Un rol desconocido en sesión es una anomalía — forzamos el cierre
        Enrutador::mostrarError("403", "Acceso Denegado", "El rol asignado a tu cuenta no está reconocido.", "index.php?LogOut", "Cerrar sesión y volver");
    }

    // La acción por defecto siempre es mostrar el panel principal del rol
    $accion = $_REQUEST['accion'] ?? "mostrarPanel";
    $rutaControlador = 'Controlador/Controlador_' . $nomControlador . ".php";

    // El Enrutador se encarga de cargar el archivo, instanciar la clase y llamar al método
    Enrutador::ejecutarControladorProtegido($rutaControlador, $nomControlador, $accion);
}

// ── 3. PROCESO DE LOGIN ───────────────────────────────────────────────────────
// Solo llega aquí si hay datos en el POST del formulario de login
else if (!empty($_REQUEST['btnLogIn'])) {
    $rutaAuth       = 'Controlador/Controlador_Usuarios.php';
    $rutaModeloUser = 'Modelo/Usuarios.php';

    // El Enrutador comprueba que ambos archivos existen antes de intentar cargarlos
    Enrutador::ejecutarLoginProtegido($rutaAuth, $rutaModeloUser);
}

// ── 4. PANTALLA INICIAL ───────────────────────────────────────────────────────
// Sin sesión y sin POST de login → mostramos la pantalla de acceso
else {
    require_once './Vista/Login.php';
    exit();
}