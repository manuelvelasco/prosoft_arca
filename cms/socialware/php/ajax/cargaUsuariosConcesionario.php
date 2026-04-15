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

        if (!estaVacio($idConcesionario) && strpos($idConcesionario, "VERTODO") === false) {
            $idConcesionario = str_replace("\\", "", $idConcesionario);

            $restricciones .= " AND idConcesionario = " . $idConcesionario . "";
        }

        // Consulta base de datos

        $usuarios_BD = consulta($conexion, "SELECT id, nombre FROM usuario WHERE rol != 'Master' " . $restricciones . " ORDER BY nombre");

        $resultado .= "<usuarios>";

        while ($usuario = obtenResultado($usuarios_BD)) {
            $resultado .= "<usuario>";
            $resultado .= "<id>" . trim($usuario["id"]) . "</nombre>";
            $resultado .= "<nombre>" . trim($usuario["nombre"]) . "</modelo>";
            $resultado .= "</usuario>";
        }

        $resultado .= "</usuarios>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
