<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));

        // Arma restricciones

        $restricciones = "";

        if (!estaVacio($nombre)) {
            $restricciones .= " AND nombre = '" . $nombre . "'";
        }

        // Consulta base de datos

        $parametros_BD = consulta($conexion, "SELECT * FROM parametro WHERE 1 = 1 " . $restricciones);

        $resultado .= "<parametros>";

        while ($parametro = obtenResultado($parametros_BD)) {
            $resultado .= "<parametro>";

            foreach (array_keys($parametro) as $llave) {
                $resultado .= "<" . $llave . ">" . $parametro[$llave] . "</" . $llave . ">";
            }

            $resultado .= "</parametro>";
        }

        $resultado .= "</parametros>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
