<?php
// Vista/Admin/Sections/Listado_Alumnos.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
validarAcceso('admin');
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
        <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="search" id="buscadorAlumnos"
                placeholder="Buscar por nombre o apellidos…"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200"/>
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

        <button type="button" onclick="limpiarFiltrosAdmin()"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-[10px] font-bold text-slate-500 hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer uppercase tracking-wide whitespace-nowrap">
            Mostrar todos
        </button>

    </div>

    <!-- Barra superior: contador + config paginación -->
    <div class="flex items-center justify-between mb-2">
        <span id="ladm-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
        <button type="button" onclick="abrirModalPag('ladm')" title="Configurar filas por página"
            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer uppercase tracking-wide">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span id="ladm-pag-label">10/pág</span>
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
                    <?php foreach ($alumnos as $al):
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
                                <p class="text-[9px] font-bold text-slate-400"><?= htmlspecialchars($al['municipio']) ?></p>
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
                            <?= $tieneHorario ? htmlspecialchars($al['horario']) : '<span class="text-orange-400 font-black italic text-[9px]">⚠️ Sin horario</span>' ?>
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

    <div id="ladm-paginacion" class="hidden flex items-center justify-center mt-1 gap-1.5">
        <button id="ladm-prev" onclick="ladmCambiarPagina(ladmPagActual - 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Anterior
        </button>
        <div id="ladm-paginas" class="flex items-center gap-1.5"></div>
        <button id="ladm-next" onclick="ladmCambiarPagina(ladmPagActual + 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            Siguiente
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>

</div>

<!-- Modal confirmar firma -->
<div id="modalConfirmarFirma" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) cerrarModalFirma()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white text-xs">✍️</span>
                CONFIRMAR FIRMA
            </h3>
            <button onclick="cerrarModalFirma()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Confirmar que este alumno está firmado?</p>
        <p id="modalFirmaNombre" class="text-sm font-black text-slate-900 mb-4 text-center uppercase"></p>

        <div class="mb-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 text-center">
                Número de anexo <span class="text-red-500">*</span>
            </label>
            <input type="text" id="inputFirmaAnexo" placeholder="Ej: 1234"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-center outline-none focus:ring-2 focus:ring-emerald-200 transition-all">
        </div>
        <p id="errorAnexo" style="display:none" class="text-[10px] font-bold text-red-500 text-center mb-4 uppercase tracking-wide">
            ⚠ El número de anexo es obligatorio
        </p>

        <div class="flex gap-3 justify-center mt-4">
            <button onclick="cerrarModalFirma()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button id="btnConfirmarFirmaAccion" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-all shadow-md cursor-pointer">Sí, confirmar</button>
        </div>
    </div>
</div>


<script>
// ─── PAGINACIÓN + FILTROS: LISTADO ALUMNOS ADMIN ─────────────────────────────
let ladmPorPagina = parseInt(localStorage.getItem('pag_ladm_porPagina')) || 10;
let ladmPagActual = 1;
let _ladmFilasVis = [];

function ladmFiltrar() {
    const q       = (document.getElementById('buscadorAlumnos')?.value || '').toLowerCase().trim();
    const ciclo   = (document.getElementById('filtroCiclo')?.value    || '').toLowerCase().trim();
    const empresa = (document.getElementById('filtroEmpresa')?.value  || '').toLowerCase().trim();

    const todas = Array.from(document.querySelectorAll('#tablaAlumnosBody .ladm-fila'));

    const visibles = todas.filter(fila => {
        const texto       = fila.textContent.toLowerCase();
        const filaCiclo   = (fila.dataset.ciclo   || '').toLowerCase();
        const filaEmpresa = (fila.dataset.empresa || '').toLowerCase();
        return (!q || texto.includes(q)) && (!ciclo || filaCiclo === ciclo) && (!empresa || filaEmpresa === empresa);
    });

    todas.forEach(f => f.style.display = 'none');
    _ladmFilasVis = visibles;
    ladmPagActual = 1;
    ladmPagRenderizar();
}

function ladmCambiarPagina(nueva) {
    const totalPaginas = Math.ceil(_ladmFilasVis.length / ladmPorPagina) || 1;
    if (nueva < 1 || nueva > totalPaginas) return;
    ladmPagActual = nueva;
    ladmPagRenderizar();
}

function ladmPagRenderizar() {
    const total        = _ladmFilasVis.length;
    const totalPaginas = Math.ceil(total / ladmPorPagina) || 1;
    const inicio       = (ladmPagActual - 1) * ladmPorPagina;
    const fin          = Math.min(inicio + ladmPorPagina, total);

    _ladmFilasVis.forEach((tr, i) => {
        tr.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const pag     = document.getElementById('ladm-paginacion');
    const contador = document.getElementById('ladm-contador');

    if (total <= ladmPorPagina) {
        pag.classList.add('hidden');
        if (contador) contador.textContent = total > 0 ? `${total} alumno${total !== 1 ? 's' : ''}` : 'Sin resultados';
        return;
    }

    pag.classList.remove('hidden');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('ladm-prev').disabled = ladmPagActual === 1;
    document.getElementById('ladm-next').disabled = ladmPagActual === totalPaginas;

    const contenedor = document.getElementById('ladm-paginas');
    contenedor.innerHTML = '';
    const pagsMostrar = new Set([1, totalPaginas, ladmPagActual, ladmPagActual - 1, ladmPagActual + 1]
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
        btn.onclick = () => ladmCambiarPagina(p);
        btn.className = p === ladmPagActual
            ? 'w-8 h-8 rounded-lg bg-violet-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const label = document.getElementById('ladm-pag-label');
    if (label) label.textContent = ladmPorPagina + '/pág';

    document.getElementById('buscadorAlumnos')?.addEventListener('input', ladmFiltrar);
    document.getElementById('filtroCiclo')?.addEventListener('change', ladmFiltrar);
    document.getElementById('filtroEmpresa')?.addEventListener('change', ladmFiltrar);

    window._filtrarAlumnos = ladmFiltrar;
    ladmFiltrar();
});

function limpiarFiltrosAdmin() {
    document.getElementById('buscadorAlumnos').value = '';
    document.getElementById('filtroCiclo').value = '';
    document.getElementById('filtroEmpresa').value = '';
    ladmFiltrar();
}

// ─── Modal configurar paginación ─────────────────────────────────────────────
window._pagCallbacks = window._pagCallbacks || {};
window._pagCallbacks['ladm'] = function(n) {
    ladmPorPagina = n;
    ladmPagActual = 1;
    ladmPagRenderizar();
};

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

let _firmaIdAsignacion = null;

function abrirModalFirmaAdmin(idAsignacion, nombre) {
    _firmaIdAsignacion = idAsignacion;
    document.getElementById('modalFirmaNombre').innerText = nombre;
    document.getElementById('inputFirmaAnexo').value = '';
    document.getElementById('errorAnexo').style.display = 'none';
    document.getElementById('modalConfirmarFirma').style.display = 'flex';
}

function cerrarModalFirma() {
    document.getElementById('modalConfirmarFirma').style.display = 'none';
    document.getElementById('inputFirmaAnexo').value = '';
    document.getElementById('errorAnexo').style.display = 'none';
    _firmaIdAsignacion = null;
}

document.getElementById('btnConfirmarFirmaAccion').addEventListener('click', function () {
    const anexo = document.getElementById('inputFirmaAnexo').value.trim();
    const error = document.getElementById('errorAnexo');

    if (!anexo) {
        error.style.display = 'block';
        document.getElementById('inputFirmaAnexo').focus();
        return;
    }

    const f = document.createElement('form');
    f.method = 'POST';
    f.action = 'index.php';
    f.innerHTML = `
        <input type="hidden" name="accion"        value="firmarAlumnoAdmin">
        <input type="hidden" name="id_asignacion" value="${_firmaIdAsignacion}">
        <input type="hidden" name="anexo"         value="${anexo}">
    `;
    document.body.appendChild(f);
    f.submit();
});
</script>

<!-- ─── Modal Configurar Paginación: Listado Alumnos Admin ────────────────── -->
<div id="modal-pag-ladm" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('ladm')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('ladm')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('ladm', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-violet-400 hover:bg-violet-50 hover:text-violet-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('ladm', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-violet-400 hover:bg-violet-50 hover:text-violet-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('ladm', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-violet-400 hover:bg-violet-50 hover:text-violet-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('ladm', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-violet-400 hover:bg-violet-50 hover:text-violet-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('ladm', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-violet-400 hover:bg-violet-50 hover:text-violet-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('ladm', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-violet-400 hover:bg-violet-50 hover:text-violet-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-ladm" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-violet-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('ladm')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('ladm')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('ladm')" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-xs font-bold hover:bg-violet-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>