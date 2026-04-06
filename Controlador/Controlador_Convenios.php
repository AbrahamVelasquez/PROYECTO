<?php
require_once './Modelo/Convenios.php';

class Convenios_Controlador {
    private $convenio; 

    public function __construct() {
        $this->convenio = new Convenios();
    }

    public function gestionar() {
        // CAMBIO CLAVE: Ahora usamos id_tutor que guardamos en el Login corregido
        if (!isset($_SESSION['id_tutor'])) {
            header("Location: login.php");
            exit();
        }
        
        $id_tutor_actual = $_SESSION['id_tutor']; 
        
        $resultadosBusqueda = [];

        // Lógica para AÑADIR
        if (isset($_POST['btnFavorito'])) {
            // Usamos el ID del tutor para que coincida con la columna id_tutor de la BD
            $this->convenio->añadirAFavoritos($id_tutor_actual, $_POST['id_convenio_fav']);
            header("Location: index.php?tab=1&busqueda=" . urlencode($_GET['busqueda'] ?? ''));
            exit();
        }

        // LÓGICA PARA ELIMINAR
    if (isset($_POST['btnEliminarFav'])) {
        $idConvenio = $_POST['id_convenio_eliminar'];
        
        $url = "index.php?tab=1";
        if (!empty($_GET['busqueda'])) {
            $url .= "&busqueda=" . urlencode($_GET['busqueda']);
        }

        if ($this->convenio->estaEnUso($idConvenio)) {
            $_SESSION['error_convenio'] = 'No se puede eliminar este convenio porque tiene alumnos asignados.';
        } else {
            $this->convenio->eliminarDeFavoritos($id_tutor_actual, $idConvenio);
        }

        header("Location: " . $url);
        exit();
    }

        // Solo entramos aquí si el usuario REALMENTE ha escrito algo
        if (isset($_GET['busqueda']) && trim($_GET['busqueda']) !== '') {
            $resultadosBusqueda = $this->convenio->buscar($_GET['busqueda']);
        } else {
            // Si no hay búsqueda o está vacía, nos aseguramos de que sea un array vacío
            $resultadosBusqueda = [];
        }

        // OBTENER FAVORITOS FILTRADOS POR TUTOR
        $misFavoritos = $this->convenio->obtenerFavoritos($id_tutor_actual);

        // Pequeño seguro: si no hay nada, devolvemos array vacío
        if (!$misFavoritos) { $misFavoritos = []; }

        return ['busqueda' => $resultadosBusqueda, 'favoritos' => $misFavoritos];
    }

    public function guardarNuevoConvenio() {
        $datos = [
            'nombre_empresa'      => strtoupper(trim($_POST['nombre_empresa'])),
            'cif'                 => strtoupper(trim($_POST['cif'])),
            'direccion'           => strtoupper(trim($_POST['direccion'])),
            'municipio'           => strtoupper(trim($_POST['municipio'])),
            'cp'                  => trim($_POST['cp']),
            'pais'                => strtoupper(trim($_POST['pais'])),
            'telefono'            => trim($_POST['telefono'] ?? ''),
            'fax'                 => trim($_POST['fax'] ?? ''),
            'mail'                => trim($_POST['email'] ?? ''),
            'nombre_representante'=> strtoupper(trim($_POST['nombre_rep_legal'] ?? '')),
            'dni_representante'   => strtoupper(trim($_POST['dni_rep_legal'] ?? '')),
            'cargo' => strtoupper(trim($_POST['cargo_rep_legal'] ?? '')),
        ];

        $idNuevoConvenio = $this->convenio->guardarNuevoConvenio($datos);

        if ($idNuevoConvenio) {
            // Añadir automáticamente a favoritos del tutor que lo registró
            $idTutor = $_POST['id_tutor_registro'];
            $this->convenio->añadirAFavoritos($idTutor, $idNuevoConvenio);
        }

        ob_end_clean(); // ← limpia cualquier output previo
        header('Location: /PROYECTO/index.php?tab=1');
        exit();
    }
}
?>