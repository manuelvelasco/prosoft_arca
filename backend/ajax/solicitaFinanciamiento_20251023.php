<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
        $apellido = sanitiza($conexion, filter_input(INPUT_POST, "apellido"));
        $telefono = sanitiza($conexion, filter_input(INPUT_POST, "telefono"));
        $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
        $enganche = sanitiza($conexion, filter_input(INPUT_POST, "enganche"));
        $plazo = sanitiza($conexion, filter_input(INPUT_POST, "plazo"));
        $pagoMensual = sanitiza($conexion, filter_input(INPUT_POST, "pagoMensual"));
        $montoCredito = sanitiza($conexion, filter_input(INPUT_POST, "montoCredito"));
        $montoMaximoCredito = sanitiza($conexion, filter_input(INPUT_POST, "montoMaximoCredito"));
        $idVehiculo = sanitiza($conexion, filter_input(INPUT_POST, "idVehiculo"));
        $descripcion = sanitiza($conexion, filter_input(INPUT_POST, "descripcion"));
        $tomarAutoACuenta = sanitiza($conexion, filter_input(INPUT_POST, "tomarAutoACuenta"));
        
        $fechaActual = date("Y-m-d H:i:s");
        // Valida parametros

        $errores = "";

        if (estaVacio($nombre)) {
            $errores .= "<error><campo>nombre</campo><mensaje>Proporciona tu nombre</mensaje></error>";
        }

        if (estaVacio($apellido)) {
            $errores .= "<error><campo>apellido</campo><mensaje>Proporciona tu apellido</mensaje></error>";
        }

        if (estaVacio($telefono)) {
            $errores .= "<error><campo>telefono</campo><mensaje>Proporciona tu teléfono</mensaje></error>";
        } else {
            $telefono_expresionRegular = "/^[0-9]{10}$/" ;

            if (preg_match($telefono_expresionRegular, $telefono) === 0) {
                $errores .= "<error><campo>telefono</campo><mensaje>Proporciona un teléfono de 10 dígitos</mensaje></error>";
            }
        }

        if (estaVacio($correoElectronico)) {
            $errores .= "<error><campo>correoElectronico</campo><mensaje>Proporciona tu correo electrónico</mensaje></error>";
        } else {
            $correoElectronico_expresionRegular = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

            if (preg_match($correoElectronico_expresionRegular, $correoElectronico) === 0) {
                $errores .= "<error><campo>correoElectronico</campo><mensaje>Proporciona un correo electrónico válido</mensaje></error>";
            }
        }

        if(!estaVacio($tomarAutoACuenta)){
            if(estaVacio($descripcion)){
                $errores .= "<error><campo>descripcion</campo><mensaje>Proporcione descripción de su vehículo.</mensaje></error>";
            }
        }

        if (!estaVacio($errores)) {
            $resultado = "<errores>" . $errores . "</errores>";
        } else {

            // Registra intencion de venta

            consulta($conexion, "INSERT INTO financiamiento ("
                    . "idVehiculo"
                    . ", fechaRegistro"
                    . ", nombre"
                    . ", apellido"
                    . ", telefono"
                    . ", correoElectronico"
                    . ", enganche"
                    . ", plazo"
                    . ", pagoMensual"
                    . ", montoCredito"
                    . ", montoMaximoCredito"
                . ") VALUES ("
                    . $idVehiculo
                    . ", '" . $fechaActual . "'"
                    . ", '" . $nombre . "'"
                    . ", '" . $apellido . "'"
                    . ", '" . $telefono . "'"
                    . ", '" . $correoElectronico . "'"
                    . ", ".$enganche
                    . ", ".$plazo
                    . ", ".$pagoMensual
                    . ", ".$montoCredito
                    . ", ".$montoMaximoCredito
                . ")");

            // Envia notificacion a staff

            // TODO Ajustar dirección de correo de envío
            /*
            $parametros_BD = consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'financiamiento_correoElectronico'");

            while ($parametro = obtenResultado($parametros_BD)) {
                $mensaje = "Se ha registrado una solicitud de financiamiento.<br /><br />"
                        . "<strong>Información del remitente</strong><br /><br />"
                        . "Nombre: " . $nombre . "<br />"
                        . "Apellido: " . $apellido . "<br />"
                        . "Teléfono: " . $telefono . "<br />"
                        . "Correo electrónico: " . $correoElectronico . "<br /><br />"
                        . "<strong>Financiamiento</strong><br /><br />"
                        . "ID vehículo: " . $idVehiculo . "<br />"
                        . "Enganche: $" . formatoDecimal($enganche, 2) . "<br />"
                        . "Plazo: " . $plazo . "<br />"
                        . "Pago mensual: $" . formatoDecimal($pagoMensual, 2) . "<br />"
                        . "Monto del crédito: $" . formatoDecimal($montoCredito, 2) . "<br />"
                        . "Monto máximo de crédito: $" . formatoDecimal($montoMaximoCredito, 2) . "<br />";

                if(!estaVacio($tomarAutoACuenta)){
                    $mensaje .= "El solicitante ha indicado que tiene un auto a tomar a cuenta, con la siguiente descripción:" . "<br />"
                            . $descripcion . "<br />";
                }
                
                enviaCorreo($parametro["valor"], "Albacar | Solicitud de financiamiento", $mensaje);
            }
            */
            //TODO Borrar de aqui
            $mensaje = "Se ha registrado una solicitud de financiamiento.<br /><br />"
                    . "<strong>Información del remitente</strong><br /><br />"
                    . "Nombre: " . $nombre . "<br />"
                    . "Apellido: " . $apellido . "<br />"
                    . "Teléfono: " . $telefono . "<br />"
                    . "Correo electrónico: " . $correoElectronico . "<br /><br />"
                    . "<strong>Financiamiento</strong><br /><br />"
                    . "ID vehículo: " . $idVehiculo . "<br />"
                    . "Enganche: $" . formatoDecimal($enganche, 2) . "<br />"
                    . "Plazo: " . $plazo . "<br />"
                    . "Pago mensual: $" . formatoDecimal($pagoMensual, 2) . "<br />"
                    . "Monto del crédito: $" . formatoDecimal($montoCredito, 2) . "<br />"
                    . "Monto máximo de crédito: $" . formatoDecimal($montoMaximoCredito, 2) . "<br />";

            if(!estaVacio($tomarAutoACuenta)){
                $mensaje .= "El solicitante ha indicado que tiene un auto a tomar a cuenta, con la siguiente descripción:" . "<br />"
                        . $descripcion . "<br />";
            }

            $destinatarios = $correoElectronico;
            enviaCorreo($destinatarios, "Albacar | Solicitud de financiamiento", $mensaje);
            //TODO a aca
            

            // TODO Ajustar número de celular de envío
            /*
            $parametros_BD = consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'financiamiento_sms'");

            while ($parametro = obtenResultado($parametros_BD)) {
                if (!estaVacio($parametro["valor"])) {
                    $mensaje = "Albacar.  Se ha registrado una solicitud de financiamiento, accede al administrador de contenido para ver los detalles.";

                    enviaSms($parametro["valor"], $mensaje);
                }
            }
            */

            //TODO Borrar de aqui
            $mensaje = "Albacar.  Se ha registrado una solicitud de financiamiento, accede al administrador de contenido para ver los detalles.";
            enviaSms("8115777042", $mensaje);
            $mensaje = "Albacar.  Hemos recibido su solicitud, lo contactaremos en breve.";
            enviaSms($telefono, $mensaje);
            //TODO a aca

            // Envia notificacion a gerente

            if (!estaVacio($idVehiculo)) {
                $sucursal_BD = consulta($conexion, "SELECT correoElectronicoLider, whatsapp FROM sucursal WHERE id = (SELECT idSucursal FROM vehiculo WHERE id = " . $idVehiculo . ")");
                $sucursal = obtenResultado($sucursal_BD);

                if (!estaVacio($sucursal["correoElectronicoLider"])) {
                    $mensaje = "Se ha registrado una solicitud de financiamiento.<br /><br />"
                            . "<strong>Información del remitente</strong><br /><br />"
                            . "Nombre: " . $nombre . "<br />"
                            . "Apellido: " . $apellido . "<br />"
                            . "Teléfono: " . $telefono . "<br />"
                            . "Correo electrónico: " . $correoElectronico . "<br /><br />"
                            . "<strong>Financiamiento</strong><br /><br />"
                            . "ID vehículo: " . $idVehiculo . "<br />"
                            . "Enganche: $" . formatoDecimal($enganche, 2) . "<br />"
                            . "Plazo: " . $plazo . "<br />"
                            . "Pago mensual: $" . formatoDecimal($pagoMensual, 2) . "<br />"
                            . "Monto del crédito: $" . formatoDecimal($montoCredito, 2) . "<br />"
                            . "Monto máximo de crédito: $" . formatoDecimal($montoMaximoCredito, 2) . "<br />";

                    enviaCorreo($sucursal["correoElectronicoLider"], "Albacar | Solicitud de financiamiento", $mensaje);
                }

                if (!estaVacio($sucursal["whatsapp"])) {
                    $mensaje = "Albacar.  Se ha registrado una solicitud de financiamiento, accede al administrador de contenido para ver los detalles.";

                    enviaSms($sucursal["whatsapp"], $mensaje);
                }
            }

            $resultado .= "ok";
        }

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
