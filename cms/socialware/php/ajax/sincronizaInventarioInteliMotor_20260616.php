<?php

    /*
     * Habilita un usuario para que pueda acceder al sistema
     */

    session_start();

    include("../comunes/funciones.php");
    $conexion = obtenConexion();

    $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));

    if ($idConcesionario == 0) {
        $idConcesionario = null;
    }

    return sincronizaInventarioIntelimotor($idConcesionario);
?>