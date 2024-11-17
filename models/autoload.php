<?php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();

// Definir la ruta base
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once(BASE_PATH . '/models/registro_model.php');
require_once(BASE_PATH . '/models/perfiles_model.php');
