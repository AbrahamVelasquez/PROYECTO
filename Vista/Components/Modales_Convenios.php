<div id="modalConvenioEnUso" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs">⚠️</span>
                CONVENIO EN USO
            </h3>
            <button onclick="document.getElementById('modalConvenioEnUso').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p id="modalConvenioMensaje" class="text-xs font-bold text-slate-600 mb-6 text-center"></p>
        <div class="flex justify-center">
            <button onclick="document.getElementById('modalConvenioEnUso').style.display='none'" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 cursor-pointer">Entendido</button>
        </div>
    </div>
</div>

<div id="modalConfirmarEliminar" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs">🗑️</span>
                ELIMINAR
            </h3>
            <button onclick="document.getElementById('modalConfirmarEliminar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Quitar de tu lista?</p>
        <p id="modalConfirmarNombre" class="text-sm font-black text-slate-900 mb-6 text-center uppercase"></p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarEliminar').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
            <button id="btnConfirmarEliminarFav" class="px-5 py-2.5 rounded-xl bg-red-500 text-white text-xs font-bold hover:bg-red-600 cursor-pointer">Sí, eliminar</button>
        </div>
    </div>
</div>

<div id="modalConfirmarAprobar" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-emerald-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 text-xs font-bold">✓</span>
                APROBAR CONVENIO
            </h3>
            <button onclick="document.getElementById('modalConfirmarAprobar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer transition-colors">✕</button>
        </div>
        
        <p class="text-xs font-bold text-emerald-600 mb-1 text-center uppercase tracking-widest">¿Confirmar aprobación de?</p>
        <p id="modalAprobarNombre" class="text-sm font-black text-slate-900 mb-6 text-center uppercase tracking-tight"></p>
        
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarAprobar').style.display='none'" 
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            
            <button id="btnConfirmarAprobarFinal" 
                    class="group flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white px-5 py-2.5 rounded-xl transition-all border border-emerald-100 shadow-sm hover:shadow-emerald-200 cursor-pointer active:scale-95">
                <span class="text-[10px] font-black uppercase tracking-widest">Sí, aprobar</span>
                <span class="text-xs">✓</span>
            </button>
        </div>
    </div>
</div>

<script>

    function abrirConfirmarEliminar(idConvenio, nombre) {
        document.getElementById('modalConfirmarNombre').textContent = nombre;
        document.getElementById('modalConfirmarEliminar').style.display = 'flex';
        document.getElementById('btnConfirmarEliminarFav').onclick = function() {
            document.querySelectorAll('input[name="id_convenio_eliminar"]').forEach(function(input) {
                if (input.value == idConvenio) {
                    var btn = document.createElement('input');
                    btn.type = 'hidden'; btn.name = 'btnEliminarFav'; btn.value = '1';
                    input.closest('form').appendChild(btn);
                    input.closest('form').submit();
                }
            });
        };
    }

// Función para abrir el modal de aprobación
    function abrirConfirmarAprobar(idConvenio, nombre) {
        document.getElementById('modalAprobarNombre').textContent = nombre;
        document.getElementById('modalConfirmarAprobar').style.display = 'flex';
        
        document.getElementById('btnConfirmarAprobarFinal').onclick = function() {
            // Buscamos el formulario que contiene el ID del convenio correspondiente
            document.querySelectorAll('input[name="id_convenio_nuevo"]').forEach(function(input) {
                if (input.value == idConvenio) {
                    // El formulario ya tiene el input 'accion' con 'aprobarNuevo'
                    input.closest('form').submit();
                }
            });
        };
    }
</script>