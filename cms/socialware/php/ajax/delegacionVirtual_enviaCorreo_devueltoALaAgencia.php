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

        $correoNotificacion = $usuario["correoElectronico"];
        $correoNotificacion_copia = null;
        $archivo = null;

        $titulo = "ARCA - " . $tramite["folioARCA"] . " - " . $concesionario["nombreComercial"] . " - " . trim($tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"]) . " - ACTUALIZACION";

        delegacionVirtual_enviaCorreo_devueltoALaAgencia(
            $correoNotificacion
            , $titulo
            , $idTramite
            , $tramite["folioARCA"]
            , $tramite["status"]
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
            , $tramite["numeroFactura"]);

        echo "ok";
    } catch (Exception $ex) {
        echo "error";
    }
?>
