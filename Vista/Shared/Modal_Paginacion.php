<?php
/**
 * Vista/Shared/Modal_Paginacion.php
 * Modal de configuración de paginación — lógica 100 % PHP (GET form).
 *
 * Variables requeridas antes del include:
 *   $pag_prefix  — prefijo de la tabla, p.ej. 'alum', 'conv', 'tut'
 *   $pag_color   — color Tailwind, p.ej. 'orange', 'blue', 'violet'
 *
 * El botón de apertura en la vista debe llamar:
 *   onclick="document.getElementById('modal-pag-PREFIX').style.display='flex'"
 */
$c   = $pag_color;
$pfx = $pag_prefix;
$ppParam  = 'pp_'  . $pfx;
$pagParam = 'pag_' . $pfx;
$ppActual = leerPorPagina($ppParam, 10);
$_pag_merged = array_merge($_GET, $pag_extra_params ?? []);
?>
<div id="modal-pag-<?= $pfx ?>" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)this.style.display='none'">
    <form method="GET" action="index.php"
          class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100"
          onclick="event.stopPropagation()">

        <?php foreach ($_pag_merged as $k => $v): ?>
            <?php if ($k !== $ppParam && $k !== $pagParam): ?>
            <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars((string)$v) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <input type="hidden" name="<?= $pagParam ?>" value="1">
        <input type="hidden" id="pp-val-<?= $pfx ?>" name="<?= $ppParam ?>" value="<?= $ppActual ?>">

        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button type="button"
                    onclick="document.getElementById('modal-pag-<?= $pfx ?>').style.display='none'"
                    class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>

        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ([5, 10, 15, 20, 25, 50] as $n): ?>
            <button type="button"
                    onclick="document.getElementById('pp-val-<?= $pfx ?>').value=<?= $n ?>;document.getElementById('pp-custom-<?= $pfx ?>').value=<?= $n ?>;this.closest('form').submit()"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-<?= $c ?>-400 hover:bg-<?= $c ?>-50 hover:text-<?= $c ?>-700 transition-all cursor-pointer"><?= $n ?></button>
            <?php endforeach; ?>
            <button type="button"
                    onclick="document.getElementById('pp-val-<?= $pfx ?>').value=0;document.getElementById('pp-custom-<?= $pfx ?>').value='';this.closest('form').submit()"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-<?= $c ?>-400 hover:bg-<?= $c ?>-50 hover:text-<?= $c ?>-700 transition-all cursor-pointer">Todos</button>
        </div>

        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="pp-custom-<?= $pfx ?>" min="1" max="200" placeholder="Ej: 12"
                   value="<?= $ppActual > 0 ? $ppActual : '' ?>"
                   oninput="document.getElementById('pp-val-<?= $pfx ?>').value=this.value?parseInt(this.value):0"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();this.closest('form').submit()}"
                   class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-<?= $c ?>-200 transition-all">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>

        <div class="flex gap-3 justify-end">
            <button type="button"
                    onclick="document.getElementById('modal-pag-<?= $pfx ?>').style.display='none'"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button type="submit"
                    class="px-4 py-2 rounded-xl bg-<?= $c ?>-600 text-white text-xs font-bold hover:bg-<?= $c ?>-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </form>
</div>
