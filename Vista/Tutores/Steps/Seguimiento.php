<?php
// Vista/Tutores/Steps/Seguimiento.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Helpers/Paginador.php';
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

function prefijoDeFichero(array $al, string $carpetaCiclo): string {
    $ape1 = normalizarTextoSeg($al['apellido1'] ?? '');
    $ape2 = normalizarTextoSeg($al['apellido2'] ?? '');
    // carpetaCiclo es "2DAW" → queremos "DAW2" para que coincida con el nombre del fichero
    $cicloTag = preg_replace('/^(\d+)([A-Z]+)$/', '$2$1', strtoupper($carpetaCiclo));
    $partes = array_filter([$ape1, $ape2, $cicloTag]);
    return implode('_', $partes);
}

function contarArchivosSeg(string $ruta, string $prefijo = ''): int {
    if (!is_dir($ruta)) return 0;
    $ficheros = array_filter(
        scandir($ruta),
        fn($f) => !in_array($f, ['.', '..'])
               && is_file($ruta . $f)
               && ($prefijo === '' || stripos($f, $prefijo) === 0)
    );
    return count($ficheros);
}

// Estado global
$hayAlgunPF = false; $hayAlgunaFicha = false;
foreach ($alumnosSeguimiento as $al) {
    $prefijo = prefijoDeFichero($al, $carpetaCiclo);
    if (contarArchivosSeg($baseDoc . $carpetaCiclo . '/Plan_Formativo/', $prefijo) > 0) $hayAlgunPF     = true;
    if (contarArchivosSeg($baseDoc . $carpetaCiclo . '/Fichas/',         $prefijo) > 0) $hayAlgunaFicha = true;
}
if ($hayAlgunPF && $hayAlgunaFicha)      { $estadoGlobal = 'Completado'; $estadoGlobalColor = 'bg-emerald-100 text-emerald-700 border-emerald-200'; }
elseif ($hayAlgunPF || $hayAlgunaFicha)  { $estadoGlobal = 'Parcial';    $estadoGlobalColor = 'bg-amber-100 text-amber-700 border-amber-200'; }
else                                      { $estadoGlobal = 'Pendiente';  $estadoGlobalColor = 'bg-red-100 text-red-700 border-red-200'; }

// Paginación PHP
$pp_seg  = leerPorPagina('pp_seg', 6);
$pag_seg = leerPaginaActual('pag_seg');
$total_seg = count($alumnosSeguimiento);
$alumnosSeguimientoPag = paginarArray(array_values($alumnosSeguimiento), $pp_seg, $pag_seg);
?>

<!-- Cabecera -->
<div class="mb-5 flex items-center justify-between">
    <div>
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Seguimiento de Documentación</h2>
        <p class="text-[10px] font-bold text-slate-400 mt-1">
            Alumnos exportados · Carpeta: <span class="text-orange-600"><?= htmlspecialchars($carpetaCiclo) ?></span>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <button type="button" onclick="abrirModalMasivo()"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-600 text-white text-[10px] font-black hover:bg-orange-700 transition-all shadow-sm cursor-pointer uppercase tracking-wide">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Subida Masiva
        </button>
        <span class="<?= $estadoGlobalColor ?> px-3 py-1.5 rounded-full text-[9px] border font-black whitespace-nowrap uppercase">
            Estado general: <?= $estadoGlobal ?>
        </span>
    </div>
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
    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
        <?php if ($pp_seg > 0 && $total_seg > $pp_seg): ?>
            Mostrando <?= ($pag_seg - 1) * $pp_seg + 1 ?>–<?= min($pag_seg * $pp_seg, $total_seg) ?> de <?= $total_seg ?>
        <?php elseif ($total_seg > 0): ?>
            <?= $total_seg ?> alumno<?= $total_seg !== 1 ? 's' : '' ?>
        <?php endif; ?>
    </span>
    <button type="button" onclick="document.getElementById('modal-pag-seg').style.display='flex'" title="Configurar filas por página"
        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span><?= $pp_seg > 0 ? $pp_seg . '/pág' : 'Todos' ?></span>
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
            <?php foreach ($alumnosSeguimientoPag as $al):
                $prefijo   = prefijoDeFichero($al, $carpetaCiclo);
                $numPF     = contarArchivosSeg($baseDoc . $carpetaCiclo . '/Plan_Formativo/', $prefijo);
                $numFichas = contarArchivosSeg($baseDoc . $carpetaCiclo . '/Fichas/',         $prefijo);

                if ($numPF > 0 && $numFichas > 0)  { $estadoLabel = 'Completado'; $estadoColor = 'bg-emerald-100 text-emerald-700 border-emerald-200'; $ordenEstado = 4; }
                elseif ($numPF > 0 && $numFichas == 0) { $estadoLabel = 'Parcial';    $estadoColor = 'bg-amber-100 text-amber-700 border-amber-200';   $ordenEstado = 2; }
                elseif ($numPF == 0 && $numFichas > 0) { $estadoLabel = 'Parcial';    $estadoColor = 'bg-amber-100 text-amber-700 border-amber-200';   $ordenEstado = 3; }
                else                                    { $estadoLabel = 'Pendiente';  $estadoColor = 'bg-red-100 text-red-700 border-red-200';          $ordenEstado = 1; }

                $nombreCompleto = $al['apellido1'] . ' ' . ($al['apellido2'] ?? '') . ', ' . $al['nombre'];
                $prefijoJs = htmlspecialchars($prefijo, ENT_QUOTES);
            ?>
            <tr class="hover:bg-slate-50/50 transition-colors uppercase seg-fila"
                data-nombre="<?= htmlspecialchars(strtoupper($nombreCompleto), ENT_QUOTES) ?>"
                data-estado="<?= $estadoLabel ?>"
                data-orden-estado="<?= $ordenEstado ?>"
                data-carpeta="<?= $prefijoJs ?>">

                <td class="p-4 font-bold text-slate-700">
                    <?= htmlspecialchars($nombreCompleto) ?>
                </td>

                <td class="p-4 text-center">
                    <button type="button"
                        onclick="abrirModalDocumentos('plan_formativo', '<?= $prefijoJs ?>')"
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
                        onclick="abrirModalDocumentos('fichas', '<?= $prefijoJs ?>')"
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

<?= renderizarNavPaginacion($total_seg, $pag_seg, $pp_seg, 'pag_seg', 'orange', ['tab' => '4']) ?>

<?php endif; ?>

<script>
window.SEGUIMIENTO_CICLO = '<?= htmlspecialchars($carpetaCiclo, ENT_QUOTES) ?>';


<?php $pag_prefix = 'seg'; $pag_color = 'orange'; $pag_extra_params = ['tab' => '4']; include $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Vista/Shared/Modal_Paginacion.php'; ?>