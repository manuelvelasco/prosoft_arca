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
            $marca = sanitiza($conexion, filter_input(INPUT_POST, "marca"));
            $ano = sanitiza($conexion, filter_input(INPUT_POST, "ano"));
            $tipo = sanitiza($conexion, filter_input(INPUT_POST, "tipo"));
            $transmision = sanitiza($conexion, filter_input(INPUT_POST, "transmision"));
            $publicado = sanitiza($conexion, filter_input(INPUT_POST, "publicado"));
            $concesionario = sanitiza($conexion, filter_input(INPUT_POST, "concesionario"));
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
                        <?php if ($esUsuarioMaster || $usuario_permisoConsultarVehiculos ) { ?>
                            <div class="main-container container-fluid">


                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Vehículos</h1>
                                </div>

                                <!-- Filtros -->

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5><strong>Utilice los filtros para detallar su búsqueda</strong></h5>

                                                <hr />

                                                <form action="vehiculos.php" method="post">
                                                    <input name="esSubmit" type="hidden" value="1" />

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-3" for="campo_tipo">Tipo</label>
                                                                        <div class="col-md-9">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_tipo" name="tipo">
                                                                                <option <?php echo estaVacio($tipo) ? "selected" : "" ?> value="">Ver todo</option>
                                                                                <?php
                                                                                    $tipos_BD = consulta($conexion, "SELECT DISTINCT tipo FROM vehiculo ORDER BY tipo");

                                                                                    while ($tipo_BD = obtenResultado($tipos_BD)) {
                                                                                        echo "<option " . ($tipo_BD["tipo"] == $tipo ? "selected" : "") . " value='" . $tipo_BD["tipo"] . "'>" . $tipo_BD["tipo"] . "</option>";
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
                                                                        <label class="form-label col-md-3" for="campo_marca">Marca</label>
                                                                        <div class="col-md-9">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_marca" name="marca">
                                                                                <option <?php echo estaVacio($marca) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                    $marcas_BD = consulta($conexion, "SELECT DISTINCT marca FROM vehiculo ORDER BY marca");

                                                                                    while ($marca_BD = obtenResultado($marcas_BD)) {
                                                                                        echo "<option " . ($marca_BD["marca"] == $marca ? "selected" : "") . " value='" . $marca_BD["marca"] . "'>" . $marca_BD["marca"] . "</option>";
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
                                                                        <label class="form-label col-md-3" for="campo_ano">Año</label>
                                                                        <div class="col-md-9">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_ano" name="ano">
                                                                                <option <?php echo estaVacio($ano) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                    $anos_BD = consulta($conexion, "SELECT DISTINCT ano FROM vehiculo ORDER BY ano");

                                                                                    while ($ano_BD = obtenResultado($anos_BD)) {
                                                                                        echo "<option " . ($ano_BD["ano"] == $ano ? "selected" : "") . " value='" . $ano_BD["ano"] . "'>" . $ano_BD["ano"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-20">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-3" for="campo_transmision">Transmisión</label>
                                                                        <div class="col-md-9">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_transmision" name="transmision">
                                                                                <option <?php echo estaVacio($transmision) ? "selected" : "" ?> value="">Ver todo</option>
                                                                                <?php
                                                                                    $transmisiones_BD = consulta($conexion, "SELECT DISTINCT transmision FROM vehiculo ORDER BY transmision");

                                                                                    while ($transmision_BD = obtenResultado($transmisiones_BD)) {
                                                                                        echo "<option " . ($transmision_BD["transmision"] == $transmision ? "selected" : "") . " value='" . $transmision_BD["transmision"] . "'>" . $transmision_BD["transmision"] . "</option>";
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
                                                                        <label class="form-label col-md-3" for="campo_habilitado">Habilitado</label>
                                                                        <div class="col-md-9">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_habilitado" name="publicado">
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
                                                                        <label class="form-label col-md-3" for="campo_concesionario">Agencia</label>
                                                                        <div class="col-md-9">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_concesionario" name="concesionario">
                                                                                <option <?php echo estaVacio($concesionario) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                    if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                                        
                                                                                        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario ORDER BY nombreComercial");

                                                                                    }else{

                                                                                        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario c WHERE c.id = " . $usuario_idConcesionario. " ORDER BY nombreComercial");
                                                                                        $concesionario = $usuario_idConcesionario;

                                                                                    }

                                                                                    while ($concesionario_BD = obtenResultado($concesionarios_BD)) {
                                                                                        echo "<option " . ($concesionario_BD["id"] == $concesionario ? "selected" : "") . " value='" . $concesionario_BD["id"] . "'>" . $concesionario_BD["nombreComercial"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-20">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <div class="col-md-8">
                                                                            <button class="btn btn-success mb-3 ml-0" type="submit" id="boton_consultar">Consultar</button>
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
                                                            <?php 

                                                                //Se valida en el caso de usuario operador, si el concesionario asignado tiene activado el servicio de Intelimotor 
                                                                $puedeAgregarVehiculo = 1;
                                                                $puedeSincronizarVehiculo = 1;

                                                                
                                                                if ($esUsuarioOperador) {
                                                                    $concesionarioAsociadoIntelimotor = consulta($conexion, "SELECT id FROM concesionario WHERE id = " . $usuario_idConcesionario . " AND intelimotor_apiKey != '' AND intelimotor_apiSecret != ''");

                                                                    if (cuentaResultados($concesionarioAsociadoIntelimotor) > 0) {
                                                                        $puedeAgregarVehiculo = 0;
                                                                    } else {
                                                                        $puedeSincronizarVehiculo = 0;
                                                                    }
                                                                //} else {
                                                                    //$puedeAgregarVehiculo = 0;
                                                                }
                                                            ?>

                                                            <?php if ($puedeAgregarVehiculo && $usuario_permisoEditarVehiculos) { ?>
                                                                <a class="btn btn-primary" data-fancybox data-type="iframe" data-preload="false" data-height="900" href="vehiculo.php" id="boton_agregar">Agregar Vehículo</a>
                                                                &nbsp;&nbsp;&nbsp;
                                                            <?php } ?>

                                                            <?php if ($puedeSincronizarVehiculo && $usuario_permisoEditarVehiculos) { ?>
                                                                <a class="btn btn-warning link_sincronizar" href="javascript:;">Sincronizar inventario InteliMotor</a>
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
                                                                    <th class="border-bottom-0">Id</th>
                                                                    <th class="border-bottom-0">Agencia</th>
                                                                    <th class="border-bottom-0">Habilitado</th>
                                                                    <th class="border-bottom-0">ID Intelimotor</th>
                                                                    <th class="border-bottom-0">Marca</th>
                                                                    <th class="border-bottom-0">Modelo</th>
                                                                    <th class="border-bottom-0">Año</th>
                                                                    <th class="border-bottom-0">Tipo</th>
                                                                    <th class="border-bottom-0">Precio</th>
                                                                    <th class="border-bottom-0">Acciones</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody id="contenedor_resultados">
                                                                <?php

                                                                    $restricciones = "";

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
                                                                    }

                                                                    if (!estaVacio($concesionario)) {
                                                                        $restricciones .= " AND v.idConcesionario = '" . $concesionario . "'";
                                                                    }

                                                                    // Consulta base de datos

                                                                    $vehiculos_BD = consulta($conexion, "SELECT"
                                                                            . " v.*, c.nombreComercial"
                                                                        . " FROM"
                                                                            . " vehiculo v"
                                                                        . " INNER JOIN concesionario c ON v.idConcesionario = c.id"
                                                                        . " WHERE 1 = 1 " . $restricciones
                                                                        . " ORDER BY marca, modelo, ano, tipo, precio");

                                                                    while ($vehiculo = obtenResultado($vehiculos_BD)) {
                                                                        echo "<tr>";
                                                                            echo "<td>" . $vehiculo["id"] . "</td>";
                                                                            echo "<td>" . $vehiculo["nombreComercial"] . "</td>";
                                                                            echo "<td>" . ($vehiculo["publicado"] == 1 ? "Si" : "No" ) . "</td>";
                                                                            echo "<td>" . $vehiculo["intelimotor_id"] . "</td>";
                                                                            echo "<td>" . $vehiculo["marca"] . "</td>";
                                                                            echo "<td>" . $vehiculo["modelo"] . "</td>";
                                                                            echo "<td>" . $vehiculo["ano"] . "</td>";
                                                                            echo "<td>" . $vehiculo["tipo"] . "</td>";
                                                                            echo "<td>" . formatoMoneda($vehiculo["precio"]) . "</td>";
                                                                            echo "<td>";
                                                                                echo "<a data-fancybox data-type='iframe' data-preload='false' data-height='1080' href='vehiculo.php?id=" . $vehiculo["id"] . "' title='Editar'><i class='fa fa-search'></i></a>";

                                                                                if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                                    //echo "<a class='link_publicar' data-id='" . $vehiculo["id"] . "'  data-publicado='" . $vehiculo["publicado"] . "' data-toggle='tooltip' href='javascript:;' title='" . ($vehiculo["publicado"] == 1 ? "Ocultar" : "Publicar") . "'><i class='fa fa-" . ($vehiculo["publicado"] == 1 ? "eye-slash" : "eye") . "'></i></a>";
                                                                                    echo "<a class='link_publicar' data-id='" . $vehiculo["id"] . "'  data-publicado='" . $vehiculo["publicado"] . "' href='javascript:;' style='color: " . ($vehiculo["publicado"] == 1 ? "#2f2c2c" : "#bfbcbc") . "' title='" . ($vehiculo["publicado"] == 1 ? "Dejar de publicar" : "Publicar") . "'><i class='fa fa-globe'></i></a>";
                                                                                }

                                                                            echo "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    registraEvento("CMS : Consulta de vehículos");
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
                                registraEvento("CMS : Consulta de vehículos bloqueada");
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


             // Sincronizar inventario InteliMotor


            $(".link_sincronizar").click(function() {
/*
                $.LoadingOverlay("show", { minSize: 30, maxSize: 30 });

                $.ajax({
                    data: {
                        idConcesionario: <?php echo $esUsuarioOperador ? $usuario_idConcesionario : "0" ; ?>
                    },
                    type: "post",
                    url: "socialware/php/ajax/sincronizaInventarioInteliMotor.php",
                    success: function(resultado) {
                        console.log("resultado = " + resultado);
                        $("#contenedor_mensaje span").html("El inventario ha sido sincronizado. El listado se actualizará en cinco segundos");
                        $("#contenedor_mensaje").addClass("alert-success");
                        $("#contenedor_mensaje").show();

                        $("html, body").animate({ scrollTop: 0 }, "slow");

                        $.LoadingOverlay("hide");

                        //setTimeout(function() { location.reload(); }, 5000);
                    }
                });
*/

                if (confirm("El proceso de sincronización puede tardar un tiempo considerable.  Al finalizar recibirás un correo electrónico de confirmación. ¿Deseas continuar?")) {
                    $.ajax({
                        data: {
                            idConcesionario: <?php echo $esUsuarioOperador ? $usuario_idConcesionario : "0" ; ?>
                        },
                        type: "post",
                        url: "socialware/php/ajax/sincronizaInventarioInteliMotor.php"
                    });

                    $("#contenedor_mensaje span").html("El proceso de sincronización ha comenzado, al finalizar recibirás un correo electrónico de confirmación.");
                    $("#contenedor_mensaje").addClass("alert-success");
                    $("#contenedor_mensaje").show();

                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            });


            // Publica / Oculta un vehiculo


            $(".link_publicar").click(function() {
                var id = $(this).attr("data-id");
                var publicado = $(this).attr("data-publicado");

                if (publicado == 1) {
                    if (confirm("Al continuar se dejara de mostrar este vehículo en el sitio web, ¿desea proceder?")) {
                        habilitarVehiculo(id, "0");
                    }
                } else {
                    habilitarVehiculo(id, "1");
                }
            });


            function habilitarVehiculo(id, publicado) {
                $.ajax({
                    data: {
                        id: id,
                        publicado: publicado
                    },
                    type: "post",
                    url: "socialware/php/ajax/habilitaVehiculo.php",
                    success: function(resultado) {
                        location.reload();
                    }
                });
            }
        </script>
    </body>
</html>
