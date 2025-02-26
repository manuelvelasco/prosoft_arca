<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>
    </head>


    <body>

        <!-- Preloader -->

        <div class="preloader-it">
            <div class="la-anim-1"></div>
        </div>

        <div class="wrapper pa-0">

            <!-- Contenido -->

            <div class="page-wrapper pa-0 ma-0">
                <div class="container-fluid">
                    <div class="table-struct full-width full-height">
                        <div class="table-cell vertical-align-middle">
                            <div class="auth-form  ml-auto mr-auto no-float">
                                <div class="panel panel-default card-view mb-0">
                                    <div class="panel-heading">
                                        <div style="text-align: center; width: 100%">
                                            <img src="../personalizado/img/Logotipo-ARCA-01-Web.png" style="width: 150px" />
                                        </div>

                                        <br />

                                        <div class="pull-left">
                                            <h6 class="panel-title txt-dark">Proporcione sus datos de acceso</h6>
                                        </div>

                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="panel-wrapper collapse in">
                                        <div class="panel-body">
                                            <div class="alert" id="contenedor_mensaje">
                                                <span></span>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <div class="form-wrap">
                                                        <form id="formulario_acceso">
                                                            <div class="form-group">
                                                                <label class="control-label mb-10" for="correoElectronico">Correo electrónico</label>

                                                                <div class="input-group">
                                                                    <input autocomplete="off" class="form-control" name="correoElectronico" required="" type="text">
                                                                    <div class="input-group-addon"><i class="icon-envelope"></i></div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="control-label mb-10" for="contrasena">Contraseña</label>

                                                                <div class="input-group">
                                                                    <input class="form-control" name="contrasena" required="" type="password">
                                                                    <div class="input-group-addon"><i class="icon-lock"></i></div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <button class="btn btn-primary btn-block" id="submit_acceso" type="button">Iniciar sesión</button>
                                                            </div>
                                                        </form>
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

        <?php include("socialware/php/estructura/plugins.php"); ?>


        <!-- Scripts -->


        <script>
            $("#submit_acceso").click(function () {
                $("#contenedor_mensaje").hide();
                $("#contenedor_mensaje").removeClass("alert-danger");

                $.ajax({
                    data: $("#formulario_acceso").serialize(),
                    type: "post",
                    url: "socialware/php/ajax/iniciaSesion.php",
                    success: function(resultado) {
                        if (resultado === "ok") {
                            window.location.replace("inicio.php");
                        } else {
                            $("#contenedor_mensaje span").html(resultado);
                            $("#contenedor_mensaje").addClass("alert-danger");
                            $("#contenedor_mensaje").show();
                        }
                    }
                });
            });
        </script>
    </body>
</html>
