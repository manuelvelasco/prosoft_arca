<?php
    /*
     * Habilita un usuario para que pueda acceder al sistema
     */

    session_start();

    include("../comunes/funciones.php");

    // Obtiene parametros de request

    $id = filter_input(INPUT_POST, "id");
    $publicado = filter_input(INPUT_POST, "publicado");

    // Obtiene parametros de sesion

    $idUsuario = $_SESSION["usuario_id"];

    // Inicializa variables

    $fechaActual = date("Y-m-d H:i:s");

    // Obtiene conexion a base de datos

    $conexion = obtenConexion();

    // Habilita / inhabilita usuario

    if (!estaVacio($id) && !estaVacio($publicado)) {
        consulta($conexion, "UPDATE vehiculo SET publicado = " . $publicado . " WHERE id = " . $id);
    }

    // Registra evento

    consulta($conexion, "INSERT INTO log (fecha, evento, idUsuario) VALUES ('" . $fechaActual . "', '" . ($publicado ? "Publicacion" : "Desactivacion") . " de vehiculo| id = " . $id . ", " . $idUsuario . ")");

    // Cierra la conexion con base de datos y libera recursos

    liberaConexion($conexion);

    // Regresa el xml resultante

    echo "ok";
?>