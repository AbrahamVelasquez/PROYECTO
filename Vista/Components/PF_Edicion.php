<div class="max-w-5xl mx-auto bg-white shadow-xl rounded-xl overflow-hidden border border-slate-200">
    <div class="bg-slate-900 p-4 text-white">
        <h2 class="text-xl font-bold">Edición del Plan Formativo</h2>
    </div>

    <form class="p-8 space-y-8">
        <section>
            <h3 class="text-sm font-black text-blue-600 uppercase tracking-wider mb-4">1. Identificación Académica y Temporal</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Fecha</label>
                    <input type="date" class="w-full border-b-2 border-slate-200 focus:border-blue-500 outline-none py-1">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Curso</label>
                    <div class="flex items-center gap-2">
                        <input type="number" placeholder="2024" class="w-full border-b-2 border-slate-200 focus:border-blue-500 outline-none py-1">
                        <span>-</span>
                        <input type="number" placeholder="2025" class="w-full border-b-2 border-slate-200 focus:border-blue-500 outline-none py-1">
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Régimen</label>
                    <input type="text" class="w-full border-b-2 border-slate-200 focus:border-blue-500 outline-none py-1">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Ciclo / Especialización</label>
                    <input type="text" class="w-full border-b-2 border-slate-200 focus:border-blue-500 outline-none py-1">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase">Código</label>
                    <input type="text" class="w-full border-b-2 border-slate-200 focus:border-blue-500 outline-none py-1">
                </div>
            </div>
        </section>

        <section class="bg-slate-50 p-4 rounded-lg">
            <h3 class="text-sm font-black text-blue-600 uppercase tracking-wider mb-4">2. Datos del Alumno/a</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1 font-bold">Nombre: <span class="font-normal border-b border-dotted border-slate-400">Juan Pérez García</span></div>
                <div class="md:col-span-1 font-bold">Email: <span class="font-normal border-b border-dotted border-slate-400">juan@email.com</span></div>
                <div class="md:col-span-1 font-bold">Tel: <span class="font-normal border-b border-dotted border-slate-400">600 000 000</span></div>
            </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <section>
                <h3 class="text-sm font-black text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">3. Centro Docente</h3>
                <input type="text" placeholder="Nombre Centro" class="w-full mb-3 border-b border-slate-200 py-1">
                <input type="text" placeholder="Tutor Académico" class="w-full border-b border-slate-200 py-1">
            </section>
            <section>
                <h3 class="text-sm font-black text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">4. Empresa</h3>
                <input type="text" placeholder="Nombre Empresa" class="w-full mb-3 border-b border-slate-200 py-1">
                <input type="text" placeholder="Tutor Empresa" class="w-full border-b border-slate-200 py-1">
            </section>
        </div>

        <section>
            <h3 class="text-sm font-black text-blue-600 uppercase tracking-wider mb-4">6. Planificación de la Formación</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-200">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="p-2 border">Nº periodo</th>
                            <th class="p-2 border">Calendario</th>
                            <th class="p-2 border">Horario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border"><input type="text" class="w-full outline-none"></td>
                            <td class="p-2 border"><input type="text" class="w-full outline-none"></td>
                            <td class="p-2 border"><input type="text" class="w-full outline-none"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="pt-6 border-t border-slate-200 flex flex-wrap gap-4 justify-between items-center">
            <button type="button" onclick="volverALista()" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold text-xs transition">
                ← VOLVER A TABLA
            </button>
            
            <div class="flex gap-3">
               <div class="relative group">
                    <button type="button" 
                        onclick="abrirModalDevolver(<?php echo $alumnoId; ?>, '<?php echo $alumnoNombre; ?>')" 
                        class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-xs flex items-center gap-2">
                        DEVOLVER ALUMNO 
                        <span class="bg-white/20 rounded-full h-4 w-4 flex items-center justify-center text-[10px]">?</span>
                    </button>
                    <div class="absolute bottom-full mb-2 hidden group-hover:block w-48 p-2 bg-slate-800 text-white text-[10px] rounded shadow-lg">
                        Caso excepcional en que el alumno irá a otra empresa.
                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-800"></div>
                    </div>
                </div>

                <button type="submit" class="px-6 py-2 bg-slate-900 hover:bg-slate-700 text-white rounded-xl font-bold text-xs transition">
                    EXPORTAR PDF
                </button>
            </div>
        </div>
    </form>
</div>

<?php include_once 'Vista/Components/Modales_Feedback.php'; ?>