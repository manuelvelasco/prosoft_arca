<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $estado = sanitiza($conexion, filter_input(INPUT_POST, "estado"));

        // Arma restricciones

        $restricciones = "";

        if (!estaVacio($estado) && strpos($estado, "VERTODO") === false) {

            // Consulta base de datos

            $municipios_BD = consulta($conexion, "SELECT DISTINCT municipio FROM sepomex WHERE estado = '" . $estado . "' ORDER BY municipio");

            $resultado .= "<municipios>";

            while ($municipio = obtenResultado($municipios_BD)) {
                $resultado .= "<municipio>";
                $resultado .= "<nombre>" . trim($municipio["municipio"]) . "</nombre>";
                $resultado .= "</municipio>";
            }

            $resultado .= "</municipios>";
        }

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
