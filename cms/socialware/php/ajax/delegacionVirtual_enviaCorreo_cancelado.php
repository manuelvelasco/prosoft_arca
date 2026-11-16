<?php
    header("Access-Control-Allow-Origin: *");
    //header("Access-Control-Allow-Origin: http://www.requesting-page.com");

    /*
     * Elimina una imagen de la galeria fotografica de un vehiculo
     */

    session_start();

    include("../comunes/constantes.php");
    include("../comunes/funciones.php");

    // Obtiene parametros de request

    $idTramite = filter_input(INPUT_POST, "idTramite");

    // Obtiene conexion a base de datos

    $conexion = obtenConexion();

    try {

        $tramite = obtenResultado(consulta($conexion, "SELECT * FROM tramite WHERE id = " . $idTramite));
        $usuario = obtenResultado(consulta($conexion, "SELECT * FROM usuario WHERE id = (SELECT idUsuario FROM tramite WHERE id = " . $idTramite . ")"));
        $concesionario = obtenResultado(consulta($conexion, "SELECT * FROM concesionario WHERE id = (SELECT idConcesionario FROM tramite WHERE id = " . $idTramite . ")"));
        $mensajero = obtenResultado(consulta($conexion, "SELECT * FROM mensajero WHERE id = (SELECT idMensajero FROM tramite WHERE id = " . $idTramite . ")"));

        $statusAnterior = obtenResultado(consulta($conexion, "SELECT status FROM tramite_bitacora WHERE idTramite != " . $idTramite . " ORDER BY id DESC LIMIT 1"))["status"];

        // Envia correo usuario

        $correoNotificacion = $usuario["correoElectronico"];
        $correoNotificarCopia = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosARCA'"))["valor"];

        $titulo = "ARCA - " . $tramite["folioARCA"] . " - " . $concesionario["nombreComercial"] . " - " . trim($tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]) . " - ACTUALIZACION";

        $archivo = $constante_rutaTramites . "/" . $idTramite . "/" . $tramite["archivo_expediente"];

        delegacionVirtual_enviaCorreo_cancelado_usuario(
            $correoNotificacion
            , $titulo
            , $tramite["archivo_expediente"]
            , base64_encode(file_get_contents($archivo))
            , $idTramite
            , $tramite["folioARCA"]
            , $tramite["status"]
            , $tramite["vehiculo_marca"]
            , $tramite["vehiculo_modelo"]
            , $tramite["vehiculo_ano"]
            , $tramite["vehiculo_color"]
            , $tramite["vehiculo_vin"]
            , $tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]
            , $tramite["cliente_correoElectronico"]
            , $tramite["tipoTramite"]
            , $tramite["tipoFactura"]
            , $tramite["numeroFactura"]);

        // Envia correo usuario staff

        if ($statusAnterior == "A la espera de respuesta de ICV"
            || $statusAnterior == "Aprobado"
            || $statusAnterior == "Aprobado con biométricos"
            || $statusAnterior == "Actualización solicitada a ICV") {

            $correoNotificacion = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosICV'"))["valor"];
            $archivo = null;

            $titulo = "ARCA - " . $tramite["folioARCA"] . " - " . $concesionario["nombreComercial"] . " - " . trim($tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]) . " - ACTUALIZACION";

            delegacionVirtual_enviaCorreo_cancelado(
                $correoNotificacion
                , $correoNotificacion_copia
                , $idTramite
                , $tramite["folioARCA"]
                , $tramite["status"]
                , $concesionario["nombreComercial"]
                , $usuario["nombre"]
                , $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                , $tramite["comentariosConcesionario"]
                , $tramite["comentariosARCA"]
                , $tramite["vehiculo_marca"]
                , $tramite["vehiculo_modelo"]
                , $tramite["vehiculo_ano"]
                , $tramite["vehiculo_color"]
                , $tramite["vehiculo_vin"]
                , $tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]
                , $tramite["cliente_correoElectronico"]
                , $tramite["tipoTramite"]
                , $tramite["tipoFactura"]
                , $tramite["numeroFactura"]);
        }

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>
