<div id="contenedor-plan-formativo">
    <div id="vista-tabla">
        <?php include_once 'Vista/Components/PF_Tabla.php'; ?>
    </div>

    <div id="vista-edicion" class="hidden">
        <?php include_once 'Vista/Components/PF_Edicion.php'; ?>
    </div>
</div>

<script>
window.prepararCamposAutomaticos = function() {
    const ahora = new Date();
    
    // --- 1. LÓGICA DE CURSO ACADÉMICO ---
    const mesActual = ahora.getMonth() + 1;
    const anioActualFull = ahora.getFullYear();
    let anioInicio, anioFin;

    if (mesActual >= 9) { // Septiembre a Diciembre
        anioInicio = anioActualFull;
        anioFin = anioActualFull + 1;
    } else { // Enero a Agosto
        anioInicio = anioActualFull - 1;
        anioFin = anioActualFull;
    }

    document.getElementById('edit_anio_inicio').value = String(anioInicio).slice(-2);
    document.getElementById('edit_anio_fin').value = String(anioFin).slice(-2);

    // --- 2. LÓGICA DE FECHA DE EMISIÓN (Hoy) ---
    // Formato YYYY-MM-DD necesario para input type="date"
    const dia = String(ahora.getDate()).padStart(2, '0');
    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
    const anio = ahora.getFullYear();
    
    const fechaHoy = `${anio}-${mes}-${dia}`;
    
    if(document.getElementById('edit_fecha_plan')) {
        document.getElementById('edit_fecha_plan').value = fechaHoy;
    }
};

// Definimos la función en el objeto window para que sea accesible desde la tabla
window.mostrarEdicion = function(id, nombre, email, empresa, telefono, nif, emailEmpresa, telEmpresa, nombreCiclo, idCurso, idCicloOriginal, nombreTutorActual, correoTutorActual, telTutorActual) {
    const vistaTabla = document.getElementById('vista-tabla');
    const vistaEdicion = document.getElementById('vista-edicion');

    if (vistaTabla && vistaEdicion) {
        vistaTabla.classList.add('hidden');
        vistaEdicion.classList.remove('hidden');
                
        // --- AUTO-CÁLCULO DEL CURSO Y FECHA ---
        // Esta función ahora pone la fecha de HOY y el CURSO 25-26 automáticamente
        window.prepararCamposAutomaticos();

        // --- 1. IDENTIFICACIÓN ACADÉMICA (Lo nuevo) ---
        if(document.getElementById('edit_nombre_ciclo')) document.getElementById('edit_nombre_ciclo').value = nombreCiclo;
        if(document.getElementById('edit_codigo_ciclo')) document.getElementById('edit_codigo_ciclo').value = idCicloOriginal;
        
        const selectorCurso = document.getElementById('edit_curso_selector');
        if(selectorCurso) selectorCurso.value = idCurso;

        // --- 2. DATOS DEL ALUMNO ---
        if(document.getElementById('edit_nombre_completo')) document.getElementById('edit_nombre_completo').value = nombre;
        if(document.getElementById('edit_email_alumno')) document.getElementById('edit_email_alumno').value = email;
        if(document.getElementById('edit_tel_alumno')) document.getElementById('edit_tel_alumno').value = telefono;
        
        // --- 3. DATOS DEL CENTRO Y TUTOR ---

        // 1. Datos estáticos del IES Ciudad Escolar (Por ser el Centro)
        document.getElementById('edit_centro_nombre').value = "IES CIUDAD ESCOLAR";
        document.getElementById('edit_centro_correo').value = "ies.ciudadescolar@educa.madrid.org";
        document.getElementById('edit_centro_tel').value = "917341244";

        // 2. Datos del Tutor (El que ha iniciado sesión)
        document.getElementById('edit_tutor_centro_nombre').value = nombreTutorActual;
        document.getElementById('edit_tutor_centro_correo').value = correoTutorActual;
        document.getElementById('edit_tutor_centro_tel').value = telTutorActual;

        // --- 4. DATOS DE LA EMPRESA ---
        if(document.getElementById('edit_nombre_empresa')) document.getElementById('edit_nombre_empresa').value = empresa;
        if(document.getElementById('edit_nif_empresa')) document.getElementById('edit_nif_empresa').value = nif;
        if(document.getElementById('edit_email_empresa')) document.getElementById('edit_email_empresa').value = emailEmpresa;
        if(document.getElementById('edit_tel_empresa')) document.getElementById('edit_tel_empresa').value = telEmpresa;
        
        // Botón devolver
        const btnDevolver = document.getElementById('btn-devolver-alumno');
        if (btnDevolver) btnDevolver.setAttribute('onclick', `abrirModalDevolver(${id}, '${nombre}')`);
    }
};

window.volverALista = function() {
    // 1. Intercambiamos las vistas
    document.getElementById('vista-tabla').classList.remove('hidden');
    document.getElementById('vista-edicion').classList.add('hidden');
    
    // 2. Opcional: Limpiar los campos para que no se queden datos viejos
    const camposALimpiar = ['edit_nombre_completo', 'edit_email_alumno', 'edit_tel_alumno'];
    camposALimpiar.forEach(id => {
        if(document.getElementById(id)) document.getElementById(id).value = '';
    });
};
</script>