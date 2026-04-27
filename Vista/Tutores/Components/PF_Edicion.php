<?php

// Vista/Tutores/Components/PF_Edicion.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
validarAcceso('tutor'); 

?>
<div class="w-full bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-200 mb-10">
    <div class="bg-slate-50 p-6 border-b border-slate-100 flex justify-between items-center">
        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">✏️</span>
            EDICIÓN DEL PLAN FORMATIVO
        </h3>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Plan Formativo</span>
    </div>

    <form class="p-8">
        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">1. Identificación Académica y Temporal</p>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nº Anexo</label>
                <input type="number" id="pf_edit_anexo" placeholder="—" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all text-center">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Régimen</label>
                <select id="pf_edit_regimen" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                    <option value="GENERAL">GENERAL</option>
                    <option value="INTENSIVO">INTENSIVO</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Fecha</label>
                <input type="date" id="pf_edit_fecha_plan" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Curso Académico</label>
                <div class="flex items-center gap-1">
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2 focus-within:ring-2 focus-within:ring-orange-100 transition-all">
                        <span class="text-xs font-bold text-slate-400">20</span>
                        <input type="number" id="pf_edit_anio_inicio" class="w-8 py-2.5 bg-transparent text-xs font-bold outline-none text-center" oninput="if(this.value.length > 2) this.value = this.value.slice(0,2)">
                    </div>
                    <span class="text-slate-400 font-bold">-</span>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2 focus-within:ring-2 focus-within:ring-orange-100 transition-all">
                        <span class="text-xs font-bold text-slate-400">20</span>
                        <input type="number" id="pf_edit_anio_fin" class="w-8 py-2.5 bg-transparent text-xs font-bold outline-none text-center" oninput="if(this.value.length > 2) this.value = this.value.slice(0,2)">
                    </div>
                </div>
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Curso</label>
                <select id="pf_edit_curso_selector" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                    <option value="1">1º CURSO</option>
                    <option value="2">2º CURSO</option>
                </select>
            </div>
            <div class="md:col-span-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Ciclo / Especialización / Programa</label>
                <input type="text" id="pf_edit_nombre_ciclo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Código</label>
                <input type="text" id="pf_edit_codigo_ciclo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">2. Datos del Alumno/a</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre Completo</label>
                <input type="text" id="pf_edit_nombre_completo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</label>
                <input type="email" id="pf_edit_email_alumno" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono</label>
                <input type="text" id="pf_edit_tel_alumno" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all bg-white">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">3. Centro Docente y Tutoría Académica</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Centro Docente</label>
                <input type="text" id="pf_edit_centro_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Centro</label>
                <input type="email" id="pf_edit_centro_correo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Centro</label>
                <input type="text" id="pf_edit_centro_tel" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tutor/a del Centro</label>
                <input type="text" id="pf_edit_tutor_centro_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Tutor</label>
                <input type="email" id="pf_edit_tutor_centro_correo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Tutor</label>
                <input type="text" id="pf_edit_tutor_centro_tel" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">4. Empresa u Organismo Equiparado</p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">ID Convenio</label>
                <input type="text" id="pf_id_convenio" readonly class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 cursor-not-allowed focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre de la Empresa</label>
                <input type="text" id="pf_edit_nombre_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">N.I.F.</label>
                <input type="text" id="pf_edit_nif_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all text-center">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico Empresa</label>
                <input type="email" id="pf_edit_email_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Empresa</label>
                <input type="text" id="pf_edit_tel_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tutor/a de Empresa</label>
                <input type="text" id="pf_edit_tutor_empresa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Tutor Emp.</label>
                <input type="email" id="pf_edit_email_tutor_emp" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Tutor Emp.</label>
                <input type="text" id="pf_edit_tel_tutor_emp" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">5. Medidas y Autorizaciones Especiales</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">¿Requiere medidas/adaptaciones por discapacidad?</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600"><input type="radio" name="discapacidad" value="SI" class="accent-orange-600"> SÍ</label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600"><input type="radio" name="discapacidad" value="NO" checked class="accent-orange-600"> NO</label>
                </div>
                <input type="text" placeholder="Especificar medidas..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">¿Requiere autorización extraordinaria?</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600"><input type="radio" name="autorizacion" value="SI" class="accent-orange-600"> SÍ</label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600"><input type="radio" name="autorizacion" value="NO" checked class="accent-orange-600"> NO</label>
                </div>
                <input type="text" placeholder="Indicar causa/s..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">6. Planificación de la Formación</p>
        <div class="mb-4 flex flex-wrap gap-6 items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Intervalo:</span>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                    <input type="checkbox" id="inter_diario" data-tipo="diario" class="check-intervalo rounded accent-orange-600" checked> DIARIO
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                    <input type="checkbox" id="inter_semanal" data-tipo="semanal" class="check-intervalo rounded accent-orange-600"> SEMANAL
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                    <input type="checkbox" id="inter_mensual" data-tipo="mensual" class="check-intervalo rounded accent-orange-600"> MENSUAL
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                    <input type="checkbox" id="inter_otros" data-tipo="otros" class="check-intervalo rounded accent-orange-600"> OTROS
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                    <input type="checkbox" id="inter_varias" data-tipo="varias" class="check-intervalo rounded accent-orange-600"> VARIAS EMPRESAS
                </label>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm mb-6">
            <table class="w-full text-left border-collapse bg-white">
                <thead class="bg-slate-50">
                    <tr class="text-slate-600 text-[9px] font-black uppercase tracking-wider">
                        <th class="p-3 border-r border-slate-200 w-24">Nº Periodo</th>
                        <th class="p-3 border-r border-slate-200">Calendario (Fechas)</th>
                        <th class="p-3">Horario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="p-2 border-r border-slate-200">
                            <select id="pf_edit_periodo_planificacion" class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 text-center bg-transparent cursor-pointer focus:ring-2 focus:ring-orange-100 rounded-lg border border-slate-200">
                                <option value="1">1º</option>
                                <option value="2">2º</option>
                                <option value="3">3º</option>
                            </select>
                        </td>
                        <td class="p-2 border-r border-slate-200">
                            <div class="flex items-center gap-1">
                                <input type="date" id="pf_edit_fecha_inicio"
                                    class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 bg-transparent">
                                <span class="text-slate-300 font-bold">/</span>
                                <input type="date" id="pf_edit_fecha_final"
                                    class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 bg-transparent">
                            </div>
                        </td>
                        <td class="p-2">
                            <input type="text" id="pf_edit_horario" placeholder="08:00 - 15:00"
                                class="w-full px-2 py-1 outline-none text-xs font-bold uppercase text-slate-600">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mb-10">
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 text-right">Total Horas:</label>
                <input type="number" id="pf_edit_horas_totales"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all text-center">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">7. Resultados de Aprendizaje Profesionales</p>

        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 mb-4 flex items-center gap-3">
            <span class="text-lg leading-none">📋</span>
            <p class="text-[10px] font-bold text-slate-500 leading-relaxed">
                Los Resultados de Aprendizaje son <span class="text-slate-800 font-black">comunes a todos los alumnos</span>.
                Gestiónelos desde el botón <span class="text-slate-800 font-black uppercase">Resultados de Aprendizaje</span> del listado de Planes Formativos.
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm mb-8">
            <table class="w-full text-left border-collapse bg-white" id="tabla-modulos">
                <thead class="bg-slate-50">
                    <tr class="text-slate-600 text-[9px] font-black uppercase tracking-wider">
                        <th class="p-3 border-r border-slate-200 w-16 text-center">Periodo</th>
                        <th class="p-3 border-r border-slate-200">Módulo Profesional</th>
                        <th class="p-3 border-r border-slate-200 w-20 text-center">Código</th>
                        <th class="p-3 border-r border-slate-200 w-32 text-center">Resultados de Aprendizaje</th>
                        <th class="p-3 border-r border-slate-200 w-36 text-center leading-tight">Impartido íntegramente en la empresa</th>
                        <th class="p-3 w-36 text-center leading-tight">Impartición compartida con el centro docente</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="modulos-tbody-pf">
                    <?php 
                    $totalFilasVisibles = 14;
                    $filaActual = 0;

                    // 1. Pintamos los RAs que vienen de la base de datos
                    if (!empty($rasExistentes)): 
                        foreach ($rasExistentes as $ra): 
                            $filaActual++;
                    ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-2 border-r border-slate-200">
                                <input type="text" readonly value="<?= htmlspecialchars($ra['periodo']) ?>" class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 text-center bg-transparent cursor-default">
                            </td>
                            <td class="p-2 border-r border-slate-200">
                                <input type="text" readonly value="<?= htmlspecialchars($ra['nombre_modulo']) ?>" class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 bg-transparent cursor-default">
                            </td>
                            <td class="p-2 border-r border-slate-200">
                                <input type="text" readonly value="<?= htmlspecialchars($ra['id_modulo']) ?>" class="w-full px-2 py-1 outline-none text-xs font-bold font-mono text-slate-500 text-center bg-transparent cursor-default">
                            </td>
                            <td class="p-2 border-r border-slate-200">
                                <input type="text" readonly value="RA<?= htmlspecialchars($ra['numero_ra']) ?>" class="w-full px-2 py-1 outline-none text-xs font-bold text-slate-600 text-center bg-transparent cursor-default">
                            </td>
                            <td class="p-2 border-r border-slate-200 text-center">
                                <input type="checkbox" disabled <?= $ra['impartido_empresa'] == 1 ? 'checked' : '' ?> class="accent-orange-600 w-4 h-4 cursor-not-allowed">
                            </td>
                            <td class="p-2 text-center">
                                <input type="checkbox" disabled <?= $ra['impartido_empresa'] == 0 ? 'checked' : '' ?> class="accent-orange-600 w-4 h-4 cursor-not-allowed">
                            </td>
                        </tr>
                    <?php 
                        endforeach; 
                    endif; 

                    // 2. Rellenamos hasta llegar a 14 filas para mantener el diseño
                    for($i = $filaActual; $i < $totalFilasVisibles; $i++): 
                    ?>
                        <tr>
                            <td class="p-2 border-r border-slate-200"><input type="text" placeholder="—" readonly class="w-full px-2 py-1 outline-none text-xs text-slate-300 text-center bg-transparent"></td>
                            <td class="p-2 border-r border-slate-200"><input type="text" placeholder="—" readonly class="w-full px-2 py-1 outline-none text-xs text-slate-300 bg-transparent"></td>
                            <td class="p-2 border-r border-slate-200"><input type="text" placeholder="—" readonly class="w-full px-2 py-1 outline-none text-xs text-slate-300 text-center bg-transparent"></td>
                            <td class="p-2 border-r border-slate-200"><input type="text" placeholder="—" readonly class="w-full px-2 py-1 outline-none text-xs text-slate-300 text-center bg-transparent"></td>
                            <td class="p-2 border-r border-slate-200 text-center"><input type="checkbox" disabled class="accent-slate-200 w-4 h-4 opacity-30"></td>
                            <td class="p-2 text-center"><input type="checkbox" disabled class="accent-slate-200 w-4 h-4 opacity-30"></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <input type="hidden" id="edit_id_asignacion" name="id_asignacion" value="<?= $_GET['editar'] ?? '' ?>">

        <?php include 'Buttons_PF_Edicion.php'; ?>

    </form>
</div>