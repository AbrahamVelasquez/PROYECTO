<?php

/**
 * Vista/Tutores/Components/PF_Tabla.php — Tabla de alumnos del Plan Formativo (paso 3)
 *
 * Lista los alumnos que ya tienen asignación firmada ($alumnosFirmados), mostrando
 * su estado de exportación y las acciones disponibles:
 *   - Editar: abre el editor PF_Edicion.php para ese alumno (añade ?editar=ID a la URL).
 *   - Gestionar RAs: abre el modal Modales_PF.php para asignar resultados de aprendizaje.
 *   - Exportar todos: envía el formulario a Exportar_PF_Todo.php con los IDs de todas
 *     las asignaciones visibles en la página actual.
 *
 * La paginación usa Paginador.php con clave de GET pp_pf/pag_pf.
 * Variables recibidas: $alumnosFirmados (array de asignaciones firmadas del ciclo).
 */

require_once __DIR__ . '/../../../Seguridad/Control_Accesos.php';

validarAcceso('tutor');

require_once __DIR__ . '/../../../Helpers/Paginador.php';

// Paginación PHP
$pp_pf  = leerPorPagina('pp_pf', 10);
$pag_pf = leerPaginaActual('pag_pf');
$total_pf = count($alumnosFirmados ?? []);
$rowsPFPag = paginarArray($alumnosFirmados ?? [], $pp_pf, $pag_pf);

?>
<div class="flex justify-between items-center mb-6 mt-2">
    <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-600 text-white text-sm">📋</span>
        Gestión de Planes de Formación
    </h2>
    <div class="flex gap-3">
        <button type="button" onclick="document.getElementById('modalGestionarRA').style.display='flex'" class="bg-slate-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-800 transition-all shadow-md flex items-center gap-2 cursor-pointer uppercase tracking-wide">
            <span>📋</span> Resultados de Aprendizaje
        </button>
        <button onclick="abrirModalExportarTodo()" class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md flex items-center gap-2 cursor-pointer uppercase tracking-wide">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Exportar Todo
        </button>
        <button onclick="abrirModalReiniciarEstados()" class="group flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition-all font-bold text-xs uppercase tracking-widest shadow-sm">
            <span class="text-lg">🔄</span>
            Reiniciar Estados
        </button>
    </div>
</div>

<form id="formFiltros" class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center">
    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" id="busqueda" 
            placeholder="BUSCAR POR NOMBRE O EMPRESA..." 
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Ordenar por:</span>
        <select id="ordenar" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer">
            <option value="alumno">ALUMNO</option>
            <option value="empresa">EMPRESA</option>
            <option value="estado">ESTADO</option>
        </select>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado:</span>
        <select id="filtroEstado" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer">
            <option value="todos">TODOS</option>
            <option value="exportado">🟢 EXPORTADO</option>
            <option value="no exportado">🔴 NO EXPORTADO</option>
        </select>
    </div>

    <button type="button" id="btnBuscar" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-[10px] hover:bg-slate-800 transition-all shadow-sm uppercase tracking-wider cursor-pointer flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        BUSCAR
    </button>
</form>

<!-- Barra superior: contador + config paginación -->
<div class="flex items-center justify-between mb-2">
    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
        <?php if ($pp_pf > 0 && $total_pf > $pp_pf): ?>
            Mostrando <?= ($pag_pf - 1) * $pp_pf + 1 ?>–<?= min($pag_pf * $pp_pf, $total_pf) ?> de <?= $total_pf ?>
        <?php elseif ($total_pf > 0): ?>
            <?= $total_pf ?> alumno<?= $total_pf !== 1 ? 's' : '' ?>
        <?php endif; ?>
    </span>
    <button type="button" onclick="document.getElementById('modal-pag-pf').style.display='flex'" title="Configurar filas por página"
        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span><?= $pp_pf . '/pág' ?></span>
    </button>
</div>

<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
    <table class="w-full text-left border-collapse bg-white table-fixed">
        <thead>
            <tr class="bg-slate-50 text-slate-600 text-[10px] font-black uppercase tracking-wider">
                <th class="p-4 w-24 text-center">EDITAR</th>
                <th class="p-4 w-1/3">ALUMNO</th>
                <th class="p-4 w-1/3">EMPRESA</th>
                <th class="p-4 w-32 text-center">ESTADO ENVÍO</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 uppercase bg-white text-[10px]" id="tablaCuerpo">
            <?php 
            $alumnosFirmados = $alumnoModelo->listarAlumnosFirmados($_SESSION['id_ciclo']); 
            
            if (empty($alumnosFirmados)): ?>
                <tr>
                <td colspan="4" class="p-12 text-center">
                    <div class="flex flex-col items-center justify-center gap-4">
                    <!-- No lo voy a usar por ahora pero lo guardaré el simbolo de cero con la raya -->
                    <!-- <span class="text-5xl text-slate-300">∅</span> -->
                    
                    <p class="text-slate-600 font-black tracking-widest uppercase text-sm">
                        No hay alumnos con asignaciones firmadas actualmente
                    </p>
                    <p class="text-slate-400 font-bold normal-case text-xs">
                        Los alumnos aparecerán aquí una vez que se complete el proceso de asignación.
                    </p>
                    </div>
                </td>
                </tr>
            <?php else: 
                foreach ($rowsPFPag as $al): 
                    $nombreFull = $al['apellido1'] . ( $al['apellido2'] ? " {$al['apellido2']}" : "" ) . ", " . $al['nombre'];
                ?>
                <tr class="pf-fila hover:bg-slate-50/50 transition-colors"
                    data-id-asignacion="<?= intval($al['id_asignacion']) ?>"
                    data-exportado="<?= $al['exportado'] ? '1' : '0' ?>"
                    data-id-convenio="<?= htmlspecialchars($al['num_convenio'] ?? '') ?>"
                    data-nombre-empresa="<?= htmlspecialchars($al['nombre_empresa'] ?? '') ?>"
                    data-cif="<?= htmlspecialchars($al['nif_empresa'] ?? '') ?>"
                    data-anexo="<?= intval($al['anexo'] ?? 0) ?>"
                    data-anio-inicio="<?= intval($al['anio_inicio'] ?? 0) ?>"
                    data-anio-fin="<?= intval($al['anio_fin'] ?? 0) ?>"
                    data-id-curso="<?= intval($al['id_curso'] ?? 0) ?>"
                    data-horario="<?= htmlspecialchars($al['horario'] ?? '') ?>"
                    data-horas-totales="<?= intval($al['num_total_horas'] ?? 0) ?>"
                    data-fecha-inicio="<?= htmlspecialchars($al['fecha_inicio'] ?? '') ?>"
                    data-fecha-final="<?= htmlspecialchars($al['fecha_final'] ?? '') ?>"
                    data-horario-excepciones="<?= htmlspecialchars($al['horario_excepciones'] ?? '') ?>"
                    data-dias-semana="<?= htmlspecialchars($al['dias_semana'] ?? '') ?>">
                    <td class="p-3 text-center">
                        <button type="button" 
                            onclick="window.mostrarEdicion(
                                <?= $al['id_alumno'] ?>,
                                '<?= addslashes($nombreFull) ?>',
                                '<?= addslashes($al['correo'] ?? '') ?>',
                                '<?= addslashes($al['nombre_empresa'] ?? '') ?>',
                                '<?= addslashes($al['telefono'] ?? '') ?>',
                                '<?= addslashes($al['nif_empresa'] ?? '') ?>',
                                '<?= addslashes($al['email_empresa'] ?? '') ?>',
                                '<?= addslashes($al['telefono_empresa'] ?? '') ?>',
                                '<?= addslashes($al['nombre_ciclo'] ?? '') ?>',
                                '<?= $al['id_curso'] ?>',
                                '<?= $al['id_ciclo'] ?>',
                                '<?= addslashes($nombreTutor ?? '') ?>',
                                '<?= addslashes($correoTutor ?? '') ?>',
                                '<?= addslashes($telTutor ?? '') ?>',
                                '<?= $al['anio_inicio'] ?? '' ?>',
                                '<?= $al['anio_fin'] ?? '' ?>',
                                <?= intval($al['id_asignacion']) ?>,
                                '<?= addslashes($al['nombre_tutor_empresa'] ?? '') ?>',
                                '<?= addslashes($al['correo_tutor_empresa'] ?? '') ?>',
                                '<?= addslashes($al['tel_tutor_empresa'] ?? '') ?>',
                                <?= intval($al['anexo'] ?? 0) ?>,
                                '<?= addslashes($al['num_convenio'] ?? '') ?>',
                                '<?= addslashes($al['horario'] ?? '') ?>',
                                <?= intval($al['num_total_horas'] ?? 0) ?>,
                                '<?= addslashes($al['fecha_inicio'] ?? '') ?>',
                                '<?= addslashes($al['fecha_final'] ?? '') ?>',
                                this.closest('tr')
                            )"
                            class="group p-2 rounded-lg hover:bg-orange-50 transition-all border border-transparent hover:border-orange-100 mx-auto flex items-center justify-center cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 group-hover:text-orange-600">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                            </svg>
                        </button>
                    </td>
                    <td class="p-4 font-bold text-slate-700"><?= htmlspecialchars($nombreFull) ?></td>
                    <td class="p-4 text-slate-600 text-[9px]"><?= htmlspecialchars($al['nombre_empresa']) ?></td>
                    <td class="p-4 text-center">
                        <?php if (isset($al['exportado']) && $al['exportado'] == 1): ?>
                            <span class="px-3 py-1 rounded-full text-[8px] border font-black bg-emerald-100 text-emerald-700 border-emerald-200 uppercase status-tag" data-estado="exportado">
                                🟢 EXPORTADO
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 rounded-full text-[8px] border font-black bg-rose-100 text-rose-700 border-rose-200 uppercase status-tag" data-estado="no exportado">
                                🔴 NO EXPORTADO
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; 
            endif; ?>
        </tbody>
    </table>
</div>

<?= renderizarNavPaginacion($total_pf, $pag_pf, $pp_pf, 'pag_pf', 'orange', ['tab' => '3']) ?>

<script>
function pfOrdenar() {
    const criterio = document.getElementById('ordenar').value;
    const tbody = document.getElementById('tablaCuerpo');
    const filas = Array.from(tbody.querySelectorAll('tr.pf-fila'));
    if (!filas.length) return;
    const col = { 'alumno': 1, 'empresa': 2, 'estado': 3 }[criterio] || 1;
    filas.sort((a, b) => a.children[col].textContent.trim().localeCompare(b.children[col].textContent.trim(), 'es', { sensitivity: 'base' }));
    filas.forEach(f => tbody.appendChild(f));
}

function pfFiltrar() {
    const texto = (document.getElementById('busqueda')?.value || '').toLowerCase().trim();
    const estadoFiltro = document.getElementById('filtroEstado')?.value || 'todos';
    const tbody = document.getElementById('tablaCuerpo');
    const todasFilas = Array.from(tbody.querySelectorAll('tr.pf-fila'));
    todasFilas.forEach(fila => {
        const nombreAlumno  = fila.children[1].textContent.toLowerCase();
        const nombreEmpresa = fila.children[2].textContent.toLowerCase();
        const statusSpan    = fila.querySelector('.status-tag');
        const estadoActual  = statusSpan ? statusSpan.getAttribute('data-estado') : '';
        const visible = (texto === '' || nombreAlumno.includes(texto) || nombreEmpresa.includes(texto))
                     && (estadoFiltro === 'todos' || estadoActual === estadoFiltro);
        fila.style.display = visible ? '' : 'none';
    });
}

function limpiarFiltrosPF() {
    document.getElementById('busqueda').value = '';
    document.getElementById('filtroEstado').value = 'todos';
    document.getElementById('ordenar').value = 'alumno';
    pfFiltrar();
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btnBuscar')?.addEventListener('click', pfFiltrar);
    document.getElementById('busqueda')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); pfFiltrar(); }
    });
    document.getElementById('filtroEstado')?.addEventListener('change', pfFiltrar);
    document.getElementById('busqueda')?.addEventListener('keypress', e => {
        if (e.key === 'Enter') { e.preventDefault(); pfFiltrar(); }
    });
    document.getElementById('ordenar')?.addEventListener('change', () => { pfOrdenar(); pfFiltrar(); });
    pfFiltrar();
});
</script>

<?php 
$pag_prefix = 'pf'; 
$pag_color = 'orange'; 
$pag_extra_params = ['tab' => '3']; 

include __DIR__ . '/../../Shared/Modal_Paginacion.php'; 
?>