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
        $mensaje = sanitiza($conexion, filter_input(INPUT_POST, "mensaje"));

        // Valida parametros

        $errores = "";

        if (estaVacio($nombre)) {
            $errores .= "<error><campo>nombre</campo><mensaje>Favor de ingresar nombre</mensaje></error>";
        }

        if (estaVacio($apellido)) {
            $errores .= "<error><campo>apellido</campo><mensaje>Favor de ingresar apellido</mensaje></error>";
        }

        if (estaVacio($telefono)) {
            $errores .= "<error><campo>telefono</campo><mensaje>Favor de ingresar número telefónico</mensaje></error>";
        } else {
            $telefono_expresionRegular = "/^[0-9]{10}$/" ;

            if (preg_match($telefono_expresionRegular, $telefono) === 0) {
                $errores .= "<error><campo>telefono</campo><mensaje>Favor de ingresar número telefónico de 10 dígitos</mensaje></error>";
            }
        }

        if (estaVacio($correoElectronico)) {
            $errores .= "<error><campo>correoElectronico</campo><mensaje>Favor de ingresar correo electrónico</mensaje></error>";
        } else {
            $correoElectronico_expresionRegular = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

            if (preg_match($correoElectronico_expresionRegular, $correoElectronico) === 0) {
                $errores .= "<error><campo>correoElectronico</campo><mensaje>Favor de ingresar correo electrónico válido</mensaje></error>";
            }
        }

        if (estaVacio($mensaje)) {
            $errores .= "<error><campo>mensaje</campo><mensaje>Favor de escribir mensaje de contacto</mensaje></error>";
        }

        if (!estaVacio($errores)) {
            $resultado = "<errores>" . $errores . "</errores>";
        } else {

            // Registra intencion de venta

            consulta($conexion, "INSERT INTO contacto ("
                    . "fechaRegistro"
                    . ", nombre"
                    . ", apellido"
                    . ", telefono"
                    . ", correoElectronico"
                    . ", mensaje"
                . ") VALUES ("
                    . "NOW()"
                    . ", '" . $nombre . "'"
                    . ", '" . $apellido . "'"
                    . ", '" . $telefono . "'"
                    . ", '" . $correoElectronico . "'"
                    . ", '" . mysqli_real_escape_string($conexion, $mensaje) . "'"
                . ")");

            // Envia notificacion a staff

            $parametros_BD = consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'contacto_correoElectronico'");

            while ($parametro = obtenResultado($parametros_BD)) {
                $mensaje = "Se ha registrado un contacto.<br /><br />"

                        . "<strong>Información del remitente</strong><br />"
                        . "Nombre: " . $nombre . "<br />"
                        . "Apellido: " . $apellido . "<br />"
                        . "Teléfono: " . $telefono . "<br />"
                        . "Correo electrónico: " . $correoElectronico . "<br />"
                        . "Mensaje: " . $mensaje;

                enviaCorreo($parametro["valor"], "Albacar | Contacto", $mensaje);
                enviaCorreo("luis@prosoft.mx", "Albacar | Contacto", $mensaje);
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
