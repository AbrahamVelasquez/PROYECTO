<?php

/**
 * Convenios/Procesar.php — Endpoint POST del formulario público de solicitud
 *
 * Delega inmediatamente en Controlador_Registro::procesarRegistro() y responde
 * con HTTP 200 si todo va bien o 500 si el controlador lanza una excepción.
 * No devuelve HTML: el resultado lo gestiona el JS del formulario Registro.php.
 */

require_once __DIR__ . '/Controlador_Registro.php';

try {
    $controlador = new Controlador_Registro();
    $controlador->procesarRegistro();
    http_response_code(200);
} catch (Throwable $e) {
    http_response_code(500);
}
exit();