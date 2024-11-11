<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $recientes = sanitiza($conexion, filter_input(INPUT_POST, "recientes"));
        $destacado = sanitiza($conexion, filter_input(INPUT_POST, "destacado"));
        $descuentoEspecial = sanitiza($conexion, filter_input(INPUT_POST, "descuentoEspecial"));
        $palabraClave = sanitiza($conexion, filter_input(INPUT_POST, "palabraClave"));
        $tipo = sanitiza($conexion, filter_input(INPUT_POST, "tipo"));
        $marca = sanitiza($conexion, filter_input(INPUT_POST, "marca"));
        $modelo = sanitiza($conexion, filter_input(INPUT_POST, "modelo"));
        $anoMinimo = sanitiza($conexion, filter_input(INPUT_POST, "anoMinimo"));
        $anoMaximo = sanitiza($conexion, filter_input(INPUT_POST, "anoMaximo"));
        //$montoMaximoCredito = sanitiza($conexion, filter_input(INPUT_POST, "montoMaximoCredito"));
        $criterioOrdenamiento = sanitiza($conexion, filter_input(INPUT_POST, "criterioOrdenamiento"));
        $semillaOrdenamiento = sanitiza($conexion, filter_input(INPUT_POST, "semillaOrdenamiento"));
        $pagina = sanitiza($conexion, filter_input(INPUT_POST, "pagina"));
        $tamanoPagina = sanitiza($conexion, filter_input(INPUT_POST, "tamanoPagina"));
        $kilometrosMinimo = sanitiza($conexion, filter_input(INPUT_POST, "kilometrosMinimo"));
        $kilometrosMaximo = sanitiza($conexion, filter_input(INPUT_POST, "kilometrosMaximo"));
        $transmision = sanitiza($conexion, filter_input(INPUT_POST, "transmision"));
        $precioMinimo = sanitiza($conexion, filter_input(INPUT_POST, "precioMinimo"));
        $precioMaximo = sanitiza($conexion, filter_input(INPUT_POST, "precioMaximo"));
        $concesionario = sanitiza($conexion, filter_input(INPUT_POST, "concesionario"));

        // Inicializa variables

        if (!$semillaOrdenamiento) {
            $semillaOrdenamiento = rand(0, 1000);
        }

        if (estaVacio($pagina)) {
            $pagina = 1;
        }

        if (estaVacio($tamanoPagina)) {
            $tamanoPagina = 10;
        }

        // Arma restricciones

        $restricciones = "";
        $ordenamiento = "";
        $limitante = "";

        if (!estaVacio($palabraClave)) {
            $restricciones .= " AND (LOWER(descripcion) LIKE '%" . strtolower($palabraClave) . "%')";
        }

        if (!estaVacio($kilometrosMinimo) && $kilometrosMinimo >= 0) {
            $restricciones .= " AND kilometraje >= " . $kilometrosMinimo;
        }

        if (!estaVacio($kilometrosMaximo) && $kilometrosMaximo >= 0) {
            $restricciones .= " AND kilometraje <= " . $kilometrosMaximo;
        }

        if (!estaVacio($tipo) && strpos($tipo, "VERTODO") === false) {
            $tipo = str_replace("\\", "", $tipo);

            $restricciones .= " AND tipo IN (" . $tipo . ")";
        }

        if (!estaVacio($marca) && strpos($marca, "VERTODO") === false) {
            $marca = str_replace("\\", "", $marca);

            $restricciones .= " AND marca IN (" . $marca . ")";
        }

        if (!estaVacio($modelo) && strpos($modelo, "VERTODO") === false) {
            $modelo = str_replace("\\", "", $modelo);

            $restricciones .= " AND modelo IN (" . $modelo . ")";
        }

        if (!estaVacio($transmision)) {
            $transmision = str_replace("\\", "", $transmision);

            $restricciones .= " AND transmision IN (" . $transmision . ")";
        }

        if (!estaVacio($anoMinimo)) {
            $restricciones .= " AND ano >= " . $anoMinimo . " AND ano <= " . $anoMaximo;
        }

        if (!estaVacio($precioMinimo)) {
            $restricciones .= " AND precio >= " . $precioMinimo;
        }

        if (!estaVacio($precioMaximo)) {
            $restricciones .= " AND precio <= " . $precioMaximo;
        }

        if (!estaVacio($concesionario)) {
            $concesionario = str_replace("\\", "", $concesionario);
            $restricciones .= " AND idConcesionario IN ( SELECT c.id from concesionario c where c.nombreComercial IN (" . $concesionario . "))";
        }

        if (!estaVacio($destacado) && $destacado == 1) {
            $restricciones .= " AND destacado = 1";
        }

        if (!estaVacio($descuentoEspecial) && $descuentoEspecial == 1) {
            $restricciones .= " AND descuentoEspecial = 1";
        }

/*
        if (!estaVacio($montoMaximoCredito)) {
            $restricciones .= " AND precio <= " . $montoMaximoCredito;
        }
*/

        if (!estaVacio($criterioOrdenamiento)) {
            switch ($criterioOrdenamiento) {
                case 1:
                    $ordenamiento = " ORDER BY RAND(" . $semillaOrdenamiento . ")";
                    break;
                case 2:
                    $ordenamiento = " ORDER BY id DESC";
                    break;
                case 3:
                    $ordenamiento = " ORDER BY precio ";
                    break;
                case 4:
                    $ordenamiento = " ORDER BY precio DESC";
                    break;
                case 5:
                    $ordenamiento = " ORDER BY ano";
                    break;
                case 6:
                    $ordenamiento = " ORDER BY ano DESC";
                    break;
                default:
                    $ordenamiento = " ORDER BY RAND(" . $semillaOrdenamiento . ")";
                    break;
            }
        } else {
            $ordenamiento = " ORDER BY RAND(" . $semillaOrdenamiento . ")";
        }

        if (!estaVacio($recientes) && $recientes == 1) {
            $limitante = " LIMIT 6";
        } else {
            $limitante = " LIMIT " . (($pagina - 1) * $tamanoPagina) . ", " . $tamanoPagina;
        }

        // Consulta base de datos
        //echo "SELECT COUNT(*) AS vehiculosEncontrados FROM vehiculo WHERE publicado = 1 AND intelimotor_id IS NOT NULL " . $restricciones; die();
        $vehiculosEncontrados = obtenResultado(consulta($conexion, "SELECT COUNT(*) AS vehiculosEncontrados FROM vehiculo WHERE publicado = 1 " . $restricciones))["vehiculosEncontrados"];

        //$vehiculos_BD = consulta($conexion, "SELECT id, marca, modelo, ano, precio, kilometraje, transmision, combustible, imagenPrincipal FROM vehiculo WHERE publicado = 1 " . $restricciones . $ordenamiento . $limitante);
        $vehiculos_BD = consulta($conexion, "SELECT * FROM vehiculo WHERE publicado = 1 " . $restricciones . $ordenamiento . $limitante);
        //echo "SELECT * FROM vehiculo WHERE publicado = 1 AND intelimotor_id IS NOT NULL " . $restricciones . $ordenamiento . $limitante;

        $resultado .= "<vehiculos total='" . $vehiculosEncontrados . "'>";

        while ($vehiculo = obtenResultado($vehiculos_BD)) {
            $resultado .= "<vehiculo>";

            foreach (array_keys($vehiculo) as $llave) {
                $resultado .= "<" . $llave . ">" . $vehiculo[$llave] . "</" . $llave . ">";
            }

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