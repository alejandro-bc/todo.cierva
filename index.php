<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List Minimalista</title>
    <style>
        /* Asegura que el fondo cubra toda la ventana */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Estilos de fondo y layout */
        body {
            font-family: Arial, sans-serif;
            background-color: #bbdefb; /* Azul suave */
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Alinea el contenido en la parte superior */
            min-height: 100vh; /* Asegura que el cuerpo tome la altura completa */
            padding: 20px;
            box-sizing: border-box;
        }

        h2 {
            font-size: 1.4em;
            color: #555;
            margin-bottom: 15px;
            font-weight: 400;
        }

        /* Estilos para el formulario de entrada */
        input[type="text"] {
            padding: 8px;
            font-size: 1em;
            border: none;
            border-bottom: 2px solid #007bff;
            outline: none;
            width: 250px;
            margin-bottom: 15px;
            background: transparent;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #0056b3;
        }

        button {
            padding: 8px 16px;
            font-size: 0.9em;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Estilos para la lista de tareas */
        ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
            max-width: 400px;
        }

        li {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 20px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: box-shadow 0.2s;
        }

        li:hover {
            box-shadow: 0px 6px 16px rgba(0, 0, 0, 0.1);
        }

        li span {
            flex: 1;
            font-size: 1em;
            color: #333;
        }

        /* Botones de editar y eliminar */
        li button {
            margin-left: 8px;
            padding: 6px 10px;
            font-size: 0.8em;
            border-radius: 15px;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        li button:first-child {
            background-color: #ffb74d;
        }

        li button:first-child:hover {
            background-color: #ff9800;
        }

        li button:last-child {
            background-color: #ef5350;
        }

        li button:last-child:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>
    <!-- Campo de entrada para la nueva tarea -->
    <label for="content" style="display:none;">Nueva tarea:</label>
    <input type="text" id="content" placeholder="Nueva tarea">
    <button id="guardar">Añadir</button><br><br>

    <h2>Tareas</h2>
    <ul id="tareas">
        <?php
        require "DB.php";
        require "todo.php";

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try {
            $db = new DB;
            $todo_list = Todo::DB_selectAll($db->connection);
            if (empty($todo_list)) {
                echo "<li style='text-align:center;'>No hay tareas aún.</li>";
            }
            foreach ($todo_list as $row) {
                echo "<li id='task-" . $row->getItem_id() . "'>" .
                     "<span id='content-" . $row->getItem_id() . "'>" . htmlspecialchars($row->getContent()) . "</span>" .
                     " <button onclick='editarTarea(" . $row->getItem_id() . ")'>Editar</button>" .
                     " <button onclick='eliminarTarea(" . $row->getItem_id() . ")'>Eliminar</button></li>";
            }
        } catch (PDOException $e) {
            echo "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        ?>
    </ul>

    <script>
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

        function editarTarea(item_id) {
            const contentSpan = document.getElementById('content-' + item_id);
            const currentContent = contentSpan.innerText;

            const newContent = prompt("Edita la tarea:", currentContent);
            if (newContent === null || newContent === "") {
                return;
            }

            const url = 'http://lamp.local/controller.php';
            const postData = { item_id: item_id, content: newContent, action: 'update' };

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => actualizarLista(data))
            .catch(error => console.error('Error en la solicitud POST:', error));
        }

        function actualizarLista(data) {
            const lista = document.getElementById('tareas');
            lista.innerHTML = '';
            data.forEach(item => {
                const li = document.createElement("li");
                li.id = 'task-' + item.item_id;
                li.innerHTML = `<span id='content-${item.item_id}'>${item.content}</span> 
                                <button onclick='editarTarea(${item.item_id})'>Editar</button> 
                                <button onclick='eliminarTarea(${item.item_id})'>Eliminar</button>`;
                lista.appendChild(li);
            });
            document.getElementById('content').value = '';
        }

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
