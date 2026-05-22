<?php

/**
 * Vista/Tutores/Dashboard_Tutores.php — Shell del dashboard del tutor (wizard de 4 pasos)
 *
 * Punto de entrada visual para el rol tutor. Monta la estructura completa de la página:
 * cabecera con indicadores de pasos (Header.php) y el contenedor con los 4 tabs de contenido.
 * Cada tab incluye el step correspondiente; la lógica de qué tab mostrar la gestiona
 * script_tabs.js leyendo el parámetro ?tab= de la URL.
 *
 * Si $_SESSION['error_convenio'] existe (set por Controlador_Convenios_Tutores cuando un
 * convenio ya está en uso), inyecta un <script> inline que abre el modal de aviso
 * antes de que el DOM esté completamente listo, limpiando la sesión tras mostrarlo.
 *
 * Variables recibidas del controlador (Controlador_Tutores.php):
 *   $pestanaActiva — número del tab que debe estar activo al cargar (1–4)
 *   + todas las variables de datos que usan los steps incluidos
 *
 * MVC: Vista shell. No contiene lógica de negocio; delega toda la presentación de
 * datos a los steps incluidos y la interactividad a los scripts JS externos.
 */

require_once __DIR__ . '/../../Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión FFE — Instituto FP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Anti-parpadeo: aplica tema antes de pintar -->
    <script>if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}</script>
    <link rel="stylesheet" href="Public/css/dark-mode.css">
    <style>
        /* Estilos base para pestañas */
        [data-tab] { display: none; }
        [data-tab].active { display: block; }
        
        .step-label { user-select: none; cursor: pointer; }
        .step-active-circle {
            background-color: #ea580c !important;
            color: white !important;
            box-shadow: 0 0 0 4px white, 0 0 0 7px #fed7aa;
        }
        .step-active-text { color: #111827 !important; font-weight: 800 !important; }

        .table-tech th { background-color: #000; color: #fff; text-transform: uppercase; font-size: 10px; padding: 12px 6px; border-right: 1px solid #334155; text-align: center; }
        .table-tech td { font-size: 10px; font-weight: 700; padding: 12px 8px; border-right: 1px solid #f1f5f9; text-transform: uppercase; }
        .border-section { border-right: 2px solid #e2e8f0 !important; }

        .help-trigger { position: relative; display: inline-flex; }
        .tooltip-box {
            visibility: hidden; opacity: 0; position: absolute; bottom: 150%; left: 50%; transform: translateX(-50%) scale(0.95);
            width: 180px; background-color: #1e293b; color: #fff; text-align: center; padding: 10px; border-radius: 8px;
            font-size: 10px; text-transform: none; z-index: 100; transition: all 0.2s ease;
        }
        .help-trigger:hover .tooltip-box { visibility: visible; opacity: 1; transform: translateX(-50%) scale(1); }
    </style>
</head>
<body class="min-h-svh bg-slate-50 text-slate-900 antialiased font-sans">
    
    <?php if (isset($_SESSION['error_convenio'])): ?>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('modalConvenioMensaje').textContent = "<?= $_SESSION['error_convenio'] ?>";
            document.getElementById('modalConvenioEnUso').style.display = 'flex';
        });
    </script>
    <?php unset($_SESSION['error_convenio']); ?>
    <?php endif; ?>

    <main class="mx-auto max-w-screen-2xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <?php include 'Vista/Tutores/Components/Header.php'; ?>

            <div class="border-t border-slate-100 p-10 bg-white min-h-[500px]">
                
                <div data-tab="1" class="<?= $pestanaActiva == 1 ? 'active' : '' ?>">
                    <?php include 'Vista/Tutores/Steps/Convenios.php'; ?>
                </div>

                <div data-tab="2" class="<?= $pestanaActiva == 2 ? 'active' : '' ?>">
                    <?php include 'Vista/Tutores/Steps/Alumnos.php'; ?>
                </div>

                <div data-tab="3" class="<?= $pestanaActiva == 3 ? 'active' : '' ?>">
                    <?php include 'Vista/Tutores/Steps/Plan_Formativo.php'; ?>
                </div>
                
                <div data-tab="4" class="<?= $pestanaActiva == 4 ? 'active' : '' ?>">
                    <?php include 'Vista/Tutores/Steps/Seguimiento.php'; ?>
                </div>

            </div>
        </div> 
    </main>

    <footer class="mt-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
        © <?= date('Y') ?> — Gestión FFE interna.
    </footer>

    <script src="Public/js/validacion.js"></script>
    <script src="Public/js/script_tabs.js"></script>
    <script src="Public/js/dark-mode.js"></script>

</body>
</html>