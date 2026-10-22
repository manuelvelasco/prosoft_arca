<?php
    header("Access-Control-Allow-Origin: *");
    //header("Access-Control-Allow-Origin: http://www.requesting-page.com");

    /*
     * Elimina una imagen de la galeria fotografica de un vehiculo
     */

    session_start();

    include("../comunes/constantes.php");
    include("../comunes/funciones.php");

    // Obtiene parametros de request

    $idMensajero = filter_input(INPUT_POST, "idMensajero");

    // Obtiene conexion a base de datos

    $conexion = obtenConexion();

    // Elimina archivo

    try {
        consulta($conexion, "UPDATE mensajero SET eliminado = 1 WHERE id = " . $idMensajero);

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>