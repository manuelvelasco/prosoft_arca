<?php include("socialware/php/comunes/manejaSesion.php"); ?>


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
                    $mensaje .= "* Ya existe una agencia con ese nombre comercial<br />";
                }

                
                if (cuentaResultados($validaRFC_BD) > 0) {
                    $mensaje .= "* Ya existe una agencia con ese RFC<br />";
                }

                if (cuentaResultados($validaRazonSocial_BD) > 0) {
                    $mensaje .= "* Ya existe una agencia con esa razón social<br />";
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

                        $concesionario_BD = consulta($conexion, "SELECT * FROM concesionario WHERE rfc = '" . $rfc . "'");

                        if (cuentaResultados($concesionario_BD) == 0) {
                            $mensaje = "No hemos podido registrar al concesionario, por favor revíse su información e intente de nuevo.";
                        } else {

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

                            $mensaje = "ok - La agencia ha sido registrada.";

                            registraEvento("CMS : Alta de concesionario | id = " . $id);
                        }
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

                    if (!estaVacio($id)) {

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
                                    }
                                }
                            } catch (Exception $e) {
                            }
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

                <?php if ($esUsuarioMaster || $esUsuarioAdministrador || $esUsuarioOperador ) { ?>

                    <div class="main-content app-content mt-0">
                        <div class="side-app">
                            <div class="main-container container-fluid">
                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Proporciona la información de la agencia</h1>

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
                                <form autocomplete="off" enctype="multipart/form-data" id="formulario" method="post" >

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
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_id">ID</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_id" name="id" readonly type="text" value="<?php echo $id; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_habilitado">Publicado</label>
                                                                        <div class="col-md-8">
                                                                            <div class="material-switch">
                                                                                <input <?php echo $habilitado == 1 ? "checked" : "" ?> id="campo_habilitado" name="habilitado" type="checkbox" />
                                                                                <label class="label-info" for="campo_habilitado"></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_intelimotor_apiKey">Token de integración con Intelimotor (Apikey)</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_intelimotor_apiKey" name="intelimotor_apiKey"  type="text" value="<?php echo $intelimotor_apiKey ?>"  <?php echo $esUsuarioMaster ? "" : "readonly"; ?> />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_intelimotor_apiSecret">Token de integración con Intelimotor (Apisecret)</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_intelimotor_apiSecret" name="intelimotor_apiSecret"  type="text" value="<?php echo $intelimotor_apiSecret ?>"  <?php echo $esUsuarioMaster ? "" : "readonly"; ?> />
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
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Información general</strong></h5>

                                                    <hr />

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_razonSocial">Razón Social <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_razonSocial" name="razonSocial" type="text" value="<?php echo $razonSocial; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_rfc">RFC <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_rfc" name="rfc" type="text" value="<?php echo $rfc; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_nombreComercial">Nombre Comercial <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_nombreComercial" name="nombreComercial" type="text" value="<?php echo $nombreComercial; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_resumen">Resumen </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_resumen" name="resumen" type="text" value="<?php echo $resumen; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_descripcion">Descripción </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_descripcion" name="descripcion" type="text" value="<?php echo $descripcion; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_calle">Calle <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_calle" name="calle" type="text" value="<?php echo $calle; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_numeroExterior">Número Exterior <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_numeroExterior" name="numeroExterior" type="text" value="<?php echo $numeroExterior; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_numeroInterior">Número interior </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_numeroInterior" name="numeroInterior" type="text" value="<?php echo $numeroInterior; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_colonia">Colonia <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_colonia" name="colonia" type="text" value="<?php echo $colonia; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_municipio">Municipio</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_municipio" name="municipio">
                                                                                <option <?php echo estaVacio($municipio) ? "selected" : "" ?> value="">Seleccione</option>
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
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_referenciasDomicilio">Referencias domicilio </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_referenciasDomicilio" name="referenciasDomicilio" type="text" value="<?php echo $referenciasDomicilio; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_horario">Horario </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_horario" name="horario" type="text" value="<?php echo $horario; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_sitioWeb">Link sitio web </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_sitioWeb" name="sitioWeb" type="text" value="<?php echo $sitioWeb; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_facebook">Link facebook </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_facebook" name="facebook" type="text" value="<?php echo $facebook; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_telefono">Teléfono de contacto <span class="txt-danger ml-10">*</span> </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control validaSoloNumeros" id="campo_telefono" maxlength="10" name="telefono" type="tel" value="<?php echo $telefono; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_correoElectronico">Email </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_correoElectronico" name="correoElectronico" type="email" value="<?php echo $correoElectronico; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_whatsapp">Número de Whatsapp </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control validaSoloNumeros" id="campo_whatsapp" maxlength="10" name="whatsapp" type="tel" value="<?php echo $whatsapp; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_instagram">Instagram </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_instagram" name="instagram" type="text" value="<?php echo $instagram; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_tiktok">Tiktok </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_tiktok" name="tiktok" type="text" value="<?php echo $tiktok; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_youtube">Youtube </label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_youtube" name="youtube" type="text" value="<?php echo $youtube; ?>" />
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
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5><strong>Multimedia</strong></h5>

                                                    <hr />

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
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
                                                                                        <div class="panel-wrapper in">
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
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
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
                                                                                        <div class="panel-wrapper in">
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
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
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
                                                                                        <div class="panel-wrapper in">
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

                                                                                                                            //if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                                                                                                                            if ($extension !== "") {
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
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="row mt-6">
                                        <div class="col-md-8 col-sm-6" id="contenedor_botonesIzquierdos">
                                            <div class="form-group">
                                                <?php if ((!estaVacio($intelimotor_apiSecret)) && (!estaVacio($intelimotor_apiKey))) {?>
                                                    <a class="btn btn-warning mb-3 link_sincronizar" href="javascript:void(0)">Sincronizar inventario InteliMotor de la agencia</a>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6" id="contenedor_botonesDerechos">
                                            <div class="form-group">
                                                <?php if ($esUsuarioMaster || $esUsuarioAdministrador || $usuario_permisoEditarConcesionarios == 1 ) { ?>
                                                    <button class="btn btn-success mb-3" type="submit" id="boton_guardar">Guardar</button>
                                                <?php } ?>
                                                <a class="btn btn-default mb-3" href="javascript:void(0)" id="boton_cerrar">Cerrar</a>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    } else {
                        registraEvento("CMS : Consulta de concesionarios bloqueada | id = " . $id);
                        muestraBloqueo();
                    }
                ?>

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
                if (confirm("Al continuar se eliminará esta imagen y ya no se mostrará en la galería de la agencia, ¿desea proceder?")) {
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
