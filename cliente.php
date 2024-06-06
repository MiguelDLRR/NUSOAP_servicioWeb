
<?php
require_once("lib/nusoap.php");

// Crear instancia del cliente NuSOAP
$client = new soapclient('http://localhost/actividad6/servidor.php?wsdl');



// Verificar si se ha enviado el formulario para insertar nuevo grupo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombreGrupo"]) && isset($_POST["genero"])) {
    // Recoger los datos del formulario
    $nombreGrupo = $_POST["nombreGrupo"];
    $generoSeleccionado = $_POST["genero"];

    // Llamar a la función en el servidor para insertar el nuevo grupo
    $resultado = $client->insertarGrupo($nombreGrupo, $generoSeleccionado);

    // Mostrar el resultado de la operación
    echo "<p>Resultado: $resultado</p>";
}

// Obtener la lista de géneros desde el servidor
$generos = $client->obtenerGeneros();

// Verificar si se ha enviado el formulario para obtener grupos por género
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["generoSeleccionado"])) {
    $generoSeleccionado = $_POST["generoSeleccionado"];

    // Llamar a la función en el servidor para obtener los grupos por género
    $gruposPorGenero = $client->obtenerGruposPorGenero($generoSeleccionado);

    // Mostrar la tabla con los grupos por género
    echo "<h3>Grupos Musicales del Género Seleccionado</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID Grupo</th><th>Nombre</th></tr>";

    foreach ($gruposPorGenero as $grupo) {
        echo "<tr><td>$grupo->id_grupo</td><td>$grupo->nombre</td></tr>";
    }

    echo "</table>";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GRUPOS MUSICA</title>
</head>
<body>

<h3>Dar de alta nuevos grupos</h3>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="nombreGrupo">Nombre del grupo:</label>
    <input type="text" name="nombreGrupo" required>
    
    <label for="genero">Género:</label>
    <select name="genero" required>
        <?php
        // Mostrar la lista de géneros en el desplegable
        foreach ($generos as $genero) {
            echo "<option value=\"$genero->id_genero\">$genero->nombre</option>";
        }
        ?>
    </select>

    <button type="submit">Enviar</button>
</form>
<br>
<h3> Ver los Grupos de cada Género</h3>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="generoSeleccionado">Seleccione un Género:</label>
    <select name="generoSeleccionado" required>
        <?php
        // Mostrar la lista de géneros en el desplegable
        foreach ($generos as $genero) {
            echo "<option value=\"$genero->id_genero\">$genero->nombre</option>";
        }
        ?>
    </select>

    <button type="submit">Ver Grupos</button>
</form>
</body>
</html>
