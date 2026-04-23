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
            $rangoFechaAlta = sanitiza($conexion, filter_input(INPUT_POST, "rangoFechaAlta"));
            $idUsuario = sanitiza($conexion, filter_input(INPUT_POST, "idUsuario"));
            $idMensajero = sanitiza($conexion, filter_input(INPUT_POST, "idMensajero"));
            $status = sanitiza($conexion, filter_input(INPUT_POST, "status"));
            $folioARCA = sanitiza($conexion, filter_input(INPUT_POST, "folioARCA"));
            $folioICV = sanitiza($conexion, filter_input(INPUT_POST, "folioICV"));
            $ejecutivoICV = sanitiza($conexion, filter_input(INPUT_POST, "ejecutivoICV"));
            $vehiculo_vin = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_vin"));
            $vehiculo_marca = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_marca"));
            $vehiculo_ano = sanitiza($conexion, filter_input(INPUT_POST, "vehiculo_ano"));
            $cliente_nombre = sanitiza($conexion, filter_input(INPUT_POST, "cliente_nombre"));

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
                        <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                            <div class="main-container container-fluid">


                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Listado de trámites</h1>
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
                                                            <a class="btn btn-primary" data-fancybox data-type="iframe" data-preload="false" data-height="900" href="tramite.php" id="boton_agregar">Agregar trámite</a>
                                                            &nbsp;&nbsp;&nbsp;
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

                                                <form action="tramites.php" method="post">
                                                    <input name="esSubmit" type="hidden" value="1" />

                                                    <div class="row mb-20">
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_idConcesionario">Rango de fechas de alta</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_rangoFechaAlta" name="rangoFechaAlta" type="text" value="<?php echo $rangoFechaAlta; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_idConcesionario">Agencia</label>
                                                                        <div class="col-md-8">
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
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_idUsuario">Usuario Agencia</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_idUsuario" name="idUsuario">
                                                                                <option <?php echo estaVacio($idUsuario) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                        
                                                                                    if ($esUsuarioMaster || $esUsuarioAdministrador) {

                                                                                        if(estaVacio($idConcesionario)){
                                                                                            $usuarios_BD = consulta($conexion, "SELECT * FROM usuario s WHERE s.rol != 'Master' ORDER BY nombre");
                                                                                        }else{
                                                                                            $usuarios_BD = consulta($conexion, "SELECT * FROM usuario s WHERE s.rol != 'Master' AND s.idConcesionario = " . $idConcesionario . " ORDER BY nombre");
                                                                                        }
                                                                                        

                                                                                    }else{

                                                                                        $usuarios_BD = consulta($conexion, "SELECT * FROM usuario c WHERE s.rol != 'Master' AND c.idConcesionario = " . $usuario_idConcesionario. " ORDER BY rol");

                                                                                    }

                                                                                    while ($usuario_BD = obtenResultado($usuarios_BD)) {
                                                                                        echo "<option " . ($usuario_BD["id"] == $idUsuario ? "selected" : "") . " value='" . $usuario_BD["id"] . "'>" . $usuario_BD["nombre"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_idMensajero">Mensajero</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_idMensajero" name="idMensajero">
                                                                                <option <?php echo estaVacio($idMensajero) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                        
                                                                                    if ($esUsuarioMaster || $esUsuarioAdministrador) {
                                                                                        
                                                                                        $mensajeros_BD = consulta($conexion, "SELECT * FROM mensajero s ORDER BY nombre, apellidoPaterno, apellidoMaterno");

                                                                                    }else{

                                                                                        $mensajeros_BD = consulta($conexion, "SELECT * FROM mensajero m INNER JOIN mensajero_concesionario mc ON m.id = mc.idMensajero WHERE mc.idConcesionario = " . $usuario_idConcesionario. " ORDER BY nombre, apellidoPaterno, apellidoMaterno");

                                                                                    }

                                                                                    while ($mensajero_BD = obtenResultado($mensajeros_BD)) {
                                                                                        echo "<option " . ($mensajero_BD["id"] == $idMensajero ? "selected" : "") . " value='" . $mensajero_BD["id"] . "'>" . $mensajero_BD["nombre"] . " ". $mensajero_BD["apellidoPaterno"] . " " . $mensajero_BD["apellidoMaterno"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-20">
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_status">Estatus</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_status" name="status">
                                                                                <option <?php echo estaVacio($status) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                        
                                                                                    $status_BD = consulta($conexion, "SELECT DISTINCT status FROM tramite s ORDER BY status");

                                                                                    while ($estatu_BD = obtenResultado($status_BD)) {
                                                                                        echo "<option " . ($estatu_BD["status"] == $status ? "selected" : "") . " value='" . $estatu_BD["status"] . "'>" . $estatu_BD["status"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_folioARCA">Folio ARCA</label>
                                                                        <div class="col-md-8">
                                                                           <input class="form-control" id="campo_folioARCA" name="folioARCA" type="text" value="<?php echo $folioARCA; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_folioICV">Folio ICV</label>
                                                                        <div class="col-md-8">
                                                                           <input class="form-control" id="campo_folioICV" name="folioICV" type="text" value="<?php echo $folioICV; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_ejecutivoICV">Ejecutivo ICV</label>
                                                                        <div class="col-md-8">
                                                                           <input class="form-control" id="campo_ejecutivoICV" name="ejecutivoICV" type="text" value="<?php echo $ejecutivoICV; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-20">
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_vin">VIN</label>
                                                                        <div class="col-md-8">
                                                                           <input class="form-control" id="campo_vehiculo_vin" name="vehiculo_vin" type="text" value="<?php echo $vehiculo_vin; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_marca">Marca</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_vehiculo_marca" name="vehiculo_marca">
                                                                                <option <?php echo estaVacio($vehiculo_marca) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                        
                                                                                    $vehiculo_marcas_BD = consulta($conexion, "SELECT DISTINCT vehiculo_marca FROM tramite s ORDER BY vehiculo_marca");

                                                                                    while ($vehiculo_marca_BD = obtenResultado($vehiculo_marcas_BD)) {
                                                                                        echo "<option " . ($vehiculo_marca_BD["vehiculo_marca"] == $vehiculo_marca ? "selected" : "") . " value='" . $vehiculo_marca_BD["vehiculo_marca"] . "'>" . $vehiculo_marca_BD["vehiculo_marca"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_vehiculo_ano">Año</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Ver todo" id="campo_vehiculo_ano" name="vehiculo_ano">
                                                                                <option <?php echo estaVacio($vehiculo_ano) ? "selected" : "" ?> value="">Ver todo</option>

                                                                                <?php
                                                                                        
                                                                                    for($ano = 1900; $ano <= 2027; $ano++){
                                                                                        echo "<option " . ($ano == $vehiculo_ano ? "selected" : "") . " value='" . $ano . "'>" . $ano . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_cliente_nombre">Nombre Cliente</label>
                                                                        <div class="col-md-8">
                                                                           <input class="form-control" id="campo_cliente_nombre" name="cliente_nombre" type="text" value="<?php echo $cliente_nombre; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-20">
                                                        <div class="col-md-8 col-sm-6 col-xs-12"></div>
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
                                                                <th class="border-bottom-0">Fecha alta</th>
                                                                <th class="border-bottom-0">Folio ARCA</th>
                                                                <th class="border-bottom-0">Folio ICV</th>
                                                                <th class="border-bottom-0">Agencia</th>
                                                                <th class="border-bottom-0">Usuario agencia</th>
                                                                <th class="border-bottom-0">Nombre cliente</th>
                                                                <th class="border-bottom-0">Mensajero</th>
                                                                <th class="border-bottom-0">Status</th>
                                                                <th class="border-bottom-0">Ejecutivo ICV</th>
                                                                <th class="border-bottom-0">VIN</th>
                                                                <th class="border-bottom-0">Marca</th>
                                                                <th class="border-bottom-0">Modelo</th>
                                                                <th class="border-bottom-0">Año</th>
                                                                <th class="border-bottom-0">Acciones</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="contenedor_resultados">
                                                            <?php

                                                                    // Arma restricciones

                                                                    $restricciones = "";

                                                                    if (!estaVacio($rangoFechaAlta)) {
                                                                        $fechaAlta = explode("-", $rangoFechaAlta);
                                                                        $restricciones .= " AND t.fechaAlta >= STR_TO_DATE('" . $fechaAlta[0] . "','%m/%d/%Y') AND t.fechaAlta <= STR_TO_DATE('" . $fechaAlta[1] . "','%m/%d/%Y')";
                                                                    }

                                                                    if (!estaVacio($idConcesionario)) {
                                                                        $restricciones .= " AND t.idConcesionario = '" . $idConcesionario ."'";
                                                                    }

                                                                    if (!estaVacio($idUsuario)) {
                                                                        $restricciones .= " AND t.idUsuario = '" . $idUsuario ."'";
                                                                    }

                                                                    if (!estaVacio($idMensajero)) {
                                                                        $restricciones .= " AND t.idMensajero = '" . $idMensajero ."'";
                                                                    }

                                                                    if (!estaVacio($status)) {
                                                                        $restricciones .= " AND t.status = '" . $status ."'";
                                                                    }

                                                                    if (!estaVacio($folioARCA)) {
                                                                        $restricciones .= " AND t.folioARCA = '" . $folioARCA ."'";
                                                                    }

                                                                    if (!estaVacio($folioICV)) {
                                                                        $restricciones .= " AND t.folioICV = '" . $folioICV ."'";
                                                                    }

                                                                    if (!estaVacio($ejecutivoICV)) {
                                                                        $restricciones .= " AND t.ejecutivoICV = '" . $ejecutivoICV ."'";
                                                                    }

                                                                    if (!estaVacio($vehiculo_vin)) {
                                                                        $restricciones .= " AND t.vehiculo_vin = '" . $vehiculo_vin ."'";
                                                                    }

                                                                    if (!estaVacio($vehiculo_marca)) {
                                                                        $restricciones .= " AND t.vehiculo_marca = '" . $vehiculo_marca ."'";
                                                                    }

                                                                    if (!estaVacio($vehiculo_ano)) {
                                                                        $restricciones .= " AND t.vehiculo_ano = '" . $vehiculo_ano ."'";
                                                                    }

                                                                    if (!estaVacio($cliente_nombre)) {
                                                                        $restricciones .= " AND CONCAT(t.cliente_nombre, ' ',t.cliente_apellidoPaterno,' ',t.cliente_apellidoMaterno) = '" . $cliente_nombre .  "'";
                                                                    }

                                                                    // Consulta base de datos

                                                                    $tramites_BD = consulta($conexion, "SELECT 
                                                                                                                t.id,
                                                                                                                t.fechaAlta,
                                                                                                                t.folioARCA,
                                                                                                                t.folioICV,
                                                                                                                c.nombreComercial,
                                                                                                                u.nombre AS nombreUsuario,
                                                                                                                t.cliente_nombre,
                                                                                                                t.cliente_apellidoPaterno,
                                                                                                                t.cliente_apellidoMaterno,
                                                                                                                m.nombre,
                                                                                                                m.apellidoPaterno,
                                                                                                                m.apellidoMaterno,
                                                                                                                t.status,
                                                                                                                t.ejecutivoICV,
                                                                                                                t.vehiculo_vin,
                                                                                                                t.vehiculo_marca,
                                                                                                                t.vehiculo_modelo,
                                                                                                                t.vehiculo_ano
                                                                                                            FROM 
                                                                                                                tramite t
                                                                                                            INNER JOIN concesionario c ON t.idConcesionario = c.id
                                                                                                            INNER JOIN usuario u ON t.idUsuario = u.id
                                                                                                            INNER JOIN mensajero m ON t.idMensajero = m.id
                                                                                                            WHERE t.eliminado = 0 " . $restricciones . " ORDER BY t.id");


                                                                    while ($tramite = obtenResultado($tramites_BD)) {
                                                                        echo "<tr>";
                                                                        echo "<td>" . $tramite["id"] . "</td>";
                                                                        echo "<td>" . $tramite["fechaAlta"] . "</td>";
                                                                        echo "<td>" . $tramite["folioARCA"] . "</td>";
                                                                        echo "<td>" . $tramite["folioICV"] . "</td>";
                                                                        echo "<td>" . $tramite["nombreComercial"] . "</td>";
                                                                        echo "<td>" . $tramite["nombreUsuario"] . "</td>";
                                                                        echo "<td>" . $tramite["cliente_nombre"] . " " . $tramite["cliente_apellidoPaterno"] . " " . $tramite["cliente_apellidoMaterno"] . "</td>";
                                                                        echo "<td>" . $tramite["nombre"] . " " . $tramite["apellidoPaterno"] . " " . $tramite["apellidoMaterno"] . "</td>";
                                                                        echo "<td>" . $tramite["status"] . "</td>";
                                                                        echo "<td>" . $tramite["ejecutivoICV"] . "</td>";
                                                                        echo "<td>" . $tramite["vehiculo_vin"] . "</td>";
                                                                        echo "<td>" . $tramite["vehiculo_marca"] . "</td>";
                                                                        echo "<td>" . $tramite["vehiculo_modelo"] . "</td>";
                                                                        echo "<td>" . $tramite["vehiculo_ano"] . "</td>";
                                                                        echo "<td>";
                                                                        //echo "<a class='link_editar' data-id='" . $tramite["id"] . "' data-toggle='tooltip' href='javascript:;' title='Ver detalle'><i class='fa fa-search'></i></a>";
                                                                        echo "<a data-fancybox data-type='iframe' data-preload='false' data-height='1080' href='tramite.php?id=" . $tramite["id"] . "' title='Editar'><i class='fa fa-search'></i></a>";

                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    registraEvento("CMS : Consulta de trámites");
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
                                registraEvento("CMS : Consulta de trámites bloqueada");
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

                $('#campo_rangoFechaAlta').daterangepicker();
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

            $("#boton_limpiarFiltros").click(function(){
                $("#campo_rangoFechaAlta").val('');
                $("#campo_idConcesionario").val('').trigger('change');
                $("#campo_idUsuario").val('').trigger('change');
                $("#campo_idMensajero").val('').trigger('change');
                $("#campo_status").val('').trigger('change');
                $("#campo_folioARCA").val('');
                $("#campo_folioICV").val('');
                $("#campo_ejecutivoICV").val('');
                $("#campo_vehiculo_vin").val('');
                $("#campo_vehiculo_marca").val('').trigger('change');
                $("#campo_vehiculo_ano").val('').trigger('change');
                $("#campo_cliente_nombre").val('');

            });

            $("#campo_idConcesionario").on("change",function(){
                var idConcesionario = $("#campo_idConcesionario").val();

                $("#campo_idUsuario").html("");

                $.ajax({
                    url: "socialware/php/ajax/cargaUsuariosConcesionario.php",
                    type: "post",
                    data: { idConcesionario: idConcesionario }
                }).done(function (resultado, textStatus, jqXHR) {
                    $("#campo_idUsuario").append("<option value=''>Ver todo</option>");
                    $(resultado).find("usuario").each(function (index) {

                        var id = $(this).find("id").text();
                        var nombre = $(this).find("nombre").text();

                        $("#campo_idUsuario").append("<option value='" + id + "'>" + nombre + "</option>");
                    });
                    $("#campo_idUsuario").select2("destroy");
                    $("#campo_idUsuario").select2();

                });
            });


        </script>
    </body>
</html>
