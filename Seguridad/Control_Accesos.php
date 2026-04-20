<?php

// Seguridad/Control_Accesos.php

// Su función es la de agregar una capa de seguridad en caso de conocer la
// url exacta de un archivo, prevenir la sesión (si está conectado o no), y 
// prevenir el rol (en caso de estar viendo contenido que no tiene permitido)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Elige el nivel de seguridad para cada vista: "tutor" o "admin".
// En caso de algo que interese a ambos roles sería "cualquiera"
function validarAcceso($rolPermitido) {
    // Inicializamos variables de control
    $titulo = "";
    $mensaje = "";
    $hayError = false;

    // 1. Verificar si la sesión está iniciada
    if (!isset($_SESSION['usuario'])) {
        $titulo = "Sesión no iniciada";
        $mensaje = "Para acceder a este apartado de la aplicación, es necesario identificarse primero.";
        $hayError = true;
    } 
    // 2. Verificar si el rol coincide (solo si no hubo error previo)
    else if ($rolPermitido !== 'cualquiera' && (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rolPermitido)) {
        $titulo = "Acceso Restringido";
        $mensaje = "Tu cuenta no tiene los permisos de <b>" . ucfirst($rolPermitido) . "</b> necesarios para ver esta sección.";
        $hayError = true;
    }

    // Fuera de la condición: Si se detectó algún problema, disparamos el error
    if ($hayError) {
        mostrarError($titulo, $mensaje);
    }
}

// Esta es la versión "Universal". Funciona tanto en "localhost/PROYECTO/..." como en "mi-dominio.es/..." 

function mostrarError($titulo, $mensaje) {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];

    // --- MAGIA PARA LA RUTA DINÁMICA ---
    // 1. Obtenemos la ruta del script que se está ejecutando (ej: /PROYECTO/index.php)
    // 2. Quitamos el nombre del archivo para quedarnos solo con la carpeta
    // 3. Nos aseguramos de que siempre apunte a la carpeta raíz
    
    $scriptName = $_SERVER['SCRIPT_NAME']; // Devuelve la ruta desde la raíz del servidor
    $directorioRaiz = str_replace('\\', '/', dirname($scriptName));
    
    // Si estamos dentro de subcarpetas (como Vista/Admin), limpiamos hasta la raíz
    // Buscamos la posición de "Vista" o "Controlador" y cortamos
    $pos = strpos($directorioRaiz, '/Vista');
    if ($pos !== false) {
        $directorioRaiz = substr($directorioRaiz, 0, $pos);
    }
    
    // Limpiamos barras finales y construimos la URL
    $directorioRaiz = rtrim($directorioRaiz, '/');
    $urlInicio = $protocolo . "://" . $host . $directorioRaiz . "/index.php"; 

    // Elegimos el icono: Si el título contiene "Sesión", usamos un candado; si no, un aviso.
    $iconPath = strpos($titulo, 'Sesión') !== false 
    ? 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' // Candado
    : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'; // Triángulo

    die('
        <script src="https://cdn.tailwindcss.com"></script>
        <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4 font-sans">
            <div class="max-w-md w-full bg-white border-t-4 border-orange-500 rounded-lg shadow-2xl p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . $iconPath . '" />
                </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-800 mb-2">' . $titulo . '</h2>
                <p class="text-gray-600 mb-8">' . $mensaje . '</p>
                <a href="' . $urlInicio . '" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md">
                    Volver al Inicio
                </a>
            </div>
        </div>
    ');
}

?>