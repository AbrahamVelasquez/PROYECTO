<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 px-2">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Convenios de Empresa</h2>
        <p class="text-slate-500 text-[11px] uppercase font-bold tracking-[0.2em] mt-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Empresas con convenio activo: <?= count($convenios) ?>
        </p>
    </div>
    <div class="flex items-center gap-4">
        <form action="index.php" method="POST">
            <input type="hidden" name="accion" value="mostrarPanel">
            <button type="submit" class="group flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-blue-600 transition-all cursor-pointer">
                <span class="transition-transform group-hover:-translate-x-1">←</span> Volver al inicio
            </button>
        </form>
    </div>
</div>

<form method="POST" action="index.php" class="flex flex-col lg:flex-row gap-4 mb-8 p-4 bg-slate-50/50 rounded-2xl border border-slate-100 items-center">
    <input type="hidden" name="accion" value="mostrarConvenios">
    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" name="busqueda" value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>" placeholder="BUSCAR POR NOMBRE O CIF..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all uppercase">
    </div>
    <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold text-[10px] hover:bg-blue-600 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
        BUSCAR
    </button>
</form>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden text-slate-700">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Empresa / CIF</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Ubicación</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Contacto</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Representante</th>
                <th class="py-5 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($convenios)): ?>
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400 italic text-xs uppercase tracking-widest">
                        No hay convenios que coincidan con la búsqueda
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($convenios as $fila): ?>
                <tr class="hover:bg-slate-50/40 transition-all group">
                    <td class="py-5 px-6">
                        <div class="text-sm font-bold text-slate-800 uppercase"><?= htmlspecialchars($fila['nombre_empresa']) ?></div>
                        <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($fila['cif']) ?></div>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-xs text-slate-600 font-bold uppercase"><?= htmlspecialchars($fila['municipio']) ?></div>
                        <div class="text-[10px] text-slate-400"><?= htmlspecialchars($fila['direccion']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-xs text-slate-500">
                        <div class="font-bold"><?= htmlspecialchars($fila['mail']) ?></div>
                        <div class="text-[9px] text-slate-400"><?= htmlspecialchars($fila['telefono'] ?? '') ?></div>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-[11px] font-bold text-slate-700 uppercase"><?= htmlspecialchars($fila['nombre_representante']) ?></div>
                        <div class="text-[9px] text-slate-400 uppercase tracking-tighter"><?= htmlspecialchars($fila['cargo']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick='abrirEditarConvenio(<?= json_encode($fila) ?>)' class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button onclick='abrirModalEliminarConvenio(<?= json_encode($fila) ?>)' class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalEliminarConvenio" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter mb-2">¿Eliminar Convenio?</h3>
            <p class="text-slate-500 text-sm mb-6">Esta acción no se puede deshacer. Se eliminará a <span id="nombreEmpresaEliminar" class="font-bold text-slate-700"></span> del sistema permanentemente.</p>
            
            <form action="index.php" method="POST" class="flex gap-3">
                <input type="hidden" name="accion" value="eliminarConvenio">    
                <input type="hidden" name="id_convenio_borrar" id="idConvenioEliminar">
                
                <button type="button" onclick="cerrarModalEliminar()" class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-3 bg-red-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-red-200 hover:bg-red-600 transition-all">
                    Eliminar Ahora
                </button>
            </form>
        </div>
    </div>
</div>

<div id="modalEditarConvenio" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col border border-slate-100 animate-in fade-in zoom-in duration-200">
        
        <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Editar Empresa</h3>
                <p class="text-blue-500 text-[10px] font-bold uppercase tracking-widest mt-0.5">Sincronización automática con registros pendientes activa</p>
            </div>
            <button onclick="cerrarEditarConvenio()" class="text-slate-400 hover:text-slate-600 text-2xl cursor-pointer">✕</button>
        </div>

        <form action="index.php" method="POST" class="overflow-y-auto p-8 bg-slate-50/30">
            <input type="hidden" name="accion" value="actualizarConvenio">
            <input type="hidden" name="id_convenio" id="edit_conv_id">
            <input type="hidden" name="cif_original" id="edit_conv_cif_old">
            <input type="hidden" name="nombre_original" id="edit_conv_nombre_old">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Nombre Empresa</label>
                    <input type="text" name="nombre_empresa" id="edit_conv_nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CIF</label>
                    <input type="text" name="cif" id="edit_conv_cif" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Teléfono</label>
                    <input type="text" name="telefono" id="edit_conv_tel" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Email</label>
                    <input type="email" name="mail" id="edit_conv_mail" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Fax</label>
                    <input type="text" name="fax" id="edit_conv_fax" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Dirección</label>
                    <input type="text" name="direccion" id="edit_conv_dir" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Municipio</label>
                    <input type="text" name="municipio" id="edit_conv_mun" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CP</label>
                    <input type="text" name="cp" id="edit_conv_cp" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">País</label>
                    <input type="text" name="pais" id="edit_conv_pais" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div class="md:col-span-3 mt-4 pt-4 border-t border-slate-200 flex items-center gap-2">
                    <span class="text-[10px] font-black bg-slate-800 text-white px-2 py-0.5 rounded uppercase">Representante Legal</span>
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Nombre Completo</label>
                    <input type="text" name="nombre_representante" id="edit_conv_rep_nom" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">DNI/NIE</label>
                    <input type="text" name="dni_representante" id="edit_conv_rep_dni" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Cargo</label>
                    <input type="text" name="cargo" id="edit_conv_rep_cargo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
            </div>

            <div class="flex gap-4 mt-10">
                <button type="button" onclick="cerrarEditarConvenio()" 
                        class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                    Guardar y Sincronizar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalEliminarConvenio(datos) {
    document.getElementById('idConvenioEliminar').value = datos.id_convenio;
    document.getElementById('nombreEmpresaEliminar').innerText = datos.nombre_empresa;
    document.getElementById('modalEliminarConvenio').style.display = 'flex';
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminarConvenio').style.display = 'none';
}

function abrirEditarConvenio(datos) {
    // Control e IDs
    document.getElementById('edit_conv_id').value = datos.id_convenio;
    document.getElementById('edit_conv_cif_old').value = datos.cif;
    document.getElementById('edit_conv_nombre_old').value = datos.nombre_empresa;

    // Campos de texto 
    document.getElementById('edit_conv_nombre').value = datos.nombre_empresa;
    document.getElementById('edit_conv_cif').value = datos.cif;
    document.getElementById('edit_conv_tel').value = datos.telefono;
    document.getElementById('edit_conv_mail').value = datos.mail;
    document.getElementById('edit_conv_fax').value = datos.fax;
    document.getElementById('edit_conv_dir').value = datos.direccion;
    document.getElementById('edit_conv_mun').value = datos.municipio;
    document.getElementById('edit_conv_cp').value = datos.cp;
    document.getElementById('edit_conv_pais').value = datos.pais;
    document.getElementById('edit_conv_rep_nom').value = datos.nombre_representante;
    document.getElementById('edit_conv_rep_dni').value = datos.dni_representante;
    document.getElementById('edit_conv_rep_cargo').value = datos.cargo;

    document.getElementById('modalEditarConvenio').style.display = 'flex';
}

function cerrarEditarConvenio() {
    document.getElementById('modalEditarConvenio').style.display = 'none';
}
</script>