<?php

// Controlador/Controlador_Convenios.php

require_once __DIR__ . '/../Modelo/Convenios.php';

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
        
        // Recogemos la búsqueda priorizando POST (el nuevo método)
        // Pero mantenemos GET por si viniera de alguna redirección antigua
        $terminoBusqueda = $_POST['busqueda_convenio'] ?? $_GET['busqueda'] ?? '';
        
        $resultadosBusqueda = [];

        // Lógica para AÑADIR A FAVORITOS
        if (isset($_POST['btnFavorito'])) {
            $resultado = $this->convenio->añadirAFavoritos($id_tutor_actual, $_POST['id_convenio_fav']);
            
            if ($resultado === "duplicado") {
                $_SESSION['error_duplicado'] = true;
            }

            // Redirigimos. NOTA: Ya no enviamos la búsqueda por la URL en el header
            header("Location: index.php?tab=1"); 
            exit();
        }

        // BUSQUEDA DE CONVENIOS
        if (trim($terminoBusqueda) !== '') {
            $resultadosBusqueda = $this->convenio->buscar($terminoBusqueda);
        }

        // LÓGICA PARA ELIMINAR DE FAVORITOS
        if (isset($_POST['btnEliminarFav'])) {
            $idConvenio = $_POST['id_convenio_eliminar'];
            $id_ciclo_actual = $_SESSION['id_ciclo']; // <--- Importante tener esto aquí
            
            // Limpiamos la URL para que sea POST puro (sin busqueda en el GET)
            $url = "index.php?tab=1";

            // Ahora pasamos ambos parámetros
            if ($this->convenio->estaEnUso($idConvenio, $id_ciclo_actual)) {
                $_SESSION['error_convenio'] = 'No puedes quitarlo de favoritos porque tienes alumnos de tu ciclo asignados a él.';
            } else {
                $this->convenio->eliminarDeFavoritos($id_tutor_actual, $idConvenio);
            }
            header("Location: " . $url);
            exit();
        }

        // OBTENER FAVORITOS
        $misFavoritos = $this->convenio->obtenerFavoritos($id_tutor_actual) ?: [];

        // OBTENER CONVENIOS EN PROCESO (Filtrados por los que NO están aprobados)
        $conveniosProceso = [];
        if ($id_ciclo_actual) {
            $conveniosProceso = $this->convenio->listarPendientesDeAprobacion($id_ciclo_actual);
        }

        return [
            'busqueda_convenio' => $resultadosBusqueda, 
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
        
        if ($exito) {
            if (isset($_SESSION['usuario'])) {
                // FLUJO INTERNO (LOGUEADO)
                header("Location: index.php?accion=mostrarPanel&mensaje=registro_ok");
                exit();
            } else {
                // FLUJO EXTERNO
                // No imprimimos el die() gigante aquí, solo confirmamos éxito 
                // para que el JS de Registro.php muestre el modal de éxito.
                return true; 
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