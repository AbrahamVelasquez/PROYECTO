<?php

/**
 * Errores/403.php — Página de error "Acceso denegado"
 *
 * Se muestra cuando el usuario tiene sesión pero no tiene permiso para la sección
 * solicitada (rol incorrecto). Puede ser incluida por Control_Accesos (que ya
 * calcula $directorioRaiz) o servida directamente por Apache; en ese caso
 * calcula la ruta base ella misma.
 */

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Si Control_Accesos ya calculó $directorioRaiz, lo respetamos.
// Si se sirve directo por Apache, lo calculamos aquí.
if (!isset($directorioRaiz)) {
    $directorioRaiz = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $directorioRaiz = preg_replace('/\/Vista(\/.*)?$/i', '', $directorioRaiz);
    $directorioRaiz = preg_replace('/\/Errores$/i', '', $directorioRaiz);
}

$esSesion = (isset($titulo) && strpos($titulo, 'Sesión') !== false);
$iconPath = $esSesion 
    ? 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' 
    : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';

$colorEstiloIcono = $esSesion ? 'bg-orange-50 text-orange-500' : 'bg-red-50 text-red-500';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Acceso Restringido') ?> — Gestión FFE</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script>if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}</script>
    
    <link rel="stylesheet" href="<?= $directorioRaiz ?>/Public/css/dark-mode.css">
</head>
<body class="min-h-svh bg-slate-50 flex items-center justify-center p-6 antialiased font-sans">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-10 text-center">
            
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-10v4m-6.364 7.364A9 9 0 1 0 18.364 5.636 9 9 0 0 0 5.636 18.364z" />
                </svg>
            </div>

            <h1 class="text-6xl font-extrabold text-slate-900 tracking-tight mb-2">403</h1>
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tight mb-2"><?= htmlspecialchars($titulo ?? 'Acceso Restringido') ?></h2>
            
            <p class="text-slate-500 mt-3 text-sm font-medium mb-8">
                <?= $mensaje ?? 'No tienes permisos para ver este recurso o tu sesión ha caducado.' ?>
            </p>

            <a href="<?= htmlspecialchars($urlInicio ?? $directorioRaiz . '/index.php') ?>" class="block w-full bg-[#1a1f2e] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 active:scale-[0.98] transition-all shadow-lg shadow-slate-200 text-center cursor-pointer">
                Volver al Inicio
            </a>
        </div>

        <p class="text-center mt-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            &copy; <?= date('Y') ?> — Instituto FP / Gestión FFE
        </p>
    </div>

    <script src="<?= $directorioRaiz ?>/Public/js/dark-mode.js"></script>
</body>
</html>