<?php
require_once "./Core/Conexion.php"; 

class Convenios {
    private $conn; 

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    public function buscar($termino) {
        $query = "SELECT id_convenio, nombre_empresa, cif, municipio, telefono, mail, nombre_representante 
                  FROM convenios 
                  WHERE nombre_empresa LIKE ? OR cif LIKE ?";
        $stmt = $this->conn->prepare($query);
        $param = "%$termino%";
        $stmt->execute([$param, $param]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function añadirAFavoritos($id_tutor, $id_convenio) {
        $sql = "INSERT INTO mi_listado (id_tutor, id_convenio) VALUES (:id_t, :id_conv)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_t', $id_tutor);
        $stmt->bindParam(':id_conv', $id_convenio);
        return $stmt->execute();
    }

    public function obtenerFavoritos($id_tutor) {
        $sql = "SELECT c.* FROM convenios c
                INNER JOIN mi_listado m ON c.id_convenio = m.id_convenio
                WHERE m.id_tutor = :id_tutor";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function eliminarDeFavoritos($id_tutor, $id_convenio) {
        $query = "DELETE FROM mi_listado WHERE id_tutor = ? AND id_convenio = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_tutor, $id_convenio]);
    }

    public function estaEnUso($id_convenio) {
        $query = "SELECT COUNT(*) FROM asignaciones WHERE id_convenio = :id_convenio";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id_convenio' => $id_convenio]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * NUEVA FUNCIÓN: Guarda en la tabla convenios_nuevos (pendientes)
     * Incluye el id_ciclo capturado del formulario
     */
        // En Modelo/Convenios.php
        public function guardarNuevoConvenioPendiente($datos) {
            $query = "INSERT INTO convenios_nuevos 
                        (nombre_empresa, cif, direccion, municipio, cp, pais, telefono, fax, mail, 
                        nombre_representante, dni_representante, cargo, id_ciclo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $datos['nombre_empresa'],      // Coincide con columna nombre_empresa
                $datos['cif'],                 // Coincide con columna cif
                $datos['direccion'],           // Coincide con columna direccion
                $datos['municipio'],           // Coincide con columna municipio
                $datos['cp'],                  // Coincide con columna cp
                $datos['pais'],                // Coincide con columna pais
                $datos['telefono'],            // Coincide con columna telefono
                $datos['fax'],                 // Coincide con columna fax
                $datos['mail'],                // Coincide con columna mail (Viene de $datos['mail'])
                $datos['nombre_representante'],// Coincide con columna nombre_representante
                $datos['dni_representante'],   // Coincide con columna dni_representante
                $datos['cargo'],               // Coincide con columna cargo
                $datos['id_ciclo']             // Esta es la columna extra que añadimos a convenios_nuevos
            ]);
        }

    /**
     * NUEVA FUNCIÓN: Obtiene los convenios en proceso para un ciclo específico
     * Se usa para rellenar la tabla naranja/ámbar de la vista
     */
    public function obtenerConveniosEnProceso($id_ciclo) {
        $query = "SELECT * FROM convenios_nuevos WHERE id_ciclo = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_ciclo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>