<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración — Ciudad Escolar</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .help-trigger { position: relative; display: inline-flex; }
        .tooltip-box {
            visibility: hidden; opacity: 0; position: absolute; bottom: 150%; left: 50%; transform: translateX(-50%) scale(0.95);
            width: 180px; background-color: #1e293b; color: #fff; text-align: center; padding: 10px; border-radius: 8px;
            font-size: 10px; text-transform: none; z-index: 100; transition: all 0.2s ease;
        }
        .help-trigger:hover .tooltip-box { visibility: visible; opacity: 1; transform: translateX(-50%) scale(1); }
        
        /* Estilos específicos para la tabla técnica */
        .table-tech th { background-color: #1e293b; color: #fff; text-transform: uppercase; font-size: 11px; padding: 12px 15px; text-align: left; }
        .table-tech td { font-size: 13px; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body class="min-h-svh bg-slate-50 text-slate-900 antialiased font-sans">

    <main class="mx-auto max-w-screen-2xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <div class="pt-6 px-8 pb-6 border-b border-slate-100"> 
                <div class="flex justify-between items-start">
                    <div class="flex-1 min-w-0"> 
                        <h1 class="text-4xl font-extrabold tracking-tight">
                            Panel de <span class="text-orange-600">Administración</span>
                        </h1>
                        <p class="mt-1 text-slate-500 text-sm max-w-2xl truncate">
                            Interfaz de control central de Ciudad Escolar. Gestión de personal docente y convenios.
                        </p>
                    </div>

                    <div class="relative ml-4 flex-shrink-0">
                        <button id="userMenuBtn" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2 pr-4 hover:bg-slate-50 transition-all shadow-sm cursor-pointer">
                            <div class="h-8 w-8 rounded-lg bg-orange-600 flex items-center justify-center text-white font-bold text-xs shadow-inner">A</div>
                            <span class="text-xs font-bold text-slate-700">Administrador</span>
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nivel de Acceso</p>
                                <p class="text-[11px] font-bold text-slate-800 uppercase italic">Administración de CE</p>
                            </div>
                            <div class="p-1">
                                <form action="index.php" method="POST">
                                    <button type="submit" name="btnLogOut" class="w-full text-left px-4 py-3 text-[10px] font-black text-red-500 hover:bg-red-50 rounded-lg transition-colors uppercase flex items-center justify-between">
                                        <span>Cerrar Sesión</span> <span>✕</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-10 min-h-[550px]">
                <?php 
                    // Si el controlador nos pasa una $subVista, la incluimos
                    if (isset($subVista) && file_exists("Vista/" . $subVista)) {
                        include "Vista/" . $subVista;
                    } else {
                        echo "<p class='text-center text-slate-400'>Error: No se pudo cargar el contenido.</p>";
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
</body>
</html>