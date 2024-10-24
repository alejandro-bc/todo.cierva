<?php
class DB {
    public $connection;

    public function __construct() {
        $db_host = "localhost";
        $db_name = "ejemplo";  // Cambia por tu base de datos
        $db_user = "prueba";   // Cambia por tu usuario
        $db_pass = "Estoesunaprueba123#";  // Cambia por tu contraseña

        try {
            $this->connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error de conexión a la base de datos.");
        }
    }

    public function __destruct() {
        $this->connection = null;
    }
}
