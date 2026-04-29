<?php include("socialware/php/comunes/manejaSesion.php"); 
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <style>

            /* Botones */

            #boton_cerrar {
                margin-right: 0;
            }

            #boton_eliminar {
                margin-left: 0;
            }

            #contenedor_botonesDerechos {
                text-align: right;
            }

            #contenedor_botonesIzquierdos {
                text-align: left;
            }

            /* Despliegue embebido sin margen izquierdo por ausencia de menu */

            .app-content {
                margin-left: 0;
            }

            /* Tablas de permisos */

            .tabla_permisos {
                width: 100%;
            }

            .tabla_permisos th {
                text-align: left;
                width: 50%;
            }

            /* Varios */

            hr {
                border-color: #888;
            }
        </style>
    </head>


    <body class="app ltr">
        <?php

            // Obtiene parametros de request

            $esSubmit = sanitiza($conexion, filter_input(INPUT_POST, "esSubmit"));
            $id = sanitiza($conexion, filter_input(INPUT_GET, "id"));
            if($esSubmit == 1){
                $id = sanitiza($conexion, filter_input(INPUT_POST, "id"));
            }
            $campo_id = sanitiza($conexion, filter_input(INPUT_POST, "campo_id"));
            $habilitado = sanitiza($conexion, filter_input(INPUT_POST, "habilitado"));
            $eliminado = sanitiza($conexion, filter_input(INPUT_POST, "eliminado"));
            $fechaAlta = sanitiza($conexion, filter_input(INPUT_POST, "fechaAlta"));
            $status = sanitiza($conexion, filter_input(INPUT_POST, "status"));
            $folioARCA = sanitiza($conexion, filter_input(INPUT_POST, "folioARCA"));
            $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));
            $idUsuario = sanitiza($conexion, filter_input(INPUT_POST, "idUsuario"));
            $idMensajero = sanitiza($conexion, filter_input(INPUT_POST, "idMensajero"));
            $comentariosARCA = sanitiza($conexion, filter_input(INPUT_POST, "comentariosARCA"));
            $comentariosConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "comentariosConcesionario"));

            $vehiculo_marca = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_marca"));
            $vehiculo_modelo = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_modelo"));
            $vehiculo_ano = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_ano"));
            $vehiculo_color = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_color"));
            $vehiculo_vin = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_vin"));
            $cliente_nombre = sanitiza($conexion, filter_input(INPUT_POST, "cliente_nombre"));
            $cliente_apellidoPaterno = sanitiza($conexion, filter_input(INPUT_POST, "cliente_apellidoPaterno"));
            $cliente_apellidoMaterno = sanitiza($conexion, filter_input(INPUT_POST, "cliente_apellidoMaterno"));
            $cliente_correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "cliente_correoElectronico"));

            $fechaRecepcionCorreoICV = sanitiza($conexion, filter_input(INPUT_POST, "fechaRecepcionCorreoICV"));
            $comentariosICV = sanitiza($conexion, filter_input(INPUT_POST, "comentariosICV"));
            $ejecutivoICV = sanitiza($conexion, filter_input(INPUT_POST, "ejecutivoICV"));
            $folioICV = sanitiza($conexion, filter_input(INPUT_POST, "folioICV"));

            $accion = sanitiza($conexion, filter_input(INPUT_POST, "accion"));

            if($accion == "Clonar"){
                $id = "";
            }
        

            // Parametros enviados por origen

            $origen = sanitiza($conexion, filter_input(INPUT_POST, "origen"));

            // Inicializa variables

            $mensaje = "";
            $fechaActual = date("Y-m-d H:i:s");
            $habilitado = estaVacio($habilitado) ? 0 : 1;
            $eliminado = estaVacio($eliminado) ? 0 : 1;
            $imagenPrincipal = "";

            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($idConcesionario)) {
                    $mensaje .= "* Agencia<br />";
                }

                if (estaVacio($idMensajero)) {
                    $mensaje .= "* Mensajero<br />";
                }

                if (estaVacio($vehiculo_marca)) {
                    $mensaje .= "* Marca<br />";
                }

                if (estaVacio($vehiculo_modelo)) {
                    $mensaje .= "* Modelo<br />";
                }

                if (estaVacio($vehiculo_ano)) {
                    $mensaje .= "* Año<br />";
                }

                if (estaVacio($vehiculo_color)) {
                    $mensaje .= "* Color<br />";
                }

                if (estaVacio($vehiculo_vin)) {
                    $mensaje .= "* VIN<br />";
                }

                if (estaVacio($cliente_nombre)) {
                    $mensaje .= "* Nombre<br />";
                }

                if (estaVacio($cliente_apellidoPaterno)) {
                    $mensaje .= "* Apellido Paterno<br />";
                }

                if (estaVacio($cliente_apellidoMaterno)) {
                    $mensaje .= "* Apellido Materno<br />";
                }

                if (estaVacio($cliente_correoElectronico)) {
                    $mensaje .= "* Correo electrónico<br />";
                }

                if((!estaVacio($id)) && ($accion == "Devuelto a la agencia")) {
                    if(estaVacio($comentariosARCA)){
                        $mensaje .= "* Comentarios ARCA<br />";
                    }
                }

                if((!estaVacio($id)) && (($accion == "Aprobado") || ($accion == "Rechazado"))) {
                    if(estaVacio($ejecutivoICV)){
                        $mensaje .= "* Ejecutivo ICV<br />";
                    }

                    if(estaVacio($fechaRecepcionCorreoICV)){
                        $mensaje .= "* Fecha y hora en la que se recibió correo de ICV<br />";
                    }

                    if(estaVacio($comentariosICV)){
                        $mensaje .= "* Comentarios ICV<br />";
                    }
                }

                if((!estaVacio($id)) && ($accion == "Aprobado")) {
                    if(estaVacio($folioICV)){
                        $mensaje .= "* Folio ICV<br />";
                    }

                    if (!(isset($_FILES['archivo_solicitudICV']) && $_FILES['archivo_solicitudICV']['error'] === UPLOAD_ERR_OK)) {
                        $mensaje .= "* PDF de la solicitud de ICV <br />";
                    }
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;

                    if(!estaVacio($id)){

                        $tramite_BD = consulta($conexion, "SELECT * FROM tramite WHERE id = " . $id);
                        $tramite = obtenResultado($tramite_BD);

                        $archivo_expediente = $tramite["archivo_expediente"];
                        $archivo_solicitudICV = $tramite["archivo_solicitudICV"];

                        $idUsuarioCapturo = $tramite["idUsuario"];
                        $usuarioCapturo_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $idUsuarioCapturo);
                        $usuarioCapturo = obtenResultado($usuarioCapturo_BD);

                    }
                } else {
                    if (estaVacio($id)) {

                        // Es insercion

                        if (!(isset($_FILES['archivo_expediente']) && $_FILES['archivo_expediente']['error'] === UPLOAD_ERR_OK)) {
                            $mensaje .= "* PDF de expediente <br />";
                        }

                        if (!estaVacio($mensaje)) {
                            $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                        } else {

                            consulta($conexion, "INSERT INTO tramite ("
                                    . "idConcesionario"
                                    . ", idMensajero"
                                    . ", vehiculo_marca"
                                    . ", vehiculo_modelo"
                                    . ", vehiculo_ano"
                                    . ", vehiculo_color"
                                    . ", vehiculo_vin"
                                    . ", cliente_nombre"
                                    . ", cliente_apellidoPaterno"
                                    . ", cliente_apellidoMaterno"
                                    . ", cliente_correoElectronico"
                                    . ", fechaAlta"
                                    . ", status"
                                    . ", idUsuario"
                                    . ", comentariosConcesionario"
                                . ") VALUES ("
                                    . $idConcesionario
                                    .", " . $idMensajero
                                    . ", '" . $vehiculo_marca . "'"
                                    . ", '" . $vehiculo_modelo . "'"
                                    . ", '" . $vehiculo_ano . "'"
                                    . ", '" . $vehiculo_color . "'"
                                    . ", '" . $vehiculo_vin . "'"
                                    . ", '" . $cliente_nombre . "'"
                                    . ", '" . $cliente_apellidoPaterno . "'"
                                    . ", '" . $cliente_apellidoMaterno . "'"
                                    . ", '" . $cliente_correoElectronico . "'"
                                    . ", '" . $fechaActual . "'"
                                    . ", 'Enviado a ARCA'"
                                    .", " . $usuario_id
                                    . ", '" . $comentariosConcesionario . "'"
                                . ")");

                            $tramite_BD = consulta($conexion, "SELECT * FROM tramite WHERE id = (SELECT MAX(id) FROM tramite)");
                            $tramite = obtenResultado($tramite_BD);

                            $id = $tramite["id"];
                            $fechaAlta = $tramite["fechaAlta"];
                            $status = $tramite["status"];
                            $folioARCA = $tramite["folioARCA"];
                            $idConcesionario = $tramite["idConcesionario"];
                            $idUsuario = $tramite["idUsuario"];
                            $idMensajero = $tramite["idMensajero"];
                            $comentariosARCA = $tramite["comentariosARCA"];
                            $comentariosConcesionario = $tramite["comentariosConcesionario"];

                            $vehiculo_marca = $tramite["vehiculo_marca"];
                            $vehiculo_modelo = $tramite["vehiculo_modelo"];
                            $vehiculo_ano = $tramite["vehiculo_ano"];
                            $vehiculo_color = $tramite["vehiculo_color"];
                            $vehiculo_vin = $tramite["vehiculo_vin"];
                            $cliente_nombre = $tramite["cliente_nombre"];
                            $cliente_apellidoPaterno = $tramite["cliente_apellidoPaterno"];
                            $cliente_apellidoMaterno = $tramite["cliente_apellidoMaterno"];
                            $cliente_correoElectronico = $tramite["cliente_correoElectronico"];
                            $archivo_expediente = $tramite["archivo_expediente"];

                            $fechaRecepcionCorreoICV = $tramite["fechaRecepcionCorreoICV"];
                            $comentariosICV = $tramite["comentariosICV"];
                            $archivo_solicitudICV = $tramite["archivo_solicitudICV"];
                            $ejecutivoICV = $tramite["ejecutivoICV"];
                            $folioICV = $tramite["folioICV"];

                            $usuarioCapturo_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $idUsuario);
                            $usuarioCapturo = obtenResultado($usuarioCapturo_BD);

                            consulta($conexion, "INSERT INTO tramite_bitacora ("
                                    . "idTramite"
                                    . ", idUsuario"
                                    . ", fechaRegistro"
                                    . ", status"
                                    . ", tiempoTranscurrido"
                                . ") VALUES ("
                                    . $id
                                    .", " . $usuario_id
                                    . ", '" . $fechaActual . "'"
                                    . ", 'Enviado a ARCA'"
                                    . ", 0"
                                . ")");

                            // Carga pdf expediente

                            if (isset($_FILES["archivo_expediente"])) {
                                try {
                                    $archivo = $_FILES["archivo_expediente"];

                                    if ($archivo["size"] > 0) {
                                        $nombreEstandarizado = $id . "_expediente_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                        $archivoDestino = $constante_rutaTramites . "/" . $id . "/" . $nombreEstandarizado;

                                        if (!file_exists($constante_rutaTramites . "/" . $id)) {
                                            mkdir($constante_rutaTramites . "/" . $id, 0755, true);
                                        }

                                        move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                        consulta($conexion, "UPDATE tramite SET archivo_expediente = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                        $archivo_expediente = $nombreEstandarizado;
                                    }
                                } catch (Exception $e) {

                                }
                            }

                            //Proceso enviar correo

                            $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $idConcesionario);
                            $concesionario = obtenResultado($concesionario_BD);

                            $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE id = " . $idMensajero);
                            $mensajero = obtenResultado($mensajero_BD);

                            $correoNotificacion_BD = consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_generarTramite_correosARCA'");
                            $correoNotificacion = obtenResultado($correoNotificacion_BD);


                            $titulo = "Nuevo solicitud de trámite";
                            $mensajeCorreo = "Se ha recibido una nueva solicitud de trámite con los siguientes datos:"
                                     . "<br/>Id: ". $id 
                                     . "<br/>Agencia: ". $concesionario["nombreComercial"]
                                     . "<br/>Usuario capturó: " . $usuario_nombre
                                     . "<br/> Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                     . "<br/> Comentarios agencia: ". $comentariosConcesionario
                                     . "<br/> Marca: ". $vehiculo_marca
                                     . "<br/> Modelo: ". $vehiculo_modelo
                                     . "<br/> Año: ". $vehiculo_ano
                                     . "<br/> Color: ". $vehiculo_color
                                     . "<br/> VIN: ". $vehiculo_vin
                                     . "<br/> Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                     . "<br/> Correo Cliente: ". $cliente_correoElectronico
                                     ;


                            $enviaCorreo = enviaCorreoMailjet($correoNotificacion["valor"],"", $titulo, $mensajeCorreo, $archivoDestino);

                            $mensaje = "ok - El tramite ha sido registrado";

                            $accion = "";

                            registraEvento("CMS : Alta de trámite | id = " . $id);
                            
                        }
                    } else {

                        // Es actualizacion

                        consulta($conexion, "UPDATE tramite SET "
                            . "idConcesionario = " . $idConcesionario
                            . ", idMensajero = " . $idMensajero
                            . ", folioARCA = '" . $folioARCA . "'"
                            . ", folioICV = '" . $folioICV . "'"
                            . ", status = '" . $accion . "'"
                            . ", vehiculo_marca = '" . $vehiculo_marca . "'"
                            . ", vehiculo_modelo = '" . $vehiculo_modelo . "'"
                            . ", vehiculo_ano = '" . $vehiculo_ano . "'"
                            . ", vehiculo_color = '" . $vehiculo_color . "'"
                            . ", vehiculo_vin = '" . $vehiculo_vin . "'"
                            . ", cliente_nombre = '" . $cliente_nombre . "'"
                            . ", cliente_apellidoPaterno = '" . $cliente_apellidoPaterno . "'"
                            . ", cliente_apellidoMaterno = '" . $cliente_apellidoMaterno . "'"
                            . ", cliente_correoElectronico = '" . $cliente_correoElectronico . "'"
                            . ", comentariosConcesionario = '" . $comentariosConcesionario . "'"
                            . ", comentariosARCA = '" . $comentariosARCA . "'"
                            . ", comentariosICV = '" . $comentariosICV . "'"
                            . ", ejecutivoICV = '" . $ejecutivoICV . "'"
                            . " WHERE id = " . $id);

                        $tramite_BD = consulta($conexion, "SELECT * FROM tramite WHERE id = " . $id);
                        $tramite = obtenResultado($tramite_BD);

                        $id = $tramite["id"];
                        $fechaAlta = $tramite["fechaAlta"];
                        $status = $tramite["status"];
                        $folioARCA = $tramite["folioARCA"];
                        $idConcesionario = $tramite["idConcesionario"];
                        $idUsuario = $tramite["idUsuario"];
                        $idMensajero = $tramite["idMensajero"];
                        $comentariosARCA = $tramite["comentariosARCA"];
                        $comentariosConcesionario = $tramite["comentariosConcesionario"];

                        $vehiculo_marca = $tramite["vehiculo_marca"];
                        $vehiculo_modelo = $tramite["vehiculo_modelo"];
                        $vehiculo_ano = $tramite["vehiculo_ano"];
                        $vehiculo_color = $tramite["vehiculo_color"];
                        $vehiculo_vin = $tramite["vehiculo_vin"];
                        $cliente_nombre = $tramite["cliente_nombre"];
                        $cliente_apellidoPaterno = $tramite["cliente_apellidoPaterno"];
                        $cliente_apellidoMaterno = $tramite["cliente_apellidoMaterno"];
                        $cliente_correoElectronico = $tramite["cliente_correoElectronico"];
                        $archivo_expediente = $tramite["archivo_expediente"];

                        $fechaRecepcionCorreoICV = $tramite["fechaRecepcionCorreoICV"];
                        $comentariosICV = $tramite["comentariosICV"];
                        $archivo_solicitudICV = $tramite["archivo_solicitudICV"];
                        $ejecutivoICV = $tramite["ejecutivoICV"];
                        $folioICV = $tramite["folioICV"];

                        $bitacora_BD = consulta($conexion, "SELECT * FROM tramite_bitacora t WHERE t.id = (SELECT MAX(l.id) FROM tramite_bitacora l where l.idTramite = " . $id . ")");
                        $bitacora = obtenResultado($bitacora_BD);

                        
                        $inicioFecha = strtotime($bitacora["fechaRegistro"]);
                        $finFecha = strtotime($fechaActual);
                        $diferenciaSegundos = $finFecha - $inicioFecha;

                         consulta($conexion, "INSERT INTO tramite_bitacora ("
                                    . "idTramite"
                                    . ", idUsuario"
                                    . ", fechaRegistro"
                                    . ", status"
                                    . ", tiempoTranscurrido"
                                . ") VALUES ("
                                    . $id
                                    .", " . $usuario_id
                                    . ", '" . $fechaActual . "'"
                                    . ", '" . $status . "'"
                                    . ", " . $diferenciaSegundos
                                . ")");

                        //Envio de correos de acuerdo al status

                        $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $idConcesionario);
                        $concesionario = obtenResultado($concesionario_BD);

                        $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE id = " . $idMensajero);
                        $mensajero = obtenResultado($mensajero_BD);

                        $usuarioCapturo_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $idUsuario);
                        $usuarioCapturo = obtenResultado($usuarioCapturo_BD);

                        
                        if($accion == 'Devuelto a la agencia'){

                            $titulo = "Actualización de trámite";
                            $mensajeCorreo = "Se ha actualizado la solicitud de trámite con los siguientes datos:"
                                     . "<br/>Id: ". $id 
                                     //. "<br/>Agencia: ". $concesionario["nombreComercial"]
                                     //. "<br/>Usuario capturó: " . $usuario_nombre
                                     //. "<br/> Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                     //. "<br/> Comentarios agencia: ". $comentariosConcesionario
                                     . "<br/> Comentarios ARCA: ". $comentariosARCA
                                     . "<br/> Marca: ". $vehiculo_marca
                                     . "<br/> Modelo: ". $vehiculo_modelo
                                     . "<br/> Año: ". $vehiculo_ano
                                     . "<br/> Color: ". $vehiculo_color
                                     . "<br/> VIN: ". $vehiculo_vin
                                     . "<br/> Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                     . "<br/> Correo Cliente: ". $cliente_correoElectronico
                                 ;
                            $correoNotificar = $usuarioCapturo["correoElectronico"];
                        }

                        if($accion == 'Reenviado a ARCA'){

                            $correoNotificacion_BD = consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_reenviarTramiteARCA_correosARCA'");
                            $correoNotificacion = obtenResultado($correoNotificacion_BD);

                            $titulo = "Actualización de trámite";
                            $mensajeCorreo = "Se ha actualizado la solicitud de trámite con los siguientes datos:"
                                     . "<br/>Id: ". $id 
                                     . "<br/>Agencia: ". $concesionario["nombreComercial"]
                                     . "<br/>Usuario capturó: " . $usuario_nombre
                                     . "<br/> Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                     . "<br/> Comentarios agencia: ". $comentariosConcesionario
                                     . "<br/> Comentarios ARCA: ". $comentariosARCA
                                     . "<br/> Marca: ". $vehiculo_marca
                                     . "<br/> Modelo: ". $vehiculo_modelo
                                     . "<br/> Año: ". $vehiculo_ano
                                     . "<br/> Color: ". $vehiculo_color
                                     . "<br/> VIN: ". $vehiculo_vin
                                     . "<br/> Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                     . "<br/> Correo Cliente: ". $cliente_correoElectronico
                                 ;
                            $correoNotificar = $correoNotificacion["valor"];
                        }

                        if($accion == 'A la espera de respuesta de ICV'){

                            $correoNotificacion_BD = consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosICV '");
                            $correoNotificacion = obtenResultado($correoNotificacion_BD);

                            $titulo = "Actualización de trámite";
                            $mensajeCorreo = "Se ha actualizado la solicitud de trámite con los siguientes datos:"
                                     . "<br/>Id: ". $id 
                                     . "<br/>Agencia: ". $concesionario["nombreComercial"]
                                     . "<br/>Usuario capturó: " . $usuario_nombre
                                     . "<br/>Folio ARCA: " . $folioARCA
                                     . "<br/> Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                     . "<br/> Comentarios agencia: ". $comentariosConcesionario
                                     . "<br/> Comentarios ARCA: ". $comentariosARCA
                                     . "<br/> Marca: ". $vehiculo_marca
                                     . "<br/> Modelo: ". $vehiculo_modelo
                                     . "<br/> Año: ". $vehiculo_ano
                                     . "<br/> Color: ". $vehiculo_color
                                     . "<br/> VIN: ". $vehiculo_vin
                                     . "<br/> Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                     . "<br/> Correo Cliente: ". $cliente_correoElectronico
                                 ;
                            $correoNotificar = $correoNotificacion["valor"];
                        }


                        if($accion == 'Aprobado'){

                            $titulo = "Actualización de trámite";
                            $mensajeCorreo = "Se ha aprobado la solicitud de trámite con los siguientes datos:"
                                     . "<br/>Id: ". $id 
                                     //. "<br/>Agencia: ". $concesionario["nombreComercial"]
                                     //. "<br/>Usuario capturó: " . $usuario_nombre
                                     //. "<br/> Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                     //. "<br/> Comentarios agencia: ". $comentariosConcesionario
                                     . "<br/> Comentarios ARCA: ". $comentariosARCA
                                     . "<br/> Marca: ". $vehiculo_marca
                                     . "<br/> Modelo: ". $vehiculo_modelo
                                     . "<br/> Año: ". $vehiculo_ano
                                     . "<br/> Color: ". $vehiculo_color
                                     . "<br/> VIN: ". $vehiculo_vin
                                     . "<br/> Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                     . "<br/> Correo Cliente: ". $cliente_correoElectronico
                                 ;
                            $correoNotificar = $usuarioCapturo["correoElectronico"];
                        }

                        if($accion == 'Rechazado'){

                            $titulo = "Actualización de trámite";
                            $mensajeCorreo = "Se ha rechazado la solicitud de trámite con los siguientes datos:"
                                     . "<br/>Id: ". $id 
                                     //. "<br/>Agencia: ". $concesionario["nombreComercial"]
                                     //. "<br/>Usuario capturó: " . $usuario_nombre
                                     //. "<br/> Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                     //. "<br/> Comentarios agencia: ". $comentariosConcesionario
                                     . "<br/> Comentarios ARCA: ". $comentariosARCA
                                     . "<br/> Comentarios ICV: ". $comentariosICV
                                     . "<br/> Marca: ". $vehiculo_marca
                                     . "<br/> Modelo: ". $vehiculo_modelo
                                     . "<br/> Año: ". $vehiculo_ano
                                     . "<br/> Color: ". $vehiculo_color
                                     . "<br/> VIN: ". $vehiculo_vin
                                     . "<br/> Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                     . "<br/> Correo Cliente: ". $cliente_correoElectronico
                                 ;
                            $correoNotificar = $usuarioCapturo["correoElectronico"];
                        }

                        $archivoCorreo = $constante_rutaTramites . "/" . $id . "/" . $archivo_expediente;

                        $enviaCorreo = enviaCorreoMailjet($correoNotificar,"", $titulo, $mensajeCorreo, $archivoCorreo);

                        $mensaje = "ok - Los cambios han sido guardados";

                        registraEvento("CMS : Actualización de trámite | id = " . $id);
                    }
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $tramite_BD = consulta($conexion, "SELECT * FROM tramite WHERE id = " . $id);
                    $tramite = obtenResultado($tramite_BD);

                    $id = $tramite["id"];
                    $fechaAlta = $tramite["fechaAlta"];
                    $status = $tramite["status"];
                    $folioARCA = $tramite["folioARCA"];
                    $idConcesionario = $tramite["idConcesionario"];
                    $idUsuario = $tramite["idUsuario"];
                    $idMensajero = $tramite["idMensajero"];
                    $comentariosARCA = $tramite["comentariosARCA"];
                    $comentariosConcesionario = $tramite["comentariosConcesionario"];

                    $vehiculo_marca = $tramite["vehiculo_marca"];
                    $vehiculo_modelo = $tramite["vehiculo_modelo"];
                    $vehiculo_ano = $tramite["vehiculo_ano"];
                    $vehiculo_color = $tramite["vehiculo_color"];
                    $vehiculo_vin = $tramite["vehiculo_vin"];
                    $cliente_nombre = $tramite["cliente_nombre"];
                    $cliente_apellidoPaterno = $tramite["cliente_apellidoPaterno"];
                    $cliente_apellidoMaterno = $tramite["cliente_apellidoMaterno"];
                    $cliente_correoElectronico = $tramite["cliente_correoElectronico"];
                    $archivo_expediente = $tramite["archivo_expediente"];

                    $fechaRecepcionCorreoICV = $tramite["fechaRecepcionCorreoICV"];
                    $comentariosICV = $tramite["comentariosICV"];
                    $archivo_solicitudICV = $tramite["archivo_solicitudICV"];
                    $ejecutivoICV = $tramite["ejecutivoICV"];
                    $folioICV = $tramite["folioICV"];
                    
                    $usuarioCapturo_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $idUsuario);
                    $usuarioCapturo = obtenResultado($usuarioCapturo_BD);

                    registraEvento("CMS : Consulta de trámite | id = " . $id);
                } else {
                    $habilitado = 1;
                    $eliminado = 1;


                }
            }
        ?>

        <!-- Preloader -->

        <div id="global-loader">
            <img alt="Cargando..." class="loader-img" src="assets/images/loader.svg" />
        </div>

        <div class="page">
            <div class="page-main">


                <!-- Menu oculto -->


                <div class="main-sidemenu" style="display: none;"> 
                    <div class="slide-left disabled" id="slide-left"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"/></svg></div>

                    <ul class="side-menu" id="contenedor_menu"></ul>

                    <div class="slide-right" id="slide-right"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg></div>
                </div>


                <!-- Contenido -->

                <?php if ($esUsuarioMaster || $usuario_permisoConsultarDelegacionVirtual ) { ?>

                    <div class="main-content app-content mt-0">
                        <div class="side-app">
                            <div class="main-container container-fluid">
                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Detalle de trámite</h1>

                                    <div>
                                        <!--
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="dashboard.html">Inicio</a></li>
                                            <li class="breadcrumb-item"><a href="javascript:void(0)">Catálogos</a></li>
                                            <li class="breadcrumb-item"><a href="javascript:void(0)">Usuarios</a></li>
                                            <li aria-current="page" class="breadcrumb-item active">Detalle de usuario</li>
                                        </ol>
                                        -->
                                    </div>
                                </div>
                                <form autocomplete="off" enctype="multipart/form-data" id="formulario" method="post">

                                    <input id="campo_esSubmit" name="esSubmit" type="hidden" value="1" />

                                    <div class="alert" id="contenedor_mensaje">
                                        <span></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5><strong>Información de control</strong></h5>

                                                    <hr />

                                                    <div class="row">
                                                        <?php if(!estaVacio($id)){ ?>
                                                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10 mb-10 hideClonacion">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_id">ID</label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_id" name="id" type="text" value="<?php echo $id; ?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10 hideClonacion">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_fechaAlta">Fecha y hora de alta</label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_fechaAlta" name="fechaAlta" type="text" value="<?php echo $fechaAlta; ?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10 hideClonacion">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_status">Estatus</label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_status" name="status" type="text" value="<?php echo $status; ?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10 hideClonacion">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_folioARCA">Folio ARCA</label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_folioARCA" name="folioARCA" type="text" value="<?php echo $folioARCA; ?>" <?php echo $esUsuarioOperador ? "readonly" : "";?>/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_idConcesionario">Agencia <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_idConcesionario" name="idConcesionario">
                                                                                <option <?php echo estaVacio($idConcesionario) ? "selected" : "" ?> value="">Seleccione</option>
                                                                                <?php
                                                                                    $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE habilitado = 1 and eliminado = 0 ORDER BY nombreComercial");

                                                                                    while ($concesionarioBD = obtenResultado($concesionarios_BD)) {
                                                                                        echo "<option " . (!estaVacio($idConcesionario) && $idConcesionario == $concesionarioBD["id"] ? "selected" : "") . " value='" . $concesionarioBD["id"] . "'>" . $concesionarioBD["nombreComercial"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php if(!estaVacio($id)){ ?>
                                                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10 hideClonacion">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_idUsuario">Usuario capturó <span class="text-danger">*</span></label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_idUsuario" name="idUsuario" type="text" value="<?php echo $usuarioCapturo["nombre"]; ?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }?>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_idMensajero">Mensajero <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_idMensajero" name="idMensajero">
                                                                                <option <?php echo estaVacio($idMensajero) ? "selected" : "" ?> value="">Seleccione</option>
                                                                                <?php
                                                                                    $mensajeros_BD = consulta($conexion, "SELECT * FROM mensajero WHERE habilitado = 1 and eliminado = 0 ORDER BY nombre, apellidoPaterno, apellidoMaterno");

                                                                                    while ($mensajeroBD = obtenResultado($mensajeros_BD)) {
                                                                                        echo "<option " . (!estaVacio($idMensajero) && $idMensajero == $mensajeroBD["id"] ? "selected" : "") . " value='" . $mensajeroBD["id"] . "'>" . $mensajeroBD["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 col-sm-12 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-20">
                                                                        <label class="form-label col-md-1" for="campo_comentariosConcesionario">Comentarios Agencia </label>
                                                                        <div class="col-md-11">
                                                                            <textarea class="form-control" id="campo_comentariosConcesionario" name="comentariosConcesionario" rows="2"><?php echo $comentariosConcesionario ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php if(!estaVacio($id)){ ?>
                                                            <div class="col-md-12 col-sm-12 col-xs-12 mb-10 hideClonacion">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-20">
                                                                            <label class="form-label col-md-1" for="campo_comentariosARCA">Comentarios ARCA </label>
                                                                            <div class="col-md-11">
                                                                                <textarea class="form-control" id="campo_comentariosARCA" name="comentariosARCA" rows="2" <?php echo $esUsuarioOperador ? "readonly" : "";?>><?php echo $comentariosARCA ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5><strong>Datos del vehículo y cliente</strong></h5>

                                                    <hr />

                                                    <div class="row">

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_marca">Marca <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="select_vehiculo_marca" name="vehiculo_marca">
                                                                                
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_modelo">Modelo <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="select_vehiculo_modelo" name="vehiculo_modelo">
                                                                                
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_ano">Año <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="select_vehiculo_ano" name="vehiculo_ano">
                                                                                
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_color">Color <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_vehiculo_color" name="vehiculo_color" type="text" value="<?php echo $vehiculo_color; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_vin">VIN <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_vehiculo_vin" name="vehiculo_vin" type="text" value="<?php echo $vehiculo_vin; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_cliente_nombre">Nombre(s) <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_cliente_nombre" name="cliente_nombre" type="text" value="<?php echo $cliente_nombre; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_cliente_apellidoPaterno">Apellido Paterno <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_cliente_apellidoPaterno" name="cliente_apellidoPaterno" type="text" value="<?php echo $cliente_apellidoPaterno; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_cliente_apellidoMaterno">Apellido Materno <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_cliente_apellidoMaterno" name="cliente_apellidoMaterno" type="text" value="<?php echo $cliente_apellidoMaterno; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_cliente_correoElectronico">Correo electrónico del cliente <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_cliente_correoElectronico" name="cliente_correoElectronico" type="text" value="<?php echo $cliente_correoElectronico; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label mb-10">PDF de expediente <span class="text-danger">*</span></label>

                                                                        <div>
                                                                            <input name="archivo_expediente" type="file" />
                                                                            <br />
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="panel panel-success card-view">
                                                                                        <div class="panel-wrapper in">
                                                                                            <div class="panel-body">
                                                                                                <ul class="chat-list-wrap">
                                                                                                    <li class="chat-list">
                                                                                                        <div class="chat-body">
                                                                                                        <?php
                                                                                                            if (!estaVacio($archivo_expediente)) {
                                                                                                                echo "<div class='chat-data' id='contenedor_archivo_expediente'>";
                                                                                                                echo "<img class='user-img' src='" . $constante_urlTramites . "/" . $id . "/" . $archivo_expediente . "' />";

                                                                                                                echo "<div class='user-data'>";
                                                                                                                echo "<span class='name block'>" . $archivo_expediente . "</span>";
                                                                                                                echo "<span class='time block txt-grey'>";
                                                                                                                echo "<a download href='" . $constante_urlTramites . "/" . $id . "/" . $archivo_expediente . "'>Descargar</a>";
                                                                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                echo "<a href='javascript:eliminaPdfExpediente(" . $id . ")'>Eliminar</a>";
                                                                                                                echo "</span>";
                                                                                                                echo "</div>";
                                                                                                                echo "<div class='clearfix'></div>";
                                                                                                                echo "</div>";
                                                                                                            }
                                                                                                        ?>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if(!estaVacio($id)){ ?>
                                        <div class="row" id="seccionICV">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5><strong>Datos ICV</strong></h5>

                                                        <hr />

                                                        <div class="row">

                                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_folioICV">Folio asignado por el ICV</label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_folioICV" name="folioICV" type="text" value="<?php echo $folioICV; ?>" <?php echo $esUsuarioOperador ? "readonly" : "";?>/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_fechaRecepcionCorreoICV">Fecha y hora en la que se recibió correo de ICV</label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_fechaRecepcionCorreoICV" name="fechaRecepcionCorreoICV" type="datetime-local" value="<?php echo $fechaRecepcionCorreoICV; ?>" <?php echo $esUsuarioOperador ? "readonly" : "";?>/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_ejecutivoICV">Ejecutivo ICV <span class="text-danger">*</span></label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_ejecutivoICV" name="ejecutivoICV" type="text" value="<?php echo $ejecutivoICV; ?>" <?php echo $esUsuarioOperador ? "readonly" : "";?>/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 col-sm-12 col-xs-12 mb-10">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-20">
                                                                            <label class="form-label col-md-1" for="campo_comentariosICV">Comentarios ICV </label>
                                                                            <div class="col-md-11">
                                                                                <textarea class="form-control" id="campo_comentariosICV" name="comentariosICV" rows="2" <?php echo $esUsuarioOperador ? "readonly" : "";?>><?php echo $comentariosICV ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            

                                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label class="control-label mb-10">PDF de la solicitud de ICV</label>

                                                                            <div>
                                                                                <input name="archivo_solicitudICV" type="file" <?php echo $esUsuarioOperador ? "disabled" : "";?>/>
                                                                                <br />
                                                                                <div class="row">
                                                                                    <div class="col-sm-12">
                                                                                        <div class="panel panel-success card-view">
                                                                                            <div class="panel-wrapper in">
                                                                                                <div class="panel-body">
                                                                                                    <ul class="chat-list-wrap">
                                                                                                        <li class="chat-list">
                                                                                                            <div class="chat-body">
                                                                                                            <?php
                                                                                                                if (!estaVacio($archivo_solicitudICV)) {
                                                                                                                    echo "<div class='chat-data' id='contenedor_archivo_solicitudICV'>";
                                                                                                                    echo "<img class='user-img' src='" . $constante_urlTramites . "/" . $id . "/" . $archivo_solicitudICV . "' />";

                                                                                                                    echo "<div class='user-data'>";
                                                                                                                    echo "<span class='name block'>" . $archivo_solicitudICV . "</span>";
                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                    echo "<a download href='" . $constante_urlTramites . "/" . $id . "/" . $archivo_solicitudICV . "'>Descargar</a>";
                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                    echo "<a href='javascript:eliminaArchivoIne(" . $id . ")'>Eliminar</a>";
                                                                                                                    echo "</span>";
                                                                                                                    echo "</div>";
                                                                                                                    echo "<div class='clearfix'></div>";
                                                                                                                    echo "</div>";
                                                                                                                }
                                                                                                            ?>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                    </ul>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="seccionBitacora">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5><strong>Bitácora</strong></h5>

                                                        <hr />

                                                        <div class="row row-sm">
                                                            <div class="col-lg-12">
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered text-nowrap key-buttons border-bottom" id="tabla_resultados">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="border-bottom-0">Fecha y hora</th>
                                                                                <th class="border-bottom-0">Estatus</th>
                                                                                <th class="border-bottom-0">Tiempo transcurrido</th>
                                                                            </tr>
                                                                        </thead>

                                                                        <tbody id="contenedor_resultados">
                                                                            <?php
                                                                                    
                                                                                    // Consulta base de datos

                                                                                    $bitacora_BD = consulta($conexion, "SELECT * FROM tramite_bitacora u WHERE u.idTramite = " . $id . " ORDER BY id");

                                                                                    while ($bitacora = obtenResultado($bitacora_BD)) {
                                                                                        $init = $bitacora["tiempoTranscurrido"];
                                                                                        $hours = floor($init / 3600);
                                                                                        $minutes = floor(($init / 60) % 60);
                                                                                        $seconds = $init % 60;
                                                                                        echo "<tr>";
                                                                                        echo "<td>" . $bitacora["fechaRegistro"] . "</td>";
                                                                                        echo "<td>" . $bitacora["status"] . "</td>";
                                                                                        echo "<td>" . $hours . " horas, " . $minutes . " minutos, " . $seconds . " segundos</td>";
                                                                                        echo "</tr>";
                                                                                    }
                                                                                ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?> 

                                    <div class="row mt-6">
                                        <div class="col-md-8 col-sm-6" id="contenedor_botonesIzquierdos">
                                            <?php if((!estaVacio($id)) && $eliminado == 0){?>
                                                <div class="form-group">
                                                    <a class="btn btn-danger mb-3" href="javascript:eliminaTramite(<?php echo  $id ?>)" id="boton_eliminar">Eliminar</a>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="col-md-4 col-sm-6" id="contenedor_botonesDerechos">
                                            <div class="form-group">
                                                <input id="campo_accion" name="accion" type="hidden" value="<?php echo $accion; ?>" />
                                                <a class="btn btn-default mb-3" href="javascript:void(0)" id="boton_cerrar">Cerrar</a>

                                                <?php if((!estaVacio($id)) && ($esUsuarioMaster || $usuario_permisoEditarDelegacionVirtual)){?>

                                                    <?php if($status == "Rechazado"){?> 
                                                        <button class="btn btn-default mb-3" id="boton_clonar_tramite" onclick="clonar()" type="submit" >Clonar trámite</button>
                                                    <?php }?>

                                                    <?php if($status == "A la espera de respuesta de ICV"){?> 
                                                        <button class="btn btn-default mb-3" id="boton_rechazado" onclick="rechazado()" type="submit" >Rechazado</button>
                                                        <button class="btn btn-default mb-3" id="boton_aprobado" onclick="aprobado()" type="submit" >Aprobado</button>
                                                    <?php }?>

                                                    <?php if(($status == 'Enviado a ARCA') || ($status == "Reenviado a ARCA")){?> 
                                                        <button class="btn btn-default mb-3" id="boton_enviar_icv" onclick="enviarICV()" type="submit" >Enviar a ICV</button>
                                                    <?php }?>

                                                    <?php if($status == "Devuelto a la agencia"){?> 
                                                        <button class="btn btn-default mb-3" id="boton_reenviar_arca" onclick="reenviarArca()" type="submit" >Reenviar a ARCA</button>
                                                    <?php }?>

                                                    <?php if(($status == 'Enviado a ARCA') || ($status == "Reenviado a ARCA")){?> 
                                                        <button class="btn btn-default mb-3" id="boton_regresar_agencia" onclick="regresarAAgencia()" type="submit" >Regresar a Agencia</button>
                                                    <?php }?>

                                                <?php } ?>
                                                <button class="btn btn-success mb-3" id="boton_guardar" onclick="return confirm('¿Desea dar de alta este trámite?');" type="submit" >Generar trámite</button>
                                                
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    } else {
                        registraEvento("CMS : Consulta de trámite bloqueada | id = " . $id);
                        muestraBloqueo();
                    }
                ?>
            </div>
        </div>


        <?php include("socialware/php/estructura/plugins.php"); ?>

        <?php include("socialware/php/estructura/scripts.php"); ?>


        <!-- Scripts -->


        <script>
            $(function() {
                var mensaje = "<?php echo $mensaje ?>";

                if (mensaje !== "") {
                    $("#contenedor_mensaje").hide();
                    $("#contenedor_mensaje").removeClass("alert-success");
                    $("#contenedor_mensaje").removeClass("alert-danger");

                    if (mensaje.startsWith("ok - ")) {
                        $("#contenedor_mensaje span").html(mensaje.substring(5));
                        $("#contenedor_mensaje").addClass("alert-success");
                        $("#contenedor_mensaje").show();
                    } else {
                        $("#contenedor_mensaje span").html(mensaje);
                        $("#contenedor_mensaje").addClass("alert-danger");
                        $("#contenedor_mensaje").show();
                    }

                    $("body").animate({ scrollTop: 0 }, 'slow');
                }

                $(".bs-switch").bootstrapSwitch({
                    handleWidth: 110,
                    labelWidth: 110
                });
/*
                $(".js-switch").each(function() {
                    new Switchery($(this)[0], $(this).data());
                });

*/
                cargaMarcas();


                // Inicializa tabla de resultados

                var tablaResultados = $("#tabla_resultados").DataTable({
                    dom: "rtip",
                    "bDestroy": true,
                    //pageLength: (paginacion ? "10" : "0"),
                    language: {
                        scrollX: "100%",
                        sSearch: "",

                        emptyTable: "No hay información",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ resultados",
                        infoEmpty: "Mostrando 0 a 0 de 0 resultados",
                        infoFiltered: "(Filtrado de _MAX_ total resultados)",
                        lengthMenu: "Mostrar _MENU_ resultados",
                        loadingRecords: "Cargando...",
                        processing: "Procesando...",
                        searchPlaceholder: "Bucar...",
                        zeroRecords: "Sin resultados encontrados",
                        paginate: {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        }
                    },
                    retrieve: true
                });

            });


            // Regresa a la interfaz de origen


            $(".link_origen").click(function() {
                $("#formulario_origen").submit();
            });


            function cargaMarcas(){
                $.ajax({
                    url: "socialware/php/ajax/cargaMarcas.php",
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var marca = '<?php echo $vehiculo_marca; ?>' ;

                    var json = jQuery.parseJSON(resultado);
                    $.each(json.data,function(i,nodo){
                        if(marca == nodo.name){
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        }else{
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#select_vehiculo_marca").append(contenido);
                    cargaModelos();
                });
            }

            function cargaModelos(){
                var idMarca = $("#select_vehiculo_marca option:selected").attr("data-id");
                $.ajax({
                    url: "socialware/php/ajax/cargaModelos.php?marca=" + idMarca,
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var modelo = '<?php echo $vehiculo_modelo; ?>' ;

                    var json = jQuery.parseJSON(resultado);
                    $.each(json.data,function(i,nodo){
                        if(modelo == nodo.name){
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        }else{
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#select_vehiculo_modelo").html("");
                    $("#select_vehiculo_ano").html("");

                    $("#select_vehiculo_modelo").append(contenido);
                    cargaAnos();
                });
            }

            function cargaAnos(){
                var idMarca = $("#select_vehiculo_marca option:selected").attr("data-id");
                var idModelo = $("#select_vehiculo_modelo option:selected").attr("data-id");

                $.ajax({
                    url: "socialware/php/ajax/cargaAnos.php?marca=" + idMarca + "&modelo=" + idModelo,
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var ano = '<?php echo $vehiculo_ano; ?>' ;

                    var json = jQuery.parseJSON(resultado);
                    $.each(json.data,function(i,nodo){
                        if(ano == nodo.name){
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        }else{
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#select_vehiculo_ano").html("");

                    $("#select_vehiculo_ano").append(contenido);
                });
            }

            $("#select_vehiculo_marca").on("change", function(){
                cargaModelos();
            });

            $("#select_vehiculo_modelo").on("change", function(){
                cargaAnos();
            });



             // Borra PDF expediente
            
            function eliminaPdfExpediente(idTramite) {
                if (confirm("Al continuar se eliminará el archivo, ¿desea proceder?")) {
                    $.ajax({
                        data: {
                            idTramite: idTramite
                        },
                        type: "post",
                        url: "socialware/php/ajax/eliminaPdfExpedienteTramite.php",
                        success: function(resultado) {
                            if (resultado === "ok") {
                                $("#contenedor_archivo_expediente").hide();
                            }
                        }
                    });
                }
            }

            // Elimina tramite
            
            function eliminaTramite(idTramite) {
                if (confirm("Al continuar se eliminará el trámite, ¿desea proceder?")) {
                    $.ajax({
                        data: {
                            idTramite: idTramite
                        },
                        type: "post",
                        url: "socialware/php/ajax/eliminaTramite.php",
                        success: function(resultado) {
                            if (resultado === "ok") {
                                alert("El trámite fue eliminado correctamente.");
                                $("#boton_cerrar").click();
                            }
                        }
                    });
                }
            }

            //Funciones por estatus

            function regresarAAgencia(){
                event.preventDefault();
                if(confirm("¿Desea reenviar a la agencia este trámite?")){
                    $("#campo_accion").val("Devuelto a la agencia");
                    $('#formulario').submit();
                }
            }

            function reenviarArca(){
                event.preventDefault();
                if(confirm("¿Desea reenviar a ARCA este trámite?")){
                    $("#campo_accion").val("Reenviado a ARCA");
                    $('#formulario').submit();
                }
            }

            function enviarICV(){
                event.preventDefault();
                if(confirm("¿Desea enviar a ICV este trámite?")){
                    $("#campo_accion").val("A la espera de respuesta de ICV");
                    $('#formulario').submit();
                }
            }

            function aprobado(){
                event.preventDefault();
                if(confirm("¿Desea aprobar este trámite?")){
                    $("#campo_accion").val("Aprobado");
                    $('#formulario').submit();
                }
            }

            function rechazado(){
                event.preventDefault();
                if(confirm("¿Desea rechazar este trámite?")){
                    $("#campo_accion").val("Rechazado");
                    $('#formulario').submit();
                }
            }

            function clonar(){
                event.preventDefault();
                if(confirm("¿Desea clonar este trámite?")){
                    $("#campo_accion").val("Clonar");
                    $("#campo_id").val("");
                    $("#campo_comentariosConcesionario").val("");
                    $(".hideClonacion").hide();
                    $("#contenedor_archivo_expediente").hide();


                    $("#boton_guardar").show();
                    $("#boton_clonar_tramite").hide();
                    $("#boton_rechazado").hide();
                    $("#boton_aprobado").hide();
                    $("#seccionBitacora").hide();
                    $("#seccionICV").hide();


                }
            }

            var id = <?php echo estaVacio($id) ? 0 : $id; ?>;
            if(id > 0){
                $("#boton_guardar").hide();
            }


        </script>
    </body>
</html>
