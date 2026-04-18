<?php 
// Vista/Admin/Components/Dashboard_Section.php
?>
<div class="flex flex-col items-center justify-center h-full py-10">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Módulos del Sistema</h2>
        <p class="text-slate-500 mt-1">Seleccione una categoría para gestionar la información de las FCT.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-6xl px-4">
        
        <form action="index.php" method="POST" class="group">
            <input type="hidden" name="accion" value="mostrarTutores">
            <button type="submit" class="w-full h-full text-left p-8 rounded-3xl border-2 border-slate-100 bg-white hover:border-orange-500 hover:shadow-xl hover:shadow-orange-100 transition-all cursor-pointer">
                <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 transition-colors">
                    <span class="text-2xl">👨‍🏫</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-orange-600 transition-colors">Personal Docente</h3>
                <p class="text-slate-500 text-xs mt-2 leading-relaxed">Gestión de tutores y asignación de ciclos formativos.</p>
            </button>
        </form>

        <form action="index.php" method="POST" class="group">
            <input type="hidden" name="accion" value="mostrarConveniosPendientes">
            <button type="submit" class="w-full h-full text-left p-8 rounded-3xl border-2 border-slate-100 bg-white hover:border-emerald-600 hover:shadow-xl hover:shadow-emerald-100 transition-all cursor-pointer">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 transition-colors">
                        <span class="text-2xl">⏳</span>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Convenios Pendientes</h3>
                <p class="text-slate-500 text-xs mt-2 leading-relaxed">Nuevas solicitudes aprobadas esperando registro final.</p>
            </button>
        </form>

        <form action="index.php" method="POST" class="group">
            <input type="hidden" name="accion" value="mostrarConvenios">
            <button type="submit" class="w-full h-full text-left p-8 rounded-3xl border-2 border-slate-100 bg-white hover:border-blue-600 hover:shadow-xl hover:shadow-blue-100 transition-all cursor-pointer">
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors">
                    <span class="text-2xl">🏢</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Convenios Validos</h3>
                <p class="text-slate-500 text-xs mt-2 leading-relaxed">Base de datos de empresas con acuerdos en vigor.</p>
            </button>
        </form>

    </div>
</div>