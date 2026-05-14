<?php
// Vista/Tutores/Steps/Seguimiento.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
validarAcceso('tutor');

$alumnosSeguimiento = array_filter($alumnosFirmados ?? [], fn($a) => $a['exportado'] == 1);

$nombreCursoSeg = strtolower(trim($cursoTutor ?? ''));
$numCursoSeg = match(true) {
    str_contains($nombreCursoSeg, 'primero') => '1',
    str_contains($nombreCursoSeg, 'segundo') => '2',
    str_contains($nombreCursoSeg, 'tercero') => '3',
    default                                   => '1',
};
$cicloSafe    = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($cicloTutor ?? 'CICLO'));
$carpetaCiclo = $numCursoSeg . $cicloSafe;

$baseDoc = $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Documentacion/';

function normalizarTextoSeg(string $texto): string {
    $mapa = [
        'á'=>'A','é'=>'E','í'=>'I','ó'=>'O','ú'=>'U',
        'à'=>'A','è'=>'E','ì'=>'I','ò'=>'O','ù'=>'U',
        'ä'=>'A','ë'=>'E','ï'=>'I','ö'=>'O','ü'=>'U',
        'â'=>'A','ê'=>'E','î'=>'I','ô'=>'O','û'=>'U',
        'ã'=>'A','õ'=>'O','ñ'=>'N','ç'=>'C',
        'ý'=>'Y','ÿ'=>'Y',
        'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U',
        'À'=>'A','È'=>'E','Ì'=>'I','Ò'=>'O','Ù'=>'U',
        'Ä'=>'A','Ë'=>'E','Ï'=>'I','Ö'=>'O','Ü'=>'U',
        'Â'=>'A','Ê'=>'E','Î'=>'I','Ô'=>'O','Û'=>'U',
        'Ã'=>'A','Õ'=>'O','Ñ'=>'N','Ç'=>'C',
        'Ý'=>'Y','Ÿ'=>'Y',
    ];
    $texto = strtr($texto, $mapa);
    $texto = strtoupper($texto);
    return preg_replace('/[^A-Z0-9]+/', '_', $texto);
}

function carpetaAlumno(array $al): string {
    $ape1 = normalizarTextoSeg($al['apellido1'] ?? '');
    $ape2 = normalizarTextoSeg($al['apellido2'] ?? '');
    $nom  = normalizarTextoSeg($al['nombre']    ?? '');
    return trim($ape1 . '_' . $ape2 . '_' . $nom, '_');
}

function contarArchivosSeg(string $ruta): int {
    if (!is_dir($ruta)) return 0;
    return count(array_filter(scandir($ruta), fn($f) => !in_array($f, ['.', '..']) && is_file($ruta . $f)));
}

// Estado global
$hayAlgunPF = false; $hayAlgunaFicha = false;
foreach ($alumnosSeguimiento as $al) {
    $carpeta = carpetaAlumno($al);
    if (contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Plan_Formativo/') > 0) $hayAlgunPF     = true;
    if (contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Fichas/')          > 0) $hayAlgunaFicha = true;
}
if ($hayAlgunPF && $hayAlgunaFicha)      { $estadoGlobal = 'Completado'; $estadoGlobalColor = 'bg-emerald-100 text-emerald-700 border-emerald-200'; }
elseif ($hayAlgunPF || $hayAlgunaFicha)  { $estadoGlobal = 'Parcial';    $estadoGlobalColor = 'bg-amber-100 text-amber-700 border-amber-200'; }
else                                      { $estadoGlobal = 'Pendiente';  $estadoGlobalColor = 'bg-red-100 text-red-700 border-red-200'; }
?>

<!-- Cabecera -->
<div class="mb-5 flex items-center justify-between">
    <div>
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Seguimiento de Documentación</h2>
        <p class="text-[10px] font-bold text-slate-400 mt-1">
            Alumnos exportados · Carpeta: <span class="text-orange-600"><?= htmlspecialchars($carpetaCiclo) ?></span>
        </p>
    </div>
    <span class="<?= $estadoGlobalColor ?> px-3 py-1.5 rounded-full text-[9px] border font-black whitespace-nowrap uppercase">
        Estado general: <?= $estadoGlobal ?>
    </span>
</div>

<?php if (empty($alumnosSeguimiento)): ?>
    <div class="text-center text-slate-400 italic py-20 uppercase font-black text-[10px] tracking-widest">
        No hay alumnos exportados aún. Cuando un alumno sea exportado aparecerá aquí.
    </div>
<?php else: ?>

<!-- ── Barra de controles: buscar / ordenar / filtrar ── -->
<div class="flex flex-wrap gap-3 mb-4 items-center">

    <!-- Buscador -->
    <div class="relative flex-1 min-w-[180px]">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input id="segBuscador" type="text" placeholder="Buscar alumno..."
            class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all uppercase placeholder:normal-case placeholder:font-normal"
            oninput="segAplicarFiltros()">
    </div>

    <!-- Ordenar -->
    <select id="segOrden"
        class="px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer"
        onchange="segAplicarFiltros()">
        <option value="estado">Ordenar: Estado</option>
        <option value="nombre">Ordenar: Nombre</option>
    </select>

    <!-- Filtrar por estado -->
    <select id="segFiltroEstado"
        class="px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer"
        onchange="segAplicarFiltros()">
        <option value="">Todos los estados</option>
        <option value="Pendiente">🔴 Pendiente</option>
        <option value="Parcial">🟡 Parcial</option>
        <option value="Completado">🟢 Completado</option>
    </select>

    <button type="button" onclick="limpiarFiltrosSeg()"
        class="flex items-center gap-1.5 px-3 py-2.5 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide whitespace-nowrap">
        Mostrar todos
    </button>

</div>

<!-- Contador de registros + config paginación -->
<div class="flex items-center justify-between mb-2">
    <span id="seg-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
    <button type="button" onclick="abrirModalPag('seg')" title="Configurar filas por página"
        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span id="seg-pag-label">6/pág</span>
    </button>
</div>

<!-- Tabla -->
<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
    <table class="w-full text-left border-collapse bg-white">
        <thead>
            <tr class="bg-slate-50 text-slate-600 text-[10px] font-black uppercase">
                <th class="p-4">Alumno</th>
                <th class="p-4 text-center w-52">Plan Formativo Firmado</th>
                <th class="p-4 text-center w-52">Fichas Firmadas</th>
                <th class="p-4 text-center w-36">Estado Subida</th>
            </tr>
        </thead>
        <tbody id="segTablaCuerpo" class="divide-y divide-slate-100 bg-white text-[10px]">
            <?php foreach ($alumnosSeguimiento as $al):
                $carpeta   = carpetaAlumno($al);
                $numPF     = contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Plan_Formativo/');
                $numFichas = contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Fichas/');

                if ($numPF > 0 && $numFichas > 0)  { $estadoLabel = 'Completado'; $estadoColor = 'bg-emerald-100 text-emerald-700 border-emerald-200'; $ordenEstado = 4; }
                elseif ($numPF > 0 && $numFichas == 0) { $estadoLabel = 'Parcial';    $estadoColor = 'bg-amber-100 text-amber-700 border-amber-200';   $ordenEstado = 2; } // Solo PF
                elseif ($numPF == 0 && $numFichas > 0) { $estadoLabel = 'Parcial';    $estadoColor = 'bg-amber-100 text-amber-700 border-amber-200';   $ordenEstado = 3; } // Solo Fichas
                else                                    { $estadoLabel = 'Pendiente';  $estadoColor = 'bg-red-100 text-red-700 border-red-200';          $ordenEstado = 1; }

                $nombreCompleto = $al['apellido1'] . ' ' . ($al['apellido2'] ?? '') . ', ' . $al['nombre'];
                $carpetaJs = htmlspecialchars($carpeta, ENT_QUOTES);
            ?>
            <tr class="hover:bg-slate-50/50 transition-colors uppercase seg-fila"
                data-nombre="<?= htmlspecialchars(strtoupper($nombreCompleto), ENT_QUOTES) ?>"
                data-estado="<?= $estadoLabel ?>"
                data-orden-estado="<?= $ordenEstado ?>">

                <td class="p-4 font-bold text-slate-700">
                    <?= htmlspecialchars($nombreCompleto) ?>
                </td>

                <td class="p-4 text-center">
                    <button type="button"
                        onclick="abrirModalDocumentos('plan_formativo', '<?= $carpetaJs ?>')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-[9px] font-black text-slate-600 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Ver Documentos
                        <?php if ($numPF > 0): ?>
                            <span class="ml-1 bg-orange-600 text-white rounded-full px-1.5 py-0.5 text-[8px] font-black"><?= $numPF ?></span>
                        <?php endif; ?>
                    </button>
                </td>

                <td class="p-4 text-center">
                    <button type="button"
                        onclick="abrirModalDocumentos('fichas', '<?= $carpetaJs ?>')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-[9px] font-black text-slate-600 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Ver Documentos
                        <?php if ($numFichas > 0): ?>
                            <span class="ml-1 bg-orange-600 text-white rounded-full px-1.5 py-0.5 text-[8px] font-black"><?= $numFichas ?></span>
                        <?php endif; ?>
                    </button>
                </td>

                <td class="p-4 text-center">
                    <span class="<?= $estadoColor ?> px-3 py-1 rounded-full text-[8px] border font-black whitespace-nowrap">
                        <?= $estadoLabel ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Sin resultados -->
    <div id="segSinResultados" style="display:none"
         class="py-12 text-center text-slate-400 text-xs italic font-bold uppercase tracking-widest">
        No hay alumnos que coincidan con los filtros.
    </div>
</div>

<!-- Controles de paginación -->
<div id="seg-paginacion" class="flex items-center justify-between mt-3 hidden">
    <button id="seg-prev" onclick="segPagCambiar(segPagActual - 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Anterior
    </button>
    <div id="seg-paginas" class="flex items-center gap-1.5"></div>
    <button id="seg-next" onclick="segPagCambiar(segPagActual + 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        Siguiente
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
</div>

<?php endif; ?>

<script>
window.SEGUIMIENTO_CICLO = '<?= htmlspecialchars($carpetaCiclo, ENT_QUOTES) ?>';

// ─── Paginación + filtros ────────────────────────────────────────────────────
let segPorPagina = parseInt(localStorage.getItem('pag_seg_porPagina')) || 6;
let segPagActual = 1;
let _segFilasVis = [];

function segAplicarFiltros() {
    const busqueda = document.getElementById('segBuscador').value.trim().toUpperCase();
    const orden    = document.getElementById('segOrden').value;
    const filtro   = document.getElementById('segFiltroEstado').value;

    const tbody = document.getElementById('segTablaCuerpo');
    if (!tbody) return;

    const filas = Array.from(tbody.querySelectorAll('tr.seg-fila'));

    // 1. Filtrar
    let visibles = filas.filter(tr => {
        const nombre = tr.dataset.nombre || '';
        const estado = tr.dataset.estado || '';
        const pasaBusqueda = !busqueda || nombre.includes(busqueda);
        const pasaFiltro   = !filtro   || estado === filtro;
        return pasaBusqueda && pasaFiltro;
    });

    // 2. Ordenar
    visibles.sort((a, b) => {
        if (orden === 'nombre') {
            return (a.dataset.nombre || '').localeCompare(b.dataset.nombre || '');
        } else {
            return parseInt(a.dataset.ordenEstado) - parseInt(b.dataset.ordenEstado);
        }
    });

    // 3. Reordenar DOM: ocultar todo, mover visibles al final en orden correcto
    filas.forEach(tr => { tr.style.display = 'none'; });
    visibles.forEach(tr => tbody.appendChild(tr));

    _segFilasVis = visibles;
    segPagActual = 1;
    segPagRenderizar();
}

function segPagCambiar(nueva) {
    const totalPaginas = Math.ceil(_segFilasVis.length / segPorPagina) || 1;
    if (nueva < 1 || nueva > totalPaginas) return;
    segPagActual = nueva;
    segPagRenderizar();
}

function segPagRenderizar() {
    const total        = _segFilasVis.length;
    const totalPaginas = Math.ceil(total / segPorPagina) || 1;
    const inicio       = (segPagActual - 1) * segPorPagina;
    const fin          = Math.min(inicio + segPorPagina, total);

    // Mostrar solo la página actual dentro de las filas visibles
    _segFilasVis.forEach((tr, i) => {
        tr.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    document.getElementById('segSinResultados').style.display = total === 0 ? 'block' : 'none';

    const paginacion = document.getElementById('seg-paginacion');
    const contador   = document.getElementById('seg-contador');
    if (!paginacion) return;

    if (total <= segPorPagina) {
        paginacion.classList.add('hidden');
        if (contador) contador.textContent = total > 0 ? `${total} alumno${total !== 1 ? 's' : ''}` : '';
        return;
    }

    paginacion.classList.remove('hidden');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('seg-prev').disabled = segPagActual === 1;
    document.getElementById('seg-next').disabled = segPagActual === totalPaginas;

    const contenedor = document.getElementById('seg-paginas');
    contenedor.innerHTML = '';

    const pagsMostrar = new Set(
        [1, totalPaginas, segPagActual, segPagActual - 1, segPagActual + 1]
        .filter(p => p >= 1 && p <= totalPaginas)
    );
    const pagsOrdenadas = [...pagsMostrar].sort((a, b) => a - b);

    let anterior = null;
    pagsOrdenadas.forEach(p => {
        if (anterior !== null && p - anterior > 1) {
            const sep = document.createElement('span');
            sep.className = 'text-slate-300 text-xs font-bold px-1';
            sep.textContent = '···';
            contenedor.appendChild(sep);
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = p;
        btn.onclick = () => segPagCambiar(p);
        btn.className = p === segPagActual
            ? 'w-8 h-8 rounded-lg bg-orange-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
        anterior = p;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const label = document.getElementById('seg-pag-label');
    if (label) label.textContent = segPorPagina + '/pág';
    segAplicarFiltros();
});

function limpiarFiltrosSeg() {
    document.getElementById('segBuscador').value = '';
    document.getElementById('segFiltroEstado').value = '';
    document.getElementById('segOrden').value = 'estado';
    segAplicarFiltros();
}

// ─── Modal configurar paginación ─────────────────────────────────────────────
window._pagCallbacks = window._pagCallbacks || {};
window._pagCallbacks['seg'] = function(n) {
    segPorPagina = n;
    const label = document.getElementById('seg-pag-label');
    if (label) label.textContent = n + '/pág';
    segPagActual = 1;
    segPagRenderizar();
};

function abrirModalPag(prefix) {
    const defaults = { lp: 6, seg: 6 };
    const val = parseInt(localStorage.getItem('pag_' + prefix + '_porPagina')) || defaults[prefix] || 10;
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

<!-- ─── Modal Configurar Paginación: Seguimiento ───────────────────────────── -->
<div id="modal-pag-seg" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('seg')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('seg')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('seg', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('seg', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('seg', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('seg', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('seg', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('seg', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-seg" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-orange-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('seg')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('seg')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('seg')" class="px-4 py-2 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../Components/Modales_Seguimiento.php'; ?>
