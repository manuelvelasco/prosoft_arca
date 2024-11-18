<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>
    </head>


    <body>
        <?php

            // Obtiene parametros de request

            $esSubmit = sanitiza($conexion, filter_input(INPUT_POST, "esSubmit"));

            $id = sanitiza($conexion, filter_input(INPUT_POST, "id"));
            $habilitado = sanitiza($conexion, filter_input(INPUT_POST, "habilitado"));
            $intelimotor_apiKey = sanitiza($conexion, filter_input(INPUT_POST, "intelimotor_apiKey"));
            $intelimotor_apiSecret = sanitiza($conexion, filter_input(INPUT_POST, "intelimotor_apiSecret"));

            $razonSocial = sanitiza($conexion, filter_input(INPUT_POST, "razonSocial"));
            $rfc = sanitiza($conexion, filter_input(INPUT_POST, "rfc"));
            $nombreComercial = sanitiza($conexion, filter_input(INPUT_POST, "nombreComercial"));
            $resumen = sanitiza($conexion, filter_input(INPUT_POST, "resumen"));
            $descripcion = sanitiza($conexion, filter_input(INPUT_POST, "descripcion"));
            $calle = sanitiza($conexion, filter_input(INPUT_POST, "calle"));
            $numeroExterior = sanitiza($conexion, filter_input(INPUT_POST, "numeroExterior"));
            $numeroInterior = sanitiza($conexion, filter_input(INPUT_POST, "numeroInterior"));
            $colonia = sanitiza($conexion, filter_input(INPUT_POST, "colonia"));
            $municipio = sanitiza($conexion, filter_input(INPUT_POST, "municipio"));
            $referenciasDomicilio = sanitiza($conexion, filter_input(INPUT_POST, "referenciasDomicilio"));
            $horario = sanitiza($conexion, filter_input(INPUT_POST, "horario"));
            $sitioWeb = sanitiza($conexion, filter_input(INPUT_POST, "sitioWeb"));
            $facebook = sanitiza($conexion, filter_input(INPUT_POST, "facebook"));
            $telefono = sanitiza($conexion, filter_input(INPUT_POST, "telefono"));
            $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
            $whatsapp = sanitiza($conexion, filter_input(INPUT_POST, "whatsapp"));
            $instagram = sanitiza($conexion, filter_input(INPUT_POST, "instagram"));
            $tiktok = sanitiza($conexion, filter_input(INPUT_POST, "tiktok"));
            $youtube = sanitiza($conexion, filter_input(INPUT_POST, "youtube"));


            // Parametros enviados por origen

            $origen = sanitiza($conexion, filter_input(INPUT_POST, "origen"));
            $origen_municipio = sanitiza($conexion, filter_input(INPUT_POST, "origen_municipio"));
            $origen_publicado = sanitiza($conexion, filter_input(INPUT_POST, "origen_publicado"));

            // Inicializa variables

            $fechaActual = date("Y-m-d H:i:s");
            $mensaje = "";
            $habilitado = estaVacio($habilitado) ? 0 : 1;
            $logotipo = "";
            $fachada = "";
            $tieneFachadaCargada = 0;

            if (isset($_FILES["fachada"])) {
                try {
                    $archivo = $_FILES["fachada"];
                    if ($archivo["size"] > 0) {
                        $tieneFachadaCargada = 1;
                    }

                } catch (Exception $e) {
                }
            }

            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($razonSocial)) {
                    $mensaje .= "* Razón Social es obligatorio<br />";
                }

                if (estaVacio($rfc)) {
                    $mensaje .= "* RFC es obligatorio<br />";
                }

                if (estaVacio($nombreComercial)) {
                    $mensaje .= "* Nombre Comercial es obligatorio<br />";
                }

                if (estaVacio($calle)) {
                    $mensaje .= "* Calle es obligatorio<br />";
                }

                if (estaVacio($numeroExterior)) {
                    $mensaje .= "* Número exterior es obligatorio<br />";
                }

                if (estaVacio($colonia)) {
                    $mensaje .= "* Colonia es obligatorio<br />";
                }

                if (estaVacio($municipio)) {
                    $mensaje .= "* Municipio es obligatorio<br />";
                }

                if (estaVacio($telefono)) {
                    $mensaje .= "* Teléfono de contacto es obligatorio<br />";
                }

                if ((estaVacio($id)) && ($tieneFachadaCargada == 0)) {
                    $mensaje .= "* Foto de fachada o foto principal es obligatorio<br />";
                }

                if (estaVacio($id)){
                    $validaNombreComecial_BD = consulta($conexion, "SELECT id FROM concesionario WHERE nombreComercial = '" . $nombreComercial . "'");
                    $validaRFC_BD = consulta($conexion, "SELECT id FROM concesionario WHERE rfc = '" . $rfc . "'");
                    $validaRazonSocial_BD = consulta($conexion, "SELECT id FROM concesionario WHERE razonSocial = '" . $razonSocial . "'");

                }else{
                    $validaNombreComecial_BD = consulta($conexion, "SELECT id FROM concesionario WHERE nombreComercial = '" . $nombreComercial . "' AND id <> " . $id);
                    $validaRFC_BD = consulta($conexion, "SELECT id FROM concesionario WHERE rfc = '" . $rfc . "' AND id <> " . $id);
                    $validaRazonSocial_BD = consulta($conexion, "SELECT id FROM concesionario WHERE razonSocial = '" . $razonSocial . "' and id <> " . $id);
                }

                if (cuentaResultados($validaNombreComecial_BD) > 0) {
                    $mensaje .= "* Ya existe un concesionario con ese nombre comercial<br />";
                }

                
                if (cuentaResultados($validaRFC_BD) > 0) {
                    $mensaje .= "* Ya existe un concesionario con ese RFC<br />";
                }

                if (cuentaResultados($validaRazonSocial_BD) > 0) {
                    $mensaje .= "* Ya existe un concesionario con ese razón social<br />";
                }

                if ((!estaVacio($telefono)) && (strlen($telefono) != 10)) {
                    $mensaje .= "* Teléfono con formato correcto<br />";
                }

                if ((!estaVacio($whatsapp)) && (strlen($whatsapp) != 10)) {
                    $mensaje .= "* Número de whatsapp con formato correcto<br />";
                }

                if ((!estaVacio($correoElectronico)) && (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL))) {
                    $mensaje .= "* Correo electrónico con formato correcto<br />";
                }

                if ((!estaVacio($sitioWeb)) && (filter_var($sitioWeb, FILTER_VALIDATE_URL) === FALSE)) {
                    $mensaje .= "* Sitio web con formato correcto <br />";
                }

                if ((!estaVacio($facebook)) && (filter_var($facebook, FILTER_VALIDATE_URL) === FALSE)) {
                    $mensaje .= "* Facebook con formato correcto <br />";
                }

                if ((!estaVacio($instagram)) && (filter_var($instagram, FILTER_VALIDATE_URL) === FALSE)) {
                    $mensaje .= "* Instagram con formato correcto <br />";
                }

                if ((!estaVacio($tiktok)) && (filter_var($tiktok, FILTER_VALIDATE_URL) === FALSE)) {
                    $mensaje .= "* Tiktok con formato correcto <br />";
                }

                if ((!estaVacio($youtube)) && (filter_var($youtube, FILTER_VALIDATE_URL) === FALSE)) {
                    $mensaje .= "* Youtube con formato correcto <br />";
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Valide los siguientes datos:<br /><br />" . $mensaje;
                } else {
                    if (estaVacio($id)) {

                        // Es insercion

                        consulta($conexion, "INSERT INTO concesionario ("
                                . "habilitado"
                                . ", intelimotor_apiKey"
                                . ", intelimotor_apiSecret"
                                . ", razonSocial"
                                . ", rfc"
                                . ", nombreComercial"
                                . ", resumen"
                                . ", descripcion"
                                . ", calle"
                                . ", numeroExterior"
                                . ", numeroInterior"
                                . ", colonia"
                                . ", municipio"
                                . ", referenciasDomicilio"
                                . ", horario"
                                . ", sitioWeb"
                                . ", facebook"
                                . ", telefono"
                                . ", correoElectronico"
                                . ", whatsapp"
                                . ", instagram"
                                . ", tiktok"
                                . ", youtube"
                            . ") VALUES ("
                                . $habilitado
                                . ", '" . $intelimotor_apiKey . "'"
                                . ", '" . $intelimotor_apiSecret . "'"
                                . ", '" . $razonSocial . "'"
                                . ", '" . $rfc . "'"
                                . ", '" . $nombreComercial . "'"
                                . ", '" . $resumen . "'"
                                . ", '" . $descripcion . "'"
                                . ", '" . $calle . "'"
                                . ", '" . $numeroExterior . "'"
                                . ", '" . $numeroInterior . "'"
                                . ", '" . $colonia . "'"
                                . ", '" . $municipio . "'"
                                . ", '" . $referenciasDomicilio . "'"
                                . ", '" . $horario . "'"
                                . ", '" . $sitioWeb . "'"
                                . ", '" . $facebook . "'"
                                . ", '" . $telefono . "'"
                                . ", '" . $correoElectronico . "'"
                                . ", '" . $whatsapp . "'"
                                . ", '" . $instagram . "'"
                                . ", '" . $tiktok . "'"
                                . ", '" . $youtube . "'"
                            . ")");

                        $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario ORDER BY id DESC LIMIT 1");
                        $concesionario = obtenResultado($concesionario_BD);

                        $id = $concesionario["id"];
                        $habilitado = $concesionario["habilitado"];
                        $intelimotor_apiKey = $concesionario["intelimotor_apiKey"];
                        $intelimotor_apiSecret = $concesionario["intelimotor_apiSecret"];
                        $razonSocial = $concesionario["razonSocial"];
                        $rfc = $concesionario["rfc"];
                        $nombreComercial = $concesionario["nombreComercial"];
                        $resumen = $concesionario["resumen"];
                        $descripcion = $concesionario["descripcion"];
                        $calle = $concesionario["calle"];
                        $numeroExterior = $concesionario["numeroExterior"];
                        $numeroInterior = $concesionario["numeroInterior"];
                        $colonia = $concesionario["colonia"];
                        $municipio = $concesionario["municipio"];
                        $referenciasDomicilio = $concesionario["referenciasDomicilio"];
                        $horario = $concesionario["horario"];
                        $sitioWeb = $concesionario["sitioWeb"];
                        $facebook = $concesionario["facebook"];
                        $telefono = $concesionario["telefono"];
                        $correoElectronico = $concesionario["correoElectronico"];
                        $whatsapp = $concesionario["whatsapp"];
                        $logotipo = $concesionario["logotipo"];
                        $fachada = $concesionario["fachada"];
                        $instagram = $concesionario["instagram"];
                        $tiktok = $concesionario["tiktok"];
                        $youtube = $concesionario["youtube"];


                        $mensaje = "ok - El concesionario ha sido registrado.";

                        registraEvento("CMS : Alta de concesionario | id = " . $id);
                    } else {
                        
                        // Es actualizacion

                        consulta($conexion, "UPDATE concesionario SET"
                                . " habilitado = " . $habilitado
                                . ", intelimotor_apiKey = '" . $intelimotor_apiKey . "'"
                                . ", intelimotor_apiSecret = '" . $intelimotor_apiSecret . "'"
                                . ", razonSocial = '" . $razonSocial . "'"
                                . ", rfc = '" . $rfc . "'"
                                . ", nombreComercial = '" . $nombreComercial . "'"
                                . ", resumen = '" . $resumen . "'"
                                . ", descripcion = '" . $descripcion . "'"
                                . ", calle = '" . $calle . "'"
                                . ", numeroExterior = '" . $numeroExterior . "'"
                                . ", numeroInterior = '" . $numeroInterior . "'"
                                . ", colonia = '" . $colonia . "'"
                                . ", municipio = '" . $municipio . "'"
                                . ", referenciasDomicilio = '" . $referenciasDomicilio . "'"
                                . ", horario = '" . $horario . "'"
                                . ", sitioWeb = '" . $sitioWeb . "'"
                                . ", facebook = '" . $facebook . "'"
                                . ", telefono = '" . $telefono . "'"
                                . ", correoElectronico = '" . $correoElectronico . "'"
                                . ", whatsapp = '" . $whatsapp . "'"
                                . ", instagram = '" . $instagram . "'"
                                . ", tiktok = '" . $tiktok . "'"
                                . ", youtube = '" . $youtube . "'"
                            . " WHERE id = " . $id);

                        $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $id);
                        $concesionario = obtenResultado($concesionario_BD);

                        $id = $concesionario["id"];
                        $habilitado = $concesionario["habilitado"];
                        $intelimotor_apiKey = $concesionario["intelimotor_apiKey"];
                        $intelimotor_apiSecret = $concesionario["intelimotor_apiSecret"];
                        $razonSocial = $concesionario["razonSocial"];
                        $rfc = $concesionario["rfc"];
                        $nombreComercial = $concesionario["nombreComercial"];
                        $resumen = $concesionario["resumen"];
                        $descripcion = $concesionario["descripcion"];
                        $calle = $concesionario["calle"];
                        $numeroExterior = $concesionario["numeroExterior"];
                        $numeroInterior = $concesionario["numeroInterior"];
                        $colonia = $concesionario["colonia"];
                        $municipio = $concesionario["municipio"];
                        $referenciasDomicilio = $concesionario["referenciasDomicilio"];
                        $horario = $concesionario["horario"];
                        $sitioWeb = $concesionario["sitioWeb"];
                        $facebook = $concesionario["facebook"];
                        $telefono = $concesionario["telefono"];
                        $correoElectronico = $concesionario["correoElectronico"];
                        $whatsapp = $concesionario["whatsapp"];
                        $logotipo = $concesionario["logotipo"];
                        $fachada = $concesionario["fachada"];
                        $instagram = $concesionario["instagram"];
                        $tiktok = $concesionario["tiktok"];
                        $youtube = $concesionario["youtube"];

                        $mensaje = "ok - Los cambios han sido guardados";

                        registraEvento("CMS : Actualización de concesionario | id = " . $id);
                    }

                    if (!file_exists($constante_rutaConcesionarios  . $id)) {
                        try {
                            mkdir($constante_rutaConcesionarios  . $id, 0755, true);
                        } catch (Exception $ex) {
                        }
                    }
                    

                    // Carga logotipo

                    if (isset($_FILES["logotipo"])) {
                        try {
                            $archivo = $_FILES["logotipo"];

                            if ($archivo["size"] > 0) {
                                $nombreEstandarizado = $id . "_logotipo_" . date("YmdHis") . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                $archivoDestino = $constante_rutaConcesionarios . "/" . $id . "/" . $nombreEstandarizado;

                                if (!file_exists($constante_rutaConcesionarios . "/" . $id)) {
                                    mkdir($constante_rutaConcesionarios . "/" . $id, 0755, true);
                                }

                                move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                consulta($conexion, "UPDATE concesionario SET logotipo = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                $logotipo = $nombreEstandarizado;

                                //
                            }
                        } catch (Exception $e) {
                        }
                    }

                    // Carga fachada

                    if (isset($_FILES["fachada"])) {
                        try {
                            $archivo = $_FILES["fachada"];

                            if ($archivo["size"] > 0) {
                                $nombreEstandarizado = $id . "_fachada_" . date("YmdHis") . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                $archivoDestino = $constante_rutaConcesionarios . "/" . $id . "/" . $nombreEstandarizado;

                                if (!file_exists($constante_rutaConcesionarios . "/" . $id)) {
                                    mkdir($constante_rutaConcesionarios . "/" . $id, 0755, true);
                                }

                                move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                consulta($conexion, "UPDATE concesionario SET fachada = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                $fachada = $nombreEstandarizado;

                                //
                            }
                        } catch (Exception $e) {
                        }
                    }

                    // Carga imagen de galeria

                    if (isset($_FILES["imagenGaleria"])) {
                        try {
                            //$archivo = $_FILES["imagenGaleria"];
                            $archivos = reArrayFiles($_FILES["imagenGaleria"]);

                            foreach ($archivos as $archivo) {
                                if ($archivo["size"] > 0) {
                                    $nombreEstandarizado = $id . "_galeria_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                    $archivoDestino = $constante_rutaConcesionarios . "/" . $id . "/galeria/" . $nombreEstandarizado;

                                    if (!file_exists($constante_rutaConcesionarios . "/" . $id . "/galeria")) {
                                        mkdir($constante_rutaConcesionarios . "/" . $id . "/galeria", 0755, true);
                                    }

                                    move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                    //$pdf->Cell( 40, 40, $pdf->Image($archivoDestino, $pdf->GetX(), $pdf->GetY(), 33.78), 0, 0, 'L', false );
                                }
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $id);
                    $concesionario = obtenResultado($concesionario_BD);

                    $id = $concesionario["id"];
                    $habilitado = $concesionario["habilitado"];
                    $intelimotor_apiKey = $concesionario["intelimotor_apiKey"];
                    $intelimotor_apiSecret = $concesionario["intelimotor_apiSecret"];
                    $razonSocial = $concesionario["razonSocial"];
                    $rfc = $concesionario["rfc"];
                    $nombreComercial = $concesionario["nombreComercial"];
                    $resumen = $concesionario["resumen"];
                    $descripcion = $concesionario["descripcion"];
                    $calle = $concesionario["calle"];
                    $numeroExterior = $concesionario["numeroExterior"];
                    $numeroInterior = $concesionario["numeroInterior"];
                    $colonia = $concesionario["colonia"];
                    $municipio = $concesionario["municipio"];
                    $referenciasDomicilio = $concesionario["referenciasDomicilio"];
                    $horario = $concesionario["horario"];
                    $sitioWeb = $concesionario["sitioWeb"];
                    $facebook = $concesionario["facebook"];
                    $telefono = $concesionario["telefono"];
                    $correoElectronico = $concesionario["correoElectronico"];
                    $whatsapp = $concesionario["whatsapp"];
                    $logotipo = $concesionario["logotipo"];
                    $fachada = $concesionario["fachada"];
                    $instagram = $concesionario["instagram"];
                    $tiktok = $concesionario["tiktok"];
                    $youtube = $concesionario["youtube"];

                    registraEvento("CMS : Consulta de concesionario | id = " . $id);
                } else {
                    $habilitado = 0;
                    $intelimotor_apiKey = "";
                    $intelimotor_apiSecret = "";

                    $razonSocial = "";
                    $rfc = "";
                    $nombreComercial = "";
                    $resumen = "";
                    $descripcion = "";
                    $calle = "";
                    $numeroExterior = "";
                    $numeroInterior = "";
                    $colonia = "";
                    $municipio = "";
                    $referenciasDomicilio = "";
                    $horario = "";
                    $sitioWeb = "";
                    $facebook = "";
                    $telefono = "";
                    $correoElectronico = "";
                    $whatsapp = "";
                    $logotipo = "";
                    $fachada = "";
                    $instagram = "";
                    $tiktok = "";
                    $youtube = "";

                }
            }


            function reArrayFiles($file) {
                $file_ary = array();
                $file_count = count($file['name']);
                $file_key = array_keys($file);

                for ($i=0; $i < $file_count; $i++) {
                    foreach ($file_key as $val) {
                        $file_ary[$i][$val] = $file[$val][$i];
                    }
                }

                return $file_ary;
            }  
        ?>

        <!-- Preloader -->

        <div class="preloader-it">
            <div class="la-anim-1"></div>
        </div>

        <div class="wrapper">
            <?php include("socialware/php/estructura/encabezado.php"); ?>

            <?php include("socialware/php/estructura/menu.php"); ?>

            <!-- Contenido -->

            <div class="page-wrapper">
                <div class="container-fluid">
                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador || $esUsuarioOperador ) { ?>

                        <!-- Titulo -->

                        <div class="row heading-bg bg-blue">
                            <div class="col-xs-12">
                                <h5 class="txt-light">Concesionario</h5>
                            </div>
                        </div>

                        <!-- Bloques de informacion -->

                        <form action="concesionario.php" enctype="multipart/form-data" id="formulario" method="post">
                            <input name="esSubmit" type="hidden" value="1" />

                            <input name="origen" type="hidden" value="<?php echo $origen ?>" />
                            <input name="origen_municipio" type="hidden" value="<?php echo $origen_municipio ?>" />
                            <input name="origen_publicado" type="hidden" value="<?php echo $origen_publicado ?>" />

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default card-view">
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <div class="alert" id="contenedor_mensaje">
                                                    <span></span>
                                                </div>

                                                <!-- Generales -->

                                                <div class="panel panel-default card-view">
                                                    <div class="panel-heading">
                                                        <div class="pull-left">
                                                            <h6 class="panel-title txt-dark">
                                                                Proporciona la información del concesionario
                                                            </h6>

                                                            <hr />
                                                        </div>

                                                        <div class="clearfix"></div>
                                                        <?php if ((!estaVacio($intelimotor_apiSecret)) && (!estaVacio($intelimotor_apiKey))) {?>
                                                            <div class="form-group mb-0 alinearDerecha">
                                                                <a class="btn btn-success link_sincronizar" href="javascript:;">Sincronizar inventario InteliMotor del concesionario</a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="panel-wrapper collapse in">
                                                        <div class="panel-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-xs-12">
                                                                    <div class="form-wrap">
                                                                        <div class="form-body">
                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Información de control</strong></h5>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Id</label>
                                                                                        <input class="form-control" name="id" readonly type="number" value="<?php echo $id ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Publicado</label>
                                                                                        <div>
                                                                                            <input <?php echo $habilitado == 1 ? "checked" : "" ?> class="form-control bs-switch" data-off-text="No publicado" data-on-text="Publicado" name="habilitado" type="checkbox" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Token de integración con Intelimotor (Apikey) </label>
                                                                                        <input class="form-control" name="intelimotor_apiKey" type="text" value="<?php echo $intelimotor_apiKey ?>"  <?php echo $esUsuarioMaster ? "" : "readonly"; ?> />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Token de integración con Intelimotor (Apisecret) </label>
                                                                                        <input class="form-control" name="intelimotor_apiSecret" type="text" value="<?php echo $intelimotor_apiSecret ?>" <?php echo $esUsuarioMaster ? "" : "readonly"; ?> />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Información general</strong></h5>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Razón Social <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="razonSocial" type="text" value="<?php echo $razonSocial ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">RFC <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="rfc" type="text" value="<?php echo $rfc ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Nombre Comercial <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="nombreComercial" type="text" value="<?php echo $nombreComercial ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Resumen</label>
                                                                                        <input class="form-control" name="resumen" type="text" value="<?php echo $resumen ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Descripción</label>
                                                                                        <input class="form-control" name="descripcion" type="text" value="<?php echo $descripcion ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Calle <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="calle" type="text" value="<?php echo $calle ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Número exterior <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="numeroExterior" type="text" value="<?php echo $numeroExterior ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Número interior</label>
                                                                                        <input class="form-control" name="numeroInterior" type="text" value="<?php echo $numeroInterior ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Colonia <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="colonia" type="text" value="<?php echo $colonia ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Municipio <span class="txt-danger ml-10">*</span></label>
                                                                                        
                                                                                        <select class="form-control select2" name="municipio">
                                                                                            <option value="">Seleccione</option>

                                                                                            <?php
                                                                                                $municipios_BD = consulta($conexion, "SELECT DISTINCT municipio FROM sepomex WHERE idEstado = 19 ORDER BY municipio");

                                                                                                while ($municipioBD = obtenResultado($municipios_BD)) {
                                                                                                    echo "<option " . (!estaVacio($municipio) && $municipio == $municipioBD["municipio"] ? "selected" : "") . " value='" . $municipioBD["municipio"] . "'>" . $municipioBD["municipio"] . "</option>";
                                                                                                }
                                                                                            ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Referencias del domicilio</label>
                                                                                        <input class="form-control" name="referenciasDomicilio" type="text" value="<?php echo $referenciasDomicilio ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Horario</label>
                                                                                        <input class="form-control" name="horario" type="text" value="<?php echo $horario ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Link sitio web</label>
                                                                                        <input class="form-control" name="sitioWeb" type="text" value="<?php echo $sitioWeb ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Link Facebook</label>
                                                                                        <input class="form-control" name="facebook" type="text" value="<?php echo $facebook ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Teléfono de contacto <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control validaSoloNumeros" maxlength="10" name="telefono" type="tel" value="<?php echo $telefono ?>" />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Email</label>
                                                                                        <input class="form-control" name="correoElectronico" type="email" value="<?php echo $correoElectronico ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Número de Whatsapp</label>
                                                                                        <input class="form-control validaSoloNumeros" maxlength="10" name="whatsapp" type="text" value="<?php echo $whatsapp ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Instagram</label>
                                                                                        <input class="form-control" name="instagram" type="text" value="<?php echo $instagram ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Tiktok</label>
                                                                                        <input class="form-control" name="tiktok" type="text" value="<?php echo $tiktok ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Youtube</label>
                                                                                        <input class="form-control" name="youtube" type="text" value="<?php echo $youtube ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Multimedia</strong></h5>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Logo</label>
                                                                                        <div>
                                                                                            <input name="logotipo" type="file" />
                                                                                            <br />
                                                                                            <div class="row">
                                                                                                <div class="col-sm-12">
                                                                                                    <div class="panel panel-success card-view">
                                                                                                        <div class="panel-wrapper collapse in">
                                                                                                            <div class="panel-body">
                                                                                                                <ul class="chat-list-wrap">
                                                                                                                    <li class="chat-list">
                                                                                                                        <div class="chat-body">
                                                                                                                            <?php
                                                                                                                                if (!estaVacio($logotipo)) {
                                                                                                                                    echo "<div class='chat-data'>";
                                                                                                                                    echo "<img class='user-img' src='" . $constante_urlConcesionarios . "/" . $id . "/" . $logotipo . "' />";

                                                                                                                                    echo "<div class='user-data'>";
                                                                                                                                    echo "<span class='name block capitalize-font'>" . $logotipo . "</span>";
                                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                                    echo "<a data-lightbox='imagen' href='" . $constante_urlConcesionarios . "/" . $id . "/" . $logotipo . "'>Ampliar</a>";
                                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                    echo "<a download href='" . $constante_urlConcesionarios . "/" . $id . "/" . $logotipo . "'>Descargar</a>";
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

                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Foto de fachada o foto principal <span class="txt-danger ml-10">*</span></label>
                                                                                        <div>
                                                                                            <input name="fachada" type="file" />
                                                                                            <br />
                                                                                            <div class="row">
                                                                                                <div class="col-sm-12">
                                                                                                    <div class="panel panel-success card-view">
                                                                                                        <div class="panel-wrapper collapse in">
                                                                                                            <div class="panel-body">
                                                                                                                <ul class="chat-list-wrap">
                                                                                                                    <li class="chat-list">
                                                                                                                        <div class="chat-body">
                                                                                                                            <?php
                                                                                                                                if (!estaVacio($fachada)) {
                                                                                                                                    echo "<div class='chat-data'>";
                                                                                                                                    echo "<img class='user-img' src='" . $constante_urlConcesionarios . "/" . $id . "/" . $fachada . "' />";

                                                                                                                                    echo "<div class='user-data'>";
                                                                                                                                    echo "<span class='name block capitalize-font'>" . $fachada . "</span>";
                                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                                    echo "<a data-lightbox='imagen' href='" . $constante_urlConcesionarios . "/" . $id . "/" . $fachada . "'>Ampliar</a>";
                                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                    echo "<a download href='" . $constante_urlConcesionarios . "/" . $id . "/" . $fachada . "'>Descargar</a>";
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

                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Galer&iacute;a de im&aacute;genes de sus instalaciones</label>
                                                                                        <div>
                                                                                            <input id="campo_archivo" multiple name="imagenGaleria[]" type="file" />
                                                                                            <br />
                                                                                            <div class="row">
                                                                                                <div class="col-sm-12">
                                                                                                    <div class="panel panel-success card-view">
                                                                                                        <div class="panel-wrapper collapse in">
                                                                                                            <div class="panel-body">
                                                                                                                <ul class="chat-list-wrap">
                                                                                                                    <li class="chat-list">
                                                                                                                        <div class="chat-body">
                                                                                                                            <?php
                                                                                                                                if (!estaVacio($id)) {
                                                                                                                                    try {
                                                                                                                                        $archivos = scandir($constante_rutaConcesionarios . $id . "/galeria");
                                                                                                                                        $indice = 1;

                                                                                                                                        foreach ($archivos as $archivo) {
                                                                                                                                            $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                                                                                                                                            if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                                                                                                                                                echo "<div class='chat-data' id='contenedor_imagen_" . $indice . "'>";
                                                                                                                                                echo "<img class='user-img' src='" . $constante_urlConcesionarios . "/" . $id . "/galeria/" . $archivo . "' />";

                                                                                                                                                echo "<div class='user-data'>";
                                                                                                                                                echo "<span class='name block capitalize-font'>" . $archivo . "</span>";
                                                                                                                                                echo "<span class='time block txt-grey'>";
                                                                                                                                                echo "<a data-lightbox='imagen' href='" . $constante_urlConcesionarios . "/" . $id . "/galeria/" . $archivo . "'>Ampliar</a>";
                                                                                                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                                echo "<a download href='" . $constante_urlConcesionarios . "/" . $id . "/galeria/" . $archivo . "'>Descargar</a>";
                                                                                                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                                echo "<a href='javascript:eliminaImagenGaleria(" . $id . ", \"" . $archivo . "\", " . $indice . ")'>Eliminar</a>";
                                                                                                                                                echo "</span>";
                                                                                                                                                echo "</div>";
                                                                                                                                                echo "<div class='clearfix'></div>";
                                                                                                                                                echo "</div>";

                                                                                                                                                $indice++;
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    } catch (Exception $e) {
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            ?>
                                                                                                                        </div>
                                                                                                                    </li>
                                                                                                                </ul>

                                                                                                                <div class="alert alert-dismissable" id="contenedor_mensaje" style="display: none">
                                                                                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> <span id="contenedor_mensaje_contenido"></span>
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

                                                                        <div class="form-actions mt-50">
                                                                            <?php if ($usuario_permisoEditarConcesionarios) {?>
                                                                            <button class="btn btn-success" id="boton_guardar" type="button">Guardar</button>
                                                                            <?php } ?>

                                                                            <?php if (!estaVacio($origen)) { ?>
                                                                                <a class="btn btn-default ml-10 link_origen" type="button">Volver</a>
                                                                            <?php } ?>

                                                                            <?php if (!estaVacio($id)) { ?>
                                                                                <!--a class="btn btn-danger ml-50 link_eliminar" type="button">Eliminar</a-->
                                                                            <?php } ?>
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
                        </form>

                        <!-- Formulario de retorno a pagina origen -->

                        <form action="<?php echo $origen ?>" id="formulario_origen" method="post">
                            <input name="esSubmit" type="hidden" value="1" />

                            <input name="municipio" type="hidden" value="<?php echo $origen_municipio ?>" />
                            <input name="publicado" type="hidden" value="<?php echo $origen_publicado ?>" />
                        </form>
                    <?php
                        } else {
                            registraEvento("CMS : Consulta de concesionario bloqueada | id = " . $id);
                            muestraBloqueo();
                        }
                    ?>

                    <?php include("socialware/php/estructura/pieDePagina.php"); ?>
                </div>
            </div>
        </div>

        <?php include("socialware/php/estructura/plugins.php"); ?>

        <!-- Tinymce JavaScript -->
        <script src="vendors/bower_components/tinymce/tinymce.min.js"></script>

        <!-- Tinymce Wysuhtml5 Init JavaScript -->
        <script src="dist/js/tinymce-data.js"></script>

        <!--
         Lightbox
         http://lokeshdhakar.com/projects/lightbox2/
        -->
        <link href="socialware/js/lightbox2-master/dist/css/lightbox.min.css" rel="stylesheet">
        <script src="socialware/js/lightbox2-master/dist/js/lightbox.min.js"></script>

        <?php include("socialware/php/estructura/scripts.php"); ?>

        <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>


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
            });


            $("#boton_guardar").click(function() {
                $("#formulario").submit();
            });


            // Regresa a la interfaz de origen


            $(".link_origen").click(function() {
                $("#formulario_origen").submit();
            });


            // Borra una imagen de la galeria


            function eliminaImagenGaleria(id, imagen, indice) {
                if (confirm("Al continuar se eliminará esta imagen y ya no se mostrará en la galería del concesionario, ¿desea proceder?")) {
                    $.ajax({
                        data: {
                            id: id,
                            imagen: imagen
                        },
                        type: "post",
                        url: "socialware/php/ajax/eliminaImagenGaleriaConcesionarios.php",
                        success: function(resultado) {
                            if (resultado === "ok") {
                                $("#contenedor_imagen_" + indice).hide();
                            }
                        }
                    });
                }
            }

            //Validación de solo numeros para telefono y whatsapp

            $('.validaSoloNumeros').keyup(function(e) {
                if (/\D/g.test(this.value)) {
                    this.value = this.value.replace(/\D/g, '');
                }
            });

            // Sincronizar inventario InteliMotor


            $(".link_sincronizar").click(function() {
                $.LoadingOverlay("show", { minSize: 30, maxSize: 30 });

                $.ajax({
                    data: {
                        idConcesionario: <?php echo estaVacio($id) ? 0 : $id; ?>
                    },
                    type: "post",
                    url: "socialware/php/ajax/sincronizaInventarioInteliMotor.php",
                    success: function(resultado) {
                    console.log("resultado = " + resultado);
                        $("#contenedor_mensaje span").html("El inventario ha sido sincronizado.");
                        $("#contenedor_mensaje").addClass("alert-success");
                        $("#contenedor_mensaje").show();

                        $("html, body").animate({ scrollTop: 0 }, "slow");

                        $.LoadingOverlay("hide");
                    }
                });
            });
        </script>
    </body>
</html>
