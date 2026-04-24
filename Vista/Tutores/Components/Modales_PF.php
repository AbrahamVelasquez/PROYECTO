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

<!-- Modal: Formulario nuevo RA -->
<div id="modalNuevoRA" style="display:none" class="fixed inset-0 bg-black/60 z-[120] flex items-center justify-center p-4" onclick="if(event.target===this) cerrarNuevoRA()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs font-black">+</span>
                Nuevo Resultado de Aprendizaje
            </h3>
            <button onclick="cerrarNuevoRA()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Periodo <span class="text-red-400">*</span></label>
                <select id="nuevoRA_periodo" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                    <option value="">-- Seleccionar --</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nº RA <span class="text-red-400">*</span></label>
                <select id="nuevoRA_numero" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                    <option value="">-- Seleccionar --</option>
                    <?php for($ra = 1; $ra <= 10; $ra++): ?>
                    <option value="RA<?= $ra ?>">RA<?= $ra ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Módulo Profesional <span class="text-red-400">*</span></label>
                <select id="nuevoRA_modulo" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                    <option value="">-- Seleccionar módulo --</option>
                    <?php
                    // Obtenemos los módulos del ciclo del tutor desde plan_estudios
                    try {
                        require_once './Core/Conexion.php';
                        $conn = Conexion::getConexion();
                        $idCicloModal = $_SESSION['id_ciclo'] ?? 0;
                        $stmtMod = $conn->prepare(
                            "SELECT m.id_modulo, m.nombre_modulo 
                             FROM modulos m 
                             INNER JOIN plan_estudios pe ON m.id_modulo = pe.id_modulo 
                             WHERE pe.id_ciclo = :idCiclo 
                             ORDER BY m.nombre_modulo"
                        );
                        $stmtMod->execute(['idCiclo' => $idCicloModal]);
                        $modulosDisponibles = $stmtMod->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($modulosDisponibles as $mod):
                    ?>
                    <option value="<?= $mod['id_modulo'] ?>" data-nombre="<?= htmlspecialchars($mod['nombre_modulo']) ?>">
                        <?= htmlspecialchars($mod['nombre_modulo']) ?>
                    </option>
                    <?php 
                        endforeach;
                    } catch (Exception $e) {
                        // Si falla la BD, el select quedará vacío con el placeholder
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="flex gap-6 mb-6 bg-slate-50 rounded-xl p-3 border border-slate-100">
            <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                <input type="checkbox" id="nuevoRA_empresa" class="accent-orange-600 w-4 h-4 cursor-pointer">
                Impartido íntegramente en la empresa
            </label>
            <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                <input type="checkbox" id="nuevoRA_compartida" class="accent-orange-600 w-4 h-4 cursor-pointer">
                Impartición compartida con el centro docente
            </label>
        </div>

        <div class="flex gap-3 justify-end">
            <button onclick="cerrarNuevoRA()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button onclick="confirmarNuevoRA()" class="px-6 py-2.5 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                Añadir fila
            </button>
        </div>
    </div>
</div>

<!-- Modal: Advertencia campos vacíos -->
<div id="modalCamposVaciosRA" style="display:none" class="fixed inset-0 bg-black/60 z-[130] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-400 text-white text-xs">⚠️</span>
                Campos incompletos
            </h3>
            <button onclick="document.getElementById('modalCamposVaciosRA').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p id="textosCamposVacios" class="text-xs font-bold text-slate-600 mb-4 text-center leading-relaxed"></p>

        <div class="bg-amber-50 p-3 rounded-lg mb-6 border border-amber-100">
            <p class="text-[10px] text-amber-700 font-medium text-center">
                Puedes añadir la fila igualmente y completarla más tarde.
            </p>
        </div>

        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalCamposVaciosRA').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Volver a rellenar
            </button>
            <button onclick="confirmarAgregarFilaRA()" class="px-5 py-2.5 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                Añadir igualmente
            </button>
        </div>
    </div>
</div>

<!-- Modal: Eliminar fila RA (z alto para quedar sobre el modal de gestión) -->
<div id="modalEliminarFilaRA" style="display:none" class="fixed inset-0 bg-black/60 z-[120] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs font-black">✕</span>
                ELIMINAR FILA
            </h3>
            <button onclick="document.getElementById('modalEliminarFilaRA').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p class="text-xs font-bold text-slate-500 mb-4 text-center uppercase tracking-widest">¿Estás seguro de que quieres eliminar esta fila?</p>

        <div class="bg-red-50 p-3 rounded-lg mb-6 border border-red-100">
            <p class="text-[10px] text-red-700 font-medium text-center">
                Esta acción no se puede deshacer. Se perderán los datos introducidos en la fila.
            </p>
        </div>

        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalEliminarFilaRA').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button id="btnConfirmarEliminarFilaRA" class="px-5 py-2.5 rounded-xl bg-red-500 text-white text-xs font-bold hover:bg-red-600 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                Sí, eliminar
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
var _pendingRAData = null;
// Array con los id_ra de filas eliminadas en el modal (se borrarán de BD al aplicar)
var _raEliminados = [];

function agregarFilaRA() {
    const tbody = document.getElementById('ra-modal-tbody');
    const filasActuales = tbody.querySelectorAll('tr:not(#ra-modal-empty)').length;

    if (filasActuales >= 14) {
        document.getElementById('modalLimiteRA').style.display = 'flex';
        return;
    }

    document.getElementById('nuevoRA_periodo').value = '';
    document.getElementById('nuevoRA_modulo').value = '';
    document.getElementById('nuevoRA_numero').value = '';
    document.getElementById('nuevoRA_empresa').checked = false;
    document.getElementById('nuevoRA_compartida').checked = false;
    document.getElementById('modalNuevoRA').style.display = 'flex';
    setTimeout(function() { document.getElementById('nuevoRA_periodo').focus(); }, 50);
}

function cerrarNuevoRA() {
    document.getElementById('modalNuevoRA').style.display = 'none';
}

function confirmarNuevoRA() {
    const periodo    = document.getElementById('nuevoRA_periodo').value;
    const idModulo   = document.getElementById('nuevoRA_modulo').value;
    const numero     = document.getElementById('nuevoRA_numero').value;
    const empresa    = document.getElementById('nuevoRA_empresa').checked;
    const compartida = document.getElementById('nuevoRA_compartida').checked;

    // Obtenemos el nombre visible del módulo seleccionado
    const selectMod  = document.getElementById('nuevoRA_modulo');
    const nombreModulo = selectMod.options[selectMod.selectedIndex]?.getAttribute('data-nombre') ?? '';

    _pendingRAData = { periodo, idModulo, nombreModulo, numero, empresa, compartida };

    const vacios = [];
    if (!periodo)  vacios.push('Periodo');
    if (!idModulo) vacios.push('Módulo Profesional');
    if (!numero)   vacios.push('Nº RA');

    if (vacios.length > 0) {
        const texto = 'Los siguientes campos están vacíos: <span class="text-amber-700 font-black">' + vacios.join(', ') + '</span>.';
        document.getElementById('textosCamposVacios').innerHTML = texto;
        document.getElementById('modalCamposVaciosRA').style.display = 'flex';
        return;
    }

    confirmarAgregarFilaRA();
}

function confirmarAgregarFilaRA() {
    document.getElementById('modalCamposVaciosRA').style.display = 'none';
    document.getElementById('modalNuevoRA').style.display = 'none';

    if (!_pendingRAData) return;
    const { periodo, idModulo, nombreModulo, numero, empresa, compartida } = _pendingRAData;
    _pendingRAData = null;

    const tbody = document.getElementById('ra-modal-tbody');
    const emptyRow = document.getElementById('ra-modal-empty');
    if (emptyRow) emptyRow.style.display = 'none';

    const fila = document.createElement('tr');
    fila.className = 'divide-slate-100';
    // data-id-ra="0" indica fila nueva (sin id en BD todavía)
    fila.setAttribute('data-id-ra', '0');
    fila.setAttribute('data-id-modulo', idModulo);
    fila.innerHTML =
        '<td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">' + _esc(periodo) + '<input type="hidden" value="' + _esc(periodo) + '"></td>' +
        '<td class="p-2 border-r border-slate-200 text-xs font-bold text-slate-600">' + _esc(nombreModulo) + '</td>' +
        '<td class="p-2 border-r border-slate-200 text-xs font-mono font-bold text-slate-500 text-center">' + _esc(idModulo) + '</td>' +
        '<td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">' + _esc(numero) + '</td>' +
        '<td class="p-2 border-r border-slate-200 text-center"><input type="checkbox" ' + (empresa ? 'checked' : '') + ' class="accent-orange-600 w-4 h-4 cursor-pointer"></td>' +
        '<td class="p-2 border-r border-slate-200 text-center"><input type="checkbox" ' + (compartida ? 'checked' : '') + ' class="accent-orange-600 w-4 h-4 cursor-pointer"></td>' +
        '<td class="p-2 text-center"><button type="button" onclick="eliminarFilaRA(this)" class="text-slate-300 hover:text-red-500 transition-colors font-black text-base leading-none cursor-pointer" title="Eliminar fila">×</button></td>';

    tbody.appendChild(fila);
    actualizarBotonAgregarRA();
}

function _esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function eliminarFilaRA(btn) {
    const modal = document.getElementById('modalEliminarFilaRA');
    const btnConfirmar = document.getElementById('btnConfirmarEliminarFilaRA');
    if (modal && btnConfirmar) {
        modal.style.display = 'flex';
        btnConfirmar.onclick = function() {
            const fila = btn.closest('tr');
            const idRa = parseInt(fila.getAttribute('data-id-ra') || '0');
            // Si ya existe en BD (id > 0), lo marcamos para borrar al aplicar
            if (idRa > 0) {
                _raEliminados.push(idRa);
            }
            fila.remove();
            modal.style.display = 'none';
            const tbody = document.getElementById('ra-modal-tbody');
            if (tbody.querySelectorAll('tr:not(#ra-modal-empty)').length === 0) {
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

async function aplicarRAsAlPF() {
    const modalTbody = document.getElementById('ra-modal-tbody');
    const filas = modalTbody.querySelectorAll('tr:not(#ra-modal-empty)');

    // Recopilar datos de las filas actuales para enviar a BD
    const rasNuevos = [];
    filas.forEach(function(fila) {
        const idRa    = parseInt(fila.getAttribute('data-id-ra') || '0');
        const idMod   = fila.getAttribute('data-id-modulo') || '';
        const celdas  = fila.querySelectorAll('td');
        const periodo = celdas[0]?.querySelector('input[type="hidden"]')?.value
                        ?? celdas[0]?.textContent?.trim() ?? '';
        const numero  = celdas[3]?.textContent?.trim() ?? '';
        const empresa = celdas[4]?.querySelector('input[type="checkbox"]')?.checked ? 1 : 0;

        if (idMod && periodo && numero) {
            rasNuevos.push({ id_ra: idRa, id_modulo: idMod, periodo: periodo, numero_ra: numero, impartido_empresa: empresa });
        }
    });

    // Construir payload
    const formData = new URLSearchParams();
    formData.append('ra_nuevos', JSON.stringify(rasNuevos));
    formData.append('ra_eliminar', JSON.stringify(_raEliminados));

    try {
        const res = await fetch('index.php?controlador=Tutores&accion=guardarRA', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        });
        const texto = await res.text();
        const inicio = texto.indexOf('{');
        const data = inicio !== -1 ? JSON.parse(texto.substring(inicio, texto.lastIndexOf('}') + 1)) : {};

        if (!data.success) {
            console.error('Error guardando RAs:', data.error ?? 'desconocido');
        }
    } catch(e) {
        console.error('Error AJAX guardarRA:', e);
    }

    // Resetear array de eliminados
    _raEliminados = [];

    // Reflejar en la tabla de PF_Edicion (si está visible)
    const pfTbody = document.getElementById('modulos-tbody-pf');
    if (pfTbody) {
        const inputs = pfTbody.querySelectorAll('tr');
        filas.forEach(function(fila, idx) {
            if (idx >= inputs.length) return;
            const celdas  = fila.querySelectorAll('td');
            const pfFila  = inputs[idx];
            const pfInps  = pfFila.querySelectorAll('input');
            const periodo = celdas[0]?.querySelector('input[type="hidden"]')?.value
                            ?? celdas[0]?.textContent?.trim() ?? '';
            if (pfInps[0]) pfInps[0].value = periodo;
            if (pfInps[1]) pfInps[1].value = celdas[1]?.textContent?.trim() ?? '';
            if (pfInps[2]) pfInps[2].value = celdas[2]?.textContent?.trim() ?? '';
            if (pfInps[3]) pfInps[3].value = celdas[3]?.textContent?.trim() ?? '';
            if (pfInps[4]) pfInps[4].checked = celdas[4]?.querySelector('input[type="checkbox"]')?.checked ?? false;
            if (pfInps[5]) pfInps[5].checked = celdas[5]?.querySelector('input[type="checkbox"]')?.checked ?? false;
        });

        for (let i = filas.length; i < inputs.length; i++) {
            const pfFila = inputs[i];
            pfFila.querySelectorAll('input[type="text"]').forEach(function(inp) { inp.value = ''; });
            pfFila.querySelectorAll('input[type="checkbox"]').forEach(function(inp) { inp.checked = false; });
        }
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

window.abrirModalGestionarRA = async function() {
    // Resetear eliminados cada vez que se abre
    _raEliminados = [];

    // Limpiar tabla del modal
    const tbody = document.getElementById('ra-modal-tbody');
    const emptyRow = document.getElementById('ra-modal-empty');
    tbody.querySelectorAll('tr:not(#ra-modal-empty)').forEach(tr => tr.remove());
    if (emptyRow) emptyRow.style.display = '';

    // Cargar RAs existentes desde BD
    try {
        const res = await fetch('index.php?controlador=Tutores&accion=obtenerRAs');
        const texto = await res.text();
        const inicio = texto.indexOf('[');
        if (inicio !== -1) {
            const ras = JSON.parse(texto.substring(inicio, texto.lastIndexOf(']') + 1));
            ras.forEach(function(ra) {
                if (emptyRow) emptyRow.style.display = 'none';
                const fila = document.createElement('tr');
                fila.className = 'divide-slate-100';
                fila.setAttribute('data-id-ra', ra.id_ra);
                fila.setAttribute('data-id-modulo', ra.id_modulo);
                fila.innerHTML =
                    '<td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">' + _esc(ra.periodo) + '<input type="hidden" value="' + _esc(ra.periodo) + '"></td>' +
                    '<td class="p-2 border-r border-slate-200 text-xs font-bold text-slate-600">' + _esc(ra.nombre_modulo) + '</td>' +
                    '<td class="p-2 border-r border-slate-200 text-xs font-mono font-bold text-slate-500 text-center">' + _esc(ra.id_modulo) + '</td>' +
                    '<td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">RA' + _esc(ra.numero_ra) + '</td>' +
                    '<td class="p-2 border-r border-slate-200 text-center"><input type="checkbox" ' + (ra.impartido_empresa ? 'checked' : '') + ' class="accent-orange-600 w-4 h-4 cursor-pointer"></td>' +
                    '<td class="p-2 border-r border-slate-200 text-center"><input type="checkbox" class="accent-orange-600 w-4 h-4 cursor-pointer"></td>' +
                    '<td class="p-2 text-center"><button type="button" onclick="eliminarFilaRA(this)" class="text-slate-300 hover:text-red-500 transition-colors font-black text-base leading-none cursor-pointer" title="Eliminar fila">×</button></td>';
                tbody.appendChild(fila);
            });
            actualizarBotonAgregarRA();
        }
    } catch(e) {
        console.error('Error cargando RAs:', e);
    }

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