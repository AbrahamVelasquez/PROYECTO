<?php

// Vista/Tutores/Components/Buttons_PF_Edicion.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
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
                onclick="document.getElementById('modalGestionarRA').style.display='flex'"
                class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-800 transition-all shadow-md flex items-center gap-2 cursor-pointer uppercase tracking-wide">
            <span>📋</span> RESULTADOS DE APRENDIZAJE
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

window.exportarYMarcar = async function(idAsignacion) {
    const idDefinitivo = idAsignacion || document.getElementById('edit_id_asignacion')?.value;

    if (!idDefinitivo) {
        alert("Error: No se encuentra el ID de la asignación.");
        return;
    }

    // 1. Recolectar todos los datos del formulario de edición
    const formData = new URLSearchParams();
    formData.append('id_asignacion', idDefinitivo);
    
    // Mapeo de los campos del formulario (IDs de los inputs en PF_Edicion.php)
    const campos = {
        'regimen': 'edit_regimen',
        'fecha_plan': 'edit_fecha_plan',
        'anio_inicio': 'edit_anio_inicio',
        'anio_fin': 'edit_anio_fin',
        'curso_selector': 'edit_curso_selector',
        'nombre_ciclo': 'edit_nombre_ciclo',
        'codigo_ciclo': 'edit_codigo_ciclo',
        'email_alumno': 'edit_email_alumno',
        'tel_alumno': 'edit_tel_alumno',
        'centro_nombre': 'edit_centro_nombre',
        'centro_correo': 'edit_centro_correo',
        'centro_tel': 'edit_centro_tel',
        'nombre_empresa': 'edit_nombre_empresa',
        'nif_empresa': 'edit_nif_empresa',
        'email_empresa': 'edit_email_empresa',
        'tel_empresa': 'edit_tel_empresa',
        'tutor_empresa': 'edit_tutor_empresa',
        'email_tutor_emp': 'edit_email_tutor_emp',
        'tel_tutor_emp': 'edit_tel_tutor_emp'
    };

    // Añadir cada valor al formData si el elemento existe
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

        const textoBruto = await res.text();
        console.log("Respuesta del servidor:", textoBruto); // Revisa esto en la consola F12

        // Buscamos donde empieza el JSON real
        const inicioJson = textoBruto.indexOf('{');
        if (inicioJson === -1) {
            throw new Error("El servidor no devolvió un JSON válido: " + textoBruto);
        }
        
        const jsonLimpio = textoBruto.substring(inicioJson, textoBruto.lastIndexOf('}') + 1);
        const data = JSON.parse(jsonLimpio);

        if (data.success) {
            // En lugar de alert, redirigimos directamente si todo fue bien
            window.location.href = "index.php?controlador=Tutores&accion=mostrarPanel&tab=3";
        } else {
            alert("Error: " + (data.error || "No se pudo guardar."));
        }
    } catch (error) {
        console.error("Error crítico:", error);
        alert("Hubo un problema al conectar con el servidor. Revisa la consola.");
    }
};
</script>