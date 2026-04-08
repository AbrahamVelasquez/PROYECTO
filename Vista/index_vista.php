<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión FFE — Instituto FP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <?php include 'Vista/Components/Header.php'; ?>

            <div class="border-t border-slate-100 p-10 bg-white min-h-[500px]">
                
                <div data-tab="1" class="<?= $pestanaActiva == 1 ? 'active' : '' ?>">
                    <?php include 'Vista/Steps/Convenios.php'; ?>
                </div>

                <div data-tab="2" class="<?= $pestanaActiva == 2 ? 'active' : '' ?>">
                    <?php include 'Vista/Steps/Alumnos.php'; ?>
                </div>

                <div data-tab="3" class="<?= $pestanaActiva == 3 ? 'active' : '' ?>">
                    <?php include 'Vista/Steps/Plan_Formativo.php'; ?>
                </div>
                
                <div data-tab="4" class="<?= $pestanaActiva == 4 ? 'active' : '' ?>">
                    <?php include 'Vista/Steps/Seguimiento.php'; ?>
                </div>

            </div>
        </div> 

        <!-- CONVENIOS EN PROCESO (visual) -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[10px] font-black text-amber-600 uppercase tracking-[0.2em] flex items-center gap-2">
                    ⏳ Convenios en Proceso
                </h3>
                <span class="text-[9px] font-black text-amber-500 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full uppercase tracking-widest">
                    Pendientes de confirmar
                </span>
            </div>
            <div class="overflow-hidden rounded-2xl border-2 border-amber-100 bg-white">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-amber-500 text-white">
                        <tr>
                            <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest">Empresa</th>
                            <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest">Estado del enlace</th>
                            <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-50">

                        <!-- Fila de ejemplo 1 -->
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 uppercase text-sm">Innovatech S.L.</div>
                                <div class="text-xs text-slate-400 font-bold">Leganés</div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-[9px] font-black uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                                    Formulario enviado · esperando respuesta
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <button onclick="aprobarConvenio(this, 'Innovatech S.L.')"
                                        class="group flex items-center gap-2 mx-auto bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white px-4 py-2 rounded-lg transition-all border border-emerald-100 cursor-pointer">
                                    <span class="text-[10px] font-black uppercase">Aprobar</span>
                                    <span class="text-xs">✓</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Fila de ejemplo 2 -->
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 uppercase text-sm">DataSphere Corp.</div>
                                <div class="text-xs text-slate-400 font-bold">Madrid</div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-[9px] font-black uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                                    Formulario enviado · esperando respuesta
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <button onclick="aprobarConvenio(this, 'DataSphere Corp.')"
                                        class="group flex items-center gap-2 mx-auto bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white px-4 py-2 rounded-lg transition-all border border-emerald-100 cursor-pointer">
                                    <span class="text-[10px] font-black uppercase">Aprobar</span>
                                    <span class="text-xs">✓</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Fila de ejemplo 3 — ya rellenado -->
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 uppercase text-sm">CloudBase Systems</div>
                                <div class="text-xs text-slate-400 font-bold">Alcorcón</div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-[9px] font-black uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Formulario completado · listo para aprobar
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <button onclick="aprobarConvenio(this, 'CloudBase Systems')"
                                        class="group flex items-center gap-2 mx-auto bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white px-4 py-2 rounded-lg transition-all border border-emerald-100 cursor-pointer">
                                    <span class="text-[10px] font-black uppercase">Aprobar</span>
                                    <span class="text-xs">✓</span>
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="mt-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
        © <?= date('Y') ?> — Gestión FFE interna.
    </footer>

    <script src="Public/js/script_tabs.js">
        
        function aprobarConvenio(btn, nombre) {
            const fila = btn.closest('tr');

            // Animación de aprobado
            fila.style.transition = 'opacity 0.4s ease';
            fila.style.opacity = '0.4';

            setTimeout(() => {
                fila.remove();

                // Añadir a "Mi Listado Personal" visualmente
                const tbody = document.querySelector('.divide-y.divide-orange-50');
                const nuevaFila = document.createElement('tr');
                nuevaFila.className = 'hover:bg-orange-50/50 transition-colors';
                nuevaFila.innerHTML = `
                    <td class="px-6 py-5">
                        <div class="font-bold text-slate-900 uppercase text-sm">${nombre}</div>
                        <div class="text-xs text-slate-400 font-bold">Añadido desde proceso</div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-[9px] font-black uppercase">
                            ✓ Añadido
                        </span>
                    </td>
                `;
                // Quita el mensaje "listado vacío" si existe
                const vacia = tbody.querySelector('td[colspan]');
                if (vacia) vacia.closest('tr').remove();

                tbody.appendChild(nuevaFila);
            }, 400);
        }


    </script>

</body>
</html>