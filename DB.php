<?php
class DB {
    public $connection;

    public function __construct() {
        $db_host = "localhost";
        $db_name = "ejemplo";    // Nombre de la base de datos
        $db_user = "prueba";     // Usuario
        $db_pass = "Estoesunaprueba123#"; // Contraseña

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
?>
