<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $preguntasFrecuentes_BD = consulta($conexion, "SELECT * FROM faq ORDER BY categoria, orden");

        $resultado .= "<preguntasFrecuentes>";

        while ($preguntaFrecuente = obtenResultado($preguntasFrecuentes_BD)) {
            $resultado .= "<preguntaFrecuente>";

            foreach (array_keys($preguntaFrecuente) as $llave) {
                $resultado .= "<" . $llave . ">" . $preguntaFrecuente[$llave] . "</" . $llave . ">";
            }

            $resultado .= "</preguntaFrecuente>";
        }

        $resultado .= "</preguntasFrecuentes>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
