<?php

/**
 * Errores/404.php — Página de error "Ruta no encontrada"
 *
 * Se muestra cuando index.php no encuentra el controlador o la ruta solicitada.
 * Calcula la ruta base desde SCRIPT_NAME en lugar de __DIR__ para que los
 * enlaces a CSS/JS no se rompan cuando el error salta desde URLs profundas.
 */

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$ruta_proyecto_web = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
// Si el navegador terminó dentro de /Errores, limpiamos ese tramo final para quedarnos en la raíz
$ruta_proyecto_web = preg_replace('/\/Errores$/i', '', $ruta_proyecto_web);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 — Página no encontrada</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <script>if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}</script>
    
    <link rel="stylesheet" href="<?= $ruta_proyecto_web ?>/Public/css/dark-mode.css">
</head>
<body class="min-h-svh bg-slate-50 flex items-center justify-center p-6 antialiased font-sans">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-10 text-center">
            
            <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h1 class="text-6xl font-extrabold text-slate-900 tracking-tight mb-2">404</h1>
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tight mb-2">Ruta no encontrada</h2>
            
            <p class="text-slate-500 mt-3 text-sm font-medium mb-8">
                Lo sentimos, no hemos podido encontrar la página que buscas. Es posible que haya sido movida, eliminada o que la URL sea incorrecta.
            </p>

            <a href="<?= $ruta_proyecto_web ?>/index.php" class="block w-full bg-[#1a1f2e] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 active:scale-[0.98] transition-all shadow-lg shadow-slate-200 cursor-pointer">
                Volver al inicio
            </a>
        </div>

        <p class="text-center mt-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            &copy; <?= date('Y') ?> — Instituto FP / Gestión FFE
        </p>
    </div>

    <script src="<?= $ruta_proyecto_web ?>/Public/js/dark-mode.js"></script>
</body>
</html>