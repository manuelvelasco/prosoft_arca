<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $concesionarios_BD = consulta($conexion, "SELECT c.nombreComercial FROM concesionario c WHERE c.habilitado = 1 AND c.eliminado = 0 AND (SELECT COUNT(*) FROM vehiculo WHERE idConcesionario = c.id AND publicado = 1) > 0 ORDER BY rand()");

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
