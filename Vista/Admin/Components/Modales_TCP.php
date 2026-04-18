<?php 
// Vista/Admin/Components/Modales_TC.php
?>
<div id="modalConfirmarValidacion" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[110] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter mb-2">Confirmar Validación</h3>
            <p class="text-slate-500 text-sm leading-relaxed">
                ¿Estás seguro de que deseas incorporar a <span id="nombre_empresa_confirmar" class="font-bold text-slate-800"></span> al sistema de convenios oficiales directamente?
            </p>
        </div>
        <div class="flex border-t border-slate-100">
            <button onclick="cerrarConfirmarValidacion()" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                Cancelar
            </button>
            <button id="btn-confirmar-submit" class="flex-1 py-4 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all">
                Sí, Incorporar
            </button>
        </div>
    </div>
</div>

<div id="modalRevisionConvenio" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="px-8 py-5 bg-white border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Revisión de Solicitud</h3>
                <p class="text-emerald-500 text-[10px] font-bold uppercase tracking-widest mt-0.5">Verifique los datos antes de la incorporación definitiva</p>
            </div>
            <button onclick="cerrarModalRevision()" class="text-slate-400 hover:text-slate-600 transition-colors text-2xl cursor-pointer">✕</button>
        </div>

        <form id="formRevision" action="index.php" method="POST" class="overflow-y-auto p-8 bg-slate-50/30">
            <input type="hidden" name="accion" value="validarConvenio">
            <input type="hidden" name="solo_guardar" value="1">
            <input type="hidden" name="id_convenio_nuevo" id="rev_id">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Nombre Empresa</label>
                    <input type="text" name="nombre_empresa" id="rev_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CIF</label>
                    <input type="text" name="cif" id="rev_cif" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Teléfono</label>
                    <input type="text" name="telefono" id="rev_tel" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Email</label>
                    <input type="email" name="mail" id="rev_mail" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Fax</label>
                    <input type="text" name="fax" id="rev_fax" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Dirección</label>
                    <input type="text" name="direccion" id="rev_dir" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Municipio</label>
                    <input type="text" name="municipio" id="rev_mun" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CP</label>
                    <input type="text" name="cp" id="rev_cp" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">País</label>
                    <input type="text" name="pais" id="rev_pais" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div class="md:col-span-3 mt-4 pt-4 border-t border-slate-200 flex items-center gap-2">
                    <span class="text-[10px] font-black bg-emerald-600 text-white px-3 py-1 rounded-lg shadow-sm shadow-emerald-100">REPRESENTANTE LEGAL</span>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Nombre Representante</label>
                    <input type="text" name="nombre_representante" id="rev_rep_nom" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">DNI</label>
                    <input type="text" name="dni_representante" id="rev_rep_dni" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Cargo</label>
                    <input type="text" name="cargo" id="rev_rep_cargo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
            </div>

            <div class="flex gap-4 mt-10 w-full">
                <button type="button" onclick="confirmarEliminacionPendiente()" class="px-6 py-3 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-100 transition-all flex items-center gap-2 border border-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Eliminar Solicitud
                </button>
                <div class="flex-1"></div> 
                <button type="button" onclick="cerrarModalRevision()" class="py-3 px-6 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Cancelar</button>
                <button type="submit" class="py-3 px-8 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    Solo Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalConfirmarEliminar" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[120] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter mb-2">¿Eliminar Solicitud?</h3>
            <p class="text-slate-500 text-sm leading-relaxed">
                Esta acción eliminará permanentemente a <span id="nombre_empresa_eliminar" class="font-bold text-slate-800"></span>. No podrás deshacer este cambio.
            </p>
        </div>
        <div class="flex border-t border-slate-100">
            <button onclick="cerrarModalEliminar()" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">Cancelar</button>
            <button id="btn-confirmar-eliminar" class="flex-1 py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all">Sí, Eliminar Todo</button>
        </div>
    </div>
</div>

<div id="modalExitoGuardado" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[120] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-200">
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter mb-2">Cambios Guardados</h3>
            <p class="text-slate-500 text-sm">Los datos se han actualizado correctamente en el borrador.</p>
        </div>
        <div class="flex border-t border-slate-100 p-4 gap-3 bg-slate-50/50">
            <button onclick="cerrarTodoYRecargar()" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-100 transition-all">Volver a la tabla</button>
            <button onclick="ocultarModalExito()" class="flex-1 py-3 bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-900 transition-all">Seguir Editando</button>
        </div>
    </div>
</div>

<script>
let idParaValidar = null;

function confirmarValidacionDirecta(id, nombre) {
    idParaValidar = id;
    document.getElementById('nombre_empresa_confirmar').innerText = nombre;
    document.getElementById('modalConfirmarValidacion').style.display = 'flex';
    document.getElementById('btn-confirmar-submit').onclick = function() {
        document.getElementById('form-validar-' + idParaValidar).submit();
    };
}

function cerrarConfirmarValidacion() {
    document.getElementById('modalConfirmarValidacion').style.display = 'none';
    idParaValidar = null;
}

window.onclick = function(event) {
    const modalValidar = document.getElementById('modalConfirmarValidacion');
    const modalRevision = document.getElementById('modalRevisionConvenio');
    if (event.target == modalValidar) cerrarConfirmarValidacion();
    if (event.target == modalRevision) cerrarModalRevision();
}

function abrirModalRevision(datos) {
    document.getElementById('rev_id').value = datos.id_convenio_nuevo;
    document.getElementById('rev_nombre').value = datos.nombre_empresa;
    document.getElementById('rev_cif').value = datos.cif;
    document.getElementById('rev_tel').value = datos.telefono;
    document.getElementById('rev_mail').value = datos.mail;
    document.getElementById('rev_fax').value = datos.fax;
    document.getElementById('rev_dir').value = datos.direccion;
    document.getElementById('rev_mun').value = datos.municipio;
    document.getElementById('rev_cp').value = datos.cp;
    document.getElementById('rev_pais').value = datos.pais;
    document.getElementById('rev_rep_nom').value = datos.nombre_representante;
    document.getElementById('rev_rep_dni').value = datos.dni_representante;
    document.getElementById('rev_rep_cargo').value = datos.cargo;
    document.getElementById('modalRevisionConvenio').style.display = 'flex';
}

function cerrarModalRevision() {
    document.getElementById('modalRevisionConvenio').style.display = 'none';
}

function confirmarEliminacionPendiente() {
    const id = document.getElementById('rev_id').value;
    const nombre = document.getElementById('rev_nombre').value;
    document.getElementById('nombre_empresa_eliminar').innerText = nombre;
    document.getElementById('modalConfirmarEliminar').style.display = 'flex';
    document.getElementById('btn-confirmar-eliminar').onclick = function() {
        window.location.href = `index.php?accion=eliminarConvenioCompleto&id=${id}`;
    };
}

function cerrarModalEliminar() {
    document.getElementById('modalConfirmarEliminar').style.display = 'none';
}

document.getElementById('formRevision').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        document.getElementById('modalExitoGuardado').style.display = 'flex';
    })
    .catch(error => alert("Error al guardar"));
};

function ocultarModalExito() {
    document.getElementById('modalExitoGuardado').style.display = 'none';
}

function cerrarTodoYRecargar() {
    window.location.reload();
}
</script>