<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $tipo = sanitiza($conexion, filter_input(INPUT_POST, "tipo"));
        $marca = sanitiza($conexion, filter_input(INPUT_POST, "marca"));

        // Arma restricciones

        $restricciones = "";

        if (!estaVacio($tipo) && strpos($tipo, "VERTODO") === false) {
            $tipo = str_replace("\\", "", $tipo);

            $restricciones .= " AND tipo IN ('" . $tipo . "')";
        }

        if (!estaVacio($marca) && strpos($marca, "VERTODO") === false) {
            $marca = str_replace("\\", "", $marca);

            $restricciones .= " AND marca IN ('" . $marca . "')";
        }

        // Consulta base de datos

        $modelos_BD = consulta($conexion, "SELECT DISTINCT modelo FROM vehiculo WHERE publicado = 1 " . $restricciones . " ORDER BY modelo");

        $resultado .= "<modelos>";

        while ($modelo = obtenResultado($modelos_BD)) {
            $resultado .= "<modelo>" . trim($modelo["modelo"]) . "</modelo>";
        }

        $resultado .= "</modelos>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
