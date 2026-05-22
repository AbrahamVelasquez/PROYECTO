<?php

/**
 * Seguridad/Control_Accesos.php — Validación de sesión y permisos por rol
 *
 * Este archivo se incluye al inicio de vistas y helpers que requieren
 * autenticación. Basta con llamar a validarAcceso('tutor') o
 * validarAcceso('admin') para proteger cualquier recurso.
 *
 * Comprueba dos cosas en orden:
 *   1. Que existe una sesión activa (el usuario se identificó)
 *   2. Que el rol de la sesión coincide con el rol requerido
 *
 * Si alguna comprobación falla, el usuario ve una pantalla de error
 * con código HTTP correcto (401 sin sesión, 403 sin permisos)
 * y la ejecución se detiene con exit().
 *
 * MVC: Capa de seguridad transversal. No pertenece al flujo MVC
 * pero es invocada por Vistas y Helpers antes de hacer cualquier cosa.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario tiene sesión activa y el rol correcto.
 * Usar 'cualquiera' como $rolPermitido solo comprueba que hay sesión,
 * sin importar el rol.
 */
function validarAcceso($rolPermitido) {
    $titulo     = "";
    $mensaje    = "";
    $hayError   = false;
    $codigoHttp = 200;

    if (!isset($_SESSION['usuario'])) {
        $titulo     = "Sesión no iniciada";
        $mensaje    = "Para acceder a este apartado de la aplicación, es necesario identificarse primero.";
        $hayError   = true;
        $codigoHttp = 401;
    }
    else if ($rolPermitido !== 'cualquiera' && (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rolPermitido)) {
        $titulo     = "Acceso Restringido";
        $mensaje    = "Tu cuenta no tiene los permisos de <b>" . ucfirst($rolPermitido) . "</b> necesarios para ver esta sección.";
        $hayError   = true;
        $codigoHttp = 403;
    }

    if ($hayError) {
        http_response_code($codigoHttp);
        mostrarError($titulo, $mensaje);
    }
}

/**
 * Muestra la página de error 403 y detiene la ejecución.
 * Reconstruye la ruta dinámica hacia /Errores/403.php para funcionar
 * independientemente del nombre de la carpeta del proyecto.
 * Elimina el fragmento "/Vista/..." de la ruta si la petición
 * viene de una vista incluida directamente.
 */
function mostrarError($titulo, $mensaje) {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host      = $_SERVER['HTTP_HOST'];

    $scriptName     = $_SERVER['SCRIPT_NAME'];
    $directorioRaiz = str_replace('\\', '/', dirname($scriptName));

    // Si la ruta incluye "/Vista", cortamos ahí para obtener la raíz del proyecto
    $pos = stripos($directorioRaiz, '/vista');
    if ($pos !== false) {
        $directorioRaiz = substr($directorioRaiz, 0, $pos);
    }

    $directorioRaiz = rtrim($directorioRaiz, '/');
    $urlInicio      = $protocolo . "://" . $host . $directorioRaiz . "/index.php";

    include $_SERVER['DOCUMENT_ROOT'] . $directorioRaiz . '/Errores/403.php';
    exit();
}