<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>
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
                        <?php if ($esUsuarioMaster || $esUsuarioAdministrador || ($esUsuarioOperador && $usuario_permisoConsultarVehiculos)) { ?>
                            <div class="main-container container-fluid">


                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Blogs</h1>
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
                                                            <a class="btn btn-primary" data-fancybox data-type="iframe" data-preload="false" data-height="900" href="blog.php" id="boton_agregar">Agregar blog</a>
                                                            &nbsp;&nbsp;&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                                <th class="border-bottom-0">Id</th>
                                                                <th class="border-bottom-0">Fecha de Publicación</th>
                                                                <th class="border-bottom-0">Título</th>
                                                                <th class="border-bottom-0">Publicado</th>
                                                                <th class="border-bottom-0">Acciones</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="contenedor_resultados">
                                                            <?php

                                                                    $blogs_BD = consulta($conexion, "SELECT * FROM post ORDER BY fecha DESC");

                                                                    while ($blog = obtenResultado($blogs_BD)) {
                                                                        echo "<tr>";
                                                                            echo "<td>" . $blog["id"] . "</td>";
                                                                            echo "<td>" . $blog["fecha"] . "</td>";
                                                                            echo "<td>" . $blog["titulo"] . "</td>";
                                                                            echo "<td>";
                                                                            if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                                echo "<a class='link_habilitar' data-id='" . $blog["id"] . "'  data-habilitado='" . $blog["habilitado"] . "' data-toggle='tooltip' href='javascript:;' style='color: " . ($blog["habilitado"] == 1 ? "#2f2c2c" : "#bfbcbc") . "'  title='" . ($blog["habilitado"] == 1 ? "Dejar de publicar" : "Publicar") . "'><i class='fa fa-globe'></i></a>";
                                                                            }
                                                                            echo "</td>";
                                                                            echo "<td>";
                                                                                echo "<a class='link_editar' data-blog='" . $blog["id"] . "'  data-toggle='tooltip' href='javascript:;' title='Ver detalle'><i class='fa fa-search'></i></a>";

                                                                            echo "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    registraEvento("CMS : Consulta de posts");
                                                                ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            } else {
                                registraEvento("CMS : Consulta de posts bloqueada");
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

            $(".link_habilitar").click(function() {
                var id = $(this).attr("data-id");
                var habilitado = $(this).attr("data-habilitado");

                if (habilitado == 1) {
                    if (confirm("Al continuar se inhabilitará este post y ya no se mostrará en el sitio web, ¿desea proceder?")) {
                        habilitaPost(id, "0");
                    }
                } else {
                    habilitaPost(id, "1");
                }
            });

            function habilitaPost(id, habilitado) {
                $.ajax({
                    data: {
                        id: id,
                        habilitado: habilitado
                    },
                    type: "post",
                    url: "socialware/php/ajax/habilitaBlog.php",
                    success: function(resultado) {
                        location.reload();
                    }
                });
            }


            // Redirige hacia edicion


            $(".link_editar").click(function() {
                $("#campo_edicion_id").val($(this).attr("data-id"));

                $("#formulario_edicion").submit();
            });

        </script>
    </body>
</html>
