<?php
    //setlocale(LC_ALL, "es_MX");     // Desarrollo
    setlocale(LC_ALL, "esm");     // Servidor
    date_default_timezone_set('America/Monterrey');


    // Carga plugin Mailjet


    //require "/Users/mvelasco/Socialware/Proyectos/Web/arca/backend/plugins/mailjet/vendor/autoload.php";
    //require "/var/www/html/socialware/arca/backend/plugins/mailjet/vendor/autoload.php";
    require "/var/www/html/arca/backend/plugins/mailjet/vendor/autoload.php";

    use Mailjet\Resources;


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


    function enviaSms($telefono, $mensaje) {
        enviaSmsWavy($telefono, $mensaje);
    }


    function enviaSmsMasivos($telefono, $mensaje) {

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


    function enviaSmsWavy($telefono, $mensaje) {
        $resultado = "";

        try {
            $telefonos = array();

            if (strpos($telefono, ",") > -1) {
                $telefonos = explode(",", $telefono);
            } else {
                array_push($telefonos, $telefono);
            }

            foreach ($telefonos as $telefono) {
                $telefono = str_replace(" ", "", $telefono);

                $json = '{
                    "destination": "521' . $telefono . '",
                    "messageText": "' . $mensaje . '"
                }';

                $canal = curl_init();

                curl_setopt($canal, CURLOPT_URL, "https://api-messaging.wavy.global/v1/send-sms");
                curl_setopt($canal, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($canal, CURLOPT_HEADER, FALSE);
                curl_setopt($canal, CURLOPT_POST, TRUE);
                curl_setopt($canal, CURLOPT_POSTFIELDS, $json);
                curl_setopt($canal, CURLOPT_HTTPHEADER, array(
                    "Content-Type: application/json",
                    "authenticationtoken: PNgz5FBL3f2_rZ3GwyTF96vBBxoMseZaDzjnWq_-",
                    "username: socialware"
                ));

                $respuesta = curl_exec($canal);

                curl_close($canal);
            }
        } catch (Error $e) {
        } catch (Exception $e) {
        }

        return $resultado;
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
