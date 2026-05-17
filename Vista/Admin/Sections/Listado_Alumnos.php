<?php
// Vista/Admin/Sections/Listado_Alumnos.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Helpers/Paginador.php';

validarAcceso('admin');

// Leer filtros de GET
$ladmBusqueda = strtolower(trim($_GET['ladm_busqueda'] ?? ''));
$ladmCiclo    = strtolower(trim($_GET['ladm_ciclo']    ?? ''));
$ladmEmpresa  = strtolower(trim($_GET['ladm_empresa']  ?? ''));

// PHP filter
$rowsLadm = $alumnos ?? [];

if ($ladmBusqueda !== '') {
    $rowsLadm = array_values(array_filter($rowsLadm, function($al) use ($ladmBusqueda) {
        $texto = strtolower(
            ($al['apellido1'] ?? '') . ' ' . ($al['apellido2'] ?? '') . ' ' . ($al['nombre'] ?? '') . ' ' .
            ($al['dni'] ?? '') . ' ' . ($al['nombre_empresa'] ?? '') . ' ' . ($al['nombre_ciclo'] ?? '')
        );
        return str_contains($texto, $ladmBusqueda);
    }));
}
if ($ladmCiclo !== '') {
    $rowsLadm = array_values(array_filter($rowsLadm, function($al) use ($ladmCiclo) {
        $cicloKey = strtolower($al['nombre_ciclo'] . ' ' . ucfirst($al['grado']));
        return $cicloKey === $ladmCiclo;
    }));
}
if ($ladmEmpresa !== '') {
    $rowsLadm = array_values(array_filter($rowsLadm, function($al) use ($ladmEmpresa) {
        return strtolower($al['nombre_empresa']) === $ladmEmpresa;
    }));
}

// Paginación PHP
$pp_ladm  = leerPorPagina('pp_ladm', 10);
$pag_ladm = leerPaginaActual('pag_ladm');
$total_ladm = count($rowsLadm);
$rowsLadmPag = paginarArray($rowsLadm, $pp_ladm, $pag_ladm);

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

    <!-- Filtros (GET form) -->
    <form id="ladm-filter-form" method="GET" action="index.php" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <input type="hidden" name="accion" value="mostrarAlumnos">

        <!-- Buscador -->
        <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="search" id="buscadorAlumnos" name="ladm_busqueda"
                value="<?= htmlspecialchars($_GET['ladm_busqueda'] ?? '') ?>"
                placeholder="Buscar por nombre o apellidos…"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200"
                oninput="clearTimeout(window._ladmT); window._ladmT = setTimeout(()=>document.getElementById('ladm-filter-form').submit(), 400)"/>
        </div>

        <!-- Filtro ciclo -->
        <select id="filtroCiclo" name="ladm_ciclo"
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200"
            onchange="this.closest('form').submit()">
            <option value="">Todos los ciclos</option>
            <?php
            $ciclosVistos = [];
            foreach ($alumnos ?? [] as $al) {
                $key = $al['nombre_ciclo'] . ' ' . ucfirst($al['grado']);
                if (!in_array($key, $ciclosVistos)) {
                    $ciclosVistos[] = $key;
                    $selected = (strtolower($key) === $ladmCiclo) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars(strtolower($key)) . '" ' . $selected . '>' . htmlspecialchars($key) . '</option>';
                }
            }
            ?>
        </select>

        <!-- Filtro empresa -->
        <select id="filtroEmpresa" name="ladm_empresa"
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200"
            onchange="this.closest('form').submit()">
            <option value="">Todas las empresas</option>
            <?php
            $empresasVistas = [];
            foreach ($alumnos ?? [] as $al) {
                $emp = $al['nombre_empresa'];
                if (!in_array($emp, $empresasVistas)) {
                    $empresasVistas[] = $emp;
                    $selected = (strtolower($emp) === $ladmEmpresa) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars(strtolower($emp)) . '" ' . $selected . '>' . htmlspecialchars($emp) . '</option>';
                }
            }
            ?>
        </select>

        <a href="index.php?accion=mostrarAlumnos"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-[10px] font-bold text-slate-500 hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer uppercase tracking-wide whitespace-nowrap">
            Mostrar todos
        </a>
    </form>

    <!-- Barra superior: contador + config paginación -->
    <div class="flex items-center justify-between mb-2">
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
            <?php if ($pp_ladm > 0 && $total_ladm > $pp_ladm): ?>
                Mostrando <?= ($pag_ladm - 1) * $pp_ladm + 1 ?>–<?= min($pag_ladm * $pp_ladm, $total_ladm) ?> de <?= $total_ladm ?>
            <?php elseif ($total_ladm > 0): ?>
                <?= $total_ladm ?> alumno<?= $total_ladm !== 1 ? 's' : '' ?>
            <?php elseif (!empty($alumnos)): ?>
                Sin resultados
            <?php endif; ?>
        </span>
        <button type="button" onclick="document.getElementById('modal-pag-ladm').style.display='flex'" title="Configurar filas por página"
            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 transition-all cursor-pointer uppercase tracking-wide">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span><?= $pp_ladm > 0 ? $pp_ladm . '/pág' : 'Todos' ?></span>
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
            <tbody class="divide-y divide-slate-100 text-[11px] uppercase">

                <?php if (empty($alumnos)): ?>
                    <tr>
                        <td colspan="11" class="py-14 text-center text-slate-400 italic text-sm normal-case">
                            No hay alumnos con FCT firmada registrados.
                        </td>
                    </tr>
                <?php elseif (empty($rowsLadmPag)): ?>
                    <tr>
                        <td colspan="11" class="py-14 text-center text-slate-400 italic text-sm normal-case">
                            No hay alumnos que coincidan con los filtros.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rowsLadmPag as $al):
                        $f_inicio   = (!empty($al['fecha_inicio']) && $al['fecha_inicio'] !== '0000-00-00') ? $al['fecha_inicio'] : null;
                        $f_final    = (!empty($al['fecha_final'])  && $al['fecha_final']  !== '0000-00-00') ? $al['fecha_final']  : null;
                        $tieneHorario = (!empty($al['horario']) && !empty($al['horas_dia']) && $al['horas_dia'] > 0);
                        $cicloLabel   = htmlspecialchars($al['nombre_ciclo'] . ' ' . ucfirst($al['grado']));
                    ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">

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

    <?= renderizarNavPaginacion($total_ladm, $pag_ladm, $pp_ladm, 'pag_ladm', 'violet', ['accion' => 'mostrarAlumnos']) ?>

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

<?php $pag_prefix = 'ladm'; $pag_color = 'violet'; $pag_extra_params = ['accion' => 'mostrarAlumnos']; include $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Vista/Shared/Modal_Paginacion.php'; ?>
