<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));

        // Consulta base de datos

        $mensajeros_BD = consulta($conexion, "SELECT id, TRIM(CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno)) AS nombre FROM mensajero WHERE id IN (SELECT idMensajero FROM mensajero_concesionario WHERE idConcesionario = " . $idConcesionario . ") AND habilitado = 1 AND eliminado = 0 ORDER BY nombre, apellidoPaterno, apellidoMaterno");

        $resultado .= "<mensajeros>";

        while ($mensajero = obtenResultado($mensajeros_BD)) {
            $resultado .= "<mensajero>";
            $resultado .= "<id>" . $mensajero["id"] . "</id>";
            $resultado .= "<nombre>" . $mensajero["nombre"] . "</nombre>";
            $resultado .= "</mensajero>";
        }

        $resultado .= "</mensajeros>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
