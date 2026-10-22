<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $contadorVehiculos_BD = consulta($conexion, "SELECT COUNT(*) AS cuantos FROM vehiculo v INNER JOIN concesionario c ON v.idConcesionario = c.id AND c.habilitado = 1 AND c.eliminado = 0 WHERE v.eliminado = 0 AND v.publicado = 1");
        $contadorVehiculos = obtenResultado($contadorVehiculos_BD);
        $resultado .= "<contadores>";

        $resultado .= "<vehiculos>" . trim($contadorVehiculos["cuantos"]) . "</vehiculos>";

        $contadorAgencias_BD = consulta($conexion, "SELECT COUNT(*) AS cuantos FROM concesionario c WHERE c.habilitado = 1 AND c.eliminado = 0 AND c.id IN (SELECT DISTINCT idConcesionario FROM vehiculo WHERE eliminado = 0 AND publicado = 1)");
        $contadorAgencias = obtenResultado($contadorAgencias_BD);

        $resultado .= "<agencias>" . trim($contadorAgencias["cuantos"]) . "</agencias>";

        $resultado .= "</contadores>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
