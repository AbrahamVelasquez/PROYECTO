<?php

// Vista/Tutores/Components/Modales_PF.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

?>
<div id="modalGestionarRA" style="display:none" class="fixed inset-0 bg-black/50 z-[110] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl border border-slate-100 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-6 border-b border-slate-100 flex-shrink-0">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-700 text-white text-xs">📋</span>
                Resultados de Aprendizaje
            </h3>
            <button onclick="document.getElementById('modalGestionarRA').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">
                Estos RAs son comunes a todos los alumnos. Máximo <span class="text-slate-700">14 filas</span>.
            </p>

            <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm mb-4">
                <table class="w-full text-left border-collapse bg-white">
                    <thead class="bg-slate-50">
                        <tr class="text-slate-600 text-[9px] font-black uppercase tracking-wider">
                            <th class="p-3 border-r border-slate-200 w-16 text-center">Periodo</th>
                            <th class="p-3 border-r border-slate-200">Módulo Profesional</th>
                            <th class="p-3 border-r border-slate-200 w-20 text-center">Código</th>
                            <th class="p-3 border-r border-slate-200 w-32 text-center">Resultados de Aprendizaje</th>
                            <th class="p-3 border-r border-slate-200 w-36 text-center leading-tight">Impartido íntegramente en la empresa</th>
                            <th class="p-3 border-r border-slate-200 w-36 text-center leading-tight">Impartición compartida con el centro docente</th>
                            <th class="p-3 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="ra-modal-tbody" class="divide-y divide-slate-100">
                        <tr id="ra-modal-empty">
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs font-bold italic">
                                Pulsa el <span class="text-orange-500 font-black not-italic">+</span> para añadir un resultado de aprendizaje
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center">
                <button type="button" id="btn-agregar-ra-modal" onclick="agregarFilaRA()" class="group flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-dashed border-slate-200 text-slate-400 hover:border-orange-400 hover:text-orange-500 hover:bg-orange-50 transition-all font-black text-xs uppercase tracking-widest">
                    <span class="text-xl font-black leading-none">+</span> Añadir resultado de aprendizaje
                </button>
            </div>
        </div>

        <div class="flex gap-3 justify-end p-6 border-t border-slate-100 flex-shrink-0">
            <button onclick="document.getElementById('modalGestionarRA').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button onclick="aplicarRAsAlPF()" class="px-6 py-2.5 rounded-xl bg-slate-700 text-white text-xs font-bold hover:bg-slate-800 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                Aplicar a todos los alumnos
            </button>
        </div>
    </div>
</div>

<div id="modalConfirmarDevolver" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-white text-xs">🔄</span>
                DEVOLVER ALUMNO
            </h3>
            <button onclick="document.getElementById('modalConfirmarDevolver').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        
        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Confirmar devolución de?</p>
        <p id="nombreAlumnoDevolver" class="text-sm font-black text-slate-900 mb-4 text-center uppercase"></p>
        
        <div class="bg-amber-50 p-3 rounded-lg mb-6">
            <p class="text-[10px] text-amber-700 font-medium text-center">
                * Caso excepcional: El alumno será liberado para ser asignado a otra empresa.
            </p>
        </div>

        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarDevolver').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                Cancelar
            </button>
            <button id="btnEjecutarDevolucion" class="px-5 py-2.5 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-700 cursor-pointer">
                Sí, devolver
            </button>
        </div>
    </div>
</div>

<div id="modalConfirmarExportarPF" style="display:none" class="fixed inset-0 bg-black/50 z-[110] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">📄</span>
                Exportar Plan
            </h3>
            <button onclick="document.getElementById('modalConfirmarExportarPF').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        
        <p class="text-xs font-bold text-slate-500 mb-4 text-center uppercase tracking-widest leading-relaxed">
            ¿Confirmar la generación del plan?
        </p>

        <div class="bg-orange-50 p-3 rounded-lg mb-6 border border-orange-100">
            <p class="text-[10px] text-orange-700 font-medium text-center italic">
                El Plan Formativo será exportado como un documento Excel.
            </p>
        </div>

        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarExportarPF').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button id="btnEjecutarExportacionPF" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                Sí, exportar
            </button>
        </div>
    </div>
</div>

<div id="modalEliminarFila" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs font-black">✕</span>
                ELIMINAR FILA
            </h3>
            <button onclick="document.getElementById('modalEliminarFila').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p class="text-xs font-bold text-slate-500 mb-4 text-center uppercase tracking-widest">¿Estás seguro de que quieres eliminar esta fila?</p>

        <div class="bg-red-50 p-3 rounded-lg mb-6 border border-red-100">
            <p class="text-[10px] text-red-700 font-medium text-center">
                Esta acción no se puede deshacer. Se perderán los datos introducidos en la fila.
            </p>
        </div>

        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalEliminarFila').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button id="btnConfirmarEliminarFila" class="px-5 py-2.5 rounded-xl bg-red-500 text-white text-xs font-bold hover:bg-red-600 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                Sí, eliminar
            </button>
        </div>
    </div>
</div>

<div id="modalExportarTodo" style="display:none" class="fixed inset-0 bg-black/50 z-[110] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">

        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </span>
                Exportar todos los planes
            </h3>
            <button onclick="document.getElementById('modalExportarTodo').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Resumen de la acción</p>

        <div class="bg-slate-50 rounded-xl border border-slate-100 p-4 mb-4 space-y-2">
            <div class="flex items-start gap-3">
                <span class="text-orange-500 mt-0.5">•</span>
                <p class="text-xs font-bold text-slate-600 leading-relaxed">
                    Se marcarán como <span class="text-orange-600">EXPORTADOS</span> todos los planes de formación que aún figuren como pendientes.
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-orange-500 mt-0.5">•</span>
                <p class="text-xs font-bold text-slate-600 leading-relaxed">
                    Planes pendientes encontrados: <span id="contadorPendientes" class="text-slate-900 font-black">—</span>
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-orange-500 mt-0.5">•</span>
                <p class="text-xs font-bold text-slate-600 leading-relaxed">
                    Los planes ya exportados <span class="text-slate-900">no se verán afectados</span>.
                </p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 mb-6 flex items-center gap-2">
            <span class="text-amber-500 text-sm">⚠️</span>
            <p class="text-[10px] text-amber-700 font-bold">
                Esta acción actualizará el estado en la base de datos de forma inmediata.
            </p>
        </div>

        <div id="exportarTodoProgreso" style="display:none" class="mb-4">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 text-center">Exportando...</p>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div id="barraProgreso" class="bg-orange-600 h-2 rounded-full transition-all duration-300" style="width:0%"></div>
            </div>
            <p id="textoProgreso" class="text-[9px] text-slate-400 text-center mt-1 font-bold"></p>
        </div>

        <div id="exportarTodoBotones" class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalExportarTodo').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button id="btnEjecutarExportarTodo" onclick="exportarTodoHandler()" class="px-6 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 shadow-md cursor-pointer transition-all uppercase tracking-wide flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Sí, exportar todos
            </button>
        </div>

    </div>
</div>

<div id="modalLimiteRA" style="display:none" class="fixed inset-0 bg-black/50 z-[110] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs">🚫</span>
                Límite alcanzado
            </h3>
            <button onclick="document.getElementById('modalLimiteRA').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-2 text-center">Máximo de filas completado</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                Has alcanzado el límite máximo de <span class="text-slate-900">14 Resultados de Aprendizaje</span> permitidos en este Plan Formativo.
            </p>
        </div>

        <div class="bg-slate-50 p-3 rounded-lg mb-6 border border-slate-100">
            <p class="text-[10px] text-slate-500 font-medium text-center italic">
                Para añadir un nuevo resultado, primero debes eliminar uno de los existentes.
            </p>
        </div>

        <div class="flex justify-center">
            <button onclick="document.getElementById('modalLimiteRA').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-all cursor-pointer uppercase tracking-widest">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
function agregarFilaRA() {
    const tbody = document.getElementById('ra-modal-tbody');
    const filasActuales = tbody.querySelectorAll('tr:not(#ra-modal-empty)').length;
    const MAX_FILAS = 14;

    if (filasActuales >= MAX_FILAS) {
        const modal = document.getElementById('modalLimiteRA');
        if (modal) modal.style.display = 'flex';
        return;
    }

    const emptyRow = document.getElementById('ra-modal-empty');
    if (emptyRow) emptyRow.style.display = 'none';

    const fila = document.createElement('tr');
    fila.className = 'divide-slate-100';
    fila.innerHTML = `
        <td class="p-2 border-r border-slate-200"><input type="text" placeholder="2" class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 text-center"></td>
        <td class="p-2 border-r border-slate-200"><input type="text" placeholder="Desarrollo web entorno servidor" class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600"></td>
        <td class="p-2 border-r border-slate-200"><input type="text" placeholder="613" class="w-full px-2 py-1 outline-none text-xs font-bold font-mono text-slate-600 text-center"></td>
        <td class="p-2 border-r border-slate-200"><input type="text" placeholder="RA: 7" class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 text-center"></td>
        <td class="p-2 border-r border-slate-200 text-center"><input type="checkbox" class="accent-orange-600 w-4 h-4 cursor-pointer"></td>
        <td class="p-2 border-r border-slate-200 text-center"><input type="checkbox" class="accent-orange-600 w-4 h-4 cursor-pointer"></td>
        <td class="p-2 text-center"><button type="button" onclick="eliminarFilaRA(this)" class="text-slate-300 hover:text-red-500 transition-colors font-black text-base leading-none cursor-pointer" title="Eliminar fila">×</button></td>
    `;

    tbody.appendChild(fila);
    actualizarBotonAgregarRA();
    fila.querySelector('input[type="text"]').focus();
}

function eliminarFilaRA(btn) {
    const modal = document.getElementById('modalEliminarFila');
    const btnConfirmar = document.getElementById('btnConfirmarEliminarFila');
    if (modal && btnConfirmar) {
        modal.style.display = 'flex';
        btnConfirmar.onclick = function() {
            btn.closest('tr').remove();
            modal.style.display = 'none';
            const tbody = document.getElementById('ra-modal-tbody');
            const dataRows = tbody.querySelectorAll('tr:not(#ra-modal-empty)');
            if (dataRows.length === 0) {
                document.getElementById('ra-modal-empty').style.display = '';
            }
            actualizarBotonAgregarRA();
        };
    }
}

function actualizarBotonAgregarRA() {
    const tbody = document.getElementById('ra-modal-tbody');
    const filas = tbody.querySelectorAll('tr:not(#ra-modal-empty)').length;
    const btn = document.getElementById('btn-agregar-ra-modal');
    if (!btn) return;
    if (filas >= 14) {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

function aplicarRAsAlPF() {
    const modalTbody = document.getElementById('ra-modal-tbody');
    const filas = modalTbody.querySelectorAll('tr:not(#ra-modal-empty)');
    const pfTbody = document.getElementById('modulos-tbody-pf');
    if (!pfTbody) {
        document.getElementById('modalGestionarRA').style.display = 'none';
        return;
    }

    const inputs = pfTbody.querySelectorAll('tr');

    filas.forEach(function(fila, idx) {
        if (idx >= inputs.length) return;
        const celdas = fila.querySelectorAll('td');
        const pfFila = inputs[idx];
        const pfInputs = pfFila.querySelectorAll('input');
        if (celdas[0]) pfInputs[0].value = celdas[0].querySelector('input')?.value ?? '';
        if (celdas[1]) pfInputs[1].value = celdas[1].querySelector('input')?.value ?? '';
        if (celdas[2]) pfInputs[2].value = celdas[2].querySelector('input')?.value ?? '';
        if (celdas[3]) pfInputs[3].value = celdas[3].querySelector('input')?.value ?? '';
        if (celdas[4]) pfInputs[4].checked = celdas[4].querySelector('input')?.checked ?? false;
        if (celdas[5]) pfInputs[5].checked = celdas[5].querySelector('input')?.checked ?? false;
    });

    for (let i = filas.length; i < inputs.length; i++) {
        const pfFila = inputs[i];
        pfFila.querySelectorAll('input[type="text"]').forEach(function(inp) { inp.value = ''; });
        pfFila.querySelectorAll('input[type="checkbox"]').forEach(function(inp) { inp.checked = false; });
    }

    document.getElementById('modalGestionarRA').style.display = 'none';
}

function abrirModalDevolver(idAlumno, nombre) {
    const elNombre = document.getElementById('nombreAlumnoDevolver');
    if (elNombre) elNombre.textContent = nombre;
    
    const modal = document.getElementById('modalConfirmarDevolver');
    if (modal) modal.style.display = 'flex';
    
    // CORRECCIÓN: Usamos la variable correcta para evitar el ReferenceError
    const botonEjecutar = document.getElementById('btnEjecutarDevolucion');
    
    if (botonEjecutar) {
        botonEjecutar.onclick = function() {
            // Enviamos los parámetros de forma que el index.php no se pierda
            const url = "index.php?controlador=Tutores&accion=devolverAlumnoAEnvio&id_alumno=" + idAlumno;
            window.location.href = url;
        };
    }
}

function cerrarModalDevolver() {
    document.getElementById('modalConfirmarDevolver').style.display = 'none';
}

window.abrirModalExportarPF = function(idAsignacion) {
    const modal = document.getElementById('modalConfirmarExportarPF');
    const botonEjecutar = document.getElementById('btnEjecutarExportacionPF');

    if (modal && botonEjecutar) {
        modal.style.display = 'flex';

        botonEjecutar.onclick = function() {
            modal.style.display = 'none';
            if(typeof window.exportarYMarcar === 'function') {
                window.exportarYMarcar(idAsignacion);
            }
        };
    }
};

window.abrirModalGestionarRA = function() {
    document.getElementById('modalGestionarRA').style.display = 'flex';
};

window.abrirModalExportarTodo = function() {
    const filas = document.querySelectorAll('#tablaCuerpo tr[data-exportado="0"]');
    document.getElementById('contadorPendientes').textContent = filas.length;
    document.getElementById('exportarTodoProgreso').style.display = 'none';
    document.getElementById('exportarTodoBotones').style.display = 'flex';
    document.getElementById('barraProgreso').style.width = '0%';
    document.getElementById('textoProgreso').textContent = '';
    document.getElementById('btnEjecutarExportarTodo').disabled = false;
    document.getElementById('modalExportarTodo').style.display = 'flex';
};

window.exportarTodoHandler = async function() {
    const filas = Array.from(document.querySelectorAll('#tablaCuerpo tr[data-exportado="0"]'));

    if (filas.length === 0) {
        document.getElementById('modalExportarTodo').style.display = 'none';
        return;
    }

    // Bloquear botones y mostrar progreso
    document.getElementById('exportarTodoBotones').style.display = 'none';
    document.getElementById('exportarTodoProgreso').style.display = 'block';
    document.getElementById('btnEjecutarExportarTodo').disabled = true;

    let completados = 0;

    for (const fila of filas) {
        const idAsignacion = fila.getAttribute('data-id-asignacion');

        try {
            const res = await fetch('index.php?controlador=Tutores&accion=marcarComoExportado', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_asignacion=${encodeURIComponent(idAsignacion)}`
            });
            const texto = await res.text();
            const inicio = texto.indexOf('{');
            const fin = texto.lastIndexOf('}') + 1;
            const data = JSON.parse(texto.substring(inicio, fin));

            if (data.success) completados++;
        } catch (e) {
            console.error('Error exportando ID ' + idAsignacion, e);
        }

        const pct = Math.round(((filas.indexOf(fila) + 1) / filas.length) * 100);
        document.getElementById('barraProgreso').style.width = pct + '%';
        document.getElementById('textoProgreso').textContent = (filas.indexOf(fila) + 1) + ' de ' + filas.length + ' procesados';
    }

    window.location.href = 'index.php?controlador=Tutores&accion=mostrarPanel&tab=3';
};

</script>