<?php 

// Vista/Admin/Sections/Tabla_Convenios_Pendientes.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
<div class="flex items-center justify-between mb-10 px-2">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Convenios Pendientes</h2>
        <p class="text-emerald-600 text-[10px] font-black tracking-[0.2em] mt-1 flex items-center gap-2">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Esperando incorporación al sistema: <?= count($pendientes) ?>
        </p>
    </div>
    
    <form action="index.php" method="POST">
        <input type="hidden" name="accion" value="mostrarPanel">
        <button type="submit" class="flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-emerald-600 transition-all cursor-pointer">
            ← VOLVER AL PANEL
        </button>
    </form>
</div>

<!-- Barra superior: contador + config paginación -->
<div class="flex items-center justify-between mb-2">
    <span id="pend-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
    <button type="button" onclick="abrirModalPag('pend')" title="Configurar filas por página"
        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 transition-all cursor-pointer uppercase tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span id="pend-pag-label">10/pág</span>
    </button>
</div>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="py-4 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Empresa</th>
                <th class="py-4 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha Aprobación</th>
                <th class="py-4 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody id="pend-tbody" class="divide-y divide-slate-100">
            <?php if (empty($pendientes)): ?>
                <tr>
                    <td colspan="3" class="py-20 px-6 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-slate-800 font-black uppercase text-sm tracking-tighter">Todo al día</h3>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mt-1 tracking-widest">No hay convenios pendientes de validación</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pendientes as $p): ?>
                <tr class="pend-fila hover:bg-emerald-50/30 transition-all group">
                    <td class="py-5 px-6">
                        <div class="font-bold text-slate-800 uppercase text-sm group-hover:text-emerald-700 transition-colors">
                            <?= htmlspecialchars($p['nombre_empresa']) ?>
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($p['cif']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-xs text-slate-500 font-medium">
                        <?= date('d/m/Y H:i', strtotime($p['fecha_aprobacion'])) ?>
                    </td>
                    <td class="py-5 px-6">
                        <div class="flex items-center justify-center gap-3">
                            <button onclick='abrirModalRevision(<?= json_encode($p) ?>)' 
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all cursor-pointer" 
                                    title="Revisar y Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            <form id="form-validar-<?= $p['id_convenio_nuevo'] ?>" method="POST" action="index.php">
                                <input type="hidden" name="accion" value="validarConvenio">
                                <input type="hidden" name="id_convenio_nuevo" value="<?= $p['id_convenio_nuevo'] ?>">
                                
                                <button type="button" 
                                        onclick="confirmarValidacionDirecta('<?= $p['id_convenio_nuevo'] ?>', '<?= htmlspecialchars($p['nombre_empresa']) ?>')"
                                        class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100 cursor-pointer flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Validar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="pend-paginacion" class="hidden flex items-center justify-center mt-3 gap-1.5">
    <button id="pend-prev" onclick="pendCambiarPagina(pendPaginaActual - 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Anterior
    </button>
    <div id="pend-paginas" class="flex items-center gap-1.5"></div>
    <button id="pend-next" onclick="pendCambiarPagina(pendPaginaActual + 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        Siguiente
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
</div>

<script>
// ─── PAGINACIÓN: CONVENIOS PENDIENTES ────────────────────────────────────────
let pendPorPagina = parseInt(localStorage.getItem('pag_pend_porPagina')) || 10;
let pendPaginaActual = 1;

function pendInicializar() {
    const filas = Array.from(document.querySelectorAll('#pend-tbody .pend-fila'));
    const total = filas.length;
    const label = document.getElementById('pend-pag-label');
    if (label) label.textContent = pendPorPagina + '/pág';
    const pag = document.getElementById('pend-paginacion');
    const contador = document.getElementById('pend-contador');
    if (total <= pendPorPagina) {
        pag.classList.add('hidden');
        filas.forEach(f => f.style.display = '');
        if (contador) contador.textContent = total > 0 ? `${total} pendiente${total !== 1 ? 's' : ''}` : '';
        return;
    }
    pag.classList.remove('hidden');
    pendRenderizar();
}

function pendCambiarPagina(nuevaPagina) {
    const filas = document.querySelectorAll('#pend-tbody .pend-fila');
    const totalPaginas = Math.ceil(filas.length / pendPorPagina);
    if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
    pendPaginaActual = nuevaPagina;
    pendRenderizar();
}

function pendRenderizar() {
    const filas = Array.from(document.querySelectorAll('#pend-tbody .pend-fila'));
    const total = filas.length;
    const totalPaginas = Math.ceil(total / pendPorPagina);
    const inicio = (pendPaginaActual - 1) * pendPorPagina;
    const fin    = Math.min(inicio + pendPorPagina, total);

    filas.forEach((fila, i) => {
        fila.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const contador = document.getElementById('pend-contador');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('pend-prev').disabled = pendPaginaActual === 1;
    document.getElementById('pend-next').disabled = pendPaginaActual === totalPaginas;

    const contenedor = document.getElementById('pend-paginas');
    contenedor.innerHTML = '';
    const pagsMostrar = new Set([1, totalPaginas, pendPaginaActual, pendPaginaActual - 1, pendPaginaActual + 1]
        .filter(p => p >= 1 && p <= totalPaginas));
    [...pagsMostrar].sort((a, b) => a - b).forEach((p, idx, arr) => {
        const prev = arr[idx - 1];
        if (prev !== undefined && p - prev > 1) {
            const sep = document.createElement('span');
            sep.className = 'text-slate-300 text-xs font-bold px-1';
            sep.textContent = '···';
            contenedor.appendChild(sep);
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = p;
        btn.onclick = () => pendCambiarPagina(p);
        btn.className = p === pendPaginaActual
            ? 'w-8 h-8 rounded-lg bg-emerald-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', pendInicializar);

// ─── Modal configurar paginación ─────────────────────────────────────────────
window._pagCallbacks = window._pagCallbacks || {};
window._pagCallbacks['pend'] = function(n) { pendPorPagina = n; pendPaginaActual = 1; pendInicializar(); };

function abrirModalPag(prefix) {
    const val = parseInt(localStorage.getItem('pag_' + prefix + '_porPagina')) || 10;
    document.getElementById('input-pag-' + prefix).value = val;
    document.getElementById('modal-pag-' + prefix).style.display = 'flex';
}
function cerrarModalPag(prefix) {
    document.getElementById('modal-pag-' + prefix).style.display = 'none';
}
function setPagPreset(prefix, n) {
    document.getElementById('input-pag-' + prefix).value = n;
}
function aplicarPag(prefix) {
    const val = parseInt(document.getElementById('input-pag-' + prefix).value);
    if (!val || val < 1) return;
    localStorage.setItem('pag_' + prefix + '_porPagina', val);
    const label = document.getElementById(prefix + '-pag-label');
    if (label) label.textContent = val + '/pág';
    cerrarModalPag(prefix);
    if (window._pagCallbacks[prefix]) window._pagCallbacks[prefix](val);
}
// ─────────────────────────────────────────────────────────────────────────────
</script>

<!-- ─── Modal Configurar Paginación: Convenios Pendientes ─────────────────── -->
<div id="modal-pag-pend" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('pend')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('pend')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('pend', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('pend', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('pend', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('pend', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('pend', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('pend', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-pend" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-emerald-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('pend')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('pend')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('pend')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>

<?php include 'Vista/Admin/Components/Modales_TCP.php'; ?>