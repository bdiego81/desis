<?php
/**
 * CLASE CONTROLADORA QUE MANEJA LAS CONSULTAS A LA BASE DE DATOS.
 */

principal();

/**
 * Maneja peticiones GET que se reciben por llamado asincronico (ajax)
 */
function principal()
{
    //peticiones post son accedidas directamente, no atraves de este enrutador
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return;
    }

    //if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

    //maneja peticion a consultar.
    $action = $_SERVER['REQUEST_METHOD'] === 'GET' ? $_GET['action'] : $_POST['action'];

    switch ($action) {
        case "getRegiones":
            getRegiones();
            break;
        case "getComunas":
            getComunas();
            break;
        case "getCandidatos":
            getCandidatos();
            break;
    }
    //}
}

/**
 * Obtiene listado de regiones
 */
function getRegiones()
{
    //Obtiene datos de la conexion
    require_once('config.php');

    $conexion = mysqli_connect($host, $username, $password, $database);

    // Verificar conexion
    if (mysqli_connect_errno()) {
        die("Error al conectar con la base de datos: " . mysqli_connect_error());
    }

    // Realizar consulta para obtener las regiones
    $consulta = "SELECT id, nombre FROM regiones";
    $resultado = mysqli_query($conexion, $consulta);

    // Comprobar si se obtuvieron resultados
    if (mysqli_num_rows($resultado) > 0) {
        $regiones = array();

        // Obtener los datos de las regiones
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $region = array(
                'id' => $fila['id'],
                'nombre' => $fila['nombre']
            );
            $regiones[] = $region;
        }

        // Devolver las regiones como respuesta JSON
        header('Content-Type: application/json');
        echo json_encode($regiones);
    }

    // Cerrar conexión a la base de datos
    mysqli_close($conexion);
}

/**
 * Obtiene listado de candidatos
 */
function getCandidatos()
{
    //Obtiene datos de la conexion
    require_once('config.php');

    $conexion = mysqli_connect($host, $username, $password, $database);

    // Verificar conexion
    if (mysqli_connect_errno()) {
        die("Error al conectar con la base de datos: " . mysqli_connect_error());
    }

    // Realizar consulta para obtener los candidatos
    $consulta = "SELECT id, nombre FROM candidatos";
    $resultado = mysqli_query($conexion, $consulta);

    // Comprobar si se obtuvieron resultados
    if (mysqli_num_rows($resultado) > 0) {
        $candidatos = array();

        // Obtener los datos de los candidatos
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $candidato = array(
                'id' => $fila['id'],
                'nombre' => $fila['nombre']
            );
            $candidatos[] = $candidato;
        }

        // Devolver los candidatos como respuesta JSON
        header('Content-Type: application/json');
        echo json_encode($candidatos);
    }

    // Cerrar conexión a la base de datos
    mysqli_close($conexion);
}

/**
 * Obtiene listado de comunas
 * @params $region La region de la comuna a buscar
 */
function getComunas()
{
    //Obtiene datos de la conexion
    require_once('config.php');

    $conexion = mysqli_connect($host, $username, $password, $database);

    // Verificar conexion
    if (mysqli_connect_errno()) {
        die("Error al conectar con la base de datos: " . mysqli_connect_error());
    }

    // Obtener la region seleccionada en el formulario
    $region = $_GET['region'];

    // Realizar consulta para obtener las comunas de la región seleccionada
    $consulta = "SELECT id, nombre FROM comunas WHERE id_region = " . $region;
    $resultado = mysqli_query($conexion, $consulta);

    // Comprobar si se obtuvieron resultados
    if (mysqli_num_rows($resultado) > 0) {
        $comunas = array();

        // Obtener los datos de las comunas
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $comuna = array(
                'id' => $fila['id'],
                'nombre' => $fila['nombre']
            );
            $comunas[] = $comuna;
        }

        // Devolver las comunas como respuesta JSON
        header('Content-Type: application/json');
        echo json_encode($comunas);
    }

    // Cerrar conexión a la base de datos
    mysqli_close($conexion);
}

/**
 * Obtiene la cantidad de votos para un rut (para validar duplicidad de voto).
 * @params $rut El rut a validar
 */
function getCantVotos($rut)
{
    //Obtiene datos de la conexion
    require_once('config.php');

    // Crear la conexión
    $conn = new mysqli($host, $username, $password, $database);

    // Verificar si hay error en la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consultar la base de datos
    $query = "SELECT * FROM votos WHERE rut = '$rut'";
    $result = $conn->query($query);
    
    // Cerrar la conexión a la base de datos
    $conn->close();

    return $result->num_rows;
}

/**
 * Inserta voto en base de datos
 * @params $nombre El nombre del votante.
 * @params $alias El alias del votante.
 * @params $rut El rut (formateado sin puntos, ni guiones, ni digito verificador) del votante.
 * @params $email El email del votante.
 * @params $comuna La id de la comuna del votante.
 * @params $candidato El id del candidato a votar.
 * @params $medio Los medios que selecciono usuario.
 */
function guardarVoto($nombre, $alias, $rut, $email, $comuna, $candidato, $medio)
{

    //Obtiene datos de la conexion
    require 'config.php';

    $conn = mysqli_connect($host, $username, $password, $database);

    // Verificar si hay errores de conexion
    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }

    // Construir la consulta SQL
    $sql = "INSERT INTO votos (nombre, alias, rut, email, id_comuna, id_candidato, medio) VALUES ('$nombre', '$alias', '$rut', '$email', '$comuna', '$candidato', '$medio')";

    $estado = $conn->query($sql) === TRUE;

    // Cerrar la conexión
    $conn->close();

    return $estado;
}
