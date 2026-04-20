<?php

// Vista/Admin/Components/Modales_TC.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
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
            
            <form id="formEliminarReal" action="index.php" method="POST" class="flex gap-3">
                <input type="hidden" name="accion" value="eliminarConvenio">    
                <input type="hidden" name="id_convenio_borrar" id="idConvenioEliminar">
                
                <button type="button" onclick="cerrarModalEliminar()" class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="abrirConfirmacionFinal()" class="flex-1 py-3 bg-red-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-red-200 hover:bg-red-600 transition-all">
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
                <p class="text-blue-500 text-[10px] font-bold uppercase tracking-widest mt-0.5">Sincronización automática activa</p>
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
                    <input type="text" name="nombre_empresa" id="edit_conv_nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CIF</label>
                    <input type="text" name="cif" id="edit_conv_cif" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Teléfono</label>
                    <input type="text" name="telefono" id="edit_conv_tel" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Email</label>
                    <input type="email" name="mail" id="edit_conv_mail" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Fax</label>
                    <input type="text" name="fax" id="edit_conv_fax" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Dirección</label>
                    <input type="text" name="direccion" id="edit_conv_dir" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Municipio</label>
                    <input type="text" name="municipio" id="edit_conv_mun" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CP</label>
                    <input type="text" name="cp" id="edit_conv_cp" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">País</label>
                    <input type="text" name="pais" id="edit_conv_pais" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none">
                </div>

                <div class="md:col-span-3 mt-4 pt-4 border-t border-slate-200 flex items-center gap-2">
                    <span class="text-[10px] font-black bg-slate-800 text-white px-2 py-0.5 rounded uppercase">Representante Legal</span>
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Nombre Completo</label>
                    <input type="text" name="nombre_representante" id="edit_conv_rep_nom" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">DNI/NIE</label>
                    <input type="text" name="dni_representante" id="edit_conv_rep_dni" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Cargo</label>
                    <input type="text" name="cargo" id="edit_conv_rep_cargo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none">
                </div>
            </div>

            <div class="flex gap-4 mt-10">
                <button type="button" onclick="cerrarEditarConvenio()" class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Guardar y Sincronizar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalConfirmacionFinal" style="display:none" class="fixed inset-0 bg-red-900/20 z-[110] flex items-center justify-center p-4 backdrop-blur-md">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xs overflow-hidden border-2 border-red-100 animate-in zoom-in duration-150">
        <div class="p-6 text-center">
            <div class="text-red-500 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">¿ESTÁS SEGURO?</h3>
            <p class="text-[11px] text-slate-500 font-bold uppercase mt-2 leading-tight">Advertencia final: eliminación permanente de la base de datos.</p>
        </div>
        <div class="flex flex-col border-t border-slate-100">
            <button id="btnEjecutarEliminacionReal" class="py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all">SÍ, BORRAR DEFINITIVAMENTE</button>
            <button onclick="cerrarConfirmacionFinal()" class="py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 bg-slate-50 transition-all">Mejor no, volver</button>
        </div>
    </div>
</div>

<script>
function abrirConfirmacionFinal() {
    document.getElementById('modalConfirmacionFinal').style.display = 'flex';
    document.getElementById('btnEjecutarEliminacionReal').onclick = function() {
        document.getElementById('formEliminarReal').submit();
    };
}
function cerrarConfirmacionFinal() {
    document.getElementById('modalConfirmacionFinal').style.display = 'none';
}
function abrirModalEliminarConvenio(datos) {
    document.getElementById('idConvenioEliminar').value = datos.id_convenio;
    document.getElementById('nombreEmpresaEliminar').innerText = datos.nombre_empresa;
    document.getElementById('modalEliminarConvenio').style.display = 'flex';
}
function cerrarModalEliminar() {
    document.getElementById('modalEliminarConvenio').style.display = 'none';
    document.getElementById('modalConfirmacionFinal').style.display = 'none';
}
function abrirEditarConvenio(datos) {
    document.getElementById('edit_conv_id').value = datos.id_convenio;
    document.getElementById('edit_conv_cif_old').value = datos.cif;
    document.getElementById('edit_conv_nombre_old').value = datos.nombre_empresa;
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