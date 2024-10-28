<?php

    /*
     * Habilita un usuario para que pueda acceder al sistema
     */

    session_start();

    include("../comunes/funciones.php");

    return sincronizaInventarioIntelimotor();
?>