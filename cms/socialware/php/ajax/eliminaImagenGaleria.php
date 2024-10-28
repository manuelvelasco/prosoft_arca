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

    $id = filter_input(INPUT_POST, "id");
    $imagen = filter_input(INPUT_POST, "imagen");

    // Elimina archivo

    try {
        unlink($constante_rutaVehiculos . "/" . $id . "/galeria/" . $imagen);

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>