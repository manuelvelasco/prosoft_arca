<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $municipios_BD = consulta($conexion, "SELECT DISTINCT s.municipio FROM sepomex s INNER JOIN concesionario c ON s.municipio = c.municipio WHERE c.habilitado = 1 AND c.eliminado = 0 ORDER BY s.municipio");

        $resultado .= "<municipios>";

        while ($municipio = obtenResultado($municipios_BD)) {
            $resultado .= "<municipio>";
            $resultado .= "<nombre>" . $municipio["municipio"] . "</nombre>";
            $resultado .= "</municipio>";
        }

        $resultado .= "</municipios>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
