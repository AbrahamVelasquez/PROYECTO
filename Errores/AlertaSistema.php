<?php

/**
 * Errores/AlertaSistema.php — Página genérica de error del sistema (incluida por Enrutador)
 *
 * Plantilla HTML reutilizable para todos los errores controlados: 400, 401, 403, 404, 500.
 * El Enrutador la include directamente después de asignar las variables globales
 * $errorCodigo, $errorTitulo, $errorMensaje, $urlBoton y $textoBoton.
 * Si alguna falta (p.ej. acceso directo a la URL), se usan los valores por defecto de 500.
 *
 * La ruta base se calcula dinámicamente eliminando el tramo "/Errores" de SCRIPT_NAME
 * para que los enlaces a CSS/JS funcionen sin importar el subdirectorio de despliegue.
 *
 * Si hay sesión activa, muestra un enlace secundario para cerrar sesión.
 */

if (session_status() === PHP_SESSION_NONE) { session_start(); }

/**
 * Cálculo dinámico de la ruta base
 */
$ruta_proyecto_web = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$ruta_proyecto_web = preg_replace('/\/Errores$/i', '', $ruta_proyecto_web);

/**
 * Mapeo de variables globales del Enrutador
 * Si no hacemos esto, el HTML dará un "Undefined variable" al no encontrar el código o el título.
 */
$errorCodigo = $GLOBALS['errorCodigo'] ?? '500';
$errorTitulo = $GLOBALS['errorTitulo'] ?? 'Error del Sistema';
$errorMensaje = $GLOBALS['errorMensaje'] ?? 'Ha ocurrido un problema al procesar la solicitud en el servidor.';
$urlBoton = $GLOBALS['urlBoton'] ?? 'index.php';
$textoBoton = $GLOBALS['textoBoton'] ?? 'Volver al panel principal';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($errorTitulo ?? 'Error del Sistema') ?> — Gestión FFE</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <script>if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}</script>
    
    <link rel="stylesheet" href="<?= $ruta_proyecto_web ?>/Public/css/dark-mode.css">
</head>
<body class="min-h-svh bg-slate-50 text-slate-900 antialiased font-sans flex flex-col justify-between">

    <main class="mx-auto max-w-screen-2xl w-full px-4 py-10 sm:px-6 lg:px-8 flex-1 flex items-center justify-center">
        
        <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl shadow-sm p-10 text-center overflow-hidden">
            
            <h1 class="text-6xl font-black text-slate-300 tracking-tighter select-none uppercase mb-4">
                <?= htmlspecialchars($errorCodigo ?? '500') ?>
            </h1>

            <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight mb-2">
                <?= htmlspecialchars($errorTitulo ?? 'Internal Server Error') ?>
            </h2>
            
            <p class="text-slate-600 mt-3 text-sm font-medium mb-8 leading-relaxed break-words">
                <?= htmlspecialchars($errorMensaje ?? 'Ha ocurrido un problema al procesar la solicitud en el servidor.') ?>
            </p>

            <a href="<?= htmlspecialchars($urlBoton ?? 'index.php') ?>" 
               class="block w-full bg-orange-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 active:scale-[0.98] transition-all shadow-md cursor-pointer mb-4">
                <?= htmlspecialchars($textoBoton ?? 'Volver al panel principal') ?>
            </a>

            <?php if (isset($_SESSION['usuario']) && ($urlBoton !== "index.php?LogOut")): ?>
                <a href="<?= htmlspecialchars($ruta_proyecto_web) ?>/index.php?LogOut" 
                   class="inline-block mt-2 text-xs font-bold text-slate-400 hover:text-orange-500 transition-colors uppercase tracking-wider cursor-pointer underline decoration-1 underline-offset-4">
                    O cerrar sesión actual
                </a>
            <?php endif; ?>
        </div>

    </main>

    <footer class="pb-10 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
        © <?= date('Y') ?> — Gestión FFE interna.
    </footer>

    <script src="<?= $ruta_proyecto_web ?>/Public/js/dark-mode.js"></script>
</body>
</html>