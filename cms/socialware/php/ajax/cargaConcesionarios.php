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

        // Arma restricciones

        $restricciones = "";

        if (!estaVacio($idConcesionario)) {
            $restricciones .= " AND id = " . $idConcesionario;
        }

        // Consulta base de datos

        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE habilitado = 1 AND eliminado = 0 " . $restricciones . " ORDER BY nombreComercial");

        $resultado .= "<concesionarios>";

        while ($concesionario = obtenResultado($concesionarios_BD)) {
            $resultado .= "<concesionario>";
            $resultado .= "<id>" . $concesionario["id"] . "</id>";
            $resultado .= "<nombre>" . $concesionario["nombre"] . "</nombre>";
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
