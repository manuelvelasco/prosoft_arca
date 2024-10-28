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
        $modelo = sanitiza($conexion, filter_input(INPUT_POST, "modelo"));

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

        if (!estaVacio($modelo) && strpos($modelo, "VERTODO") === false) {
            $modelo = str_replace("\\", "", $modelo);

            $restricciones .= " AND modelo IN ('" . $modelo . "')";
        }

        // Consulta base de datos

        $anos_BD = consulta($conexion, "SELECT ano FROM vehiculo WHERE publicado = 1 AND intelimotor_id IS NOT NULL " . $restricciones . " GROUP BY ano ORDER BY ano");

        $resultado .= "<anos>";

        while ($ano = obtenResultado($anos_BD)) {
            $resultado .= "<ano>";
            $resultado .= "<numero>" . $ano["ano"] . "</numero>";
            $resultado .= "</ano>";
        }

        $resultado .= "</anos>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
