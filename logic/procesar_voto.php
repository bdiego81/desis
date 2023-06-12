<?php

/**
 * CLASE QUE VALIDA EL POST DEL FORMULARIO INGRESADO POR EL USUARIO
 * Realiza validaciones correspondientes, y luego ingresa informacion a la base de datos (llamando a controlador db)
 */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errores = [];

    //valida nombre no este vacio
    if (empty($_POST['nombre'])) {
        // Los campos Nombre y Apellido son obligatorios
        $errores[] = "El campo Nombre y Apellido son obligatorios.\n";
    }

    //valida alias que tenga al menos 5 caracteres con letras y numeros
    if (strlen($_POST['alias']) <= 5 || !preg_match('/^(?=.*\d)(?=.*[A-Za-z])/', $_POST['alias'])) {
        $errores[] =  "El alias debe tener al menos 5 caracteres y contener letras y números.\n";
    }

    //valida que rut no este vacio o tenga un formato distinto XX.XXX.XXX-X
    if (empty($_POST['rut']) || !isRutFormatValid($_POST['rut'])) {
        $errores[] = "El campo Rut es obligatorio.\n";
    } else {
        //SOLO SI RUT ES VALIDO
        //obtiene rut completo del formulario
        $rutCompleto = $_POST['rut'];
        //se obtiene su digito verificador ingresado
        $dv = substr($rutCompleto, -1);
        //se quitan puntos, guiones y digito verificador
        $rutLimpio = str_replace(array('.', '-'), '', $rutCompleto);
        $rutLimpio = substr_replace($rutLimpio, "", -1);

        //Se verifica que el digito verificador ingresado por el usuario coincida con el calculado por nuestro sistema.
        if (strtoupper(getDigitoVerificador($rutLimpio)) != strtoupper($dv)) {
            $errores[] = "El Rut ingresado es invalido.\n";
        } else {
            //SOLO SI RUT ES VALIDO Y RUT ES LEGITIMO
            //se valida que el rut no haya votado aun (duplicidad de rut en voto)
            include '../database/controlador_db.php';
            if (getCantVotos($rutLimpio) > 0) {
                $errores[] =  "Voto ya existe para este rut.\n";
            }
        }
    }

    //valida formato del email ingresado
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El campo Email no tiene un formato válido.' . "\n";
    }

    //valida que haya seleccionado una region
    if (empty($_POST['region'])) {
        // Los campos Nombre y Apellido son obligatorios
        $errores[] =  "Debe seleccionar una region.\n";
    }

    //valida que haya seleccionado una comuna
    if (empty($_POST['comuna'])) {
        // Los campos Nombre y Apellido son obligatorios
        $errores[] =  "Debe seleccionar una comuna.\n";
    }

    //valida que haya seleccionado un candidato
    if (empty($_POST['candidato'])) {
        // Los campos Nombre y Apellido son obligatorios
        $errores[] =  "Debe seleccionar un candidato.\n";
    }

    //valida que haya seleccionado al menos 2 medios.
    if (!isset($_POST['medio']) || count($_POST['medio']) < 2) {
        $errores[] =  "Debe seleccionar al menos 2 opciones de como se enteró de nosotros.\n";
    }

    //Si existe al menos 1 error segun validaciones
    if (!empty($errores)) {
        //detiene proceso y retorna valores correspondientes.
        $response = array("success" => false, "message" => $errores);
        echo json_encode($response);
        return;
    }

    //Obtiene variables del formulario
    $nombre = $_POST['nombre'];
    $alias = $_POST['alias'];
    $rut = $rutLimpio;
    $email = $_POST['email'];
    $region = $_POST['region'];
    $comuna = $_POST['comuna'];
    $candidato = $_POST['candidato'];
    $medio = implode(",", $_POST['medio']);

    //llama al metodo para guardar el voto
    $success = guardarVoto($nombre, $alias, $rut, $email, $comuna, $candidato, $medio);

    if ($success === TRUE) {
        return retornaRespuesta(true, "El voto ha sido ingresado exitosamente.");
    } else {
        return retornaRespuesta(false, "No ha sido posible ingresar el voto.");
    }
}
/**
 * Devuelve la respuesta en formato de json.
 * Se usa este formato ya que es llamado desde una peticion en ajax.
 * @params $estado Estado de la respuesta [TRUE|FALSE]
 * @params $msg Mensaje de la respuesta.
 */
function retornaRespuesta($estado, $msg)
{
    $response = array("success" => $estado, "message" => $msg);
    echo json_encode($response);
}
/**
 * Obtiene el digito verificador de un rut.
 * @params $r Rut (sin puntos, ni guion, ni digito verificador) a validar
 */
function getDigitoVerificador($r)
{
    $s = 1;
    for ($m = 0; $r != 0; $r /= 10)
        $s = ($s + $r % 10 * (9 - $m++ % 6)) % 11;
    return chr($s ? $s + 47 : 75);
}
/**
 * Valida que el rut tenga formato valido XX.XXX.XXX-X
 */
function isRutFormatValid($rut)
{
    // Eliminar puntos y guión del RUT (si los hay)
    $rut = str_replace(array('.', '-'), '', $rut);

    // Verificar que el RUT tenga el formato correcto (XXXXXXXX-Y)
    if (!preg_match('/^[0-9]{1,2}\.?[0-9]{3}\.?[0-9]{3}-?[0-9kK]{1}$/', $rut)) {
        return false;
    }
    return true;
}
