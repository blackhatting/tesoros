<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once(BASE_PATH . '/db/Database.php');

class PerfilesModel extends Database
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Actualizar la visibilidad del perfil
    public function actualizarVisibilidadPerfil($usuarioId, $esPublico)
    {
        $query = "UPDATE usuarios SET es_publico = :es_publico WHERE id = :usuarioId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':es_publico', $esPublico, PDO::PARAM_BOOL);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Bloquear a otro usuario
    public function bloquearUsuario($usuarioId, $bloqueadoId)
    {
        $query = "INSERT INTO bloqueos (id_usuario, id_bloqueado) VALUES (:usuarioId, :bloqueadoId)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(':bloqueadoId', $bloqueadoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Verificar si un usuario está bloqueado
    public function estaBloqueado($usuarioId, $bloqueadoId)
    {
        $query = "SELECT * FROM bloqueos WHERE id_usuario = :usuarioId AND id_bloqueado = :bloqueadoId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(':bloqueadoId', $bloqueadoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0; // Retorna true si está bloqueado
    }

    public function obtenerDatosUsuario($usuario_id)//OK FUNCIONANDO
    {
        try {
            $query = "
            SELECT u.id, e.trabajo, f.estudios, c.estado
            FROM usuarios u
            LEFT JOIN empleos e ON u.empleo_id = e.id
            LEFT JOIN formacion f ON u.formacion_id = f.id
            LEFT JOIN estado_civil c ON u.estadocivil_id = c.id
            WHERE u.id = :usuario_id
        ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Error al obtener datos del usuario: " . $error->getMessage());
            return null; // O manejar el error de otra manera
        }
    }
    public function obtenerEmpleoConPrivacidad($usuario_id) //OK FUNCIONANDO
    {
        // Obtener los empleos y la privacidad relacionada
        $sql = "SELECT e.trabajo, 
                   p.privacidad, 
                   p.private_img 
            FROM empleos e
            LEFT JOIN privacidad p ON e.privacidad_id = p.id
            WHERE e.usuarios_id = :usuarios_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuarios_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerFormacionConPrivacidad($usuario_id)//OK FUNCIONANDO
    {
        $sql = "SELECT f.estudios, 
                   p.privacidad, 
                   p.private_img 
            FROM formacion f
            LEFT JOIN privacidad p ON f.privacidad_id = p.id
            WHERE f.usuarios_id = :usuarios_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuarios_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerEstadoCivilConPrivacidad($usuario_id)
    {
        $sql = "SELECT uec.usuarios_id, ec.estado, p.privacidad, p.private_img
                FROM usuario_estado_civil uec
                INNER JOIN estado_civil ec ON uec.estado_civil_id = ec.id
                INNER JOIN privacidad p ON uec.privacidad_id = p.id
                WHERE uec.usuarios_id = :usuarios_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuarios_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    OBTENER UBICACIÓN
    */
    public function obtenerUbicacionConPrivacidad($usuario_id)
    {
        $sql = "SELECT u.pais, 
        p.privacidad, 
        p.private_img
        FROM ubicacion u
        LEFT JOIN privacidad p ON u.privacidad_id = p.id
        WHERE u.usuarios_id = :usuarios_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuarios_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}
