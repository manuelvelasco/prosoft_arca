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
                                    <h1 class="page-title">Contacto</h1>
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
                                                                <th class="border-bottom-0">Fecha Contacto</th>
                                                                <th class="border-bottom-0">Nombre</th>
                                                                <th class="border-bottom-0">Teléfono</th>
                                                                <th class="border-bottom-0">Correo electrónico</th>
                                                                <th class="border-bottom-0">Mensaje</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="contenedor_resultados">
                                                            <?php

                                                                    $contacto_BD = consulta($conexion, "SELECT * FROM contacto ORDER BY fechaRegistro DESC");

                                                                    while ($contacto = obtenResultado($contacto_BD)) {
                                                                        echo "<tr>";
                                                                            echo "<td>" . $contacto["fechaRegistro"] . "</td>";
                                                                            echo "<td>" . $contacto["nombre"] . "</td>";
                                                                            echo "<td>" . $contacto["telefono"] . "</td>";
                                                                            echo "<td>" . $contacto["correoElectronico"] . "</td>";
                                                                            echo "<td>" . $contacto["mensaje"] . "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    registraEvento("CMS : Consulta de contactos");
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
                                registraEvento("CMS : Consulta de contacto bloqueada");
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

        </script>
    </body>
</html>
