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
            $eliminado = sanitiza($conexion, filter_input(INPUT_POST, "eliminado"));
            $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
            $apellidoPaterno = sanitiza($conexion, filter_input(INPUT_POST, "apellidoPaterno"));
            $apellidoMaterno = sanitiza($conexion, filter_input(INPUT_POST, "apellidoMaterno"));
            $curp = sanitiza($conexion, filter_input(INPUT_POST, "curp"));
            $telefono = sanitiza($conexion, filter_input(INPUT_POST, "telefono"));
            $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
            $calle = sanitiza($conexion, filter_input(INPUT_POST, "calle"));
            $numeroExterior = sanitiza($conexion, filter_input(INPUT_POST, "numeroExterior"));
            $numeroInterior = sanitiza($conexion, filter_input(INPUT_POST, "numeroInterior"));
            $colonia = sanitiza($conexion, filter_input(INPUT_POST, "colonia"));
            $estado = sanitiza($conexion, filter_input(INPUT_POST, "estado"));
            $municipio = sanitiza($conexion, filter_input(INPUT_POST, "municipio"));
            if(isset($_POST["idConcesionario"])){
                $idConcesionarioArray = $_POST["idConcesionario"];
                $idConcesionarioLista = implode(",",$_POST["idConcesionario"]);
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

                if (!is_array($idConcesionarioArray)) {
                    $mensaje .= "* Agencia<br />";
                }

                if (estaVacio($nombre)) {
                    $mensaje .= "* Nombre<br />";
                }

                if (estaVacio($apellidoPaterno)) {
                    $mensaje .= "* Apellido Paterno<br />";
                }

                if (estaVacio($apellidoMaterno)) {
                    $mensaje .= "* Apellido Materno<br />";
                }

                if (estaVacio($curp)) {
                    $mensaje .= "* CURP<br />";
                } else if (strlen($curp) != 18) {
                    $mensaje .= "* CURP con tamaño correcto<br />";
                }

                if (estaVacio($telefono)) {
                    $mensaje .= "* Teléfono<br />";
                } else if (strlen($telefono) != 10) {
                    $mensaje .= "* Teléfono con tamaño correcto<br />";
                }

                if (estaVacio($correoElectronico)) {
                    $mensaje .= "* Correo electrónico<br />";
                } else if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
                    $mensaje .= "* Correo electrónico con formato correcto<br />";
                }

                if (estaVacio($calle)) {
                    $mensaje .= "* Calle<br />";
                }

                if (estaVacio($numeroExterior)) {
                    $mensaje .= "* Número exterior<br />";
                }

                if (estaVacio($colonia)) {
                    $mensaje .= "* Colonia<br />";
                }

                if (estaVacio($estado)) {
                    $mensaje .= "* Estado<br />";
                }

                if (estaVacio($municipio)) {
                    $mensaje .= "* Municipio<br />";
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                } else {
                    if (estaVacio($id)) {

                        // Es insercion

                        if (!estaVacio($mensaje)) {
                            $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                        } else {
                            $usuario_BD = consulta($conexion, "SELECT id FROM mensajero WHERE curp = '" . $curp . "'");

                            if (cuentaResultados($usuario_BD) > 0) {
                                $mensaje = "El mensajero ya se encuentra registrado en la base de datos";
                            } else {
                                consulta($conexion, "INSERT INTO mensajero ("
                                        . "habilitado"
                                        . ", nombre"
                                        . ", apellidoPaterno"
                                        . ", apellidoMaterno"
                                        . ", curp"
                                        . ", telefono"
                                        . ", correoElectronico"
                                        . ", calle"
                                        . ", numeroExterior"
                                        . ", numeroInterior"
                                        . ", colonia"
                                        . ", estado"
                                        . ", municipio"
                                    . ") VALUES ("
                                        . $habilitado
                                        . ", '" . $nombre . "'"
                                        . ", '" . $apellidoPaterno . "'"
                                        . ", '" . $apellidoMaterno . "'"
                                        . ", '" . $curp . "'"
                                        . ", '" . $telefono . "'"
                                        . ", '" . $correoElectronico . "'"
                                        . ", '" . $calle . "'"
                                        . ", '" . $numeroExterior . "'"
                                        . ", '" . $numeroInterior . "'"
                                        . ", '" . $colonia . "'"
                                        . ", '" . $estado . "'"
                                        . ", '" . $municipio . "'"
                                    . ")");

                                $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE correoElectronico = '" . $correoElectronico . "'");
                                $mensajero = obtenResultado($mensajero_BD);

                                $id = $mensajero["id"];
                                $habilitado = $mensajero["habilitado"];
                                $eliminado = $mensajero["eliminado"];
                                $nombre = $mensajero["nombre"];
                                $apellidoPaterno = $mensajero["apellidoPaterno"];
                                $apellidoMaterno = $mensajero["apellidoMaterno"];
                                $curp = $mensajero["curp"];
                                $telefono = $mensajero["telefono"];
                                $correoElectronico = $mensajero["correoElectronico"];
                                $calle = $mensajero["calle"];
                                $numeroExterior = $mensajero["numeroExterior"];
                                $numeroInterior = $mensajero["numeroInterior"];
                                $colonia = $mensajero["colonia"];
                                $estado = $mensajero["estado"];
                                $municipio = $mensajero["municipio"];

                                consulta($conexion, "DELETE FROM mensajero_concesionario WHERE idMensajero = " . $id);

                                for($i = 0; $i < count($idConcesionarioArray); $i++){
                                    consulta($conexion, "INSERT INTO mensajero_concesionario ("
                                        . "idMensajero"
                                        . ", idConcesionario"
                                    . ") VALUES ("
                                        . $id
                                        .", " . $idConcesionarioArray[$i]
                                    . ")");
                                }

                                $mensajeroArray_BD = consulta($conexion, "SELECT GROUP_CONCAT(mc.idConcesionario) AS lista FROM mensajero_concesionario mc WHERE mc.idMensajero = " . $id);
                                $mensajeroArray = obtenResultado($mensajeroArray_BD);

                                $idConcesionarioLista = $mensajeroArray["lista"];

                                $mensaje = "ok - El mensajero ha sido registrado";

                                registraEvento("CMS : Alta de mensajero | id = " . $id);
                            }
                        }
                    } else {

                        // Es actualizacion

                        consulta($conexion, "UPDATE mensajero SET "
                            . "habilitado = " . $habilitado
                            . ", nombre = '" . $nombre . "'"
                            . ", apellidoPaterno = '" . $apellidoPaterno . "'"
                            . ", apellidoMaterno = '" . $apellidoMaterno . "'"
                            . ", curp = '" . $curp . "'"
                            . ", telefono = '" . $telefono . "'"
                            . ", correoElectronico = '" . $correoElectronico . "'"
                            . ", calle = '" . $calle . "'"
                            . ", numeroExterior = '" . $numeroExterior . "'"
                            . ", numeroInterior = '" . $numeroInterior . "'"
                            . ", colonia = '" . $colonia . "'"
                            . ", estado = '" . $estado . "'"
                            . ", municipio = '" . $municipio . "'"
                            . " WHERE id = " . $id);

                        $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE id = " . $id);
                        $mensajero = obtenResultado($mensajero_BD);

                        $id = $mensajero["id"];
                        $habilitado = $mensajero["habilitado"];
                        $eliminado = $mensajero["eliminado"];
                        $nombre = $mensajero["nombre"];
                        $apellidoPaterno = $mensajero["apellidoPaterno"];
                        $apellidoMaterno = $mensajero["apellidoMaterno"];
                        $curp = $mensajero["curp"];
                        $telefono = $mensajero["telefono"];
                        $correoElectronico = $mensajero["correoElectronico"];
                        $calle = $mensajero["calle"];
                        $numeroExterior = $mensajero["numeroExterior"];
                        $numeroInterior = $mensajero["numeroInterior"];
                        $colonia = $mensajero["colonia"];
                        $estado = $mensajero["estado"];
                        $municipio = $mensajero["municipio"];
                        $archivo_ine = $mensajero["archivo_ine"];

                        consulta($conexion, "DELETE FROM mensajero_concesionario WHERE idMensajero = " . $id);

                        for($i = 0; $i < count($idConcesionarioArray); $i++){
                            consulta($conexion, "INSERT INTO mensajero_concesionario ("
                                . "idMensajero"
                                . ", idConcesionario"
                            . ") VALUES ("
                                . $id
                                .", " . $idConcesionarioArray[$i]
                            . ")");
                        }

                        $mensajeroArray_BD = consulta($conexion, "SELECT GROUP_CONCAT(mc.idConcesionario) AS lista FROM mensajero_concesionario mc WHERE mc.idMensajero = " . $id);
                        $mensajeroArray = obtenResultado($mensajeroArray_BD);

                        $idConcesionarioLista = $mensajeroArray["lista"];

                        // Carga archivo_ine

                        if (isset($_FILES["archivo_ine"])) {
                            try {
                                $archivo = $_FILES["archivo_ine"];

                                if ($archivo["size"] > 0) {
                                    $nombreEstandarizado = $id . "_ine_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                    $archivoDestino = $constante_rutaMensajeros . "/" . $id . "/" . $nombreEstandarizado;

                                    if (!file_exists($constante_rutaMensajeros . "/" . $id)) {
                                        mkdir($constante_rutaMensajeros . "/" . $id, 0755, true);
                                    }

                                    move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                    consulta($conexion, "UPDATE mensajero SET archivo_ine = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                    $archivo_ine = $nombreEstandarizado;
                                }
                            } catch (Exception $e) {

                            }
                        }

                        $mensaje = "ok - Los cambios han sido guardados";

                        registraEvento("CMS : Actualización de mensajero | id = " . $id);
                    }
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $mensajero_BD = consulta($conexion, "SELECT * FROM mensajero WHERE id = " . $id);
                    $mensajero = obtenResultado($mensajero_BD);

                    $id = $mensajero["id"];
                    $habilitado = $mensajero["habilitado"];
                    $eliminado = $mensajero["eliminado"];
                    $nombre = $mensajero["nombre"];
                    $apellidoPaterno = $mensajero["apellidoPaterno"];
                    $apellidoMaterno = $mensajero["apellidoMaterno"];
                    $curp = $mensajero["curp"];
                    $telefono = $mensajero["telefono"];
                    $correoElectronico = $mensajero["correoElectronico"];
                    $calle = $mensajero["calle"];
                    $numeroExterior = $mensajero["numeroExterior"];
                    $numeroInterior = $mensajero["numeroInterior"];
                    $colonia = $mensajero["colonia"];
                    $estado = $mensajero["estado"];
                    $municipio = $mensajero["municipio"];
                    $archivo_ine = $mensajero["archivo_ine"];

                    $mensajeroArray_BD = consulta($conexion, "SELECT GROUP_CONCAT(mc.idConcesionario) AS lista FROM mensajero_concesionario mc WHERE mc.idMensajero = " . $id);
                    $mensajeroArray = obtenResultado($mensajeroArray_BD);

                    $idConcesionarioLista = $mensajeroArray["lista"]; 

                    registraEvento("CMS : Consulta de mensajero | id = " . $id);
                } else {
                    $habilitado = 1;
                    $eliminado = 1;
                    $nombre = "";
                    $apellidoPaterno = "";
                    $apellidoMaterno = "";
                    $curp = "";
                    $telefono = "";
                    $correoElectronico = "";
                    $calle = "";
                    $numeroExterior = "";
                    $numeroInterior = "";
                    $colonia = "";
                    $estado = "";
                    $municipio = "";


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
                                    <h1 class="page-title">Edición de mensajero</h1>

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
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
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
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_habilitado">Habilitado</label>
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

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_idConcesionario">Agencia <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_idConcesionario" multiple="multiple" name="idConcesionario[]">
                                                                                <?php
                                                                                    $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE habilitado = 1 and eliminado = 0 ORDER BY nombreComercial");

                                                                                    while ($concesionarioBD = obtenResultado($concesionarios_BD)) {
                                                                                        $arrayIdConcesionario = explode(",", $idConcesionarioLista);
                                                                                        if(in_array($concesionarioBD["id"], $arrayIdConcesionario)){
                                                                                            echo "<option selected value='" . $concesionarioBD["id"] . "'>" . $concesionarioBD["nombreComercial"] . "</option>";

                                                                                        }else{
                                                                                            echo "<option value='" . $concesionarioBD["id"] . "'>" . $concesionarioBD["nombreComercial"] . "</option>";
                                                                                        }
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_nombre">Nombre <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_nombre" name="nombre" type="text" value="<?php echo $nombre; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_apellidoPaterno">Apellido paterno <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_apellidoPaterno" name="apellidoPaterno" type="text" value="<?php echo $apellidoPaterno; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_apellidoMaterno">Apellido materno <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_apellidoMaterno" name="apellidoMaterno" type="text" value="<?php echo $apellidoMaterno; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_curp">CURP <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_curp" maxlength="18" name="curp" type="text" value="<?php echo $curp; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_telefono">Teléfono <span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_telefono" maxlength="10" name="telefono" onkeypress='return event.charCode >= 48 && event.charCode <= 57' type="tel" value="<?php echo $telefono; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_correoElectronico">Correo electrónico<span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_correoElectronico" name="correoElectronico" type="email" value="<?php echo $correoElectronico; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_calle">Calle<span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_calle" name="calle" type="text" value="<?php echo $calle; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_numeroExterior">Número exterior<span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_numeroExterior" name="numeroExterior" type="text" value="<?php echo $numeroExterior; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_numeroInterior">Número interior</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_numeroInterior" name="numeroInterior" type="text" value="<?php echo $numeroInterior; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_colonia">Colonia<span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_colonia" name="colonia" type="text" value="<?php echo $colonia; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_estado">Estado <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_estado" name="estado">
                                                                                <option <?php echo estaVacio($estado) ? "selected" : "" ?> value="">Seleccione</option>
                                                                                <?php
                                                                                    $estados_BD = consulta($conexion, "SELECT DISTINCT estado FROM sepomex ORDER BY estado");

                                                                                    while ($estadoBD = obtenResultado($estados_BD)) {
                                                                                        echo "<option " . (!estaVacio($estado) && $estado == $estadoBD["estado"] ? "selected" : "") . " value='" . $estadoBD["estado"] . "'>" . $estadoBD["estado"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-4">
                                                                        <label class="form-label col-md-4" for="campo_municipio">Municipio <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_municipio" name="municipio">
                                                                                <option <?php echo estaVacio($municipio) ? "selected" : "" ?> value="">Seleccione</option>
                                                                                <?php
                                                                                    if(!estaVacio($estado)){
                                                                                        $municipios_BD = consulta($conexion, "SELECT DISTINCT municipio FROM sepomex WHERE estado = '" . $estado . "' ORDER BY municipio");

                                                                                        while ($municipioBD = obtenResultado($municipios_BD)) {
                                                                                            echo "<option " . (!estaVacio($municipio) && $municipio == $municipioBD["municipio"] ? "selected" : "") . " value='" . $municipioBD["municipio"] . "'>" . $municipioBD["municipio"] . "</option>";
                                                                                        }
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php if(!estaVacio($id)) { ?>

                                                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label class="control-label mb-10">PDF con INE por ambos lados</label>

                                                                            <div>
                                                                                <input name="archivo_ine" type="file" />
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
                                                                                                                if (!estaVacio($archivo_ine)) {
                                                                                                                    echo "<div class='chat-data' id='contenedor_archivo_ine'>";
                                                                                                                    echo "<img class='user-img' src='" . $constante_urlMensajeros . "/" . $id . "/" . $archivo_ine . "' />";

                                                                                                                    echo "<div class='user-data'>";
                                                                                                                    echo "<span class='name block'>" . $archivo_ine . "</span>";
                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                    echo "<a download href='" . $constante_urlMensajeros . "/" . $id . "/" . $archivo_ine . "'>Descargar</a>";
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

                                                        <?php } ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-6">
                                        <?php if ($esUsuarioMaster || $usuario_permisoEditarDelegacionVirtual ) { ?>
                                            <div class="col-md-8 col-sm-6" id="contenedor_botonesIzquierdos">
                                                <?php if((!estaVacio($id)) && $eliminado == 0){?>
                                                    <div class="form-group">
                                                        <a class="btn btn-danger mb-3" href="javascript:eliminaMensajero(<?php echo  $id ?>)" id="boton_eliminar">Eliminar</a>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <div class="col-md-4 col-sm-6" id="contenedor_botonesDerechos">
                                                <div class="form-group">
                                                    <button class="btn btn-success mb-3" type="submit" id="boton_guardar">Guardar</button>
                                                    <a class="btn btn-default mb-3" href="javascript:void(0)" id="boton_cerrar">Cerrar</a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                <?php
                    } else {
                        registraEvento("CMS : Consulta de mensajero bloqueada | id = " . $id);
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
            });


            // Regresa a la interfaz de origen


            $(".link_origen").click(function() {
                $("#formulario_origen").submit();
            });

            $("#campo_estado").on("change",function(){
                var estado = $("#campo_estado").val();

                $("#campo_municipio").html("");

                $.ajax({
                    url: "socialware/php/ajax/cargaMunicipios.php",
                    type: "post",
                    data: { estado: estado }
                }).done(function (resultado, textStatus, jqXHR) {
                    $("#campo_municipio").append("<option value=''>Ver todo</option>");
                    $(resultado).find("municipio").each(function (index) {

                        var nombre = $(this).find("nombre").text();

                        $("#campo_municipio").append("<option value='" + nombre + "'>" + nombre + "</option>");
                    });
                    $("#campo_municipio").select2("destroy");
                    $("#campo_municipio").select2();

                });
            });

            // Borra archivo INE
            
            function eliminaArchivoIne(idMensajero) {
                if (confirm("Al continuar se eliminará el archivo, ¿desea proceder?")) {
                    $.ajax({
                        data: {
                            idMensajero: idMensajero
                        },
                        type: "post",
                        url: "socialware/php/ajax/eliminaArchivoIneMensajero.php",
                        success: function(resultado) {
                            if (resultado === "ok") {
                                $("#contenedor_archivo_ine").hide();
                            }
                        }
                    });
                }
            }

            // Elimina mensajero
            
            function eliminaMensajero(idMensajero) {
                if (confirm("Al continuar se eliminará el mensajero, ¿desea proceder?")) {
                    $.ajax({
                        data: {
                            idMensajero: idMensajero
                        },
                        type: "post",
                        url: "socialware/php/ajax/eliminaMensajero.php",
                        success: function(resultado) {
                            if (resultado === "ok") {
                                alert("El mensajero fue eliminado correctamente.");
                                $("#boton_cerrar").click();
                            }
                        }
                    });
                }
            }


        </script>
    </body>
</html>
