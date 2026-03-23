<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <style>

            /* Botones */

            #boton_cerrar {
                margin-right: 0;
            }

            #boton_eliminar {
                margin-left: 0;
            }

            #contenedor_botonesDerechos {
                text-align: right;
            }

            #contenedor_botonesIzquierdos {
                text-align: left;
            }

            /* Despliegue embebido sin margen izquierdo por ausencia de menu */

            .app-content {
                margin-left: 0;
            }

            /* Tablas de permisos */

            .tabla_permisos {
                width: 100%;
            }

            .tabla_permisos th {
                text-align: left;
                width: 50%;
            }

            /* Varios */

            hr {
                border-color: #888;
            }
        </style>
    </head>


    <body class="app ltr">
        <?php

            // Obtiene parametros de request

            $esSubmit = filter_input(INPUT_POST, "esSubmit");
            $id = sanitiza($conexion, filter_input(INPUT_GET, "id"));
            if($esSubmit == 1){
                $id = sanitiza($conexion, filter_input(INPUT_POST, "id"));
            }
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

        <div id="global-loader">
            <img alt="Cargando..." class="loader-img" src="assets/images/loader.svg" />
        </div>

        <div class="page">
            <div class="page-main">


                <!-- Menu oculto -->


                <div class="main-sidemenu" style="display: none;"> 
                    <div class="slide-left disabled" id="slide-left"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"/></svg></div>

                    <ul class="side-menu" id="contenedor_menu"></ul>

                    <div class="slide-right" id="slide-right"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg></div>
                </div>


                <!-- Contenido -->

                <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>

                    <div class="main-content app-content mt-0">
                        <div class="side-app">
                            <div class="main-container container-fluid">
                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Detalle de post</h1>

                                    <div>
                                        <!--
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="dashboard.html">Inicio</a></li>
                                            <li class="breadcrumb-item"><a href="javascript:void(0)">Catálogos</a></li>
                                            <li class="breadcrumb-item"><a href="javascript:void(0)">Usuarios</a></li>
                                            <li aria-current="page" class="breadcrumb-item active">Detalle de usuario</li>
                                        </ol>
                                        -->
                                    </div>
                                </div>
                                <form autocomplete="off" enctype="multipart/form-data" id="formulario" method="post" >

                                    <input id="campo_esSubmit" name="esSubmit" type="hidden" value="1" />

                                    <div class="alert" id="contenedor_mensaje">
                                        <span></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5><strong>Información de control</strong></h5>

                                                    <hr />

                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_id">ID</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_id" name="id" readonly type="text" value="<?php echo $id; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_fecha">Fecha de registro</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_fecha" name="fecha" readonly type="text" value="<?php echo $fecha; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_habilitado">Publicado</label>
                                                                        <div class="col-md-8">
                                                                            <div class="material-switch">
                                                                                <input <?php echo $habilitado == 1 ? "checked" : "" ?> id="campo_habilitado" name="habilitado" type="checkbox" />
                                                                                <label class="label-info" for="campo_habilitado"></label>
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
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Datos generales</strong></h5>

                                                    <hr />

                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-20">
                                                                        <label class="form-label col-md-2" for="campo_titulo">Título <span class="text-danger">*</span></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" id="campo_titulo" name="titulo" type="text" value="<?php echo $titulo; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-20">
                                                                        <label class="form-label col-md-2" for="campo_autor">Autor <span class="text-danger">*</span></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" id="campo_autor" name="autor" type="text" value="<?php echo $autor; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md64 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-20">
                                                                        <label class="form-label col-md-2" for="campo_etiquetas">Etiquetas </label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" id="campo_etiquetas" name="etiquetas" placeholder="Separa con comas" type="text" value="<?php echo $etiquetas; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-20">
                                                                        <label class="form-label col-md-2" for="campo_categoria">Categoría <span class="text-danger">*</span></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" id="campo_categoria" name="categoria" type="text" value="<?php echo $categoria; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-20">
                                                                        <label class="form-label col-md-1" for="campo_resumen">Resumen </label>
                                                                        <div class="col-md-11">
                                                                            <textarea class="form-control" id="campo_resumen" name="resumen" rows="3"><?php echo $resumen ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-20">
                                                                        <label class="form-label col-md-1" for="campo_detalle">Detalle</label>
                                                                        <div class="col-md-11">
                                                                            <textarea class="tinymce" id="campo_detalle" name="detalle"><?php echo $detalle ?></textarea>
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

                                    <?php if (!estaVacio($id)) { ?>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5><strong>Multimedia</strong></h5>

                                                        <hr />

                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
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
                                                                                            <div class="panel-wrapper in">
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
                                                            </div>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
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
                                                                                            <div class="panel-wrapper in">
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

                                    <div class="row mt-6">
                                        <div class="col-md-8 col-sm-6" id="contenedor_botonesIzquierdos">
                                            <div class="form-group">
                                                <!--a class="btn btn-danger mb-3" href="javascript:void(0)" id="boton_eliminar">Eliminar</a-->
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6" id="contenedor_botonesDerechos">
                                            <div class="form-group">
                                                <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                                                    <button class="btn btn-success mb-3" type="submit" id="boton_guardar">Guardar</button>
                                                <?php } ?>
                                                <a class="btn btn-default mb-3" href="javascript:void(0)" id="boton_cerrar">Cerrar</a>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    } else {
                        registraEvento("CMS : Consulta de post bloqueada | id = " . $id);
                        muestraBloqueo();
                    }
                ?>

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

            // Borra una imagen de la galeria
            
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



        </script>
    </body>
</html>
