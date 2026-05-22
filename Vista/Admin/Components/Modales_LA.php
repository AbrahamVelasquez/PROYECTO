<?php

/**
 * Vista/Admin/Components/Modales_LA.php — Modales del listado de alumnos (admin)
 *
 * Contiene los overlays para las acciones del admin sobre alumnos:
 *   - Ver firma: muestra la imagen de firma capturada para una asignación concreta.
 *   - Confirmar eliminación de alumno del sistema.
 *
 * Los modales se activan desde Listado_Alumnos.php con JS que inyecta
 * el ID del alumno o la ruta de la firma en los campos correspondientes.
 */

require_once __DIR__ . '/../../../Seguridad/Control_Accesos.php';

validarAcceso('admin');

?>

<div id="modal-firma-admin" style="display:none"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white text-xs">✍️</span>
                CONFIRMAR FIRMA
            </h3>
            <button onclick="cerrarModalFirmaAdmin()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Confirmar que este alumno está firmado?</p>
        <p id="firma-admin-nombre" class="text-sm font-black text-slate-900 mb-4 text-center uppercase"></p>

        <div class="mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 text-center">
                Número de anexo <span class="text-emerald-600">*</span>
            </label>
            <input type="text" id="firma-admin-anexo" placeholder="Ej: 1"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-center outline-none focus:ring-2 focus:ring-emerald-200 transition-all"
                oninput="document.getElementById('firma-admin-error').style.display='none'">
            <p id="firma-admin-error" style="display:none"
                class="text-[10px] font-bold text-red-500 text-center mt-2 uppercase tracking-wide">
                Debes introducir el número de anexo antes de confirmar.
            </p>
        </div>

        <form method="POST" action="index.php?accion=firmarAlumnoAdmin">
            <input type="hidden" name="id_asignacion" id="firma-admin-id">
            <input type="hidden" name="anexo"         id="firma-admin-anexo-hidden">
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="cerrarModalFirmaAdmin()"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
                <button  type="button" onclick="confirmarFirmaAdmin()"
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition-all shadow-md cursor-pointer">Sí, confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalFirmaAdmin(idAsignacion, nombre) {
    document.getElementById('firma-admin-id').value        = idAsignacion;
    document.getElementById('firma-admin-nombre').textContent = nombre;
    document.getElementById('firma-admin-anexo').value     = '';
    document.getElementById('modal-firma-admin').style.display = 'flex';
}

function cerrarModalFirmaAdmin() {
    document.getElementById('modal-firma-admin').style.display = 'none';
}

function confirmarFirmaAdmin() {
    const anexo = document.getElementById('firma-admin-anexo').value.trim();
    if (!anexo) {
        document.getElementById('firma-admin-error').style.display = 'block';
        return;
    }
    document.getElementById('firma-admin-anexo-hidden').value = anexo;
    document.querySelector('#modal-firma-admin form').submit();
}

// Sincroniza el input visible con el hidden antes de enviar
document.querySelector('#modal-firma-admin form').addEventListener('submit', () => {
    document.getElementById('firma-admin-anexo-hidden').value =
        document.getElementById('firma-admin-anexo').value;
});
</script>