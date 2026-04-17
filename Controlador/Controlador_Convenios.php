<?php

// Controlador/Controlador_Convenios.php

require_once './Modelo/Convenios.php';

class Convenios_Controlador {

    private $convenio; 

    public function __construct() {
        $this->convenio = new Convenios();
    }

    public function gestionar() {
        if (!isset($_SESSION['id_tutor'])) {
            header("Location: login.php");
            exit();
        }
        
        $id_tutor_actual = $_SESSION['id_tutor']; 
        $id_ciclo_actual = $_SESSION['id_ciclo'] ?? null; 
        
        $resultadosBusqueda = [];

        // Lógica para AÑADIR A FAVORITOS
        if (isset($_POST['btnFavorito'])) {
            $resultado = $this->convenio->añadirAFavoritos($id_tutor_actual, $_POST['id_convenio_fav']);
            
            // Si es duplicado, guardamos en la sesión
            if ($resultado === "duplicado") {
                $_SESSION['error_duplicado'] = true;
            }

            // Redirección limpia (sin parámetros de error en la URL)
            header("Location: index.php?tab=1&busqueda=" . urlencode($_GET['busqueda'] ?? ''));
            exit();
        }

        // LÓGICA PARA ELIMINAR DE FAVORITOS
        if (isset($_POST['btnEliminarFav'])) {
            $idConvenio = $_POST['id_convenio_eliminar'];
            $id_ciclo_actual = $_SESSION['id_ciclo']; // <--- Importante tener esto aquí
            
            $url = "index.php?tab=1" . (!empty($_GET['busqueda']) ? "&busqueda=" . urlencode($_GET['busqueda']) : "");

            // Ahora pasamos ambos parámetros
            if ($this->convenio->estaEnUso($idConvenio, $id_ciclo_actual)) {
                $_SESSION['error_convenio'] = 'No puedes quitarlo de favoritos porque tienes alumnos de tu ciclo asignados a él.';
            } else {
                $this->convenio->eliminarDeFavoritos($id_tutor_actual, $idConvenio);
            }
            header("Location: " . $url);
            exit();
        }

        // BUSQUEDA DE CONVENIOS OFICIALES
        if (isset($_GET['busqueda']) && trim($_GET['busqueda']) !== '') {
            $resultadosBusqueda = $this->convenio->buscar($_GET['busqueda']);
        }

        // OBTENER FAVORITOS
        $misFavoritos = $this->convenio->obtenerFavoritos($id_tutor_actual) ?: [];

        // OBTENER CONVENIOS EN PROCESO (Filtrados por los que NO están aprobados)
        $conveniosProceso = [];
        if ($id_ciclo_actual) {
            $conveniosProceso = $this->convenio->listarPendientesDeAprobacion($id_ciclo_actual);
        }

        return [
            'busqueda' => $resultadosBusqueda, 
            'favoritos' => $misFavoritos,
            'proceso'   => $conveniosProceso 
        ];
    }

    public function guardarNuevoConvenioPendiente() {
        $datos = [
            'nombre_empresa'      => strtoupper(trim($_POST['nombre_empresa'])),
            'cif'                 => strtoupper(trim($_POST['cif'])),
            'direccion'           => strtoupper(trim($_POST['direccion'])),
            'municipio'           => strtoupper(trim($_POST['municipio'])),
            'cp'                  => trim($_POST['cp']),
            'pais'                => strtoupper(trim($_POST['pais'])),
            'telefono'            => trim($_POST['telefono']),
            'fax'                 => trim($_POST['fax']),
            'mail'                => trim($_POST['email']),
            'nombre_representante'=> strtoupper(trim($_POST['nombre_rep_legal'])),
            'dni_representante'   => strtoupper(trim($_POST['dni_rep_legal'])),
            'cargo'               => strtoupper(trim($_POST['cargo_rep_legal'])),
            'id_ciclo'            => $_POST['id_ciclo']
        ];

        $exito = $this->convenio->guardarNuevoConvenioPendiente($datos);
        
        //VALIDACION ANTERIOR
        /*if ($exito) {
            $_SESSION['mensaje_exito'] = "Solicitud de convenio enviada correctamente.";
        } else {
            $_SESSION['error_convenio'] = "Hubo un error al registrar la solicitud.";
        }*/

        if ($exito) {
        if (isset($_SESSION['usuario'])) {
            // CASO A: EL USUARIO TIENE SESIÓN (Tutor/Admin)
            // Lo redirigimos a su panel con un mensaje de éxito normal
            header("Location: index.php?accion=mostrarPanel&mensaje=registro_ok");
            exit();
        } else {
            // CASO B: USUARIO EXTERNO (Sin sesión)
            // Mostramos la pantalla de éxito sin salida
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
                        <p class="text-slate-500 font-medium leading-relaxed mb-6">
                            El convenio se ha registrado correctamente en nuestro sistema. <br>
                            Nuestro equipo revisará la información próximamente.
                        </p>
                        <div class="pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Ya puede cerrar esta ventana de forma segura
                            </p>
                        </div>
                    </div>
                </div>
            ');
            }
        }
    }

    public function aprobarNuevo() {
        if (isset($_POST['id_convenio_nuevo'])) {
            $id = $_POST['id_convenio_nuevo'];
            $exito = $this->convenio->registrarAprobacion($id);
            
            if ($exito) {
                $_SESSION['mensaje_exito'] = "Convenio marcado como aprobado.";
            } else {
                $_SESSION['error_convenio'] = "No se pudo procesar la aprobación.";
            }
        }
        header("Location: index.php?tab=1");
        exit();
    }

    public function editarConvenioNuevo() {
        // Verificamos que venga el ID, si no, no podemos editar
        if (!isset($_POST['id_convenio_nuevo'])) {
            header('Location: index.php?tab=1');
            exit();
        }

        $id = $_POST['id_convenio_nuevo'];

        $datos = [
            'nombre_empresa'    => strtoupper(trim($_POST['nombre_empresa'])),
            'cif'               => strtoupper(trim($_POST['cif'])),
            'direccion'         => strtoupper(trim($_POST['direccion'])),
            'municipio'         => strtoupper(trim($_POST['municipio'])),
            'cp'                => trim($_POST['cp']),
            'pais'              => strtoupper(trim($_POST['pais'])),
            'telefono'          => trim($_POST['telefono']),
            'fax'               => trim($_POST['fax']),
            'mail'             => trim($_POST['email']), 
            'nombre_representante'  => strtoupper(trim($_POST['nombre_rep_legal'])),
            'dni_representante'     => strtoupper(trim($_POST['dni_rep_legal'])),
            'cargo'   => strtoupper(trim($_POST['cargo_rep_legal']))
        ];

        // Llamamos a la función de actualizar del modelo que creamos antes
        $exito = $this->convenio->actualizarConvenioNuevo($id, $datos);

        if ($exito) {
            $_SESSION['mensaje_exito'] = "Convenio actualizado correctamente.";
        } else {
            $_SESSION['error_convenio'] = "Error al actualizar los datos del convenio.";
        }

        header('Location: index.php?tab=1');
        exit();
    }

} // Llave de la clase

?>