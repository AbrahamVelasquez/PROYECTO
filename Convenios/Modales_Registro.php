<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div id="modalErrorCiclo" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="max-w-md w-full bg-white border-t-4 border-red-500 rounded-2xl shadow-2xl p-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
        </div>
        <h2 class="text-3xl font-black uppercase tracking-tight text-slate-800 mb-4">¡Error de <span class="text-red-600">Acceso!</span></h2>
        <p class="text-slate-500 font-medium leading-relaxed mb-6">No se ha detectado un identificador de ciclo válido. Por favor, solicite una nueva URL a la organización.</p>
    </div>
</div>

<div id="modalConfirmar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 text-center">
            <h3 class="text-2xl font-black uppercase text-slate-800 mb-2">¿Confirmar <span class="text-orange-600">Registro?</span></h3>
            <p class="text-slate-500">Asegúrese de que todos los datos de la empresa y el representante legal son correctos antes de finalizar.</p>
        </div>
        <div class="flex border-t border-slate-100">
            <button onclick="cerrarConfirmacion()" class="flex-1 px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Revisar Datos</button>
            <button onclick="ejecutarEnvioReal()" class="flex-1 px-6 py-4 text-xs font-black uppercase tracking-widest bg-orange-600 text-white hover:bg-orange-700 transition-colors">Sí, Enviar</button>
        </div>
    </div>
</div>

<div id="modalExito" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="max-w-md w-full bg-white border-t-4 border-emerald-500 rounded-2xl shadow-2xl p-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
        </div>
        <h2 class="text-3xl font-black uppercase tracking-tight text-slate-800 mb-4">¡Registro <span class="text-emerald-600">Completado!</span></h2>
        <p class="text-slate-500 font-medium leading-relaxed mb-6">El convenio se ha registrado correctamente. <br> Nuestro equipo revisará la información próximamente.</p>
        <div class="pt-6 border-t border-slate-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ya puede cerrar esta ventana de forma segura</p>
        </div>
    </div>
</div>