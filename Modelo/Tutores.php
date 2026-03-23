<?php
require_once "./Core/Conexion.php";

class Tutores {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::getConexion();
    }

    public function obtenerDatosPerfil($username) {
        // Ajustado según tu diagrama de BD
        $sql = "SELECT t.nombre, t.apellidos, cur.nombre_curso, cic.nombre_ciclo 
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                INNER JOIN ciclos cic ON t.id_ciclo = cic.id_ciclo
                INNER JOIN cursos cur ON cic.id_curso = cur.id_curso
                WHERE u.username = :username";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}