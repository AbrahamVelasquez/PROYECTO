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

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL: SUBIDA MASIVA                                               -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div id="modalSubidaMasiva" style="display:none"
     class="fixed inset-0 bg-black/50 z-[200] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalMasivo()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col border border-slate-100 overflow-hidden max-h-[90vh]" onclick="event.stopPropagation()">

        <!-- Cabecera -->
        <div class="flex items-center justify-between px-8 pt-7 pb-5 shrink-0 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </span>
                Subida Masiva de Documentos
            </h3>
            <button onclick="cerrarModalMasivo()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer leading-none">✕</button>
        </div>

        <!-- Body (scrollable) -->
        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-5">

            <!-- Selector tipo -->
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Tipo de documento</p>
                <div class="flex gap-3">
                    <button type="button" id="masivoBtnPF" onclick="masivoSetTipo('plan_formativo')"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-orange-500 bg-orange-50 text-orange-700 text-[10px] font-black uppercase tracking-wide cursor-pointer transition-all">
                        📋 Plan Formativo
                    </button>
                    <button type="button" id="masivoBtnFichas" onclick="masivoSetTipo('fichas')"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200 text-slate-500 text-[10px] font-black uppercase tracking-wide cursor-pointer transition-all hover:border-orange-300 hover:bg-orange-50/50 hover:text-orange-600">
                        📄 Fichas
                    </button>
                </div>
            </div>

            <!-- Info formato -->
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1.5">Formato de nombre de archivo esperado</p>
                <p class="text-[10px] font-bold text-slate-600 leading-relaxed">
                    El archivo debe comenzar por <span class="font-black text-slate-800">Apellido1_Apellido2_Ciclo</span>
                    seguido de cualquier sufijo.<br>
                    Plan Formativo: <span class="text-orange-600 font-black">Garcia_Lopez_DAW2.pdf</span>
                    · <span class="text-orange-600 font-black">Garcia_Lopez_DAW2_signed.pdf</span><br>
                    Fichas: <span class="text-orange-600 font-black">Garcia_Lopez_DAW2_marzo26.pdf</span>
                    · <span class="text-orange-600 font-black">Garcia_Lopez_DAW2_abril26_corregida.pdf</span>
                </p>
            </div>

            <!-- Drop zone -->
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Archivos</p>
                <label class="block cursor-pointer">
                    <div id="masivoDropZone"
                         class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-orange-300 hover:bg-orange-50/30 transition-all">
                        <p class="text-2xl mb-1.5">📂</p>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Clic para seleccionar múltiples archivos</p>
                        <p class="text-[9px] text-slate-400 font-bold mt-1" id="masivoNombreFicheros">Ningún fichero seleccionado</p>
                    </div>
                    <input type="file" id="masivoInputFicheros" class="hidden" multiple onchange="onMasivoFilesSelected(this)">
                </label>
            </div>

            <!-- Lista de asignaciones detectadas -->
            <div id="masivoListaWrapper" style="display:none">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Asignación detectada</p>
                <div id="masivoLista" class="space-y-2"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between shrink-0">
            <p id="masivoResumenTexto" class="text-[10px] font-bold text-slate-500"></p>
            <div class="flex gap-3">
                <button onclick="cerrarModalMasivo()"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">
                    Cancelar
                </button>
                <button id="masivoBtnSubir" onclick="subirMasivo()" disabled
                    class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed uppercase tracking-wide">
                    Subir archivos
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ─── Subida Masiva ────────────────────────────────────────────────────────────
let _masivoTipo     = 'plan_formativo';
let _masivoArchivos = [];

function abrirModalMasivo() {
    _masivoTipo     = 'plan_formativo';
    _masivoArchivos = [];
    document.getElementById('masivoInputFicheros').value         = '';
    document.getElementById('masivoNombreFicheros').textContent  = 'Ningún fichero seleccionado';
    document.getElementById('masivoListaWrapper').style.display  = 'none';
    document.getElementById('masivoLista').innerHTML             = '';
    document.getElementById('masivoBtnSubir').disabled           = true;
    document.getElementById('masivoBtnSubir').textContent        = 'Subir archivos';
    document.getElementById('masivoResumenTexto').textContent    = '';
    _masivoActualizarBotonesTipo();
    document.getElementById('modalSubidaMasiva').style.display   = 'flex';
}

function cerrarModalMasivo() {
    document.getElementById('modalSubidaMasiva').style.display = 'none';
}

function masivoSetTipo(tipo) {
    _masivoTipo = tipo;
    _masivoActualizarBotonesTipo();
    if (_masivoArchivos.length > 0) _masivoMostrarPreview(_masivoArchivos.map(a => a.file));
}

function _masivoActualizarBotonesTipo() {
    const activo   = 'flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-orange-500 bg-orange-50 text-orange-700 text-[10px] font-black uppercase tracking-wide cursor-pointer transition-all';
    const inactivo = 'flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200 text-slate-500 text-[10px] font-black uppercase tracking-wide cursor-pointer transition-all hover:border-orange-300 hover:bg-orange-50/50 hover:text-orange-600';
    document.getElementById('masivoBtnPF').className     = _masivoTipo === 'plan_formativo' ? activo : inactivo;
    document.getElementById('masivoBtnFichas').className = _masivoTipo === 'fichas'          ? activo : inactivo;
}

function _masivoNormalizar(s) {
    const s2 = s.normalize('NFD').replace(/[̀-ͯ]/g, '').toUpperCase().replace(/[^A-Z0-9]+/g, '_');
    return s2.replace(/^_+/, '').replace(/_+$/, '');
}

async function onMasivoFilesSelected(input) {
    if (!input.files || !input.files.length) return;
    const files = Array.from(input.files);
    document.getElementById('masivoNombreFicheros').textContent =
        files.length === 1 ? files[0].name : `${files.length} archivos seleccionados`;
    await _masivoMostrarPreview(files);
}

async function _masivoMostrarPreview(files) {
    // Recoge todos los alumnos del DOM (incluyendo filas ocultas por filtros/paginación)
    const alumnos = Array.from(document.querySelectorAll('tr.seg-fila')).map(tr => ({
        carpeta: tr.dataset.carpeta || '',
        nombre:  tr.dataset.nombre  || '',
        flat:    (tr.dataset.carpeta || '').replace(/_/g, ''),
    }));

    const ciclo = window.SEGUIMIENTO_CICLO || '';

    // Para cada alumno con ficheros asignados, cargamos sus archivos actuales
    const archivosExistentes = {};
    const alumnosImplicados = new Set();
    files.forEach(file => {
        const baseNorm = _masivoNormalizar(file.name.replace(/\.[^.]+$/, ''));
        const match = alumnos.find(al => baseNorm.startsWith(_masivoNormalizar(al.carpeta)));
        if (match) alumnosImplicados.add(match.carpeta);
    });

    await Promise.all([...alumnosImplicados].map(async carpeta => {
        try {
            const res  = await fetch(`index.php?controlador=Tutores&accion=seguimientoListar&tipo=${encodeURIComponent(_masivoTipo)}&ciclo=${encodeURIComponent(ciclo)}&alumno=${encodeURIComponent(carpeta)}`);
            const data = await res.json();
            if (data.success) archivosExistentes[carpeta] = (data.archivos || []).map(f => f.toLowerCase());
        } catch { archivosExistentes[carpeta] = []; }
    }));

    _masivoArchivos = files.map((file, i) => {
        // El prefijo del alumno es GARCIA_LOPEZ_DAW2 — el fichero debe empezar por él
        const base     = file.name.replace(/\.[^.]+$/, '');           // sin extensión
        const baseNorm = _masivoNormalizar(base);                     // normalizado
        const match    = alumnos.find(al => {
            const prefijo = _masivoNormalizar(al.carpeta);
            return baseNorm.startsWith(prefijo);
        });
        const nombreNorm = file.name.toLowerCase();
        const existentes  = match ? (archivosExistentes[match.carpeta] || []) : [];
        const esDuplicado = existentes.includes(nombreNorm);
        return { file, idx: i, matched: !!match, carpeta: match?.carpeta ?? null, nombreAlumno: match?.nombre ?? null, duplicado: esDuplicado };
    });

    const svgOk  = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 shrink-0"><polyline points="20 6 9 17 4 12"/></svg>`;
    const svgNo  = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-red-400 shrink-0"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
    const svgWarn = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-amber-500 shrink-0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;

    document.getElementById('masivoLista').innerHTML = _masivoArchivos.map(item => {
        let bgClass, icon, statusText, statusColor;
        if (!item.matched) {
            bgClass = 'bg-red-50 border-red-200'; icon = svgNo;
            statusText = 'Sin coincidencia encontrada'; statusColor = 'text-red-400';
        } else if (item.duplicado) {
            bgClass = 'bg-amber-50 border-amber-300'; icon = svgWarn;
            statusText = '⚠ Ya existe — se subirá con nombre distinto'; statusColor = 'text-amber-600';
        } else {
            bgClass = 'bg-emerald-50 border-emerald-200'; icon = svgOk;
            statusText = '→ ' + item.nombreAlumno; statusColor = 'text-emerald-600';
        }
        return `
        <div id="masivo-item-${item.idx}" class="flex items-center gap-3 px-3 py-2.5 border rounded-xl ${bgClass}">
            <span id="masivo-icon-${item.idx}" class="shrink-0">${icon}</span>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black text-slate-700 truncate">${item.file.name}</p>
                <p id="masivo-status-${item.idx}" class="text-[9px] font-bold mt-0.5 ${statusColor}">${statusText}</p>
            </div>
        </div>`;
    }).join('');

    document.getElementById('masivoListaWrapper').style.display = 'block';

    const matchCount = _masivoArchivos.filter(a => a.matched).length;
    const dupCount   = _masivoArchivos.filter(a => a.matched && a.duplicado).length;
    const total      = _masivoArchivos.length;
    let resumen = `${matchCount} de ${total} archivo${total !== 1 ? 's' : ''} asignado${matchCount !== 1 ? 's' : ''}`;
    if (dupCount > 0) resumen += ` · ⚠ ${dupCount} ya exist${dupCount !== 1 ? 'en' : 'e'}`;
    document.getElementById('masivoResumenTexto').textContent = resumen;
    const btn = document.getElementById('masivoBtnSubir');
    btn.disabled    = matchCount === 0;
    btn.textContent = matchCount > 0
        ? `Subir ${matchCount} archivo${matchCount !== 1 ? 's' : ''}`
        : 'Subir archivos';
}

async function subirMasivo() {
    const toSubir = _masivoArchivos.filter(a => a.matched);
    if (!toSubir.length) return;

    const btn   = document.getElementById('masivoBtnSubir');
    const ciclo = window.SEGUIMIENTO_CICLO || '';
    btn.disabled = true;
    document.getElementById('masivoResumenTexto').textContent = 'Subiendo...';

    const svgOk  = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 shrink-0"><polyline points="20 6 9 17 4 12"/></svg>`;
    const svgErr = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 shrink-0"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;

    let ok = 0, fail = 0;

    for (const item of toSubir) {
        const iconEl   = document.getElementById(`masivo-icon-${item.idx}`);
        const statusEl = document.getElementById(`masivo-status-${item.idx}`);
        if (statusEl) statusEl.textContent = 'Subiendo...';

        const fd = new FormData();
        fd.append('fichero', item.file);
        fd.append('tipo',    _masivoTipo);
        fd.append('ciclo',   ciclo);
        fd.append('alumno',  item.carpeta);

        try {
            const res  = await fetch('index.php?controlador=Tutores&accion=seguimientoSubir', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                ok++;
                if (iconEl)   iconEl.innerHTML   = svgOk;
                if (statusEl) { statusEl.textContent = '✓ Subido'; statusEl.className = 'text-[9px] font-bold mt-0.5 text-emerald-600'; }
            } else {
                fail++;
                if (iconEl)   iconEl.innerHTML   = svgErr;
                if (statusEl) { statusEl.textContent = '✗ ' + (data.error ?? 'Error'); statusEl.className = 'text-[9px] font-bold mt-0.5 text-red-500'; }
            }
        } catch (e) {
            fail++;
            if (iconEl)   iconEl.innerHTML   = svgErr;
            if (statusEl) { statusEl.textContent = '✗ Error de conexión'; statusEl.className = 'text-[9px] font-bold mt-0.5 text-red-500'; }
        }
    }

    document.getElementById('masivoResumenTexto').textContent =
        `✓ ${ok} subido${ok !== 1 ? 's' : ''}` + (fail > 0 ? ` · ✗ ${fail} con error` : '');

    if (ok > 0) {
        setTimeout(() => {
            cerrarModalMasivo();
            location.href = 'index.php?controlador=Tutores&accion=mostrarPanel&tab=4';
        }, 1500);
    } else {
        btn.disabled = false;
    }
}
// ─────────────────────────────────────────────────────────────────────────────
</script>
