<?php

class Database {
    private $host = 'localhost';
    private $db = 'tesoros_tiempo';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    private $conn;

    // Constructor para inicializar la conexión a la base de datos
    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Error de conexión: ' . $e->getMessage());
        }
    }

    // Método para obtener la instancia PDO
    public function getConnection() {
        return $this->conn;
    }
}
