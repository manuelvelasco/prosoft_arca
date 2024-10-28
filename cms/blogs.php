<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <?php

            // Obtiene parametros de request

            $esSubmit = sanitiza($conexion, filter_input(INPUT_POST, "esSubmit"));
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
                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador || ($esUsuarioOperador && $usuario_permisoConsultarVehiculos)) { ?>

                        <!-- Titulo -->

                        <div class="row heading-bg bg-blue">
                            <div class="col-xs-12">
                                <h5 class="txt-light">Blogs</h5>
                            </div>
                        </div>

                        <!-- Formulario -->

                        

                        <!-- Tabla de resultados -->

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
                                                                    <th>ID</th>
                                                                    <th>Fecha de publicación</th>
                                                                    <th>Título</th>
                                                                    <th>Publicado</th>
                                                                    <th class="columna_acciones">Acciones</th>
                                                                </tr>
                                                            </thead>

                                                            <tfoot>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Fecha de publicación</th>
                                                                    <th>Título</th>
                                                                    <th>Publicado</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </tfoot>

                                                            <tbody>
                                                                <?php

                                                                    // Arma restricciones

                                                                    /* $restricciones = "";

                                                                    if (!estaVacio($marca)) {
                                                                        $restricciones .= " AND v.marca = '" . $marca . "'";
                                                                    }

                                                                    if (!estaVacio($ano)) {
                                                                        $restricciones .= " AND v.ano = " . $ano;
                                                                    }

                                                                    if (!estaVacio($publicado)) {
                                                                        $restricciones .= " AND v.publicado = " . $publicado;
                                                                    }

                                                                    if (!estaVacio($transmision)) {
                                                                        $restricciones .= " AND v.transmision = '" . $transmision ."'";
                                                                    }

                                                                    if (!estaVacio($tipo)) {
                                                                        $restricciones .= " AND v.tipo = '" . $tipo . "'";
                                                                    } */

                                                                    // Consulta base de datos
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

                                        <br />
                                        <div class="form-group mb-0">
                                            <a class="btn btn-primary link_agregar" href="javascript:;">Agregar blog</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- Formulario de redireccion hacia edicion -->

                        <form action="blog.php" id="formulario_edicion" method="post">
                            <input name="origen" type="hidden" value="blogs.php" />

                            <!-- Ida -->

                            <input id="campo_edicion_id" name="id" type="hidden" />

                        </form>
                    <?php
                        } else {
                            registraEvento("CMS : Consulta de posts bloqueada");
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

                // Inicializa tabla de resultados

                $("#tabla_resultados").DataTable({
                    order: [[0, "desc"]],
                    dom: "Bfrtip",
                    buttons: [
                        "copy", "excel", "pdf", "print"
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
                $("#campo_edicion_id").val($(this).attr("data-blog"));

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
        </script>
    </body>
</html>
