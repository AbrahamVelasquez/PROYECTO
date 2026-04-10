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

<script>
    function abrirModalDevolver(idAlumno, nombre) {
        // 1. Insertar el nombre en el modal
        document.getElementById('nombreAlumnoDevolver').textContent = nombre;
        
        // 2. Mostrar el modal
        document.getElementById('modalConfirmarDevolver').style.display = 'flex';
        
        // 3. Configurar la acción del botón "Sí, devolver"
        document.getElementById('btnEjecutarDevolucion').onclick = function() {
            console.log("Procesando devolución del alumno ID:", idAlumno);
            
            // Aquí puedes hacer el submit de un formulario o un fetch
            // Ejemplo: enviar a un controlador de PHP
            window.location.href = `/PlanFormativo/devolver/${idAlumno}`;
        };
    }
</script>