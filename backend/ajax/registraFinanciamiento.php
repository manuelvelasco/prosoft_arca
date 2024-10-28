<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $idVehiculo = sanitiza($conexion, filter_input(INPUT_POST, "idVehiculo"));
        $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
        $apellido = sanitiza($conexion, filter_input(INPUT_POST, "apellido"));
        $telefono = sanitiza($conexion, filter_input(INPUT_POST, "telefono"));
        $enganche = sanitiza($conexion, filter_input(INPUT_POST, "enganche"));
        $plazo = sanitiza($conexion, filter_input(INPUT_POST, "plazo"));
        $pagoMensual = sanitiza($conexion, filter_input(INPUT_POST, "pagoMensual"));
        $montoCredito = sanitiza($conexion, filter_input(INPUT_POST, "montoCredito"));

        // Inserta registro

        consulta($conexion, "INSERT INTO financiamiento ("
                . "idVehiculo"
                . ", fechaRegistro"
                . ", nombre"
                . ", apellido"
                . ", telefono"
                . ", enganche"
                . ", plazo"
                . ", pagoMensual"
                . ", montoCredito"
                . ", montoMaximoCredito"
            . ") VALUES ("
                . $idVehiculo
                . ", NOW()"
                . ", '" . $nombre . "'"
                . ", '" . $apellido . "'"
                . ", '" . $telefono . "'"
                . ", " . $enganche
                . ", " . $plazo
                . ", " . $pagoMensual
                . ", " . $montoCredito
                . ", " . $montoCredito
            . ")");

        // Notifica a staff

        $parametros_BD = consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'financiamiento_correoElectronico'");

        while ($parametro = obtenResultado($parametros_BD)) {
            $mensaje = "Se ha registrado una solicitud de financiamiento."
                    . "<br /><br />"
                    . "<strong>Información del remitente</strong>"
                    . "<br />"
                    . "Nombre: " . $nombre
                    . "<br />"
                    . "Apellido: " . $apellido
                    . "<br />"
                    . "Teléfono: " . $telefono
                    . "<br /><br />"
                    . "<strong>Financiamiento</strong>"
                    . "<br />"
                    . "ID vehículo: " . $idVehiculo
                    . "<br />"
                    . "Enganche: $" . formatoDecimal($enganche, 2)
                    . "<br />"
                    . "Monto del crédito: $" . formatoDecimal($montoCredito, 2)
                    . "<br />"
                    . "Plazo: " . $plazo
                    . "<br />"
                    . "Pago mensual: $" . formatoDecimal($pagoMensual, 2);

            enviaCorreo($parametro["valor"], "Albacar | Solicitud de financiamiento", $mensaje);
        }

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>