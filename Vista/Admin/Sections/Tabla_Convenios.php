<?php 

// Vista/Admin/Sections/Tabla_Convenios.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 px-2">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Convenios de Empresa</h2>
        <p class="text-slate-500 text-[11px] uppercase font-bold tracking-[0.2em] mt-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Empresas con convenio activo: <?= count($convenios) ?>
        </p>
    </div>
    <div class="flex items-center gap-4">
        <form action="index.php" method="POST">
            <input type="hidden" name="accion" value="mostrarPanel">
            <button type="submit" class="group flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-blue-600 transition-all cursor-pointer">
                <span class="transition-transform group-hover:-translate-x-1">←</span> Volver al inicio
            </button>
        </form>
        <button onclick="document.getElementById('modalImportarConvenios').style.display='flex'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-all shadow-md cursor-pointer uppercase tracking-wide">
            📥 Importar Convenios
        </button>
    </div>
</div>

<form method="POST" action="index.php" class="flex flex-col lg:flex-row gap-4 mb-8 p-4 bg-slate-50/50 rounded-2xl border border-slate-100 items-center">
    <input type="hidden" name="accion" value="mostrarConvenios">
    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" name="busqueda" value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>" placeholder="BUSCAR POR NOMBRE O CIF..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all uppercase">
    </div>
    <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold text-[10px] hover:bg-blue-600 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
        BUSCAR
    </button>
    <button type="button" onclick="this.closest('form').querySelector('[name=busqueda]').value=''; this.closest('form').submit();"
        class="flex items-center gap-1.5 px-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold text-slate-500 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all cursor-pointer uppercase tracking-wide whitespace-nowrap">
        Mostrar todos
    </button>
</form>

<!-- Barra superior: contador + config paginación -->
<div class="flex items-center justify-between mb-2">
    <span id="conv-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
    <button type="button" onclick="abrirModalPag('conv')" title="Configurar filas por página"
        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all cursor-pointer uppercase tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span id="conv-pag-label">10/pág</span>
    </button>
</div>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden text-slate-700">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Empresa / CIF</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Ubicación</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Contacto</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Representante</th>
                <th class="py-5 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody id="conv-tbody" class="divide-y divide-slate-100">
            <?php if (empty($convenios)): ?>
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400 italic text-xs uppercase tracking-widest">No hay convenios</td>
                </tr>
            <?php else: ?>
                <?php foreach ($convenios as $fila): ?>
                <tr class="conv-fila hover:bg-slate-50/40 transition-all group">
                    <td class="py-5 px-6">
                        <div class="text-sm font-bold text-slate-800 uppercase"><?= htmlspecialchars($fila['nombre_empresa']) ?></div>
                        <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($fila['cif']) ?></div>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-xs text-slate-600 font-bold uppercase"><?= htmlspecialchars($fila['municipio']) ?></div>
                        <div class="text-[10px] text-slate-400"><?= htmlspecialchars($fila['direccion']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-xs text-slate-500">
                        <div class="font-bold"><?= htmlspecialchars($fila['mail']) ?></div>
                        <div class="text-[9px] text-slate-400"><?= htmlspecialchars($fila['telefono'] ?? '') ?></div>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-[11px] font-bold text-slate-700 uppercase"><?= htmlspecialchars($fila['nombre_representante']) ?></div>
                        <div class="text-[9px] text-slate-400 uppercase tracking-tighter"><?= htmlspecialchars($fila['cargo']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick='abrirEditarConvenio(<?= json_encode($fila) ?>)' class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button onclick='abrirModalEliminarConvenio(<?= json_encode($fila) ?>)' class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="conv-paginacion" class="hidden flex items-center justify-center mt-3 gap-1.5">
    <button id="conv-prev" onclick="convCambiarPagina(convPaginaActual - 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Anterior
    </button>
    <div id="conv-paginas" class="flex items-center gap-1.5"></div>
    <button id="conv-next" onclick="convCambiarPagina(convPaginaActual + 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        Siguiente
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
</div>

<script>
// ─── PAGINACIÓN: CONVENIOS DE EMPRESA ────────────────────────────────────────
let convPorPagina = parseInt(localStorage.getItem('pag_conv_porPagina')) || 10;
let convPaginaActual = 1;

function convInicializar() {
    const filas = Array.from(document.querySelectorAll('#conv-tbody .conv-fila'));
    const total = filas.length;
    const label = document.getElementById('conv-pag-label');
    if (label) label.textContent = convPorPagina + '/pág';
    const pag = document.getElementById('conv-paginacion');
    const contador = document.getElementById('conv-contador');
    if (total <= convPorPagina) {
        pag.classList.add('hidden');
        filas.forEach(f => f.style.display = '');
        if (contador) contador.textContent = total > 0 ? `${total} convenio${total !== 1 ? 's' : ''}` : '';
        return;
    }
    pag.classList.remove('hidden');
    convRenderizar();
}

function convCambiarPagina(nuevaPagina) {
    const filas = document.querySelectorAll('#conv-tbody .conv-fila');
    const totalPaginas = Math.ceil(filas.length / convPorPagina);
    if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
    convPaginaActual = nuevaPagina;
    convRenderizar();
}

function convRenderizar() {
    const filas = Array.from(document.querySelectorAll('#conv-tbody .conv-fila'));
    const total = filas.length;
    const totalPaginas = Math.ceil(total / convPorPagina);
    const inicio = (convPaginaActual - 1) * convPorPagina;
    const fin    = Math.min(inicio + convPorPagina, total);

    filas.forEach((fila, i) => {
        fila.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const contador = document.getElementById('conv-contador');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('conv-prev').disabled = convPaginaActual === 1;
    document.getElementById('conv-next').disabled = convPaginaActual === totalPaginas;

    const contenedor = document.getElementById('conv-paginas');
    contenedor.innerHTML = '';
    const pagsMostrar = new Set([1, totalPaginas, convPaginaActual, convPaginaActual - 1, convPaginaActual + 1]
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
        btn.onclick = () => convCambiarPagina(p);
        btn.className = p === convPaginaActual
            ? 'w-8 h-8 rounded-lg bg-blue-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', convInicializar);

// ─── Modal configurar paginación ─────────────────────────────────────────────
window._pagCallbacks = window._pagCallbacks || {};
window._pagCallbacks['conv'] = function(n) { convPorPagina = n; convPaginaActual = 1; convInicializar(); };

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

<!-- ─── Modal Configurar Paginación: Convenios de Empresa ─────────────────── -->
<div id="modal-pag-conv" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('conv')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('conv')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('conv', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('conv', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('conv', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('conv', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('conv', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('conv', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-conv" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-blue-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('conv')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('conv')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('conv')" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>

<?php include 'Vista/Admin/Components/Modales_TC.php'; ?>