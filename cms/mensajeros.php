<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <?php

            // Obtiene parametros de request

            $esSubmit = "1";
            $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));
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
                        <?php if ($esUsuarioMaster || $usuario_permisoConsultarDelegacionVirtual) { ?>
                            <div class="main-container container-fluid">


                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Listado de mensajeros</h1>
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
                                                            <?php if ($esUsuarioMaster || $usuario_permisoEditarDelegacionVirtual) { ?>
                                                                <a class="btn btn-primary" data-fancybox data-type="iframe" data-preload="false" data-height="900" href="mensajero.php" id="boton_agregar">Agregar mensajero</a>
                                                                &nbsp;&nbsp;&nbsp;
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filtros -->

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5><strong>Utilice los filtros para detallar su búsqueda</strong></h5>

                                                <hr />

                                                <form action="mensajeros.php" method="post">
                                                    <input name="esSubmit" type="hidden" value="1" />

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-3" for="campo_idConcesionario">Agencia</label>
                                                                        <div class="col-md-9">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_idConcesionario" name="idConcesionario">
                                                                                <option <?php echo estaVacio($idConcesionario) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                        
                                                                                    if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                                        
                                                                                        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario ORDER BY nombreComercial");

                                                                                    }else{

                                                                                        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario c WHERE c.id = " . $usuario_idConcesionario. " ORDER BY nombreComercial");
                                                                                        $concesionario = $usuario_idConcesionario;

                                                                                    }

                                                                                    while ($concesionario_BD = obtenResultado($concesionarios_BD)) {
                                                                                        echo "<option " . ($concesionario_BD["id"] == $idConcesionario ? "selected" : "") . " value='" . $concesionario_BD["id"] . "'>" . $concesionario_BD["nombreComercial"] . "</option>";
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
                                                                        <div class="col-md-3">
                                                                            <button class="btn btn-success mb-3" type="submit" id="boton_consultar">Consultar</button>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <button class="btn btn-info mb-3" type="button" id="boton_limpiarFiltros">Limpiar filtros</button>
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
                                                                <th class="border-bottom-0">Agencia</th>
                                                                <th class="border-bottom-0">Nombre</th>
                                                                <th class="border-bottom-0">Curp</th>
                                                                <th class="border-bottom-0">Correo electrónico</th>
                                                                <th class="border-bottom-0">Teléfono</th>
                                                                <th class="border-bottom-0">Habilitado</th>
                                                                <th class="border-bottom-0">Acciones</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="contenedor_resultados">
                                                            <?php

                                                                    // Arma restricciones

                                                                    $restricciones = "";

                                                                    if (!estaVacio($idConcesionario)) {
                                                                        $restricciones .= " AND id = '" . $idConcesionario ."'";
                                                                    }

                                                                    // Consulta base de datos

                                                                    $mensajeros_BD = consulta($conexion, "SELECT 
                                                                            m.id,
                                                                            (SELECT GROUP_CONCAT(nombreComercial ORDER BY nombreComercial SEPARATOR '<br /><br />') FROM concesionario WHERE id IN (SELECT idConcesionario FROM mensajero_concesionario WHERE idMensajero = m.id) " . $restricciones . ") AS concesionarios,
                                                                            m.nombre,
                                                                            m.apellidoPaterno,
                                                                            m.apellidoMaterno,
                                                                            m.curp,
                                                                            m.correoElectronico,
                                                                            m.telefono,
                                                                            m.habilitado
                                                                        FROM
                                                                            mensajero m
                                                                        WHERE
                                                                            m.eliminado = 0
                                                                        ORDER BY
                                                                            m.nombre,
                                                                            m.apellidoPaterno,
                                                                            m.apellidoMaterno");

                                                                    while ($mensajero = obtenResultado($mensajeros_BD)) {
                                                                        echo "<tr>";
                                                                        echo "<td>" . $mensajero["id"] . "</td>";
                                                                        echo "<td>" . $mensajero["concesionarios"] . "</td>";
                                                                        echo "<td>" . $mensajero["nombre"] . " " . $mensajero["apellidoPaterno"] . " " . $mensajero["apellidoMaterno"] . "</td>";
                                                                        echo "<td>" . $mensajero["curp"] . "</td>";
                                                                        echo "<td>" . $mensajero["correoElectronico"] . "</td>";
                                                                        echo "<td>" . $mensajero["telefono"] . "</td>";
                                                                        echo "<td>" . ($mensajero["habilitado"] == 1 ? "Si" : "No") . "</td>";
                                                                        echo "<td>";
                                                                        //echo "<a class='link_editar' data-id='" . $mensajero["id"] . "' data-toggle='tooltip' href='javascript:;' title='Ver detalle'><i class='fa fa-search'></i></a>";
                                                                        echo "<a data-fancybox data-type='iframe' data-preload='false' data-height='1080' href='mensajero.php?id=" . $mensajero["id"] . "' title='Editar'><i class='fa fa-search'></i></a>";

                                                                        if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                            echo "<a class='link_habilitar' data-id='" . $mensajero["id"] . "' data-habilitado='" . $mensajero["habilitado"] . "'  href='javascript:;' title='" . ($mensajero["habilitado"] == 1 ? "Inhabilitar" : "Habilitar") . "'><i class='fa fa-" . ($mensajero["habilitado"] == 1 ? "lock" : "unlock") . "'></i></a>";
                                                                        }

                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    registraEvento("CMS : Consulta de mensajeros");
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
                                registraEvento("CMS : Consulta de mensajeros bloqueada");
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
                $("#campo_edicion_id").val($(this).attr("data-id"));

                $("#formulario_edicion").submit();
            });


            // Habilita / inhabilita usuario


            $(".link_habilitar").click(function() {
                var id = $(this).attr("data-id");
                var habilitado = $(this).attr("data-habilitado");

                if (habilitado == 1) {
                    if (confirm("Al continuar se inhabilitará este mensajero, ¿desea proceder?")) {
                        habilitaMensajero(id, "0");
                    }
                } else {
                    habilitaMensajero(id, "1");
                }
            });

            $("#boton_limpiarFiltros").click(function(){
                $("#campo_idConcesionario").val('').trigger('change');
                $("#campo_rol").val('').trigger('change');
            });


            function habilitaMensajero(id, habilitado) {
                $.ajax({
                    data: {
                        id: id,
                        habilitado: habilitado
                    },
                    type: "post",
                    url: "socialware/php/ajax/habilitaMensajero.php",
                    success: function(resultado) {
                        location.reload();
                    }
                });
            }
        </script>
    </body>
</html>
