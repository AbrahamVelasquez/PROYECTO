<div id="contenedor-plan-formativo">
    <div id="vista-tabla">
        <?php include_once 'Vista/Components/PF_Tabla.php'; ?>
    </div>

    <div id="vista-edicion" class="hidden">
        <?php include_once 'Vista/Components/PF_Edicion.php'; ?>
    </div>
</div>

<script>
    function mostrarEdicion(alumnoData) {
        // 1. Cambiar visibilidad
        document.getElementById('vista-tabla').classList.add('hidden');
        document.getElementById('vista-edicion').classList.remove('hidden');
        
        // 2. Actualizar el botón de "Devolver Alumno" en la vista de edición
        // Buscamos el botón dentro del contenedor de edición
        const btnDevolver = document.querySelector('#vista-edicion button[onclick^="abrirModalDevolver"]');
        
        if (btnDevolver) {
            // Actualizamos el onclick dinámicamente con los datos recibidos
            btnDevolver.setAttribute('onclick', `abrirModalDevolver(${alumnoData.id}, '${alumnoData.nombre}')`);
        }

        console.log("Editando a:", alumnoData);
    }

    function volverALista() {
        document.getElementById('vista-edicion').classList.add('hidden');
        document.getElementById('vista-tabla').classList.remove('hidden');
    }
</script>