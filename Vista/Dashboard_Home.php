<div class="flex flex-col items-center justify-center h-full py-10">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-slate-800">Módulos del Sistema</h2>
        <p class="text-slate-500 mt-1">Seleccione una tabla para visualizar o editar la información.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl">
        <form action="index.php" method="POST" class="group">
            <input type="hidden" name="accion" value="mostrarTutores">
            <button type="submit" class="w-full text-left p-8 rounded-3xl border-2 border-slate-100 bg-white hover:border-orange-500 hover:shadow-xl hover:shadow-orange-100 transition-all cursor-pointer">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center group-hover:bg-orange-600 transition-colors">
                        <span class="text-2xl">👨‍🏫</span>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 group-hover:text-orange-600 transition-colors">Personal Docente</h3>
                <p class="text-slate-500 text-sm mt-2">Gestión completa de tutores y ciclos.</p>
            </button>
        </form>

        <div class="p-8 rounded-3xl border-2 border-slate-100 bg-white opacity-60">
            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mb-6">
                <span class="text-2xl">🏢</span>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Convenios de Empresa</h3>
            <p class="text-slate-500 text-sm mt-2">Módulo en desarrollo.</p>
        </div>
    </div>
</div>