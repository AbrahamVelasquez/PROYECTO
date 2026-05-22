<?php

/**
 * Controlador/Logout.php — Cierre de sesión
 *
 * Se incluye desde index.php cuando el usuario pulsa el botón de salida.
 * Destruye todos los datos de la sesión y redirige al index.php,
 * que al no encontrar $_SESSION['usuario'] mostrará automáticamente el login.
 *
 * Si alguien intenta acceder a esta URL sin sesión activa, simplemente
 * no hace nada (la condición no se cumple y no hay error).
 */

if (isset($_SESSION['usuario'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
}

?>
