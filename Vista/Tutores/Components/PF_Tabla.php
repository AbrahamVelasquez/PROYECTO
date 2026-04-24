<?php

// Vista/Tutores/Components/PF_Tabla.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

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
    </div>
</div>

<form id="formFiltros" class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center">
    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" id="busqueda" 
            placeholder="BUSCAR POR NOMBRE O EMPRESA..." 
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Ordenar por:</span>
        <select id="ordenar" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="alumno">ALUMNO</option>
            <option value="empresa">EMPRESA</option>
            <option value="estado">ESTADO</option>
        </select>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado:</span>
        <select id="filtroEstado" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="todos">TODOS</option>
            <option value="exportado">🟢 EXPORTADO</option>
            <option value="no exportado">🔴 NO EXPORTADO</option>
        </select>
    </div>

    <button type="button" id="btnBuscar" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-[10px] hover:bg-slate-800 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
        BUSCADO AUTOMÁTICO
    </button>
</form>

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
                foreach ($alumnosFirmados as $al): 
                    $nombreFull = $al['apellido1'] . ( $al['apellido2'] ? " {$al['apellido2']}" : "" ) . ", " . $al['nombre'];
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors"
                    data-id-asignacion="<?= intval($al['id_asignacion']) ?>"
                    data-exportado="<?= $al['exportado'] ? '1' : '0' ?>"
                    data-id-convenio="<?= intval($al['id_convenio'] ?? 0) ?>"
                    data-nombre-empresa="<?= htmlspecialchars($al['nombre_empresa'] ?? '') ?>"
                    data-cif="<?= htmlspecialchars($al['nif_empresa'] ?? '') ?>"
                    data-anexo="<?= intval($al['anexo'] ?? 0) ?>"
                    data-anio-inicio="<?= intval($al['anio_inicio'] ?? 0) ?>"
                    data-anio-fin="<?= intval($al['anio_fin'] ?? 0) ?>"
                    data-id-curso="<?= intval($al['id_curso'] ?? 0) ?>">
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
                                <?= intval($al['id_convenio'] ?? 0) ?>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputBusqueda = document.querySelector('#busqueda');
    const btnBuscar = document.querySelector('#btnBuscar');
    const selectOrdenar = document.querySelector('#ordenar');
    const selectEstado = document.querySelector('#filtroEstado'); // Selector de estado añadido
    const tablaCuerpo = document.querySelector('#tablaCuerpo');

    // --- FUNCIÓN BUSCAR Y FILTRAR ---
    const realizarBusqueda = () => {
        const texto = inputBusqueda.value.toLowerCase().trim();
        const estadoFiltro = selectEstado.value; // Obtener el valor del filtro (todos, exportado, no exportado)
        const filas = Array.from(tablaCuerpo.querySelectorAll('tr'));

        filas.forEach(fila => {
            // Saltamos la fila si es la de "No hay alumnos" (que suele tener un colspan)
            if (fila.cells.length < 4) return;

            const nombreAlumno = fila.children[1].textContent.toLowerCase();
            const nombreEmpresa = fila.children[2].textContent.toLowerCase();
            
            // Localizamos el span del estado y extraemos el valor del data-attribute
            const statusSpan = fila.querySelector('.status-tag');
            const estadoActual = statusSpan ? statusSpan.getAttribute('data-estado') : '';

            // Lógica combinada: debe coincidir el texto Y el estado
            const coincideTexto = texto === '' || nombreAlumno.includes(texto) || nombreEmpresa.includes(texto);
            const coincideEstado = estadoFiltro === 'todos' || estadoActual === estadoFiltro;

            if (coincideTexto && coincideEstado) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    };

    // --- FUNCIÓN ORDENAR ---
    const ordenarTabla = () => {
        const criterio = selectOrdenar.value;
        const filas = Array.from(tablaCuerpo.querySelectorAll('tr:not(.no-data)')); // Evitar ordenar fila de vacíos
        
        if (filas.length === 0) return;

        // Mapeo de columna según el select
        const indiceColumna = {
            'alumno': 1,
            'empresa': 2,
            'estado': 3
        }[criterio] || 1;

        filas.sort((a, b) => {
            const valA = a.children[indiceColumna].textContent.trim();
            const valB = b.children[indiceColumna].textContent.trim();
            return valA.localeCompare(valB, 'es', { sensitivity: 'base' });
        });

        // Reinyectar las filas ordenadas
        filas.forEach(fila => tablaCuerpo.appendChild(fila));
    };

    // --- EVENTOS ---
    
    // Botón Buscar
    btnBuscar.addEventListener('click', realizarBusqueda);
    
    // Búsqueda en tiempo real
    inputBusqueda.addEventListener('input', realizarBusqueda);

    // Cambio de Filtro de Estado
    selectEstado.addEventListener('change', realizarBusqueda);

    // Cambio de Orden
    selectOrdenar.addEventListener('change', ordenarTabla);
    
    // Manejo de tecla Enter
    inputBusqueda.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            realizarBusqueda();
        }
    });
});
</script>