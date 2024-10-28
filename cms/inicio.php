<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <!--meta http-equiv="refresh" content="60" /-->
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

                    <!-- Titulo -->

                    <div class="row heading-bg bg-blue">
                        <div class="col-xs-12">
                            <h5 class="txt-light">Inicio</h5>
                        </div>
                    </div>

                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                    <?php } ?>

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

                $(".tabla_resultados").DataTable({
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

                // Actualiza la pagina cada minuto

                setTimeout(function() {
                    location.reload();
                }, 60000);
            });


            // Redirige hacia edicion


            $(".link_editar").click(function() {
                $("#campo_edicion_id").val($(this).attr("data-id"));

                $("#formulario_edicion").submit();
            });
        </script>
    </body>
</html>
