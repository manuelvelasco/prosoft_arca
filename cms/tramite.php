<?php include("socialware/php/comunes/manejaSesion.php"); ?>
<?php // error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED); ?>


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

            #campo_vehiculo_vin_error {
                display: none;
            }
        </style>
    </head>


    <body class="app ltr">
        <?php

            // Obtiene parametros de request

            $esSubmit = sanitiza($conexion, filter_input(INPUT_POST, "esSubmit"));
            $id = sanitiza($conexion, filter_input(INPUT_GET, "id"));

            if ($esSubmit == 1) {
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
            $vehiculo_placa = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_placa"));
            $cliente_nombre = sanitiza($conexion, filter_input(INPUT_POST, "cliente_nombre"));
            $cliente_apellidoPaterno = sanitiza($conexion, filter_input(INPUT_POST, "cliente_apellidoPaterno"));
            $cliente_apellidoMaterno = sanitiza($conexion, filter_input(INPUT_POST, "cliente_apellidoMaterno"));
            $cliente_correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "cliente_correoElectronico"));

            $fechaRecepcionCorreoICV = sanitiza($conexion, filter_input(INPUT_POST, "fechaRecepcionCorreoICV"));
            $comentariosICV = sanitiza($conexion, filter_input(INPUT_POST, "comentariosICV"));
            $ejecutivoICV = sanitiza($conexion, filter_input(INPUT_POST, "ejecutivoICV"));
            $folioICV = sanitiza($conexion, filter_input(INPUT_POST, "folioICV"));

            $accion = sanitiza($conexion, filter_input(INPUT_POST, "accion"));

            if ($accion == "Clonar") {
                $id = "";
                $status = "Enviado a ARCA";
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

                if (estaVacio($comentariosConcesionario)) {
                    $mensaje .= "* Comentarios Agencia<br />";
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

                if ((!estaVacio($id)) && ($accion == "Devuelto a la agencia")) {
                    if(estaVacio($comentariosARCA)){
                        $mensaje .= "* Comentarios ARCA<br />";
                    }
                }

                if ((!estaVacio($id)) && ($accion == "A la espera de respuesta de ICV")) {
                    if (estaVacio($folioARCA)) {
                        $mensaje .= "* Folio ARCA<br />";
                    }
                }

                if ((!estaVacio($id)) && (($accion == "Aprobado") || ($accion == "Rechazado"))) {
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

                if ((!estaVacio($id)) && ($accion == "Aprobado")) {
                    if (estaVacio($folioICV)) {
                        $mensaje .= "* Folio ICV<br />";
                    }

                    if (!(isset($_FILES['archivo_solicitudICV']) && $_FILES['archivo_solicitudICV']['error'] === UPLOAD_ERR_OK)) {
                        $mensaje .= "* PDF de la solicitud de ICV <br />";
                    }
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;

                    if (!estaVacio($id)) {
                        $tramite_BD = consulta($conexion, "SELECT * FROM tramite WHERE id = " . $id);
                        $tramite = obtenResultado($tramite_BD);

                        $archivo_expediente = $tramite["archivo_expediente"];
                        $archivo_comprobantePago = $tramite["archivo_comprobantePago"];
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

                            // Genera folio ARCA

                            $concesionario_numeroSocio = obtenResultado(consulta($conexion, "SELECT numeroSocio FROM concesionario WHERE id = " . $idConcesionario))["numeroSocio"];
                            $concesionario_consecutivoDelegacionVirtual = obtenResultado(consulta($conexion, "SELECT consecutivoDelegacionVirtual FROM concesionario WHERE id = " . $idConcesionario))["consecutivoDelegacionVirtual"];

                            if (estaVacio($concesionario_numeroSocio) || estaVacio($concesionario_consecutivoDelegacionVirtual)) {
                                $mensaje = "El concesionario no cuenta con los valores necesarios para generar trámites";
                            } else {
                                //$folioARCA = "A" . $concesionario_numeroSocio . date("YmdHi") . $concesionario_consecutivoDelegacionVirtual;
                                $folioARCA = "A" . str_pad($concesionario_numeroSocio, 3, "0") . date("dmy") . str_pad($concesionario_consecutivoDelegacionVirtual, 4, "0", STR_PAD_LEFT);

                                // Registra tramite

                                consulta($conexion, "INSERT INTO tramite ("
                                        . "idConcesionario"
                                        . ", idMensajero"
                                        . ", folioARCA"
                                        . ", vehiculo_marca"
                                        . ", vehiculo_modelo"
                                        . ", vehiculo_ano"
                                        . ", vehiculo_color"
                                        . ", vehiculo_vin"
                                        . ", vehiculo_placa"
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
                                        . ", " . (estaVacio($folioARCA) ? "NULL" : "'" . $folioARCA . "'")
                                        . ", '" . $vehiculo_marca . "'"
                                        . ", '" . $vehiculo_modelo . "'"
                                        . ", '" . $vehiculo_ano . "'"
                                        . ", '" . $vehiculo_color . "'"
                                        . ", '" . $vehiculo_vin . "'"
                                        . ", " . (estaVacio($vehiculo_placa) ? "NULL" : "'" . $vehiculo_placa . "'")
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
                                $vehiculo_placa = $tramite["vehiculo_placa"];
                                $cliente_nombre = $tramite["cliente_nombre"];
                                $cliente_apellidoPaterno = $tramite["cliente_apellidoPaterno"];
                                $cliente_apellidoMaterno = $tramite["cliente_apellidoMaterno"];
                                $cliente_correoElectronico = $tramite["cliente_correoElectronico"];
                                $archivo_expediente = $tramite["archivo_expediente"];
                                $archivo_comprobantePago = $tramite["archivo_comprobantePago"];

                                $fechaRecepcionCorreoICV = $tramite["fechaRecepcionCorreoICV"];
                                $comentariosICV = $tramite["comentariosICV"];
                                $archivo_solicitudICV = $tramite["archivo_solicitudICV"];
                                $ejecutivoICV = $tramite["ejecutivoICV"];
                                $folioICV = $tramite["folioICV"];

                                // Actualiza consecutivo de concesionario

                                consulta($conexion, "UPDATE concesionario SET consecutivoDelegacionVirtual = consecutivoDelegacionVirtual + 1 WHERE id = " . $idConcesionario);

                                // Inicia bitacora de tramite

                                $usuarioCapturo_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $idUsuario);
                                $usuarioCapturo = obtenResultado($usuarioCapturo_BD);

                                consulta($conexion, "INSERT INTO tramite_bitacora ("
                                        . "idTramite"
                                        . ", idUsuario"
                                        . ", status"
                                        . ", fechaInicial"
                                    . ") VALUES ("
                                        . $id
                                        . ", " . $usuario_id
                                        . ", 'Enviado a ARCA'"
                                        . ", '" . $fechaActual . "'"
                                    . ")");

                                // Carga pdf expediente

                                if (isset($_FILES["archivo_expediente"])) {
                                    try {
                                        $archivo = $_FILES["archivo_expediente"];

                                        if ($archivo["size"] > 0) {
                                            $nombreEstandarizado = $id . "_expediente_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                            $archivoDestino = $constante_rutaTramites . "/" . $id . "/" . $nombreEstandarizado;

                                            if (!file_exists($constante_rutaTramites . "/" . $id)) {
                                                //mkdir($constante_rutaTramites . "/" . $id, 0755, true);
                                                mkdir($constante_rutaTramites . "/" . $id);
                                            }

                                            move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                            consulta($conexion, "UPDATE tramite SET archivo_expediente = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                            $archivo_expediente = $nombreEstandarizado;
                                        }
                                    } catch (Exception $e) {
                                    }
                                }

                                // Envia correo

                                $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $idConcesionario);
                                $concesionario = obtenResultado($concesionario_BD);

                                $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE id = " . $idMensajero);
                                $mensajero = obtenResultado($mensajero_BD);

                                $correoNotificacion_BD = consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_generarTramite_correosARCA'");
                                $correoNotificacion = obtenResultado($correoNotificacion_BD);

                                $titulo = "Nueva solicitud de trámite";
                                $mensajeCorreo = "Se ha recibido una nueva solicitud de trámite con los siguientes datos:"
                                    . "<br />Id: ". $id 
                                    . "<br />Agencia: ". $concesionario["nombreComercial"]
                                    . "<br />Usuario capturó: " . $usuario_nombre
                                    . "<br />Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                    . "<br />Comentarios agencia: ". $comentariosConcesionario
                                    . "<br />Marca: ". $vehiculo_marca
                                    . "<br />Modelo: ". $vehiculo_modelo
                                    . "<br />Año: ". $vehiculo_ano
                                    . "<br />Color: ". $vehiculo_color
                                    . "<br />VIN: ". $vehiculo_vin
                                    . "<br />Número de placa: " . $vehiculo_placa
                                    . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                    . "<br />Correo Cliente: ". $cliente_correoElectronico
                                    . "<br />";

                                //$enviaCorreo = enviaCorreoMailjet($correoNotificacion["valor"],"", $titulo, $mensajeCorreo, $archivoDestino);
                                $enviaCorreo = enviaCorreoMailjet($correoNotificacion["valor"],"", $titulo, $mensajeCorreo, $archivoDestino, "delegacionvirtual@seminuevosarca.com.mx");

                                $mensaje = "ok - El tramite ha sido registrado";

                                $accion = "";

                                registraEvento("CMS : Alta de trámite | id = " . $id);
                            }
                        }
                    } else {

                        // Es actualizacion

                        if (estaVacio($folioARCA)) {
                            $mensaje .= "Proporcione el Folio ARCA";
                        } else {
                            if (isset($_FILES["archivo_comprobantePago"])) {
                                try {
                                    $archivo = $_FILES["archivo_comprobantePago"];

                                    if ($archivo["size"] > 0) {
                                        $nombreEstandarizado = $id . "_comprobantePago_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                        $archivoDestino = $constante_rutaTramites . "/" . $id . "/" . $nombreEstandarizado;

                                        if (!file_exists($constante_rutaTramites . "/" . $id)) {
                                            //mkdir($constante_rutaTramites . "/" . $id, 0755, true);
                                            mkdir($constante_rutaTramites . "/" . $id);
                                        }

                                        move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                        consulta($conexion, "UPDATE tramite SET archivo_comprobantePago = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                        $archivo_comprobantePago = $nombreEstandarizado;
                                    }

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
                                    $vehiculo_placa = $tramite["vehiculo_placa"];
                                    $cliente_nombre = $tramite["cliente_nombre"];
                                    $cliente_apellidoPaterno = $tramite["cliente_apellidoPaterno"];
                                    $cliente_apellidoMaterno = $tramite["cliente_apellidoMaterno"];
                                    $cliente_correoElectronico = $tramite["cliente_correoElectronico"];
                                    $archivo_expediente = $tramite["archivo_expediente"];
                                    $archivo_comprobantePago = $tramite["archivo_comprobantePago"];

                                    $fechaRecepcionCorreoICV = $tramite["fechaRecepcionCorreoICV"];
                                    $comentariosICV = $tramite["comentariosICV"];
                                    $archivo_solicitudICV = $tramite["archivo_solicitudICV"];
                                    $ejecutivoICV = $tramite["ejecutivoICV"];
                                    $folioICV = $tramite["folioICV"];

                                    $usuarioCapturo_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $idUsuario);
                                    $usuarioCapturo = obtenResultado($usuarioCapturo_BD);

                                    // Envia correo de notificacion

                                    $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $idConcesionario);
                                    $concesionario = obtenResultado($concesionario_BD);

                                    $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE id = " . $idMensajero);
                                    $mensajero = obtenResultado($mensajero_BD);

                                    $correoNotificar = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_cargarComprobantePago_correosARCA'"))["valor"];

                                    $titulo = "Carga de comprobante de pago";
                                    $mensajeCorreo = "Se ha cargado el comprobante de pago del trámite con los siguientes datos:"
                                        . "<br /><br />Id: ". $id 
                                        . "<br />Agencia: ". $concesionario["nombreComercial"]
                                        . "<br />Usuario capturó: " . $usuario_nombre
                                        . "<br />Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                        . "<br />Comentarios agencia: ". $comentariosConcesionario
                                        . "<br />Comentarios ARCA: ". $comentariosARCA
                                        . "<br />Marca: ". $vehiculo_marca
                                        . "<br />Modelo: ". $vehiculo_modelo
                                        . "<br />Año: ". $vehiculo_ano
                                        . "<br />Color: ". $vehiculo_color
                                        . "<br />VIN: ". $vehiculo_vin
                                        . "<br />Número de placa: " . $vehiculo_placa
                                        . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                        . "<br />Correo Cliente: ". $cliente_correoElectronico
                                        . "<br />";

                                    $archivoCorreo = $constante_rutaTramites . "/" . $id . "/" . $archivo_comprobantePago;

                                    //$enviaCorreo = enviaCorreoMailjet($correoNotificar, null, $titulo, $mensajeCorreo, $archivoCorreo);
                                    $enviaCorreo = enviaCorreoMailjet($correoNotificar, null, $titulo, $mensajeCorreo, $archivoCorreo, "delegacionvirtual@seminuevosarca.com.mx");
                                } catch (Exception $e) {
                                }
                            } else {
                                $statusAnterior = obtenResultado(consulta($conexion, "SELECT status FROM tramite WHERE id = " . $id))["status"];

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
                                    . ", vehiculo_placa = " . (estaVacio($vehiculo_placa) ? "NULL" : "'" . $vehiculo_placa . "'")
                                    . ", cliente_nombre = '" . $cliente_nombre . "'"
                                    . ", cliente_apellidoPaterno = '" . $cliente_apellidoPaterno . "'"
                                    . ", cliente_apellidoMaterno = '" . $cliente_apellidoMaterno . "'"
                                    . ", cliente_correoElectronico = '" . $cliente_correoElectronico . "'"
                                    . ", comentariosConcesionario = '" . $comentariosConcesionario . "'"
                                    . ", comentariosARCA = '" . $comentariosARCA . "'"
                                    . ", comentariosICV = '" . $comentariosICV . "'"
                                    . ", ejecutivoICV = '" . $ejecutivoICV . "'"
                                    . ", fechaRecepcionCorreoICV = " . (estaVacio($fechaRecepcionCorreoICV) ? "NULL" : "'" . $fechaRecepcionCorreoICV . "'")
                                    . " WHERE id = " . $id);

                                if (isset($_FILES["archivo_expediente"])) {
                                    try {
                                        $archivo = $_FILES["archivo_expediente"];

                                        if ($archivo["size"] > 0) {
                                            $nombreEstandarizado = $id . "_expediente_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                            $archivoDestino = $constante_rutaTramites . "/" . $id . "/" . $nombreEstandarizado;

                                            if (!file_exists($constante_rutaTramites . "/" . $id)) {
                                                //mkdir($constante_rutaTramites . "/" . $id, 0755, true);
                                                mkdir($constante_rutaTramites . "/" . $id);
                                            }

                                            move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                            consulta($conexion, "UPDATE tramite SET archivo_expediente = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                            $archivo_expediente = $nombreEstandarizado;
                                        }
                                    } catch (Exception $e) {
                                    }
                                }

                                if (isset($_FILES["archivo_solicitudICV"])) {
                                    try {
                                        $archivo = $_FILES["archivo_solicitudICV"];

                                        if ($archivo["size"] > 0) {
                                            $nombreEstandarizado = $id . "_solicitudICV_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                            $archivoDestino = $constante_rutaTramites . "/" . $id . "/" . $nombreEstandarizado;

                                            if (!file_exists($constante_rutaTramites . "/" . $id)) {
                                                //mkdir($constante_rutaTramites . "/" . $id, 0755, true);
                                                mkdir($constante_rutaTramites . "/" . $id);
                                            }

                                            move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                            consulta($conexion, "UPDATE tramite SET archivo_solicitudICV = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                            $archivo_solicitudICV = $nombreEstandarizado;
                                        }
                                    } catch (Exception $e) {
                                    }
                                }

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
                                $vehiculo_placa = $tramite["vehiculo_placa"];
                                $cliente_nombre = $tramite["cliente_nombre"];
                                $cliente_apellidoPaterno = $tramite["cliente_apellidoPaterno"];
                                $cliente_apellidoMaterno = $tramite["cliente_apellidoMaterno"];
                                $cliente_correoElectronico = $tramite["cliente_correoElectronico"];
                                $archivo_expediente = $tramite["archivo_expediente"];
                                $archivo_comprobantePago = $tramite["archivo_comprobantePago"];

                                $fechaRecepcionCorreoICV = $tramite["fechaRecepcionCorreoICV"];
                                $comentariosICV = $tramite["comentariosICV"];
                                $archivo_solicitudICV = $tramite["archivo_solicitudICV"];
                                $ejecutivoICV = $tramite["ejecutivoICV"];
                                $folioICV = $tramite["folioICV"];

                                if ($status != $statusAnterior) {
                                    $bitacora_BD = consulta($conexion, "SELECT * FROM tramite_bitacora WHERE idTramite = " . $id . " ORDER BY id DESC LIMIT 1");
                                    $bitacora = obtenResultado($bitacora_BD);

                                    consulta($conexion, "UPDATE tramite_bitacora SET fechaFinal = '" . $fechaActual . "' WHERE id = " . $bitacora["id"]);

                                    consulta($conexion, "INSERT INTO tramite_bitacora ("
                                            . "idTramite"
                                            . ", idUsuario"
                                            . ", status"
                                            . ", fechaInicial"
                                        . ") VALUES ("
                                            . $id
                                            . ", " . $usuario_id
                                            . ", '" . $status . "'"
                                            . ", '" . $fechaActual . "'"
                                        . ")");

                                    // Envio de correos de acuerdo al status

                                    $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $idConcesionario);
                                    $concesionario = obtenResultado($concesionario_BD);

                                    $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE id = " . $idMensajero);
                                    $mensajero = obtenResultado($mensajero_BD);

                                    $usuarioCapturo_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $idUsuario);
                                    $usuarioCapturo = obtenResultado($usuarioCapturo_BD);

                                    $correoNotificar = null;
                                    $correoNotificarCopia = null;
                                    $archivoCorreo = null;

                                    switch ($accion) {
                                        case  "Devuelto a la agencia":
                                            $correoNotificar = $usuarioCapturo["correoElectronico"];

                                            $titulo = "Actualización de trámite";
                                            $mensajeCorreo = "Se ha actualizado la solicitud de trámite con los siguientes datos:"
                                                . "<br /><br />Id: ". $id 
                                                . "<br />Comentarios ARCA: ". $comentariosARCA
                                                . "<br />Marca: ". $vehiculo_marca
                                                . "<br />Modelo: ". $vehiculo_modelo
                                                . "<br />Año: ". $vehiculo_ano
                                                . "<br />Color: ". $vehiculo_color
                                                . "<br />VIN: ". $vehiculo_vin
                                                . "<br />Número de placa: " . $vehiculo_placa
                                                . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                                . "<br />Correo Cliente: ". $cliente_correoElectronico
                                                . "<br />";

                                            break;
                                        case  "Reenviado a ARCA":
                                            $correoNotificar = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_reenviarTramiteARCA_correosARCA'"))["valor"];

                                            $titulo = "Actualización de trámite";
                                            $mensajeCorreo = "Se ha actualizado la solicitud de trámite con los siguientes datos:"
                                                . "<br /><br />Id: ". $id 
                                                . "<br />Agencia: ". $concesionario["nombreComercial"]
                                                . "<br />Usuario capturó: " . $usuario_nombre
                                                . "<br />Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                                . "<br />Comentarios agencia: ". $comentariosConcesionario
                                                . "<br />Comentarios ARCA: ". $comentariosARCA
                                                . "<br />Marca: ". $vehiculo_marca
                                                . "<br />Modelo: ". $vehiculo_modelo
                                                . "<br />Año: ". $vehiculo_ano
                                                . "<br />Color: ". $vehiculo_color
                                                . "<br />VIN: ". $vehiculo_vin
                                                . "<br />Número de placa: " . $vehiculo_placa
                                                . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                                . "<br />Correo Cliente: ". $cliente_correoElectronico
                                                . "<br />";

                                            break;
                                        case  "A la espera de respuesta de ICV":
                                            $correoNotificar = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosICV'"))["valor"];
                                            $correoNotificarCopia = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosARCA'"))["valor"];

                                            $titulo = "ARCA - " . $folioARCA . " - " . $concesionario["nombreComercial"] . " - " . trim($cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno);
                                            $mensajeCorreo = "Se ha actualizado la solicitud de trámite con los siguientes datos:"
                                                . "<br /><br />Id: ". $id 
                                                . "<br />Agencia: ". $concesionario["nombreComercial"]
                                                . "<br />Usuario capturó: " . $usuario_nombre
                                                . "<br />Folio ARCA: " . $folioARCA
                                                . "<br />Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                                . "<br />Comentarios agencia: ". $comentariosConcesionario
                                                . "<br />Comentarios ARCA: ". $comentariosARCA
                                                . "<br />Marca: ". $vehiculo_marca
                                                . "<br />Modelo: ". $vehiculo_modelo
                                                . "<br />Año: ". $vehiculo_ano
                                                . "<br />Color: ". $vehiculo_color
                                                . "<br />VIN: ". $vehiculo_vin
                                                . "<br />Número de placa: " . $vehiculo_placa
                                                . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                                . "<br />Correo Cliente: ". $cliente_correoElectronico
                                                . "<br />";

                                            break;
                                        case  "Aprobado":
                                            $correoNotificar = $usuarioCapturo["correoElectronico"];
                                            $archivoCorreo = $constante_rutaTramites . "/" . $id . "/" . $archivo_expediente;

                                            $titulo = "Actualización de trámite";
                                            $mensajeCorreo = "Se ha aprobado la solicitud de trámite con los siguientes datos:"
                                                . "<br />Id: ". $id 
                                                . "<br /><br />Marca: ". $vehiculo_marca
                                                . "<br />Modelo: ". $vehiculo_modelo
                                                . "<br />Año: ". $vehiculo_ano
                                                . "<br />Color: ". $vehiculo_color
                                                . "<br />VIN: ". $vehiculo_vin
                                                . "<br />Número de placa: " . $vehiculo_placa
                                                . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                                . "<br />Correo Cliente: ". $cliente_correoElectronico
                                                . "<br />";

                                            break;
                                        case  "Rechazado":
                                            $correoNotificar = $usuarioCapturo["correoElectronico"];
                                            $archivoCorreo = $constante_rutaTramites . "/" . $id . "/" . $archivo_expediente;

                                            $titulo = "Actualización de trámite";
                                            $mensajeCorreo = "Se ha rechazado la solicitud de trámite con los siguientes datos:"
                                                . "<br /><br />Id: ". $id 
                                                . "<br />Comentarios ICV: ". $comentariosICV
                                                . "<br />Marca: ". $vehiculo_marca
                                                . "<br />Modelo: ". $vehiculo_modelo
                                                . "<br />Año: ". $vehiculo_ano
                                                . "<br />Color: ". $vehiculo_color
                                                . "<br />VIN: ". $vehiculo_vin
                                                . "<br />Número de placa: " . $vehiculo_placa
                                                . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                                . "<br />Correo Cliente: ". $cliente_correoElectronico
                                                . "<br />";

                                            break;
                                        case  "Cancelado":
                                            $correoNotificar = $usuarioCapturo["correoElectronico"];
                                            $archivoCorreo = $constante_rutaTramites . "/" . $id . "/" . $archivo_expediente;

                                            $titulo = "Actualización de trámite";
                                            $mensajeCorreo = "Se ha cancelado la solicitud de trámite con los siguientes datos:"
                                                . "<br /><br />Id: ". $id 
                                                . "<br />Marca: ". $vehiculo_marca
                                                . "<br />Modelo: ". $vehiculo_modelo
                                                . "<br />Año: ". $vehiculo_ano
                                                . "<br />Color: ". $vehiculo_color
                                                . "<br />VIN: ". $vehiculo_vin
                                                . "<br />Número de placa: " . $vehiculo_placa
                                                . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                                . "<br />Correo Cliente: ". $cliente_correoElectronico
                                                . "<br />";

                                            if ($statusAnterior == "A la espera de respuesta de ICV") {
                                                $correoNotificar = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosICV'"))["valor"];
                                                $correoNotificarCopia = obtenResultado(consulta($conexion, "SELECT * FROM parametro WHERE nombre = 'delegacionVirtual_enviarTramiteICV_correosARCA'"))["valor"];

                                                $titulo = "ARCA - " . $folioARCA . " - " . $concesionario["nombreComercial"] . " - " . trim($cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno);
                                                $mensajeCorreo = "Se desea solicitar la cancelación del trámite con los siguientes datos:"
                                                    . "<br /><br />Id: ". $id 
                                                    . "<br />Agencia: ". $concesionario["nombreComercial"]
                                                    . "<br />Usuario capturó: " . $usuario_nombre
                                                    . "<br />Folio ARCA: " . $folioARCA
                                                    . "<br />Mensajero: " . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"]
                                                    . "<br />Comentarios agencia: ". $comentariosConcesionario
                                                    . "<br />Comentarios ARCA: ". $comentariosARCA
                                                    . "<br />Marca: ". $vehiculo_marca
                                                    . "<br />Modelo: ". $vehiculo_modelo
                                                    . "<br />Año: ". $vehiculo_ano
                                                    . "<br />Color: ". $vehiculo_color
                                                    . "<br />VIN: ". $vehiculo_vin
                                                    . "<br />Número de placa: " . $vehiculo_placa
                                                    . "<br />Nombre Cliente: ". $cliente_nombre . " " . $cliente_apellidoPaterno . " " . $cliente_apellidoMaterno
                                                    . "<br />Correo Cliente: ". $cliente_correoElectronico
                                                    . "<br />";
                                            }

                                            break;
                                    }

                                    $archivoCorreo = $constante_rutaTramites . "/" . $id . "/" . $archivo_expediente;

                                    //$enviaCorreo = enviaCorreoMailjet($correoNotificar, $correoNotificarCopia, $titulo, $mensajeCorreo, $archivoCorreo);
                                    $enviaCorreo = enviaCorreoMailjet($correoNotificar, $correoNotificarCopia, $titulo, $mensajeCorreo, $archivoCorreo, "delegacionvirtual@seminuevosarca.com.mx");
                                }
                            }

                            $mensaje = "ok - Los cambios han sido guardados";

                            registraEvento("CMS : Actualización de trámite | id = " . $id);
                        }
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
                    $vehiculo_placa = $tramite["vehiculo_placa"];
                    $cliente_nombre = $tramite["cliente_nombre"];
                    $cliente_apellidoPaterno = $tramite["cliente_apellidoPaterno"];
                    $cliente_apellidoMaterno = $tramite["cliente_apellidoMaterno"];
                    $cliente_correoElectronico = $tramite["cliente_correoElectronico"];
                    $archivo_expediente = $tramite["archivo_expediente"];
                    $archivo_comprobantePago = $tramite["archivo_comprobantePago"];

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

            // Campos de texto a habilitar

            $habilitar_campo_folioARCA = false;
            $habilitar_campo_comentariosConcesionario = false;
            $habilitar_campo_comentariosARCA = false;
            $habilitar_campo_vehiculo_color = false;
            $habilitar_campo_vehiculo_vin = false;
            $habilitar_campo_vehiculo_placa = false;
            $habilitar_campo_cliente_nombre = false;
            $habilitar_campo_cliente_apellidoPaterno = false;
            $habilitar_campo_cliente_apellidoMaterno = false;
            $habilitar_campo_cliente_correoElectronico = false;
            $habilitar_campo_folioICV = false;
            $habilitar_campo_fechaRecepcionCorreoICV = false;
            $habilitar_campo_ejecutivoICV = false;
            $habilitar_campo_comentariosICV = false;

            // Campos de archivos a habilitad

            $habilitar_campo_archivo_expediente = false;
            $habilitar_campo_archivo_comprobantePago = false;
            $habilitar_campo_archivo_solicitudICV = false;

            // Selects a habilitar

            $habilitar_campo_idConcesionario = false;
            $habilitar_campo_idMensajero = false;
            $habilitar_campo_vehiculo_marca = false;
            $habilitar_campo_vehiculo_modelo = false;
            $habilitar_campo_vehiculo_ano = false;

            // Botones a habilitar

            $habilitar_boton_eliminar = false;
            $habilitar_boton_clonarTramite = false;
            $habilitar_boton_rechazado = false;
            $habilitar_boton_aprobado = false;
            $habilitar_boton_enviarICV = false;
            $habilitar_boton_reenviarARCA = false;
            $habilitar_boton_regresarAgencia = false;
            $habilitar_boton_guardar = false;
            $habilitar_boton_guardarComprobantePago = false;
            $habilitar_boton_eliminarArchivoExpediente = false;
            $habilitar_boton_eliminarArchivoSolicitudICV = false;
            $habilitar_boton_cancelarTramite = false;

            if (estaVacio($id)) {
                if ($esUsuarioOperador && $usuario_permisoEditarDelegacionVirtual) {
                    $habilitar_campo_comentariosConcesionario = true;
                    $habilitar_campo_vehiculo_color = true;
                    $habilitar_campo_vehiculo_vin = true;
                    $habilitar_campo_vehiculo_placa = true;
                    $habilitar_campo_cliente_nombre = true;
                    $habilitar_campo_cliente_apellidoPaterno = true;
                    $habilitar_campo_cliente_apellidoMaterno = true;
                    $habilitar_campo_cliente_correoElectronico = true;

                    $habilitar_campo_archivo_expediente = true;

                    $habilitar_campo_idConcesionario = true;
                    $habilitar_campo_idMensajero = true;
                    $habilitar_campo_vehiculo_marca = true;
                    $habilitar_campo_vehiculo_modelo = true;
                    $habilitar_campo_vehiculo_ano = true;

                    $habilitar_boton_guardar = true;
                } else if ($esUsuarioAdministrador && $usuario_permisoEditarDelegacionVirtual) {
                    $habilitar_campo_folioARCA = true;
                    $habilitar_campo_comentariosConcesionario = true;
                    $habilitar_campo_comentariosARCA = true;
                    $habilitar_campo_vehiculo_color = true;
                    $habilitar_campo_vehiculo_vin = true;
                    $habilitar_campo_vehiculo_placa = true;
                    $habilitar_campo_cliente_nombre = true;
                    $habilitar_campo_cliente_apellidoPaterno = true;
                    $habilitar_campo_cliente_apellidoMaterno = true;
                    $habilitar_campo_cliente_correoElectronico = true;
                    $habilitar_campo_folioICV = true;
                    $habilitar_campo_fechaRecepcionCorreoICV = true;
                    $habilitar_campo_ejecutivoICV = true;
                    $habilitar_campo_comentariosICV = true;

                    $habilitar_campo_archivo_expediente = true;
                    $habilitar_campo_archivo_solicitudICV = true;

                    $habilitar_campo_idConcesionario = true;
                    $habilitar_campo_idMensajero = true;
                    $habilitar_campo_vehiculo_marca = true;
                    $habilitar_campo_vehiculo_modelo = true;
                    $habilitar_campo_vehiculo_ano = true;

                    $habilitar_boton_guardar = true;
                } else if ($esUsuarioMaster) {
                    $habilitar_campo_folioARCA = true;
                    $habilitar_campo_comentariosConcesionario = true;
                    $habilitar_campo_comentariosARCA = true;
                    $habilitar_campo_vehiculo_color = true;
                    $habilitar_campo_vehiculo_vin = true;
                    $habilitar_campo_vehiculo_placa = true;
                    $habilitar_campo_cliente_nombre = true;
                    $habilitar_campo_cliente_apellidoPaterno = true;
                    $habilitar_campo_cliente_apellidoMaterno = true;
                    $habilitar_campo_cliente_correoElectronico = true;
                    $habilitar_campo_folioICV = true;
                    $habilitar_campo_fechaRecepcionCorreoICV = true;
                    $habilitar_campo_ejecutivoICV = true;
                    $habilitar_campo_comentariosICV = true;

                    $habilitar_campo_archivo_expediente = true;
                    $habilitar_campo_archivo_solicitudICV = true;

                    $habilitar_campo_idConcesionario = true;
                    $habilitar_campo_idMensajero = true;
                    $habilitar_campo_vehiculo_marca = true;
                    $habilitar_campo_vehiculo_modelo = true;
                    $habilitar_campo_vehiculo_ano = true;

                    $habilitar_boton_guardar = true;
                }
            } else {
                $statusTramite = obtenResultado(consulta($conexion, "SELECT status FROM tramite WHERE id = " . $id))["status"];

                switch ($statusTramite) {
                    case "Enviado a ARCA":
                        if ($esUsuarioOperador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioAdministrador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosARCA = true;

                            $habilitar_boton_enviarICV = true;
                            $habilitar_boton_regresarAgencia = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioMaster) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosConcesionario = true;
                            $habilitar_campo_comentariosARCA = true;
                            $habilitar_campo_vehiculo_color = true;
                            $habilitar_campo_vehiculo_vin = true;
                            $habilitar_campo_vehiculo_placa = true;
                            $habilitar_campo_cliente_nombre = true;
                            $habilitar_campo_cliente_apellidoPaterno = true;
                            $habilitar_campo_cliente_apellidoMaterno = true;
                            $habilitar_campo_cliente_correoElectronico = true;
                            $habilitar_campo_folioICV = true;
                            $habilitar_campo_fechaRecepcionCorreoICV = true;
                            $habilitar_campo_ejecutivoICV = true;
                            $habilitar_campo_comentariosICV = true;

                            $habilitar_campo_archivo_expediente = true;
                            $habilitar_campo_archivo_solicitudICV = true;

                            $habilitar_campo_idConcesionario = true;
                            $habilitar_campo_idMensajero = true;
                            $habilitar_campo_vehiculo_marca = true;
                            $habilitar_campo_vehiculo_modelo = true;
                            $habilitar_campo_vehiculo_ano = true;

                            $habilitar_boton_eliminar = true;
                            $habilitar_boton_enviarICV = true;
                            $habilitar_boton_regresarAgencia = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoExpediente = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        }

                        break;
                    case "Devuelto a la agencia":
                        if ($esUsuarioOperador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_campo_comentariosConcesionario = true;
                            $habilitar_campo_vehiculo_color = true;
                            $habilitar_campo_vehiculo_vin = true;
                            $habilitar_campo_vehiculo_placa = true;
                            $habilitar_campo_cliente_nombre = true;
                            $habilitar_campo_cliente_apellidoPaterno = true;
                            $habilitar_campo_cliente_apellidoMaterno = true;
                            $habilitar_campo_cliente_correoElectronico = true;

                            $habilitar_campo_archivo_expediente = true;

                            $habilitar_campo_idMensajero = true;
                            $habilitar_campo_vehiculo_marca = true;
                            $habilitar_campo_vehiculo_modelo = true;
                            $habilitar_campo_vehiculo_ano = true;

                            $habilitar_boton_reenviarARCA = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioAdministrador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioMaster) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosConcesionario = true;
                            $habilitar_campo_comentariosARCA = true;
                            $habilitar_campo_vehiculo_color = true;
                            $habilitar_campo_vehiculo_vin = true;
                            $habilitar_campo_vehiculo_placa = true;
                            $habilitar_campo_cliente_nombre = true;
                            $habilitar_campo_cliente_apellidoPaterno = true;
                            $habilitar_campo_cliente_apellidoMaterno = true;
                            $habilitar_campo_cliente_correoElectronico = true;
                            $habilitar_campo_folioICV = true;
                            $habilitar_campo_fechaRecepcionCorreoICV = true;
                            $habilitar_campo_ejecutivoICV = true;
                            $habilitar_campo_comentariosICV = true;

                            $habilitar_campo_archivo_expediente = true;
                            $habilitar_campo_archivo_solicitudICV = true;

                            $habilitar_campo_idConcesionario = true;
                            $habilitar_campo_idMensajero = true;
                            $habilitar_campo_vehiculo_marca = true;
                            $habilitar_campo_vehiculo_modelo = true;
                            $habilitar_campo_vehiculo_ano = true;

                            $habilitar_boton_eliminar = true;
                            $habilitar_boton_reenviarARCA = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoExpediente = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        }

                        break;
                    case "Reenviado a ARCA":
                        if ($esUsuarioOperador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioAdministrador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosARCA = true;

                            $habilitar_boton_enviarICV = true;
                            $habilitar_boton_regresarAgencia = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioMaster) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosConcesionario = true;
                            $habilitar_campo_comentariosARCA = true;
                            $habilitar_campo_vehiculo_color = true;
                            $habilitar_campo_vehiculo_vin = true;
                            $habilitar_campo_vehiculo_placa = true;
                            $habilitar_campo_cliente_nombre = true;
                            $habilitar_campo_cliente_apellidoPaterno = true;
                            $habilitar_campo_cliente_apellidoMaterno = true;
                            $habilitar_campo_cliente_correoElectronico = true;
                            $habilitar_campo_folioICV = true;
                            $habilitar_campo_fechaRecepcionCorreoICV = true;
                            $habilitar_campo_ejecutivoICV = true;
                            $habilitar_campo_comentariosICV = true;

                            $habilitar_campo_archivo_expediente = true;
                            $habilitar_campo_archivo_solicitudICV = true;

                            $habilitar_campo_idConcesionario = true;
                            $habilitar_campo_idMensajero = true;
                            $habilitar_campo_vehiculo_marca = true;
                            $habilitar_campo_vehiculo_modelo = true;
                            $habilitar_campo_vehiculo_ano = true;

                            $habilitar_boton_eliminar = true;
                            $habilitar_boton_enviarICV = true;
                            $habilitar_boton_regresarAgencia = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoExpediente = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        }

                        break;
                    case "A la espera de respuesta de ICV":
                        if ($esUsuarioOperador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioAdministrador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_campo_folioICV = true;
                            $habilitar_campo_fechaRecepcionCorreoICV = true;
                            $habilitar_campo_ejecutivoICV = true;
                            $habilitar_campo_comentariosICV = true;

                            $habilitar_campo_archivo_solicitudICV = true;

                            $habilitar_boton_rechazado = true;
                            $habilitar_boton_aprobado = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        } else if ($esUsuarioMaster) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosConcesionario = true;
                            $habilitar_campo_comentariosARCA = true;
                            $habilitar_campo_vehiculo_color = true;
                            $habilitar_campo_vehiculo_vin = true;
                            $habilitar_campo_vehiculo_placa = true;
                            $habilitar_campo_cliente_nombre = true;
                            $habilitar_campo_cliente_apellidoPaterno = true;
                            $habilitar_campo_cliente_apellidoMaterno = true;
                            $habilitar_campo_cliente_correoElectronico = true;
                            $habilitar_campo_folioICV = true;
                            $habilitar_campo_fechaRecepcionCorreoICV = true;
                            $habilitar_campo_ejecutivoICV = true;
                            $habilitar_campo_comentariosICV = true;

                            $habilitar_campo_archivo_expediente = true;
                            $habilitar_campo_archivo_solicitudICV = true;

                            $habilitar_campo_idConcesionario = true;
                            $habilitar_campo_idMensajero = true;
                            $habilitar_campo_vehiculo_marca = true;
                            $habilitar_campo_vehiculo_modelo = true;
                            $habilitar_campo_vehiculo_ano = true;

                            $habilitar_boton_eliminar = true;
                            $habilitar_boton_rechazado = true;
                            $habilitar_boton_aprobado = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoExpediente = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                            $habilitar_boton_cancelarTramite = true;
                        }

                        break;
                    case "Aprobado":
                    case "Aprobado con biométricos":
                        if ($esUsuarioOperador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_campo_archivo_comprobantePago = true;

                            $habilitar_boton_guardar = true;
                            $habilitar_boton_guardarComprobantePago = true;
                        } else if ($esUsuarioAdministrador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_campo_archivo_comprobantePago = true;

                            $habilitar_boton_guardar = true;
                            $habilitar_boton_guardarComprobantePago = true;
                        } else if ($esUsuarioMaster) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosConcesionario = true;
                            $habilitar_campo_comentariosARCA = true;
                            $habilitar_campo_vehiculo_color = true;
                            $habilitar_campo_vehiculo_vin = true;
                            $habilitar_campo_vehiculo_placa = true;
                            $habilitar_campo_cliente_nombre = true;
                            $habilitar_campo_cliente_apellidoPaterno = true;
                            $habilitar_campo_cliente_apellidoMaterno = true;
                            $habilitar_campo_cliente_correoElectronico = true;
                            $habilitar_campo_folioICV = true;
                            $habilitar_campo_fechaRecepcionCorreoICV = true;
                            $habilitar_campo_ejecutivoICV = true;
                            $habilitar_campo_comentariosICV = true;

                            $habilitar_campo_archivo_expediente = true;
                            $habilitar_campo_archivo_comprobantePago = true;
                            $habilitar_campo_archivo_solicitudICV = true;

                            $habilitar_campo_idConcesionario = true;
                            $habilitar_campo_idMensajero = true;
                            $habilitar_campo_vehiculo_marca = true;
                            $habilitar_campo_vehiculo_modelo = true;
                            $habilitar_campo_vehiculo_ano = true;

                            $habilitar_boton_eliminar = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_guardarComprobantePago = true;
                            $habilitar_boton_eliminarArchivoExpediente = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                        }

                        break;
                    case "Rechazado":
                        if ($esUsuarioOperador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_boton_clonarTramite = true;
                            $habilitar_boton_guardar = true;
                        } else if ($esUsuarioAdministrador && $usuario_permisoEditarDelegacionVirtual) {
                            $habilitar_boton_clonarTramite = true;
                            $habilitar_boton_guardar = true;
                        } else if ($esUsuarioMaster) {
                            $habilitar_campo_folioARCA = true;
                            $habilitar_campo_comentariosConcesionario = true;
                            $habilitar_campo_comentariosARCA = true;
                            $habilitar_campo_vehiculo_color = true;
                            $habilitar_campo_vehiculo_vin = true;
                            $habilitar_campo_vehiculo_placa = true;
                            $habilitar_campo_cliente_nombre = true;
                            $habilitar_campo_cliente_apellidoPaterno = true;
                            $habilitar_campo_cliente_apellidoMaterno = true;
                            $habilitar_campo_cliente_correoElectronico = true;
                            $habilitar_campo_folioICV = true;
                            $habilitar_campo_fechaRecepcionCorreoICV = true;
                            $habilitar_campo_ejecutivoICV = true;
                            $habilitar_campo_comentariosICV = true;

                            $habilitar_campo_archivo_expediente = true;
                            $habilitar_campo_archivo_solicitudICV = true;

                            $habilitar_campo_idConcesionario = true;
                            $habilitar_campo_idMensajero = true;
                            $habilitar_campo_vehiculo_marca = true;
                            $habilitar_campo_vehiculo_modelo = true;
                            $habilitar_campo_vehiculo_ano = true;

                            $habilitar_boton_eliminar = true;
                            $habilitar_boton_clonarTramite = true;
                            $habilitar_boton_guardar = true;
                            $habilitar_boton_eliminarArchivoExpediente = true;
                            $habilitar_boton_eliminarArchivoSolicitudICV = true;
                        }

                        break;
                    default:
                        break;
                }
            }


/*
            function calculaTiempoHabilTransccurrido($fechaInicial, $fechaFinal, $horaApertura = '09:00', $horaCierre = '18:00') {
                $inicio = new DateTime($fechaInicial);
                $fin = new DateTime($fechaFinal);

                if ($inicio > $fin) return "00:00:00";

                $totalSegundos = 0;
                $diaIteracion = clone $inicio;
                $diaIteracion->setTime((int)$horaApertura, 0, 0);

                while ($diaIteracion <= $fin) {
                    // Omitir fines de semana (sábado = 6, domingo = 7)
                    if ($diaIteracion->format('N') < 6) {
                        $jornadaInicio = clone $diaIteracion;
                        $jornadaInicio->setTime((int)$horaApertura, 0, 0);

                        $jornadaFin = clone $diaIteracion;
                        $jornadaFin->setTime((int)$horaCierre, 0, 0);

                        // Calcular solapamiento para el día actual
                        $validoInicio = max($inicio, $jornadaInicio);
                        $validoFin = min($fin, $jornadaFin);

                        if ($validoInicio < $validoFin) {
                            $totalSegundos += ($validoFin->getTimestamp() - $validoInicio->getTimestamp());
                        }
                    }
                    $diaIteracion->modify('+1 day');
                    $diaIteracion->setTime((int)$horaApertura, 0, 0);
                }

                // Convertir segundos totales a formato horas:minutos:segundos
                $horas = floor($totalSegundos / 3600);
                $minutos = floor(($totalSegundos / 60) % 60);
                $segundos = $totalSegundos % 60;

                return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
            }
*/


            function calculaTiempoHabilTransccurrido($fechaInicial, $fechaFinal) {
                if (estaVacio($fechaFinal)) {
                    $fechaFinal = date("Y-m-d H:i:s");
                }
//echo "<br />A";
//echo " - fechaInicial = " . $fechaInicial;
//echo " - fechaFinal = " . $fechaFinal;
                $inicio = new DateTime($fechaInicial);
                $fin = new DateTime($fechaFinal);

                if ($inicio > $fin) {
//echo " - A";
                    //return "00:00:00";

                    return "00d 00h 00m";
                } else {
//echo " - B";
                    global $conexion;
//echo " - C";

                    $horario_lunesaviernes_inicio = obtenResultado(consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'horario_lunesaviernes_inicio'"))["valor"];
                    $horario_lunesaviernes_fin = obtenResultado(consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'horario_lunesaviernes_fin'"))["valor"];
                    $horario_sabado_inicio = obtenResultado(consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'horario_sabado_inicio'"))["valor"];
                    $horario_sabado_fin = obtenResultado(consulta($conexion, "SELECT valor FROM parametro WHERE nombre = 'horario_sabado_fin'"))["valor"];

                    $totalSegundos = 0;
                    $diaIteracion = clone $inicio;

                    if ($diaIteracion->format("N") < 6) {
                        $diaIteracion->setTime((int)$horario_lunesaviernes_inicio, 0, 0);
                    } else if ($diaIteracion->format("N") == 6) {
                        $diaIteracion->setTime((int)$horario_sabado_inicio, 0, 0);
                    }

                    while ($diaIteracion <= $fin) {
//echo " - " . $diaIteracion->format("N") . " <= " . $fin->format("N");
//echo ", x";
                        if ($diaIteracion->format("N") <= 6) {
//echo "y";
                            $jornadaInicio = clone $diaIteracion;

                            if ($jornadaInicio->format("N") < 6) {
                                $jornadaInicio->setTime((int)$horario_lunesaviernes_inicio, 0, 0);
                            } else if ($jornadaInicio->format("N") == 6) {
                                $jornadaInicio->setTime((int)$horario_sabado_inicio, 0, 0);
                            }

                            $jornadaFin = clone $diaIteracion;

                            if ($jornadaFin->format("N") < 6) {
                                $jornadaFin->setTime((int)$horario_lunesaviernes_fin, 0, 0);
                            } else if ($jornadaFin->format("N") == 6) {
                                $jornadaFin->setTime((int)$horario_sabado_fin, 0, 0);
                            }

                            // Calcular solapamiento para el día actual

                            $validoInicio = max($inicio, $jornadaInicio);
                            $validoFin = min($fin, $jornadaFin);

                            if ($validoInicio < $validoFin) {
                                $totalSegundos += ($validoFin->getTimestamp() - $validoInicio->getTimestamp());
                            }
                        }

                        $diaIteracion->modify("+1 day");

                        if ($diaIteracion->format("N") < 6) {
                            $diaIteracion->setTime((int)$horario_lunesaviernes_inicio, 0, 0);
                        } else if ($diaIteracion->format("N") == 6) {
                            $diaIteracion->setTime((int)$horario_sabado_inicio, 0, 0);
                        }
                    }

                    // Convertir segundos totales a formato horas:minutos:segundos

                    //$horas = floor($totalSegundos / 3600);
                    //$minutos = floor(($totalSegundos / 60) % 60);
                    //$segundos = $totalSegundos % 60;

                    //return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
//echo " - totalSegundos = " . $totalSegundos;

                    $dias = floor($totalSegundos / 86400);
                    $horas = floor(($totalSegundos / 3600) % 24);
                    $minutos = floor(($totalSegundos / 60) % 60);
//echo " - tiempoTranscurrido = " . sprintf("%02dd %02dh %02dm", $dias, $horas, $minutos);

                    return sprintf("%02dd %02dh %02dm", $dias, $horas, $minutos);
                }
            }


            // Ejemplo de uso:
            //echo "A - " . calculaTiempoHabilTransccurrido('2026-06-15 11:00:00', '2026-06-23 10:00:00');
/*
            15 - 5
            16 - 8
            17 - 8
            18 - 8
            19 - 8
            20 - 6
            21 - 0
            22 - 8
            23 - 2
                 53
*/
            // Resultado esperado: 02:00:00 (1 hora del lunes + 1 hora del martes)
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
                                                        <?php if(!estaVacio($id)) { ?>
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
                                                                                <input class="form-control" id="campo_fechaAlta" name="fechaAlta" type="text" value="<?php echo $fechaAlta; ?>" readonly />
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
                                                                                <input class="form-control" id="campo_status" name="status" type="text" value="<?php echo $status; ?>" readonly />
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
                                                                                <input class="form-control" id="campo_folioARCA" name="folioARCA" type="text" value="<?php echo $folioARCA; ?>" <?php echo $habilitar_campo_folioARCA ? "" : "readonly" ?> />
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
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_idConcesionario" name="idConcesionario" <?php echo $habilitar_campo_idConcesionario ? "" : "disabled" ?>>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php if(!estaVacio($id)) { ?>
                                                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10 hideClonacion">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="row mb-12">
                                                                            <label class="form-label col-md-4" for="campo_idUsuario">Usuario capturó <span class="text-danger">*</span></label>
                                                                            <div class="col-md-8">
                                                                                <input class="form-control" id="campo_idUsuario" name="idUsuario" type="text" value="<?php echo $usuarioCapturo["nombre"]; ?>" readonly />
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
                                                                        <label class="form-label col-md-4" for="campo_idMensajero">Mensajero <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_idMensajero" name="idMensajero" <?php echo $habilitar_campo_idMensajero ? "" : "disabled" ?>>
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
                                                                        <label class="form-label col-md-1" for="campo_comentariosConcesionario">Comentarios Agencia <span class="text-danger">*</span></label>
                                                                        <div class="col-md-11">
                                                                            <textarea class="form-control" id="campo_comentariosConcesionario" name="comentariosConcesionario" rows="2" <?php echo $habilitar_campo_comentariosConcesionario ? "" : "readonly" ?>><?php echo $comentariosConcesionario ?></textarea>
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
                                                                                <textarea class="form-control" id="campo_comentariosARCA" name="comentariosARCA" rows="2" <?php echo $habilitar_campo_comentariosARCA ? "" : "readonly" ?>><?php echo $comentariosARCA ?></textarea>
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
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_vehiculo_marca" name="vehiculo_marca" <?php echo $habilitar_campo_vehiculo_marca ? "" : "disabled" ?>>
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
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_vehiculo_modelo" name="vehiculo_modelo" <?php echo $habilitar_campo_vehiculo_modelo ? "" : "disabled" ?>>
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
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_vehiculo_ano" name="vehiculo_ano" <?php echo $habilitar_campo_vehiculo_ano ? "" : "disabled" ?>>
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
                                                                            <input class="form-control" id="campo_vehiculo_color" name="vehiculo_color" type="text" value="<?php echo $vehiculo_color; ?>" <?php echo $habilitar_campo_vehiculo_color ? "" : "readonly" ?> />
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
                                                                            <input class="form-control" id="campo_vehiculo_vin" name="vehiculo_vin" type="text" value="<?php echo $vehiculo_vin; ?>" <?php echo $habilitar_campo_vehiculo_vin ? "" : "readonly" ?> />
                                                                            <span class="text-danger" id="campo_vehiculo_vin_error">El número de caracteres del VIN es diferente a 17</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 mb-10">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_placa">Número de placa</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_vehiculo_placa" name="vehiculo_placa" type="text" value="<?php echo $vehiculo_placa ?>" <?php echo $habilitar_campo_vehiculo_placa ? "" : "readonly" ?> />
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
                                                                            <input class="form-control" id="campo_cliente_nombre" name="cliente_nombre" type="text" value="<?php echo $cliente_nombre; ?>" <?php echo $habilitar_campo_cliente_nombre ? "" : "readonly" ?> />
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
                                                                            <input class="form-control" id="campo_cliente_apellidoPaterno" name="cliente_apellidoPaterno" type="text" value="<?php echo $cliente_apellidoPaterno; ?>" <?php echo $habilitar_campo_cliente_apellidoPaterno ? "" : "readonly" ?> />
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
                                                                            <input class="form-control" id="campo_cliente_apellidoMaterno" name="cliente_apellidoMaterno" type="text" value="<?php echo $cliente_apellidoMaterno; ?>" <?php echo $habilitar_campo_cliente_apellidoMaterno ? "" : "readonly" ?> />
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
                                                                            <input class="form-control" id="campo_cliente_correoElectronico" name="cliente_correoElectronico" type="text" value="<?php echo $cliente_correoElectronico; ?>" <?php echo $habilitar_campo_cliente_correoElectronico ? "" : "readonly" ?> />
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
                                                                        <label class="form-label mb-10">PDF de expediente <span class="text-danger">*</span></label>

                                                                        <div>
                                                                            <input id="campo_archivo_expediente" name="archivo_expediente" type="file" <?php echo $habilitar_campo_archivo_expediente ? "" : "disabled" ?> />
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
                                                                                                                //echo "<img class='user-img' src='" . $constante_urlTramites . "/" . $id . "/" . $archivo_expediente . "' />";

                                                                                                                echo "<div class='user-data'>";
                                                                                                                echo "<span class='name block'>" . $archivo_expediente . "</span>";
                                                                                                                echo "<span class='time block txt-grey'>";
                                                                                                                echo "<a download href='" . $constante_urlTramites . "/" . $id . "/" . $archivo_expediente . "'>Descargar</a>";

                                                                                                                if ($habilitar_boton_eliminarArchivoExpediente) {
                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                    echo "<a href='javascript:eliminaPdfExpediente(" . $id . ")' id='boton_eliminarArchivoExpediente'>Eliminar</a>";
                                                                                                                }

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

                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label class="form-label mb-10">PDF de pago</label>

                                                                        <div>
                                                                            <input id="campo_archivo_comprobantePago" name="archivo_comprobantePago" type="file" <?php echo $habilitar_campo_archivo_comprobantePago ? "" : "disabled" ?> />
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
                                                                                                            if (!estaVacio($archivo_comprobantePago)) {
                                                                                                                echo "<div class='chat-data' id='contenedor_archivo_comprobantePago'>";
                                                                                                                //echo "<img class='user-img' src='" . $constante_urlTramites . "/" . $id . "/" . $archivo_expediente . "' />";

                                                                                                                echo "<div class='user-data'>";
                                                                                                                echo "<span class='name block'>" . $archivo_comprobantePago . "</span>";
                                                                                                                echo "<span class='time block txt-grey'>";
                                                                                                                echo "<a download href='" . $constante_urlTramites . "/" . $id . "/" . $archivo_comprobantePago . "'>Descargar</a>";
/*
                                                                                                                if ($habilitar_boton_eliminarArchivoExpediente) {
                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                    echo "<a href='javascript:eliminaPdfExpediente(" . $id . ")' id='boton_eliminarArchivoExpediente'>Eliminar</a>";
                                                                                                                }
*/
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
                                                                                <input class="form-control" id="campo_folioICV" name="folioICV" type="text" value="<?php echo $folioICV; ?>" <?php echo $habilitar_campo_folioICV ? "" : "readonly" ?> />
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
                                                                                <input class="form-control" id="campo_fechaRecepcionCorreoICV" name="fechaRecepcionCorreoICV" type="datetime-local" value="<?php echo $fechaRecepcionCorreoICV; ?>" <?php echo $habilitar_campo_fechaRecepcionCorreoICV ? "" : "readonly" ?> />
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
                                                                                <input class="form-control" id="campo_ejecutivoICV" name="ejecutivoICV" type="text" value="<?php echo $ejecutivoICV; ?>" <?php echo $habilitar_campo_ejecutivoICV ? "" : "readonly" ?> />
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
                                                                                <textarea class="form-control" id="campo_comentariosICV" name="comentariosICV" rows="2" <?php echo $habilitar_campo_comentariosICV ? "" : "readonly" ?>><?php echo $comentariosICV ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label class="form-label mb-10">PDF de la solicitud de ICV</label>

                                                                            <div>
                                                                                <input id="campo_archivo_solicitudICV" name="archivo_solicitudICV" type="file" <?php echo $habilitar_campo_archivo_solicitudICV ? "" : "disabled" ?>/>
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
                                                                                                                    //echo "<img class='user-img' src='" . $constante_urlTramites . "/" . $id . "/" . $archivo_solicitudICV . "' />";

                                                                                                                    echo "<div class='user-data'>";
                                                                                                                    echo "<span class='name block'>" . $archivo_solicitudICV . "</span>";
                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                    echo "<a download href='" . $constante_urlTramites . "/" . $id . "/" . $archivo_solicitudICV . "'>Descargar</a>";

                                                                                                                    if ($habilitar_boton_eliminarArchivoSolicitudICV) {
                                                                                                                        echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                        echo "<a href='javascript:eliminaArchivoIne(" . $id . ")' id='boton_eliminarArchivoSolicitudICV'>Eliminar</a>";
                                                                                                                    }

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
                                                                                        echo "<tr>";
                                                                                            echo "<td>" . $bitacora["fechaInicial"] . "</td>";
                                                                                            echo "<td>" . $bitacora["status"] . "</td>";
                                                                                            echo "<td>" . ($bitacora["status"] == "Aprobado" || $bitacora["status"] == "Rechazado" ? "" : calculaTiempoHabilTransccurrido($bitacora["fechaInicial"], $bitacora["fechaFinal"])) . "</td>";
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
                                        <div class="col-md-2 col-sm-3" id="contenedor_botonesIzquierdos">
                                            <?php if ($habilitar_boton_eliminar && $eliminado == 0) { ?>
                                                <div class="form-group">
                                                    <a class="btn btn-danger mb-3" href="javascript:eliminaTramite(<?php echo  $id ?>)" id="boton_eliminar">Eliminar</a>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="col-md-10 col-sm-9" id="contenedor_botonesDerechos">
                                            <div class="form-group">
                                                <input id="campo_accion" name="accion" type="hidden" value="<?php echo $accion; ?>" />
                                                <a class="btn btn-default mb-3" href="javascript:void(0)" id="boton_cerrar" style="color: #fff !important">Cerrar</a>

                                                <?php if ($habilitar_boton_guardar) { ?>
                                                    <button class="btn btn-success mb-3"  id="boton_guardar" onclick="return confirm('¿Desea dar de alta este trámite?');" style="background-color: #2d146f; color: #fff !important" type="submit" >Generar trámite</button>
                                                <?php } ?>

                                                <?php if ($habilitar_boton_enviarICV) { ?>
                                                    <a class="btn btn-default mb-3"  href="javascript:;" id="boton_enviar_icv" onclick="enviarICV()" style="background-color: #fb8402; color: #fff !important">Enviar a ICV</a>
                                                <?php }?>

                                                <?php if ($habilitar_boton_reenviarARCA) { ?>
                                                    <a class="btn btn-default mb-3"  href="javascript:;" id="boton_reenviar_arca" onclick="reenviarArca()" style="background-color: #2d146f; color: #fff !important">Reenviar a ARCA</a>
                                                <?php }?>

                                                <?php if ($habilitar_boton_regresarAgencia) { ?>
                                                    <a class="btn btn-default mb-3"  href="javascript:;" id="boton_regresar_agencia" onclick="regresarAAgencia()" style="background-color: #ebc807; color: #fff !important">Regresar a Agencia</a>
                                                <?php } ?>

                                                <?php if ($habilitar_boton_rechazado) { ?>
                                                    <a class="btn btn-default mb-3"  href="javascript:;" id="boton_rechazado" onclick="rechazado()" style="background-color: #e20000; color: #fff !important">Rechazado</a>
                                                <?php }?>

                                                <?php if ($habilitar_boton_clonarTramite) { ?>
                                                    <a class="btn btn-default mb-3"  href="javascript:;" id="boton_clonar_tramite" onclick="clonar()" style="background-color: #5ce1e6; color: #fff !important">Clonar trámite</a>
                                                <?php }?>

                                                <?php if ($habilitar_boton_aprobado) { ?>
                                                    <a class="btn btn-default mb-3"  href="javascript:;" id="boton_aprobado" onclick="aprobado()" style="background-color: #00ae3a; color: #fff !important">Aprobado</a>
                                                <?php }?>

                                                <?php if ($habilitar_boton_guardarComprobantePago) { ?>
                                                    <a class="btn btn-success mb-3"  href="javascript:;" id="boton_guardarComprobantePago" onclick="submit()" style="color: #fff !important">Guardar</a>
                                                <?php }?>

                                                <?php if ($habilitar_boton_aprobado) { ?>
                                                    <a class="btn btn-default mb-3"  href="javascript:;" id="boton_aprobarConBiometricos" onclick="aprobarConBiometricos()" style="background-color: #5170ff; color: #fff !important">Aprobado con biométricos</a>
                                                <?php }?>

                                                <?php if ($habilitar_boton_cancelarTramite) { ?>
                                                    <a class="btn btn-default mb-3" href="javascript:;" id="boton_cancelarTramite" onclick="cancelarTramite()" style="background-color: #432918; color: #fff !important">Cancelar trámite</a>
                                                <?php }?>
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

                cargaConcesionarios();
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


            function cargaConcesionarios() {
                $.LoadingOverlay("show", { minSize: 30, maxSize: 30 });

                var idConcesionario = "<?php echo $esUsuarioMaster || $esUsuarioAdministrador ? "" : $usuario_idConcesionario ?>";

                $.ajax({
                    url: "socialware/php/ajax/cargaConcesionarios.php",
                    type: "post",
                    data: {
                        idConcesionario: idConcesionario
                    }
                }).done(function (resultado, textStatus, jqXHR) {
                    $("#campo_idConcesionario").append("<option value=''>Seleccione</option>");

                    var idConcesionario = "<?php echo $idConcesionario; ?>";

                    $(resultado).find("concesionario").each(function (index) {
                        var id = $(this).find("id").text();
                        var nombreComercial = $(this).find("nombreComercial").text();

                        $("#campo_idConcesionario").append("<option " + (idConcesionario == id ? "selected" : "") + " value='" + id + "'>" + nombreComercial + "</option>");
                    });

                    $("#campo_idConcesionario").select2("destroy");
                    $("#campo_idConcesionario").select2();

                    cargaMensajeros();
                });
            }


            function cargaMensajeros() {
                var idConcesionario = $("#campo_idConcesionario option:selected").val();

                $.ajax({
                    url: "socialware/php/ajax/cargaMensajeros.php",
                    type: "post",
                    data: {
                        idConcesionario: idConcesionario
                    }
                }).done(function (resultado, textStatus, jqXHR) {
                    $("#campo_idMensajero").html("<option value=''>Seleccione</option>");

                    var idMensajero = "<?php echo $idMensajero; ?>";

                    $(resultado).find("mensajero").each(function (index) {
                        var id = $(this).find("id").text();
                        var nombre = $(this).find("nombre").text();

                        $("#campo_idMensajero").append("<option " + (idMensajero == id ? "selected" : "") + " value='" + id + "'>" + nombre + "</option>");
                    });

                    $("#campo_idMensajero").select2("destroy");
                    $("#campo_idMensajero").select2();
                });
            }


            function cargaMarcas() {
                //$.LoadingOverlay("show", { minSize: 30, maxSize: 30 });

                $.ajax({
                    url: "socialware/php/ajax/cargaMarcas.php",
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var marca = '<?php echo $vehiculo_marca; ?>' ;

                    var json = jQuery.parseJSON(resultado);

                    $.each(json.data,function(i,nodo){
                        if (marca == nodo.name) {
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        } else {
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#campo_vehiculo_marca").append(contenido);

                    cargaModelos();
                });
            }


            function cargaModelos() {
                var idMarca = $("#campo_vehiculo_marca option:selected").attr("data-id");

                $.ajax({
                    url: "socialware/php/ajax/cargaModelos.php?marca=" + idMarca,
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var modelo = '<?php echo $vehiculo_modelo; ?>' ;

                    var json = jQuery.parseJSON(resultado);

                    $.each(json.data,function(i,nodo){
                        if (modelo == nodo.name) {
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        } else {
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#campo_vehiculo_modelo").html("");
                    $("#campo_vehiculo_modelo").append(contenido);

                    $("#campo_vehiculo_ano").html("");

                    cargaAnos();
                });
            }


            function cargaAnos() {
                var idMarca = $("#campo_vehiculo_marca option:selected").attr("data-id");
                var idModelo = $("#campo_vehiculo_modelo option:selected").attr("data-id");

                $.ajax({
                    url: "socialware/php/ajax/cargaAnos.php?marca=" + idMarca + "&modelo=" + idModelo,
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var ano = '<?php echo $vehiculo_ano; ?>' ;

                    var json = jQuery.parseJSON(resultado);

                    $.each(json.data,function(i,nodo){
                        if (ano == nodo.name) {
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        } else {
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#campo_vehiculo_ano").html("");
                    $("#campo_vehiculo_ano").append(contenido);

                    $.LoadingOverlay("hide");
                });
            }


            $("#campo_idConcesionario").on("change", function() {
                cargaMensajeros();
            });


            $("#campo_vehiculo_marca").on("change", function() {
                cargaModelos();
            });


            $("#campo_vehiculo_modelo").on("change", function() {
                cargaAnos();
            });


            $("#campo_vehiculo_vin").on("focusout", function() {
                var vin = $(this).val();

                if (vin.length === 17) {
                    $("#campo_vehiculo_vin_error").hide();
                } else {
                    $("#campo_vehiculo_vin_error").show();
                }
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


            // Funciones por estatus


            function regresarAAgencia() {
                //event.preventDefault();

                if (confirm("¿Desea reenviar a la agencia este trámite?")) {
                    $("#campo_accion").val("Devuelto a la agencia");

                    $("input").removeAttr("readonly");
                    $("select").removeAttr("disabled");

                    $('#formulario').submit();
                }
            }


            function reenviarArca() {
                //event.preventDefault();

                let cuentaConArchivoExpediente = "<?php echo estaVacio($archivo_expediente) ? 0 : 1 ?>";
                let archivoExpedienteAdjunto = $("#archivo_expediente").val();

                if (cuentaConArchivoExpediente == 0 && !archivoExpedienteAdjunto) {
                    alert("Para continuar debe adjuntar un archivo PDF de expediente");
                } else {
                    if (confirm("¿Desea reenviar a ARCA este trámite?")) {
                        $("#campo_accion").val("Reenviado a ARCA");

                        $("input").removeAttr("readonly");
                        $("select").removeAttr("disabled");

                        $('#formulario').submit();
                    }
                }
            }


            function enviarICV() {
                //event.preventDefault();

                if (confirm("¿Desea enviar a ICV este trámite?")) {
                    $("#campo_accion").val("A la espera de respuesta de ICV");

                    $("input").removeAttr("readonly");
                    $("select").removeAttr("disabled");

                    $('#formulario').submit();
                }
            }


            function aprobado() {
                //event.preventDefault();

                if (confirm("¿Desea aprobar este trámite?")) {
                    $("#campo_accion").val("Aprobado");

                    $("input").removeAttr("readonly");
                    $("select").removeAttr("disabled");

                    $('#formulario').submit();
                }
            }


            function rechazado() {
                //event.preventDefault();

                if (confirm("¿Desea rechazar este trámite?")) {
                    $("#campo_accion").val("Rechazado");

                    $("input").removeAttr("readonly");
                    $("select").removeAttr("disabled");

                    $('#formulario').submit();
                }
            }


            function cancelarTramite() {
                //event.preventDefault();

                if (confirm("¿Desea cancelar este trámite?")) {
                    $("#campo_accion").val("Cancelado");

                    $("input").removeAttr("readonly");
                    $("select").removeAttr("disabled");

                    $('#formulario').submit();
                }
            }


            function aprobarConBiometricos() {
                //event.preventDefault();

                if (confirm("¿Desea aprobar este trámite?")) {
                    $("#campo_accion").val("Aprobado con biométricos");

                    $("input").removeAttr("readonly");
                    $("select").removeAttr("disabled");

                    $('#formulario').submit();
                }
            }


            function submit() {
                //event.preventDefault();

                //$("#campo_accion").val("Aprobado");

                $("input").removeAttr("readonly");
                $("select").removeAttr("disabled");

                $('#formulario').submit();
            }


            function clonar() {
                //event.preventDefault();

                if (confirm("¿Desea clonar este trámite?")) {
                    $("#campo_accion").val("Clonar");

                    $("#campo_id").val("");
                    $("#campo_comentariosConcesionario").val("");

                    $("#campo_comentariosConcesionario").removeAttr("readonly");
                    $("#campo_vehiculo_color").removeAttr("readonly");
                    $("#campo_vehiculo_vin").removeAttr("readonly");
                    $("#campo_vehiculo_placa").removeAttr("readonly");
                    $("#campo_cliente_nombre").removeAttr("readonly");
                    $("#campo_cliente_apellidoPaterno").removeAttr("readonly");
                    $("#campo_cliente_apellidoMaterno").removeAttr("readonly");
                    $("#campo_cliente_correoElectronico").removeAttr("readonly");

                    $("#campo_archivo_expediente").removeAttr("disabled");

                    $("#campo_idConcesionario").removeAttr("disabled");
                    $("#campo_idMensajero").removeAttr("disabled");
                    $("#campo_vehiculo_marca").removeAttr("disabled");
                    $("#campo_vehiculo_modelo").removeAttr("disabled");
                    $("#campo_vehiculo_ano").removeAttr("disabled");

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


/*
            var id = <?php echo estaVacio($id) ? 0 : $id; ?>;

            if (id > 0) {
                $("#boton_guardar").hide();
            }
*/
            <?php if (!estaVacio($id)) { ?>
                $("#boton_guardar").hide();
            <?php } ?>
        </script>
    </body>
</html>
