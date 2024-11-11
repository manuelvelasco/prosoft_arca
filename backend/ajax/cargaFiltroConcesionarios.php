<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $concesionarios_BD = consulta($conexion, "SELECT nombreComercial FROM concesionario WHERE habilitado = 1 AND eliminado = 0 ORDER BY nombreComercial");

        $resultado .= "<concesionarios>";

        while ($concesionario = obtenResultado($concesionarios_BD)) {
            $resultado .= "<concesionario>";
            $resultado .= "<nombre>" . $concesionario["nombreComercial"] . "</nombre>";
            $resultado .= "</concesionario>";
        }

        $resultado .= "</concesionarios>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
