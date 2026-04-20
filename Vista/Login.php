<?php

// Vista/Login.php

// 1. Definimos si el acceso es legal (si viene del index tendrá ROOT_PATH definido)
if (!defined('ROOT_PATH')) {
    // Si alguien entra directo a la URL, lo mandamos al index real
    // Usamos una ruta que suba niveles para encontrar el index
    header("Location: ../index.php");
    exit();
}

// 2. Si la sesión ya existe, lo mandamos al index con ruta absoluta
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_SESSION['usuario'])) {
    // Calculamos la URL base para el index.php
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $urlIndex = $protocolo . "://" . $_SERVER['HTTP_HOST'] . "/PROYECTO/index.php";
    
    header("Location: $urlIndex");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Gestión FFE</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-svh bg-slate-50 flex items-center justify-center p-6 antialiased font-sans">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Bienvenido</h1>
            <p class="text-slate-500 mt-3 text-sm font-medium">Introduce tus credenciales para acceder al panel FFE.</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-10">
            <form action="index.php" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">
                        Usuario
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <input type="text" name="usuario" id="usuario" required 
                            class="w-full pl-12 pr-4 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold outline-none focus:ring-4 focus:ring-orange-50 focus:border-orange-500 focus:bg-white transition-all placeholder:text-slate-300"
                            placeholder="Escribe tu usuario...">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">
                        Contraseña
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" id="passInput" name="contrasena" required 
                            class="w-full pl-12 pr-12 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-sm font-bold outline-none focus:ring-4 focus:ring-orange-50 focus:border-orange-500 focus:bg-white transition-all placeholder:text-slate-300"
                            placeholder="••••••••">
                        
                        <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-600 transition-colors cursor-pointer">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="hidden w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="btnLogIn" value="Inicia sesion" class="w-full bg-[#1a1f2e] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 active:scale-[0.98] transition-all shadow-lg shadow-slate-200 cursor-pointer">
                    Iniciar sesión
                </button>

                <?php if (isset($_GET['mensaje'])): ?>
                <div class="mt-4 p-4 rounded-xl bg-red-50 border border-red-100 flex items-center gap-3">
                    <span class="text-red-500 text-xs">⚠️</span>
                    <p class="text-[10px] font-bold text-red-600 uppercase tracking-tight">
                        <?php echo htmlspecialchars($_GET['mensaje']); ?>
                    </p>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <p class="text-center mt-8 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            &copy; <?= date('Y') ?> — Instituto de Formación Profesional
        </p>
    </div>

    <script src="Public/js/script_password.js"></script>
</body>
</html>