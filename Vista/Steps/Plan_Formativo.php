<div id="contenedor-plan-formativo">
    <div id="vista-tabla">
        <?php include_once 'Vista/Components/PF_Tabla.php'; ?>
    </div>

    <div id="vista-edicion" class="hidden">
        <?php include_once 'Vista/Components/PF_Edicion.php'; ?>
    </div>
</div>

<script>
// Definimos la función en el objeto window para que sea accesible desde la tabla
window.mostrarEdicion = function(id, nombre, email, empresa, telefono) {
    const vistaTabla = document.getElementById('vista-tabla');
    const vistaEdicion = document.getElementById('vista-edicion');

    if (vistaTabla && vistaEdicion) {
        vistaTabla.classList.add('hidden');
        vistaEdicion.classList.remove('hidden');
        
        // Rellenamos los campos del formulario en PF_Edicion
        if(document.getElementById('edit_nombre_completo')) document.getElementById('edit_nombre_completo').value = nombre;
        if(document.getElementById('edit_email_alumno')) document.getElementById('edit_email_alumno').value = email;
        if(document.getElementById('edit_tel_alumno')) document.getElementById('edit_tel_alumno').value = telefono;
        
        // Actualizamos el botón de Devolver para el modal
        const btnDevolver = document.getElementById('btn-devolver-alumno');
        if (btnDevolver) {
            btnDevolver.setAttribute('onclick', `abrirModalDevolver(${id}, '${nombre}')`);
        }
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