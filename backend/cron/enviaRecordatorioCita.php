<?php
    header("Access-Control-Allow-Origin: *");

    //include("../comunes/funciones.php");
    //include("/var/www/html/albacar3/backend/comunes/funciones.php");
    include("/var/www/html/albacar/backend/comunes/funciones.php");
    
    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta telefono para cambio de citas

        $telefonoReprogramacion = obtenResultado(consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'cita_sms'"))["valor"];

        // Consulta citas que estan por expirar

        $citas_BD = consulta($conexion, "SELECT * FROM cita WHERE STR_TO_DATE(CONCAT(fechaSolicitada, ' ', horaSolicitada), '%Y-%m-%d %H:%i') BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 HOUR)");

        while ($cita = obtenResultado($citas_BD)) {

            // Envia recordatorio a usuario

            $mensaje = "En breve dará inicio nuestra cita, te esperamos con gusto.<br /><br />"
                    . "<strong>Información del solicitante</strong><br /><br />"
                    . "Nombre: " . $cita["nombre"] . "<br />"
                    . "Apellido: " . $cita["apellido"] . "<br />"
                    . "Teléfono: " . $cita["telefono"] . "<br />"
                    . "Correo electrónico: " . $cita["correoElectronico"] . "<br /><br />"
                    . "<strong>Forma de contacto</strong><br /><br />"
                    . "Fecha solicitada: " . $cita["fechaSolicitada"] . "<br />"
                    . "Horario solicitado: " . $cita["horaSolicitada"] . " hrs<br />"
                    . "Medio de contacto: " . $cita["medioSolicitado"] . "<br /><br />"
                    . "Si deseas reprogramar o cancelar tu cita marca al número ". $telefonoReprogramacion . ".<br /><br />";

            enviaCorreo($cita["correoElectronico"], "Albacar | Solicitud de cita", $mensaje);

            $mensaje = "Albacar.  En breve dara inicio nuestra cita, te esperamos con gusto.  Reprograma o cancela marcando al " . $telefonoReprogramacion . ".";

            enviaSms($cita["telefono"], $mensaje);
        }

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
