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


    <body class="app sidebar-mini ltr">


        <!-- Loader -->


        <div id="global-loader">
            <img alt="Cargando..." class="loader-img" src="assets/images/loader.svg" />
        </div>

        <div class="page">
            <div class="page-main">
                <?php include("socialware/php/estructura/encabezado.php"); ?>

                <?php include("socialware/php/estructura/menu.php"); ?>

                <!-- Contenido -->

                <div class="main-content app-content mt-0">
                    <div class="side-app">
                        <?php if ($esUsuarioMaster || $usuario_permisoConsultarConcesionarios ) { ?>
                            <div class="main-container container-fluid">


                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Agencias</h1>
                                </div>

                                <!-- Filtros -->

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5><strong>Utilice los filtros para detallar su búsqueda</strong></h5>

                                                <hr />

                                                <form action="concesionarios.php" method="post">
                                                    <input name="esSubmit" type="hidden" value="1" />

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_publicado">Publicado</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_publicado" name="publicado">
                                                                                <option <?php echo estaVacio($publicado) ? "selected" : "" ?> value="">Ver todo</option>
                                                                                <option <?php echo $publicado == "1" ? "selected" : "" ?> value="1">Si</option>
                                                                                <option <?php echo $publicado == "0" ? "selected" : "" ?> value="0">No</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_habilitado">Municipio</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_municipio" name="municipio">
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
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <div class="col-md-8">
                                                                            <button class="btn btn-success mb-3" type="submit" id="boton_consultar">Consultar</button>
                                                                        </div>
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


                                <!-- Tabla de resultados -->


                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="text-wrap">
                                                    <div class="d-flex">
                                                        <div class="input-group wd-150" id="contenedor_botones">
                                                            <input class="form-control br-0" id="campo_llaveResultado" placeholder="Buscar..." type="text" />
                                                            <?php if ($esUsuarioMaster || $usuario_permisoEditarConcesionarios) { ?>
                                                                <a class="btn btn-primary" data-fancybox data-type="iframe" data-preload="false" data-height="900" href="concesionario.php" id="boton_agregar">Agregar Agencia</a>
                                                                &nbsp;&nbsp;&nbsp;
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!estaVacio($esSubmit) && $esSubmit === "1") { ?>

                                    <div class="row row-sm">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Resultados</h3>
                                                </div>

                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered text-nowrap key-buttons border-bottom" id="tabla_resultados">
                                                            <thead>
                                                                <tr>
                                                                    <th class="border-bottom-0">Logo</th>
                                                                    <th class="border-bottom-0">Id</th>
                                                                    <th class="border-bottom-0">Publicado</th>
                                                                    <th class="border-bottom-0">Razón Social</th>
                                                                    <th class="border-bottom-0">Nombre Comercial</th>
                                                                    <th class="border-bottom-0">Colonia</th>
                                                                    <th class="border-bottom-0">Municipio</th>
                                                                    <th class="border-bottom-0">Link Sitio Web</th>
                                                                    <th class="border-bottom-0">Teléfono de Contacto</th>
                                                                    <th class="border-bottom-0">Email</th>
                                                                    <th class="border-bottom-0">Número de Whatsapp</th>
                                                                    <th class="border-bottom-0">Acciones</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody id="contenedor_resultados">
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
                                                                                echo "<a data-fancybox data-type='iframe' data-preload='false' data-height='1080' href='concesionario.php?id=" . $concesionario["id"] . "' title='Editar'><i class='fa fa-search'></i></a>";

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
                                    </div>
                                <?php } ?>
                            </div>
                        <?php
                            } else {
                                registraEvento("CMS : Consulta de concesionarios bloqueada");
                                muestraBloqueo();
                            }
                        ?>

                    </div>
                </div>


                <?php include("socialware/php/estructura/pieDePagina.php"); ?>

            </div>
        </div>

        <?php include("socialware/php/estructura/plugins.php"); ?>


        <?php include("socialware/php/estructura/scripts.php"); ?>


        <!-- Scripts -->


        <script>
            $(function() {

                // Inicializa tabla de resultados

                var tablaResultados = $("#tabla_resultados").DataTable({
                    buttons: [
                        {
                            extend: "copy",
                            text: "Copiar",
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                        , {
                            extend: "excel",
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                        , {
                            extend: "pdf",
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            },
                            orientation: "landscape"
                        }
                        , {
                            extend: "colvis",
                            text: "Columnas"
                        }
                    ],
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
                 tablaResultados.buttons().container().appendTo("#contenedor_botones");

                 $("#campo_llaveResultado").keyup(function() {
                    tablaResultados.search($(this).val()).draw();
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
        </script>
    </body>
</html>
