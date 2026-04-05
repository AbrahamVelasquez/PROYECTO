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
        // CAMBIADO: id_usuario -> id_tutor (según tu esquema)
        $sql = "INSERT INTO mi_listado (id_tutor, id_convenio) VALUES (:id_t, :id_conv)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_t', $id_tutor);
        $stmt->bindParam(':id_conv', $id_convenio);
        return $stmt->execute();
    }

    public function obtenerFavoritos($id_tutor) {
        // CAMBIADO: m.id_usuario -> m.id_tutor (según tu esquema)
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

public function guardarNuevoConvenio($datos) {
    $query = "INSERT INTO convenios 
                (nombre_empresa, cif, direccion, municipio, cp, pais, telefono, fax, mail, 
                 nombre_representante, dni_representante, cargo)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    try {
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $datos['nombre_empresa'],
            $datos['cif'],
            $datos['direccion'],
            $datos['municipio'],
            $datos['cp'],
            $datos['pais'],
            $datos['telefono'],
            $datos['fax'],
            $datos['mail'],
            $datos['nombre_representante'],
            $datos['dni_representante'],
            $datos['cargo']
        ]);
        return $this->conn->lastInsertId();
    } catch (PDOException $e) {
        die("ERROR SQL: " . $e->getMessage());
    }
}

}
?>