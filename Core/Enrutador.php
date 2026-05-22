<?php

/**
 * Core/Enrutador.php — Cargador seguro de controladores y gestor de errores
 *
 * Se encarga de dos cosas principales:
 *   1. Mostrar errores de forma limpia y centralizada (sin stacks de PHP en pantalla)
 *   2. Cargar controladores de forma segura, comprobando que el archivo existe,
 *      que la clase está definida y que el método solicitado existe antes de llamarlo
 *
 * Tiene dos modos de carga:
 *   - ejecutarControladorProtegido: para peticiones con sesión activa (panel tutor/admin)
 *   - ejecutarLoginProtegido: para el proceso de autenticación inicial
 *
 * MVC: Infraestructura central — actúa como capa de seguridad entre index.php
 * y los Controladores, capturando cualquier excepción antes de que llegue al usuario.
 */

class Enrutador {

    /**
     * Muestra una pantalla de error limpia y detiene la ejecución.
     * Limpia el buffer de salida para que no aparezcan warnings de PHP encima del error.
     * Los datos del error se pasan via $GLOBALS para que la vista los lea.
     */
    public static function mostrarError($codigo, $titulo, $mensaje, $urlBoton = "index.php", $textoBoton = "Volver al panel principal") {
        if (ob_get_length()) ob_clean();

        $GLOBALS['errorCodigo']  = $codigo;
        $GLOBALS['errorTitulo']  = $titulo;
        $GLOBALS['errorMensaje'] = $mensaje;
        $GLOBALS['urlBoton']     = $urlBoton;
        $GLOBALS['textoBoton']   = $textoBoton;

        require_once 'Errores/AlertaSistema.php';
        exit();
    }

    /**
     * Carga y ejecuta un controlador de forma protegida (para usuarios con sesión activa).
     *
     * Comprueba en orden:
     *   1. Que el archivo del controlador existe en disco
     *   2. Que la clase NombreControlador_Controlador está definida
     *   3. Que el método solicitado ($accion) existe en esa clase
     *
     * Cualquier excepción no controlada dentro del controlador o sus modelos
     * queda atrapada aquí y se muestra como error 500, sin romper la página.
     */
    public static function ejecutarControladorProtegido($rutaControlador, $nomControlador, $accion) {
        if (!file_exists($rutaControlador)) {
            self::mostrarError("404", "Módulo No Encontrado", "El controlador general '{$nomControlador}' no se encuentra en el servidor.", "javascript:window.location.reload();", "Reintentar conexión");
        }

        try {
            require_once $rutaControlador;
            $nombreClase = $nomControlador . "_Controlador";

            if (!class_exists($nombreClase)) {
                throw new Error("La clase '{$nombreClase}' no se encuentra definida en el archivo raíz.");
            }

            $controlador = new $nombreClase();

            if (method_exists($controlador, $accion)) {
                $controlador->$accion();
            } else {
                self::mostrarError("404", "Acción Inválida", "La acción '{$accion}' solicitada no pertenece al controlador activo.");
            }

        } catch (Throwable $e) {
            self::mostrarError("500", "Fallo en Submódulo o Modelo", "Error crítico al cargar componentes secundarios: " . $e->getMessage(), "javascript:window.location.reload();", "Reintentar conexión");
        }
    }

    /**
     * Carga y ejecuta el proceso de login de forma protegida.
     *
     * Antes de require_once comprueba que tanto el controlador como el modelo
     * de usuarios existen en disco, para dar un mensaje de error claro si
     * falta algún archivo en lugar de un fatal error de PHP.
     */
    public static function ejecutarLoginProtegido($rutaAuth, $rutaModeloUser) {
        if (!file_exists($rutaAuth)) {
            self::mostrarError("500", "Servicio Caído", "Falta el componente del controlador de inicio de sesión.", "javascript:window.location.reload();", "Reintentar conexión");
        }

        if (!file_exists($rutaModeloUser)) {
            self::mostrarError("500", "Fallo de Estructura", "Falta el archivo esencial del modelo de datos ('{$rutaModeloUser}').", "javascript:window.location.reload();", "Reintentar conexión");
        }

        try {
            require_once $rutaAuth;

            if (!class_exists('Usuarios_Controlador')) {
                throw new Error("La clase 'Usuarios_Controlador' no está definida en el archivo de autenticación.");
            }

            $user = new Usuarios_Controlador();
            $user->validarUsuario();

        } catch (Throwable $e) {
            self::mostrarError("500", "Error de Lógica Interna", "Se detectó un problema en el código de autenticación: " . $e->getMessage(), "javascript:window.location.reload();", "Reintentar conexión");
        }
    }
}