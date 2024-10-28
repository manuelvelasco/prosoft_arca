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

        // Arma restricciones

        $restricciones = "";

        if (!estaVacio($tipo) && strpos($tipo, "VERTODO") === false) {
            $tipo = str_replace("\\", "", $tipo);

            $restricciones .= " AND tipo IN ('" . $tipo . "')";
        }

        // Consulta base de datos

        $marcas_BD = consulta($conexion, "SELECT marca, modelo, count(*) AS cantidad FROM vehiculo WHERE publicado = 1 AND intelimotor_id IS NOT NULL " . $restricciones . " GROUP BY marca, modelo ORDER BY marca, modelo");

        $resultado .= "<marcas>";

        while ($marca = obtenResultado($marcas_BD)) {
            $resultado .= "<marca>";
            $resultado .= "<nombre>" . $marca["marca"] . "</nombre>";
            $resultado .= "<modelo>" . $marca["modelo"] . "</modelo>";
            $resultado .= "<cantidad>" . $marca["cantidad"] . "</cantidad>";
            $resultado .= "</marca>";
        }

        $resultado .= "</marcas>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
