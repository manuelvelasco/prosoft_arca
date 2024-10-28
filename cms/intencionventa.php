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
                                <h5 class="txt-light">Intención Venta</h5>
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
                                                                    <th>Fecha Registro</th>
                                                                    <th>Año</th>
                                                                    <th>Marca</th>
                                                                    <th>Modelo</th>
                                                                    <th>Versión</th>
                                                                    <th>Kilometraje</th>
                                                                    <th>Color</th>
                                                                    <th>Nombre</th>
                                                                    <th>Apellidos</th>
                                                                    <th>Correo Electrónico</th>
                                                                    <th>Teléfono</th>
                                                                </tr>
                                                            </thead>

                                                            <tfoot>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Fecha Registro</th>
                                                                    <th>Año</th>
                                                                    <th>Marca</th>
                                                                    <th>Modelo</th>
                                                                    <th>Versión</th>
                                                                    <th>Kilometraje</th>
                                                                    <th>Color</th>
                                                                    <th>Nombre</th>
                                                                    <th>Apellidos</th>
                                                                    <th>Correo Electrónico</th>
                                                                    <th>Teléfono</th>
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
                                                                    $ventas_BD = consulta($conexion, "SELECT * FROM intencionventa ORDER BY fechaRegistro DESC");

                                                                    while ($intencion = obtenResultado($ventas_BD)) {
                                                                        echo "<tr>";
                                                                            echo "<td>" . $intencion["id"] . "</td>";
                                                                            echo "<td>" . $intencion["fechaRegistro"] . "</td>";
                                                                            echo "<td>" . $intencion["ano"] . "</td>";
                                                                            echo "<td>" . $intencion["marca"] . "</td>";
                                                                            echo "<td>" . $intencion["modelo"] . "</td>";
                                                                            echo "<td>" . $intencion["version"] . "</td>";
                                                                            echo "<td>" . $intencion["kilometraje"] . "</td>";
                                                                            echo "<td>" . $intencion["color"] . "</td>";
                                                                            echo "<td>" . $intencion["nombre"] . "</td>";
                                                                            echo "<td>" . $intencion["apellido"] . "</td>";
                                                                            echo "<td>" . $intencion["correoElectronico"] . "</td>";
                                                                            echo "<td>" . $intencion["telefono"] . "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    registraEvento("CMS : Consulta de intenciones de venta");
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <br />
                                    </div>
                                </div>
                            </div>

                        <!-- Formulario de redireccion hacia edicion -->

                        <form action="intencionventa.php" id="formulario_edicion" method="post">
                            <input name="origen" type="hidden" value="intencionesventas.php" />

                            <!-- Ida -->

                            <input id="campo_edicion_id" name="id" type="hidden" />

                        </form>
                    <?php
                        } else {
                            registraEvento("CMS : Consulta de intenciones de venta bloqueada");
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

        </script>
    </body>
</html>
