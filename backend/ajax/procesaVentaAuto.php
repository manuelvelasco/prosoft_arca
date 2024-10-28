<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $paso = sanitiza($conexion, filter_input(INPUT_POST, "paso"));

        $marca = sanitiza($conexion, filter_input(INPUT_POST, "marca"));
        $modelo = sanitiza($conexion, filter_input(INPUT_POST, "modelo"));
        $ano = sanitiza($conexion, filter_input(INPUT_POST, "ano"));
        $version = sanitiza($conexion, filter_input(INPUT_POST, "version"));
        $kilometraje = sanitiza($conexion, filter_input(INPUT_POST, "kilometraje"));
        $color = sanitiza($conexion, filter_input(INPUT_POST, "color"));

        $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
        $apellido = sanitiza($conexion, filter_input(INPUT_POST, "apellido"));
        $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
        $telefono = sanitiza($conexion, filter_input(INPUT_POST, "telefono"));

        $precioSugerido = sanitiza($conexion, filter_input(INPUT_POST, "precioSugerido"));

        // Valida parametros

        $errores = "";

        if ($paso >= 1) {
            if (estaVacio($marca)) {
                $errores .= "<error><campo>marca</campo><mensaje>Proporciona una marca</mensaje></error>";
            }

            if (estaVacio($modelo)) {
                $errores .= "<error><campo>modelo</campo><mensaje>Proporciona un modelo</mensaje></error>";
            }

            if (estaVacio($ano)) {
                $errores .= "<error><campo>ano</campo><mensaje>Proporciona un año</mensaje></error>";
            }

            if (estaVacio($version)) {
                $errores .= "<error><campo>version</campo><mensaje>Proporciona una versión</mensaje></error>";
            }

            if (estaVacio($kilometraje)) {
                $errores .= "<error><campo>kilometraje</campo><mensaje>Proporciona un kilometraje</mensaje></error>";
            }

            if (estaVacio($color)) {
                $errores .= "<error><campo>color</campo><mensaje>Proporciona un color</mensaje></error>";
            }
        }

        if ($paso >= 2) {
            if (estaVacio($nombre)) {
                $errores .= "<error><campo>nombre</campo><mensaje>Proporciona tu nombre</mensaje></error>";
            }

            if (estaVacio($apellido)) {
                $errores .= "<error><campo>apellido</campo><mensaje>Proporciona tu apellido</mensaje></error>";
            }

            if (estaVacio($correoElectronico)) {
                $errores .= "<error><campo>correoElectronico</campo><mensaje>Proporciona tu correo electrónico</mensaje></error>";
            } else {
                $correoElectronico_expresionRegular = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

                if (preg_match($correoElectronico_expresionRegular, $correoElectronico) === 0) {
                    $errores .= "<error><campo>correoElectronico</campo><mensaje>Proporciona un correo electrónico válido</mensaje></error>";
                }
            }

            if (estaVacio($telefono)) {
                $errores .= "<error><campo>telefono</campo><mensaje>Proporciona tu teléfono</mensaje></error>";
            } else {
                $telefono_expresionRegular = "/^[0-9]{10}$/" ;

                if (preg_match($telefono_expresionRegular, $telefono) === 0) {
                    $errores .= "<error><campo>telefono</campo><mensaje>Proporciona un teléfono de 10 dígitos</mensaje></error>";
                }
            }
        }

        if ($paso == 3) {
            
        }

        if (!estaVacio($errores)) {
            $resultado = "<errores>" . $errores . "</errores>";
        } else {
            if ($paso == 3) {

                // Registra intencion de venta

                consulta($conexion, "INSERT INTO intencionventa ("
                        . "fechaRegistro"
                        . ", marca"
                        . ", modelo"
                        . ", ano"
                        . ", version"
                        . ", kilometraje"
                        . ", color"
                        . ", nombre"
                        . ", apellido"
                        . ", correoElectronico"
                        . ", telefono"
                        . ", precioSugerido"
                    . ") VALUES ("
                        . "NOW()"
                        . ", '" . $marca . "'"
                        . ", '" . $modelo . "'"
                        . ", " . $ano
                        . ", '" . $version . "'"
                        . ", " . $kilometraje
                        . ", '" . $color . "'"
                        . ", '" . $nombre . "'"
                        . ", '" . $apellido . "'"
                        . ", '" . $correoElectronico . "'"
                        . ", '" . $telefono . "'"
                        . ", " . $precioSugerido
                    . ")");

                // Envia notificacion a staff

                $parametros_BD = consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'quierovender_confirmacion_correoElectronico'");

                while ($parametro = obtenResultado($parametros_BD)) {
                    $mensaje = "Se ha registrado una intención de venta de auto.<br /><br />"
                            . "<strong>Información del vehículo</strong><br /><br />"
                            . "Marca: " . $marca . "<br />"
                            . "Modelo: " . $modelo . "<br />"
                            . "Año: " . $ano . "<br />"
                            . "Versión: " . $version . "<br />"
                            . "Kilometraje: " . $kilometraje . "<br />"
                            . "Color: " . $color . "<br /><br />"
                            . "<strong>Información de contacto</strong><br /><br />"
                            . "Nombre: " . $nombre . "<br />"
                            . "Apellido: " . $apellido . "<br />"
                            . "Correo electrónico: " . $correoElectronico . "<br />"
                            . "Teléfono: " . $telefono . "<br /><br />"
                            . "<strong>Precio sugerido: $" . formatoDecimal($precioSugerido, 2) . "</strong><br /><br />";

                    enviaCorreo($parametro["valor"], "Albacar | Intención de venta", $mensaje);
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
