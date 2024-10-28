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
            $idVehiculo = sanitiza($conexion, filter_input(INPUT_POST, "idVehiculo"));
            $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
            $apellido = sanitiza($conexion, filter_input(INPUT_POST, "apellido"));
            $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
            $telefono = sanitiza($conexion, filter_input(INPUT_POST, "telefono"));
            $enganche = sanitiza($conexion, filter_input(INPUT_POST, "enganche"));
            $plazo = sanitiza($conexion, filter_input(INPUT_POST, "plazo"));
            $pagoMensual = sanitiza($conexion, filter_input(INPUT_POST, "pagoMensual"));
            $montoCredito = sanitiza($conexion, filter_input(INPUT_POST, "montoCredito"));
            $montoMaximoCredito = sanitiza($conexion, filter_input(INPUT_POST, "montoMaximoCredito"));
            $correoElectronicoLider = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronicoLider"));
            $telefonoLider = sanitiza($conexion, filter_input(INPUT_POST, "telefonoLider"));
            $nombreLider = sanitiza($conexion, filter_input(INPUT_POST, "nombreLider"));
            $permisoConsultarVehiculos = sanitiza($conexion, filter_input(INPUT_POST, "permisoConsultarVehiculos"));
            $permisoEditarVehiculos = sanitiza($conexion, filter_input(INPUT_POST, "permisoEditarVehiculos"));

            // Parametros enviados por origen

            $origen = sanitiza($conexion, filter_input(INPUT_POST, "origen"));

            // Inicializa variables

            $mensaje = "";
            $fechaActual = date("Y-m-d H:i:s");
            $permisoConsultarVehiculos = estaVacio($permisoConsultarVehiculos) ? 0 : 1;
            $permisoEditarVehiculos = estaVacio($permisoEditarVehiculos) ? 0 : 1;

            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($nombre)) {
                    $mensaje .= "* Nombre<br />";
                }
                if (estaVacio($nombreLider)) {
                    $mensaje .= "* Nombre del líder<br />";
                }
                if (estaVacio($telefonoLider)) {
                    $mensaje .= "* Teléfono líder<br />";
                }
                if (estaVacio($correoElectronicoLider)) {
                    $mensaje .= "* Correo electrónico del líder<br />";
                }
                if (estaVacio($correoElectronico)) {
                    $mensaje .= "* Correo electrónico<br />";
                } else if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
                    $mensaje .= "* Correo electrónico con formato correcto<br />";
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                } else {

                    // Es actualizacion
                    $titulo = "Nueva solicitud de Financiamiento";
                    $mensaje = $nombreLider . ", se ha recibido una nueva solicitud de financiamiento con los siguientes datos:"
                             . "<br/>Cliente: ". $nombre . " ". $apellido
                             . "<br/>Teléfono: ". $telefono
                             . "<br/>Correo electrónico: ". $correoElectronico
                             . "<br/><br/> Importe de enganche: ".$enganche
                             . "<br/> Plazo: ". $plazo
                             . "<br/> Pago mensual: ". $pagoMensual;

                    $enviaCorreo = enviaCorreo($correoElectronicoLider, $titulo, $mensaje);

                    $enviaSMS = enviaSms( $telefono, "Se ha recibido una nueva solicitud de financiamiento. Ingrese al CMS para ver los detalles.");

                    $financiamiento_BD = consulta($conexion, "SELECT f.*,
                                                                     v.idSucursal,
                                                                     s.*
                                                                FROM financiamiento f
                                                                JOIN vehiculo v  ON v.id = f.idVehiculo
                                                                LEFT JOIN sucursal s ON s.id = v.idSucursal
                                                                WHERE f.id = " . $id);

                    $financiamiento = obtenResultado($financiamiento_BD);

                    $id = $financiamiento["id"];
                    $idVehiculo = $financiamiento["idVehiculo"];
                    $nombre = $financiamiento["nombre"];
                    $apellido = $financiamiento["apellido"];
                    $correoElectronico = $financiamiento["correoElectronico"];
                    $telefono = $financiamiento["telefono"];
                    $enganche = $financiamiento["enganche"];
                    $plazo = $financiamiento["plazo"];
                    $pagoMensual = $financiamiento["pagoMensual"];
                    $montoCredito = $financiamiento["montoCredito"];
                    $montoMaximoCredito = $financiamiento["montoMaximoCredito"];

                    $mensaje = "ok - Los cambios han sido guardados";

                    registraEvento("CMS : Actualización de financiamiento | id = " . $id);
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $financiamiento_BD = consulta($conexion, "SELECT f.*,
                                                                     v.idSucursal,
                                                                     s.*
                                                                FROM financiamiento f
                                                                JOIN vehiculo v  ON v.id = f.idVehiculo
                                                                JOIN sucursal s ON s.id = v.idSucursal
                                                                WHERE f.id = " . $id);
                    $financiamiento = obtenResultado($financiamiento_BD);

                    $id = $financiamiento["id"];
                    $idVehiculo = $financiamiento["idVehiculo"];
                    $nombre = $financiamiento["nombre"];
                    $apellido = $financiamiento["apellido"];
                    $correoElectronico = $financiamiento["correoElectronico"];
                    $telefono = $financiamiento["telefono"];
                    $enganche = $financiamiento["enganche"];
                    $plazo = $financiamiento["plazo"];
                    $pagoMensual = $financiamiento["pagoMensual"];
                    $montoCredito = $financiamiento["montoCredito"];
                    $montoMaximoCredito = $financiamiento["montoMaximoCredito"];
                    $nombreLider = $financiamiento["nombreLider"];
                    $telefonoLider = $financiamiento["telefonoLider"];
                    $correoElectronicoLider = $financiamiento["correoElectronicoLider"];

                    registraEvento("CMS : Consulta de financiamiento | id = " . $id);
                } else {
                    $idVehiculo = 0;
                    $nombre ="";
                    $apellido = "";
                    $correoElectronico = "";
                    $telefono = "";
                    $enganche = 0;
                    $plazo = 0;
                    $pagoMensual = 0;
                    $montoCredito = 0;
                    $montoMaximoCredito = 0;
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
                                <h5 class="txt-light">Consulta Financiamiento</h5>
                            </div>
                        </div>

                        <!-- Bloques de informacion -->

                        <form action="financiamiento.php" enctype="multipart/form-data" method="post">
                            <input name="esSubmit" type="hidden" value="1" />
                            <input name="id" type="hidden" value="<?php echo $id ?>" />

                            <input name="origen" type="hidden" value="<?php echo $origen ?>" />
                            <input name="correoElectronicoLider" type="hidden" value="<?php echo $correoElectronicoLider ?>" />
                            <input name="telefonoLider" type="hidden" value="<?php echo $telefonoLider ?>" />
                            <input name="nombreLider" type="hidden" value="<?php echo $nombreLider ?>" />

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
                                                                Detalles del financiamiento
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
                                                                                    <div class="col-md-12">
                                                                                        <h5><strong>Datos de contacto</strong></h5>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">ID Vehículo</label>
                                                                                            <input class="form-control" readonly type="text" value="<?php echo $idVehiculo ?>" name="idVehiculo" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Importe de enganche</label>
                                                                                            <input class="form-control" readonly type="text" value="<?php echo $enganche ?>" name="enganche" />
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Plazo</label>
                                                                                            <input class="form-control" readonly type="text" value="<?php echo $plazo ?>" name="plazo" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Pago mensual</label>
                                                                                            <input class="form-control" readonly type="text" value="<?php echo $pagoMensual ?>" name="pagoMensual" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <!--?php } ?-->

                                                                            <br /><br />

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Datos del cliente</strong></h5>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Nombre <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="nombre" type="text" value="<?php echo $nombre ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Apellido</label>
                                                                                        <input class="form-control" name="apellido" type="text" value="<?php echo $apellido ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Teléfono <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="telefono" type="text" value="<?php echo $telefono ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Correo electrónico <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="correoElectronico" type="text" value="<?php echo $correoElectronico ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <br />

                                                                        </div>

                                                                        <div class="form-actions mt-50">
                                                                            <button class="btn btn-success" type="submit">Enviar</button>

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
                            registraEvento("CMS : Consulta de financiamiento bloqueada | id = " . $id);
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
