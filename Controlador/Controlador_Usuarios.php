<?php

/**
 * Controlador/Controlador_Usuarios.php — Autenticación de usuarios
 *
 * Orquesta el proceso de login: recibe la petición desde index.php
 * y delega la validación de credenciales al modelo Usuarios.
 *
 * Deliberadamente thin — toda la lógica de credenciales y sesión
 * vive en el modelo para poder ser reutilizada o probada de forma independiente.
 *
 * MVC: Controlador de autenticación. Solo tiene una responsabilidad:
 * conectar el formulario de login con el modelo Usuarios.
 */

require_once './Modelo/Usuarios.php';

class Usuarios_Controlador {

    // Instancia del modelo que gestiona credenciales y sesión
    private $usuario;

    public function __construct() {
        $this->usuario = new Usuarios();
    }

    // Punto de entrada del login. El modelo se encarga de validar, abrir sesión y redirigir.
    public function validarUsuario() {
        $this->usuario->validarDatos();
    }

} // Llave de la clase

?>
