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
            $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
            $nombreLider = sanitiza($conexion, filter_input(INPUT_POST, "nombreLider"));
            $telefonoLider = sanitiza($conexion, filter_input(INPUT_POST, "telefonoLider"));
            $whatsapp = sanitiza($conexion, filter_input(INPUT_POST, "whatsapp"));
            $correoElectronicoLider = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronicoLider"));

            // Parametros enviados por origen

            $origen = sanitiza($conexion, filter_input(INPUT_POST, "origen"));

            // Inicializa variables
            $mensaje = "";
            $fechaActual = date("Y-m-d H:i:s");

            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($nombre)) {
                    $mensaje .= "* Nombre<br />";
                }
                if (estaVacio($nombreLider)) {
                    $mensaje .= "* Nombre del líder<br />";
                }
                $telefonoLider = normalizaTelefono($telefonoLider);
                if (estaVacio($telefonoLider)) {
                    $mensaje .= "* Teléfono líder<br />";
                } else if (strlen($telefonoLider) != 10) {
                    $mensaje .= "* Teléfono líder con formato correcto<br />";
                }
                if (estaVacio($correoElectronicoLider)) {
                    $mensaje .= "* Correo electrónico<br />";
                } else if (!filter_var($correoElectronicoLider, FILTER_VALIDATE_EMAIL)) {
                    $mensaje .= "* Correo electrónico con formato correcto<br />";
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                } else {

                    if(estaVacio($id)){
                        
                        consulta($conexion, "INSERT INTO sucursal ("
                            . "nombre"
                            . ", nombreLider"
                            . ", telefonoLider"
                            . ", correoElectronicoLider"
                            . ", whatsapp"
                        . ") VALUES ("
                            . "'" . $nombre . "'"
                            . ", '" . $nombreLider . "'"
                            . ", '" . $telefonoLider . "'"
                            . ", '" . $correoElectronicoLider . "'"
                            . ", '" . $whatsapp . "'"
                        . ")");

                        $sucursal_BD = consulta($conexion, "SELECT * FROM sucursal WHERE nombre = '" . $nombre . "'");
                        $sucursal = obtenResultado($sucursal_BD);

                        $id = $sucursal["id"];
                        $nombre = $sucursal["nombre"];
                        $nombreLider = $sucursal["nombreLider"];
                        $telefonoLider = $sucursal["telefonoLider"];
                        $correoElectronicoLider = $sucursal["correoElectronicoLider"];
                        $whatsapp = $sucursal["whatsapp"];

                        registraEvento("CMS : Alta de sucursal | id = " . $id);

                        $mensaje = "ok - La sucursal ha sido registrada";

                    } else {
                        // Es actualizacion

                        consulta($conexion, "UPDATE sucursal SET"
                                . "  nombre = '" . $nombre."'"
                                . ", nombreLider = '" . $nombreLider . "'"
                                . ", correoElectronicoLider = '" . $correoElectronicoLider . "'"
                                . ", telefonoLider = '" . $telefonoLider . "'"
                                . ", whatsapp = '" . $whatsapp . "'"
                            . " WHERE id = " . $id);

                        $sucursal_BD = consulta($conexion, "SELECT * FROM sucursal WHERE id = ". $id);
                        $sucursal = obtenResultado($sucursal_BD);

                        $id = $sucursal["id"];
                        $nombre = $sucursal["nombre"];
                        $nombreLider = $sucursal["nombreLider"];
                        $telefonoLider = $sucursal["telefonoLider"];
                        $correoElectronicoLider = $sucursal["correoElectronicoLider"];
                        $whatsapp = $sucursal["whatsapp"];

                        $mensaje = "ok - Los cambios han sido guardados";

                        registraEvento("CMS : Actualización de sucursal | id = " . $id);
                    }

                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $sucursal_BD = consulta($conexion, "SELECT * FROM sucursal WHERE id = ". $id);
                    $sucursal = obtenResultado($sucursal_BD);

                    $id = $sucursal["id"];
                    $nombre = $sucursal["nombre"];
                    $nombreLider = $sucursal["nombreLider"];
                    $telefonoLider = $sucursal["telefonoLider"];
                    $correoElectronicoLider = $sucursal["correoElectronicoLider"];
                    $whatsapp = $sucursal["whatsapp"];

                    registraEvento("CMS : Consulta de sucursal | id = " . $id);
                    
                } else {
                    
                    $id = "";
                    $nombre = "";
                    $nombreLider = "";
                    $telefonoLider = "";
                    $correoElectronicoLider = "";
                    $whatsapp = "";

                }
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
                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>

                        <!-- Titulo -->

                        <div class="row heading-bg bg-blue">
                            <div class="col-xs-12">
                                <h5 class="txt-light">Consulta Sucursal</h5>
                            </div>
                        </div>

                        <!-- Bloques de informacion -->

                        <form action="sucursal.php"  method="post">
                            <input name="esSubmit" type="hidden" value="1" />

                            <input name="origen" type="hidden" value="<?php echo $origen ?>" />

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
                                                                Detalles de la sucursal
                                                            </h6>

                                                            <hr />
                                                        </div>

                                                        <div class="clearfix"></div>
                                                    </div>

                                                    <div class="panel-wrapper collapse in">
                                                        <div class="panel-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-xs-12">
                                                                    <div class="form-wrap">
                                                                        <div class="form-body">
                                                                            <!--?php if (!estaVacio($id)) { ?-->

                                                                                <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">ID</label>
                                                                                            <input class="form-control" readonly type="text" value="<?php echo $id ?>" name="id" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Nombre sucursal</label>
                                                                                            <input class="form-control" type="text" value="<?php echo $nombre ?>" name="nombre" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <!--?php } ?-->

                                                                            <br /><br />

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Datos de contacto</strong></h5>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Nombre Líder<span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="nombreLider" type="text" value="<?php echo $nombreLider ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Telefono Líder <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="telefonoLider" type="text" value="<?php echo $telefonoLider ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Correo Electrónico Líder <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="correoElectronicoLider" type="text" value="<?php echo $correoElectronicoLider ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Whatsapp</label>
                                                                                        <input class="form-control" name="whatsapp" type="text" value="<?php echo $whatsapp ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <br />

                                                                        </div>

                                                                        <div class="form-actions mt-50">
                                                                            <button class="btn btn-success" type="submit">Guardar</button>

                                                                            <?php if (!estaVacio($origen)) { ?>
                                                                                <a class="btn btn-default ml-10 link_origen" type="button">Volver</a>
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
                        </form>
                    <?php
                        } else {
                            registraEvento("CMS : Consulta de sucursal bloqueada | id = " . $id);
                            muestraBloqueo();
                        }
                    ?>

                    <?php include("socialware/php/estructura/pieDePagina.php"); ?>
                </div>
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
        </script>
    </body>
</html>
