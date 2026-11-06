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
        $usuario = obtenResultadoconsulta($conexion, "SELECT * FROM usuario WHERE id = (SELECT idUsuario FROM tramite WHERE id = " . $idTramite . ")");
        $concesionario = obtenResultado(consulta($conexion, "SELECT * FROM concesionario WHERE id = (SELECT idConcesionario FROM tramite WHERE id = " . $idTramite . ")"));
        $mensajero = obtenResultado(consulta($conexion, "SELECT * FROM mensajero WHERE id = (SELECT idMensajero FROM tramite WHERE id = " . $idTramite . ")"));

        $correoNotificacion = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosICV'"))["valor"];
        $correoNotificacion_copia = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosARCA'"))["valor"];
        $archivo = $constante_rutaTramites . "/" . $id . "/" . $tramite["archivo_expediente"];

        $titulo = "ARCA - " . $tramite["folioARCA"] . " - " . $concesionario["nombreComercial"] . " - " . trim($tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]);

        $mensaje = "Se ha actualizado la solicitud de trámite con los siguientes datos:"
            . "<br /><br />Id: ". $idTramite
            . "<br />Agencia: ". $concesionario["nombreComercial"]
            . "<br />Usuario capturó: " . $usuario["nombre"]
            . "<br />Folio ARCA: ". $tramite["folioARCA"]
            . "<br />Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
            . "<br />Comentarios agencia: ". $tramite["comentariosConcesionario"]
            . "<br />Comentarios ARCA: ". $tramite["comentariosARCA"]
            . "<br />Marca: ". $tramite["vehiculo_marca"]
            . "<br />Modelo: ". $tramite["vehiculo_modelo"]
            . "<br />Año: ". $tramite["vehiculo_ano"]
            . "<br />Color: ". $tramite["vehiculo_color"]
            . "<br />VIN: ". $tramite["vehiculo_vin"]
            . "<br />Número de placa: " . $tramite["vehiculo_placa"]
            . "<br />Nombre Cliente: ". $tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]
            . "<br />Correo Cliente: ". $tramite["cliente_correoElectronico"]
            . "<br />Tipo de trámite: ". $tramite["tipoTramite"]
            . "<br />Tipo de factura: ". $tramite["tipoFactura"]
            . "<br />Número de factura: ". $tramite["numeroFactura"];

        enviaCorreoMailjet($correoNotificacion, $correoNotificacion_copia, $titulo, $mensaje, $archivo, "delegacionvirtual@seminuevosarca.com.mx");

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>
