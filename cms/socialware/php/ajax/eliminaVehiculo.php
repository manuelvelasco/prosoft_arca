<?php

    /*
     * Elimina un vehiculo y sus archivos relacionados
     */

    session_start();

    include("../comunes/constantes.php");
    include("../comunes/funciones.php");

    // Obtiene parametros de request

    $id = filter_input(INPUT_POST, "id");

    // Obtiene parametros de sesion

    $idUsuario = $_SESSION["usuario_id"];

    // Inicializa variables

    $fechaActual = date("Y-m-d H:i:s");

    // Obtiene conexion a base de datos

    $conexion = obtenConexion();

    // Elimina vehiculo

    if (!estaVacio($id)) {
        borraDirectorio($constante_rutaVehiculos . "/" . $id);

        consulta($conexion, "DELETE FROM vehiculo WHERE id = " . $id);
    }

    // Registra evento

    consulta($conexion, "INSERT INTO log (idUsuario, fecha, evento) VALUES (" . $idUsuario . ", '" . $fechaActual . "', 'Eliminacion de vehículo | id = " . $id . "')");

    // Cierra la conexion con base de datos y libera recursos

    liberaConexion($conexion);

    // Regresa el xml resultante

    echo "ok";
?>