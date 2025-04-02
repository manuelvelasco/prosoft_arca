<?php
    //setlocale(LC_ALL, "es_MX");     // Desarrollo
    setlocale(LC_ALL, "esm");     // Servidor
    date_default_timezone_set('America/Monterrey');


    /*
     * Comunicacion con base de datos
     */


    function consulta($conexion, $consulta) {
        $conexion->query("SET NAMES 'utf8'");
        return $conexion->query($consulta);
    }


    function cuentaResultados($resultados) {
        if ($resultados == false) {
            return 0;
        } else {
            return mysqli_num_rows($resultados);
        }
    }


    function escapa($conexion, $valor) {
        return mysqli_real_escape_string($conexion, $valor);
    }


    function liberaConexion($conexion) {
        $conexion->close();
    }


    function liberaResultados($resultados) {
        $resultados->free();
    }


    function obtenConexion() {
        //return new mysqli("localhost", "root", "root", "arca");
        return new mysqli("stagedb-2.c7vpephv5j2g.us-east-1.rds.amazonaws.com", "app_respaldos_dbuser", "2brv7b24rV,8sdu8c", "arca");
        //return new mysqli("production.c7vpephv5j2g.us-east-1.rds.amazonaws.com", "app_respaldos_dbuser", "2brv7b24rV,8sdu8c", "arca");
    }


    function obtenResultado($resultados) {
        if ($resultados != null) {
            return $resultados->fetch_assoc();
        } else {
            return null;
        }
    }


    function registraEvento($evento) {

        // Inicializa variables

        $fechaActual = date("Y-m-d H:i:s");
        $idUsuario = $_SESSION["usuario_id"];

        // Obtiene conexion a base de datos

	$conexion = obtenConexion();

        // Registra evento

        consulta($conexion, "INSERT INTO log (fecha, evento, idUsuario) VALUES ('" . $fechaActual . "', '" . $evento . "', " . $idUsuario . ")");
    }


    function reiniciaResultados($resultados) {
        $resultados->data_seek(0);
    }


    /*
     * Control de datos
     */


    function estaVacio($valor) {
        return (!isset($valor) || trim($valor) === "");
    }


    function esFecha($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }


    function esNumero($valor) {
        return is_numeric($valor);
    }


    function objetoAArreglo($d) {
	if (is_object($d)) {
	    return get_object_vars($d);
	} else if (is_array($d)) {
	    return array_map(__FUNCTION__, $d);
	} else {
	    return $d;
	}
    }


    function sanitiza($conexion, $valor) {
        if (!estaVacio($valor) && is_string($valor)) {
            //$valor = str_replace("SELECT", "", strtoupper($valor));

            // Elimina SQL Injection

            $valor = preg_replace("/\bDATABASE\b/iu", "", $valor);
            $valor = preg_replace("/\bEXECUTE\b/iu", "", $valor);
            $valor = preg_replace("/\bSHOW\b/iu", "", $valor);
            $valor = preg_replace("/\bSLEEP\b/iu", "", $valor);
            $valor = preg_replace("/\bSELECT\b/iu", "", $valor);
            $valor = preg_replace("/\bFROM\b/iu", "", $valor);
            $valor = preg_replace("/\bAND\b/iu", "", $valor);
            $valor = preg_replace("/\bOR\b/iu", "", $valor);
            $valor = preg_replace("/\bNOT\b/iu", "", $valor);
            $valor = preg_replace("/\bIN\b/iu", "", $valor);
            $valor = preg_replace("/\bJOIN\b/iu", "", $valor);
            $valor = preg_replace("/\bINSERT\b/iu", "", $valor);
            $valor = preg_replace("/\bUPDATE\b/iu", "", $valor);
            $valor = preg_replace("/\bDELETE\b/iu", "", $valor);
            $valor = preg_replace("/\bTRUNCATE\b/iu", "", $valor);
            $valor = preg_replace("/\bDROP\b/iu", "", $valor);

            // Escapa caracteres especiales

            $valor = mysqli_real_escape_string($conexion, $valor);

            // Corrige generacion de saltos de linea de mysqli_real_escape_string

            $valor = str_replace(array('\r','\n'), '', $valor);
        }

        return $valor;
    }


    /*
     * Formato de datos
     */


    function eliminaCaracteresEspeciales($cadena){
        $caracteresOriginales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $caracteresModificados = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBSaaaaaaaceeeeiiiidnoooooouuuyybyRr';

        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($caracteresOriginales), $caracteresModificados);

        return utf8_encode($cadena);
    }


    function formatoDecimal($valor, $decimales = 2) {
        if (!estaVacio($valor)) {
            return number_format((float) $valor, $decimales);
        } else {
            return "$0.00";
        }
    }


    function formatoMillaresLetra($valor) {
        if (!estaVacio($valor)) {
            if ($valor > 1000) {
                if ($valor % 1000 == 0) {
                    $decimales = 0;
                } else {
                    $decimales = 1;
                }

                return number_format((float) $valor / 1000, $decimales) . "mil";
            } else {
                return number_format((float) $valor, 0);
            }
        } else {
            return "0";
        }
    }


    function formatoMoneda($valor, $decimales = 2) {
        if (!estaVacio($valor)) {
            return "$" . number_format((float) $valor, $decimales);
        } else {
            return "$0.00";
        }
    }


    function normalizaTelefono($telefono) {
        if (!estaVacio($telefono)) {
            return preg_replace('/\D/', '', $telefono);
        } else {
            return null;
        }
    }


    /*
     * Utilerias
     */


    function generaCadenaAleatoria($length = 3) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }


    function calculaTiempoDeInactividad($fechaInicial, $fechaFinal) {
        $tiempoInactividad = $fechaFinal->diff($fechaInicial);
        return $tiempoInactividad->d . " dias, " . $tiempoInactividad->h . " horas, " . $tiempoInactividad->i . " minutos";
    }


    function muestraBloqueo() {
        echo "<div class='table-struct full-width full-height'>"
                . "<div class='table-cell vertical-align-middle'>"
                    . "<div class='auth-form  ml-auto mr-auto no-float'>"
                        . "<div class='panel panel-default card-view mb-0'>"
                            . "<div class='panel-wrapper collapse in'>"
                                . "<div class='panel-body'>"
                                    . "<div class='row'>"
                                        . "<div class='col-sm-12 col-xs-12 text-center'>"
                                            . "<h3 class='mb-20 txt-danger'>Permisos insuficientes</h3>"
                                            . "<p class='font-18 txt-dark mb-15'>Tu usuario no cuenta con permiso para utilizar esta funci&oacute;n</p>"
                                            . "<p>Estos intentos quedan registrados autom&aacute;ticamente</p>"
                                        . "</div>"
                                    . "</div>"
                                . "</div>"
                            . "</div>"
                        . "</div>"
                    . "</div>"
                . "</div>"
            . "</div>";
    }


    function borraDirectorio($directorio) {
        if ($directorio !== "/" && is_dir($directorio)) {
            $objetos = scandir($directorio);

            foreach ($objetos as $objeto) {
                if ($objeto != "." && $objeto != "..") {
                    if (is_dir($directorio . "/" . $objeto))
                        borraDirectorio($directorio . "/" . $objeto);
                    else
                        unlink($directorio . "/" . $objeto);
                }
            }

            rmdir($directorio);
        }
    }


    function registraAnalitica($url, $selector, $evento) {

        // Inicializa variables

        $fechaActual = date("Y-m-d H:i:s");

        // Obtiene conexion a base de datos

	$conexion = obtenConexion();

        // Registra evento

        consulta($conexion, "INSERT INTO analitica (fechaRegistro, url, selector, evento) VALUES ('" . $fechaActual . "', '" . $url . "', " . (estaVacio($selector) ? "NULL" : "'" . $selector . "'") . ", " . (estaVacio($evento) ? "NULL" : "'" . $evento . "'") . ")");
    }


    function sincronizaInventarioInteliMotor($idConcesionario = null) {
        try {
//$rutaLog = "/Users/mvelasco/Socialware/error.log";
$rutaLog = "/var/www/html/error.log";
error_log("\n\n" . date("Y-m-d H:i:s"), 3, $rutaLog);

            // Obtiene conexion a base de datos

            $conexion = obtenConexion();
error_log("\nA", 3, $rutaLog);

            // Inicializa variables

            $fechaActual = date("Y-m-d H:i:s");

            // Se obtiene informacion de concesionario

            if (!estaVacio($idConcesionario)) {
error_log("\nB", 3, $rutaLog);
               //$concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $idConcesionario);
               $concesionario_BD = consulta($conexion, "SELECT c.*, (CASE WHEN (SELECT COUNT(*) FROM concesionario c2 WHERE c2.intelimotor_apiKey = c.intelimotor_apiKey) > 1 THEN 1 ELSE 0 END) AS intelimotor_llaveCompartida FROM concesionario c WHERE c.id = " . $idConcesionario);
            } else {
error_log("\nC", 3, $rutaLog);
               //$concesionario_BD = consulta($conexion, "SELECT * FROM concesionario");
               $concesionario_BD = consulta($conexion, "SELECT c.*, (CASE WHEN (SELECT COUNT(*) FROM concesionario c2 WHERE c2.intelimotor_apiKey = c.intelimotor_apiKey) > 1 THEN 1 ELSE 0 END) AS intelimotor_llaveCompartida FROM concesionario c;");
               //$concesionario_BD = consulta($conexion, "SELECT c.*, (SELECT COUNT(*) FROM concesionario c2 WHERE c2.id != c.id AND c2.intelimotor_businessUnit_id = c.intelimotor_businessUnit_id) AS intelimotor_llaveCompartida FROM concesionario c");
            }

            while ($concesionario = obtenResultado($concesionario_BD)) {
error_log("\nD", 3, $rutaLog);
                $idConcesionario = $concesionario["id"];
                $intelimotor_apiKey = $concesionario["intelimotor_apiKey"];
                $intelimotor_apiSecret = $concesionario["intelimotor_apiSecret"];
                $intelimotor_businessUnit_id = $concesionario["intelimotor_businessUnit_id"];
                $intelimotor_llaveCompartida = $concesionario["intelimotor_llaveCompartida"];

                //if (!estaVacio($intelimotor_apiKey) && !estaVacio($intelimotor_apiSecret)) {
                if (!estaVacio($intelimotor_apiKey) && !estaVacio($intelimotor_apiSecret) && ($intelimotor_llaveCompartida == 0 || !estaVacio($intelimotor_businessUnit_id))) {
error_log("\nE", 3, $rutaLog);

                    // Despublica todos los vehiculos del concesionario

                    consulta($conexion, "UPDATE vehiculo SET publicado = 0 WHERE idConcesionario = " . $idConcesionario);
error_log("\nE 1", 3, $rutaLog);
error_log("\nintelimotor_apiKey = " . $intelimotor_apiKey, 3, $rutaLog);
error_log("\nintelimotor_apiSecret = " . $intelimotor_apiSecret, 3, $rutaLog);

                    // Invoca web service InteliMotor

                    $canal = curl_init();

                    //curl_setopt($canal, CURLOPT_URL, "https://app.intelimotor.com/api/inventory-units?apiKey=89e42108c0292fdab98c7725d557ac5ac7b031c7dbfd5e4a8fc957f6c576e40a&apiSecret=df7d14926120badf19783f88b4b453cb37b681fbcbc8717a1df0f1c6e9b5aeb5&pageNumber=" . $pagina . "&pageSize=" . $tamanoPagina);
                    //curl_setopt($canal, CURLOPT_URL, "https://app.intelimotor.com/api/inventory-units?apiKey=89e42108c0292fdab98c7725d557ac5ac7b031c7dbfd5e4a8fc957f6c576e40a&apiSecret=df7d14926120badf19783f88b4b453cb37b681fbcbc8717a1df0f1c6e9b5aeb5&getAll=true");
                    curl_setopt($canal, CURLOPT_URL, "https://app.intelimotor.com/api/inventory-units?apiKey=" . $intelimotor_apiKey . "&apiSecret=" . $intelimotor_apiSecret . "&getAll=true");
                    curl_setopt($canal, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($canal, CURLOPT_HEADER, FALSE);
                    //curl_setopt($canal, CURLOPT_POST, TRUE);
                    //curl_setopt($canal, CURLOPT_POSTFIELDS, $json);
                    curl_setopt($canal, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
error_log("\nE 2", 3, $rutaLog);

                    $respuesta = curl_exec($canal);
error_log("\nE 3", 3, $rutaLog);

                    curl_close($canal);

                    $respuesta = json_decode($respuesta);

                    //foreach ($respuesta->data as $vehiculo) {
                    foreach ($respuesta as $vehiculo) {
error_log("\nF", 3, $rutaLog);
                        $id = $vehiculo->id;

error_log("\nid = " . $id, 3, $rutaLog);
error_log("\nintelimotor_llaveCompartida = " . $intelimotor_llaveCompartida, 3, $rutaLog);
error_log("\nintelimotor_businessUnit_id = " . $intelimotor_businessUnit_id, 3, $rutaLog);
error_log("\nvehiculo-businessUnit-id = " . $vehiculo->businessUnit->id, 3, $rutaLog);
                        if ($intelimotor_llaveCompartida == 0 || $intelimotor_businessUnit_id == $vehiculo->businessUnit->id) {
error_log("\nG", 3, $rutaLog);
                            $vehiculo_BD = consulta($conexion, "SELECT id FROM vehiculo WHERE intelimotor_id = '" . $id . "'");

                            if (cuentaResultados($vehiculo_BD) == 0) {
error_log("\nH", 3, $rutaLog);


error_log("\nINSERT INTO vehiculo (
                                        intelimotor_id,
                                        intelimotor_businessUnit_id,
                                        intelimotor_imported,
                                        intelimotor_kms,
                                        intelimotor_listPrice,
                                        intelimotor_title,
                                        intelimotor_brand,
                                        intelimotor_model,
                                        intelimotor_year,
                                        intelimotor_trim,
                                        intelimotor_transmission,
                                        intelimotor_doors,
                                        intelimotor_fuelType,
                                        intelimotor_steering,
                                        intelimotor_tractionControl,
                                        intelimotor_vehicleBodyType,
                                        intelimotor_engine,
                                        intelimotor_exteriorColor,
                                        intelimotor_interiorColor,
                                        intelimotor_hasAutopilot,
                                        intelimotor_hasLightOnReminder,
                                        intelimotor_hasOnboardComputer,
                                        intelimotor_hasRearFoldingSeat,
                                        intelimotor_hasSlidingRoof,
                                        intelimotor_hasXenonHeadlights,
                                        intelimotor_hasCoasters,
                                        intelimotor_hasClimateControl,
                                        intelimotor_hasAbsBrakes,
                                        intelimotor_hasAlarm,
                                        intelimotor_hasAlloyWheels,
                                        intelimotor_hasDriverAirbag,
                                        intelimotor_hasElectronicBrakeAssist,
                                        intelimotor_hasEngineInmovilizer,
                                        intelimotor_hasFogLight,
                                        intelimotor_hasFrontFoglights,
                                        intelimotor_hasPassengerAirbag,
                                        intelimotor_hasRainSensor,
                                        intelimotor_hasRearFoglights,
                                        intelimotor_hasRearWindowDefogger,
                                        intelimotor_hasRollBar,
                                        intelimotor_hasSideImpactAirbag,
                                        intelimotor_hasStabilityControl,
                                        intelimotor_hasSteeringWheelControl,
                                        intelimotor_hasThirdStop,
                                        intelimotor_hasCurtainAirbag,
                                        intelimotor_armored,
                                        intelimotor_hasAirConditioning,
                                        intelimotor_hasElectricMirrors,
                                        intelimotor_hasGps,
                                        intelimotor_hasHeadlightControl,
                                        intelimotor_hasHeadrestRearSeat,
                                        intelimotor_hasHeightAdjustableDriverSeat,
                                        intelimotor_hasLeatherUpholstery,
                                        intelimotor_hasLightSensor,
                                        intelimotor_hasPaintedBumper,
                                        intelimotor_hasParkingSensor,
                                        intelimotor_hasPowerWindows,
                                        intelimotor_hasRemoteTrunkRelease,
                                        intelimotor_hasElectricSeats,
                                        intelimotor_hasRearBackrest,
                                        intelimotor_hasCentralPowerDoorLocks,
                                        intelimotor_hasAmfmRadio,
                                        intelimotor_hasBluetooth,
                                        intelimotor_hasCdPlayer,
                                        intelimotor_hasDvd,
                                        intelimotor_hasMp3Player,
                                        intelimotor_hasSdCard,
                                        intelimotor_hasUsb,
                                        intelimotor_hasBullBar,
                                        intelimotor_hasSpareTyreSupport,
                                        intelimotor_hasTrayCover,
                                        intelimotor_hasTrayMat,
                                        intelimotor_hasWindscreenWiper,
                                        intelimotor_singleOwner,
                                        intelimotor_youtubeVideoUrl,
                                        intelimotor_picture,

                                        idConcesionario,
                                        idSucursal,
                                        fechaRegistro,
                                        publicado,
                                        imagenPrincipal,

                                        tipo,
                                        marca,
                                        modelo,
                                        version,
                                        ano,
                                        color,
                                        precio,
                                        litros,
                                        combustible,
                                        transmision,
                                        puertas,
                                        kilometraje,
                                        unicoDueno,
                                        descripcion,
                                        video_url,

                                        tieneAireAcondicionado,
                                        tieneFarosNiebla,
                                        tieneRadioAMFM,

                                        colorInterior,
                                        controlTraccion,
                                        direccion,
                                        esBlindado,
                                        esImportado,
                                        tieneAlarma,
                                        tieneAlfombrillaLlantaRefaccion,
                                        tieneAperturaRemotaCajuela,
                                        tieneAsientoConductorAjusteAltura,
                                        tieneAsientosElectricos,
                                        tieneAsientosTraserosAbatibles,
                                        tieneAsistenciaFrenado,
                                        tieneBandejaLlantaRefaccion,
                                        tieneBarraAntivuelco,
                                        tieneBluetooth,
                                        tieneBolsaAireConductor,
                                        tieneBolsaAirePasajero,
                                        tieneBolsasAireCortina,
                                        tieneBolsasAireLaterales,
                                        tieneCabecerasAsientosTraseros,
                                        tieneComputadoraAbordo,
                                        tieneControlEstabilidad,
                                        tieneControlLucesDelanteras,
                                        tieneControlTemperatura,
                                        tieneControlVolante,
                                        tieneDefensasColorCarroceria,
                                        tieneDesempanadorTrasero,
                                        tieneEspejosElectricos,
                                        tieneFrenosABS,
                                        tieneGps,
                                        tieneInmovilizador,
                                        tieneLimpiaparabrisas,
                                        tieneLlantaRefaccion,
                                        tieneLucesNieblaDelanteras,
                                        tieneLucesNieblaTraseras,
                                        tieneLucesXenon,
                                        tieneParachoques,
                                        tienePilotoAutomatico,
                                        tienePortavasos,
                                        tieneQuemacocos,
                                        tieneRecordatorioEncendidoLuces,
                                        tieneReproductorCD,
                                        tieneReproductorDVD,
                                        tieneReproductorMP3,
                                        tieneRespadosTraseros,
                                        tieneRinesAleacion,
                                        tieneSegurosElectricosCentralizados,
                                        tieneSensorLluvia,
                                        tieneSensoresLuz,
                                        tieneSensoresReversa,
                                        tieneTapiceriaPiel,
                                        tieneTarjetaSD,
                                        tieneTerceraLuzFrenado,
                                        tieneUsb,
                                        tieneVidriosElectricos
                                    ) VALUES (
                                        '" . $id . "',
                                        " . $vehiculo->businessUnit->id . "',
                                        " . ($vehiculo->imported == true ? "1" : "0") . ",
                                        " . $vehiculo->kms . ",
                                        " . $vehiculo->listPrice . ",
                                        '" . $vehiculo->listingInfo->title . "',
                                        '" . $vehiculo->listingInfo->brand . "',
                                        '" . $vehiculo->listingInfo->model . "',
                                        " . $vehiculo->listingInfo->year . ",
                                        '" . $vehiculo->listingInfo->trim . "',
                                        '" . $vehiculo->listingInfo->transmission . "',
                                        " . $vehiculo->listingInfo->doors . ",
                                        '" . $vehiculo->listingInfo->fuelType . "',
                                        '" . $vehiculo->listingInfo->steering . "',
                                        '" . $vehiculo->listingInfo->tractionControl . "',
                                        '" . $vehiculo->listingInfo->vehicleBodyType . "',
                                        '" . $vehiculo->listingInfo->engine . "',
                                        '" . $vehiculo->listingInfo->exteriorColor . "',
                                        '" . $vehiculo->listingInfo->interiorColor . "',
                                        " . ($vehiculo->listingInfo->hasAutopilot == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightOnReminder == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasOnboardComputer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoldingSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSlidingRoof == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasXenonHeadlights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCoasters == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasClimateControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAbsBrakes == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlarm == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlloyWheels == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDriverAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectronicBrakeAssist == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasEngineInmovilizer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFrontFoglights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPassengerAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRainSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoglights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearWindowDefogger == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRollBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSideImpactAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasStabilityControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSteeringWheelControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasThirdStop == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCurtainAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->armored == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAirConditioning == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricMirrors == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasGps == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadlightControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadrestRearSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeightAdjustableDriverSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLeatherUpholstery == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPaintedBumper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasParkingSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPowerWindows == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRemoteTrunkRelease == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricSeats == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearBackrest == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCentralPowerDoorLocks == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAmfmRadio == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBluetooth == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCdPlayer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDvd == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasMp3Player == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSdCard == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasUsb == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBullBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSpareTyreSupport == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayCover == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayMat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasWindscreenWiper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->singleOwner == true ? "1" : "0") . ",
                                        '" . $vehiculo->listingInfo->youtubeVideoUrl . "',
                                        '" . $vehiculo->listingInfo->pictures[0] . "',

                                        " . $idConcesionario . ",
                                        1,
                                        '" . $fechaActual . "',
                                        " . ($vehiculo->isSold == true ? "0" : "1") . ",
                                        '" . $vehiculo->listingInfo->pictures[0] . "',

                                        '" . $vehiculo->listingInfo->vehicleBodyType . "',
                                        '" . $vehiculo->listingInfo->brand . "',
                                        '" . $vehiculo->listingInfo->model . "',
                                        '" . $vehiculo->listingInfo->trim . "',
                                        " . $vehiculo->listingInfo->year . ",
                                        '" . $vehiculo->listingInfo->exteriorColor . "',
                                        " . $vehiculo->listPrice . ",
                                        '" . $vehiculo->listingInfo->engine . "',
                                        '" . $vehiculo->listingInfo->fuelType . "',
                                        '" . $vehiculo->listingInfo->transmission . "',
                                        " . $vehiculo->listingInfo->doors . ",
                                        " . $vehiculo->kms . ",
                                        " . ($vehiculo->listingInfo->singleOwner == true ? "1" : "0") . ",
                                        '" . $vehiculo->listingInfo->title . "',
                                        '" . $vehiculo->listingInfo->youtubeVideoUrl . "',

                                        " . ($vehiculo->listingInfo->hasAirConditioning == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAmfmRadio == true ? "1" : "0") . ",

                                        '" . $vehiculo->listingInfo->interiorColor . "',
                                        '" . $vehiculo->listingInfo->tractionControl . "',
                                        '" . $vehiculo->listingInfo->steering . "',
                                        " . ($vehiculo->listingInfo->armored == true ? "1" : "0") . ",
                                        " . ($vehiculo->imported == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlarm == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayMat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRemoteTrunkRelease == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeightAdjustableDriverSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricSeats == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoldingSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectronicBrakeAssist == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayCover == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRollBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBluetooth == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDriverAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPassengerAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCurtainAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSideImpactAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadrestRearSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasOnboardComputer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasStabilityControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadlightControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasClimateControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSteeringWheelControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPaintedBumper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearWindowDefogger == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricMirrors == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAbsBrakes == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasGps == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasEngineInmovilizer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasWindscreenWiper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSpareTyreSupport == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoglights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasXenonHeadlights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBullBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAutopilot == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCoasters == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSlidingRoof == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightOnReminder == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCdPlayer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDvd == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasMp3Player == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearBackrest == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlloyWheels == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCentralPowerDoorLocks == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRainSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasParkingSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLeatherUpholstery == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSdCard == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasThirdStop == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasUsb == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPowerWindows == true ? "1" : "0") . "
                                     )", 3, $rutaLog);

                                // Es insercion

                                consulta($conexion, "INSERT INTO vehiculo (
                                        intelimotor_id,
                                        intelimotor_businessUnit_id,
                                        intelimotor_imported,
                                        intelimotor_kms,
                                        intelimotor_listPrice,
                                        intelimotor_title,
                                        intelimotor_brand,
                                        intelimotor_model,
                                        intelimotor_year,
                                        intelimotor_trim,
                                        intelimotor_transmission,
                                        intelimotor_doors,
                                        intelimotor_fuelType,
                                        intelimotor_steering,
                                        intelimotor_tractionControl,
                                        intelimotor_vehicleBodyType,
                                        intelimotor_engine,
                                        intelimotor_exteriorColor,
                                        intelimotor_interiorColor,
                                        intelimotor_hasAutopilot,
                                        intelimotor_hasLightOnReminder,
                                        intelimotor_hasOnboardComputer,
                                        intelimotor_hasRearFoldingSeat,
                                        intelimotor_hasSlidingRoof,
                                        intelimotor_hasXenonHeadlights,
                                        intelimotor_hasCoasters,
                                        intelimotor_hasClimateControl,
                                        intelimotor_hasAbsBrakes,
                                        intelimotor_hasAlarm,
                                        intelimotor_hasAlloyWheels,
                                        intelimotor_hasDriverAirbag,
                                        intelimotor_hasElectronicBrakeAssist,
                                        intelimotor_hasEngineInmovilizer,
                                        intelimotor_hasFogLight,
                                        intelimotor_hasFrontFoglights,
                                        intelimotor_hasPassengerAirbag,
                                        intelimotor_hasRainSensor,
                                        intelimotor_hasRearFoglights,
                                        intelimotor_hasRearWindowDefogger,
                                        intelimotor_hasRollBar,
                                        intelimotor_hasSideImpactAirbag,
                                        intelimotor_hasStabilityControl,
                                        intelimotor_hasSteeringWheelControl,
                                        intelimotor_hasThirdStop,
                                        intelimotor_hasCurtainAirbag,
                                        intelimotor_armored,
                                        intelimotor_hasAirConditioning,
                                        intelimotor_hasElectricMirrors,
                                        intelimotor_hasGps,
                                        intelimotor_hasHeadlightControl,
                                        intelimotor_hasHeadrestRearSeat,
                                        intelimotor_hasHeightAdjustableDriverSeat,
                                        intelimotor_hasLeatherUpholstery,
                                        intelimotor_hasLightSensor,
                                        intelimotor_hasPaintedBumper,
                                        intelimotor_hasParkingSensor,
                                        intelimotor_hasPowerWindows,
                                        intelimotor_hasRemoteTrunkRelease,
                                        intelimotor_hasElectricSeats,
                                        intelimotor_hasRearBackrest,
                                        intelimotor_hasCentralPowerDoorLocks,
                                        intelimotor_hasAmfmRadio,
                                        intelimotor_hasBluetooth,
                                        intelimotor_hasCdPlayer,
                                        intelimotor_hasDvd,
                                        intelimotor_hasMp3Player,
                                        intelimotor_hasSdCard,
                                        intelimotor_hasUsb,
                                        intelimotor_hasBullBar,
                                        intelimotor_hasSpareTyreSupport,
                                        intelimotor_hasTrayCover,
                                        intelimotor_hasTrayMat,
                                        intelimotor_hasWindscreenWiper,
                                        intelimotor_singleOwner,
                                        intelimotor_youtubeVideoUrl,
                                        intelimotor_picture,

                                        idConcesionario,
                                        idSucursal,
                                        fechaRegistro,
                                        publicado,
                                        imagenPrincipal,

                                        tipo,
                                        marca,
                                        modelo,
                                        version,
                                        ano,
                                        color,
                                        precio,
                                        litros,
                                        combustible,
                                        transmision,
                                        puertas,
                                        kilometraje,
                                        unicoDueno,
                                        descripcion,
                                        video_url,

                                        tieneAireAcondicionado,
                                        tieneFarosNiebla,
                                        tieneRadioAMFM,

                                        colorInterior,
                                        controlTraccion,
                                        direccion,
                                        esBlindado,
                                        esImportado,
                                        tieneAlarma,
                                        tieneAlfombrillaLlantaRefaccion,
                                        tieneAperturaRemotaCajuela,
                                        tieneAsientoConductorAjusteAltura,
                                        tieneAsientosElectricos,
                                        tieneAsientosTraserosAbatibles,
                                        tieneAsistenciaFrenado,
                                        tieneBandejaLlantaRefaccion,
                                        tieneBarraAntivuelco,
                                        tieneBluetooth,
                                        tieneBolsaAireConductor,
                                        tieneBolsaAirePasajero,
                                        tieneBolsasAireCortina,
                                        tieneBolsasAireLaterales,
                                        tieneCabecerasAsientosTraseros,
                                        tieneComputadoraAbordo,
                                        tieneControlEstabilidad,
                                        tieneControlLucesDelanteras,
                                        tieneControlTemperatura,
                                        tieneControlVolante,
                                        tieneDefensasColorCarroceria,
                                        tieneDesempanadorTrasero,
                                        tieneEspejosElectricos,
                                        tieneFrenosABS,
                                        tieneGps,
                                        tieneInmovilizador,
                                        tieneLimpiaparabrisas,
                                        tieneLlantaRefaccion,
                                        tieneLucesNieblaDelanteras,
                                        tieneLucesNieblaTraseras,
                                        tieneLucesXenon,
                                        tieneParachoques,
                                        tienePilotoAutomatico,
                                        tienePortavasos,
                                        tieneQuemacocos,
                                        tieneRecordatorioEncendidoLuces,
                                        tieneReproductorCD,
                                        tieneReproductorDVD,
                                        tieneReproductorMP3,
                                        tieneRespadosTraseros,
                                        tieneRinesAleacion,
                                        tieneSegurosElectricosCentralizados,
                                        tieneSensorLluvia,
                                        tieneSensoresLuz,
                                        tieneSensoresReversa,
                                        tieneTapiceriaPiel,
                                        tieneTarjetaSD,
                                        tieneTerceraLuzFrenado,
                                        tieneUsb,
                                        tieneVidriosElectricos
                                    ) VALUES (
                                        '" . $id . "',
                                        " . $vehiculo->businessUnit->id . "',
                                        " . ($vehiculo->imported == true ? "1" : "0") . ",
                                        " . $vehiculo->kms . ",
                                        " . $vehiculo->listPrice . ",
                                        '" . $vehiculo->listingInfo->title . "',
                                        '" . $vehiculo->listingInfo->brand . "',
                                        '" . $vehiculo->listingInfo->model . "',
                                        " . $vehiculo->listingInfo->year . ",
                                        '" . $vehiculo->listingInfo->trim . "',
                                        '" . $vehiculo->listingInfo->transmission . "',
                                        " . $vehiculo->listingInfo->doors . ",
                                        '" . $vehiculo->listingInfo->fuelType . "',
                                        '" . $vehiculo->listingInfo->steering . "',
                                        '" . $vehiculo->listingInfo->tractionControl . "',
                                        '" . $vehiculo->listingInfo->vehicleBodyType . "',
                                        '" . $vehiculo->listingInfo->engine . "',
                                        '" . $vehiculo->listingInfo->exteriorColor . "',
                                        '" . $vehiculo->listingInfo->interiorColor . "',
                                        " . ($vehiculo->listingInfo->hasAutopilot == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightOnReminder == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasOnboardComputer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoldingSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSlidingRoof == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasXenonHeadlights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCoasters == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasClimateControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAbsBrakes == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlarm == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlloyWheels == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDriverAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectronicBrakeAssist == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasEngineInmovilizer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFrontFoglights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPassengerAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRainSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoglights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearWindowDefogger == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRollBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSideImpactAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasStabilityControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSteeringWheelControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasThirdStop == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCurtainAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->armored == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAirConditioning == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricMirrors == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasGps == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadlightControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadrestRearSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeightAdjustableDriverSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLeatherUpholstery == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPaintedBumper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasParkingSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPowerWindows == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRemoteTrunkRelease == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricSeats == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearBackrest == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCentralPowerDoorLocks == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAmfmRadio == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBluetooth == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCdPlayer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDvd == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasMp3Player == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSdCard == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasUsb == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBullBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSpareTyreSupport == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayCover == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayMat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasWindscreenWiper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->singleOwner == true ? "1" : "0") . ",
                                        '" . $vehiculo->listingInfo->youtubeVideoUrl . "',
                                        '" . $vehiculo->listingInfo->pictures[0] . "',

                                        " . $idConcesionario . ",
                                        1,
                                        '" . $fechaActual . "',
                                        " . ($vehiculo->isSold == true ? "0" : "1") . ",
                                        '" . $vehiculo->listingInfo->pictures[0] . "',

                                        '" . $vehiculo->listingInfo->vehicleBodyType . "',
                                        '" . $vehiculo->listingInfo->brand . "',
                                        '" . $vehiculo->listingInfo->model . "',
                                        '" . $vehiculo->listingInfo->trim . "',
                                        " . $vehiculo->listingInfo->year . ",
                                        '" . $vehiculo->listingInfo->exteriorColor . "',
                                        " . $vehiculo->listPrice . ",
                                        '" . $vehiculo->listingInfo->engine . "',
                                        '" . $vehiculo->listingInfo->fuelType . "',
                                        '" . $vehiculo->listingInfo->transmission . "',
                                        " . $vehiculo->listingInfo->doors . ",
                                        " . $vehiculo->kms . ",
                                        " . ($vehiculo->listingInfo->singleOwner == true ? "1" : "0") . ",
                                        '" . $vehiculo->listingInfo->title . "',
                                        '" . $vehiculo->listingInfo->youtubeVideoUrl . "',

                                        " . ($vehiculo->listingInfo->hasAirConditioning == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAmfmRadio == true ? "1" : "0") . ",

                                        '" . $vehiculo->listingInfo->interiorColor . "',
                                        '" . $vehiculo->listingInfo->tractionControl . "',
                                        '" . $vehiculo->listingInfo->steering . "',
                                        " . ($vehiculo->listingInfo->armored == true ? "1" : "0") . ",
                                        " . ($vehiculo->imported == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlarm == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayMat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRemoteTrunkRelease == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeightAdjustableDriverSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricSeats == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoldingSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectronicBrakeAssist == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasTrayCover == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRollBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBluetooth == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDriverAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPassengerAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCurtainAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSideImpactAirbag == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadrestRearSeat == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasOnboardComputer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasStabilityControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasHeadlightControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasClimateControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSteeringWheelControl == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPaintedBumper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearWindowDefogger == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasElectricMirrors == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAbsBrakes == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasGps == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasEngineInmovilizer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasWindscreenWiper == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSpareTyreSupport == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearFoglights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasXenonHeadlights == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasBullBar == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAutopilot == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCoasters == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSlidingRoof == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightOnReminder == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCdPlayer == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasDvd == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasMp3Player == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRearBackrest == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasAlloyWheels == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasCentralPowerDoorLocks == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasRainSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLightSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasParkingSensor == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasLeatherUpholstery == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasSdCard == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasThirdStop == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasUsb == true ? "1" : "0") . ",
                                        " . ($vehiculo->listingInfo->hasPowerWindows == true ? "1" : "0") . "
                                     )");
                            } else {
error_log("\nI", 3, $rutaLog);

                                // Es actualizacion

                                consulta($conexion, "UPDATE
                                        vehiculo
                                    SET
                                        intelimotor_businessUnit_id = '" . $vehiculo->businessUnit->id . "',
                                        intelimotor_imported = " . ($vehiculo->imported == true ? "1" : "0") . ",
                                        intelimotor_kms = " . $vehiculo->kms . ",
                                        intelimotor_listPrice = " . $vehiculo->listPrice . ",
                                        intelimotor_title = '" . $vehiculo->listingInfo->title . "',
                                        intelimotor_brand = '" . $vehiculo->listingInfo->brand . "',
                                        intelimotor_model = '" . $vehiculo->listingInfo->model . "',
                                        intelimotor_year = " . $vehiculo->listingInfo->year . ",
                                        intelimotor_trim = '" . $vehiculo->listingInfo->trim . "',
                                        intelimotor_transmission = '" . $vehiculo->listingInfo->transmission . "',
                                        intelimotor_doors = " . $vehiculo->listingInfo->doors . ",
                                        intelimotor_fuelType = '" . $vehiculo->listingInfo->fuelType . "',
                                        intelimotor_steering = '" . $vehiculo->listingInfo->steering . "',
                                        intelimotor_tractionControl = '" . $vehiculo->listingInfo->tractionControl . "',
                                        intelimotor_vehicleBodyType = '" . $vehiculo->listingInfo->vehicleBodyType . "',
                                        intelimotor_engine = '" . $vehiculo->listingInfo->engine . "',
                                        intelimotor_exteriorColor = '" . $vehiculo->listingInfo->exteriorColor . "',
                                        intelimotor_interiorColor = '" . $vehiculo->listingInfo->interiorColor . "',
                                        intelimotor_hasAutopilot = " . ($vehiculo->listingInfo->hasAutopilot == true ? "1" : "0") . ",
                                        intelimotor_hasLightOnReminder = " . ($vehiculo->listingInfo->hasLightOnReminder == true ? "1" : "0") . ",
                                        intelimotor_hasOnboardComputer = " . ($vehiculo->listingInfo->hasOnboardComputer == true ? "1" : "0") . ",
                                        intelimotor_hasRearFoldingSeat = " . ($vehiculo->listingInfo->hasRearFoldingSeat == true ? "1" : "0") . ",
                                        intelimotor_hasSlidingRoof = " . ($vehiculo->listingInfo->hasSlidingRoof == true ? "1" : "0") . ",
                                        intelimotor_hasXenonHeadlights = " . ($vehiculo->listingInfo->hasXenonHeadlights == true ? "1" : "0") . ",
                                        intelimotor_hasCoasters = " . ($vehiculo->listingInfo->hasCoasters == true ? "1" : "0") . ",
                                        intelimotor_hasClimateControl = " . ($vehiculo->listingInfo->hasClimateControl == true ? "1" : "0") . ",
                                        intelimotor_hasAbsBrakes = " . ($vehiculo->listingInfo->hasAbsBrakes == true ? "1" : "0") . ",
                                        intelimotor_hasAlarm = " . ($vehiculo->listingInfo->hasAlarm == true ? "1" : "0") . ",
                                        intelimotor_hasAlloyWheels = " . ($vehiculo->listingInfo->hasAlloyWheels == true ? "1" : "0") . ",
                                        intelimotor_hasDriverAirbag = " . ($vehiculo->listingInfo->hasDriverAirbag == true ? "1" : "0") . ",
                                        intelimotor_hasElectronicBrakeAssist = " . ($vehiculo->listingInfo->hasElectronicBrakeAssist == true ? "1" : "0") . ",
                                        intelimotor_hasEngineInmovilizer = " . ($vehiculo->listingInfo->hasEngineInmovilizer == true ? "1" : "0") . ",
                                        intelimotor_hasFogLight = " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        intelimotor_hasFrontFoglights = " . ($vehiculo->listingInfo->hasFrontFoglights == true ? "1" : "0") . ",
                                        intelimotor_hasPassengerAirbag = " . ($vehiculo->listingInfo->hasPassengerAirbag == true ? "1" : "0") . ",
                                        intelimotor_hasRainSensor = " . ($vehiculo->listingInfo->hasRainSensor == true ? "1" : "0") . ",
                                        intelimotor_hasRearFoglights = " . ($vehiculo->listingInfo->hasRearFoglights == true ? "1" : "0") . ",
                                        intelimotor_hasRearWindowDefogger = " . ($vehiculo->listingInfo->hasRearWindowDefogger == true ? "1" : "0") . ",
                                        intelimotor_hasRollBar = " . ($vehiculo->listingInfo->hasRollBar == true ? "1" : "0") . ",
                                        intelimotor_hasSideImpactAirbag = " . ($vehiculo->listingInfo->hasSideImpactAirbag == true ? "1" : "0") . ",
                                        intelimotor_hasStabilityControl = " . ($vehiculo->listingInfo->hasStabilityControl == true ? "1" : "0") . ",
                                        intelimotor_hasSteeringWheelControl = " . ($vehiculo->listingInfo->hasSteeringWheelControl == true ? "1" : "0") . ",
                                        intelimotor_hasThirdStop = " . ($vehiculo->listingInfo->hasThirdStop == true ? "1" : "0") . ",
                                        intelimotor_hasCurtainAirbag = " . ($vehiculo->listingInfo->hasCurtainAirbag == true ? "1" : "0") . ",
                                        intelimotor_armored = " . ($vehiculo->listingInfo->armored == true ? "1" : "0") . ",
                                        intelimotor_hasAirConditioning = " . ($vehiculo->listingInfo->hasAirConditioning == true ? "1" : "0") . ",
                                        intelimotor_hasElectricMirrors = " . ($vehiculo->listingInfo->hasElectricMirrors == true ? "1" : "0") . ",
                                        intelimotor_hasGps = " . ($vehiculo->listingInfo->hasGps == true ? "1" : "0") . ",
                                        intelimotor_hasHeadlightControl = " . ($vehiculo->listingInfo->hasHeadlightControl == true ? "1" : "0") . ",
                                        intelimotor_hasHeadrestRearSeat = " . ($vehiculo->listingInfo->hasHeadrestRearSeat == true ? "1" : "0") . ",
                                        intelimotor_hasHeightAdjustableDriverSeat = " . ($vehiculo->listingInfo->hasHeightAdjustableDriverSeat == true ? "1" : "0") . ",
                                        intelimotor_hasLeatherUpholstery = " . ($vehiculo->listingInfo->hasLeatherUpholstery == true ? "1" : "0") . ",
                                        intelimotor_hasLightSensor = " . ($vehiculo->listingInfo->hasLightSensor == true ? "1" : "0") . ",
                                        intelimotor_hasPaintedBumper = " . ($vehiculo->listingInfo->hasPaintedBumper == true ? "1" : "0") . ",
                                        intelimotor_hasParkingSensor = " . ($vehiculo->listingInfo->hasParkingSensor == true ? "1" : "0") . ",
                                        intelimotor_hasPowerWindows = " . ($vehiculo->listingInfo->hasPowerWindows == true ? "1" : "0") . ",
                                        intelimotor_hasRemoteTrunkRelease = " . ($vehiculo->listingInfo->hasRemoteTrunkRelease == true ? "1" : "0") . ",
                                        intelimotor_hasElectricSeats = " . ($vehiculo->listingInfo->hasElectricSeats == true ? "1" : "0") . ",
                                        intelimotor_hasRearBackrest = " . ($vehiculo->listingInfo->hasRearBackrest == true ? "1" : "0") . ",
                                        intelimotor_hasCentralPowerDoorLocks = " . ($vehiculo->listingInfo->hasCentralPowerDoorLocks == true ? "1" : "0") . ",
                                        intelimotor_hasAmfmRadio = " . ($vehiculo->listingInfo->hasAmfmRadio == true ? "1" : "0") . ",
                                        intelimotor_hasBluetooth = " . ($vehiculo->listingInfo->hasBluetooth == true ? "1" : "0") . ",
                                        intelimotor_hasCdPlayer = " . ($vehiculo->listingInfo->hasCdPlayer == true ? "1" : "0") . ",
                                        intelimotor_hasDvd = " . ($vehiculo->listingInfo->hasDvd == true ? "1" : "0") . ",
                                        intelimotor_hasMp3Player = " . ($vehiculo->listingInfo->hasMp3Player == true ? "1" : "0") . ",
                                        intelimotor_hasSdCard = " . ($vehiculo->listingInfo->hasSdCard == true ? "1" : "0") . ",
                                        intelimotor_hasUsb = " . ($vehiculo->listingInfo->hasUsb == true ? "1" : "0") . ",
                                        intelimotor_hasBullBar = " . ($vehiculo->listingInfo->hasBullBar == true ? "1" : "0") . ",
                                        intelimotor_hasSpareTyreSupport = " . ($vehiculo->listingInfo->hasSpareTyreSupport == true ? "1" : "0") . ",
                                        intelimotor_hasTrayCover = " . ($vehiculo->listingInfo->hasTrayCover == true ? "1" : "0") . ",
                                        intelimotor_hasTrayMat = " . ($vehiculo->listingInfo->hasTrayMat == true ? "1" : "0") . ",
                                        intelimotor_hasWindscreenWiper = " . ($vehiculo->listingInfo->hasWindscreenWiper == true ? "1" : "0") . ",
                                        intelimotor_singleOwner = " . ($vehiculo->listingInfo->singleOwner == true ? "1" : "0") . ",
                                        intelimotor_youtubeVideoUrl = '" . $vehiculo->listingInfo->youtubeVideoUrl . "',
                                        intelimotor_picture = '" . $vehiculo->listingInfo->pictures[0] . "',

                                        idConcesionario = " . $idConcesionario . ",
                                        publicado = " . ($vehiculo->isSold == true ? "0" : "1") . ",
                                        imagenPrincipal = '" . $vehiculo->listingInfo->pictures[0] . "',

                                        tipo = '" . $vehiculo->listingInfo->vehicleBodyType . "',
                                        marca = '" . $vehiculo->listingInfo->brand . "',
                                        modelo = '" . $vehiculo->listingInfo->model . "',
                                        version = '" . $vehiculo->listingInfo->trim . "',
                                        ano = " . $vehiculo->listingInfo->year . ",
                                        color = '" . $vehiculo->listingInfo->exteriorColor . "',
                                        precio = " . $vehiculo->listPrice . ",
                                        litros = '" . $vehiculo->listingInfo->engine . "',
                                        combustible = '" . $vehiculo->listingInfo->fuelType . "',
                                        transmision = '" . $vehiculo->listingInfo->transmission . "',
                                        puertas = " . $vehiculo->listingInfo->doors . ",
                                        kilometraje = " . $vehiculo->kms . ",
                                        unicoDueno = " . ($vehiculo->listingInfo->singleOwner == true ? "1" : "0") . ",
                                        descripcion = '" . $vehiculo->listingInfo->title . "',
                                        video_url = '" . $vehiculo->listingInfo->youtubeVideoUrl . "',

                                        tieneAireAcondicionado = " . ($vehiculo->listingInfo->hasAirConditioning == true ? "1" : "0") . ",
                                        tieneFarosNiebla = " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        tieneRadioAMFM = " . ($vehiculo->listingInfo->hasAmfmRadio == true ? "1" : "0") . ",

                                        colorInterior = '" . $vehiculo->listingInfo->interiorColor . "',
                                        controlTraccion = '" . $vehiculo->listingInfo->tractionControl . "',
                                        direccion = '" . $vehiculo->listingInfo->steering . "',
                                        esBlindado = " . ($vehiculo->listingInfo->armored == true ? "1" : "0") . ",
                                        esImportado = " . ($vehiculo->imported == true ? "1" : "0") . ",
                                        tieneAlarma = " . ($vehiculo->listingInfo->hasAlarm == true ? "1" : "0") . ",
                                        tieneAlfombrillaLlantaRefaccion = " . ($vehiculo->listingInfo->hasTrayMat == true ? "1" : "0") . ",
                                        tieneAperturaRemotaCajuela = " . ($vehiculo->listingInfo->hasRemoteTrunkRelease == true ? "1" : "0") . ",
                                        tieneAsientoConductorAjusteAltura = " . ($vehiculo->listingInfo->hasHeightAdjustableDriverSeat == true ? "1" : "0") . ",
                                        tieneAsientosElectricos = " . ($vehiculo->listingInfo->hasElectricSeats == true ? "1" : "0") . ",
                                        tieneAsientosTraserosAbatibles = " . ($vehiculo->listingInfo->hasRearFoldingSeat == true ? "1" : "0") . ",
                                        tieneAsistenciaFrenado = " . ($vehiculo->listingInfo->hasElectronicBrakeAssist == true ? "1" : "0") . ",
                                        tieneBandejaLlantaRefaccion = " . ($vehiculo->listingInfo->hasTrayCover == true ? "1" : "0") . ",
                                        tieneBarraAntivuelco = " . ($vehiculo->listingInfo->hasRollBar == true ? "1" : "0") . ",
                                        tieneBluetooth = " . ($vehiculo->listingInfo->hasBluetooth == true ? "1" : "0") . ",
                                        tieneBolsaAireConductor = " . ($vehiculo->listingInfo->hasDriverAirbag == true ? "1" : "0") . ",
                                        tieneBolsaAirePasajero = " . ($vehiculo->listingInfo->hasPassengerAirbag == true ? "1" : "0") . ",
                                        tieneBolsasAireCortina = " . ($vehiculo->listingInfo->hasCurtainAirbag == true ? "1" : "0") . ",
                                        tieneBolsasAireLaterales = " . ($vehiculo->listingInfo->hasSideImpactAirbag == true ? "1" : "0") . ",
                                        tieneCabecerasAsientosTraseros = " . ($vehiculo->listingInfo->hasHeadrestRearSeat == true ? "1" : "0") . ",
                                        tieneComputadoraAbordo = " . ($vehiculo->listingInfo->hasOnboardComputer == true ? "1" : "0") . ",
                                        tieneControlEstabilidad = " . ($vehiculo->listingInfo->hasStabilityControl == true ? "1" : "0") . ",
                                        tieneControlLucesDelanteras = " . ($vehiculo->listingInfo->hasHeadlightControl == true ? "1" : "0") . ",
                                        tieneControlTemperatura = " . ($vehiculo->listingInfo->hasClimateControl == true ? "1" : "0") . ",
                                        tieneControlVolante = " . ($vehiculo->listingInfo->hasSteeringWheelControl == true ? "1" : "0") . ",
                                        tieneDefensasColorCarroceria = " . ($vehiculo->listingInfo->hasPaintedBumper == true ? "1" : "0") . ",
                                        tieneDesempanadorTrasero = " . ($vehiculo->listingInfo->hasRearWindowDefogger == true ? "1" : "0") . ",
                                        tieneEspejosElectricos = " . ($vehiculo->listingInfo->hasElectricMirrors == true ? "1" : "0") . ",
                                        tieneFrenosABS = " . ($vehiculo->listingInfo->hasAbsBrakes == true ? "1" : "0") . ",
                                        tieneGps = " . ($vehiculo->listingInfo->hasGps == true ? "1" : "0") . ",
                                        tieneInmovilizador = " . ($vehiculo->listingInfo->hasEngineInmovilizer == true ? "1" : "0") . ",
                                        tieneLimpiaparabrisas = " . ($vehiculo->listingInfo->hasWindscreenWiper == true ? "1" : "0") . ",
                                        tieneLlantaRefaccion = " . ($vehiculo->listingInfo->hasSpareTyreSupport == true ? "1" : "0") . ",
                                        tieneLucesNieblaDelanteras = " . ($vehiculo->listingInfo->hasFogLight == true ? "1" : "0") . ",
                                        tieneLucesNieblaTraseras = " . ($vehiculo->listingInfo->hasRearFoglights == true ? "1" : "0") . ",
                                        tieneLucesXenon = " . ($vehiculo->listingInfo->hasXenonHeadlights == true ? "1" : "0") . ",
                                        tieneParachoques = " . ($vehiculo->listingInfo->hasBullBar == true ? "1" : "0") . ",
                                        tienePilotoAutomatico = " . ($vehiculo->listingInfo->hasAutopilot == true ? "1" : "0") . ",
                                        tienePortavasos = " . ($vehiculo->listingInfo->hasCoasters == true ? "1" : "0") . ",
                                        tieneQuemacocos = " . ($vehiculo->listingInfo->hasSlidingRoof == true ? "1" : "0") . ",
                                        tieneRecordatorioEncendidoLuces = " . ($vehiculo->listingInfo->hasLightOnReminder == true ? "1" : "0") . ",
                                        tieneReproductorCD = " . ($vehiculo->listingInfo->hasCdPlayer == true ? "1" : "0") . ",
                                        tieneReproductorDVD = " . ($vehiculo->listingInfo->hasDvd == true ? "1" : "0") . ",
                                        tieneReproductorMP3 = " . ($vehiculo->listingInfo->hasMp3Player == true ? "1" : "0") . ",
                                        tieneRespadosTraseros = " . ($vehiculo->listingInfo->hasRearBackrest == true ? "1" : "0") . ",
                                        tieneRinesAleacion = " . ($vehiculo->listingInfo->hasAlloyWheels == true ? "1" : "0") . ",
                                        tieneSegurosElectricosCentralizados = " . ($vehiculo->listingInfo->hasCentralPowerDoorLocks == true ? "1" : "0") . ",
                                        tieneSensorLluvia = " . ($vehiculo->listingInfo->hasRainSensor == true ? "1" : "0") . ",
                                        tieneSensoresLuz = " . ($vehiculo->listingInfo->hasLightSensor == true ? "1" : "0") . ",
                                        tieneSensoresReversa = " . ($vehiculo->listingInfo->hasParkingSensor == true ? "1" : "0") . ",
                                        tieneTapiceriaPiel = " . ($vehiculo->listingInfo->hasLeatherUpholstery == true ? "1" : "0") . ",
                                        tieneTarjetaSD = " . ($vehiculo->listingInfo->hasSdCard == true ? "1" : "0") . ",
                                        tieneTerceraLuzFrenado = " . ($vehiculo->listingInfo->hasThirdStop == true ? "1" : "0") . ",
                                        tieneUsb = " . ($vehiculo->listingInfo->hasUsb == true ? "1" : "0") . ",
                                        tieneVidriosElectricos = " . ($vehiculo->listingInfo->hasPowerWindows == true ? "1" : "0") . "
                                   WHERE
                                        intelimotor_id = '" . $id . "'");
                            }
error_log("\nJ", 3, $rutaLog);

                            // Carga imagenes

                            $indiceImagenes = 0;

                            consulta($conexion, "DELETE FROM imagen WHERE intelimotor_id = '" . $id . "'");

                            foreach ($vehiculo->listingInfo->pictures as $imagen) {
                                if ($indiceImagenes > 0) {
                                    consulta($conexion, "INSERT INTO imagen (intelimotor_id, imagen) VALUES ('" . $id . "', '" . $imagen . "')");
                                }

                                $indiceImagenes++;
                            }
                        }
                    }
                }
            }
error_log("\nK", 3, $rutaLog);

            // Cierra la conexion con base de datos y libera recursos

            liberaConexion($conexion);
        } catch (Exception $ex) {
error_log("\nException = " . $ex->getMessage(), 3, $rutaLog);
        }
    }


    /*
     * Comunicacion
     */


    function enviaCorreo($para, $titulo, $mensaje) {
        enviaCorreoMailjet($para, null, $titulo, $mensaje, null);
/*
        $cabeceras = "MIME-Version: 1.0\r\n";
        $cabeceras .= "Content-type: text/html; charset=utf-8\r\n";
        //$cabeceras .= "Cc: luislc1971@live.com.mx\r\n";
        //$cabeceras .= "Bcc: luis@socialware.mx,manuel@socialware.mx\r\n";
        $cabeceras .= "From: app@Albacar.com";

        mail($para, $titulo, $mensaje, $cabeceras);
*/
    }


    function enviaCorreoMailer($para, $titulo, $mensaje, $porSeparado = false, $remitenteCorreoElectronico = "contacto@socialware.mx", $remitenteNombre = "Socialware") {
        enviaCorreoMailjet($para, null, $titulo, $mensaje, null);
/*
        try {
            $email = new PHPMailer();
            $email->CharSet = "UTF-8";
            //$email->Encoding = "16bit";
            //$email->isHTML();
            $email->Subject = $titulo;
            //$email->Body = utf8_decode($mensaje);
            $email->MsgHTML($mensaje);
            $email->SetFrom($remitenteCorreoElectronico, $remitenteNombre);

            if (strpos($para, ",") !== false) {
                $destinatarios = explode(",", $para);

                if ($porSeparado) {
                    foreach ($destinatarios as $destinatario) {
                        $email2 = clone $email;
                        $email2->AddAddress($destinatario);
                        $email2->Send();
                    }
                } else {
                    foreach ($destinatarios as $destinatario) {
                        $email->AddAddress($destinatario);
                    }

                    $email->Send();
                }
            } else {
                $email->AddAddress($para);
                $email->Send();
            }
        } catch (Exception $e) {
        }
*/
    }


    function enviaCorreoAldeamo($para, $titulo, $mensaje) {
        enviaCorreoMailjet($para, null, $titulo, $mensaje, null);
/*
        try {
            $mensaje = preg_replace('/\s+/S', " ", $mensaje);

            // Arma cadena de destinatarios

            $cadenaPara = "";

            if (strpos($para, ",") > -1) {
                $destinatarios = explode(",", $para);

                foreach ($destinatarios as $destinatario) {
                    $cadenaPara .= '{ "email": "' . $destinatario . '" },';
                }

                $cadenaPara = substr($cadenaPara, 0, -1);
            } else {
                $cadenaPara = '{ "email": "' . $para . '" }';
            }

            // Forma cadena de envio

            $parametros = '{
                "from": {
                    "email": "contacto@albacar.mx",
                    "name": "Albacar"
                },
                "to": [
                    ' . $cadenaPara . '
                ],
                "replyTo": {
                    "email": "contacto@albacar.mx",
                    "name": "Albacar"
                },
                "subject": "' . $titulo . '",
                "body": "' . $mensaje . '",
                "options": {}
            }';

            // Envia correo electronico

            curl_setopt_array($canal = curl_init(), array(
                //CURLOPT_URL => "http://email.aldeamo.com:5000/v1/email",
                CURLOPT_URL => "http://api.ckpnd.com:5000/v1/email",
                CURLOPT_HTTPHEADER => array("Content-Type: application/json", "Authorization: Bearer 78c4191b.ba3a4d5f84831db85521931a"),
                CURLOPT_HEADER => true,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $parametros
            ));

            $respuesta = curl_exec($canal);

            $httpCode = curl_getinfo($canal, CURLINFO_HTTP_CODE);

            curl_close($canal);

            return $httpCode;
        } catch (Error $e) {
        } catch (Exception $e) {
        }
*/
    }


    /*
     * Integracion con Mailjet
     * https://dev.mailjet.com/email/guides/#getting-started
     */


    function enviaCorreoMailjet($para, $copia, $titulo, $mensaje, $archivoAdjunto) {
        try {
            $mensaje = str_replace(array("\r", "\n"), "", $mensaje);

            $fechaActual = date("Y-m-d_H:i:s");

            $mailjet = new Mailjet\Client('11fdc29f37135ee7ebb728a4db6b8936', 'c4e570820d0d6f54529c4c8989943698', true, ['version' => 'v3.1']);

            $archivo = null;
            $archivo_nombre = null;
            $archivo_tipoMime = null;
            $archivo_base64 = null;

            if (!estaVacio($archivoAdjunto)) {
                $archivo = file_get_contents($archivoAdjunto);
                $archivo_nombre = basename($archivoAdjunto);
                $archivo_tipoMime = get_mime_type($archivo_nombre);
                $archivo_base64 = base64_encode($archivo);
            }

            $contenido = '{';
                $contenido .= '"Messages": [';
                    $contenido .= '{';
                        $contenido .= '"From": {';
                            $contenido .= '"Email": "contacto@socialware.mx",';
                            $contenido .= '"Name": "ARCA"';
                        $contenido .= '},';

                        if (!estaVacio($para)) {
                            $contenido .= '"To": [';
                                foreach(explode(",", $para) as $destinatario) {
                                    $contenido .= '{';
                                        $contenido .= '"Email": "' . $destinatario . '",';
                                        $contenido .= '"Name": "' . $destinatario . '"';
                                    $contenido .= '},';
                                }
                                $contenido = rtrim($contenido, ",");
                            $contenido .= '],';
                        }

                        if (!estaVacio($copia)) {
                            $contenido .= '"Cc": [';
                                foreach(explode(",", $copia) as $destinatario) {
                                    $contenido .= '{';
                                        $contenido .= '"Email": "' . $destinatario . '",';
                                        $contenido .= '"Name": "' . $destinatario . '"';
                                    $contenido .= '},';
                                }
                                $contenido = rtrim($contenido, ",");
                            $contenido .= '],';
                        }

                        $contenido .= '"Subject": "' . $titulo . '",';
                        $contenido .= '"HTMLPart": "' . $mensaje . '",';

                        if (!estaVacio($archivo_nombre) && !estaVacio($archivo_tipoMime) && !estaVacio($archivo_base64)) {
                            $contenido .= '"Attachments": [';
                                $contenido .= '{';
                                    $contenido .= '"ContentType": "' . $archivo_tipoMime . '",';
                                    $contenido .= '"Filename": "' . $archivo_nombre . '",';
                                    $contenido .= '"Base64Content": "' . base64_encode($archivo) . '"';
                                $contenido .= '}';
                            $contenido .= '],';
                        }

                        $contenido .= '"CustomID": "arca.mx_' . $fechaActual . '"';
                    $contenido .= '}';
                $contenido .= ']';
            $contenido .= '}';

            $cuerpo = json_decode($contenido);

            $respuesta = $mailjet->post(Resources::$Email, ["body" => $cuerpo]);
            //$respuesta->success();

            //var_dump($respuesta->getData());
        } catch (Exception $e) {
        }
    }


    /*
     * Integracion con SMSs Masivos
     */


    function enviaSms($telefono, $mensaje) {

        // Integracion con SMSs Masivos

        curl_setopt_array($canal = curl_init(), array(
                CURLOPT_URL => "https://app.smsmasivos.com.mx/components/api/api.envio.sms.php",
                //HTTPS
                //CURLOPT_URL => "https://smsmasivos.com.mx/sms/api.envio.new.php",
                //CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POSTFIELDS => array(
                    // 1 via
                    "apikey" => "80607e5c71de572f05dddb716aed6df28702400e",
                    // 2 vias
                    //"apikey" => "367dd2b78953bd42ce135685a20eb1bcff9f0cd4",
                    "mensaje" => $mensaje,
                    "numcelular" => $telefono,
                    "numregion" => "52"/*,
                    "sandbox" => "1",*/
                )
            )
        );

        $respuesta = curl_exec($canal);
        curl_close($canal);

        $respuesta = json_decode($respuesta);

        echo $respuesta->mensaje;
    }


    /*
     * Integracion con One Signal
     */


    function enviaNotificacion($playerId, $mensaje) {
        echo "<div style='display:none'>";

        try {
            $contenido = array(
                "en" => $mensaje
            );

            $campos = array(
                "app_id" => "e036afcd-3b50-4a50-8fbf-6ff6d6d90767",
                "include_player_ids" => array($playerId),
                "contents" => $contenido
            );

            $campos = json_encode($campos);

            $canal = curl_init();
            curl_setopt($canal, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($canal, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8"));
            curl_setopt($canal, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($canal, CURLOPT_HEADER, FALSE);
            curl_setopt($canal, CURLOPT_POST, TRUE);
            curl_setopt($canal, CURLOPT_POSTFIELDS, $campos);
            curl_setopt($canal, CURLOPT_SSL_VERIFYPEER, FALSE);

            $respuesta = curl_exec($canal);
            curl_close($canal);
        } catch (Exception $e) {
        }

        echo "</div>";
    }
?>
