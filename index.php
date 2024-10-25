<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List actualizable</title>
</head>
<body>
    <!-- Campo de entrada para la nueva tarea -->
    <label for="content">Nueva tarea:</label>
    <input type="text" id="content" placeholder="Ingresa una tarea">
    <button id="guardar">Guardar</button><br><br>

    <h2>Tareas</h2>
    <ul id="tareas">
        <?php
        require "DB.php";
        require "todo.php";

        try {
            $db = new DB;
            $todo_list = Todo::DB_selectAll($db->connection);
            foreach ($todo_list as $row) {
                echo "<li>" . $row->getItem_id() . ". " . $row->getContent() . 
                " <button onclick='eliminarTarea(" . $row->getItem_id() . ")'>Eliminar</button></li>";
            }
        } catch (PDOException $e) {
            echo "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        ?>
    </ul>

    <script>
        // Función para guardar una nueva tarea
        document.getElementById('guardar').addEventListener('click', function () {
            const content = document.getElementById('content').value;
            if (!content) {
                alert('Por favor, introduce una tarea.');
                return;
            }

            const url = 'http://lamp.local/controller.php';
            const postData = { content: content };

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => actualizarLista(data))
            .catch(error => console.error('Error en la solicitud POST:', error));
        });

        // Función para actualizar la lista de tareas en el HTML
        function actualizarLista(data) {
            const lista = document.getElementById('tareas');
            lista.innerHTML = '';
            data.forEach(item => {
                const li = document.createElement("li");
                li.innerHTML = item.item_id + ". " + item.content + 
                " <button onclick='eliminarTarea(" + item.item_id + ")'>Eliminar</button>";
                lista.appendChild(li);
            });
            document.getElementById('content').value = '';
        }

        // Función para eliminar una tarea usando fetch
        function eliminarTarea(item_id) {
            const url = 'http://lamp.local/controller.php';
            const postData = { item_id: item_id };

            fetch(url, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => actualizarLista(data))
            .catch(error => console.error('Error en la solicitud DELETE:', error));
        }
    </script>
</body>
</html>
