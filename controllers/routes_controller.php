<?php
// Iniciar la sesión al principio
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Definir la ruta base
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

ob_start();
// Requerir archivos esenciales
require_once(BASE_PATH . '/db/Database.php');
require_once(BASE_PATH . '/models/autoload.php');
require_once(BASE_PATH . '/views/header_view.phtml');
if (isset($_SESSION['usuario_id'])) {
    require_once(BASE_PATH . '/views/nav_bar.phtml');
}


require_once BASE_PATH . '/vendor/autoload.php'; // Cargar autoload de Composer

// Acción solicitada
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Variable que almacena la vista a cargar
$vista_cuerpo = '';
$db = new Database();
$registroModel = new Registro($db->getConnection()); // Pasamos la conexión PDO
$perfilesModel = new PerfilesModel($db->getConnection());

// Switch para gestionar las diferentes rutas (acciones)
switch ($action) {
    case 'inici':
        // Cargar la vista de inicio
        $vista_cuerpo = BASE_PATH . '/views/index_view.phtml';
        break;

    case 'crear_cuenta':
        $roles = $registroModel->obtenerRoles();
        // Cargar la vista de crear cuenta
        $vista_cuerpo = BASE_PATH . '/views/registro_view.phtml';
        break;


    case 'procesar_registro':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['nombre'], $_POST['correo'], $_POST['contrasena'], $_POST['rol'])) {
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $contrasena = $_POST['contrasena'];
                $rol_id = $_POST['rol'];

                // Verificar si el usuario ya existe
                if ($registroModel->usuarioExiste($nombre, $correo)) {
                    // Usuario ya existe, mostrar mensaje
                    header('Location: usuario_existe');
                } else {
                    // Generar un código de verificación
                    $codigoVerificacion = bin2hex(random_bytes(16)); // Código de 32 caracteres

                    // Intentar registrar el usuario
                    if ($registroModel->registrarUsuario($nombre, $correo, $contrasena, $rol_id, $codigoVerificacion)) {
                        // Enviar correo de verificación
                        $registroModel->enviarCorreoVerificacion($correo, $nombre, $codigoVerificacion);
                        // Registro exitoso
                        header('Location: correo_exitoso');
                    } else {
                        // Error en el registro
                        echo "Ocurrió un error al registrar el usuario. Por favor, inténtalo de nuevo.";
                    }
                }
            } else {
                // Campos vacíos
                echo "Por favor, rellena todos los campos.";
            }
        }
        break;
    case 'correo_exitoso':
        $vista_cuerpo = BASE_PATH . '/views/registroExitoso_view.phtml';
        break;
    case 'correo_erroneo':
        $vista_cuerpo = BASE_PATH . '/views/registroErroneo_view.phtml';
        break;
    case 'usuario_existe':
        $vista_cuerpo = BASE_PATH . '/views/usuarioExiste_view.phtml';
        break;




        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['nombre'], $_POST['contrasena'])) {
                    $nombre = $_POST['nombre'];
                    $contrasena = $_POST['contrasena'];
        
                    // Verificar el usuario y contraseña
                    $resultado = $registroModel->verificarUsuario($nombre, $contrasena);
        
                    if (is_numeric($resultado)) { // Verificar si se obtuvo un ID de usuario
                        // Iniciar sesión
                        $_SESSION['usuario_id'] = $resultado; // $resultado es el ID del usuario
                        $_SESSION['nombre'] = $nombre;
        
                        // Redirigir a la página de inicio o perfil
                        header("Location: logueado");
                        exit();
                    } elseif ($resultado === "cuenta_bloqueada") {
                        // La cuenta está bloqueada
                        $error = "La cuenta está bloqueada. Contacta con el soporte.";
                    } else {
                        // Usuario o contraseña incorrectos
                        $error = "Usuario o contraseña incorrectos.";
                    }
                }
            }
        


        // Cargar la vista de login
        $vista_cuerpo = BASE_PATH . '/views/index_view.phtml';
        break;

    case 'verificar_usuario':
        if (isset($_GET['codigo'])) {
            $codigo_verificacion = $_GET['codigo'];

            // Llamar a la función para verificar el usuario
            if ($registroModel->usuarioVerificado($codigo_verificacion)) {
                $mensaje = "Cuenta verificada con éxito. Puedes iniciar sesión.";
            } else {
                $mensaje = "Error al verificar la cuenta. Asegúrate de que el enlace sea correcto o que ya no haya sido verificado.";
            }
        } else {
            $mensaje = "Código de verificación no proporcionado.";
        }

        // Aquí puedes cargar una vista para mostrar el mensaje
        $vista_cuerpo = BASE_PATH . '/views/verificacion_view.phtml'; // Cargar una vista para el mensaje de verificación
        break;

        case 'logout':
            // Destruir la sesión
            session_unset(); // Eliminar todas las variables de sesión
            session_destroy(); // Destruir la sesión
        
            // Redirigir al usuario a la página de inicio
            header('Location: inici');
            exit(); // Salir para evitar la ejecución de código adicional
        
        

    case 'logueado':
        // Cargar la vista de usuario logueado
        $vista_cuerpo = BASE_PATH . '/views/login_view.phtml';
        break;
    
    /*
    ============================================
    Consultar su perfil
    ============================================
    */
    case 'ver_perfil':
        // Asegúrate de que el usuario esté logueado
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
    
            // Obtener los datos del usuario desde el modelo
            $datos_usuario = $perfilesModel->obtenerDatosUsuario($usuario_id);
    
            // Obtener empleos con privacidad
            $empleosConPrivacidad = $perfilesModel->obtenerEmpleoConPrivacidad($usuario_id);
            
            // Obtener formaciones con privacidad
            $formacionConPrivacidad = $perfilesModel->obtenerFormacionConPrivacidad($usuario_id);
    
            // Obtener estado civil con privacidad
            $estadoCivilConPrivacidad = $perfilesModel->obtenerEstadoCivilConPrivacidad($usuario_id);
            //Obtener ubicación con privacidad
            $ubicacionConPrivacidad = $perfilesModel->obtenerUbicacionConPrivacidad($usuario_id);

            // Comprobar si se obtuvieron los datos
            if ($datos_usuario) {
                // Pasar los datos a la vista
                $vista_cuerpo = BASE_PATH . '/views/miperfil_view.phtml'; 
            } else {
                $mensaje = "No se encontraron datos para el usuario.";
                // Aquí podrías redirigir a una página de error o mostrar un mensaje
            }
        } else {
            $mensaje = "Debes iniciar sesión para ver tu perfil.";
            // Aquí podrías redirigir al inicio de sesión
        }
        break;
    
    

    /*
    ============================================
    bloquear perfiles
    ============================================
    */
    case 'actualizar_visibilidad':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $esPublico = isset($_POST['es_publico']) ? 1 : 0; // Recibir valor de un checkbox
            $perfilesModel->actualizarVisibilidadPerfil($_SESSION['usuario_id'], $esPublico);
            header("Location: perfil");
            exit();
        }
        break;

    case 'bloquear_usuario':
        if (isset($_GET['id'])) {
            $bloqueadoId = $_GET['id'];
            $perfilesModel->bloquearUsuario($_SESSION['usuario_id'], $bloqueadoId);
            header("Location: perfil");
            exit();
        }
        break;

    default:
        // Cargar la vista por defecto
        $vista_cuerpo = BASE_PATH . '/views/index_view.phtml';
        break;
}

// Incluir la vista correspondiente
if ($vista_cuerpo != '') {
    require_once($vista_cuerpo);
}

// Solo incluir el nav_bar si el usuario ha iniciado sesión


// Incluir el footer
require_once(BASE_PATH . '/views/footer_view.phtml');
ob_end_flush();
?>