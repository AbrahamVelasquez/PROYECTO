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
            header("Location: index.php?busqueda=" . urlencode($_GET['busqueda'] ?? ''));
            exit();
        }

        // LÓGICA PARA ELIMINAR
        if (isset($_POST['btnEliminarFav'])) {
            $this->convenio->eliminarDeFavoritos($id_tutor_actual, $_POST['id_convenio_eliminar']);
            
            // Si hay algo en la búsqueda, lo mantenemos; si no, vamos a index puro
            $url = "index.php";
            if (!empty($_GET['busqueda'])) {
                $url .= "?busqueda=" . urlencode($_GET['busqueda']);
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
}
?>