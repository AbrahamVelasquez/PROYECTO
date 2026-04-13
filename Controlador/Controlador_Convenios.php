<?php
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
        $id_ciclo_actual = $_SESSION['id_ciclo'] ?? null; // Obtenemos el ciclo de la sesión
        
        $resultadosBusqueda = [];

        // Lógica para AÑADIR A FAVORITOS (Convenios oficiales)
        if (isset($_POST['btnFavorito'])) {
            $this->convenio->añadirAFavoritos($id_tutor_actual, $_POST['id_convenio_fav']);
            header("Location: index.php?tab=1&busqueda=" . urlencode($_GET['busqueda'] ?? ''));
            exit();
        }

        // LÓGICA PARA ELIMINAR DE FAVORITOS
        if (isset($_POST['btnEliminarFav'])) {
            $idConvenio = $_POST['id_convenio_eliminar'];
            $url = "index.php?tab=1" . (!empty($_GET['busqueda']) ? "&busqueda=" . urlencode($_GET['busqueda']) : "");

            if ($this->convenio->estaEnUso($idConvenio)) {
                $_SESSION['error_convenio'] = 'No se puede eliminar este convenio porque tiene alumnos asignados.';
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

        // OBTENER FAVORITOS (MIS CONVENIOS)
        $misFavoritos = $this->convenio->obtenerFavoritos($id_tutor_actual) ?: [];

        // NUEVO: OBTENER CONVENIOS EN PROCESO (TABLA NARANJA)
        // Solo para el ciclo del tutor actual
        $conveniosProceso = [];
        if ($id_ciclo_actual) {
            $conveniosProceso = $this->convenio->obtenerConveniosEnProceso($id_ciclo_actual);
        }

        return [
            'busqueda' => $resultadosBusqueda, 
            'favoritos' => $misFavoritos,
            'proceso'   => $conveniosProceso // Se pasa a la vista
        ];
    }

    /**
     * Procesa el formulario de Registro_Convenio.php
     */
    public function guardarNuevoConvenioPendiente() {
        // Mapeo de datos asegurando mayúsculas y limpieza
        $datos = [
            'nombre_empresa'      => strtoupper(trim($_POST['nombre_empresa'])),
            'cif'                 => strtoupper(trim($_POST['cif'])),
            'direccion'           => strtoupper(trim($_POST['direccion'])),
            'municipio'           => strtoupper(trim($_POST['municipio'])),
            'cp'                  => trim($_POST['cp']),
            'pais'                => strtoupper(trim($_POST['pais'])),
            'telefono'            => trim($_POST['telefono']),
            'fax'                 => trim($_POST['fax']),
            'mail'                => trim($_POST['email']),             // Del input 'email'
            'nombre_representante'=> strtoupper(trim($_POST['nombre_rep_legal'])), // Del input 'nombre_rep_legal'
            'dni_representante'   => strtoupper(trim($_POST['dni_rep_legal'])),    // Del input 'dni_rep_legal'
            'cargo'               => strtoupper(trim($_POST['cargo_rep_legal'])),  // Del input 'cargo_rep_legal'
            'id_ciclo'            => $_POST['id_ciclo']
        ];

        // Guardamos en la tabla convenios_nuevos
        $exito = $this->convenio->guardarNuevoConvenioPendiente($datos);

        if ($exito) {
            $_SESSION['mensaje_exito'] = "Solicitud de convenio enviada correctamente.";
        } else {
            $_SESSION['error_convenio'] = "Hubo un error al registrar la solicitud.";
        }

        // Redirigir siempre a la pestaña de convenios (tab 1)
        header('Location: index.php?tab=1');
        exit();
    }
}