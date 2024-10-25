<?php
require "todo.php";  
require "DB.php";

function return_response($status, $statusMessage, $data) {
    header("HTTP/1.1 $status $statusMessage");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
}

$bodyRequest = file_get_contents("php://input");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $db = new DB();
        $new_todo = new Todo;
        $new_todo->jsonConstruct($bodyRequest);
        $new_todo->insert($db->connection);
        $todo_list = Todo::DB_selectAll($db->connection);
        return_response(200, "OK", $todo_list);
        break;

    case 'DELETE':
        $db = new DB();
        $data = json_decode($bodyRequest, true);
        if (isset($data['item_id'])) {
            Todo::deleteById($db->connection, $data['item_id']);
            $todo_list = Todo::DB_selectAll($db->connection);
            return_response(200, "OK", $todo_list);
        } else {
            return_response(400, "Bad Request", ["error" => "item_id missing"]);
        }
        break;

    default:
        return_response(405, "Method Not Allowed", null);
        break;
}
?>
