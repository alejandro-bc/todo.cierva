<?php
require "todo.php";  // Incluye la clase Todo y la lógica de conexión a la base de datos
require "DB.php";
function return_response($status, $statusMessage, $data) {
    header("HTTP/1.1 $status $statusMessage");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
}

// Recibir el cuerpo de la solicitud (que contiene los datos de la nueva tarea)
$bodyRequest = file_get_contents("php://input");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Conexión a la base de datos
        $db = new DB();
        
        // Crear un nuevo objeto Todo e inicializarlo con los datos recibidos en JSON
        $new_todo = new Todo;
        $new_todo->jsonConstruct($bodyRequest);
        
        // Insertar la nueva tarea en la base de datos
        $new_todo->insert($db->connection);

        // Obtener la lista actualizada de tareas desde la base de datos
        $todo_list = Todo::DB_selectAll($db->connection);

        // Convertir la lista de tareas a JSON y devolverla al cliente
        return_response(200, "OK", $todo_list);
        break;

    default:
        // Si se utiliza un método HTTP diferente a POST, devolver error 405
        return_response(405, "Method Not Allowed", null);
        break;
}
?>
