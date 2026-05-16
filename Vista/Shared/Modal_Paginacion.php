<?php
/**
 * Template del modal de configuración de paginación.
 * Variables requeridas antes del include:
 *   $pag_prefix  — prefijo de la tabla, p.ej. 'conv', 'tut', 'ladm', 'alum'
 *   $pag_color   — color Tailwind, p.ej. 'blue', 'orange', 'violet'
 */
$c = $pag_color;
?>
<div id="modal-pag-<?= $pag_prefix ?>" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('<?= $pag_prefix ?>')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">

        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('<?= $pag_prefix ?>')"
                class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>

        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ([5, 10, 15, 20, 25, 50] as $n): ?>
            <button type="button" onclick="setPagPreset('<?= $pag_prefix ?>', <?= $n ?>)"
                class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-<?= $c ?>-400 hover:bg-<?= $c ?>-50 hover:text-<?= $c ?>-700 transition-all cursor-pointer"><?= $n ?></button>
            <?php endforeach; ?>
            <button type="button" onclick="setPagTodos('<?= $pag_prefix ?>')"
                class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-<?= $c ?>-400 hover:bg-<?= $c ?>-50 hover:text-<?= $c ?>-700 transition-all cursor-pointer">Todos</button>
        </div>

        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-<?= $pag_prefix ?>" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-<?= $c ?>-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('<?= $pag_prefix ?>')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>

        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('<?= $pag_prefix ?>')"
                class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('<?= $pag_prefix ?>')"
                class="px-4 py-2 rounded-xl bg-<?= $c ?>-600 text-white text-xs font-bold hover:bg-<?= $c ?>-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>

    </div>
</div>
