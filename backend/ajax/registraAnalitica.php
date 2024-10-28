<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $url = sanitiza($conexion, filter_input(INPUT_POST, "url"));
        $selector = sanitiza($conexion, filter_input(INPUT_POST, "selector"));
        $evento = sanitiza($conexion, filter_input(INPUT_POST, "evento"));

        // Inserta registro

        registraAnalitica($url, $selector, $evento);

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>