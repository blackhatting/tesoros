<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__)); // Un paso atrás para llegar a la raíz del proyecto
}

require_once(BASE_PATH . '/db/Database.php');
use PHPMailer\PHPMailer\PHPMailer; // Importar PHPMailer
use PHPMailer\PHPMailer\Exception; // Importar Exception


require_once BASE_PATH . '/vendor/autoload.php'; // Cargar autoload de Composer

class Registro extends Database
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Función para comprobar si el usuario existe
    public function usuarioExiste($nombre, $correo)
    {
        $query = "SELECT * FROM usuarios WHERE nombre = :nombre OR correo = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Función para obtener los roles de la base de datos
    public function obtenerRoles()
    {
        try {
            $query = "SELECT id, nombre FROM roles";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Función para registrar nuevo usuario en la tabla usuarios
    public function registrarUsuario($nombre, $correo, $contrasena, $rol_id, $codigo_verificacion)
    {
        try {
            $query = "INSERT INTO usuarios (nombre, correo, contrasena, rol_id, codigo_verificacion) VALUES (:nombre, :correo, :contrasena, :rol_id, :codigo_verificacion)";
            $stmt = $this->conn->prepare($query);
            //hash de contraseña mediante ARGON2
            $hashed_password = password_hash($contrasena, PASSWORD_ARGON2ID);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':contrasena', $hashed_password);
            $stmt->bindParam(':rol_id', $rol_id);
            $stmt->bindParam(':codigo_verificacion', $codigo_verificacion);

            // Ejecutar la consulta
            return $stmt->execute(); // Retorna true si se ejecutó correctamente
        } catch (PDOException $error) {
            error_log("Error al registrar usuario: " . $error->getMessage()); // Registrar error
            return false; // Retornar false en caso de error
        }
    }

    public function enviarCorreoVerificacion($correo, $nombre, $codigo_verificacion)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
        $mail->SMTPAuth = true; // Habilitar autenticación SMTP
        $mail->Username = 'borjamaiques@gmail.com'; // Tu correo
        $mail->Password = 'qqdn qhgv omkq gnns'; // Contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Encriptación SSL
        $mail->Port = 465; // Usamos el puerto 465 para SSL (no TLS)
        $mail->CharSet = 'UTF-8'; // Asegura que el correo use UTF-8
        $mail->Encoding = 'base64'; // Codificación del cuerpo del mensaje

        /*
        ============================================================================
        ALERTA PORQUE ESTO ESTO SE CARGA TODOS LOS CERTIFICADOS DE SEGURIDAD
        ============================================================================
        */
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false, // Verificar certificados del servidor
                'verify_peer_name' => false, // Verificar nombre del servidor
                'allow_self_signed' => true, // No permitir certificados autofirmados
            ),
        );
        /*
        ============================================================================
        HASTA AQUI
        ============================================================================
        */

        // Remitente
        $mail->setFrom('borjamaiques@gmail.com', 'Tesoros del tiempo');
        // Destinatario
        $mail->addAddress($correo, $nombre);

        // Contenido del correo
        $mail->isHTML(true); // Permitir HTML
        $mail->Subject = 'Verificación de Correo';
        $mail->Body = 'Hola ' . htmlspecialchars($nombre) . ',<br> Por favor verifica tu correo haciendo clic en el siguiente enlace: <a href="http://tu-dominio.com/tu-url-de-verificacion?codigo=' . $codigo_verificacion . '">Verificar Correo</a>';

        // Enviar el correo
        $mail->send();
        return true; // Retornar true si el correo se envió correctamente
    } catch (Exception $e) {
        error_log("Error al enviar el correo: " . $mail->ErrorInfo); // Loguear el error
        return false; // Retornar false si falla
    }
}


public function verificarUsuario($nombre, $contrasena)
{
    try {
        $sql = "SELECT id, contrasena, intentos_fallidos, cuenta_bloqueada FROM usuarios WHERE nombre = :nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar si la cuenta está bloqueada
            if ($usuario['cuenta_bloqueada']) {
                return "cuenta_bloqueada"; // Retornar este valor si la cuenta está bloqueada
            }

            // Verificar la contraseña
            if (password_verify($contrasena, $usuario['contrasena'])) {
                // Reiniciar intentos fallidos al iniciar sesión correctamente
                $this->reiniciarIntentos($usuario['id']);
                return $usuario['id']; // La contraseña es correcta, retorna el ID
            } else {
                // Incrementar los intentos fallidos
                $this->incrementarIntentos($usuario['id'], $usuario['intentos_fallidos']);
                return false; // Contraseña incorrecta
            }
        } else {
            return false; // Usuario no encontrado
        }
    } catch (PDOException $error) {
        echo "Error en la verificación del usuario: " . $error->getMessage();
        return false;
    }
}

// Método para incrementar los intentos fallidos
private function incrementarIntentos($usuarioId, $intentosFallidos)
{
    $intentosFallidos++;
    if ($intentosFallidos >= 3) {
        // Bloquear la cuenta si se alcanzan 3 intentos fallidos
        $sql = "UPDATE usuarios SET intentos_fallidos = :intentos_fallidos, cuenta_bloqueada = 1 WHERE id = :id";
    } else {
        // Solo actualizar los intentos fallidos
        $sql = "UPDATE usuarios SET intentos_fallidos = :intentos_fallidos WHERE id = :id";
    }
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':intentos_fallidos', $intentosFallidos);
    $stmt->bindParam(':id', $usuarioId);
    $stmt->execute();
}

// Método para reiniciar los intentos fallidos
private function reiniciarIntentos($usuarioId)
{
    $sql = "UPDATE usuarios SET intentos_fallidos = 0, cuenta_bloqueada = 0 WHERE id = :id"; // También reinicia el estado de bloqueado
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $usuarioId);
    $stmt->execute();
}


    // usuario verificado por correo
    public function usuarioVerificado($codigo_verificacion)
    {
        try {
            $query = "UPDATE usuarios SET activo = 1 WHERE codigo_verificacion = :codigo_verificacion";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':codigo_verificacion', $codigo_verificacion);
            return $stmt->execute(); // Retorna verdadero si se actualizó correctamente
        } catch (PDOException $error) {
            echo "Error: " . $error->getMessage();
            return false;
        }
    }
}
?>