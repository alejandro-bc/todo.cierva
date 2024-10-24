<?php

class Todo implements \JsonSerializable {
    private int $item_id;
    private string $content;

    // Inicializa todas las variables del objeto con las pasadas por parámetros
    public function parametersConstruct(int $item_id, string $content) {
        $this->item_id = $item_id;
        $this->content = $content;
    }

    // Inicializa todas las variables con el JSON pasado por parámetro
    public function jsonConstruct($json) {
        foreach (json_decode($json, true) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    // Método getter para item_id
    public function getItem_id() {
        return $this->item_id;
    }

    // Método getter para content
    public function getContent() {
        return $this->content;
    }

    // Método para insertar una nueva tarea en la base de datos
    public function insert($dbconn) {
        $stmt = $dbconn->prepare("INSERT INTO todo_list (content) VALUES (:content)");
        $stmt->bindParam(':content', $this->content);
        $stmt->execute();
        $this->item_id = $dbconn->lastInsertId();  // Obtener el último ID insertado
    }

    // Método para devolver todos los elementos de la base de datos usando consultas preparadas
    public static function DB_selectAll($dbconn) {
        $stmt = $dbconn->prepare("SELECT item_id, content FROM todo_list");
        $stmt->execute();
        $todo_list = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $new_todo = new Todo;
            $new_todo->parametersConstruct($row['item_id'], $row['content']);
            $todo_list[] = $new_todo;
        }

        return $todo_list;
    }

    // Implementación del método jsonSerialize
    public function jsonSerialize() {
        return [
            'item_id' => $this->item_id,
            'content' => $this->content,
        ];
    }
}
