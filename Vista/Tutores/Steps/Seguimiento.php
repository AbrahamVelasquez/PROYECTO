<?php

/**
 * Vista/Tutores/Steps/Seguimiento.php — Paso 4: Seguimiento de documentación
 *
 * Muestra la tabla de alumnos que ya han sido exportados (exportado = 1)
 * y permite al tutor gestionar los tres tipos de documentación por alumno:
 *   - Plan Formativo firmado
 *   - Fichas de seguimiento
 *   - Valoraciones finales
 *
 * Cada celda de la tabla abre un modal (Modales_Seguimiento.php) donde se pueden
 * subir, descargar y eliminar archivos PDF. La comunicación con el servidor
 * para estas operaciones se hace via AJAX hacia los helpers de Seguimiento.
 *
 * Secciones de esta vista:
 *   - Cabecera con estado global del ciclo (Pendiente / Parcial / Completado)
 *   - Filtros: búsqueda con autocomplete, ordenación y filtro por estado
 *   - Tabla paginada de alumnos con contadores de documentos por tipo
 *   - Script JS: filtrado en el cliente sin recargar la página, dropdown de búsqueda
 *
 * La lógica del estado global (el badge de la cabecera) comprueba si existe
 * al menos un archivo de cada tipo en todo el ciclo, no solo para un alumno.
 *
 * Variables recibidas del Controlador_Tutores → mostrarPanel():
 *   $alumnosFirmados  → alumnos con asignación firmada (Paso 3 completado)
 *   $cicloTutor       → nombre del ciclo, para construir la ruta de carpetas
 *   $cursoTutor       → nombre del curso ("Primero", "Segundo"...) para el prefijo
 *
 * MVC: Vista. No accede a BD directamente. Los archivos en disco se consultan
 * desde PHP (contarArchivosSeg) para inicializar los contadores de la tabla.
 */

require_once __DIR__ . '/../../../Seguridad/Control_Accesos.php';

validarAcceso('tutor');

require_once __DIR__ . '/../../../Helpers/Paginador.php';

// Solo mostramos alumnos que han pasado por el proceso de exportación del Plan Formativo
$alumnosFirmados    = $alumnosFirmados ?? [];
$alumnosSeguimiento = array_filter($alumnosFirmados, fn($a) => isset($a['exportado']) && $a['exportado'] == 1);

$nombreCursoSeg = strtolower(trim($cursoTutor ?? ''));
$numCursoSeg = match(true) {
    str_contains($nombreCursoSeg, 'primero') => '1',
    str_contains($nombreCursoSeg, 'segundo') => '2',
    str_contains($nombreCursoSeg, 'tercero') => '3',
    default                                  => '1',
};
$cicloSafe    = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($cicloTutor ?? 'CICLO'));
$carpetaCiclo = $numCursoSeg . $cicloSafe;

$baseDoc = realpath(__DIR__ . '/../../../Documentacion') . '/';

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

// Un único scan: calcula estado de cada alumno, estado global y lista para masivo
$hayAlgunPF = false; $hayAlgunaFicha = false; $hayAlgunaValoracion = false;
$alumnosSeguimientoConEstado = [];
foreach ($alumnosSeguimiento as $al) {
    $prefijo       = prefijoDeFichero($al, $carpetaCiclo);
    $numPF         = contarArchivosSeg($baseDoc . $carpetaCiclo . '/Plan_Formativo/', $prefijo);
    $numFichas     = contarArchivosSeg($baseDoc . $carpetaCiclo . '/Fichas/',         $prefijo);
    $numValoracion = contarArchivosSeg($baseDoc . $carpetaCiclo . '/Valoraciones/',   $prefijo);
    if ($numPF > 0)         $hayAlgunPF          = true;
    if ($numFichas > 0)     $hayAlgunaFicha      = true;
    if ($numValoracion > 0) $hayAlgunaValoracion = true;
    $tieneLosTres = $numPF > 0 && $numFichas > 0 && $numValoracion > 0;
    $tieneAlguno  = $numPF > 0 || $numFichas > 0 || $numValoracion > 0;
    $al['_numPF']        = $numPF;
    $al['_numFichas']    = $numFichas;
    $al['_numValoracion']= $numValoracion;
    $al['_estado']       = $tieneLosTres ? 'Completado' : ($tieneAlguno ? 'Parcial' : 'Pendiente');
    $al['_orden_estado'] = $tieneLosTres ? 4 : ($tieneAlguno ? 2 : 1);
    $alumnosSeguimientoConEstado[] = $al;
}

if ($hayAlgunPF && $hayAlgunaFicha && $hayAlgunaValoracion) {
    $estadoGlobal = 'Completado'; $estadoGlobalColor = 'bg-emerald-100 text-emerald-700 border-emerald-200';
} elseif ($hayAlgunPF || $hayAlgunaFicha || $hayAlgunaValoracion) {
    $estadoGlobal = 'Parcial';    $estadoGlobalColor = 'bg-amber-100 text-amber-700 border-amber-200';
} else {
    $estadoGlobal = 'Pendiente';  $estadoGlobalColor = 'bg-red-100 text-red-700 border-red-200';
}

// Filtros desde GET (persisten en la URL y en los enlaces de paginación)
$seg_orden    = $_GET['seg_orden']    ?? 'estado';
$seg_estado   = $_GET['seg_estado']   ?? '';
$seg_busqueda = trim($_GET['seg_busqueda'] ?? '');

// Filtrar
$alumnosFiltrados = $alumnosSeguimientoConEstado;
if ($seg_estado !== '') {
    $alumnosFiltrados = array_values(array_filter($alumnosFiltrados, fn($al) => $al['_estado'] === $seg_estado));
}
if ($seg_busqueda !== '') {
    $alumnosFiltrados = array_values(array_filter($alumnosFiltrados, function ($al) use ($seg_busqueda) {
        $texto = $al['apellido1'] . ' ' . ($al['apellido2'] ?? '') . ' ' . $al['nombre'];
        return mb_stripos($texto, $seg_busqueda) !== false;
    }));
}

// Ordenar
if ($seg_orden === 'nombre') {
    usort($alumnosFiltrados, fn($a, $b) => strcmp($a['apellido1'] ?? '', $b['apellido1'] ?? ''));
} else {
    usort($alumnosFiltrados, fn($a, $b) =>
        ($a['_orden_estado'] - $b['_orden_estado']) ?: strcmp($a['apellido1'] ?? '', $b['apellido1'] ?? ''));
}

// Paginación
$pp_seg   = leerPorPagina('pp_seg', 6);
$pag_seg  = leerPaginaActual('pag_seg');
$total_seg = count($alumnosFiltrados);
$alumnosSeguimientoPag = paginarArray($alumnosFiltrados, $pp_seg, $pag_seg);

// Lista COMPLETA de alumnos (sin filtrar, sin paginar) para la subida masiva
$allAlumnosParaMasivo = array_map(fn($al) => [
    'carpeta' => prefijoDeFichero($al, $carpetaCiclo),
    'nombre'  => mb_strtoupper(trim($al['apellido1'] . ' ' . ($al['apellido2'] ?? '') . ', ' . $al['nombre']), 'UTF-8'),
], $alumnosSeguimientoConEstado);
?>

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

<form method="GET" action="index.php" id="formFiltroSeg" class="flex flex-wrap gap-3 mb-4 items-center">
    <input type="hidden" name="tab" value="4">
    <input type="hidden" name="pp_seg" value="<?= (int)$pp_seg ?>">

    <div class="relative flex-1 min-w-[180px]" id="seg-dropdown-wrapper">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input id="segBuscador" name="seg_busqueda" type="text"
            value="<?= htmlspecialchars($seg_busqueda) ?>"
            placeholder="Buscar alumno..."
            autocomplete="off"
            class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all uppercase placeholder:normal-case placeholder:font-normal">
        <ul id="seg-dropdown"
            class="hidden absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden max-h-72 overflow-y-auto">
        </ul>
    </div>

    <select id="segOrden" name="seg_orden"
        class="px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer"
        onchange="this.form.submit()">
        <option value="estado" <?= $seg_orden === 'estado' ? 'selected' : '' ?>>Ordenar: Estado</option>
        <option value="nombre" <?= $seg_orden === 'nombre' ? 'selected' : '' ?>>Ordenar: Nombre</option>
    </select>

    <select id="segFiltroEstado" name="seg_estado"
        class="px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer"
        onchange="this.form.submit()">
        <option value="" <?= $seg_estado === '' ? 'selected' : '' ?>>Todos los estados</option>
        <option value="Pendiente" <?= $seg_estado === 'Pendiente' ? 'selected' : '' ?>>🔴 Pendiente</option>
        <option value="Parcial"   <?= $seg_estado === 'Parcial'   ? 'selected' : '' ?>>🟡 Parcial</option>
        <option value="Completado"<?= $seg_estado === 'Completado'? 'selected' : '' ?>>🟢 Completado</option>
    </select>

    <button type="submit" id="segBtnBuscar"
        class="bg-slate-900 text-white px-5 py-2.5 rounded-xl font-bold text-[10px] hover:bg-slate-800 transition-all shadow-sm uppercase tracking-wider cursor-pointer flex items-center gap-2 whitespace-nowrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        BUSCAR
    </button>
</form>

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
        <span><?= htmlspecialchars($pp_seg) . '/pág' ?></span>
    </button>
</div>

<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
    <table class="w-full text-left border-collapse bg-white">
        <thead>
            <tr class="bg-slate-50 text-slate-600 text-[10px] font-black uppercase">
                <th class="p-4">Alumno</th>
                <th class="p-4 text-center w-44">Plan Formativo Firmado</th>
                <th class="p-4 text-center w-44">Fichas Firmadas</th>
                <th class="p-4 text-center w-44">Valoraciones</th>
                <th class="p-4 text-center w-36">Estado Subida</th>
            </tr>
        </thead>
        <tbody id="segTablaCuerpo" class="divide-y divide-slate-100 bg-white text-[10px]">
            <?php foreach ($alumnosSeguimientoPag as $al):
                $prefijo       = prefijoDeFichero($al, $carpetaCiclo);
                $numPF         = $al['_numPF'];
                $numFichas     = $al['_numFichas'];
                $numValoracion = $al['_numValoracion'];
                $estadoLabel   = $al['_estado'];
                $ordenEstado   = $al['_orden_estado'];
                $estadoColor   = match($estadoLabel) {
                    'Completado' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'Parcial'    => 'bg-amber-100 text-amber-700 border-amber-200',
                    default      => 'bg-red-100 text-red-700 border-red-200',
                };
                $nombreCompleto = $al['apellido1'] . ' ' . ($al['apellido2'] ?? '') . ', ' . $al['nombre'];
                $prefijoJs = htmlspecialchars($prefijo, ENT_QUOTES);
            ?>
            <tr class="hover:bg-slate-50/50 transition-colors uppercase seg-fila"
                data-nombre="<?= htmlspecialchars(mb_strtoupper($nombreCompleto, "UTF-8"), ENT_QUOTES) ?>"
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
                    <button type="button"
                        onclick="abrirModalDocumentos('valoraciones', '<?= $prefijoJs ?>')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-[9px] font-black text-slate-600 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Ver Documentos
                        <?php if ($numValoracion > 0): ?>
                            <span class="ml-1 bg-orange-600 text-white rounded-full px-1.5 py-0.5 text-[8px] font-black"><?= $numValoracion ?></span>
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

    <div id="segSinResultados" style="display:none"
         class="py-12 text-center text-slate-400 text-xs italic font-bold uppercase tracking-widest">
        No hay alumnos que coincidan con los filtros.
    </div>
</div>

<?= renderizarNavPaginacion($total_seg, $pag_seg, $pp_seg, 'pag_seg', 'orange', [
    'tab'          => '4',
    'seg_orden'    => $seg_orden,
    'seg_estado'   => $seg_estado,
    'seg_busqueda' => $seg_busqueda,
]) ?>

<?php endif; ?>

<?php include __DIR__ . '/../Components/Modales_Seguimiento.php'; ?>

<script>
window.SEGUIMIENTO_CICLO = '<?= htmlspecialchars($carpetaCiclo, ENT_QUOTES) ?>';
window.SEGUIMIENTO_ALUMNOS_ALL = <?= json_encode(array_values($allAlumnosParaMasivo), JSON_UNESCAPED_UNICODE) ?>;

function segAplicarFiltros() {
    document.getElementById('formFiltroSeg')?.submit();
}

function limpiarFiltrosSeg() {
    const url = new URL(window.location.href);
    url.searchParams.delete('seg_busqueda');
    url.searchParams.delete('seg_estado');
    url.searchParams.delete('seg_orden');
    url.searchParams.delete('pag_seg');
    window.location.href = url.toString();
}

// ── Dropdown Seguimiento ────────────────────────────────────────────────────
(function () {
    const input    = document.getElementById('segBuscador');
    const dropdown = document.getElementById('seg-dropdown');
    const wrapper  = document.getElementById('seg-dropdown-wrapper');
    if (!input || !dropdown) return;
    let activeIndex = -1;

    function getSugerencias(q) {
        const txt = q.toUpperCase().trim();
        if (!txt) return [];
        const vistas = new Set();
        const res = [];
        (window.SEGUIMIENTO_ALUMNOS_ALL || []).forEach(al => {
            const nombre = (al.nombre || '').toUpperCase();
            if (nombre.includes(txt) && !vistas.has(nombre)) {
                vistas.add(nombre);
                res.push({ valor: nombre });
            }
        });
        return res.slice(0, 10);
    }

    function resaltar(texto, q) {
        const esc = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return texto.replace(new RegExp('(' + esc + ')', 'gi'),
            '<mark class="bg-orange-100 text-orange-700 rounded px-0.5">$1</mark>');
    }

    function mostrar(sugerencias) {
        dropdown.innerHTML = '';
        activeIndex = -1;
        if (!sugerencias.length) { ocultar(); return; }
        sugerencias.forEach(s => {
            const li = document.createElement('li');
            li.className = 'px-5 py-3 cursor-pointer hover:bg-orange-50 transition-colors border-b border-slate-100 last:border-b-0 text-[11px] font-black text-slate-800 uppercase tracking-wide';
            li.innerHTML = resaltar(s.valor, input.value);
            li.addEventListener('mousedown', e => { e.preventDefault(); seleccionar(s.valor); });
            dropdown.appendChild(li);
        });
        dropdown.classList.remove('hidden');
    }

    function ocultar() { dropdown.classList.add('hidden'); dropdown.innerHTML = ''; activeIndex = -1; }

    function seleccionar(valor) {
        input.value = valor;
        ocultar();
        document.getElementById('formFiltroSeg')?.submit();
    }

    function resaltarActivo() {
        dropdown.querySelectorAll('li').forEach((li, i) => li.classList.toggle('bg-orange-50', i === activeIndex));
    }

    input.addEventListener('input', () => {
        const q = input.value.trim();
        if (q.length < 2) { ocultar(); return; }
        mostrar(getSugerencias(q));
    });

    input.addEventListener('keydown', e => {
        const items = dropdown.querySelectorAll('li');
        if (e.key === 'ArrowDown') { e.preventDefault(); activeIndex = Math.min(activeIndex + 1, items.length - 1); resaltarActivo(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); activeIndex = Math.max(activeIndex - 1, -1); resaltarActivo(); }
        else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && items[activeIndex]) {
                input.value = items[activeIndex].textContent.trim();
                ocultar();
            }
            document.getElementById('formFiltroSeg')?.submit();
        } else if (e.key === 'Escape') { ocultar(); }
    });

    document.addEventListener('click', e => { if (!wrapper.contains(e.target)) ocultar(); });
})();
</script>

<?php
$pag_prefix = 'seg';
$pag_color = 'orange';
$pag_extra_params = [
    'tab'          => '4',
    'seg_orden'    => $seg_orden,
    'seg_estado'   => $seg_estado,
    'seg_busqueda' => $seg_busqueda,
];

include __DIR__ . '/../../Shared/Modal_Paginacion.php';
?>