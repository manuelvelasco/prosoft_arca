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
        //$cargaReducida = sanitiza($conexion, filter_input(INPUT_POST, "cargaReducida"));

        // Consulta base de datos

        $concesionario_BD = consulta($conexion, "SELECT c.* FROM concesionario c WHERE c.id = " . $id . " AND c.habilitado = 1 AND c.eliminado = 0");

        $resultado .= "<concesionarios>";

        while ($concesionario = obtenResultado($concesionario_BD)) {
            $resultado .= "<concesionario>";

            foreach (array_keys($concesionario) as $llave) {
                $resultado .= "<" . $llave . ">" . $concesionario[$llave] . "</" . $llave . ">";
            }
            $resultado .= "<constanteUrlConcesionarios>" . $constante_urlConcesionarios . "</constanteUrlConcesionarios>";
            
            // Complementa con galeria

            $resultado .= "<imagenes>";

            $archivos = scandir($constante_rutaConcesionarios . $id . "/galeria");

            foreach ($archivos as $archivo) {
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                    $ruta = $archivo;
                    $resultado .= "<imagen>" . $ruta . "</imagen>";
                }
            }

            $resultado .= "</imagenes>";

            // Complementa con vehiculos 
            $resultado .= "<vehiculosEquivalentes>";

            $limite = 4;

            $vehiculosEquivalentes_BD = consulta($conexion, "SELECT * FROM vehiculo WHERE idConcesionario = " . $id . " AND publicado = 1 ORDER BY id DESC LIMIT " . $limite);

            while ($vehiculoEquivalente = obtenResultado($vehiculosEquivalentes_BD)) {
                $resultado .= "<vehiculoEquivalente>";

                foreach (array_keys($vehiculoEquivalente) as $llave) {
                    $resultado .= "<vehiculoEquivalente_" . $llave . ">" . $vehiculoEquivalente[$llave] . "</vehiculoEquivalente_" . $llave . ">";
                }

                $resultado .= "</vehiculoEquivalente>";

                $limite--;
            }

            $resultado .= "</vehiculosEquivalentes>";
    
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
