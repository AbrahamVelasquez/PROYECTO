<?php

// Vista/Tutores/Components/Buttons_PF_Edicion.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
validarAcceso('tutor');

?>
<div class="pt-8 border-t border-slate-100 flex flex-wrap gap-4 justify-between items-center">
    <button type="button" onclick="volverALista()" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-[10px] transition-all uppercase tracking-widest cursor-pointer border border-slate-200">
        ← VOLVER AL LISTADO
    </button>

    <div class="flex gap-3">
        <div class="relative group">
            <?php $idEditar = $_GET['editar'] ?? 0; ?>
            <button type="button"
                    id="btn-devolver-alumno"
                    onclick="abrirModalDevolver(<?= $idEditar ?>, '<?= htmlspecialchars($datosAlumno['nombre'] ?? '') ?>')"
                    class="px-6 py-3 bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 rounded-xl font-bold text-[10px] flex items-center gap-2 transition-all cursor-pointer uppercase tracking-widest">
                DEVOLVER ALUMNO
                <span class="bg-red-200 text-red-700 rounded-full h-4 w-4 flex items-center justify-center text-[9px]">!</span>
            </button>
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 hidden group-hover:block w-56 p-3 bg-slate-900 text-white text-[9px] rounded-xl shadow-xl leading-relaxed z-10 text-center tracking-tighter">
                Acción excepcional para reasignar al alumno.
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-900"></div>
            </div>
        </div>

        <button type="button"
            onclick="guardarBorrador(document.getElementById('edit_id_asignacion')?.value)"
            class="bg-slate-600 text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-700 transition-all shadow-md flex items-center gap-2 cursor-pointer uppercase tracking-wide">
            <span>💾</span> GUARDAR BORRADOR
        </button>

        <button type="button"
                onclick="abrirModalExportarPF(document.getElementById('edit_id_asignacion')?.value)"
                class="bg-orange-600 text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md flex items-center gap-2 cursor-pointer uppercase tracking-wide">
            <span>📤</span> GUARDAR Y EXPORTAR EXCEL
        </button>
    </div>
</div>

<script>
function volverALista() {
    window.location.href = "index.php?controlador=Tutores&accion=mostrarPanel&tab=3";
}

// ─────────────────────────────────────────────────────────────
// Recoge todos los valores del formulario de edición
// ─────────────────────────────────────────────────────────────
function recogerDatosFormulario(idAsignacion) {
    return {
        'id_asignacion':        idAsignacion,
        // Identificación académica
        'anio_inicio':          document.getElementById('pf_edit_anio_inicio')?.value      ?? '',
        'anio_fin':             document.getElementById('pf_edit_anio_fin')?.value         ?? '',
        'regimen':              document.getElementById('pf_edit_regimen')?.value          ?? '',
        'nombre_ciclo':         document.getElementById('pf_edit_nombre_ciclo')?.value     ?? '',
        'codigo_ciclo':         document.getElementById('pf_edit_codigo_ciclo')?.value     ?? '',
        'curso_selector':       document.getElementById('pf_edit_curso_selector')?.value   ?? '1',
        'fecha_plan':           document.getElementById('pf_edit_fecha_plan')?.value       ?? '',
        // Alumno
        'nombre_completo':      document.getElementById('pf_edit_nombre_completo')?.value  ?? '',
        'email_alumno':         document.getElementById('pf_edit_email_alumno')?.value     ?? '',
        'tel_alumno':           document.getElementById('pf_edit_tel_alumno')?.value       ?? '',
        // Centro
        'centro_nombre':        document.getElementById('pf_edit_centro_nombre')?.value    ?? '',
        'centro_correo':        document.getElementById('pf_edit_centro_correo')?.value    ?? '',
        'centro_tel':           document.getElementById('pf_edit_centro_tel')?.value       ?? '',
        'tutor_centro_nombre':  document.getElementById('pf_edit_tutor_centro_nombre')?.value  ?? '',
        'tutor_centro_correo':  document.getElementById('pf_edit_tutor_centro_correo')?.value  ?? '',
        'tutor_centro_tel':     document.getElementById('pf_edit_tutor_centro_tel')?.value     ?? '',
        // Empresa
        'id_convenio':          document.getElementById('pf_id_convenio')?.value           ?? '',
        'nombre_empresa':       document.getElementById('pf_edit_nombre_empresa')?.value   ?? '',
        'nif_empresa':          document.getElementById('pf_edit_nif_empresa')?.value      ?? '',
        'email_empresa':        document.getElementById('pf_edit_email_empresa')?.value    ?? '',
        'tel_empresa':          document.getElementById('pf_edit_tel_empresa')?.value      ?? '',
        'tutor_empresa':        document.getElementById('pf_edit_tutor_empresa')?.value    ?? '',
        'email_tutor_emp':      document.getElementById('pf_edit_email_tutor_emp')?.value  ?? '',
        'tel_tutor_emp':        document.getElementById('pf_edit_tel_tutor_emp')?.value    ?? '',
        // Planificación
        'anexo':                document.getElementById('pf_edit_anexo')?.value            ?? '',
        'horas_totales':        document.getElementById('pf_edit_horas_totales')?.value    ?? '',
        'fecha_inicio':         document.getElementById('pf_edit_fecha_inicio')?.value     ?? '',
        'fecha_final':          document.getElementById('pf_edit_fecha_final')?.value      ?? '',
        'horario':              document.getElementById('pf_edit_horario')?.value          ?? '',
        // Periodo del select (nuevo)
        'periodo_planificacion': document.getElementById('pf_edit_periodo_planificacion')?.value ?? '1',
        // Intervalos (con id directos)
        'inter_diario':   document.getElementById('inter_diario')?.checked  ? '1' : '',
        'inter_semanal':  document.getElementById('inter_semanal')?.checked ? '1' : '',
        'inter_mensual':  document.getElementById('inter_mensual')?.checked ? '1' : '',
        'inter_otros':    document.getElementById('inter_otros')?.checked   ? '1' : '',
        'inter_varias':   document.getElementById('inter_varias')?.checked  ? '1' : '',
        // Medidas
        'adaptaciones':  document.querySelector('input[name="discapacidad"]:checked')?.value ?? 'NO',
        'autorizacion':  document.querySelector('input[name="autorizacion"]:checked')?.value  ?? 'NO',
    };
}

// ─────────────────────────────────────────────────────────────
// EXPORTAR EXCEL
// ─────────────────────────────────────────────────────────────
window.exportarYMarcar = async function(idAsignacion) {
    const idDefinitivo = idAsignacion || document.getElementById('edit_id_asignacion')?.value;
    if (!idDefinitivo) { alert("Error: No se encuentra el ID de la asignación."); return; }

    // 1. Guardar en BD primero
    const formGuardar = new URLSearchParams();
    formGuardar.append('id_asignacion', idDefinitivo);
    const camposBD = {
        'anexo':          'pf_edit_anexo',
        'nombre_empresa': 'pf_edit_nombre_empresa',
        'nif_empresa':    'pf_edit_nif_empresa',
        'email_empresa':  'pf_edit_email_empresa',
        'tel_empresa':    'pf_edit_tel_empresa',
        'tutor_empresa':  'pf_edit_tutor_empresa',
        'email_tutor_emp':'pf_edit_email_tutor_emp',
        'tel_tutor_emp':  'pf_edit_tel_tutor_emp',
        'horario':        'pf_edit_horario',
        'horas_totales':  'pf_edit_horas_totales',
        'fecha_inicio':   'pf_edit_fecha_inicio',
        'fecha_final':    'pf_edit_fecha_final',
        'email_alumno':   'pf_edit_email_alumno',
        'tel_alumno':     'pf_edit_tel_alumno',
    };
    for (const [key, id] of Object.entries(camposBD)) {
        const el = document.getElementById(id);
        if (el) formGuardar.append(key, el.value);
    }
    try {
        const resBD = await fetch('index.php?controlador=Tutores&accion=marcarComoExportado', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formGuardar.toString()
        });
        const textoBD = await resBD.text();
        const i = textoBD.indexOf('{');
        if (i !== -1) {
            const dataBD = JSON.parse(textoBD.substring(i, textoBD.lastIndexOf('}') + 1));
            if (!dataBD.success) console.warn('Advertencia BD:', dataBD.error);
        }
    } catch (e) { console.warn('No se pudo guardar en BD:', e); }

    // 2. Generar y descargar Excel via POST (descarga directa)
    const campos = recogerDatosFormulario(idDefinitivo);
    const form   = document.createElement('form');
    form.method  = 'POST';
    form.action  = 'index.php?controlador=Tutores&accion=exportarExcelPF';
    form.style.display = 'none';

    for (const [key, value] of Object.entries(campos)) {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = key;
        input.value = value ?? '';
        form.appendChild(input);
    }
    document.body.appendChild(form);
    form.submit();
    setTimeout(() => document.body.removeChild(form), 3000);

    // 3. Redirigir al listado tras la descarga
    setTimeout(() => {
        window.location.href = "index.php?controlador=Tutores&accion=mostrarPanel&tab=3";
    }, 1500);
};

// ─────────────────────────────────────────────────────────────
// GUARDAR BORRADOR
// ─────────────────────────────────────────────────────────────
window.guardarBorrador = async function(idAsignacion) {
    const idDefinitivo = idAsignacion || document.getElementById('edit_id_asignacion')?.value;
    if (!idDefinitivo) { alert("Error: No se encuentra el ID de la asignación."); return; }

    const formData = new URLSearchParams();
    formData.append('id_asignacion', idDefinitivo);
    formData.append('solo_borrador', '1');

    const campos = {
        'anexo':          'pf_edit_anexo',
        'regimen':        'pf_edit_regimen',
        'email_alumno':   'pf_edit_email_alumno',
        'tel_alumno':     'pf_edit_tel_alumno',
        'nombre_empresa': 'pf_edit_nombre_empresa',
        'nif_empresa':    'pf_edit_nif_empresa',
        'email_empresa':  'pf_edit_email_empresa',
        'tel_empresa':    'pf_edit_tel_empresa',
        'tutor_empresa':  'pf_edit_tutor_empresa',
        'email_tutor_emp':'pf_edit_email_tutor_emp',
        'tel_tutor_emp':  'pf_edit_tel_tutor_emp',
        'horario':        'pf_edit_horario',
        'horas_totales':  'pf_edit_horas_totales',
        'fecha_inicio':   'pf_edit_fecha_inicio',
        'fecha_final':    'pf_edit_fecha_final',
    };
    for (const [key, id] of Object.entries(campos)) {
        const el = document.getElementById(id);
        if (el) formData.append(key, el.value);
    }

    try {
        const res = await fetch('index.php?controlador=Tutores&accion=marcarComoExportado', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        });
        const texto = await res.text();
        const inicio = texto.indexOf('{');
        if (inicio === -1) throw new Error("Respuesta no válida: " + texto);
        const data = JSON.parse(texto.substring(inicio, texto.lastIndexOf('}') + 1));

        if (data.success) {
            window.location.href = "index.php?controlador=Tutores&accion=mostrarPanel&tab=3";
        } else {
            alert("Error: " + (data.error || "No se pudo guardar."));
        }
    } catch (error) {
        console.error("Error crítico:", error);
        alert("Hubo un problema al conectar con el servidor.");
    }
};
</script>