<?php

/**
 * Vista/Admin/Dashboard_Admin.php — Shell del panel de administración
 *
 * Marco HTML para el rol admin. Incluye la cabecera fija (Dashboard_Header.php)
 * y a continuación la sub-vista activa, cuya ruta llega en $subVista desde el
 * controlador (Controlador_Admin.php).
 *
 * $subVista puede ser:
 *   - Admin/Components/Dashboard_Sections.php (página de inicio con los 4 módulos)
 *   - Admin/Sections/Tabla_Tutores.php
 *   - Admin/Sections/Tabla_Convenios.php
 *   - Admin/Sections/Tabla_Convenios_Pendientes.php
 *   - Admin/Sections/Listado_Alumnos.php
 *
 * El include dinámico usa la ruta prefijada por "Vista/" para que funcione
 * independientemente del subdirectorio desde el que se ejecuta index.php.
 *
 * MVC: Vista shell del admin. No contiene lógica de negocio.
 */

require_once __DIR__ . '/../../Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración — Ciudad Escolar</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Anti-parpadeo: aplica tema antes de pintar -->
    <script>if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}</script>
    <link rel="stylesheet" href="Public/css/dark-mode.css">
    <style>
        .help-trigger { position: relative; display: inline-flex; }
        .tooltip-box {
            visibility: hidden; opacity: 0; position: absolute; bottom: 150%; left: 50%; transform: translateX(-50%) scale(0.95);
            width: 180px; background-color: #1e293b; color: #fff; text-align: center; padding: 10px; border-radius: 8px;
            font-size: 10px; text-transform: none; z-index: 100; transition: all 0.2s ease;
        }
        .help-trigger:hover .tooltip-box { visibility: visible; opacity: 1; transform: translateX(-50%) scale(1); }
        
        .table-tech th { background-color: #1e293b; color: #fff; text-transform: uppercase; font-size: 11px; padding: 12px 15px; text-align: left; }
        .table-tech td { font-size: 13px; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body class="min-h-svh bg-slate-50 text-slate-900 antialiased font-sans">

    <main class="mx-auto max-w-screen-2xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <?php include 'Components/Dashboard_Header.php'; ?>

            <div class="p-10 min-h-[550px]">
                <?php 
                    // Como index.php es la raíz, buscamos dentro de la carpeta Vista/
                    $rutaFinal = "Vista/" . $subVista;
                    
                    if (isset($subVista) && file_exists($rutaFinal)) {
                        include $rutaFinal;
                    } else {
                        echo "<p class='text-center text-slate-400'>Error: No se pudo cargar la vista [{$rutaFinal}].</p>";
                    }
                ?>
            </div>
        </div> 
    </main>

    <footer class="mt-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
        © <?= date('Y') ?> — Gestión Interna Ciudad Escolar.
    </footer>

    <script>
        const btn = document.getElementById('userMenuBtn');
        const dropdown = document.getElementById('userDropdown');
        btn?.addEventListener('click', (e) => { e.stopPropagation(); dropdown.classList.toggle('hidden'); });
        document.addEventListener('click', () => { dropdown?.classList.add('hidden'); });
    </script>
    <script src="Public/js/validacion.js"></script>
    <script src="Public/js/dark-mode.js"></script>
</body>
</html>