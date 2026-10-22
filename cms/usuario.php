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

            $esSubmit = sanitiza($conexion, filter_input(INPUT_POST, "esSubmit"));
            $id = sanitiza($conexion, filter_input(INPUT_GET, "id"));
            $campo_id = sanitiza($conexion, filter_input(INPUT_POST, "campo_id"));
            $habilitado = sanitiza($conexion, filter_input(INPUT_POST, "habilitado"));
            $rol = sanitiza($conexion, filter_input(INPUT_POST, "rol"));
            $nombre = sanitiza($conexion, filter_input(INPUT_POST, "nombre"));
            $correoElectronico = sanitiza($conexion, filter_input(INPUT_POST, "correoElectronico"));
            $contrasena = sanitiza($conexion, filter_input(INPUT_POST, "contrasena"));
            $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));
            $permisoConsultarVehiculos = sanitiza($conexion, filter_input(INPUT_POST, "permisoConsultarVehiculos"));
            $permisoEditarVehiculos = sanitiza($conexion, filter_input(INPUT_POST, "permisoEditarVehiculos"));
            $permisoConsultarConcesionarios = sanitiza($conexion, filter_input(INPUT_POST, "permisoConsultarConcesionarios"));
            $permisoEditarConcesionarios = sanitiza($conexion, filter_input(INPUT_POST, "permisoEditarConcesionarios"));
            $permisoConsultarDelegacionVirtual = sanitiza($conexion, filter_input(INPUT_POST, "permisoConsultarDelegacionVirtual"));
            $permisoEditarDelegacionVirtual = sanitiza($conexion, filter_input(INPUT_POST, "permisoEditarDelegacionVirtual"));

            // Parametros enviados por origen

            $origen = sanitiza($conexion, filter_input(INPUT_POST, "origen"));

            // Inicializa variables

            $mensaje = "";
            $fechaActual = date("Y-m-d H:i:s");
            $habilitado = estaVacio($habilitado) ? 0 : 1;
            $permisoConsultarVehiculos = estaVacio($permisoConsultarVehiculos) ? 0 : 1;
            $permisoEditarVehiculos = estaVacio($permisoEditarVehiculos) ? 0 : 1;
            $permisoConsultarConcesionarios = estaVacio($permisoConsultarConcesionarios) ? 0 : 1;
            $permisoEditarConcesionarios = estaVacio($permisoEditarConcesionarios) ? 0 : 1;
            $permisoConsultarDelegacionVirtual = estaVacio($permisoConsultarDelegacionVirtual) ? 0 : 1;
            $permisoEditarDelegacionVirtual = estaVacio($permisoEditarDelegacionVirtual) ? 0 : 1;

            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($rol)) {
                    $mensaje .= "* Rol<br />";
                }

                if (estaVacio($nombre)) {
                    $mensaje .= "* Nombre<br />";
                }

                if (estaVacio($correoElectronico)) {
                    $mensaje .= "* Correo electrónico<br />";
                } else if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
                    $mensaje .= "* Correo electrónico con formato correcto<br />";
                }

                if (!estaVacio($id)) {
                    $usuario_BD = consulta($conexion, "SELECT id FROM usuario WHERE correoElectronico = '" . $correoElectronico . "' AND id <> " . $id);

                    if (cuentaResultados($usuario_BD) > 0) { 
                        $mensaje .="Ese correo ya se esta utilizando por otro usuario <br />";
                    }
                }

                if (estaVacio($idConcesionario) && $rol == 'Operador') {
                   $mensaje .="Para el rol Operador, es obligatorio asignarle una agencia <br />";
                }

                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                } else {
                    if (estaVacio($id)) {

                        // Es insercion

                        if (estaVacio($contrasena)) {
                            $mensaje .= "* Contraseña<br />";
                        }

                        if (!estaVacio($mensaje)) {
                            $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                        } else {
                            $usuario_BD = consulta($conexion, "SELECT id FROM usuario WHERE correoElectronico = '" . $correoElectronico . "'");

                            if (cuentaResultados($usuario_BD) > 0) {
                                $mensaje = "El usuario ya se encuentra registrado en la base de datos";
                            } else {
                                consulta($conexion, "INSERT INTO usuario ("
                                        . "habilitado"
                                        . ", rol"
                                        . ", nombre"
                                        . ", correoElectronico"
                                        . ", contrasena"
                                        . ", permisoConsultarVehiculos"
                                        . ", permisoEditarVehiculos"
                                        . ", permisoConsultarConcesionarios"
                                        . ", permisoEditarConcesionarios"
                                        . ", permisoConsultarDelegacionVirtual"
                                        . ", permisoEditarDelegacionVirtual"
                                        . ", idConcesionario"
                                    . ") VALUES ("
                                        . "1"
                                        . ", '" . $rol . "'"
                                        . ", '" . $nombre . "'"
                                        . ", '" . $correoElectronico . "'"
                                        . ", '" . md5($contrasena) . "'"
                                        . ", " . $permisoConsultarVehiculos
                                        . ", " . $permisoEditarVehiculos
                                        . ", " . $permisoConsultarConcesionarios
                                        . ", " . $permisoEditarConcesionarios
                                        . ", " . $permisoConsultarDelegacionVirtual
                                        . ", " . $permisoEditarDelegacionVirtual
                                        . ", " . (estaVacio($idConcesionario) ? "NULL" : $idConcesionario)
                                    . ")");

                                $usuario_BD = consulta($conexion, "SELECT * FROM usuario WHERE correoElectronico = '" . $correoElectronico . "'");
                                $usuario = obtenResultado($usuario_BD);

                                $id = $usuario["id"];
                                $habilitado = $usuario["habilitado"];
                                $rol = $usuario["rol"];
                                $nombre = $usuario["nombre"];
                                $correoElectronico = $usuario["correoElectronico"];
                                $contrasena = "";
                                $permisoConsultarVehiculos = $usuario["permisoConsultarVehiculos"];
                                $permisoEditarVehiculos = $usuario["permisoEditarVehiculos"];
                                $permisoConsultarConcesionarios = $usuario["permisoConsultarConcesionarios"];
                                $permisoEditarConcesionarios = $usuario["permisoEditarConcesionarios"];
                                $permisoConsultarDelegacionVirtual = $usuario["permisoConsultarDelegacionVirtual"];
                                $permisoEditarDelegacionVirtual = $usuario["permisoEditarDelegacionVirtual"];
                                $idConcesionario = $usuario["idConcesionario"];

                                $mensaje = "ok - El usuario ha sido registrado";

                                registraEvento("CMS : Alta de usuario | id = " . $id);
                            }
                        }
                    } else {

                        // Es actualizacion

                        consulta($conexion, "UPDATE usuario SET "
                            . "habilitado = " . $habilitado
                            . ", rol = '" . $rol . "'"
                            . ", nombre = '" . $nombre . "'"
                            . ", correoElectronico = '" . $correoElectronico . "'"
                            . ", permisoConsultarVehiculos = " . $permisoConsultarVehiculos
                            . ", permisoEditarVehiculos = " . $permisoEditarVehiculos
                            . ", permisoConsultarConcesionarios = " . $permisoConsultarConcesionarios
                            . ", permisoEditarConcesionarios = " . $permisoEditarConcesionarios
                            . ", permisoConsultarDelegacionVirtual = " . $permisoConsultarDelegacionVirtual
                            . ", permisoEditarDelegacionVirtual = " . $permisoEditarDelegacionVirtual
                            . ", idConcesionario = " . (estaVacio($idConcesionario) ? "NULL" : $idConcesionario)
                            . " WHERE id = " . $id);

                        if (!estaVacio($contrasena)) {
                            consulta($conexion, "UPDATE usuario SET "
                                . "contrasena = '" . md5($contrasena) . "'"
                                . " WHERE id = " . $id);
                        }

                        $usuario_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $id);
                        $usuario = obtenResultado($usuario_BD);

                        $id = $usuario["id"];
                        $habilitado = $usuario["habilitado"];
                        $rol = $usuario["rol"];
                        $nombre = $usuario["nombre"];
                        $correoElectronico = $usuario["correoElectronico"];
                        $contrasena = "";
                        $permisoConsultarVehiculos = $usuario["permisoConsultarVehiculos"];
                        $permisoEditarVehiculos = $usuario["permisoEditarVehiculos"];
                        $permisoConsultarConcesionarios = $usuario["permisoConsultarConcesionarios"];
                        $permisoEditarConcesionarios = $usuario["permisoEditarConcesionarios"];
                        $permisoConsultarDelegacionVirtual = $usuario["permisoConsultarDelegacionVirtual"];
                        $permisoEditarDelegacionVirtual = $usuario["permisoEditarDelegacionVirtual"];
                        $idConcesionario = $usuario["idConcesionario"];

                        $mensaje = "ok - Los cambios han sido guardados";

                        registraEvento("CMS : Actualización de usuario | id = " . $id);
                    }
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $usuario_BD = consulta($conexion, "SELECT * FROM usuario WHERE id = " . $id);
                    $usuario = obtenResultado($usuario_BD);

                    $id = $usuario["id"];
                    $habilitado = $usuario["habilitado"];
                    $rol = $usuario["rol"];
                    $nombre = $usuario["nombre"];
                    $correoElectronico = $usuario["correoElectronico"];
                    $contrasena = "";
                    $permisoConsultarVehiculos = $usuario["permisoConsultarVehiculos"];
                    $permisoEditarVehiculos = $usuario["permisoEditarVehiculos"];
                    $permisoConsultarConcesionarios = $usuario["permisoConsultarConcesionarios"];
                    $permisoEditarConcesionarios = $usuario["permisoEditarConcesionarios"];
                    $permisoConsultarDelegacionVirtual = $usuario["permisoConsultarDelegacionVirtual"];
                    $permisoEditarDelegacionVirtual = $usuario["permisoEditarDelegacionVirtual"];
                    $idConcesionario = $usuario["idConcesionario"];

                    registraEvento("CMS : Consulta de usuario | id = " . $id);
                } else {
                    $habilitado = 1;
                    $rol = "";
                    $nombre = "";
                    $correoElectronico = "";
                    $contrasena = "";
                    $permisoConsultarVehiculos = 0;
                    $permisoEditarVehiculos = 0;
                    $permisoConsultarConcesionarios = 0;
                    $permisoEditarConcesionarios = 0;
                    $permisoConsultarDelegacionVirtual = 0;
                    $permisoEditarDelegacionVirtual = 0;
                    $idConcesionario = 0;
                }
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


                <div class="main-content app-content mt-0">
                    <div class="side-app">
                        <div class="main-container container-fluid">
                            <!-- Titulo -->


                            <div class="page-header">
                                <h1 class="page-title">Edición de usuario</h1>

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
                            <form autocomplete="off" id="formulario" method="post">

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
                                                                    <label class="form-label col-md-4" for="campo_habilitado">Habilitado</label>
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
                                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-4">
                                                                    <label class="form-label col-md-4" for="campo_nombre">Nombre <span class="text-danger">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_nombre" name="nombre" type="text" value="<?php echo $nombre; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-4">
                                                                    <label class="form-label col-md-4" for="campo_rol">Rol <span class="text-danger">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_rol" name="rol">
                                                                            <?php if ($esUsuarioMaster) { ?>
                                                                                <option value="Master" <?php echo ($rol == "Master") ? "selected" : "" ?>>Master</option>
                                                                            <?php } ?>

                                                                            <option value="Administrador" <?php echo ($rol == "Administrador") ? "selected" : "" ?>>Administrador</option>
                                                                            <option value="Operador" <?php echo ($rol == "Operador") ? "selected" : "" ?>>Operador</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-4">
                                                                    <label class="form-label col-md-4" for="campo_idConcesionario">Agencia</label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Elige" id="campo_idConcesionario" name="idConcesionario">
                                                                            <option <?php echo estaVacio($idConcesionario) ? "selected" : "" ?> value="">Seleccione</option>
                                                                            <?php
                                                                                $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE habilitado = 1 and eliminado = 0 ORDER BY nombreComercial");

                                                                                while ($concesionarioBD = obtenResultado($concesionarios_BD)) {
                                                                                    echo "<option " . (!estaVacio($idConcesionario) && $idConcesionario == $concesionarioBD["id"] ? "selected" : "") . " value='" . $concesionarioBD["id"] . "'>" . $concesionarioBD["nombreComercial"] . "</option>";
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
                                                                <div class="row mb-4">
                                                                    <label class="form-label col-md-4" for="campo_correoElectronico">Correo electrónico <span class="text-danger">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_correoElectronico" name="correoElectronico" type="text" value="<?php echo $correoElectronico; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-4">
                                                                    <label class="form-label col-md-4" for="campo_contrasena">Contraseña</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_contrasena" name="contrasena" type="password" value="<?php echo $contrasena; ?>" />
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
                                                <h5><strong>Permisos</strong></h5>

                                                <hr />

                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-6">Interfaz</label>
                                                                    <label class="form-label col-md-3">Acceso</label>
                                                                    <label class="form-label col-md-3">Edición</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-6" >Agencias</label>
                                                                    <div class="col-md-3">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $permisoConsultarConcesionarios == 1 ? "checked" : "" ?> id="campo_permisoConsultarConcesionarios" name="permisoConsultarConcesionarios" type="checkbox" />
                                                                            <label class="label-info" for="campo_permisoConsultarConcesionarios"></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $permisoEditarConcesionarios == 1 ? "checked" : "" ?> id="campo_permisoEditarConcesionarios" name="permisoEditarConcesionarios" type="checkbox" />
                                                                            <label class="label-info" for="campo_permisoEditarConcesionarios"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-6" >Vehículos</label>
                                                                    <div class="col-md-3">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $permisoConsultarVehiculos == 1 ? "checked" : "" ?> id="campo_permisoConsultarVehiculos" name="permisoConsultarVehiculos" type="checkbox" />
                                                                            <label class="label-info" for="campo_permisoConsultarVehiculos"></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $permisoEditarVehiculos == 1 ? "checked" : "" ?> id="campo_permisoEditarVehiculos" name="permisoEditarVehiculos" type="checkbox" />
                                                                            <label class="label-info" for="campo_permisoEditarVehiculos"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-6" >Delegación virtual</label>
                                                                    <div class="col-md-3">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $permisoConsultarDelegacionVirtual == 1 ? "checked" : "" ?> id="campo_permisoConsultarDelegacionVirtual" name="permisoConsultarDelegacionVirtual" type="checkbox" />
                                                                            <label class="label-info" for="campo_permisoConsultarDelegacionVirtual"></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $permisoEditarDelegacionVirtual == 1 ? "checked" : "" ?> id="campo_permisoEditarDelegacionVirtual" name="permisoEditarDelegacionVirtual" type="checkbox" />
                                                                            <label class="label-info" for="campo_permisoEditarDelegacionVirtual"></label>
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

                                <div class="row mt-6">
                                    <div class="col-md-8 col-sm-6" id="contenedor_botonesIzquierdos">
                                        <div class="form-group">
                                            <!--a class="btn btn-danger mb-3" href="javascript:void(0)" id="boton_eliminar">Eliminar</a-->
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-6" id="contenedor_botonesDerechos">
                                        <div class="form-group">
                                            <button class="btn btn-success mb-3" type="submit" id="boton_guardar">Guardar</button>
                                            <a class="btn btn-default mb-3" href="javascript:void(0)" id="boton_cerrar">Cerrar</a>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <?php include("socialware/php/estructura/plugins.php"); ?>

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
/*
                $(".js-switch").each(function() {
                    new Switchery($(this)[0], $(this).data());
                });
*/
            });


            // Regresa a la interfaz de origen


            $(".link_origen").click(function() {
                $("#formulario_origen").submit();
            });

            $("#campo_permisoEditarConcesionarios").click(function(){
                if($("#campo_permisoEditarConcesionarios").is(":checked")){
                    $("#campo_permisoConsultarConcesionarios").prop("checked",true);
                }
            });

            $("#campo_permisoEditarVehiculos").click(function(){
                if($("#campo_permisoEditarVehiculos").is(":checked")){
                    $("#campo_permisoConsultarVehiculos").prop("checked",true);
                }
            });

            $("#campo_permisoEditarDelegacionVirtual").click(function(){
                if($("#campo_permisoEditarDelegacionVirtual").is(":checked")){
                    $("#campo_permisoConsultarDelegacionVirtual").prop("checked",true);
                }
            });

            $("#campo_permisoConsultarConcesionarios").click(function(){
                if(!$("#campo_permisoConsultarConcesionarios").is(":checked")){
                    $("#campo_permisoEditarConcesionarios").prop("checked",false);
                }
            });

            $("#campo_permisoConsultarVehiculos").click(function(){
                if(!$("#campo_permisoConsultarVehiculos").is(":checked")){
                    $("#campo_permisoEditarVehiculos").prop("checked",false);
                }
            });

            $("#campo_permisoConsultarDelegacionVirtual").click(function(){
                if(!$("#campo_permisoConsultarDelegacionVirtual").is(":checked")){
                    $("#campo_permisoEditarDelegacionVirtual").prop("checked",false);
                }
            });
        </script>
    </body>
</html>
