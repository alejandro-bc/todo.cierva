<?php
class Todo implements \JsonSerializable {
    private int $item_id;
    private string $content;

    public function parametersConstruct(int $item_id, string $content) {
        $this->item_id = $item_id;
        $this->content = $content;
    }

    public function jsonConstruct($json) {
        foreach (json_decode($json, true) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    // Métodos getter para acceder a item_id y content
    public function getItem_id() {
        return $this->item_id;
    }

    public function getContent() {
        return $this->content;
    }

    public function insert($dbconn) {
        $stmt = $dbconn->prepare("INSERT INTO todo_list (content) VALUES (:content)");
        $stmt->bindParam(':content', $this->content);
        $stmt->execute();
        $this->item_id = $dbconn->lastInsertId();
    }

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

    public static function updateContentById($dbconn, $item_id, $content) {
        $stmt = $dbconn->prepare("UPDATE todo_list SET content = :content WHERE item_id = :item_id");
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function deleteById($dbconn, $item_id) {
        $stmt = $dbconn->prepare("DELETE FROM todo_list WHERE item_id = :item_id");
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function jsonSerialize() {
        return [
            'item_id' => $this->item_id,
            'content' => $this->content,
        ];
    }
}
?>
