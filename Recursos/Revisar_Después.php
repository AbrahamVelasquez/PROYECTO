<?php

// Dentro de la función guardarNuevoConvenio(), en el caso sin sesión:
//Cerrar ventana con tiempo despues de rellenar el formulario

else {
    die('
        <script src="https://cdn.tailwindcss.com"></script>
        <div class="min-h-screen flex items-center justify-center bg-slate-50 p-4 font-sans text-slate-900">
            <div class="max-w-md w-full bg-white border-t-4 border-emerald-500 rounded-2xl shadow-2xl p-10 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 rounded-full mb-6">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-3xl font-black uppercase tracking-tight text-slate-800 mb-4">
                    ¡Registro <span class="text-emerald-600">Completado!</span>
                </h2>
                <p class="text-slate-500 font-medium leading-relaxed mb-8">
                    El convenio se ha registrado correctamente. <br>
                    La ventana se cerrará automáticamente en unos segundos.
                </p>
                
                <button onclick="window.close();" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-800 transition-all shadow-lg cursor-pointer">
                    Cerrar Ventana Ahora
                </button>
            </div>
        </div>

        <script>
            // Intentar cerrar la ventana automáticamente tras 5 segundos
            setTimeout(function() {
                window.close();
            }, 5000);
        </script>
    ');
}

/////////////////////////////////////////////////////////////////////////

// Ver porque en Controlador_Tutores esto no da error al js de los pasos

    public function mostrarPanel() {
        // --- PESTAÑA ACTIVA ---
        $pestanaActiva = $_GET['tab'] ?? 1;

        // --- GESTIÓN DE PERFIL DEL TUTOR ---
        $tutorModelo = new Tutores();
        $perfil = $tutorModelo->obtenerDatosPerfil($_SESSION['usuario']);

        $nombreTutor = $perfil ? ($perfil['nombre'] . " " . $perfil['apellidos']) : $_SESSION['usuario'];
        $correoTutor = $perfil['email'] ?? '';
        $telTutor = $perfil['telefono'] ?? '';
        $cicloTutor = $perfil['nombre_ciclo'] ?? 'Sin Ciclo';
        $cursoTutor = $perfil['nombre_curso'] ?? 'Sin Curso';
        $idCicloTutor = $perfil['id_ciclo'] ?? 0;
        $_SESSION['id_ciclo'] = $idCicloTutor; // Aseguramos que el ID esté en sesión para el registro

        // --- GESTIÓN DE CONVENIOS ---
        $convControlador = new Convenios_Controlador();
        $data = $convControlador->gestionar();
        
        $convenios = $data['busqueda_convenio'];
        $misConvenios = $data['favoritos'];
        
        // REGLA: Solo mostramos los convenios nuevos que NO estén en la tabla de aprobados
        $convModelo = new Convenios();
        $conveniosProceso = $convModelo->listarPendientesDeAprobacion($idCicloTutor);

        // --- RESTO DEL CÓDIGO (Alumnos, etc.) ---
        $busqueda = $_REQUEST['busqueda'] ?? '';
        $estadoFiltro = $_REQUEST['estado'] ?? '';

        $alumnoModelo = new Alumnos();
        $ordenar = $_POST['ordenar'] ?? '';
        $misConveniosIds = array_column($misConvenios, 'id_convenio');
        $alumnos = $alumnoModelo->listarPorCiclo($idCicloTutor, $busqueda, $estadoFiltro, $ordenar, $misConveniosIds);
        $alumnosFirmados = $alumnoModelo->listarAlumnosFirmados($idCicloTutor);

        // --- CARGA DE VISTA ---
        require_once 'Vista/Tutores/Dashboard_Tutores.php';
    }

// y la siguiente versión, con la uso de variables del constructor, si da

    public function mostrarPanel() {
        // --- PESTAÑA ACTIVA ---
        $pestanaActiva = $_GET['tab'] ?? 1;

        // --- GESTIÓN DE PERFIL DEL TUTOR ---
        // $tutorModelo = new Tutores(); // <-- ESTO YA NO ES NECESARIO
        $perfil = $this->tutorModelo->obtenerDatosPerfil($_SESSION['usuario']);

        $nombreTutor = $perfil ? ($perfil['nombre'] . " " . $perfil['apellidos']) : $_SESSION['usuario'];
        $correoTutor = $perfil['email'] ?? '';
        $telTutor = $perfil['telefono'] ?? '';
        $cicloTutor = $perfil['nombre_ciclo'] ?? 'Sin Ciclo';
        $cursoTutor = $perfil['nombre_curso'] ?? 'Sin Curso';
        $idCicloTutor = $perfil['id_ciclo'] ?? 0;
        $_SESSION['id_ciclo'] = $idCicloTutor; // Aseguramos que el ID esté en sesión para el registro

        // --- GESTIÓN DE CONVENIOS ---
        // $convControlador = new Convenios_Controlador(); // <-- ESTO YA NO ES NECESARIO
        $data = $this->convControlador->gestionar();
        
        $convenios = $data['busqueda_convenio'];
        $misConvenios = $data['favoritos'];
        
        // REGLA: Solo mostramos los convenios nuevos que NO estén en la tabla de aprobados
        // $convModelo = new Convenios(); // <-- ESTO YA NO ES NECESARIO
        $conveniosProceso = $this->convModelo->listarPendientesDeAprobacion($idCicloTutor);

        // --- RESTO DEL CÓDIGO (Alumnos, etc.) ---
        $busqueda = $_REQUEST['busqueda'] ?? '';
        $estadoFiltro = $_REQUEST['estado'] ?? '';

        // $alumnoModelo = new Alumnos(); // <-- ESTO YA NO ES NECESARIO
        $ordenar = $_POST['ordenar'] ?? '';
        $misConveniosIds = array_column($misConvenios, 'id_convenio');
        $alumnos = $this->alumnoModelo->listarPorCiclo($idCicloTutor, $busqueda, $estadoFiltro, $ordenar, $misConveniosIds);
        $alumnosFirmados = $this->alumnoModelo->listarAlumnosFirmados($idCicloTutor);

        // --- CARGA DE VISTA ---
        require_once 'Vista/Tutores/Dashboard_Tutores.php';
    }


?>

