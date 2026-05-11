<?php
// Vista/Tutores/Components/Modales_Seguimiento.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
validarAcceso('tutor');
?>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL: VER DOCUMENTOS (Plan Formativo / Fichas)                   -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="modalVerDocumentos" style="display:none"
     class="fixed inset-0 bg-black/50 z-[200] flex items-center justify-center p-4"
     id="segModalVerDocumentos_backdrop">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 border border-slate-100" onclick="event.stopPropagation()">

        <!-- Cabecera -->
        <div class="flex items-center justify-between mb-6">
            <h3 id="modalDocTitulo" class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">📁</span>
                Documentos
            </h3>
            <button onclick="cerrarModalDocumentos()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <!-- Lista de archivos -->
        <div id="modalDocLista" class="mb-6 min-h-[80px] max-h-64 overflow-y-auto space-y-2">
            <div class="text-center py-8 text-slate-400 text-xs italic font-bold">Cargando...</div>
        </div>

        <!-- Subir documento -->
        <div class="border-t border-slate-100 pt-5">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Subir documento</p>
            <label class="block w-full cursor-pointer mb-4">
                <div id="docDropZone"
                     class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center hover:border-orange-300 hover:bg-orange-50/30 transition-all">
                    <p class="text-xl mb-1">📂</p>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Clic para seleccionar</p>
                    <p class="text-[9px] text-slate-400 font-bold mt-1" id="docNombreFichero">Ningún fichero seleccionado</p>
                </div>
                <input type="file" id="docInputFichero" class="hidden" onchange="onDocFicheroSeleccionado(this)">
            </label>

            <!-- Barra de progreso -->
            <div id="docProgreso" style="display:none" class="mb-4">
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div id="docBarraProgreso" class="bg-orange-600 h-1.5 rounded-full transition-all" style="width:0%"></div>
                </div>
                <p id="docTextoProgreso" class="text-[9px] text-slate-500 font-bold mt-1 text-center"></p>
            </div>

            <div class="flex gap-3 justify-end">
                <button onclick="cerrarModalDocumentos()"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                    Cancelar
                </button>
                <button id="docBtnSubir" onclick="subirDocumento()" disabled
                    class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed uppercase tracking-wide">
                    Subir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal confirmación eliminar — z MAYOR que el padre para no quedar bloqueado -->
<div id="segModalConfirmarEliminar" style="display:none"
     class="fixed inset-0 bg-black/60 z-[300] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs">🗑</span>
                ELIMINAR ARCHIVO
            </h3>
            <button onclick="document.getElementById('segModalConfirmarEliminar').style.display='none'"
                class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-600 text-center mb-2 leading-relaxed">
            ¿Seguro que quieres eliminar el archivo?
        </p>
        <p id="segEliminarNombreArchivo" class="text-xs font-black text-slate-900 text-center mb-6 break-all"></p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('segModalConfirmarEliminar').style.display='none'"
                class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button id="segBtnConfirmarEliminar"
                class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition-all shadow-md cursor-pointer">
                Sí, eliminar
            </button>
        </div>
    </div>
</div>

<!-- Modal advertencia archivo duplicado — z MAYOR que todos los anteriores -->
<div id="segModalDuplicado" style="display:none"
     class="fixed inset-0 bg-black/60 z-[400] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-white text-xs">⚠</span>
                ARCHIVO DUPLICADO
            </h3>
            <button onclick="cerrarModalDuplicado()"
                class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-600 leading-relaxed mb-3">
            Ya existe un archivo con el mismo nombre en esta carpeta:
        </p>
        <p id="segDuplicadoNombreArchivo"
           class="text-xs font-black text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-center break-all mb-4"></p>
        <p class="text-[10px] text-slate-500 leading-relaxed mb-6">
            Si continúas, el nuevo archivo se guardará con un número añadido al final del nombre para no sobrescribir el existente.
        </p>
        <div class="flex gap-3 justify-center">
            <button onclick="cerrarModalDuplicado()"
                class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                Cancelar
            </button>
            <button id="segBtnConfirmarSubida"
                class="px-5 py-2.5 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-all shadow-md cursor-pointer uppercase tracking-wide">
                Continuar de todas formas
            </button>
        </div>
    </div>
</div>

<script>
// ─── Estado del modal ────────────────────────────────────────────────────────
let _docTipo             = '';   // 'plan_formativo' | 'fichas'
let _docCiclo            = '';   // ej: '2DAW'
let _docAlumno           = '';   // ej: 'GARCIA_SANCHEZ_JOSUE'
let _archivoAEliminar    = '';
let _docArchivosActuales = [];   // nombres de archivos ya subidos en esta carpeta

// ─── Abrir modal ─────────────────────────────────────────────────────────────
// tipo: 'plan_formativo' | 'fichas'
// alumno: nombre saneado del alumno (pasado desde el botón en la tabla)
function abrirModalDocumentos(tipo, alumno) {
    _docTipo             = tipo;
    _docCiclo            = window.SEGUIMIENTO_CICLO || '';
    _docAlumno           = alumno;
    _docArchivosActuales = [];

    const titulo = tipo === 'plan_formativo' ? 'Plan Formativo Firmado' : 'Fichas Firmadas';
    document.getElementById('modalDocTitulo').innerHTML =
        `<span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">📁</span> ${titulo}`;

    // Reset
    document.getElementById('docInputFichero').value = '';
    document.getElementById('docNombreFichero').textContent = 'Ningún fichero seleccionado';
    document.getElementById('docBtnSubir').disabled = true;
    document.getElementById('docProgreso').style.display = 'none';
    document.getElementById('docBarraProgreso').classList.remove('bg-red-500');
    document.getElementById('docBarraProgreso').classList.add('bg-orange-600');

    document.getElementById('modalVerDocumentos').style.display = 'flex';
    cargarListaDocumentos();
}

function cerrarModalDocumentos() {
    document.getElementById('modalVerDocumentos').style.display = 'none';
}

// ─── Cargar lista AJAX ───────────────────────────────────────────────────────
async function cargarListaDocumentos() {
    const lista = document.getElementById('modalDocLista');
    lista.innerHTML = '<div class="text-center py-8 text-slate-400 text-xs italic font-bold">Cargando...</div>';

    try {
        const url = `index.php?controlador=Tutores&accion=seguimientoListar`
            + `&tipo=${encodeURIComponent(_docTipo)}`
            + `&ciclo=${encodeURIComponent(_docCiclo)}`
            + `&alumno=${encodeURIComponent(_docAlumno)}`;

        const res  = await fetch(url);
        const data = await res.json();

        if (!data.success) {
            lista.innerHTML = `<div class="text-center py-8 text-red-400 text-xs font-bold">${data.error ?? 'Error al cargar.'}</div>`;
            return;
        }

        _docArchivosActuales = data.archivos || [];

        if (data.archivos.length === 0) {
            lista.innerHTML = '<div class="text-center py-8 text-slate-400 text-xs italic font-bold">No hay documentos subidos todavía.</div>';
            return;
        }

        lista.innerHTML = data.archivos.map(nombre => {
            const urlDescarga = `index.php?controlador=Tutores&accion=seguimientoDescargar&tipo=${encodeURIComponent(_docTipo)}&ciclo=${encodeURIComponent(_docCiclo)}&alumno=${encodeURIComponent(_docAlumno)}&nombre=${encodeURIComponent(nombre)}`;
            return `
            <div class="flex items-center justify-between px-3 py-2.5 bg-slate-50 rounded-xl border border-slate-100 group">
                <a href="${urlDescarga}" download
                   class="flex items-center gap-2 overflow-hidden flex-1 min-w-0 hover:text-orange-600 transition-colors"
                   title="Descargar ${nombre}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-500 shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span class="text-[10px] font-bold text-slate-700 truncate group-hover:text-orange-600 transition-colors">${nombre}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300 shrink-0 group-hover:text-orange-500 transition-colors"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </a>
                <button type="button"
                    onclick='pedirConfirmacionEliminar(${JSON.stringify(nombre)})'
                    class="ml-3 text-[9px] font-black text-slate-300 hover:text-red-500 uppercase tracking-widest cursor-pointer transition-colors shrink-0 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    Eliminar
                </button>
            </div>`;
        }).join('');

    } catch (e) {
        lista.innerHTML = '<div class="text-center py-8 text-red-400 text-xs font-bold">Error de conexión.</div>';
    }
}

// ─── Seleccionar fichero ─────────────────────────────────────────────────────
function onDocFicheroSeleccionado(input) {
    const btn   = document.getElementById('docBtnSubir');
    const label = document.getElementById('docNombreFichero');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        btn.disabled = false;
    } else {
        label.textContent = 'Ningún fichero seleccionado';
        btn.disabled = true;
    }
}

// ─── Subir documento ─────────────────────────────────────────────────────────
function _sanitizarNombreArchivo(nombre) {
    return nombre.replace(/[^a-zA-Z0-9._\-]/g, '_');
}

async function subirDocumento() {
    const input = document.getElementById('docInputFichero');
    if (!input.files || !input.files[0]) return;

    const nombreOriginal   = input.files[0].name;
    const nombreSanitizado = _sanitizarNombreArchivo(nombreOriginal);

    const hayDuplicado = _docArchivosActuales.some(existente =>
        existente.toLowerCase() === nombreOriginal.toLowerCase() ||
        existente.toLowerCase() === nombreSanitizado.toLowerCase()
    );

    if (hayDuplicado) {
        mostrarModalDuplicado(nombreOriginal);
        return;
    }

    await _ejecutarSubida();
}

async function _ejecutarSubida() {
    const input = document.getElementById('docInputFichero');
    const btn   = document.getElementById('docBtnSubir');

    btn.disabled = true;
    document.getElementById('docProgreso').style.display = 'block';
    document.getElementById('docBarraProgreso').style.width = '30%';
    document.getElementById('docTextoProgreso').textContent = 'Subiendo...';

    const formData = new FormData();
    formData.append('fichero', input.files[0]);
    formData.append('tipo',    _docTipo);
    formData.append('ciclo',   _docCiclo);
    formData.append('alumno',  _docAlumno);

    try {
        const res  = await fetch('index.php?controlador=Tutores&accion=seguimientoSubir', {
            method: 'POST',
            body:   formData,
        });
        const data = await res.json();

        document.getElementById('docBarraProgreso').style.width = '100%';

        if (data.success) {
            document.getElementById('docTextoProgreso').textContent = '✓ Subido correctamente';
            input.value = '';
            document.getElementById('docNombreFichero').textContent = 'Ningún fichero seleccionado';
            setTimeout(() => {
                document.getElementById('docProgreso').style.display = 'none';
                cargarListaDocumentos();
                location.href = 'index.php?controlador=Tutores&accion=mostrarPanel&tab=4';
            }, 900);
        } else {
            document.getElementById('docTextoProgreso').textContent = '✗ Error: ' + (data.error ?? 'desconocido');
            document.getElementById('docBarraProgreso').classList.replace('bg-orange-600', 'bg-red-500');
            btn.disabled = false;
        }
    } catch (e) {
        document.getElementById('docTextoProgreso').textContent = '✗ Error de conexión.';
        btn.disabled = false;
    }
}

function mostrarModalDuplicado(nombre) {
    document.getElementById('segDuplicadoNombreArchivo').textContent = nombre;
    document.getElementById('segBtnConfirmarSubida').onclick = async () => {
        cerrarModalDuplicado();
        await _ejecutarSubida();
    };
    document.getElementById('segModalDuplicado').style.display = 'flex';
}

function cerrarModalDuplicado() {
    document.getElementById('segModalDuplicado').style.display = 'none';
}

// ─── Eliminar documento ──────────────────────────────────────────────────────
function pedirConfirmacionEliminar(nombre) {
    _archivoAEliminar = nombre;
    document.getElementById('segEliminarNombreArchivo').textContent = nombre;
    document.getElementById('segModalConfirmarEliminar').style.display = 'flex';

    // Asignar handler en cada apertura para que tenga el nombre correcto
    document.getElementById('segBtnConfirmarEliminar').onclick = segConfirmarEliminarDoc;
}

async function segConfirmarEliminarDoc() {
    document.getElementById('segModalConfirmarEliminar').style.display = 'none';

    try {
        const res  = await fetch('index.php?controlador=Tutores&accion=seguimientoEliminar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `tipo=${encodeURIComponent(_docTipo)}&ciclo=${encodeURIComponent(_docCiclo)}&alumno=${encodeURIComponent(_docAlumno)}&nombre=${encodeURIComponent(_archivoAEliminar)}`,
        });
        const texto = await res.text();
        let data;
        try {
            const inicio = texto.indexOf('{');
            data = JSON.parse(inicio >= 0 ? texto.substring(inicio) : texto);
        } catch(parseErr) {
            alert('Respuesta inesperada del servidor: ' + texto.substring(0, 200));
            return;
        }

        if (data.success) {
            cargarListaDocumentos();
            location.href = 'index.php?controlador=Tutores&accion=mostrarPanel&tab=4';
        } else {
            alert('Error al eliminar: ' + (data.error ?? 'desconocido'));
        }
    } catch (e) {
        alert('Error de conexión al eliminar.');
    }
}

// Cerrar modal principal solo si NO hay modal de confirmación abierto
document.addEventListener('DOMContentLoaded', function() {
    const backdrop = document.getElementById('segModalVerDocumentos_backdrop');
    if (backdrop) {
        backdrop.addEventListener('click', function(e) {
            if (e.target !== this) return;
            const confirmAbierto = document.getElementById('segModalConfirmarEliminar').style.display === 'flex';
            if (!confirmAbierto) cerrarModalDocumentos();
        });
    }
});

</script>
