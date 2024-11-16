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
            $rol = sanitiza($conexion, filter_input(INPUT_POST, "rol"));
            $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
            $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
            $contrasena = sanitiza($conexion, filter_input(INPUT_POST, "contrasena"));
            $permisoConsultarVehiculos = sanitiza($conexion, filter_input(INPUT_POST, "permisoConsultarVehiculos"));
            $permisoEditarVehiculos = sanitiza($conexion, filter_input(INPUT_POST, "permisoEditarVehiculos"));
            $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));

            // Parametros enviados por origen

            $origen = sanitiza($conexion, filter_input(INPUT_POST, "origen"));

            // Inicializa variables

            $mensaje = "";
            $fechaActual = date("Y-m-d H:i:s");
            $habilitado = estaVacio($habilitado) ? 0 : 1;
            $permisoConsultarVehiculos = estaVacio($permisoConsultarVehiculos) ? 0 : 1;
            $permisoEditarVehiculos = estaVacio($permisoEditarVehiculos) ? 0 : 1;

            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($rol)) {
                    $mensaje .= "* Rol<br />";
                }

                if (estaVacio($nombre)) {
                    $mensaje .= "* Nombre<br />";
                }

                if (estaVacio($correoElectronico)) {
                    $mensaje .= "* Correo electrónico<br />";
                } else if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
                    $mensaje .= "* Correo electrónico con formato correcto<br />";
                }

                if (!estaVacio($id)) {
                    $usuario_BD = consulta($conexion, "SELECT id FROM usuario WHERE correoElectronico = '" . $correoElectronico . "' AND id <> " . $id);

                    if (cuentaResultados($usuario_BD) > 0) { 
                        $mensaje .="Ese correo ya se esta utilizando por otro usuario <br />";
                    }
                }

                if (estaVacio($idConcesionario) && $rol == 'Operador') {
                    $mensaje .="Para el rol Operador, es obligatorio asignarle un concesionario <br />";
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                } else {
                    if (estaVacio($id)) {

                        // Es insercion

                        if (estaVacio($contrasena)) {
                            $mensaje .= "* Contraseña<br />";
                        }

                        if (!estaVacio($mensaje)) {
                            $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                        } else {
                            $usuario_BD = consulta($conexion, "SELECT id FROM usuario WHERE correoElectronico = '" . $correoElectronico . "'");

                            if (cuentaResultados($usuario_BD) > 0) {
                                $mensaje = "El usuario ya se encuentra registrado en la base de datos";
                            } else {
                                consulta($conexion, "INSERT INTO usuario ("
                                        . "habilitado"
                                        . ", rol"
                                        . ", nombre"
                                        . ", correoElectronico"
                                        . ", contrasena"
                                        . ", permisoConsultarVehiculos"
                                        . ", permisoEditarVehiculos"
                                        . ", idConcesionario"
                                    . ") VALUES ("
                                        . "1"
                                        . ", '" . $rol . "'"
                                        . ", '" . $nombre . "'"
                                        . ", '" . $correoElectronico . "'"
                                        . ", '" . md5($contrasena) . "'"
                                        . ", " . $permisoConsultarVehiculos
                                        . ", " . $permisoEditarVehiculos
                                        . ", " . (estaVacio($idConcesionario) ? "NULL" : $idConcesionario)
                                    . ")");

                                $usuario_BD = consulta($conexion, "SELECT * FROM usuario WHERE correoElectronico = '" . $correoElectronico . "'");
                                $usuario = obtenResultado($usuario_BD);

                                $id = $usuario["id"];
                                $habilitado = $usuario["habilitado"];
                                $rol = $usuario["rol"];
                                $nombre = $usuario["nombre"];
                                $correoElectronico = $usuario["correoElectronico"];
                                $contrasena = "";
                                $permisoConsultarVehiculos = $usuario["permisoConsultarVehiculos"];
                                $permisoEditarVehiculos = $usuario["permisoEditarVehiculos"];
                                $idConcesionario = $usuario["idConcesionario"];

                                $mensaje = "ok - El usuario ha sido registrado";

                                registraEvento("CMS : Alta de usuario | id = " . $id);
                            }
                        }
                    } else {

                        // Es actualizacion

                        consulta($conexion, "UPDATE usuario SET "
                            . "habilitado = " . $habilitado
                            . ", rol = '" . $rol . "'"
                            . ", nombre = '" . $nombre . "'"
                            . ", correoElectronico = '" . $correoElectronico . "'"
                            . ", permisoConsultarVehiculos = " . $permisoConsultarVehiculos
                            . ", permisoEditarVehiculos = " . $permisoEditarVehiculos
                            . ", idConcesionario = " . (estaVacio($idConcesionario) ? "NULL" : $idConcesionario)
                            . " WHERE id = " . $id);

                        if (!estaVacio($contrasena)) {
                            consulta($conexion, "UPDATE usuario SET "
                                . "contrasena = '" . md5($contrasena) . "'"
                                . " WHERE id = " . $id);
                        }

                        $usuario_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $id);
                        $usuario = obtenResultado($usuario_BD);

                        $id = $usuario["id"];
                        $habilitado = $usuario["habilitado"];
                        $rol = $usuario["rol"];
                        $nombre = $usuario["nombre"];
                        $correoElectronico = $usuario["correoElectronico"];
                        $contrasena = "";
                        $permisoConsultarVehiculos = $usuario["permisoConsultarVehiculos"];
                        $permisoEditarVehiculos = $usuario["permisoEditarVehiculos"];
                        $idConcesionario = $usuario["idConcesionario"];

                        $mensaje = "ok - Los cambios han sido guardados";

                        registraEvento("CMS : Actualización de usuario | id = " . $id);
                    }
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $usuario_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $id);
                    $usuario = obtenResultado($usuario_BD);

                    $id = $usuario["id"];
                    $habilitado = $usuario["habilitado"];
                    $rol = $usuario["rol"];
                    $nombre = $usuario["nombre"];
                    $correoElectronico = $usuario["correoElectronico"];
                    $contrasena = "";
                    $permisoConsultarVehiculos = $usuario["permisoConsultarVehiculos"];
                    $permisoEditarVehiculos = $usuario["permisoEditarVehiculos"];
                    $idConcesionario = $usuario["idConcesionario"];

                    registraEvento("CMS : Consulta de usuario | id = " . $id);
                } else {
                    $habilitado = 1;
                    $rol = "";
                    $nombre = "";
                    $correoElectronico = "";
                    $contrasena = "";
                    $permisoConsultarVehiculos = 0;
                    $permisoEditarVehiculos = 0;
                    $idConcesionario = 0;
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
                                <h5 class="txt-light">Edición de Usuario</h5>
                            </div>
                        </div>

                        <!-- Bloques de informacion -->

                        <form action="usuario.php" enctype="multipart/form-data" method="post">
                            <input name="esSubmit" type="hidden" value="1" />
                            <input name="id" type="hidden" value="<?php echo $id ?>" />

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
                                                                Proporciona la información del usuario
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
                                                                                        <h5><strong>Información de control</strong></h5>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mb-30">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Id</label>
                                                                                            <input class="form-control" readonly type="text" value="<?php echo $id ?>" />
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Habilitado</label>
                                                                                            <div>
                                                                                                <input <?php echo $habilitado == 1 ? "checked" : "" ?> class="form-control bs-switch" data-off-text="Inhabilitado" data-on-text="Habilitado" name="habilitado" type="checkbox" />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <!--?php } ?-->

                                                                            <br /><br />

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Datos generales</strong></h5>
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
                                                                                        <label class="control-label mb-10">Rol <span class="txt-danger ml-10">*</span></label>
                                                                                        <select class="form-control select2" name="rol">
                                                                                            <option <?php echo estaVacio($rol) ? "selected" : "" ?> value="">Seleccione</option>

                                                                                            <?php if ($esUsuarioMaster) { ?>
                                                                                                <option <?php echo ($rol == "Master") ? "selected" : "" ?> value="Master">Master</option>
                                                                                            <?php } ?>
                                                                                            <option <?php echo ($rol == "Administrador") ? "selected" : "" ?> value="Administrador">Administrador</option>
                                                                                            <option <?php echo ($rol == "Operador") ? "selected" : "" ?> value="Operador">Operador</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Concesionario </label>
                                                                                        <select class="form-control select2" name="idConcesionario">
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
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Correo electrónico <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="correoElectronico" type="text" value="<?php echo $correoElectronico ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Contraseña</label>
                                                                                        <input class="form-control" name="contrasena" type="text" value="<?php echo $contrasena ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <br /><br />

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
                            registraEvento("CMS : Consulta de usuario bloqueada | id = " . $id);
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
