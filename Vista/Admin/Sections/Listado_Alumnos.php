<?php

/**
 * Vista/Admin/Sections/Listado_Alumnos.php — Sección "Listado de Alumnos" del panel admin
 *
 * Vista de sólo lectura (no edición) que muestra todos los alumnos del sistema
 * con sus datos de asignación: empresa, ciclo, fechas y estado.
 * El admin puede filtrar por ciclo y exportar el listado completo.
 *
 * La paginación usa Paginador.php con clave de GET pp_ladm/pag_ladm.
 * Los tooltips de estado de cada fila se implementan con CSS puro ([data-tooltip]).
 * Los modales están en Modales_LA.php.
 *
 * Variables recibidas del controlador: $alumnos (array completo).
 */

require_once __DIR__ . '/../../../Seguridad/Control_Accesos.php';

validarAcceso('admin');

require_once __DIR__ . '/../../../Helpers/Paginador.php';

// Paginación PHP
$pp_ladm    = leerPorPagina('pp_ladm', 10);
$pag_ladm   = leerPaginaActual('pag_ladm');
$total_ladm = count($alumnos ?? []);
$alumnosPag = paginarArray($alumnos ?? [], $pp_ladm, $pag_ladm);

?>

<style>
    [data-tooltip] { position: relative; }
    [data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        top: 50%; right: 125%;
        transform: translateY(-50%);
        background: #1e293b; color: white;
        padding: 8px 12px; border-radius: 6px;
        font-size: 10px; font-weight: bold;
        z-index: 9999; white-space: normal;
        width: 160px; text-align: center;
        line-height: 1.4; text-transform: none;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
        pointer-events: none;
    }
    [data-tooltip]:hover::before {
        content: '';
        position: absolute;
        top: 50%; right: 105%;
        transform: translateY(-50%);
        border-width: 5px; border-style: solid;
        border-color: transparent transparent transparent #1e293b;
        z-index: 9999;
    }
</style>

<div class="space-y-6">

    <!-- Cabecera -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4 px-2">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Listado de Alumnos</h2>
            <p class="text-slate-500 text-[11px] uppercase font-bold tracking-[0.2em] mt-1 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                Pendientes de firma: <?= count($alumnos ?? []) ?>
            </p>
        </div>
        <div class="flex items-center gap-4">
            <form action="index.php" method="POST">
                <input type="hidden" name="accion" value="mostrarPanel">
                <button type="submit" class="group flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-violet-600 transition-all cursor-pointer">
                    <span class="transition-transform group-hover:-translate-x-1">←</span> Volver al inicio
                </button>
            </form>
        </div>
    </div>

    <!-- Filtros -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

        <!-- Buscador -->
        <div class="relative flex-1" id="ladm-dropdown-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="search" id="buscadorAlumnos"
                placeholder="Buscar por nombre o apellidos…"
                autocomplete="off"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200"/>
            <ul id="ladm-dropdown"
                class="hidden absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden max-h-72 overflow-y-auto">
            </ul>
        </div>

        <!-- Filtro ciclo -->
        <select id="filtroCiclo" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
            <option value="">Todos los ciclos</option>
            <?php
            $ciclosVistos = [];
            foreach ($alumnos ?? [] as $al) {
                $key = $al['nombre_ciclo'] . ' ' . ucfirst($al['grado']);
                if (!in_array($key, $ciclosVistos)) {
                    $ciclosVistos[] = $key;
                    echo '<option value="' . htmlspecialchars($key) . '">' . htmlspecialchars($key) . '</option>';
                }
            }
            ?>
        </select>

        <!-- Filtro empresa -->
        <select id="filtroEmpresa" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
            <option value="">Todas las empresas</option>
            <?php
            $empresasVistas = [];
            foreach ($alumnos ?? [] as $al) {
                $emp = $al['nombre_empresa'];
                if (!in_array($emp, $empresasVistas)) {
                    $empresasVistas[] = $emp;
                    echo '<option value="' . htmlspecialchars($emp) . '">' . htmlspecialchars($emp) . '</option>';
                }
            }
            ?>
        </select>

    </div>

    <!-- Barra superior: contador + config paginación -->
    <div class="flex items-center justify-between mb-2">
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
            Mostrando <?= ($pag_ladm - 1) * $pp_ladm + 1 ?>–<?= min($pag_ladm * $pp_ladm, $total_ladm) ?> de <?= $total_ladm ?>
        </span>
        <button type="button" onclick="document.getElementById('modal-pag-ladm').style.display='flex'" title="Configurar filas por página"
            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer uppercase tracking-wide">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span><?= $pp_ladm . '/pág' ?></span>
        </button>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm">
        <table class="w-full border-collapse bg-white text-left">
            <thead>
                <tr class="bg-slate-800 text-white text-[10px] font-black uppercase tracking-wide">
                    <th class="p-4">Alumno</th>
                    <th class="p-4 w-28">DNI / NIE</th>
                    <th class="p-4 w-10 text-center">Sexo</th>
                    <th class="p-4">Ciclo</th>
                    <th class="p-4">Empresa</th>
                    <th class="p-4">Dirección CT</th>
                    <th class="p-4 w-24 text-center">F. Inicio</th>
                    <th class="p-4 w-24 text-center">F. Final</th>
                    <th class="p-4 w-28 text-center">Horario</th>
                    <th class="p-4 w-14 text-center">H/Día</th>
                    <th class="p-4 w-24 text-center">Firmar</th>
                </tr>
            </thead>
            <tbody id="tablaAlumnosBody" class="divide-y divide-slate-100 text-[11px] uppercase">

                <?php if (empty($alumnos)): ?>
                    <tr>
                        <td colspan="11" class="py-14 text-center text-slate-400 italic text-sm normal-case">
                            No hay alumnos con FCT firmada registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($alumnosPag as $al):
                        $f_inicio   = (!empty($al['fecha_inicio']) && $al['fecha_inicio'] !== '0000-00-00') ? $al['fecha_inicio'] : null;
                        $f_final    = (!empty($al['fecha_final'])  && $al['fecha_final']  !== '0000-00-00') ? $al['fecha_final']  : null;
                        $tieneHorario = (!empty($al['horario']) && !empty($al['horas_dia']) && $al['horas_dia'] > 0);
                        $cicloLabel   = htmlspecialchars($al['nombre_ciclo'] . ' ' . ucfirst($al['grado']));
                    ?>
                    <tr class="ladm-fila hover:bg-slate-50/60 transition-colors" data-ciclo="<?= $cicloLabel ?>" data-empresa="<?= htmlspecialchars($al['nombre_empresa']) ?>">

                        <!-- Alumno -->
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <?php $iniciales = mb_strtoupper(mb_substr($al['apellido1'], 0, 1) . mb_substr($al['nombre'], 0, 1)); ?>
                                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[11px] font-bold text-emerald-700">
                                    <?= $iniciales ?>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($al['apellido1'] . ' ' . $al['apellido2'] . ', ' . $al['nombre']) ?></p>
                                    <p class="text-[9px] normal-case text-slate-400"><?= htmlspecialchars($al['correo'] ?? '') ?></p>
                                </div>
                            </div>
                        </td>

                        <!-- DNI -->
                        <td class="p-4 font-mono text-slate-500"><?= htmlspecialchars($al['dni'] ?? '-') ?></td>

                        <!-- Sexo -->
                        <td class="p-4 text-center text-slate-500"><?= htmlspecialchars($al['sexo'] ?? '-') ?></td>

                        <!-- Ciclo -->
                        <td class="p-4">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-[9px] font-black text-slate-600">
                                <?= $cicloLabel ?>
                            </span>
                            <p class="mt-1 text-[9px] normal-case text-slate-400"><?= htmlspecialchars(ucfirst($al['nombre_curso'])) ?></p>
                        </td>

                        <!-- Empresa -->
                        <td class="p-4 font-semibold text-slate-700"><?= htmlspecialchars($al['nombre_empresa']) ?></td>

                        <!-- Dirección CT -->
                        <td class="p-4">
                            <?php if (!empty($al['direccion'])): ?>
                                <p class="text-[9px] normal-case leading-tight text-slate-600"><?= htmlspecialchars($al['direccion']) ?></p>
                                <p class="text-[9px] font-bold text-slate-400"><?= htmlspecialchars($al['localidad'] ?? '') ?></p>
                            <?php else: ?>
                                <span class="text-orange-400 font-black italic text-[9px]">⚠️ Sin dir.</span>
                            <?php endif; ?>
                        </td>

                        <!-- F. Inicio -->
                        <td class="p-4 text-center text-slate-600">
                            <?= $f_inicio ? date('d/m/y', strtotime($f_inicio)) : '<span class="text-orange-400 font-bold">--/--/--</span>' ?>
                        </td>

                        <!-- F. Final -->
                        <td class="p-4 text-center text-slate-600">
                            <?= $f_final ? date('d/m/y', strtotime($f_final)) : '<span class="text-orange-400 font-bold">--/--/--</span>' ?>
                        </td>

                        <!-- Horario -->
                        <td class="p-4 text-center text-slate-600">
                            <?php if (!$tieneHorario): ?>
                                <span class="text-orange-400 font-black italic text-[9px]">⚠️ Sin horario</span>
                            <?php else:
                                $excepciones = trim($al['horario_excepciones'] ?? '');
                                $bloques = $excepciones ? json_decode($excepciones, true) : null;
                                if (!empty($bloques) && is_array($bloques)):
                                    $ORDEN = ['L'=>0,'M'=>1,'X'=>2,'J'=>3,'V'=>4,'S'=>5,'D'=>6];
                                    foreach ($bloques as $bloque):
                                        if (empty($bloque['dias'])) continue;
                                        $dias = $bloque['dias'];
                                        usort($dias, fn($a,$b) => $ORDEN[$a] - $ORDEN[$b]);
                                        $esConsecutivo = true;
                                        for ($i = 1; $i < count($dias); $i++) {
                                            if ($ORDEN[$dias[$i]] !== $ORDEN[$dias[$i-1]] + 1) { $esConsecutivo = false; break; }
                                        }
                                        $labelDias = (count($dias) > 1 && $esConsecutivo)
                                            ? $dias[0] . '-' . $dias[count($dias)-1]
                                            : implode('', $dias);
                            ?>
                                    <span class="block text-slate-600 leading-tight"><?= htmlspecialchars($labelDias . ' ' . $bloque['inicio'] . '-' . $bloque['fin']) ?></span>
                            <?php   endforeach;
                                else: ?>
                                    <span class="text-slate-600"><?= htmlspecialchars($al['horario']) ?></span>
                            <?php endif; endif; ?>
                        </td>

                        <!-- H/Día -->
                        <td class="p-4 text-center font-bold text-slate-700">
                            <?= $tieneHorario ? number_format($al['horas_dia'], 0) : '-' ?>
                        </td>

                        <!-- Firmar -->
                        <td class="p-4 text-center">
                            <button type="button"
                                onclick="abrirModalFirmaAdmin(<?= $al['id_asignacion'] ?>, '<?= htmlspecialchars(addslashes($al['apellido1'] . ' ' . $al['apellido2'] . ', ' . $al['nombre'])) ?>')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-1.5 text-[9px] font-black uppercase tracking-wide text-white shadow-sm transition hover:bg-emerald-700 active:scale-95 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Firmar
                            </button>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>


    <?= renderizarNavPaginacion($total_ladm, $pag_ladm, $pp_ladm, 'pag_ladm', 'violet', ['accion' => 'mostrarListadoAlumnos']) ?>

</div><!-- end .space-y-6 -->

<script>

function filtrarAlumnosAdmin() {
    const texto   = document.getElementById('buscadorAlumnos').value.trim().toUpperCase();
    const ciclo   = document.getElementById('filtroCiclo').value.toUpperCase();
    const empresa = document.getElementById('filtroEmpresa').value.toUpperCase();

    document.querySelectorAll('#tablaAlumnosBody tr.ladm-fila').forEach(tr => {
        const nombre      = tr.querySelector('td:first-child').textContent.toUpperCase();
        const dataCiclo   = (tr.dataset.ciclo   || '').toUpperCase();
        const dataEmpresa = (tr.dataset.empresa  || '').toUpperCase();
        const visible = (!texto   || nombre.includes(texto))
                     && (!ciclo   || dataCiclo === ciclo)
                     && (!empresa || dataEmpresa === empresa);
        tr.style.display = visible ? '' : 'none';
    });
}

function limpiarFiltrosAdmin() {
    document.getElementById('buscadorAlumnos').value = '';
    document.getElementById('filtroCiclo').value     = '';
    document.getElementById('filtroEmpresa').value   = '';
    filtrarAlumnosAdmin();
}

document.getElementById('filtroCiclo').addEventListener('change',     filtrarAlumnosAdmin);
document.getElementById('filtroEmpresa').addEventListener('change',   filtrarAlumnosAdmin);

// ── Dropdown Listado Alumnos Admin ─────────────────────────────────────────
(function(){
    const input = document.getElementById('buscadorAlumnos');
    const dropdown = document.getElementById('ladm-dropdown');
    const wrapper = document.getElementById('ladm-dropdown-wrapper');
    let activeIndex = -1;

    function getSugerencias(q) {
        const txt = q.toLowerCase().trim();
        if (!txt) return [];
        const vistas = new Set(), res = [];
        document.querySelectorAll('tr.ladm-fila').forEach(tr => {
            const nombre = (tr.querySelector('td:first-child p:first-child')?.textContent || '').trim();
            const dni    = (tr.children[1]?.textContent || '').trim();
            [[nombre, dni], [dni, nombre]].forEach(([v, sub]) => {
                if (v && v.toLowerCase().includes(txt) && !vistas.has(v)) {
                    vistas.add(v); res.push({ valor: v, sublabel: sub });
                }
            });
        });
        return res.slice(0, 10);
    }
    function resaltar(t, q) {
        return t.replace(new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')','gi'),
            '<mark class="bg-slate-200 text-slate-800 rounded px-0.5">$1</mark>');
    }
    function ocultar() { dropdown.classList.add('hidden'); dropdown.innerHTML=''; activeIndex=-1; }
    function seleccionar(v) { input.value=v; ocultar(); filtrarAlumnosAdmin(); }
    function resaltarActivo() { dropdown.querySelectorAll('li').forEach((li,i)=>li.classList.toggle('bg-slate-100',i===activeIndex)); }

    function mostrar(sugs) {
        dropdown.innerHTML=''; activeIndex=-1;
        if (!sugs.length) { ocultar(); return; }
        sugs.forEach(s => {
            const li = document.createElement('li');
            li.className = 'px-5 py-3 cursor-pointer hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-b-0';
            li.innerHTML = `<div class="text-[11px] font-bold text-slate-800">${resaltar(s.valor, input.value)}</div>
                            <div class="text-[10px] font-bold text-slate-400 font-mono mt-0.5">${s.sublabel}</div>`;
            li.addEventListener('mousedown', e => { e.preventDefault(); seleccionar(s.valor); });
            dropdown.appendChild(li);
        });
        dropdown.classList.remove('hidden');
    }

    input.addEventListener('input', () => {
        const q = input.value.trim();
        if (q.length < 2) { ocultar(); return; }
        mostrar(getSugerencias(q));
    });
    input.addEventListener('keydown', e => {
        const items = dropdown.querySelectorAll('li');
        if (e.key==='ArrowDown') { e.preventDefault(); activeIndex=Math.min(activeIndex+1,items.length-1); resaltarActivo(); }
        else if (e.key==='ArrowUp') { e.preventDefault(); activeIndex=Math.max(activeIndex-1,-1); resaltarActivo(); }
        else if (e.key==='Enter') {
            e.preventDefault();
            if (activeIndex>=0 && items[activeIndex]) { input.value=items[activeIndex].querySelector('div').textContent.trim(); ocultar(); }
            filtrarAlumnosAdmin();
        } else if (e.key==='Escape') ocultar();
    });
    document.addEventListener('click', e => { if (!wrapper.contains(e.target)) ocultar(); });
})();
</script>

<?php 
$pag_prefix = 'ladm'; 
$pag_color = 'violet'; 
$pag_extra_params = ['accion' => 'mostrarListadoAlumnos']; 

include __DIR__ . '/../../Shared/Modal_Paginacion.php'; 
?>

<?php include 'Vista/Admin/Components/Modales_LA.php'; ?>
