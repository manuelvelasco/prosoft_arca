<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $fechaSolicitada = sanitiza($conexion, filter_input(INPUT_POST, "fechaSolicitada"));
        $horaSolicitada = sanitiza($conexion, filter_input(INPUT_POST, "horaSolicitada"));
        $medioSolicitado = sanitiza($conexion, filter_input(INPUT_POST, "medioSolicitado"));
        $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
        $apellido = sanitiza($conexion, filter_input(INPUT_POST, "apellido"));
        $telefono = sanitiza($conexion, filter_input(INPUT_POST, "telefono"));
        $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
        $canalIngreso = sanitiza($conexion, filter_input(INPUT_POST, "canalIngreso"));
        $idVehiculo = sanitiza($conexion, filter_input(INPUT_POST, "idVehiculo"));

        // Inicializa variables

        $fechaActual = date('Y-m-d');

        // Valida parametros

        $errores = "";
        
        if (estaVacio($fechaSolicitada)) {
            $errores .= "<error><campo>fechaSolicitada</campo><mensaje>Elige una fecha</mensaje></error>";
        } else if (strlen($fechaSolicitada) == 10) {
            if ($fechaSolicitada < $fechaActual) {
                $errores .= "<error><campo>fechaSolicitada</campo><mensaje>Favor de solicitar una fecha igual o mayor a la actual.</mensaje></error>";
            }
        } else {
            $errores .= "<error><campo>fechaSolicitada</campo><mensaje>Formato de fecha no válido. Fromato correcto: DD/MM/AAAA</mensaje></error>";
        }
        
        if (estaVacio($horaSolicitada)) {
            $errores .= "<error><campo>horaSolicitada</campo><mensaje>Elige una hora</mensaje></error>";
        }

        if (estaVacio($medioSolicitado)) {
            $errores .= "<error><campo>medioSolicitado</campo><mensaje>Elige un medio de contacto</mensaje></error>";
        }

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

        if (!estaVacio($errores)) {
            $resultado = "<errores>" . $errores . "</errores>";
        } else {

            // Registra intencion de venta

            consulta($conexion, "INSERT INTO cita ("
                    . "fechaRegistro"
                    . ", fechaSolicitada"
                    . ", horaSolicitada"
                    . ", medioSolicitado"
                    . ", nombre"
                    . ", apellido"
                    . ", telefono"
                    . ", correoElectronico"
                    . ", canalIngreso"
                . ") VALUES ("
                    . "NOW()"
                    . ", '" . $fechaSolicitada . "'"
                    . ", '" . $horaSolicitada . "'"
                    . ", '" . $medioSolicitado . "'"
                    . ", '" . $nombre . "'"
                    . ", '" . $apellido . "'"
                    . ", '" . $telefono . "'"
                    . ", '" . $correoElectronico . "'"
                    . ", '" . $canalIngreso . "'"
                . ")");

            // Envia notificacion a staff

            $parametros_BD = consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'cita_correoElectronico'");

            while ($parametro = obtenResultado($parametros_BD)) {
                if (!estaVacio($parametro["valor"])) {
                    $mensaje = "Se ha registrado una cita.<br /><br />"

                            . "<strong>Información del solicitante</strong><br />"
                            . "Nombre: " . $nombre . "<br />"
                            . "Apellido: " . $apellido . "<br />"
                            . "Teléfono: " . $telefono . "<br />"
                            . "Correo electrónico: " . $correoElectronico . "<br /><br />"

                            . "<strong>Forma de contacto</strong><br />"
                            . "Fecha solicitada: " . $fechaSolicitada . "<br />"
                            . "Horario solicitado: " . $horaSolicitada . " hrs<br />"
                            . "Medio de contacto: " . $medioSolicitado . "<br /><br />"

                            . "<strong>ID vehículo</strong><br />"
                            . $idVehiculo . "<br /><br />"

                            . "<strong>Canal de ingreso</strong><br />"
                            . $canalIngreso;

                    enviaCorreo($parametro["valor"], "Albacar | Solicitud de cita", $mensaje);
                }
            }

            $parametros_BD = consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'cita_sms'");

            while ($parametro = obtenResultado($parametros_BD)) {
                if (!estaVacio($parametro["valor"])) {
                    $mensaje = "Albacar.  Se ha registrado una cita, accede al administrador de contenido para ver los detalles.";

                    enviaSms($parametro["valor"], $mensaje);
                }
            }

            // Envia notificacion a gerente

            if (!estaVacio($idVehiculo)) {
                $sucursal_BD = consulta($conexion, "SELECT correoElectronicoLider, whatsapp FROM sucursal WHERE id = (SELECT idSucursal FROM vehiculo WHERE id = " . $idVehiculo . ")");
                $sucursal = obtenResultado($sucursal_BD);

                if (!estaVacio($sucursal["correoElectronicoLider"])) {
                    $mensaje = "Se ha registrado una cita.<br /><br />"

                            . "<strong>Información del solicitante</strong><br />"
                            . "Nombre: " . $nombre . "<br />"
                            . "Apellido: " . $apellido . "<br />"
                            . "Teléfono: " . $telefono . "<br />"
                            . "Correo electrónico: " . $correoElectronico . "<br /><br />"

                            . "<strong>Forma de contacto</strong><br />"
                            . "Fecha solicitada: " . $fechaSolicitada . "<br />"
                            . "Horario solicitado: " . $horaSolicitada . " hrs<br />"
                            . "Medio de contacto: " . $medioSolicitado . "<br /><br />"

                            . "<strong>ID vehículo</strong><br />"
                            . $idVehiculo . "<br /><br />"

                            . "<strong>Canal de ingreso</strong><br />"
                            . $canalIngreso;

                    enviaCorreo($sucursal["correoElectronicoLider"], "Albacar | Solicitud de cita", $mensaje);
                }

                if (!estaVacio($sucursal["whatsapp"])) {
                    $mensaje = "Albacar.  Se ha registrado una cita, accede al administrador de contenido para ver los detalles.";

                    enviaSms($sucursal["whatsapp"], $mensaje);
                }
            }

            // Envia confirmacion a usuario

            $mensaje = "Hemos recibido tu solicitud de cita, en breve te contactaremos.<br /><br />"

                    . "<strong>Información del solicitante</strong><br />"
                    . "Nombre: " . $nombre . "<br />"
                    . "Apellido: " . $apellido . "<br />"
                    . "Teléfono: " . $telefono . "<br />"
                    . "Correo electrónico: " . $correoElectronico . "<br /><br />"

                    . "<strong>Forma de contacto</strong><br />"
                    . "Fecha solicitada: " . $fechaSolicitada . "<br />"
                    . "Horario solicitado: " . $horaSolicitada . " hrs<br />"
                    . "Medio de contacto: " . $medioSolicitado;

            enviaCorreo($correoElectronico, "Albacar | Solicitud de cita", $mensaje);

            $mensaje = "Albacar.  Hemos recibido tu solicitud de cita, en breve te contactaremos.";

            enviaSms($telefono, $mensaje);

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
