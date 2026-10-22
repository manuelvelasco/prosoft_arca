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

    $idTramite = filter_input(INPUT_POST, "idTramite");

    // Obtiene conexion a base de datos

    $conexion = obtenConexion();

    // Elimina archivo

    try {
        consulta($conexion, "UPDATE tramite SET eliminado = 1 WHERE id = " . $idTramite);

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>