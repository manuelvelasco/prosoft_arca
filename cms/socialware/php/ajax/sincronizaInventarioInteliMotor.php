<?php

    /*
     * Habilita un usuario para que pueda acceder al sistema
     */

    session_start();

    include("../comunes/funciones.php");


    // Obtiene conexion a base de datos

    $conexion = obtenConexion();

    // Obtiene parametros de sesion

    $usuario_correoElectronico = $_SESSION["usuario_correoElectronico"];

    // Obtiene parametros de request

    $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));

    if ($idConcesionario == 0) {
        $idConcesionario = null;
    }

    return sincronizaInventarioIntelimotor($usuario_correoElectronico, $idConcesionario);
?>