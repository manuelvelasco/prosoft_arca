<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $marcas_BD = consulta($conexion, "SELECT DISTINCT marca FROM vehiculo WHERE publicado = 1 ORDER BY marca");

        $resultado .= "<marcas>";

        while ($marca = obtenResultado($marcas_BD)) {
            $resultado .= "<marca>";
            $resultado .= "<nombre>" . $marca["marca"] . "</nombre>";
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
