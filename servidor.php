<?php
require_once("lib/nusoap.php");
require_once("datos.php");

//CONFIGURACIÓN DEL SERVIDOR
$namespace = "http://localhost/actividad6/servidor.php";
$server = new soap_server(); //llamamos a la funcion, crea un nuevo servidor
$server->configureWSDL("Servidor de musica", $namespace);//protocolo de definicion de lo que va  a tener mi servicio web
$server->soap_defencoding = "UTF-8";//codificacion de datos


//FUNCIONES DEL SERVIDOR

function insertarGrupo($nombre, $genero) {
    $conexion = mysqli_connect($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['pass'], $GLOBALS['dbname']);

    if (!$conexion) {
        die("Error de conexión a la base de datos: " . mysqli_connect_error());
    }

    // Insertar nuevo grupo
    $query = "INSERT INTO grupo (nombre, genero) VALUES ('$nombre', $genero)";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $id_grupo_insertado = mysqli_insert_id($conexion);
        mysqli_close($conexion);
        return $id_grupo_insertado;
    } else {
        mysqli_close($conexion);
        return "Error al insertar el grupo: " . mysqli_error($conexion);
    }
}



function obtenerGeneros() {
    $conexion = mysqli_connect($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['pass'], $GLOBALS['dbname']);

    if (!$conexion) {
        die("Error de conexión a la base de datos: " . mysqli_connect_error());
    }

    // Consulta para obtener todos los géneros
    $query = "SELECT id_genero, nombre FROM Genero";
    $result = mysqli_query($conexion, $query);

    // Construir el array de géneros
    $generos = array();
    while ($fila = mysqli_fetch_assoc($result)) {
        $genero = array('id_genero' => $fila['id_genero'], 'nombre' => $fila['nombre']);
        $generos[] = $genero;
    }

    mysqli_close($conexion);

    return $generos;
}

function obtenerGruposPorGenero($id_genero) {
    $conexion = mysqli_connect($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['pass'], $GLOBALS['dbname']);

    if (!$conexion) {
        die("Error de conexión a la base de datos: " . mysqli_connect_error());
    }

    // Consulta para obtener grupos por género
    $query = "SELECT id_grupo, nombre FROM grupo WHERE genero = $id_genero";
    $result = mysqli_query($conexion, $query);

    // Construir el array de grupos
    $grupos = array();
    while ($fila = mysqli_fetch_assoc($result)) {
        $grupo = array('id_grupo' => $fila['id_grupo'], 'nombre' => $fila['nombre']);
        $grupos[] = $grupo;
    }

    mysqli_close($conexion);

    return $grupos;
}


//REGISTRO DE FUNCIONES EN EL SERVIDOR

//insertarGrupo
$server->register(
    'insertarGrupo',
    array('nombre' => 'xsd:string', 'genero' => 'xsd:int'),
    array('return' => 'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Inserta un nuevo grupo en la base de datos'
);

//obtenerGeneros
$server->register(
    'obtenerGeneros',
    array(),
    array('return' => 'tns:ArrayGeneros'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Devuelve un array con todos los géneros de la base de datos'
);

//Definicion de tipo complejo para el array de generos
$server->wsdl->addComplexType(
    'Genero',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'id_genero' => array('name' => 'id_genero', 'type' => 'xsd:int'),
        'nombre' => array('name' => 'nombre', 'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'ArrayGeneros',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:Genero[]')
    ),
    'tns:Genero'
);

//registro de la funcion obtenerGruposPorGenero
$server->register(
    'obtenerGruposPorGenero',
    array('id_genero' => 'xsd:int'),
    array('return' => 'tns:ArrayGrupos'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Devuelve un array con todos los grupos de un determinado género'
);

// Definición de tipo complejo para el array de grupos
$server->wsdl->addComplexType(
    'Grupo',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'id_grupo' => array('name' => 'id_grupo', 'type' => 'xsd:int'),
        'nombre' => array('name' => 'nombre', 'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'ArrayGrupos',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:Grupo[]')
    ),
    'tns:Grupo'
);


// Procesar la solicitud del cliente
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service(file_get_contents("php://input"));// finalmente añadimos la siguiente linea, encargada de iniciar el servicio y dejarlo en escucha de posibles peticiones
?>