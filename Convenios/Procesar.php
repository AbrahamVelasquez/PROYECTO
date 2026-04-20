<?php
// Convenios/Procesar.php
require_once __DIR__ . '/Controlador_Registro.php';

$controlador = new Controlador_Registro();

// Simplemente ejecutamos. El controlador de convenios 
// devolverá true/false al final, pero para el AJAX un status 200 es suficiente.
$controlador->procesarRegistro();

// No imprimimos nada más para que el fetch() reciba una respuesta limpia
http_response_code(200); 
exit();