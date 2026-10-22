<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <style>
            .header-brand-img {
                max-width: 120px;
            }

            .tabs-menu1 ul li {
                text-align: center; 
                width: 100%
            }
            .panel-primary{
                border: none;
            }
            .tab-pane{
                margin-left: 0;
            }
            .panel{
                box-shadow: none;
            }
            .btn-primary{
                margin-left: 0;
            }
        </style>
    </head>


    <body>

        <div class="login-img">


            <!-- Loader -->


            <div id="global-loader">
                <img alt="Loader" class="loader-img" src="assets/images/loader.svg">
            </div>


            <!-- Contenido especifico de la pagina -->


            <div class="page">
                <div class="">
                    <div class="container-login100">
                        <div class="wrap-login100 p-6">
                            <form class="login100-form validate-form" id="formulario_acceso">
                                <div class="text-center mb-3">
                                    <img alt="" class="header-brand-img" src="../personalizado/img/Logotipo-ARCA-01-Web.png" />
                                </div>

                                <span class="login100-form-title pb-5">
                                    Inicio de sesión
                                </span>

                                <div id="contenedor_mensaje"></div>

                                <div class="panel panel-primary">
                                    <div class="tab-menu-heading">
                                        <div class="tabs-menu1">
                                            <ul class="nav panel-tabs">
                                                <li class="mx-0"><a class="active" data-bs-toggle="tab" href="#tab5">Correo electrónico</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="panel-body tabs-menu-body p-0 pt-5">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab5">
                                                <div class="wrap-input100 validate-input input-group" data-bs-validate="Valid email is required: ex@abc.xyz">
                                                    <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                                        <i class="zmdi zmdi-email text-muted" aria-hidden="true"></i>
                                                    </a>
                                                    <input class="input100 border-start-0 form-control ms-0" id="campo_correoElectronico" name="correoElectronico" placeholder="Correo electrónico" type="email" />
                                                </div>

                                                <div class="wrap-input100 validate-input input-group" id="Password-toggle">
                                                    <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                                        <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                                    </a>
                                                    <input class="input100 border-start-0 form-control ms-0" id="campo_contrasena" name="contrasena" placeholder="Contraseña" type="password" />
                                                </div>

                                                <div class="container-login100-form-btn">
                                                    <a href="#" id="boton_iniciarSesion" class="login100-form-btn btn-primary">
                                                        Acceder
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("socialware/php/estructura/plugins.php"); ?>


        <!-- Scripts -->


        <script>
            $("#boton_iniciarSesion").click(function () {
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
                            $("#contenedor_mensaje").html(resultado);
                            $("#contenedor_mensaje").addClass("alert-danger");
                            $("#contenedor_mensaje").show();
                        }
                    }
                });
            });
        </script>
    </body>
</html>
