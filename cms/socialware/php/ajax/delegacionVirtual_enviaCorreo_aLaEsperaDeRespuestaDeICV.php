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

    // Elimina archivo

    try {

        // Envia correo

        $tramite = obtenResultado(consulta($conexion, "SELECT * FROM tramite WHERE id = " . $idTramite));
        $usuario = obtenResultado(consulta($conexion, "SELECT * FROM usuario WHERE id = (SELECT idUsuario FROM tramite WHERE id = " . $idTramite . ")"));
        $concesionario = obtenResultado(consulta($conexion, "SELECT * FROM concesionario WHERE id = (SELECT idConcesionario FROM tramite WHERE id = " . $idTramite . ")"));
        $mensajero = obtenResultado(consulta($conexion, "SELECT * FROM mensajero WHERE id = (SELECT idMensajero FROM tramite WHERE id = " . $idTramite . ")"));

        $correoNotificacion = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosICV'"))["valor"];
        $correoNotificacion_copia = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosARCA'"))["valor"];
        $archivo = $constante_rutaTramites . "/" . $idTramite . "/" . $tramite["archivo_expediente"];
        $archivo = $constante_rutaTramites . "/" . $idTramite . "/" . $tramite["archivo_expediente"];
        $archivoAdjunto = $constante_urlTramites . "/" . $idTramite . "/" . $tramite["archivo_expediente"];

        $titulo = "ARCA - " . $tramite["folioARCA"] . " - " . $concesionario["nombreComercial"] . " - " . trim($tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]);

        delegacionVirtual_enviaCorreo_aLaEsperaDeRespuestaDeICV(
            $correoNotificacion
            , $correoNotificacion_copia
            , $titulo
            , $idTramite
            , $tramite["folioARCA"]
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
            , $tramite["vehiculo_placa"]
            , $tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]
            , $tramite["cliente_correoElectronico"]
            , $tramite["tipoTramite"]
            , $tramite["tipoFactura"]
            , $tramite["numeroFactura"]
            , $archivoAdjunto);

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>
