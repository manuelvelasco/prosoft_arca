<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        //$tipos_BD = consulta($conexion, "SELECT DISTINCT tipo FROM vehiculo where publicado = 1 ORDER BY tipo");
        $tipos_BD = consulta($conexion, "SELECT tipo, count(*) as cantidad FROM vehiculo WHERE publicado = 1 GROUP BY tipo ORDER BY tipo");

        $resultado .= "<tipos>";

        while ($tipo = obtenResultado($tipos_BD)) {
            $resultado .= "<tipo>";
            $resultado .= "<nombre>" . $tipo["tipo"] . "</nombre>";
            $resultado .= "<cantidad>" . $tipo["cantidad"] . "</cantidad>";
            $resultado .= "</tipo>";
        }

        $resultado .= "</tipos>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
