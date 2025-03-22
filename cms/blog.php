<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>
    </head>
    <body>
        <?php

            // Obtiene parametros de request

            $esSubmit = filter_input(INPUT_POST, "esSubmit");
            $id = filter_input(INPUT_POST, "id");
            $habilitado = filter_input(INPUT_POST, "habilitado");
            $fecha = filter_input(INPUT_POST, "fecha");
            $autor = filter_input(INPUT_POST, "autor");
            $titulo = filter_input(INPUT_POST, "titulo");
            $resumen = filter_input(INPUT_POST, "resumen");
            $detalle = filter_input(INPUT_POST, "detalle");
            $etiquetas = filter_input(INPUT_POST, "etiquetas");
            $categoria = filter_input(INPUT_POST, "categoria");
            // Parametros enviados por origen

            $origen = filter_input(INPUT_POST, "origen");

            // Inicializa variables

            $mensaje = "";
            $fechaActual = date("Y-m-d H:i:s");
            $habilitado = estaVacio($habilitado) ? 0 : 1;
            $imagenPrincipal = "";
            $imagenGaleria = "";
            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($autor)) {
                    $mensaje .= "* Autor<br />";
                }

                if (estaVacio($titulo)) {
                    $mensaje .= "* Título<br />";
                }

                if (estaVacio($resumen)) {
                    $mensaje .= "* Resumen<br />";
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporciona los siguientes datos:<br /><br />" . $mensaje;

                    $post_BD = consulta($conexion, "SELECT imagenPrincipal FROM post WHERE id = " . $id);
                    $post = obtenResultado($post_BD);

                    $imagenPrincipal = $post["imagenPrincipal"];
                } else {
                    if (estaVacio($id)) {

                        // Es insercion

                        $post_BD = consulta($conexion, "SELECT id FROM post WHERE titulo = '" . $titulo . "'");

                        if (cuentaResultados($post_BD) > 0) {
                            $mensaje = "El post ya se encuentra registrado en la base de datos";
                        } else {
                            consulta($conexion, "INSERT INTO post ("
                                    . "habilitado"
                                    . ", fecha"
                                    . ", autor"
                                    . ", titulo"
                                    . ", resumen"
                                    . ", detalle"
                                    . ", etiquetas"
                                    . ", categoria"
                                . ") VALUES ("
                                    . $habilitado
                                    . ", '" . $fechaActual . "'"
                                    . ", '" . $autor . "'"
                                    . ", '" . $titulo . "'"
                                    . ", '" . $resumen . "'"
                                    . ", " . (!estaVacio($detalle) ? "'" . $detalle . "'" : "NULL")
                                    . ", " . (!estaVacio($etiquetas) ? "'" . $etiquetas . "'" : "NULL")
                                    . ", '" . $categoria . "'"
                                . ")");

                            // Carga informacion actualizada

                            $post_BD = consulta($conexion, "SELECT * FROM post WHERE titulo = '" . $titulo . "'");
                            $post = obtenResultado($post_BD);

                            $id = $post["id"];
                            $habilitado = $post["habilitado"];
                            $fecha = $post["fecha"];
                            $autor = $post["autor"];
                            $titulo = $post["titulo"];
                            $resumen = $post["resumen"];
                            $detalle = $post["detalle"];
                            $etiquetas = $post["etiquetas"];
                            $categoria = $post["categoria"];
                            $imagenPrincipal = $post["imagenPrincipal"];

                            registraEvento("CMS : Alta de post | id = " . $id);

                            $mensaje = "ok - El post ha sido registrado";
                        }
                    } else {

                        // Es actualizacion

                        consulta($conexion, "UPDATE post SET"
                                . " habilitado = " . $habilitado
                                . ", autor = '" . $autor . "'"
                                . ", titulo = '" . $titulo . "'"
                                . ", resumen = '" . $resumen . "'"
                                . ", detalle = " . (!estaVacio($detalle) ? "'" . $detalle . "'" : "NULL")
                                . ", etiquetas = " . (!estaVacio($etiquetas) ? "'" . $etiquetas . "'" : "NULL")
                                . ", categoria = '" . $categoria . "'"
                            . " WHERE id = " . $id);

                        // Carga imagen principal

                        if (isset($_FILES["imagenPrincipal"])) {
                            try {
                                $archivo = $_FILES["imagenPrincipal"];

                                if ($archivo["size"] > 0) {
                                    $nombreEstandarizado = "post_" . $id . "_imagenPrincipal_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                    $archivoDestino = $constante_rutaPosts . "/" . $id . "/" . $nombreEstandarizado;

                                    if (!file_exists($constante_rutaPosts . "/" . $id)) {
                                        mkdir($constante_rutaPosts . "/" . $id, 0755, true);
                                    }

                                    move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                    consulta($conexion, "UPDATE post SET imagenPrincipal = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                    $imagenPrincipal = $nombreEstandarizado;
                                }
                            } catch (Exception $e) {
                            }
                        }

                        // Carga imagen de galeria

                        if (isset($_FILES["imagenGaleria"])) {
                            try {
                                //$archivo = $_FILES["imagenGaleria"];
                                $archivos = reArrayFiles($_FILES["imagenGaleria"]);

                                foreach ($archivos as $archivo) {
                                    if ($archivo["size"] > 0) {
                                        $nombreEstandarizado = "post_" . $id . "_imagenGaleria_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                        $archivoDestino = $constante_rutaPosts . "/" . $id . "/galeria/" . $nombreEstandarizado;

                                        if (!file_exists($constante_rutaPosts . "/" . $id . "/galeria")) {
                                            mkdir($constante_rutaPosts . "/" . $id . "/galeria", 0755, true);
                                        }

                                        move_uploaded_file($archivo["tmp_name"], $archivoDestino);
                                    }
                                }
                            } catch (Exception $e) {
                            }
                        }

                        // Carga informacion actualizada

                        $post_BD = consulta($conexion, "SELECT * FROM post WHERE id = " . $id);
                        $post = obtenResultado($post_BD);

                        $id = $post["id"];
                        $habilitado = $post["habilitado"];
                        $fecha = $post["fecha"];
                        $autor = $post["autor"];
                        $titulo = $post["titulo"];
                        $resumen = $post["resumen"];
                        $detalle = $post["detalle"];
                        $etiquetas = $post["etiquetas"];
                        $categoria = $post["categoria"];
                        $imagenPrincipal = $post["imagenPrincipal"];

                        registraEvento("CMS : Actualización de post | id = " . $id);

                        $mensaje = "ok - Los cambios han sido guardados";
                    }
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $post_BD = consulta($conexion, "SELECT * FROM post WHERE id = " . $id);
                    $post = obtenResultado($post_BD);

                    $id = $post["id"];
                    $habilitado = $post["habilitado"];
                    $fecha = $post["fecha"];
                    $autor = $post["autor"];
                    $titulo = $post["titulo"];
                    $resumen = $post["resumen"];
                    $detalle = $post["detalle"];
                    $etiquetas = $post["etiquetas"];
                    $categoria = $post["categoria"];
                    $imagenPrincipal = $post["imagenPrincipal"];

                    registraEvento("CMS : Consulta de post | id = " . $id);
                }
            }

            function reArrayFiles($file) {
                $file_ary = array();
                $file_count = count($file['name']);
                $file_key = array_keys($file);

                for ($i=0; $i < $file_count; $i++) {
                    foreach ($file_key as $val) {
                        $file_ary[$i][$val] = $file[$val][$i];
                    }
                }

                return $file_ary;
            }
        ?>

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
                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>

                        <!-- Titulo -->

                        <div class="row heading-bg bg-blue">
                            <div class="col-xs-12">
                                <h5 class="txt-light">Detalle de Post</h5>
                            </div>
                        </div>

                        <!-- Bloques de informacion -->

                        <form action="blog.php" enctype="multipart/form-data" method="post">
                            <input name="esSubmit" type="hidden" value="1" />

                            <input name="origen" type="hidden" value="<?php echo $origen ?>" />

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default card-view">
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <div class="alert" id="contenedor_mensaje">
                                                    <span></span>
                                                </div>

                                                <!-- Generales -->

                                                <div class="panel panel-default card-view">
                                                    <div class="panel-heading">
                                                        <div class="pull-left">
                                                            <h6 class="panel-title txt-dark">
                                                                Proporciona la información que se solicita
                                                            </h6>

                                                            <hr />
                                                        </div>

                                                        <div class="clearfix"></div>
                                                    </div>

                                                    <div class="panel-wrapper collapse in">
                                                        <div class="panel-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 col-xs-12">
                                                                    <div class="form-wrap">
                                                                        <div class="form-body">
                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Información de control</strong></h5>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Id</label>
                                                                                        <input class="form-control" name="id" readonly type="text" value="<?php echo $id ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Fecha de registro</label>
                                                                                        <input class="form-control" name="fecha" readonly type="text" value="<?php echo $fecha ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Publicado</label>
                                                                                        <div>
                                                                                            <input <?php echo $habilitado == 1 ? "checked" : "" ?> class="form-control bs-switch" data-off-text="No publicado" data-on-text="Publicado" name="habilitado" type="checkbox" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <h5><strong>Datos generales</strong></h5>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Título <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="titulo" type="text" value="<?php echo $titulo ?>" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Autor <span class="txt-danger ml-10">*</span></label>
                                                                                        <input class="form-control" name="autor" type="text" value="<?php echo $autor ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Resumen</label>
                                                                                        <textarea class="form-control" name="resumen" rows="3"><?php echo $resumen ?></textarea>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Etiquetas</label>
                                                                                        <div class="tags-default">
                                                                                            <input class="form-control" data-role="tagsinput" name="etiquetas" placeholder="Separa con coma" type="text" value="<?php echo $etiquetas ?>" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-30">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Categoría &nbsp; <span class="txt-danger">*</span></label>
                                                                                        <input class="form-control" data-role="tagsinput" name="categoria" placeholder="Categoria" type="text" value="<?php echo $categoria ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-30">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label class="control-label mb-10">Detalle</label>
                                                                                        <textarea class="tinymce" name="detalle"><?php echo $detalle ?></textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <?php if (!estaVacio($id)) { ?>
                                                                                <div class="row mb-30">
                                                                                    <div class="col-md-12">
                                                                                        <h5><strong>Multimedia</strong></h5>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Imagen principal</label>

                                                                                            <span>
                                                                                                <!--
                                                                                                <br />
                                                                                                Formatos aceptados: .jpg, .jpeg, .png
                                                                                                -->
                                                                                                <br />
                                                                                                Tamaño preferente: 850 x 425 pixeles
                                                                                                <br />
                                                                                                Se muestra en:
                                                                                                <ul class="lista_seMuestraEn">
                                                                                                    <li>Blog</li>
                                                                                                    <li>Detalle de post</li>
                                                                                                </ul>
                                                                                                <br /><br />
                                                                                            </span>

                                                                                            <div>
                                                                                                <input name="imagenPrincipal" type="file" />
                                                                                                <br />
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <div class="panel panel-success card-view">
                                                                                                            <div class="panel-wrapper collapse in">
                                                                                                                <div class="panel-body">
                                                                                                                    <ul class="chat-list-wrap">
                                                                                                                        <li class="chat-list">
                                                                                                                            <div class="chat-body">
                                                                                                                            <?php
                                                                                                                                if (!estaVacio($imagenPrincipal)) {
                                                                                                                                    echo "<div class='chat-data'>";
                                                                                                                                    echo "<img class='user-img' src='" . $constante_urlPosts . "/" . $id . "/" . $imagenPrincipal . "' />";

                                                                                                                                    echo "<div class='user-data'>";
                                                                                                                                    echo "<span class='name block'>" . $imagenPrincipal . "</span>";
                                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                                    echo "<a data-lightbox='imagen' href='" . $constante_urlPosts . "/" . $id . "/" . $imagenPrincipal . "'>Ampliar</a>";
                                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                    echo "<a download href='" . $constante_urlPosts . "/" . $id . "/" . $imagenPrincipal . "'>Descargar</a>";
                                                                                                                                    echo "</span>";
                                                                                                                                    echo "</div>";
                                                                                                                                    echo "<div class='clearfix'></div>";
                                                                                                                                    echo "</div>";
                                                                                                                                }
                                                                                                                            ?>
                                                                                                                            </div>
                                                                                                                        </li>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label mb-10">Imagenes para insertar en el contenido</label>

                                                                                            <span>
                                                                                                <!--
                                                                                                <br />
                                                                                                Formatos aceptados: .jpg, .jpeg, .png
                                                                                                -->
                                                                                                <br />
                                                                                                Se muestra en:
                                                                                                <ul class="lista_seMuestraEn">
                                                                                                    <li>Detalle del post</li>
                                                                                                </ul>
                                                                                                <br /><br />
                                                                                            </span>

                                                                                            <div>
                                                                                                <input id="campo_archivo" multiple name="imagenGaleria[]" type="file" />
                                                                                                <br />
                                                                                                <div class="row">
                                                                                                    <div class="col-sm-12">
                                                                                                        <div class="panel panel-success card-view">
                                                                                                            <div class="panel-wrapper collapse in">
                                                                                                                <div class="panel-body">
                                                                                                                    <ul class="chat-list-wrap">
                                                                                                                        <li class="chat-list">
                                                                                                                            <div class="chat-body">
                                                                                                                            <?php
                                                                                                                                if (!estaVacio($id)) {
                                                                                                                                    try {
                                                                                                                                        $archivos = scandir($constante_rutaPosts . $id . "/galeria");
                                                                                                                                        $indice = 1;
                                                                                                                                        
                                                                                                                                        foreach ($archivos as $archivo) {
                                                                                                                                            $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                                                                                                                                            //if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                                                                                                                                            if ($extension !== "") {
                                                                                                                                                echo "<div class='chat-data' id='contenedor_imagen_" . $indice . "'>";
                                                                                                                                                echo "<img class='user-img' src='" . $constante_urlPosts . "/" . $id . "/galeria/" . $archivo . "' />";

                                                                                                                                                echo "<div class='user-data'>";
                                                                                                                                                echo "<span class='name block'>". $constante_urlPosts . $id . "/galeria/" . $archivo . "</span>";
                                                                                                                                                echo "<span class='time block txt-grey'>";
                                                                                                                                                echo "<a data-lightbox='imagen' href='" . $constante_urlPosts . "/" . $id . "/galeria/" . $archivo . "'>Ampliar</a>";
                                                                                                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                                echo "<a download href='" . $constante_urlPosts . "/" . $id . "/galeria/" . $archivo . "'>Descargar</a>";
                                                                                                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                                echo "<a href='javascript:eliminaImagenGaleria(" . $id . ", \"" . $archivo . "\", " . $indice . ")'>Eliminar</a>";
                                                                                                                                                echo "</span>";
                                                                                                                                                echo "</div>";
                                                                                                                                                echo "<div class='clearfix'></div>";
                                                                                                                                                echo "</div>";

                                                                                                                                                $indice++;
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    } catch (Exception $e) {
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            ?>
                                                                                                                            </div>
                                                                                                                        </li>
                                                                                                                    </ul>

                                                                                                                    <div class="alert alert-dismissable" id="contenedor_mensaje" style="display: none">
                                                                                                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> <span id="contenedor_mensaje_contenido"></span>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            <?php } ?>
                                                                        </div>

                                                                        <div class="form-actions mt-50">
                                                                            <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                                                                                <button class="btn btn-success" type="submit">Guardar</button>
                                                                            <?php } ?>

                                                                            <?php if (!estaVacio($origen)) { ?>
                                                                                <a class="btn btn-default ml-10 link_origen" type="button">Volver</a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Formulario de retorno a pagina origen -->

                        <form action="<?php echo $origen ?>" id="formulario_origen" method="post">
                            <input name="esSubmit" type="hidden" value="1" />

                        </form>
                    <?php
                        } else {
                            registraEvento("CMS : Consulta de post bloqueada | id = " . $id);
                            muestraBloqueo();
                        }
                    ?>

                </div>
            </div>
        </div>

        <?php include("socialware/php/estructura/plugins.php"); ?>

        <!-- Tinymce JavaScript -->
        <script src="vendors/bower_components/tinymce/tinymce.min.js"></script>

        <!-- Tinymce Wysuhtml5 Init JavaScript -->
        <script src="dist/js/tinymce-data.js"></script>

        <!--
         Lightbox
         http://lokeshdhakar.com/projects/lightbox2/
        -->
        <link href="socialware/js/lightbox2-master/dist/css/lightbox.min.css" rel="stylesheet">
        <script src="socialware/js/lightbox2-master/dist/js/lightbox.min.js"></script>

        <?php include("socialware/php/estructura/scripts.php"); ?>


        <!-- Scripts -->



        <script>
            $(function() {
                var mensaje = "<?php echo $mensaje ?>";

                if (mensaje !== "") {
                    $("#contenedor_mensaje").hide();
                    $("#contenedor_mensaje").removeClass("alert-success");
                    $("#contenedor_mensaje").removeClass("alert-danger");

                    if (mensaje.startsWith("ok - ")) {
                        $("#contenedor_mensaje span").html(mensaje.substring(5));
                        $("#contenedor_mensaje").addClass("alert-success");
                        $("#contenedor_mensaje").show();
                    } else {
                        $("#contenedor_mensaje span").html(mensaje);
                        $("#contenedor_mensaje").addClass("alert-danger");
                        $("#contenedor_mensaje").show();
                    }

                    $("body").animate({ scrollTop: 0 }, 'slow');
                }

                $(".bs-switch").bootstrapSwitch({
                    handleWidth: 110,
                    labelWidth: 110
                });
            });
            $(".select2").select2();
            // Llena combo de categorias
            /* var idExpo = $("#campo_idExpo").val();
                $.ajax({
                    type: "post",
                    url: "personalizado/php/ajax/cargaCategorias.php",
                    data: { idExpo: idExpo },
                    success: function(xml) {
                        var idCategoria = "<?php echo $idCategoria ?>";

                        $("#campo_idCategoria").append("<option value=''>Seleccione</option>");

                        $(xml).find("categoria").each(function() {
                            var id = $(this).find("id").text();
                            var nombre = $(this).find("nombre").text();

                            $("#campo_idCategoria").append("<option " + (idCategoria == id ? "selected" : "") + " value='" + id + "'>" + nombre + "</option>");
                        });
                    }
                }); */

            // Regresa a la interfaz de origen
            
            function eliminaImagenGaleria(id, imagen, indice) {
                if (confirm("Al continuar se eliminará esta imagen y ya no se mostrará en la galería del post, ¿desea proceder?")) {
                    $.ajax({
                        data: {
                            id: id,
                            imagen: imagen
                        },
                        type: "post",
                        url: "socialware/php/ajax/eliminaImagenGaleriaPost.php",
                        success: function(resultado) {
                            if (resultado === "ok") {
                                $("#contenedor_imagen_" + indice).hide();
                            }
                        }
                    });
                }
            }

            $(".link_origen").click(function() {
                $("#formulario_origen").submit();
            });


            // Borra una imagen de la galeria

        </script>
    </body>
</html>
