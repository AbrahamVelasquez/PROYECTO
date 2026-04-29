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
                        <?php if (empty($rasExistentes)): ?>
                            <tr id="ra-modal-empty">
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs font-bold italic">
                                    Pulsa el <span class="text-orange-500 font-black not-italic">+</span> para añadir un resultado de aprendizaje
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rasExistentes as $ra): ?>
                                <tr class="divide-slate-100 border-b border-slate-50 hover:bg-slate-50 transition-colors" 
                                    data-id-ra="<?= $ra['id_ra'] ?>" 
                                    data-id-modulo="<?= $ra['id_modulo'] ?>">
                                    
                                    <td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">
                                        <?= htmlspecialchars($ra['periodo']) ?>
                                        <input type="hidden" class="val-periodo" value="<?= htmlspecialchars($ra['periodo']) ?>">
                                    </td>
                                    
                                    <td class="p-2 border-r border-slate-200 text-xs font-bold text-slate-600">
                                        <?= htmlspecialchars($ra['nombre_modulo']) ?>
                                    </td>
                                    
                                    <td class="p-2 border-r border-slate-200 text-xs font-mono font-bold text-slate-500 text-center">
                                        <?= htmlspecialchars($ra['id_modulo']) ?>
                                    </td>
                                    
                                    <td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">
                                        RA<?= htmlspecialchars($ra['numero_ra']) ?>
                                        <input type="hidden" class="val-numero" value="<?= htmlspecialchars($ra['numero_ra']) ?>">
                                    </td>
                                    
                                    <td class="p-2 border-r border-slate-200 text-center">
                                        <input type="checkbox" <?= $ra['impartido_empresa'] == 1 ? 'checked' : '' ?> class="check-empresa accent-orange-600 w-4 h-4 cursor-pointer">
                                    </td>
                                    
                                    <td class="p-2 border-r border-slate-200 text-center">
                                        <input type="checkbox" <?= $ra['impartido_empresa'] == 0 ? 'checked' : '' ?> class="check-centro accent-orange-600 w-4 h-4 cursor-pointer">
                                    </td>
                                    
                                    <td class="p-2 text-center">
                                        <button type="button" onclick="eliminarFilaRA(this)" class="text-slate-300 hover:text-red-500 transition-colors font-black text-base cursor-pointer">×</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr id="ra-modal-empty" style="display:none">
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs font-bold italic">
                                    Pulsa el <span class="text-orange-500 font-black not-italic">+</span> para añadir un resultado de aprendizaje
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center">
                <button type="button" id="btn-agregar-ra-modal" 
                        onclick="document.getElementById('modalNuevoRA').style.display='flex'" 
                        class="group flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-dashed border-slate-200 text-slate-400 hover:border-orange-400 hover:text-orange-500 hover:bg-orange-50 transition-all font-black text-xs uppercase tracking-widest">
                    <span class="text-xl font-black leading-none">+</span> Añadir nuevo resultado
                </button>
            </div>
        </div>

        <div class="flex gap-3 justify-end p-6 border-t border-slate-100 flex-shrink-0">
            <button onclick="document.getElementById('modalGestionarRA').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button onclick="aplicarRAsAlPF()" class="px-6 py-2.5 rounded-xl bg-slate-700 text-white text-xs font-bold hover:bg-slate-800 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                Aplicar y Guardar Cambios
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

        <div id="exportarTodoSinPendientes" style="display:none" class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex items-center gap-2">
            <span class="text-slate-400 text-sm">📋</span>
            <p class="text-[10px] text-slate-500 font-bold leading-relaxed">
                No hay planes pendientes de exportar. Si desea exportar un plan formativo concreto, 
                puede hacerlo de manera <span class="text-slate-700 font-black">individual</span> 
                desde el botón de edición de cada alumno.
            </p>
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

<div id="modalReiniciarEstado" style="display:none" class="fixed inset-0 bg-black/50 z-[110] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
        
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-600 text-white text-xs">🔄</span>
                Reiniciar Estados
            </h3>
            <button onclick="document.getElementById('modalReiniciarEstado').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Planes formativos ya exportados</p>

        <div class="flex justify-between items-center px-4 py-2 bg-slate-50 rounded-t-xl border border-slate-100 text-[9px] font-black text-slate-600 uppercase tracking-tighter">
            <span>Alumno</span>
            <label class="flex items-center gap-2 cursor-pointer hover:text-red-600 transition-colors select-none">
                <span>Marcar todos</span>
                <input type="checkbox" id="checkReiniciarTodos" onclick="toggleTodosReiniciar(this)" class="w-4 h-4 rounded border-slate-300 accent-red-600 cursor-pointer">
            </label>
        </div>

        <div class="max-h-60 overflow-y-auto mb-6 border-x border-b border-slate-100 rounded-b-xl custom-scrollbar">
            <?php
            $alumnosFirmados = $alumnoModelo->listarAlumnosFirmados($_SESSION['id_ciclo']); 
            $hayExportados = false;
            // IMPORTANTE: $listaAsignaciones debe ser la variable que contiene los datos de la tabla principal
            foreach ($alumnosFirmados as $alFir): 
                if ($alFir['exportado'] == 1): 
                    $hayExportados = true;
            ?>
                <div class="flex justify-between items-center px-4 py-3 hover:bg-red-50/50 transition-colors border-b border-slate-50 last:border-0">
                    <span class="text-xs font-bold text-slate-700 uppercase truncate pr-4">
                        <?= htmlspecialchars($alFir['nombre']) . " " .  htmlspecialchars($alFir['apellido1']) ?>
                    </span>
                    <input type="checkbox" name="reiniciar_ids[]" value="<?= $alFir['id_asignacion'] ?>" class="check-reiniciar w-5 h-5 rounded border-slate-300 accent-red-600 cursor-pointer">
                </div>
            <?php endif; endforeach; ?>

            <?php if (!$hayExportados): ?>
                <div class="py-10 text-center text-slate-400 text-xs italic font-medium">No hay planes marcados como exportados.</div>
            <?php endif; ?>
        </div>

        <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 mb-6 flex items-start gap-2">
            <span class="text-amber-500 text-sm">⚠️</span>
            <p class="text-[10px] text-amber-700 font-bold leading-tight">
                Al reiniciar, los planes volverán al estado <span class="text-red-600">NO EXPORTADO</span>. 
                Esto será en caso de que haya la necesidad de volver a editar la estructura conjunta del plan formativo, 
                o simplemente como marcador visual de que se volvió a editar algún plan formativo, y volver a exportarlo.
            </p>
        </div>

        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalReiniciarEstado').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <?php if ($hayExportados): ?>
                <button onclick="ejecutarReiniciarEstados()" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-red-600 shadow-md cursor-pointer transition-all uppercase tracking-wide">
                    Confirmar Reinicio
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="modalErrorSeleccionReiniciar" style="display:none" class="fixed inset-0 bg-black/50 z-[120] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">⚠️</span>
                SIN SELECCIÓN
            </h3>
            <button onclick="document.getElementById('modalErrorSeleccionReiniciar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Ningún alumno seleccionado</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                Debes seleccionar al menos un alumno antes de proceder con el reinicio de estado.
            </p>
        </div>
        <div class="flex justify-center">
            <button onclick="document.getElementById('modalErrorSeleccionReiniciar').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors cursor-pointer uppercase">Entendido</button>
        </div>
    </div>
</div>

<div id="modalConfirmarFinalReiniciar" style="display:none" class="fixed inset-0 bg-black/50 z-[120] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-600 text-white text-xs">🔄</span>
                Confirmar Reinicio
            </h3>
            <button onclick="document.getElementById('modalConfirmarFinalReiniciar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-500 mb-6 text-center uppercase tracking-widest leading-relaxed">
            ¿Seguro que quieres reiniciar el estado de los alumnos seleccionados?
        </p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarFinalReiniciar').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all uppercase">Cancelar</button>
            <button onclick="procederConReinicioFinal()" class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition-all shadow-md cursor-pointer uppercase">Sí, reiniciar</button>
        </div>
    </div>
</div>

<script>
var _pendingRAData = null;
var _raEliminados = [];

// 1. CONSTANTE DE MÓDULOS (Viene del controlador)
const MODULOS_DEL_CICLO = <?php echo isset($modulosCiclo) ? json_encode($modulosCiclo) : '[]'; ?>;

// 2. CARGA INICIAL
document.addEventListener('DOMContentLoaded', function() {
    // Si quieres que se carguen al entrar a la página:
    // cargarRAsExistentesEnTabla(); 
});

// 3. GESTIÓN DEL SEGUNDO MODAL (NUEVO RA)
function cerrarNuevoRA() {
    document.getElementById('modalNuevoRA').style.display = 'none';
}

function confirmarNuevoRA() {
    const periodo    = document.getElementById('nuevoRA_periodo').value;
    const idModulo   = document.getElementById('nuevoRA_modulo').value;
    const numero     = document.getElementById('nuevoRA_numero').value;
    const empresa    = document.getElementById('nuevoRA_empresa').checked;
    const compartida = document.getElementById('nuevoRA_compartida').checked;

    const selectMod  = document.getElementById('nuevoRA_modulo');
    const nombreModulo = selectMod.options[selectMod.selectedIndex]?.getAttribute('data-nombre') ?? '';

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

    _pendingRAData = { periodo, idModulo, nombreModulo, numero, empresa, compartida };
    confirmarAgregarFilaRA();
}

// 4. PASAR DEL SEGUNDO MODAL A LA TABLA DEL PRIMERO
function confirmarAgregarFilaRA() {
    document.getElementById('modalCamposVaciosRA').style.display = 'none';
    document.getElementById('modalNuevoRA').style.display = 'none';

    if (!_pendingRAData) return;
    const data = _pendingRAData;
    _pendingRAData = null;

    // Usamos una función unificada para pintar la fila y no repetir código
    pintarFilaEnTablaModal({
        id_ra: 0, // 0 significa que es nuevo
        id_modulo: data.idModulo,
        nombre_modulo: data.nombreModulo,
        periodo: data.periodo,
        numero_ra: data.numero,
        impartido_empresa: data.empresa ? 1 : 0
    });
    
    actualizarBotonAgregarRA();
}

// 5. FUNCIÓN UNIFICADA PARA PINTAR FILAS (Evita errores de formato)
function pintarFilaEnTablaModal(ra) {
    const tbody = document.getElementById('ra-modal-tbody');
    const emptyRow = document.getElementById('ra-modal-empty');
    if (emptyRow) emptyRow.style.display = 'none';

    const fila = document.createElement('tr');
    fila.className = 'divide-slate-100 border-b border-slate-50';
    fila.setAttribute('data-id-ra', ra.id_ra);
    fila.setAttribute('data-id-modulo', ra.id_modulo);

    // IMPORTANTE: Revisa que ra.periodo, ra.nombre_modulo, etc., 
    // se llamen igual que en tu base de datos (SELECT en el modelo)
    fila.innerHTML = `
        <td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">
            ${_esc(ra.periodo)}
            <input type="hidden" class="val-periodo" value="${_esc(ra.periodo)}">
        </td>
        <td class="p-2 border-r border-slate-200 text-xs font-bold text-slate-600">
            ${_esc(ra.nombre_modulo)}
        </td>
        <td class="p-2 border-r border-slate-200 text-xs font-mono font-bold text-slate-50 text-center">
             <span class="bg-slate-100 px-1 rounded text-slate-500">${_esc(ra.id_modulo)}</span>
        </td>
        <td class="p-2 border-r border-slate-200 text-center text-xs font-bold text-slate-600">
            ${_esc(ra.numero_ra)}
            <input type="hidden" class="val-numero" value="${_esc(ra.numero_ra)}">
        </td>
        <td class="p-2 border-r border-slate-200 text-center">
            <input type="checkbox" ${ra.impartido_empresa == 1 ? 'checked' : ''} class="check-empresa w-4 h-4">
        </td>
        <td class="p-2 border-r border-slate-200 text-center">
            <input type="checkbox" ${ra.impartido_empresa == 0 ? 'checked' : ''} class="check-centro w-4 h-4">
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="eliminarFilaRA(this)" class="text-slate-300 hover:text-red-500 text-lg">×</button>
        </td>
    `;
    tbody.appendChild(fila);
}

// 6. ABRIR EL MODAL PRINCIPAL Y CARGAR DATOS
window.abrirModalGestionarRA = async function() {
    _raEliminados = [];
    const tbody = document.getElementById('ra-modal-tbody');
    const emptyRow = document.getElementById('ra-modal-empty');
    
    // 1. Limpiamos lo que hubiera antes
    tbody.querySelectorAll('tr:not(#ra-modal-empty)').forEach(tr => tr.remove());
    if (emptyRow) {
        emptyRow.style.display = '';
        emptyRow.innerHTML = '<td colspan="7" class="py-8 text-center text-slate-400 text-xs italic font-bold">Cargando datos de la base de datos...</td>';
    }

    try {
        // 2. Pedimos los datos al controlador
        const res = await fetch('index.php?controlador=Tutores&accion=obtenerRAs');
        const data = await res.json(); // Intentamos leer JSON directo

        if (data && data.length > 0) {
            if (emptyRow) emptyRow.style.display = 'none';
            // 3. Pintamos cada RA usando la función que ya tenemos
            data.forEach(ra => pintarFilaEnTablaModal(ra));
        } else {
            if (emptyRow) {
                emptyRow.innerHTML = '<td colspan="7" class="py-8 text-center text-slate-400 text-xs font-bold italic">No hay RAs guardados. Pulsa el <span class="text-orange-500">+</span></td>';
            }
        }
    } catch(e) {
        console.error('Error al traer los RAs:', e);
        if (emptyRow) emptyRow.innerHTML = '<td colspan="7" class="py-8 text-center text-red-400 text-xs">Error de conexión al cargar RAs</td>';
    }

    document.getElementById('modalGestionarRA').style.display = 'flex';
};

// 7. GUARDAR CAMBIOS (APLICAR)
async function aplicarRAsAlPF() {
    const modalTbody = document.getElementById('ra-modal-tbody');
    const filas = modalTbody.querySelectorAll('tr:not(#ra-modal-empty)');
    const rasNuevos = [];

    filas.forEach(fila => {
        const idRa     = fila.getAttribute('data-id-ra');
        const idMod    = fila.getAttribute('data-id-modulo');
        const periodo  = fila.querySelector('.val-periodo')?.value;
        const numero   = fila.querySelector('.val-numero')?.value;
        const empresa  = fila.querySelector('.check-empresa').checked ? 1 : 0;

        if (idMod && periodo && numero) {
            rasNuevos.push({ 
                id_ra: idRa, 
                id_modulo: idMod, 
                periodo: periodo, 
                numero_ra: numero, 
                impartido_empresa: empresa 
            });
        }
    });

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

        if (data.success) {
            _raEliminados = [];
            document.getElementById('modalGestionarRA').style.display = 'none';
            // Opcional: Refrescar la página para ver cambios
            location.reload();
        } else {
            alert('Error al guardar: ' + (data.error || 'Desconocido'));
        }
    } catch(e) {
        console.error('Error AJAX guardarRA:', e);
    }
}

// 8. ELIMINAR FILA
function eliminarFilaRA(btn) {
    const modal = document.getElementById('modalEliminarFilaRA');
    const btnConfirmar = document.getElementById('btnConfirmarEliminarFilaRA');
    
    if (modal && btnConfirmar) {
        modal.style.display = 'flex';
        btnConfirmar.onclick = function() {
            const fila = btn.closest('tr');
            const idRa = fila.getAttribute('data-id-ra');
            
            if (idRa && idRa !== '0') {
                _raEliminados.push(parseInt(idRa));
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

// 9. UTILIDADES
function actualizarBotonAgregarRA() {
    const filas = document.querySelectorAll('#ra-modal-tbody tr:not(#ra-modal-empty)').length;
    const btn = document.getElementById('btn-agregar-ra-modal');
    if (!btn) return;
    btn.disabled = (filas >= 14);
    btn.classList.toggle('opacity-50', filas >= 14);
    btn.classList.toggle('cursor-not-allowed', filas >= 14);
}

function _esc(str) {
    if (!str) return "";
    return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// El resto de funciones (Devolver, Exportar) se mantienen igual que las tenías...

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

window.abrirModalExportarTodo = function() {
    const count = document.querySelectorAll('#tablaCuerpo tr[data-exportado="0"]').length;
    document.getElementById('contadorPendientes').textContent = count;
    document.getElementById('exportarTodoProgreso').style.display = 'none';
    document.getElementById('barraProgreso').style.width = '0%';
    document.getElementById('textoProgreso').textContent = '';
    document.getElementById('btnEjecutarExportarTodo').disabled = false;

    document.getElementById('exportarTodoBotones').style.display       = count > 0 ? 'flex' : 'none';
    document.getElementById('exportarTodoSinPendientes').style.display = count === 0 ? 'flex' : 'none';

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
 
    const idsAsignacion = [];
    let completados = 0;
 
    // 1. Marcar todos en BD primero
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
            const fin    = texto.lastIndexOf('}') + 1;
            const data   = JSON.parse(texto.substring(inicio, fin));
 
            if (data.success) {
                completados++;
                idsAsignacion.push(idAsignacion);
            }
        } catch (e) {
            console.error('Error marcando ID ' + idAsignacion, e);
        }
 
        const pct = Math.round(((filas.indexOf(fila) + 1) / filas.length) * 100);
        document.getElementById('barraProgreso').style.width = pct + '%';
        document.getElementById('textoProgreso').textContent =
            (filas.indexOf(fila) + 1) + ' de ' + filas.length + ' procesados';
    }
 
    // 2. Generar Excel(s) — uno o ZIP según cantidad
    if (idsAsignacion.length > 0) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controlador=Tutores&accion=exportarTodoPF';
        form.style.display = 'none';
 
        idsAsignacion.forEach(id => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'ids_asignacion[]';
            input.value = id;
            form.appendChild(input);
        });
 
        document.body.appendChild(form);
        form.submit();
        setTimeout(() => {
            if (document.body.contains(form)) document.body.removeChild(form);
        }, 3000);
    }
 
    // 3. Redirigir al listado tras un breve delay
    setTimeout(() => {
        window.location.href = 'index.php?controlador=Tutores&accion=mostrarPanel&tab=3';
    }, 1500);
};

// Abre el modal de reinicio
function abrirModalReiniciarEstados() {
    document.getElementById('modalReiniciarEstado').style.display = 'flex';
}

// Checkbox de "Seleccionar todos"
function toggleTodosReiniciar(source) {
    const checkboxes = document.querySelectorAll('.check-reiniciar');
    checkboxes.forEach(cb => cb.checked = source.checked);
}

// Ejecuta la llamada al servidor para cambiar de 1 a 0
// Variable global temporal para guardar los IDs seleccionados
let _idsParaReiniciar = [];

function ejecutarReiniciarEstados() {
    // 1. Obtener los IDs seleccionados
    const checkboxes = document.querySelectorAll('.check-reiniciar:checked');
    _idsParaReiniciar = Array.from(checkboxes).map(cb => cb.value);

    // 2. Validar si hay selección
    if (_idsParaReiniciar.length === 0) {
        document.getElementById('modalErrorSeleccionReiniciar').style.display = 'flex';
        return;
    }

    // 3. Mostrar confirmación final
    document.getElementById('modalConfirmarFinalReiniciar').style.display = 'flex';
}

async function procederConReinicioFinal() {
    // Cerramos los modales de confirmación
    document.getElementById('modalConfirmarFinalReiniciar').style.display = 'none';
    document.getElementById('modalReiniciarEstado').style.display = 'none';

    // Mostramos un aviso de carga (opcional, puedes usar el estilo de exportar todo)
    console.log("Reiniciando IDs:", _idsParaReiniciar);

    const formData = new URLSearchParams();
    formData.append('ids_asignacion', JSON.stringify(_idsParaReiniciar));
    formData.append('nuevo_estado', 0);

    try {
        const res = await fetch('index.php?controlador=Tutores&accion=cambiarEstadoExportacion', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        });

        const data = await res.json();

        if (data.success) {
            // Recarga limpia a la pestaña correspondiente
            window.location.href = 'index.php?controlador=Tutores&accion=mostrarPanel&tab=3';
        } else {
            alert("Error al procesar: " + (data.error || "Desconocido"));
        }
    } catch (e) {
        console.error("Error Ajax:", e);
    }
}

</script>