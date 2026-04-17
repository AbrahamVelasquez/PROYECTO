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

<div id="modalEditarConvenioNuevo" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8 border border-amber-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-white text-xs">✏️</span>
                EDITAR CONVENIO EN PROCESO
            </h3>
            <button onclick="document.getElementById('modalEditarConvenioNuevo').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <form method="POST" action="index.php" id="formEditarConvenioNuevo">
            <input type="hidden" name="accion" value="editarConvenioNuevo">
            <input type="hidden" name="id_convenio_nuevo" id="edit_conv_id">

            <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-4 border-b border-amber-50 pb-2">Información de la Empresa</p>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre Empresa <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre_empresa" id="edit_conv_nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">CIF <span class="text-red-500">*</span></label>
                    <input type="text" name="cif" id="edit_conv_cif" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-4">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Dirección <span class="text-red-500">*</span></label>
                    <input type="text" name="direccion" id="edit_conv_direccion" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Municipio <span class="text-red-500">*</span></label>
                    <input type="text" name="municipio" id="edit_conv_municipio" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">CP <span class="text-red-500">*</span></label>
                    <input type="text" name="cp" id="edit_conv_cp" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">País</label>
                    <input type="text" name="pais" id="edit_conv_pais" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tfno</label>
                    <input type="text" name="telefono" id="edit_conv_telefono" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">FAX</label>
                    <input type="text" name="fax" id="edit_conv_fax" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Email</label>
                    <input type="email" name="email" id="edit_conv_email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
            </div>

            <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-4 border-b border-amber-50 pb-2">Representante Legal</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre y Apellidos</label>
                    <input type="text" name="nombre_rep_legal" id="edit_conv_rep_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">DNI</label>
                    <input type="text" name="dni_rep_legal" id="edit_conv_rep_dni" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Cargo</label>
                    <input type="text" name="cargo_rep_legal" id="edit_conv_rep_cargo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">
                </div>
            </div>

            <div class="flex gap-3 justify-between w-full">
                <button type="button" onclick="confirmarEliminarConvenioNuevo()" 
                        class="px-5 py-2.5 rounded-xl border border-red-100 text-xs font-bold text-red-500 hover:bg-red-50 transition-all cursor-pointer flex items-center gap-2">
                    <span>🗑️</span> Eliminar Solicitud
                </button>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modalEditarConvenioNuevo').style.display='none'" 
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-all shadow-md cursor-pointer">
                        Actualizar Datos
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modalConfirmarBorrarConvenio" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">⚠️</div>
        <h3 class="text-lg font-black text-slate-900 mb-2">¿Estás seguro?</h3>
        <p class="text-slate-500 text-sm mb-6">Esta acción eliminará la solicitud de convenio de forma permanente. No se puede deshacer.</p>
        
        <form method="POST" action="index.php">
            <input type="hidden" name="accion" value="eliminarConvenioNuevo">
            <input type="hidden" name="id_convenio_nuevo" id="id_eliminar_nuevo">
            
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalConfirmarBorrarConvenio').style.display='none'" 
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50">
                    No, cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 text-white text-xs font-bold hover:bg-red-600 shadow-lg shadow-red-200">
                    Sí, eliminar
                </button>
            </div>
        </form>
    </div>
</div>

<script>

    function abrirEditarConvenioNuevo(datos) {
        // ID
        document.getElementById('edit_conv_id').value = datos.id_convenio_nuevo;
        
        // Empresa
        document.getElementById('edit_conv_nombre').value = datos.nombre_empresa;
        document.getElementById('edit_conv_cif').value = datos.cif;
        document.getElementById('edit_conv_direccion').value = datos.direccion;
        document.getElementById('edit_conv_municipio').value = datos.municipio;
        document.getElementById('edit_conv_cp').value = datos.cp;
        document.getElementById('edit_conv_pais').value = datos.pais || 'ESPAÑA';
        document.getElementById('edit_conv_telefono').value = datos.telefono || '';
        document.getElementById('edit_conv_fax').value = datos.fax || '';
        document.getElementById('edit_conv_email').value = datos.mail;
        
        // Representante
        document.getElementById('edit_conv_rep_nombre').value = datos.nombre_representante || '';
        document.getElementById('edit_conv_rep_dni').value = datos.dni_representante || '';
        document.getElementById('edit_conv_rep_cargo').value = datos.cargo || '';

        document.getElementById('modalEditarConvenioNuevo').style.display = 'flex';
    }

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

    function confirmarEliminarConvenioNuevo() {
        // Obtenemos el ID que ya está cargado en el modal de edición
        const id = document.getElementById('edit_conv_id').value;
        
        // Pasamos el ID al modal de confirmación
        document.getElementById('id_eliminar_nuevo').value = id;
        
        // Mostramos el modal de confirmación (encima del otro)
        document.getElementById('modalConfirmarBorrarConvenio').style.display = 'flex';
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