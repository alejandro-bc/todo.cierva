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
    <input type="text" id="content" placeholder="Ingresa una tarea"><br><br>
    <!-- Botón para guardar la tarea -->
    <button id="guardar">Guardar</button>

    <h2>Tareas</h2>
    <ul id="tareas">
        <!-- Aquí se mostrarán las tareas -->
        <?php
        // Mostrar las tareas que ya están en la base de datos al cargar la página
        require "DB.php";
        require "todo.php";

        try {
            $db = new DB;
            $todo_list = Todo::DB_selectAll($db->connection);
            foreach ($todo_list as $row) {
                echo "<li>" . $row->getItem_id() . ". " . $row->getContent() . "</li>";
            }
        } catch (PDOException $e) {
            echo "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        ?>
    </ul>

    <script>
        // Función para manejar el envío del formulario y la inserción de nuevas tareas
        document.getElementById('guardar').addEventListener('click', function () {
            const content = document.getElementById('content').value;

            if (!content) {
                alert('Por favor, introduce una tarea.');
                return;
            }

            const url = 'http://lamp.local/controller.php';  
            const postData = { content: content };

            // Hacer la solicitud POST al servidor
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())  // Convertir la respuesta en JSON
            .then(data => {
                const lista = document.getElementById('tareas');
                lista.innerHTML = '';  // Limpiar la lista antes de agregar los nuevos datos

                // Iterar sobre el array de tareas devuelto por el servidor
                data.forEach(item => {
                    const li = document.createElement("li");
                    li.appendChild(document.createTextNode(item.item_id + ". " + item.content));
                    lista.appendChild(li);  // Añadir cada tarea a la lista
                });

                // Limpiar el campo de texto después de insertar
                document.getElementById('content').value = '';
            })
            .catch(error => console.error('Error en la solicitud POST:', error));  // Manejo de errores
        });
    </script>

</body>
</html>
