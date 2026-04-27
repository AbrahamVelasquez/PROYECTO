<?php

// Vista/Tutores/Steps/Plan_Formativo.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

?>
<div id="contenedor-plan-formativo">
    <?php include_once 'Vista/Tutores/Components/Modales_PF.php'; ?>

    <div id="vista-tabla">
        <?php include_once 'Vista/Tutores/Components/PF_Tabla.php'; ?>
    </div>

    <div id="vista-edicion" class="hidden">
        <?php include_once 'Vista/Tutores/Components/PF_Edicion.php'; ?>
    </div>
</div>

<script>
window.prepararCamposAutomaticos = function(anioInicioDB, anioFinDB) {
    const ahora = new Date();
    
    // --- 1. LÓGICA DE CURSO ACADÉMICO (Usando datos de BD) ---
    // Si recibimos los años de la BD, sacamos los últimos 2 dígitos
    if (anioInicioDB && anioFinDB) {
        document.getElementById('pf_edit_anio_inicio').value = String(anioInicioDB).slice(-2);
        document.getElementById('pf_edit_anio_fin').value = String(anioFinDB).slice(-2);
    }

    // --- 2. LÓGICA DE FECHA DE EMISIÓN (Hoy) ---
    const dia = String(ahora.getDate()).padStart(2, '0');
    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
    const anio = ahora.getFullYear();
    const fechaHoy = `${anio}-${mes}-${dia}`;
    
    if(document.getElementById('pf_edit_fecha_plan')) {
        document.getElementById('pf_edit_fecha_plan').value = fechaHoy;
    }
};

// Definimos la función en el objeto window para que sea accesible desde la tabla
window.mostrarEdicion = function(id, nombre, email, empresa, telefono, nif, emailEmpresa, 
                                telEmpresa, nombreCiclo, idCurso, idCicloOriginal, nombreTutorActual, 
                                correoTutorActual, telTutorActual, anioInicio, anioFin, idAsignacionReal, 
                                nombreTutorEmpresa, correoTutorEmpresa, telTutorEmpresa, anexo, idConvenio,
                                horario, horasTotales, fechaInicio, fechaFinal) {

    const vistaTabla = document.getElementById('vista-tabla');
    const vistaEdicion = document.getElementById('vista-edicion');

    if (vistaTabla && vistaEdicion) {
        vistaTabla.classList.add('hidden');
        vistaEdicion.classList.remove('hidden');
        
        // Usamos el nuevo idAsignacionReal para el input de exportación
        if(document.getElementById('edit_id_asignacion')) {
            document.getElementById('edit_id_asignacion').value = idAsignacionReal;
        }

        // Pasamos los años de la BD a la función de autocompletado
        window.prepararCamposAutomaticos(anioInicio, anioFin);

        // --- 1. IDENTIFICACIÓN ACADÉMICA ---
        if(document.getElementById('pf_edit_anexo')) document.getElementById('pf_edit_anexo').value = anexo ?? '';
        if(document.getElementById('pf_edit_nombre_ciclo')) document.getElementById('pf_edit_nombre_ciclo').value = nombreCiclo;
        if(document.getElementById('pf_edit_codigo_ciclo')) document.getElementById('pf_edit_codigo_ciclo').value = idCicloOriginal;
        
        const selectorCurso = document.getElementById('pf_edit_curso_selector');
        if(selectorCurso) selectorCurso.value = idCurso;

        // --- 2. DATOS DEL ALUMNO ---
        if(document.getElementById('pf_edit_nombre_completo')) document.getElementById('pf_edit_nombre_completo').value = nombre;
        if(document.getElementById('pf_edit_email_alumno')) document.getElementById('pf_edit_email_alumno').value = email;
        if(document.getElementById('pf_edit_tel_alumno')) document.getElementById('pf_edit_tel_alumno').value = telefono;
        
        // --- 3. DATOS DEL CENTRO Y TUTOR ---
        document.getElementById('pf_edit_centro_nombre').value = "IES CIUDAD ESCOLAR";
        document.getElementById('pf_edit_centro_correo').value = "ies.ciudadescolar@educa.madrid.org";
        document.getElementById('pf_edit_centro_tel').value = "917341244";

        document.getElementById('pf_edit_tutor_centro_nombre').value = nombreTutorActual;
        document.getElementById('pf_edit_tutor_centro_correo').value = correoTutorActual;
        document.getElementById('pf_edit_tutor_centro_tel').value = telTutorActual;

        // --- 4. DATOS DE LA EMPRESA ---
        if(document.getElementById('pf_id_convenio')) document.getElementById('pf_id_convenio').value = idConvenio;
        if(document.getElementById('pf_edit_nombre_empresa')) document.getElementById('pf_edit_nombre_empresa').value = empresa;
        if(document.getElementById('pf_edit_nif_empresa')) document.getElementById('pf_edit_nif_empresa').value = nif;
        if(document.getElementById('pf_edit_email_empresa')) document.getElementById('pf_edit_email_empresa').value = emailEmpresa;
        if(document.getElementById('pf_edit_tel_empresa')) document.getElementById('pf_edit_tel_empresa').value = telEmpresa;
        if(document.getElementById('pf_edit_tutor_empresa')) document.getElementById('pf_edit_tutor_empresa').value = nombreTutorEmpresa ?? '';
        if(document.getElementById('pf_edit_email_tutor_emp')) document.getElementById('pf_edit_email_tutor_emp').value = correoTutorEmpresa ?? '';
        if(document.getElementById('pf_edit_tel_tutor_emp')) document.getElementById('pf_edit_tel_tutor_emp').value = telTutorEmpresa ?? '';
        
        // --- 5. NUEVOS: HORARIO, HORAS Y CALENDARIO ---
        if (document.getElementById('pf_edit_horario'))
            document.getElementById('pf_edit_horario').value = horario ?? '';

        if (document.getElementById('pf_edit_horas_totales'))
            document.getElementById('pf_edit_horas_totales').value = horasTotales ?? '';

        // Calendario: concatenación "fecha_inicio / fecha_final"
        if (document.getElementById('pf_edit_fecha_inicio'))
            document.getElementById('pf_edit_fecha_inicio').value = fechaInicio ?? '';
        if (document.getElementById('pf_edit_fecha_final'))
            document.getElementById('pf_edit_fecha_final').value = fechaFinal ?? '';

        // Botón devolver (sigue usando el ID de alumno original si es necesario)
        const btnDevolver = document.getElementById('btn-devolver-alumno');
        if (btnDevolver) btnDevolver.setAttribute('onclick', `abrirModalDevolver(${id}, '${nombre}')`);
    }
};

window.volverALista = function() {
    // 1. Intercambiamos las vistas
    document.getElementById('vista-tabla').classList.remove('hidden');
    document.getElementById('vista-edicion').classList.add('hidden');
    
    // 2. Opcional: Limpiar los campos para que no se queden datos viejos
    const camposALimpiar = ['pf_edit_nombre_completo', 'pf_edit_email_alumno', 'pf_edit_tel_alumno'];
    camposALimpiar.forEach(id => {
        if(document.getElementById(id)) document.getElementById(id).value = '';
    });
};

document.addEventListener('DOMContentLoaded', () => {
    // Lógica para habilitar/deshabilitar campos de texto según Radio Buttons
    const setupRadioToggle = (radioName) => {
        const radios = document.querySelectorAll(`input[name="${radioName}"]`);
        radios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                // Buscamos el input de texto que está en el mismo contenedor padre
                const contenedor = e.target.closest('.space-y-4');
                const inputTexto = contenedor.querySelector('input[type="text"]');
                
                if (e.target.value === 'SI') {
                    inputTexto.disabled = false;
                    inputTexto.classList.remove('bg-slate-50', 'cursor-not-allowed');
                    inputTexto.focus();
                } else {
                    inputTexto.disabled = true;
                    inputTexto.value = ''; // Limpiamos el texto si marca NO
                    inputTexto.classList.add('bg-slate-50', 'cursor-not-allowed');
                }
            });
        });
    };

    // Inicializamos para Discapacidad y Autorización
    setupRadioToggle('discapacidad');
    setupRadioToggle('autorizacion');
    
    // Dejar los campos deshabilitados por defecto al cargar (ya que el HTML tiene "NO" checked)
    document.querySelectorAll('input[name="discapacidad"], input[name="autorizacion"]').forEach(r => {
        if(r.checked && r.value === 'NO') {
            const input = r.closest('.space-y-4').querySelector('input[type="text"]');
            input.disabled = true;
            input.classList.add('bg-slate-50', 'cursor-not-allowed');
        }
    });
});
</script>