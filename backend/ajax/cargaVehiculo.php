<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/constantes.php");
    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $id = sanitiza($conexion, filter_input(INPUT_POST, "id"));
        $cargaReducida = sanitiza($conexion, filter_input(INPUT_POST, "cargaReducida"));

        // Consulta base de datos

        $vehiculo_BD = consulta($conexion, "SELECT v.*, s.whatsapp AS gerente_whatsapp, c.nombreComercial, c.whatsapp AS concesionarioWhatsapp, c.colonia, c.municipio, c.fachada, c.telefono FROM vehiculo v INNER JOIN concesionario c ON v.idConcesionario = c.id LEFT JOIN sucursal s ON s.id = v.idSucursal WHERE v.id = " . $id . " AND v.publicado = 1");

        $resultado .= "<vehiculos>";

        while ($vehiculo = obtenResultado($vehiculo_BD)) {
            $resultado .= "<vehiculo>";

            foreach (array_keys($vehiculo) as $llave) {
                $resultado .= "<" . $llave . ">" . $vehiculo[$llave] . "</" . $llave . ">";
            }
            $resultado .= "<constanteUrlConcesionarios>" . $constante_urlConcesionarios . "</constanteUrlConcesionarios>";

            // Complementa con galeria

            $resultado .= "<imagenes>";

            if (estaVacio($vehiculo["intelimotor_id"])) {
                if (!estaVacio($vehiculo["imagenPrincipal"])) {
                    $resultado .= "<imagen>" . $constante_urlVehiculos . $id . "/" . $vehiculo["imagenPrincipal"] . "</imagen>";
                }
            } else {
                if (!estaVacio($vehiculo["intelimotor_picture"])) {
                    $resultado .= "<imagen>" . $vehiculo["intelimotor_picture"] . "</imagen>";
                }
            }

            if (estaVacio($cargaReducida)) {
                if (estaVacio($vehiculo["intelimotor_id"])) {
                    $archivos = scandir($constante_rutaVehiculos . "/" . $id . "/galeria");

                    foreach ($archivos as $archivo) {
                        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                        if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                            $ruta = $constante_urlVehiculos . "/" . $vehiculo["id"] . "/galeria/" . $archivo;
                            $resultado .= "<imagen>" . $ruta . "</imagen>";
                        }
                    }
                } else {
                    $imagenesIntelimotor_BD = consulta($conexion, "SELECT imagen FROM imagen i LEFT JOIN vehiculo v ON v.intelimotor_id = i.intelimotor_id WHERE v.id = " . $id . " AND v.publicado = 1");

                    while($imagenIntelimotor = obtenResultado($imagenesIntelimotor_BD)){
                        $resultado .= "<imagen>" . $imagenIntelimotor["imagen"] . "</imagen>";
                    }
                }
            }

            $resultado .= "</imagenes>";

            // Complementa con vehiculos similares

            $limite = 3;
            $idsVehiculosSimilares = "";

            $vehiculosSimilares_BD = consulta($conexion, "SELECT *, ABS(precio - " . $vehiculo["precio"] . ") AS diferencia FROM vehiculo WHERE id != " . $id . " AND publicado = 1 AND intelimotor_id IS NOT NULL AND marca = '" . $vehiculo["marca"] . "' AND modelo = '" . $vehiculo["modelo"] . "' and precio >= (select c.precio - 100000 from vehiculo c where c.id = " . $id . " ) and precio <= (select c.precio + 100000 from vehiculo c where c.id = " . $id . " ) ORDER BY rand()");
            //echo "SELECT *, ABS(precio - " . $vehiculo["precio"] . ") AS diferencia FROM vehiculo WHERE id != " . $id . " AND publicado = 1 AND intelimotor_id IS NOT NULL AND marca = '" . $vehiculo["marca"] . "' AND modelo = '" . $vehiculo["modelo"] . "' ORDER BY diferencia";
            
            $resultado .= "<vehiculosSimilares>";

            while ($vehiculoSimilar = obtenResultado($vehiculosSimilares_BD)) {
                $resultado .= "<vehiculoSimilar>";

                foreach (array_keys($vehiculoSimilar) as $llave) {
                    $resultado .= "<vehiculoSimilar_" . $llave . ">" . $vehiculoSimilar[$llave] . "</vehiculoSimilar_" . $llave . ">";
                }

                $resultado .= "</vehiculoSimilar>";

                $limite--;
                $idsVehiculosSimilares .= $vehiculoSimilar["id"] . ",";
            }

            if ($limite > 0) {
                $restricciones = "";

                if (!estaVacio($idsVehiculosSimilares)) {
                    $restricciones .= " AND id NOT IN (" . rtrim($idsVehiculosSimilares, ",") . ")";
                }

                $vehiculosSimilares_BD = consulta($conexion, "SELECT *, ABS(precio - " . $vehiculo["precio"] . ") AS diferencia FROM vehiculo WHERE id != " . $id . $restricciones . " AND publicado = 1 AND intelimotor_id IS NOT NULL AND tipo = '" . $vehiculo["tipo"] . "' ORDER BY diferencia, marca, modelo, version LIMIT " . $limite);

                while ($vehiculoSimilar = obtenResultado($vehiculosSimilares_BD)) {
                    $resultado .= "<vehiculoSimilar>";

                    foreach (array_keys($vehiculoSimilar) as $llave) {
                        $resultado .= "<vehiculoSimilar_" . $llave . ">" . $vehiculoSimilar[$llave] . "</vehiculoSimilar_" . $llave . ">";
                    }

                    $resultado .= "</vehiculoSimilar>";
                }
            }

            $resultado .= "</vehiculosSimilares>";

            // Complementa con vehiculos equivalentes

            $limite = 8;

            $vehiculosEquivalentes_BD = consulta($conexion, "SELECT *, ABS(precio - " . $vehiculo["precio"] . ") AS diferencia FROM vehiculo WHERE id != " . $id . " AND publicado = 1 AND intelimotor_id IS NOT NULL AND precio >= (select c.precio - 100000 from vehiculo c where c.id = " . $id . " ) AND precio <= (select c.precio + 100000 from vehiculo c where c.id = " . $id . " ) ORDER BY rand() LIMIT " . $limite);

            while ($vehiculoEquivalente = obtenResultado($vehiculosEquivalentes_BD)) {
                $resultado .= "<vehiculoEquivalente>";

                foreach (array_keys($vehiculoEquivalente) as $llave) {
                    $resultado .= "<vehiculoEquivalente_" . $llave . ">" . $vehiculoEquivalente[$llave] . "</vehiculoEquivalente_" . $llave . ">";
                }

                $resultado .= "</vehiculoEquivalente>";

                $limite--;
            }

            $resultado .= "<vehiculosEquivalentes>";

            $resultado .= "</vehiculosEquivalentes>";

            $filename = $constante_rutaVehiculos  . $id . "/FICHA_TECNICA_" . $id . ".pdf";
            $resultado .= "<pdf>" . (file_exists($filename) ? $constante_urlVehiculos  . $id . "/FICHA_TECNICA_" . $id . ".pdf" : "") . "</pdf>";

            $resultado .= "</vehiculo>";
        }

        $resultado .= "</vehiculos>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
