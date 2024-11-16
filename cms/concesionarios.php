<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <?php

            // Obtiene parametros de request

            //$esSubmit = sanitiza($conexion, filter_input(INPUT_POST, "esSubmit"));
            $esSubmit = "1";
            $municipio = sanitiza($conexion, filter_input(INPUT_POST, "municipio"));
            $publicado = sanitiza($conexion, filter_input(INPUT_POST, "publicado"));
        ?>
    </head>


    <body>

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
                                <h5 class="txt-light">Concesionarios</h5>
                            </div>
                        </div>

                        <!-- Formulario -->

                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="panel panel-default card-view">
                                    <div class="panel-heading">
                                        <div class="pull-left">
                                            <h6 class="panel-title txt-dark">Utilice los filtros para detallar su búsqueda</h6>
                                            <hr />
                                        </div>

                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="panel-wrapper collapse in">
                                        <div class="panel-body">
                                            <div class="alert" id="contenedor_mensaje">
                                                <span></span>
                                            </div>

                                            <div class="form-wrap">
                                                <form action="concesionarios.php" method="post">
                                                    <input name="esSubmit" type="hidden" value="1" />

                                                    <div class="form-body">
                                                        <div class="row mb-30">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Publicado</label>
                                                                    <select class="form-control select2" id="campo_publicado" name="publicado">
                                                                        <option <?php echo estaVacio($publicado) ? "selected" : "" ?> value="">Ver todo</option>
                                                                        <option <?php echo $publicado == "1" ? "selected" : "" ?> value="1">Si</option>
                                                                        <option <?php echo $publicado == "0" ? "selected" : "" ?> value="0">No</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Municipio</label>
                                                                    <select class="form-control select2" id="campo_municipio" name="municipio">
                                                                        <option <?php echo estaVacio($municipio) ? "selected" : "" ?> value="">Ver todo</option>

                                                                        <?php
                                                                            $municipios_BD = consulta($conexion, "SELECT DISTINCT municipio FROM sepomex WHERE idEstado = 19 ORDER BY municipio");

                                                                            while ($municipio_BD = obtenResultado($municipios_BD)) {
                                                                                echo "<option " . ($municipio_BD["municipio"] == $municipio ? "selected" : "") . " value='" . $municipio_BD["municipio"] . "'>" . $municipio_BD["municipio"] . "</option>";
                                                                            }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-actions mt-10">
                                                        <button class="btn btn-primary" id="boton_consultar" type="submit">Consultar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de resultados -->

                        <?php if (!estaVacio($esSubmit) && $esSubmit === "1") { ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default card-view">
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <div class="table-wrap">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover display  pb-30" id="tabla_resultados">
                                                            <thead>
                                                                <tr>
                                                                    <th>Logo</th>
                                                                    <th>ID</th>
                                                                    <th>Publicado</th>
                                                                    <th>Razón Social</th>
                                                                    <th>Nombre Comercial</th>
                                                                    <th>Colonia</th>
                                                                    <th>Municipio</th>
                                                                    <th>Link Sitio Web</th>
                                                                    <th>Teléfono de Contacto</th>
                                                                    <th>Email</th>
                                                                    <th>Número de Whatsapp</th>
                                                                    <th class="columna_acciones">Acciones</th>
                                                                </tr>
                                                            </thead>

                                                            <tfoot>
                                                                <tr>
                                                                    <th>Logo</th>
                                                                    <th>ID</th>
                                                                    <th>Publicado</th>
                                                                    <th>Razón Social</th>
                                                                    <th>Nombre Comercial</th>
                                                                    <th>Colonia</th>
                                                                    <th>Municipio</th>
                                                                    <th>Link Sitio Web</th>
                                                                    <th>Teléfono de Contacto</th>
                                                                    <th>Email</th>
                                                                    <th>Número de Whatsapp</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </tfoot>

                                                            <tbody>
                                                                <?php

                                                                    // Arma restricciones

                                                                    $restricciones = "";

                                                                    if (!estaVacio($publicado)) {
                                                                        $restricciones .= " AND c.habilitado = " . $publicado;
                                                                    }

                                                                    if (!estaVacio($municipio)) {
                                                                        $restricciones .= " AND c.municipio = '" . $municipio ."'";
                                                                    }

                                                                    if ($esUsuarioOperador) {
                                                                        $restricciones .= " AND c.id = " . $usuario_idConcesionario;
                                                                    }

                                                                    // Consulta base de datos

                                                                    $concesionarios_BD = consulta($conexion, "SELECT"
                                                                            . " c.*"
                                                                        . " FROM"
                                                                            . " concesionario c"
                                                                        . " WHERE 1 = 1 " . $restricciones
                                                                        . " ORDER BY id");

                                                                    while ($concesionario = obtenResultado($concesionarios_BD)) {
                                                                        echo "<tr>";
                                                                            echo "<td><img src='" . $constante_urlConcesionarios . $concesionario["id"] . "/" . $concesionario["logotipo"] . "' class='icono_listado'/></td>";
                                                                            echo "<td>" . $concesionario["id"] . "</td>";
                                                                            echo "<td>" . ($concesionario["habilitado"] == 1 ? " Si " : " No ") . "</td>";
                                                                            echo "<td>" . $concesionario["razonSocial"] . "</td>";
                                                                            echo "<td>" . $concesionario["nombreComercial"] . "</td>";
                                                                            echo "<td>" . $concesionario["colonia"] . "</td>";
                                                                            echo "<td>" . $concesionario["municipio"] . "</td>";
                                                                            echo "<td>" . $concesionario["sitioWeb"] . "</td>";
                                                                            echo "<td>" . $concesionario["telefono"] . "</td>";
                                                                            echo "<td>" . $concesionario["correoElectronico"] . "</td>";
                                                                            echo "<td>" . $concesionario["whatsapp"] . "</td>";
                                                                            echo "<td>";
                                                                                echo "<a class='link_editar' data-concesionario='" . $concesionario["id"] . "'  data-toggle='tooltip' href='javascript:;' title='Ver detalle'><i class='fa fa-search'></i></a>";

                                                                                /*if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                                    //echo "<a class='link_publicar' data-id='" . $concesionario["id"] . "'  data-habilitado='" . $concesionario["habilitado"] . "' data-toggle='tooltip' href='javascript:;' title='" . ($concesionario["habilitado"] == 1 ? "Ocultar" : "Publicar") . "'><i class='fa fa-" . ($concesionario["habilitado"] == 1 ? "eye-slash" : "eye") . "'></i></a>";
                                                                                    echo "<a class='link_publicar' data-id='" . $concesionario["id"] . "'  data-habilitado='" . $concesionario["habilitado"] . "' data-toggle='tooltip' href='javascript:;' style='color: " . ($concesionario["habilitado"] == 1 ? "#2f2c2c" : "#bfbcbc") . "' title='" . ($concesionario["habilitado"] == 1 ? "Dejar de publicar" : "Publicar") . "'><i class='fa fa-globe'></i></a>";
                                                                                }*/

                                                                            echo "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    registraEvento("CMS : Consulta de Concesionarios");
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <br />
                                        <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                                            <div class="form-group mb-0">
                                                <a class="btn btn-success link_agregar" href="javascript:;">Agregar Concesionario</a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Formulario de redireccion hacia edicion -->

                        <form action="concesionario.php" id="formulario_edicion" method="post">
                            <input name="origen" type="hidden" value="concesionarios.php" />

                            <!-- Ida -->

                            <input id="campo_edicion_id" name="id" type="hidden" />

                            <!-- Regreso -->

                            <input id="campo_edicion_municipio" name="origen_municipio" type="hidden" value="" />
                            <input id="campo_edicion_publicado" name="origen_publicado" type="hidden" value="" />
                        </form>
                    <?php
                        } else {
                            registraEvento("CMS : Consulta de concesionarios bloqueada");
                            muestraBloqueo();
                        }
                    ?>

                    <?php include("socialware/php/estructura/pieDePagina.php"); ?>
                </div>
            </div>
        </div>

        <?php include("socialware/php/estructura/plugins.php"); ?>

        <?php include("socialware/php/estructura/scripts.php"); ?>

        <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>


        <!-- Scripts -->


        <script>
            $(function() {

                // Inicializa tabla de resultados

                $("#tabla_resultados").DataTable({
                    order: [[0, "desc"]],
                    dom: "Bfrtip",
                    buttons: [
                        "copy", 
                        "excel", {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'LEGAL' 
                        }, 
                        "print"
                    ],
                    language: {
                        decimal: "",
                        emptyTable: "No hay información",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "Mostrando 0 to 0 of 0 registros",
                        infoFiltered: "(Filtrado de _MAX_ total registros)",
                        infoPostFix: "",
                        lengthMenu: "Mostrar _MENU_ registros",
                        loadingRecords: "Cargando...",
                        processing: "Procesando...",
                        search: "Buscar:",
                        thousands: ",",
                        zeroRecords: "No se han encontrado resultados",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        },
                        buttons: {
                            copy: "Copiar",
                            excel: "Excel",
                            pdf: "PDF",
                            print: "Imprimir"
                        }
                    }
                });
            });


            // Redirige hacia alta


            $(".link_agregar").click(function() {
                $("#formulario_edicion").submit();
            });


            // Redirige hacia edicion


            $(".link_editar").click(function() {
                $("#campo_edicion_id").val($(this).attr("data-concesionario"));

                $("#campo_edicion_municipio").val($("#campo_municipio").val());
                $("#campo_edicion_publicado").val($("#campo_publicado").val());

                $("#formulario_edicion").submit();
            });


            // Publica / Oculta un concesionario


            /*$(".link_publicar").click(function() {
                var id = $(this).attr("data-id");
                var publicado = $(this).attr("data-publicado");

                if (publicado == 1) {
                    if (confirm("Al continuar se dejara de mostrar este vehículo en el sitio web, ¿desea proceder?")) {
                        habilitarConcesionario(id, "0");
                    }
                } else {
                    habilitarConcesionario(id, "1");
                }
            });*/


            /*function habilitarConcesionario(id, publicado) {
                $.ajax({
                    data: {
                        id: id,
                        publicado: publicado
                    },
                    type: "post",
                    url: "socialware/php/ajax/habilitaConcesionario.php",
                    success: function(resultado) {
                        location.reload();
                    }
                });
            }*/
        </script>
    </body>
</html>
