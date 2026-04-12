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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Régimen</label>
                <select class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                    <option value="GENERAL">GENERAL</option>
                    <option value="INTENSIVO">INTENSIVO</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Fecha</label>
                <input type="date" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Curso Académico</label>
                <div class="flex items-center gap-2">
                    <input type="number" placeholder="2024" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 text-center text-[10px]">
                    <span class="text-slate-400">-</span>
                    <input type="number" placeholder="2025" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 text-center text-[10px]">
                </div>
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Curso</label>
                <select class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all cursor-pointer">
                    <option value="1">1º CURSO</option>
                    <option value="2">2º CURSO</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Ciclo / Especialización / Programa</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Código</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">2. Datos del Alumno/a</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre Completo</label>
                <input type="text" id="edit_nombre_completo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</label>
                <input type="email" id="edit_email_alumno" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono</label>
                <input type="text" id="edit_tel_alumno" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all bg-white">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">3. Centro Docente y Tutoría Académica</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Centro Docente</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Centro</label>
                <input type="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Centro</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tutor/a del Centro</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Tutor</label>
                <input type="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Tutor</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
        </div>

        <p class="text-[11px] font-black text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">4. Empresa u Organismo Equiparado</p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre de la Empresa</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">N.I.F.</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all text-center">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico Empresa</label>
                <input type="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Empresa</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tutor/a de Empresa</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Tutor Emp.</label>
                <input type="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono Tutor Emp.</label>
                <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all">
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
                <?php foreach(['Diario', 'Semanal', 'Mensual', 'Otros', 'Varias empresas'] as $int): ?>
                    <label class="flex items-center gap-2 cursor-pointer text-[10px] font-black text-slate-600">
                        <input type="checkbox" class="rounded accent-orange-600"> <?= strtoupper($int) ?>
                    </label>
                <?php endforeach; ?>
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
                        <td class="p-2 border-r border-slate-200"><input type="text" placeholder="1" class="w-full px-2 py-1 outline-none text-xs font-bold uppercase text-slate-600 text-center"></td>
                        <td class="p-2 border-r border-slate-200"><input type="text" placeholder="Octubre 2024 - Junio 2025" class="w-full px-2 py-1 outline-none text-xs font-bold uppercase text-slate-600"></td>
                        <td class="p-2"><input type="text" placeholder="08:00 - 15:00" class="w-full px-2 py-1 outline-none text-xs font-bold uppercase text-slate-600"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-end mb-10">
            <div class="w-full md:w-64">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 text-right">Total Horas:</label>
                <input type="number" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all text-center">
            </div>
        </div>

        <?php include 'Buttons_PF_Edicion.php'; ?>
        
    </form>
</div>


<?php include_once 'Vista/Components/Modales_Feedback.php'; ?>