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
                                <h5 class="txt-light">Vehículos</h5>
                            </div>
                        </div>

                        <!-- Formulario -->

                        <div class="row">
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
                                                <form action="vehiculos.php" method="post">
                                                    <input name="esSubmit" type="hidden" value="1" />

                                                    <div class="form-body">
                                                        <div class="row mb-30">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Tipo</label>
                                                                    <select class="form-control select2" id="campo_tipo" name="tipo">
                                                                        <option <?php echo estaVacio($tipo) ? "selected" : "" ?> value="">Ver todo</option>

                                                                        <?php
                                                                            $tipos_BD = consulta($conexion, "SELECT DISTINCT tipo FROM vehiculo ORDER BY tipo");

                                                                            while ($tipo_BD = obtenResultado($tipos_BD)) {
                                                                                echo "<option " . ($tipo_BD["tipo"] == $tipo ? "selected" : "") . " value='" . $tipo_BD["tipo"] . "'>" . $tipo_BD["tipo"] . "</option>";
                                                                            }
                                                                        ?>

                                                                        <!--option <?php echo $tipo == "Auto" ? "selected" : "" ?> value="Auto">Auto</option>
                                                                        <option <?php echo $tipo == "Pickup" ? "selected" : "" ?> value="Pickup">Pickup</option>
                                                                        <option <?php echo $tipo == "SUV" ? "selected" : "" ?> value="SUV">SUV</option>
                                                                        <option <?php echo $tipo == "Jeep" ? "selected" : "" ?> value="Jeep">Jeep</option>
                                                                        <option <?php echo $tipo == "Todo terreno" ? "selected" : "" ?> value="Todo terreno">Todo terreno</option-->
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Marca</label>
                                                                    <select class="form-control select2" id="campo_marca" name="marca">
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

                                                    <div class="form-body">
                                                        <div class="row mb-30">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Año</label>
                                                                    <select class="form-control select2" id="campo_ano" name="ano">
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

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Transmisión</label>
                                                                    <select class="form-control select2" id="campo_transmision" name="transmision">
                                                                        <option <?php echo estaVacio($transmision) ? "selected" : "" ?> value="">Ver todo</option>

                                                                        <?php
                                                                            $transmisiones_BD = consulta($conexion, "SELECT DISTINCT transmision FROM vehiculo ORDER BY transmision");

                                                                            while ($transmision_BD = obtenResultado($transmisiones_BD)) {
                                                                                echo "<option " . ($transmision_BD["transmision"] == $transmision ? "selected" : "") . " value='" . $transmision_BD["transmision"] . "'>" . $transmision_BD["transmision"] . "</option>";
                                                                            }
                                                                        ?>

                                                                        <!--option <?php echo $transmision == "Automática" ? "selected" : "" ?> value="Automática">Automática</option>
                                                                        <option <?php echo $transmision == "Estándar" ? "selected" : "" ?> value="Estándar">Estándar</option>
                                                                        <option <?php echo $transmision == "CVT" ? "selected" : "" ?> value="CVT">CVT</option>
                                                                        <option <?php echo $transmision == "Tiptronic" ? "selected" : "" ?> value="Tiptronic">Tiptronic</option-->
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-body">
                                                        <div class="row mb-30">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Habilitado</label>
                                                                    <select class="form-control select2" id="campo_publicado" name="publicado">
                                                                        <option <?php echo estaVacio($publicado) ? "selected" : "" ?> value="">Ver todo</option>
                                                                        <option <?php echo $publicado == "1" ? "selected" : "" ?> value="1">Si</option>
                                                                        <option <?php echo $publicado == "0" ? "selected" : "" ?> value="0">No</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label mb-10">Concesionario</label>
                                                                    <select class="form-control select2" id="campo_concesionario" name="concesionario">
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

                                                                        <!--option <?php echo $transmision == "Automática" ? "selected" : "" ?> value="Automática">Automática</option>
                                                                        <option <?php echo $transmision == "Estándar" ? "selected" : "" ?> value="Estándar">Estándar</option>
                                                                        <option <?php echo $transmision == "CVT" ? "selected" : "" ?> value="CVT">CVT</option>
                                                                        <option <?php echo $transmision == "Tiptronic" ? "selected" : "" ?> value="Tiptronic">Tiptronic</option-->
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
                                                                    <th>ID</th>
                                                                    <th>Concesionario</th>
                                                                    <th>Habilitado</th>
                                                                    <th>ID Intelimotor</th>
                                                                    <th>Marca</th>
                                                                    <th>Modelo</th>
                                                                    <th>Año</th>
                                                                    <th>Tipo</th>
                                                                    <th>Precio</th>
                                                                    <th class="columna_acciones">Acciones</th>
                                                                </tr>
                                                            </thead>

                                                            <tfoot>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Concesionario</th>
                                                                    <th>Habilitado</th>
                                                                    <th>ID Intelimotor</th>
                                                                    <th>Marca</th>
                                                                    <th>Modelo</th>
                                                                    <th>Año</th>
                                                                    <th>Tipo</th>
                                                                    <th>Precio</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </tfoot>

                                                            <tbody>
                                                                <?php

                                                                    // Arma restricciones

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
                                                                                echo "<a class='link_editar' data-vehiculo='" . $vehiculo["id"] . "'  data-toggle='tooltip' href='javascript:;' title='Ver detalle'><i class='fa fa-search'></i></a>";

                                                                                if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                                    //echo "<a class='link_publicar' data-id='" . $vehiculo["id"] . "'  data-publicado='" . $vehiculo["publicado"] . "' data-toggle='tooltip' href='javascript:;' title='" . ($vehiculo["publicado"] == 1 ? "Ocultar" : "Publicar") . "'><i class='fa fa-" . ($vehiculo["publicado"] == 1 ? "eye-slash" : "eye") . "'></i></a>";
                                                                                    echo "<a class='link_publicar' data-id='" . $vehiculo["id"] . "'  data-publicado='" . $vehiculo["publicado"] . "' data-toggle='tooltip' href='javascript:;' style='color: " . ($vehiculo["publicado"] == 1 ? "#2f2c2c" : "#bfbcbc") . "' title='" . ($vehiculo["publicado"] == 1 ? "Dejar de publicar" : "Publicar") . "'><i class='fa fa-globe'></i></a>";
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

                                        <br />

                                        <div class="form-group mb-0">
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
                                            } else {
                                                $puedeAgregarVehiculo = 0;
                                            }

                                            if ($puedeAgregarVehiculo) { ?>
                                                <a class="btn btn-primary link_agregar" href="javascript:;">Agregar Vehículo</a>
                                            <?php }

                                            if ($puedeSincronizarVehiculo) { ?>
                                                <a class="btn btn-success link_sincronizar" href="javascript:;">Sincronizar inventario InteliMotor</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Formulario de redireccion hacia edicion -->

                        <form action="vehiculo.php" id="formulario_edicion" method="post">
                            <input name="origen" type="hidden" value="vehiculos.php" />

                            <!-- Ida -->

                            <input id="campo_edicion_id" name="id" type="hidden" />

                            <!-- Regreso -->

                            <input id="campo_edicion_marca" name="origen_marca" type="hidden" value="" />
                            <input id="campo_edicion_ano" name="origen_ano" type="hidden" value="" />
                            <input id="campo_edicion_tipo" name="origen_tipo" type="hidden" value="" />
                            <input id="campo_edicion_transmision" name="origen_transmision" type="hidden" value="" />
                            <input id="campo_edicion_publicado" name="origen_publicado" type="hidden" value="" />
                            <input id="campo_edicion_concesionario" name="origen_concesionario" type="hidden" value="" />
                        </form>
                    <?php
                        } else {
                            registraEvento("CMS : Consulta de vehículos bloqueada");
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


            // Sincronizar inventario InteliMotor


            $(".link_sincronizar").click(function() {
                $.LoadingOverlay("show", { minSize: 30, maxSize: 30 });

                $.ajax({
                    data: {
                        idConcesionario: <?php echo $esUsuarioOperador? $usuario_idConcesionario : "0" ; ?>
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
            });


            // Redirige hacia edicion


            $(".link_editar").click(function() {
                $("#campo_edicion_id").val($(this).attr("data-vehiculo"));

                $("#campo_edicion_marca").val($("#campo_marca").val());
                $("#campo_edicion_ano").val($("#campo_ano").val());
                $("#campo_edicion_tipo").val($("#campo_tipo").val());
                $("#campo_edicion_transmision").val($("#campo_transmision").val());
                $("#campo_edicion_publicado").val($("#campo_publicado").val());

                $("#formulario_edicion").submit();
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
