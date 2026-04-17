<?php

////////////////////////////////////////////////
// Este fichero, por ahora, no se está usando //
////////////////////////////////////////////////

// Controlador/Seguridad.php

// Su función será agregar una capa de seguridad en caso de conocer la url
// exacta de un archivo, prevenir la sesión (si está conectado o no), y 
// prevenir el rol (en caso de estar viendo contenido que no tiene permitido)

function validarAcceso($rolPermitido) {
    // 1. Verificar si la sesión está iniciada
    if (!isset($_SESSION['usuario'])) {
        mostrarErrorNaranja("Sesión no iniciada", "Para acceder a este apartado de la aplicación, es necesario identificarse primero.");
    }

    // 2. Verificar si el rol coincide (si no es 'cualquiera')
    if ($rolPermitido !== 'cualquiera' && (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rolPermitido)) {
        mostrarErrorNaranja("Acceso Restringido", "Tu cuenta no tiene los permisos de <b>" . ucfirst($rolPermitido) . "</b> necesarios para ver esta sección.");
    }
}

function mostrarErrorNaranja($titulo, $mensaje) {
    // El die() detiene la carga de la vista sospechosa inmediatamente
    die('
        <script src="https://cdn.tailwindcss.com"></script>
        <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4 font-sans">
            <div class="max-w-md w-full bg-white border-t-4 border-orange-500 rounded-lg shadow-2xl p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-8V7m0 0a2 2 0 100-4 2 2 0 000 4zm-3.34 12.16l-4.58-4.58a2 2 0 010-2.83l4.58-4.58a2 2 0 012.83 0l4.58 4.58a2 2 0 010 2.83l-4.58 4.58a2 2 0 01-2.83 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-800 mb-2">' . $titulo . '</h2>
                <p class="text-gray-600 mb-8">' . $mensaje . '</p>
                <a href="../index.php" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md">
                    Volver al Inicio
                </a>
            </div>
        </div>
    ');
}

// Se usa de la siguiente manera, colocando esto arriba de la vista:

session_start();

require_once __DIR__ . '/../../Controlador/Seguridad.php'; // Varía en base al fichero que estés

// Elige el nivel de seguridad para ESTA página: tutor/admin
validarAcceso('tutor'); 

?>