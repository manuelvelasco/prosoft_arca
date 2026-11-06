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

        $correoNotificacion = $usuarioCapturo["correoElectronico"];
        $correoNotificacion_copia = null;
        $archivo = null;

        $titulo = "ARCA - " . $tramite["folioARCA"] . " - " . $concesionario["nombreComercial"] . " - " . trim($tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]) . " - ACTUALIZACION";

        $mensajeCorreo = "Se ha actualizado la solicitud de trámite con los siguientes datos, favor de ingresar al sistema para ver los detalles:"
            . "<br /><br />Id: ". $idTramite
            . "<br />Folio ARCA: ". $tramite["folioARCA"]
            . "<br />Estatus: ". $tramite["status"]
            . "<br />Marca: ". $tramite["vehiculo_marca"]
            . "<br />Modelo: ". $tramite["vehiculo_modelo"]
            . "<br />Año: ". $tramite["vehiculo_ano"]
            . "<br />Color: ". $tramite["vehiculo_color"]
            . "<br />VIN: ". $tramite["vehiculo_vin"]
            . "<br />Nombre Cliente: ". $tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]
            . "<br />Correo Cliente: ". $tramite["cliente_correoElectronico"]
            . "<br />Tipo de trámite: ". $tramite["tipoTramite"]
            . "<br />Tipo de factura: ". $tramite["tipoFactura"]
            . "<br />Número de factura: ". $tramite["numeroFactura"];

        $enviaCorreo = enviaCorreoMailjet($correoNotificacion, $correoNotificacion_copia, $titulo, $mensaje, $archivo, "delegacionvirtual@seminuevosarca.com.mx");

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>
