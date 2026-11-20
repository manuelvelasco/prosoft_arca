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

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.intelimotor.com/api/brands/' . $idMarca . '/models/' . $idModelo . '/years?apiKey=' . $intelimotor_apiKey . '&apiSecret=' . $intelimotor_apiSecret . '&getAll=true/',
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

        $anos = reset($resultado) ?: [];

        $anosSQL = "";

        foreach ($anos as $ano) {
            $anosSQL .= $ano["name"] . ",";
        }

        $anosSQL = rtrim($anosSQL, ",");

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $anos_BD = consulta($conexion, "SELECT DISTINCT ano FROM vehiculo WHERE marca = '" . $marca . "' AND modelo = '" . $modelo . "' AND ano NOT IN (" . $anosSQL . ")");

        // Libera conexion a base de datos

        liberaConexion($conexion);

        // Agrega a arreglo Intelimotor los elementos faltantes

        while ($ano = obtenResultado($anos_BD)) {
            $resultado["data"][] = [
                "id"   => $ano["ano"],
                "name" => $ano["ano"]
            ];
        }

        // Ordena el arreglo de forma ascendente

        usort($resultado["data"], fn($a, $b) => $a["name"] <=> $b["name"]);

        // Devuelve el resultado a string

        $jsonActualizado = json_encode($resultado);

        // Regresa el resultado

        echo $jsonActualizado;


















/*
        // Inicializa arreglo de anos

        $arregloAnos = [];

        // Agrega a arreglo de anos los provenientes de Intelimotor

        $anos = reset(json_decode($response, true)) ?: [];

        foreach ($anos as $ano) {
            if (!in_array($ano["name"], $arregloAnos, true)) {
                $arregloAnos[] = $ano["name"];
            }
        }

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Agrega a arreglo de anos los provenientes de nuestra base de datos

        $anos_BD = consulta($conexion, "SELECT DISTINCT ano FROM vehiculo");

        while ($ano = obtenResultado($anos_BD)) {
            if (!in_array($ano["ano"], $arregloAnos, true)) {
                $arregloAnos[] = $ano["ano"];
            }
        }

        // Libera conexion a base de datos

        liberaConexion($conexion);

        // Depura arreglo de anos

print_r($arregloAnos);
*/
        
        
        
        
        
        
/*
        // Arma restricciones

        $anos = reset(json_decode($response, true)) ?: [];

        $anosSQL = "";

        foreach ($anos as $ano) {
            $anosSQL .= $ano["name"] . ",";
        }

        $anosSQL = rtrim($anosSQL, ",");

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        $consulta($conexion, "SELECT DISTINCT ano FROM vehiculo WHERE ano NOT IN (" . $anosSQL . ")");

        // Libera conexion a base de datos

        liberaConexion($conexion);
*/






/*
        $data = json_decode($response);
        $anos = is_array($data) ? reset($data) : [];

$primeraClave = array_key_first($data);
echo "primeraClave = " . $primeraClave . "<br />";

        if (is_array($anos)) {
echo "a <br />";
            foreach ($anos as $ano) {
echo "b <br />";
                //echo $ano["name"];
                print_r($ano);
            }
        }
*/

/*        
{"data":[{"id":"5cdc4a90a6ca044a76083a0b","name":"1997"},{"id":"5cdc4a90a6ca044a76083a0a","name":"1999"},{"id":"5cdc4a90a6ca044a76083a09","name":"2000"},{"id":"5cdc4a90a6ca044a76083a08","name":"2001"},{"id":"5cdc4a90a6ca044a76083a07","name":"2002"},{"id":"5cdc4a90a6ca044a76083a06","name":"2003"},{"id":"5cdc4a90a6ca044a76083a05","name":"2004"},{"id":"5cdc4a90a6ca044a76083a04","name":"2005"},{"id":"5cdc4a90a6ca044a76083a03","name":"2006"},{"id":"5cdc4a90a6ca044a76083a02","name":"2007"},{"id":"5cdc4a90a6ca044a76083a01","name":"2008"},{"id":"5cdc4a90a6ca044a76083a00","name":"2009"},{"id":"5cdc4a90a6ca044a760839ff","name":"2010"},{"id":"5cdc4a90a6ca044a760839fe","name":"2011"},{"id":"5cdc4a90a6ca044a760839fd","name":"2012"},{"id":"5cdc4a90a6ca044a760839fc","name":"2013"},{"id":"5cdc4a90a6ca044a760839fb","name":"2014"},{"id":"5cdc4a90a6ca044a760839fa","name":"2015"},{"id":"5cdc4a90a6ca044a760839f9","name":"2016"},{"id":"5cdc4a90a6ca044a760839f8","name":"2017"},{"id":"5cdc4a90a6ca044a760839f7","name":"2018"},{"id":"5cdc4a90a6ca044a760839f6","name":"2019"},{"id":"5e34b349c54db4001ad847e9","name":"2020"},{"id":"606270fb156ecc001353f47b","name":"2021"},{"id":"61b78411cd187600124f7d12","name":"2022"},{"id":"6529da34d4ac9800195ff7fd","name":"2023"},{"id":"674107389bac71cdbdbecc94","name":"2024"},{"id":"6838da41026dcccae4eddc48","name":"2025"}]}
*/
        
        
        
        //echo $response;
    } catch (Error $e) {
        echo $e->getLine();
        echo "<br />";
    } catch (Exception $e) {
        echo $e->getMessage();
        echo "<br />";
    }
?>