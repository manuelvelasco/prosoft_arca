<?php
    include("../comunes/constantes.php");
    include("../comunes/funciones.php");

    try {
        $intelimotor_apiKey = "89e42108c0292fdab98c7725d557ac5ac7b031c7dbfd5e4a8fc957f6c576e40a";
        $intelimotor_apiSecret = "df7d14926120badf19783f88b4b453cb37b681fbcbc8717a1df0f1c6e9b5aeb5";

        $idMarca = $_GET["idMarca"];
        $marca = $_GET["marca"];

        $idModelo = $_GET["idModelo"];
        $modelo = $_GET["modelo"];

        $idAno = $_GET["idAno"];
        $ano = $_GET["ano"];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.intelimotor.com/api/brands/' . $idMarca . '/models/' . $idModelo . '/years/' . $idAno . '/trims?apiKey=' . $intelimotor_apiKey . '&apiSecret=' . $intelimotor_apiSecret . '&getAll=true/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Convierte el resultado a objeto

        $resultado = json_decode($response, true);

        // Construye restricciones para buscar en BD los anos que no vienen en Intelimotor

        $versiones = reset($resultado) ?: [];

        $versionesSQL = "";

        foreach ($versiones as $version) {
            $versionesSQL .= "'" . $version["name"] . "',";
        }

        $versionesSQL = rtrim($versionesSQL, ",");

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $versiones_BD = consulta($conexion, "SELECT DISTINCT version FROM vehiculo WHERE marca = '" . $marca . "' AND modelo = '" . $modelo . "' AND ano = " . $ano . " AND version NOT IN (" . $versionesSQL . ")");

        // Libera conexion a base de datos

        liberaConexion($conexion);

        // Agrega a arreglo Intelimotor los elementos faltantes

        while ($version = obtenResultado($versiones_BD)) {
            $resultado["data"][] = [
                "id"   => $version["version"],
                "name" => $version["version"]
            ];
        }

        // Ordena el arreglo de forma ascendente

        usort($resultado["data"], fn($a, $b) => $a["name"] <=> $b["name"]);

        // Devuelve el resultado a string

        $jsonActualizado = json_encode($resultado);

        // Regresa el resultado

        echo $jsonActualizado;


















/*
        $marca = $_GET["marca"];
        $modelo = $_GET["modelo"];
        $ano = $_GET["ano"];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.intelimotor.com/api/brands/' . $marca . '/models/' . $modelo . '/years/' . $ano . '/trims?apiKey=' . $intelimotor_apiKey . '&apiSecret=' . $intelimotor_apiSecret . '&getAll=true/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
*/
    } catch (Exception $e) {
        echo $e->getMessage();
        echo "<br />";
    }
?>