<?php

/**
 * Errores/500.php — Página de error interno del servidor
 *
 * Se muestra cuando una excepción no controlada llega al manejador del Enrutador.
 * Usa la misma lógica de ruta dinámica que AlertaSistema.php para resolver
 * correctamente los enlaces a CSS/JS desde cualquier subdirectorio.
 */

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Misma lógica de ruta que AlertaSistema.php — fiable en cualquier configuración
$ruta_proyecto_web = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$ruta_proyecto_web = preg_replace('/\/Errores$/i', '', $ruta_proyecto_web);
$directorioRaiz = $ruta_proyecto_web;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 500 — Error interno del servidor</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <script>if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}</script>
    
    <link rel="stylesheet" href="<?= $directorioRaiz ?>/Public/css/dark-mode.css">
</head>
<body class="min-h-svh bg-slate-50 flex items-center justify-center p-6 antialiased font-sans">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-10 text-center">
            
            <div class="w-20 h-20 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <h1 class="text-6xl font-extrabold text-slate-900 tracking-tight mb-2">500</h1>
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tight mb-2">Error del Servidor</h2>
            
            <p class="text-slate-500 mt-3 text-sm font-medium mb-8">
                Lo sentimos, algo ha salido mal en nuestros servidores. Estamos experimentando una interrupción técnica temporal. Por favor, intenta recargar la página en unos instantes.
            </p>

            <button onclick="window.location.reload();" class="block w-full bg-[#1a1f2e] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 active:scale-[0.98] transition-all shadow-lg shadow-slate-200 cursor-pointer">
                Recargar página
            </button>
        </div>

        <p class="text-center mt-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            &copy; <?= date('Y') ?> — Instituto FP / Gestión FFE
        </p>
    </div>

    <script src="<?= $directorioRaiz ?>/Public/js/dark-mode.js"></script>
</body>
</html>