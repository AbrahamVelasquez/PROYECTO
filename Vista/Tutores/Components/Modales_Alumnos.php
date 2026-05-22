<?php

/**
 * Vista/Tutores/Components/Modales_Alumnos.php — Modales del paso 2 (Alumnos)
 *
 * Contiene todos los overlays HTML del wizard de alumnos:
 *   - Cargar alumnos desde Excel (importación masiva).
 *   - Editar datos básicos de un alumno (nombre, apellidos, correo).
 *   - Añadir/cambiar asignación: empresa, fechas, horario, tutor de empresa.
 *   - Captura de firma del alumno (pad canvas con signature_pad.js).
 *   - Confirmación de borrado de alumno.
 *
 * Los modales se abren desde Steps/Alumnos.php con JS que inyecta los datos
 * actuales del alumno seleccionado antes de mostrar el formulario de edición.
 * El modal de firma usa AJAX (fetch) para enviar el PNG al servidor.
 */

require_once __DIR__ . '/../../../Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

?>
<style>
    /* Estilos para el tooltip */
    .help-trigger {
        position: relative;
        display: inline-block;
    }

    .tooltip-box {
        display: none;
        position: absolute;
        bottom: 125%; /* Aparece arriba del signo ? */
        left: 50%;
        transform: translateX(-50%);
        width: 200px;
        background-color: #1e293b; /* slate-800 */
        color: white;
        text-align: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: bold;
        line-height: 1.4;
        text-transform: none;
        z-index: 100;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .tooltip-box::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1e293b transparent transparent transparent;
    }

    .help-trigger:hover .tooltip-box {
        display: block;
    }
</style>

<div id="modalAgregarAlumno" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">👤</span>
                NUEVO ALUMNO
            </h3>
            <button onclick="document.getElementById('modalAgregarAlumno').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
        </div>
        <form method="POST" action="index.php" novalidate onsubmit="return validarForm(this)">
            <input type="hidden" name="accion" value="agregarAlumno">
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Primer Apellido <span class="text-red-500">*</span></label>
                <input type="text" name="apellido1" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Segundo Apellido <span class="text-red-500">*</span></label>
                <input type="text" name="apellido2" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            <div class="flex gap-3 mb-4">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">DNI / NIE</label>
                    <input type="text" name="dni" maxlength="9" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
                <div class="w-28">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sexo</span></label>
                    <select name="sexo" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
                        <option value="">--</option><option value="H">H</option><option value="M">M</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono</label>
                <input type="text" name="telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all" placeholder="Ej: 600123456">
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</label>
                <input type="email" name="correo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            <div class="mb-6">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Curso Académico</label>
                <select name="anio_inicio" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
                    <?php
                    $anioBase = (int)date('Y');
                    for ($i = -1; $i <= 2; $i++):
                        $ini = $anioBase + $i;
                        $fin = $ini + 1;
                        $label = sprintf('%02d-%02d', $ini % 100, $fin % 100);
                    ?>
                    <option value="<?= $ini ?>" <?= $i === 0 ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modalAgregarAlumno').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Guardar Alumno</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditarAlumno" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">✏️</span>
                EDITAR ALUMNO
            </h3>
            <button onclick="document.getElementById('modalEditarAlumno').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
        </div>
        <form method="POST" action="index.php" id="formEditarAlumno" novalidate onsubmit="return validarForm(this)">
            <input type="hidden" name="accion" value="editarAlumno">
            <input type="hidden" name="id_alumno" id="edit_id_alumno">
            
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">Datos del Alumno</p>
            
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Primer Apellido <span class="text-red-500">*</span></label>
                <input type="text" name="apellido1" id="edit_apellido1" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Segundo Apellido <span class="text-red-500">*</span></label>
                <input type="text" name="apellido2" id="edit_apellido2" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" id="edit_nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            
            <div class="flex gap-3 mb-4">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">DNI / NIE</label>
                    <input type="text" name="dni" id="edit_dni" maxlength="9" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
                <div class="w-28">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sexo</label>
                    <select name="sexo" id="edit_sexo" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
                        <option value="">--</option><option value="H">H</option><option value="M">M</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono</label>
                <input type="text" name="telefono" id="edit_telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>

            <div class="mb-6">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</label>
                <input type="email" name="correo" id="edit_correo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">Asignación de Empresa</p>
            
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Convenio / Empresa</label>
                <select name="num_convenio" id="edit_id_convenio" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
                    <option value="">-- Sin asignar --</option>
                    <?php foreach ($misConvenios as $conv): ?>
                       <option value="<?= htmlspecialchars($conv['num_convenio']) ?>">
                            <?= htmlspecialchars($conv['num_convenio']) ?> — <?= htmlspecialchars($conv['nombre_empresa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex gap-3 mb-4">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">F. Inicio</label>
                    <input type="date" name="fecha_inicio" id="edit_fecha_inicio" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">F. Final</label>
                    <input type="date" name="fecha_final" id="edit_fecha_final" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
            </div>
            
            <div class="flex gap-3 mb-3">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Horario</label>
                    <input type="text" name="horario" id="edit_horario" placeholder="08:00-15:00" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
                <div class="w-20">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">H/DÍA</label>
                    <input type="number" name="horas_dia" id="edit_horas_dia" min="0" max="24" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
                <div class="w-24">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">H. TOTAL</label>
                    <input type="number" name="num_total_horas" id="edit_horas_totales" min="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all" placeholder="400">
                </div>
            </div>

            <input type="hidden" name="horario_excepciones" id="edit_horario_excepciones">

            <div class="mb-6">
                <button type="button" onclick="abrirModalHorarioAvanzado()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-[10px] font-black text-slate-600 uppercase tracking-widest hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span id="btn_horario_avanzado_label">Configurar horario avanzado</span>
                </button>
                <div id="resumen_excepciones_editar" class="mt-2 hidden"></div>
            </div>

            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">Tutor de Empresa</p>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre Tutor de Empresa</label>
                <input type="text" name="nombre_tutor_empresa" id="edit_nombre_tutor_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>
            <div class="flex gap-3 mb-6">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Tutor Empresa</label>
                    <input type="email" name="correo_tutor_empresa" id="edit_correo_tutor_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
                <div class="w-40">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Tutor Emp.</label>
                    <input type="text" name="tel_tutor_empresa" id="edit_tel_tutor_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
            </div>

            <div id="bloque_enviado" class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">¿Documentación enviada?</span>
                    <div class="help-trigger relative">
                        <span class="cursor-help flex h-4 w-4 items-center justify-center rounded-full border border-slate-300 text-[10px] text-slate-400 font-bold hover:bg-slate-100 transition-colors">?</span>
                        <div class="tooltip-box">Quitar el check implica que se quiere volver a editar el alumno y volver a enviarlo</div>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enviado" id="edit_enviado" value="1" class="w-5 h-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 accent-orange-600">
                </label>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button"
                        onclick="pedirConfirmacionEliminarAlumno()"
                        class="mr-auto px-5 py-2.5 rounded-xl border border-red-200 bg-red-50 text-red-600 text-xs font-black uppercase hover:bg-red-600 hover:text-white transition-all cursor-pointer flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Eliminar alumno
                </button>
                <button type="button" onclick="document.getElementById('modalEditarAlumno').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Confirmar eliminación de alumno -->
<div id="modalConfirmarEliminarAlumno" style="display:none" class="fixed inset-0 bg-black/50 z-[200] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-red-100 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-red-600"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </div>
        <h3 class="text-base font-black text-slate-900 uppercase mb-2">¿Eliminar alumno?</h3>
        <p class="text-xs font-bold text-slate-500 leading-relaxed mb-1">Esta acción no se puede deshacer.</p>
        <p id="nombreAlumnoEliminar" class="text-sm font-black text-slate-800 uppercase mb-6"></p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarEliminarAlumno').style.display='none'"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                Cancelar
            </button>
            <button onclick="ejecutarEliminarAlumno()"
                    class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-xs font-black uppercase hover:bg-red-700 transition-all shadow-md cursor-pointer">
                Sí, eliminar
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Alumno tiene asignación — debe ponerlo en Sin Asignar primero -->
<div id="modalNoSePuedeEliminar" style="display:none" class="fixed inset-0 bg-black/50 z-[200] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-amber-100 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h3 class="text-base font-black text-slate-900 uppercase mb-2">No se puede eliminar</h3>
        <p class="text-xs font-bold text-slate-500 leading-relaxed mb-6">
            Este alumno tiene una <span class="text-orange-600 font-black">asignación activa</span>.<br>
            Primero retira el convenio asignado (déjalo en <span class="font-black text-slate-700">Sin Asignar</span>) y vuelve a intentarlo.
        </p>
        <button onclick="document.getElementById('modalNoSePuedeEliminar').style.display='none'"
                class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-black uppercase hover:bg-orange-600 transition-all cursor-pointer">
            Entendido
        </button>
    </div>
</div>

<div id="modalSeleccionarExportar" style="display:none" class="fixed inset-0 bg-black/50 z-[90] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">📋</span>
                Seleccionar Alumnos
            </h3>
            <button onclick="document.getElementById('modalSeleccionarExportar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Alumnos con estado "Completado" no enviados</p>
        <div class="flex justify-between items-center px-4 py-2 bg-slate-50 rounded-t-xl border-b border-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-tighter">
            <span>Alumno</span>
            <label class="flex items-center gap-2 cursor-pointer hover:text-slate-700 transition-colors select-none">
                <span>Seleccionar todos</span>
                <input type="checkbox" id="checkSeleccionarTodos" onclick="seleccionarTodosExportar(this)" class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 accent-orange-600 cursor-pointer">
            </label>
        </div>
        <form id="formExportar" method="POST" action="index.php">
            <input type="hidden" name="accion" value="exportarAlumnos">
            <div class="max-h-60 overflow-y-auto mb-6 custom-scrollbar">
                <?php 
                $hayCandidatos = false;
                foreach ($alumnos as $al): 
                    $tieneEmpresa = !empty($al['num_convenio']);
                    $tieneDireccion = !empty($al['direccion']);
                    $tieneFechas = ($al['fecha_inicio'] && $al['fecha_final'] && $al['fecha_inicio'] !== '0000-00-00');
                    $tieneHorario = (!empty($al['horario']) && $al['horas_dia'] > 0);
                    $esCompletado = ($tieneEmpresa && $tieneDireccion && $tieneFechas && $tieneHorario);
                    
                    if ($esCompletado && $al['enviado'] == 0): 
                        $hayCandidatos = true;
                ?>
                    <div class="flex justify-between items-center px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                        <span class="text-xs font-bold text-slate-700 uppercase truncate pr-4">
                            <?= htmlspecialchars($al['apellido1'] . " " . $al['nombre']) ?>
                        </span>
                        <input type="checkbox" name="exportar_ids[]" value="<?= $al['id_alumno'] ?>" class="w-5 h-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 accent-orange-600 cursor-pointer">
                    </div>
                <?php endif; endforeach; ?>
                <?php if (!$hayCandidatos): ?>
                    <div class="py-10 text-center text-slate-400 text-xs italic font-medium">No hay alumnos pendientes de exportar.</div>
                <?php endif; ?>
            </div>
            <div class="flex gap-3 justify-end flex-wrap">
                <button type="button" onclick="document.getElementById('modalSeleccionarExportar').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
                <?php if ($hayCandidatos): ?>
                    <button type="button" onclick="abrirConfirmacionFinal()" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Exportar Selección</button>
                <?php endif; ?>
                <button type="button" onclick="abrirModalExportarTodoAlumnos()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition-all shadow-md cursor-pointer">Exportar Todo</button>
            </div>
        </form>
    </div>
</div>

<div id="modalErrorFirma" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">⚠️</span>
                ACCIÓN BLOQUEADA
            </h3>
            <button onclick="document.getElementById('modalErrorFirma').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Documentación pendiente</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                No se puede firmar la asignación de <span id="nombreAlumnoError" class="text-slate-900"></span> porque la documentación aún no ha sido <span class="text-orange-600">ENVIADA</span>.
            </p>
        </div>
        <div class="flex justify-center">
            <button onclick="document.getElementById('modalErrorFirma').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors cursor-pointer">Entendido</button>
        </div>
    </div>
</div>

<div id="modalErrorExportar" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">⚠️</span>
                ACCIÓN BLOQUEADA
            </h3>
            <button onclick="document.getElementById('modalErrorExportar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Exportación pendiente</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                No se puede marcar manualmente como enviado a <span id="nombreAlumnoExportError" class="text-slate-900"></span>. Para realizar esta acción, debe ir al apartado de <span class="text-orange-600">EXPORTAR ALUMNOS</span>.
            </p>
        </div>
        <div class="flex justify-center">
            <button onclick="document.getElementById('modalErrorExportar').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors cursor-pointer">Entendido</button>
        </div>
    </div>
</div>

<div id="modalYaFirmado" style="display:none" class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">🔒</span>
                YA REGISTRADO
            </h3>
            <button onclick="document.getElementById('modalYaFirmado').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Firma confirmada</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                La asignación de <span id="nombreAlumnoFirmado" class="text-slate-900"></span> ya consta como <span class="text-orange-600">FIRMADA</span> en la base de datos.
            </p>
        </div>
        <div class="flex justify-center">
            <button onclick="document.getElementById('modalYaFirmado').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors cursor-pointer">Entendido</button>
        </div>
    </div>
</div>

<div id="modalConfirmarFirma" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">✍️</span>
                CONFIRMAR FIRMA
            </h3>
            <button onclick="cerrarModalFirma()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        
        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Confirmar que este alumno está firmado?</p>
        <p id="modalFirmaNombre" class="text-sm font-black text-slate-900 mb-4 text-center uppercase"></p>
        
        <div class="mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 text-center">
                ¿Desea introducir el número de anexo? <span class="text-slate-400 font-normal normal-case">(Opcional)</span>
            </label>
            <input type="text" id="inputFirmaAnexo" placeholder="Ej: 1" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-center outline-none focus:ring-2 focus:ring-orange-200 transition-all">
        </div>
        
        <div class="flex gap-3 justify-center">
            <button onclick="cerrarModalFirma()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button id="btnConfirmarFirmaAccion" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Sí, confirmar</button>
        </div>
    </div>
</div>


<div id="modalExportarTodoAlumnos" style="display:none" class="fixed inset-0 bg-black/50 z-[120] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-800 text-white text-xs">📤</span>
                Exportar Todos
            </h3>
            <button onclick="document.getElementById('modalExportarTodoAlumnos').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <div class="bg-slate-50 rounded-xl border border-slate-100 p-4 mb-6 space-y-3">
            <div class="flex items-start gap-3">
                <span class="text-slate-600 mt-0.5">•</span>
                <p class="text-xs font-bold text-slate-600 leading-relaxed">
                    Se exportarán <span class="text-slate-900">todos los alumnos con estado <span class="text-emerald-600">COMPLETADO</span></span>, independientemente de si han sido enviados o firmados.
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-slate-600 mt-0.5">•</span>
                <p class="text-xs font-bold text-slate-600 leading-relaxed">
                    Un alumno está en estado <span class="text-emerald-600 font-black">COMPLETADO</span> cuando tiene empresa asignada, dirección, fechas de inicio y fin, y horario definido.
                </p>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-slate-600 mt-0.5">•</span>
                <p class="text-xs font-bold text-slate-600 leading-relaxed">
                    El archivo descargado incluirá el sufijo <span class="text-slate-900 font-black">- Todos</span> al final del nombre.
                </p>
            </div>
        </div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 text-center">¿Deseas continuar con la exportación completa?</p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalExportarTodoAlumnos').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="ejecutarExportarTodoAlumnos()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition-all shadow-md cursor-pointer">Sí, exportar todo</button>
        </div>
    </div>
</div>

<div id="modalSinCompletadosExportar" style="display:none" class="fixed inset-0 bg-black/50 z-[130] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-amber-100 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <h3 class="text-base font-black text-slate-900 uppercase mb-2">Sin alumnos completados</h3>
        <p class="text-xs font-bold text-slate-500 leading-relaxed mb-6">
            No hay ningún alumno con estado <span class="text-emerald-600 font-black">COMPLETADO</span> para exportar.<br>
            Un alumno está completado cuando tiene empresa, dirección, fechas y horario definidos.
        </p>
        <button onclick="document.getElementById('modalSinCompletadosExportar').style.display='none'"
                class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-black uppercase hover:bg-orange-600 transition-all cursor-pointer">
            Entendido
        </button>
    </div>
</div>

<div id="modalSinSeleccion" style="display:none" class="fixed inset-0 bg-black/50 z-[110] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">⚠️</span>
                SIN SELECCIÓN
            </h3>
            <button onclick="document.getElementById('modalSinSeleccion').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Ningún alumno seleccionado</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                Debes seleccionar al menos un alumno antes de continuar con la exportación.
            </p>
        </div>
        <div class="flex justify-center">
            <button onclick="document.getElementById('modalSinSeleccion').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors cursor-pointer">Entendido</button>
        </div>
    </div>
</div>

<div id="modalConfirmarExportar" style="display:none" class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">📤</span>
                Exportar Alumnos
            </h3>
            <button onclick="document.getElementById('modalConfirmarExportar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-500 mb-6 text-center uppercase tracking-widest leading-relaxed">¿Seguro que quieres exportar los alumnos seleccionados?</p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarExportar').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="exportarAlumnosWord()" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Sí, exportar</button>
        </div>
    </div>
</div>

<!-- ALERTA: DNI Duplicado (pequeño, centrado, sin backdrop) -->
<div id="alertDniDuplicado" style="display:none"
     class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[500] w-80"
     style="filter: drop-shadow(0 25px 50px rgba(0,0,0,0.25))">
    <div class="bg-white border-2 border-amber-300 rounded-2xl shadow-2xl p-6">
        <div class="text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h4 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-2">DNI ya registrado</h4>
            <p class="text-xs font-bold text-slate-600 leading-relaxed mb-1">
                El DNI introducido <span class="text-amber-700 font-black">ya pertenece a otro alumno</span> del sistema.
            </p>
            <p class="text-xs text-slate-500 mb-5">Revisa el número e inténtalo de nuevo.</p>
            <button onclick="cerrarAlertDni()"
                class="w-full py-2.5 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-wide hover:bg-slate-700 transition-all cursor-pointer">
                Entendido
            </button>
        </div>
    </div>
</div>

<!-- ALERTA: Fechas incorrectas (pequeño, centrado, sin backdrop) -->
<div id="alertFechasInvalidas" style="display:none"
     class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[500] w-80">
    <div class="bg-white border-2 border-red-300 rounded-2xl shadow-2xl p-6">
        <div class="text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-red-600"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <h4 class="text-sm font-black text-red-700 uppercase tracking-wide mb-2">Fechas incorrectas</h4>
            <p class="text-xs font-bold text-slate-600 leading-relaxed mb-1">
                La <span class="text-red-600 font-black">fecha de fin</span> de las prácticas
                no puede ser anterior a la <span class="text-red-600 font-black">fecha de inicio</span>.
            </p>
            <p class="text-xs text-slate-500 mb-5">Corrígela antes de guardar los cambios.</p>
            <button onclick="cerrarAlertFechas()"
                class="w-full py-2.5 rounded-xl bg-red-600 text-white text-xs font-black uppercase tracking-wide hover:bg-red-700 transition-all cursor-pointer">
                Corregir fecha
            </button>
        </div>
    </div>
</div>

<div id="modalCargarAlumnos" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-700 text-white text-xs">📥</span>
                CARGAR ALUMNOS
            </h3>
            <button onclick="cerrarModalCargar()" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
        </div>
 
        <!-- Estado: seleccionar fichero -->
        <div id="cargar_estado_inicial">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-4">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Formato esperado</p>
                <p class="text-[10px] font-bold text-slate-400 leading-relaxed">
                    El fichero debe ser <span class="text-slate-700">.xlsx</span> o <span class="text-slate-700">.xls</span> con tres columnas en este orden:
                </p>
                <div class="mt-2 flex gap-2">
                    <span class="px-2 py-1 bg-slate-200 rounded text-[9px] font-black text-slate-600 uppercase">Nombre</span>
                    <span class="px-2 py-1 bg-slate-200 rounded text-[9px] font-black text-slate-600 uppercase">Apellido(s)</span>
                    <span class="px-2 py-1 bg-slate-200 rounded text-[9px] font-black text-slate-600 uppercase">Dirección de correo</span>
                </div>
                <a href="index.php?controlador=Tutores&accion=descargarPlantillaAlumnos" download
                   class="mt-3 inline-flex items-center gap-2 text-[10px] font-black text-slate-600 hover:text-orange-600 transition-colors uppercase tracking-widest">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Descargar Plantilla
                </a>
            </div>
 
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Curso Académico</label>
                <select id="anio_inicio_importar" name="anio_inicio" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
                    <?php
                    $anioBase2 = (int)date('Y');
                    for ($i = -1; $i <= 2; $i++):
                        $ini2 = $anioBase2 + $i;
                        $fin2 = $ini2 + 1;
                        $label2 = sprintf('%02d-%02d', $ini2 % 100, $fin2 % 100);
                    ?>
                    <option value="<?= $ini2 ?>" <?= $i === 0 ? 'selected' : '' ?>><?= $label2 ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <label class="block w-full cursor-pointer">
                <div id="dropZone" class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-orange-300 hover:bg-orange-50/30 transition-all">
                    <p class="text-2xl mb-2">📂</p>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Haz clic para seleccionar</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1" id="nombreFicheroSeleccionado">Ningún fichero seleccionado</p>
                </div>
                <input type="file" id="inputFicheroAlumnos" accept=".xlsx,.xls" class="hidden" onchange="onFicheroSeleccionado(this)">
            </label>

            <div class="flex gap-3 justify-end mt-6">
                <button type="button" onclick="cerrarModalCargar()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
                <button type="button" id="btnSubirFichero" onclick="importarAlumnosExcel()" disabled
                    class="px-5 py-2.5 rounded-xl bg-slate-700 text-white text-xs font-bold hover:bg-slate-800 transition-all shadow-md cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed uppercase tracking-wide">
                    Cargar Alumnos
                </button>
            </div>
        </div>
 
        <!-- Estado: cargando -->
        <div id="cargar_estado_cargando" style="display:none" class="text-center py-8">
            <div class="inline-block w-8 h-8 border-4 border-slate-200 border-t-orange-600 rounded-full animate-spin mb-4"></div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Procesando fichero...</p>
        </div>
 
        <!-- Estado: resultado -->
        <div id="cargar_estado_resultado" style="display:none">
            <div id="cargar_resultado_ok" style="display:none" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4 text-center">
                <p class="text-2xl mb-2">✅</p>
                <p class="text-sm font-black text-emerald-700" id="cargar_texto_ok"></p>
            </div>
            <div id="cargar_resultado_errores" style="display:none" class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-2">Filas omitidas</p>
                <ul id="cargar_lista_errores" class="text-[10px] text-amber-600 font-bold space-y-1"></ul>
            </div>
            <div class="flex justify-center mt-4">
                <button onclick="cerrarModalCargarYRecargar()" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 cursor-pointer uppercase tracking-widest">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL: HORARIO AVANZADO (z-60, sobre modalEditarAlumno z-50)  -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<style>
.ha-dia-btn {
    width: 2.75rem; height: 2.75rem;
    border-radius: 0.625rem;
    border: 2px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    cursor: pointer;
    transition: all .15s;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 1px;
    user-select: none;
}
.ha-dia-btn:hover { border-color: #f97316; color: #ea580c; background: #fff7ed; }
.ha-dia-btn.activo {
    background: #ea580c;
    border-color: #ea580c;
    color: #fff;
    box-shadow: 0 2px 8px rgba(234,88,12,.35);
}
.ha-dia-btn .ha-dia-letra { font-size: 13px; font-weight: 900; line-height: 1; }
.ha-dia-btn .ha-dia-nombre { font-size: 7px; font-weight: 700; opacity: .75; line-height: 1; }
.ha-hora-input {
    width: 100%; padding: .45rem .75rem;
    border: 2px solid #e2e8f0;
    border-radius: .625rem;
    font-size: 14px; font-weight: 800;
    text-align: center; outline: none;
    transition: border-color .15s;
    color: #1e293b;
}
.ha-hora-input:focus { border-color: #f97316; }
</style>

<div id="modalHorarioAvanzado" style="display:none" class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 max-h-[92vh] flex flex-col">

        <!-- Cabecera -->
        <div class="flex items-center justify-between px-7 pt-6 pb-4 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-600 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                Horario por días
            </h3>
            <button onclick="cerrarModalHorarioAvanzado()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer leading-none">✕</button>
        </div>

        <!-- Aviso informativo -->
        <div class="mx-7 mt-4 mb-1 flex items-start gap-2.5 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <span class="text-amber-500 text-base leading-none mt-0.5">💡</span>
            <p class="text-[10px] font-bold text-amber-700 leading-relaxed">
                Aquí defines a qué hora entra y sale el alumno cada día. <strong>Las 8 horas oficiales no cambian</strong> en la base de datos, pero este horario aparecerá en el Plan Formativo y en las exportaciones.
            </p>
        </div>

        <!-- Lista de bloques (scrollable) -->
        <div class="flex-1 overflow-y-auto px-7 py-4 space-y-4" id="ha_bloques_lista"></div>

        <!-- Botón añadir -->
        <div class="px-7 pb-3">
            <button type="button" onclick="haAnyadirBloque()"
                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl border-2 border-dashed border-slate-200 text-[11px] font-black text-slate-400 uppercase tracking-wider hover:border-orange-400 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Añadir otro grupo de días
            </button>
        </div>

        <!-- Resumen -->
        <div id="ha_resumen_semanal" class="mx-7 mb-3 hidden">
            <div class="bg-slate-50 rounded-xl border border-slate-200 px-4 py-3">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Resumen configurado</p>
                <div id="ha_resumen_contenido" class="space-y-1.5"></div>
            </div>
        </div>

        <!-- Pie -->
        <div class="flex items-center justify-between px-7 pb-6 pt-3 border-t border-slate-100">
            <button type="button" onclick="haBorrarTodo()"
                class="text-[10px] font-black text-red-400 hover:text-red-600 uppercase tracking-widest cursor-pointer transition-colors">
                Borrar todo
            </button>
            <div class="flex gap-3">
                <button type="button" onclick="cerrarModalHorarioAvanzado()"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                    Cancelar
                </button>
                <button type="button" onclick="haGuardarYCerrar()"
                    class="px-6 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Guardar horario
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ─── HORARIO AVANZADO ─────────────────────────────────────────────────────────
let haBloques = [];
const HA_DIAS  = ['L','M','X','J','V','S','D'];
const HA_LETRAS = { L:'L', M:'M', X:'X', J:'J', V:'V', S:'S', D:'D' };
const HA_NOMBRES = { L:'Lunes', M:'Martes', X:'Miér', J:'Jueves', V:'Viernes', S:'Sábado', D:'Domingo' };
const HA_CORTOS  = { L:'Lun', M:'Mar', X:'Mié', J:'Jue', V:'Vie', S:'Sáb', D:'Dom' };

function abrirModalHorarioAvanzado() {
    const raw = document.getElementById('edit_horario_excepciones').value.trim();
    try { haBloques = raw ? JSON.parse(raw) : []; } catch(e) { haBloques = []; }
    if (haBloques.length === 0) haBloques.push({ dias: ['L','M','X','J'], inicio: '08:00', fin: '17:00' });
    haRenderizar();
    document.getElementById('modalHorarioAvanzado').style.display = 'flex';
}

function cerrarModalHorarioAvanzado() {
    document.getElementById('modalHorarioAvanzado').style.display = 'none';
}

function haAnyadirBloque() {
    haBloques.push({ dias: [], inicio: '08:00', fin: '15:00' });
    haRenderizar();
}

function haEliminarBloque(idx) {
    haBloques.splice(idx, 1);
    haRenderizar();
}

function haBorrarTodo() {
    haBloques = [];
    haRenderizar();
}

// Toggle de día: actualiza datos y re-renderiza completo → visual siempre correcto
function haToggleDia(idx, dia) {
    const pos = haBloques[idx].dias.indexOf(dia);
    if (pos === -1) {
        haBloques[idx].dias.push(dia);
    } else {
        haBloques[idx].dias.splice(pos, 1);
    }
    haRenderizar();
}

function haActualizarHora(idx, campo, valor) {
    haBloques[idx][campo] = valor;
    haActualizarResumen();
}

function haRenderizar() {
    const lista = document.getElementById('ha_bloques_lista');
    lista.innerHTML = '';

    if (haBloques.length === 0) {
        lista.innerHTML = `
            <div class="text-center py-8">
                <div class="text-4xl mb-3">🗓️</div>
                <p class="text-[11px] font-bold text-slate-400">Sin bloques configurados.</p>
                <p class="text-[10px] text-slate-300 mt-1">Usa el botón de abajo para añadir uno.</p>
            </div>`;
        haActualizarResumen();
        return;
    }

    haBloques.forEach((bloque, idx) => {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-xl border-2 border-slate-100 p-4 shadow-sm';

        // Cabecera del bloque
        const hayDias = bloque.dias.length > 0;
        const diasLabel = hayDias
            ? bloque.dias.map(d => HA_CORTOS[d]).join(', ')
            : '<span class="text-red-400">Selecciona al menos un día</span>';

        card.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-md bg-orange-100 text-orange-600 text-[9px] font-black">${idx + 1}</span>
                    <span class="text-[10px] font-black text-slate-600">${diasLabel}</span>
                </div>
                <button type="button" onclick="haEliminarBloque(${idx})"
                    class="text-[9px] font-black text-slate-300 hover:text-red-500 uppercase tracking-widest cursor-pointer transition-colors flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    Eliminar
                </button>
            </div>

            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">¿Qué días aplica?</p>
            <div class="flex gap-2 mb-4 flex-wrap">
                ${HA_DIAS.map(d => `
                    <button type="button"
                        onclick="haToggleDia(${idx},'${d}')"
                        class="ha-dia-btn${bloque.dias.includes(d) ? ' activo' : ''}"
                        title="${HA_NOMBRES[d]}">
                        <span class="ha-dia-letra">${d}</span>
                        <span class="ha-dia-nombre">${HA_CORTOS[d]}</span>
                    </button>
                `).join('')}
            </div>

            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Horario ese día</p>
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <label class="block text-[9px] text-slate-400 font-bold mb-1">Entrada</label>
                    <input type="time" value="${bloque.inicio}"
                        oninput="haActualizarHora(${idx},'inicio',this.value)"
                        class="ha-hora-input">
                </div>
                <div class="flex flex-col items-center justify-end pb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
                <div class="flex-1">
                    <label class="block text-[9px] text-slate-400 font-bold mb-1">Salida</label>
                    <input type="time" value="${bloque.fin}"
                        oninput="haActualizarHora(${idx},'fin',this.value)"
                        class="ha-hora-input">
                </div>
            </div>
        `;
        lista.appendChild(card);
    });

    haActualizarResumen();
}

function haActualizarResumen() {
    const resumen  = document.getElementById('ha_resumen_semanal');
    const contenido = document.getElementById('ha_resumen_contenido');
    const activos = haBloques.filter(b => b.dias.length > 0);

    if (activos.length === 0) { resumen.classList.add('hidden'); return; }
    resumen.classList.remove('hidden');

    contenido.innerHTML = activos.map(b => {
        const diasTexto = b.dias.map(d => HA_NOMBRES[d]).join(', ');
        return `<div class="flex items-center justify-between gap-4">
            <span class="text-[10px] font-bold text-slate-600">${diasTexto}</span>
            <span class="text-[11px] font-black text-orange-600 shrink-0">${b.inicio} → ${b.fin}</span>
        </div>`;
    }).join('');
}

function haGuardarYCerrar() {
    const validos = haBloques.filter(b => b.dias.length > 0 && b.inicio && b.fin);
    const json = validos.length > 0 ? JSON.stringify(validos) : '';

    // Guardar en el campo oculto del formulario principal
    document.getElementById('edit_horario_excepciones').value = json;

    // Actualizar el botón y el resumen visual dentro de modalEditarAlumno
    haRefrescarBadgeEditar(json);
    cerrarModalHorarioAvanzado();
}

function haRefrescarBadgeEditar(jsonStr) {
    const labelBtn  = document.getElementById('btn_horario_avanzado_label');
    const resumenDiv = document.getElementById('resumen_excepciones_editar');
    try {
        const bloques = jsonStr ? JSON.parse(jsonStr) : [];
        if (bloques.length > 0) {
            labelBtn.textContent = `✓ Horario configurado — ${bloques.length} grupo${bloques.length > 1 ? 's' : ''}`;
            resumenDiv.className = 'mt-2 flex flex-wrap gap-1.5';
            resumenDiv.innerHTML = bloques.map(b => {
                const dias = b.dias.map(d => HA_CORTOS[d]).join(' · ');
                return `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-orange-100 text-orange-700 rounded-lg text-[9px] font-black border border-orange-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    ${dias}: ${b.inicio}–${b.fin}
                </span>`;
            }).join('');
        } else {
            labelBtn.textContent = 'Configurar horario avanzado';
            resumenDiv.className = 'mt-2 hidden';
            resumenDiv.innerHTML = '';
        }
    } catch(e) {
        labelBtn.textContent = 'Configurar horario avanzado';
        resumenDiv.className = 'mt-2 hidden';
    }
}

// Llamado al abrir modalEditarAlumno para restaurar el badge
function haRestaurarResumenEdicion(jsonStr) {
    haRefrescarBadgeEditar(jsonStr);
}

// ─── Validación de fechas ─────────────────────────────────────────────────────
function validarFechasAlumno() {
    const inicio = document.getElementById('edit_fecha_inicio').value;
    const final  = document.getElementById('edit_fecha_final').value;
    const campoFinal = document.getElementById('edit_fecha_final');
    if (inicio && final && final < inicio) {
        campoFinal.style.borderColor = '#f87171';
        campoFinal.style.boxShadow   = '0 0 0 3px rgba(248,113,113,0.2)';
        document.getElementById('alertFechasInvalidas').style.display = 'block';
        return false;
    }
    campoFinal.style.borderColor = '';
    campoFinal.style.boxShadow   = '';
    return true;
}

function cerrarAlertFechas() {
    document.getElementById('alertFechasInvalidas').style.display = 'none';
    document.getElementById('edit_fecha_final').focus();
}

function cerrarAlertDni() {
    document.getElementById('alertDniDuplicado').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const elInicio = document.getElementById('edit_fecha_inicio');
    const elFinal  = document.getElementById('edit_fecha_final');
    const formEd   = document.getElementById('formEditarAlumno');

    if (elFinal)  elFinal.addEventListener('change',  validarFechasAlumno);
    if (elInicio) elInicio.addEventListener('change', function () {
        if (document.getElementById('edit_fecha_final').value) validarFechasAlumno();
    });
    if (formEd) {
        formEd.addEventListener('submit', function (e) {
            if (!validarFechasAlumno()) e.preventDefault();
        });
    }
});
// ─────────────────────────────────────────────────────────────────────────────
</script>

<?php
// IDs de TODOS los alumnos COMPLETADOS — lista completa sin paginar
// Misma lógica que determina el estado en Alumnos.php
$_idsCompletados = [];
foreach ($alumnos ?? [] as $_al) {
    $tieneEmpresa   = !empty($_al['num_convenio']);
    $tieneDireccion = !empty($_al['direccion']);
    $tieneFechas    = !empty($_al['fecha_inicio']) && !empty($_al['fecha_final'])
                      && $_al['fecha_inicio'] !== '0000-00-00';
    $tieneHorario   = !empty($_al['horario']) && $_al['horas_dia'] > 0;
    if ($tieneEmpresa && $tieneDireccion && $tieneFechas && $tieneHorario) {
        $_idsCompletados[] = (int) $_al['id_alumno'];
    }
}
?>
<script>
// Array con TODOS los IDs COMPLETADOS, independientemente de la página visible
const TODOS_COMPLETADOS_IDS = <?= json_encode($_idsCompletados) ?>;

function exportarAlumnosWord() {
    // Recoge los checkboxes seleccionados del formExportar
    const seleccionados = document.querySelectorAll('#formExportar input[name="exportar_ids[]"]:checked');
    if (seleccionados.length === 0) return;

    // Crea un form temporal apuntando a la nueva acción Word
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?controlador=Tutores&accion=exportarAlumnosWord';
    form.style.display = 'none';

    seleccionados.forEach(cb => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'exportar_ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
        form.submit();

        // Limpiamos el DOM
        setTimeout(() => {
            if (document.body.contains(form)) {
                document.body.removeChild(form);
            }
            // RECARGA LA PÁGINA para ver los cambios (los checks verdes)
            window.location.href = "index.php?tab=2&status=success";
        }, 1500); // 1.5 segundos es suficiente para que inicie la descarga

        // Cerramos el modal de confirmación
        document.getElementById('modalConfirmarExportar').style.display = 'none';
    }

function onFicheroSeleccionado(input) {
    const btn   = document.getElementById('btnSubirFichero');
    const label = document.getElementById('nombreFicheroSeleccionado');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        btn.disabled = false;
    } else {
        label.textContent = 'Ningún fichero seleccionado';
        btn.disabled = true;
    }
}
 
function cerrarModalCargar() {
    document.getElementById('modalCargarAlumnos').style.display = 'none';
    // Resetear estado
    document.getElementById('cargar_estado_inicial').style.display   = 'block';
    document.getElementById('cargar_estado_cargando').style.display  = 'none';
    document.getElementById('cargar_estado_resultado').style.display = 'none';
    document.getElementById('inputFicheroAlumnos').value = '';
    document.getElementById('nombreFicheroSeleccionado').textContent = 'Ningún fichero seleccionado';
    document.getElementById('btnSubirFichero').disabled = true;
}
 
function cerrarModalCargarYRecargar() {
    cerrarModalCargar();
    window.location.href = 'index.php?controlador=Tutores&accion=mostrarPanel&tab=2';
}
 
async function importarAlumnosExcel() {
    const input = document.getElementById('inputFicheroAlumnos');
    if (!input.files || !input.files[0]) return;
 
    // Mostrar spinner
    document.getElementById('cargar_estado_inicial').style.display  = 'none';
    document.getElementById('cargar_estado_cargando').style.display = 'block';
 
    const formData = new FormData();
    formData.append('fichero_alumnos', input.files[0]);
    formData.append('anio_inicio', document.getElementById('anio_inicio_importar').value);
 
    try {
        const res  = await fetch('index.php?controlador=Tutores&accion=importarAlumnos', {
            method: 'POST',
            body:   formData,
        });
        const data = await res.json();
 
        // Ocultar spinner, mostrar resultado
        document.getElementById('cargar_estado_cargando').style.display  = 'none';
        document.getElementById('cargar_estado_resultado').style.display = 'block';
 
        if (data.success) {
            const okDiv  = document.getElementById('cargar_resultado_ok');
            const txtOk  = document.getElementById('cargar_texto_ok');
            okDiv.style.display = 'block';
            txtOk.textContent   = `${data.insertados} alumno${data.insertados !== 1 ? 's' : ''} importado${data.insertados !== 1 ? 's' : ''} correctamente.`;
 
            if (data.errores && data.errores.length > 0) {
                const errDiv  = document.getElementById('cargar_resultado_errores');
                const errList = document.getElementById('cargar_lista_errores');
                errDiv.style.display = 'block';
                errList.innerHTML = data.errores.map(e => `<li>• ${e}</li>`).join('');
            }
        } else {
            const okDiv = document.getElementById('cargar_resultado_ok');
            okDiv.style.display = 'block';
            okDiv.className = okDiv.className.replace('emerald', 'red');
            document.getElementById('cargar_texto_ok').textContent = 'Error: ' + (data.error ?? 'desconocido');
        }
    } catch (err) {
        document.getElementById('cargar_estado_cargando').style.display  = 'none';
        document.getElementById('cargar_estado_resultado').style.display = 'block';
        const okDiv = document.getElementById('cargar_resultado_ok');
        okDiv.style.display = 'block';
        document.getElementById('cargar_texto_ok').textContent = 'Error de conexión con el servidor.';
    }
}

function abrirModalExportarTodoAlumnos() {
    document.getElementById('modalSeleccionarExportar').style.display = 'none';
    document.getElementById('modalExportarTodoAlumnos').style.display = 'flex';
}

function ejecutarExportarTodoAlumnos() {
    // Usa el array generado por PHP con TODOS los COMPLETADOS (ignora paginación)
    if (TODOS_COMPLETADOS_IDS.length === 0) {
        document.getElementById('modalExportarTodoAlumnos').style.display = 'none';
        document.getElementById('modalSinCompletadosExportar').style.display = 'flex';
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?controlador=Tutores&accion=exportarAlumnosWord';
    form.style.display = 'none';

    const inputTodo = document.createElement('input');
    inputTodo.type  = 'hidden';
    inputTodo.name  = 'exportar_todo';
    inputTodo.value = '1';
    form.appendChild(inputTodo);

    TODOS_COMPLETADOS_IDS.forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'exportar_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();

    setTimeout(() => {
        if (document.body.contains(form)) document.body.removeChild(form);
    }, 3000);

    document.getElementById('modalExportarTodoAlumnos').style.display = 'none';
}


// ── Eliminar alumno ────────────────────────────────────────────────────────
function pedirConfirmacionEliminarAlumno() {
    const convenio = document.getElementById('edit_id_convenio').value;

    // Si tiene convenio asignado → no se puede eliminar, mostrar aviso directamente
    if (convenio && convenio.trim() !== '') {
        document.getElementById('modalEditarAlumno').style.display = 'none';
        document.getElementById('modalNoSePuedeEliminar').style.display = 'flex';
        return;
    }

    // Sin asignación → pedir confirmación
    const ap1 = document.getElementById('edit_apellido1').value;
    const ap2 = document.getElementById('edit_apellido2').value;
    const nom = document.getElementById('edit_nombre').value;
    document.getElementById('nombreAlumnoEliminar').textContent =
        (ap1 + ' ' + ap2 + ', ' + nom).trim().replace(/\s+/g, ' ');
    document.getElementById('modalEditarAlumno').style.display = 'none';
    document.getElementById('modalConfirmarEliminarAlumno').style.display = 'flex';
}

function ejecutarEliminarAlumno() {
    const idAlumno = document.getElementById('edit_id_alumno').value;
    document.getElementById('modalConfirmarEliminarAlumno').style.display = 'none';

    fetch('index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=eliminarAlumno&id_alumno=' + encodeURIComponent(idAlumno)
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.ok) {
            const fila = document.querySelector('tr[data-id-alumno="' + idAlumno + '"]');
            if (fila) {
                fila.remove();
            } else {
                window.location.href = 'index.php?tab=2';
            }
        } else if (data.motivo === 'tiene_asignacion') {
            document.getElementById('modalNoSePuedeEliminar').style.display = 'flex';
        } else {
            window.location.href = 'index.php?tab=2';
        }
    })
    .catch(() => {
        window.location.href = 'index.php?tab=2';
    });
}


</script>
