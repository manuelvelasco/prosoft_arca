<?php
    header("Access-Control-Allow-Origin: *");

    //include("/Users/mvelasco/Socialware/Proyectos/Web/arca/cms/socialware/php/comunes/funciones.php");
    include("/var/www/html/socialware/arca/cms/socialware/php/comunes/funciones.php");
    //include("/var/www/html/arca/cms/socialware/php/comunes/funciones.php");

    sincronizaInventarioInteliMotor();
?>
