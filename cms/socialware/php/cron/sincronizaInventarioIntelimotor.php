<?php
    header("Access-Control-Allow-Origin: *");

    //include("../comunes/funciones.php");
    //include("/var/www/html/albacar3/cms/socialware/php/comunes/funciones.php");
    include("/var/www/html/albacar/cms/socialware/php/comunes/funciones.php");

    sincronizaInventarioInteliMotor();
?>
