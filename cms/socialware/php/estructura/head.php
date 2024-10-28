        <title>Albacar</title>

        <meta charset="UTF-8" />
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
        <meta content="Albacar" name="description" />
        <meta content="Albacar" name="keywords" />
        <meta content="Albacar" name="author" />

        <!-- Favicon -->
        <link href="favicon.ico" rel="shortcut icon">
        <link href="favicon.ico" rel="icon" type="image/x-icon">
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://www.datatables.net/rss.xml">

        <!-- Morris Charts CSS -->
        <link href="vendors/bower_components/morris.js/morris.css" rel="stylesheet" type="text/css"/>

        <!-- vector map CSS -->
        <link href="vendors/bower_components/jquery-wizard.js/css/wizard.css" rel="stylesheet" type="text/css"/>

        <!-- jquery-steps css -->
        <link rel="stylesheet" href="vendors/bower_components/jquery.steps/demo/css/jquery.steps.css">

        <!-- Data table CSS -->
        <link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>

        <!-- Jasny-bootstrap CSS -->
        <link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css"/>

        <!-- Custom Fonts -->
        <link href="dist/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- vector map CSS -->
        <link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css"/>

        <!-- select2 CSS -->
        <link href="vendors/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css"/>

        <!-- switchery CSS -->
        <link href="vendors/bower_components/switchery/dist/switchery.min.css" rel="stylesheet" type="text/css"/>

        <!-- bootstrap-select CSS -->
        <link href="vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>

        <!-- multi-select CSS -->
        <link href="vendors/bower_components/multiselect/css/multi-select.css" rel="stylesheet" type="text/css"/>

        <!-- Bootstrap Switches CSS -->
        <link href="vendors/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>

        <!-- Bootstrap Datetimepicker CSS -->
        <link href="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>

        <!-- Bootstrap Daterangepicker CSS -->
        <link href="vendors/bower_components/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>

        <!-- Bootstrap Wysihtml5 css -->
        <link href="vendors/bower_components/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.css" rel="stylesheet" />

        <!-- Custom CSS -->
        <link href="dist/css/style.css" rel="stylesheet" type="text/css">


        <!-- Personalizado -->


        <link href="socialware/css/estilos.css" rel="stylesheet" type="text/css">

        <link href="https://fonts.googleapis.com/css?family=Kodchasan|Audiowide|Gruppo" rel="stylesheet">

        <?php include("socialware/php/plugins/numeroAtexto_pesos.php"); ?>

        <?php

            /*
             * Mobile Detect
             * http://mobiledetect.net/
             */

            require_once "socialware/php/plugins/Mobile-Detect-2.8.25/Mobile_Detect.php";

            $mobileDetect = new Mobile_Detect;
            $esMovil = $mobileDetect->isMobile();

            // Obtiene conexion a base de datos

            $conexion = obtenConexion();

            // Obtiene parametros de sesion

            $usuario_id = isset($_SESSION["usuario_id"]) ? sanitiza($conexion, $_SESSION["usuario_id"]) : "";
            $usuario_rol = isset($_SESSION["usuario_rol"]) ? sanitiza($conexion, $_SESSION["usuario_rol"]) : "";
            $usuario_nombre = isset($_SESSION["usuario_nombre"]) ? sanitiza($conexion, $_SESSION["usuario_nombre"]) : "";
            $usuario_correoElectronico = isset($_SESSION["usuario_correoElectronico"]) ? sanitiza($conexion, $_SESSION["usuario_correoElectronico"]) : "";
            $sesion_horaDeInicio = isset($_SESSION["sesion_horaDeInicio"]) ? sanitiza($conexion, $_SESSION["sesion_horaDeInicio"]) : "";
            $sesion_horaDeExpiracion = isset($_SESSION["sesion_horaDeExpiracion"]) ? sanitiza($conexion, $_SESSION["sesion_horaDeExpiracion"]) : "";

            // Inicializa variables para cotejamiento de permisos de acceso

            $esUsuarioMaster = ($usuario_rol === "Master");
            $esUsuarioAdministrador = ($usuario_rol === "Administrador");
            $esUsuarioOperador = ($usuario_rol === "Operador");

            $usuario_permisoConsultarVehiculos = isset($_SESSION["usuario_permisoConsultarVehiculos"]) ? sanitiza($conexion, $_SESSION["usuario_permisoConsultarVehiculos"]) : "";
            $usuario_permisoEditarVehiculos = isset($_SESSION["usuario_permisoEditarVehiculos"]) ? sanitiza($conexion, $_SESSION["usuario_permisoEditarVehiculos"]) : "";
        ?>


        <style type="text/css">
            .navbar .brand-img {
                margin: 5px 0;
                max-width: 150px;
            }
/*
            .btn {
                 margin-bottom: 20px !important;
            }
*/

            .bootstrap-datetimepicker-widget dropdown-menu usetwentyfour top {
                z-index: 9999 !important;
            }

            .bootstrap-switch .bootstrap-switch-handle-on.bootstrap-switch-primary, .bootstrap-switch .bootstrap-switch-handle-off.bootstrap-switch-primary {
                background-color: #FAAB15;
            }

            .card-view:hover {
                box-shadow: none;
            }

            .columna_acciones {
                width: 150px;
            }

            #contenedor_mensaje {
                display: none;
            }

            .contenedor_mensaje {
                display: none;
            }

            .control-label {
                text-transform: none;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover, .dataTables_wrapper .dataTables_paginate .paginate_button:hover, .dataTables_wrapper .dataTables_paginate .paginate_button:active, .dataTables_wrapper .dataTables_paginate .paginate_button:focus {
                background-color: #566fc9;
                border-color: #566fc9;
            }

            .dropdown-menu > .active > a {
                background-color: #566fc9;
            }

            .dropdown-menu > .active > a:hover {
                background-color: #566fc9;
            }

            .etiquetaCampoRequerido {
                margin-left: 10px;
            }

            .fa {
                font-size: 16px;
            }

            .fixed-sidebar-left {
                width: 300px;
            }

            h5 {
                text-transform: none;
            }

            hr {
                margin-bottom: 10px;
            }

            .link_detalleProspecto {
                cursor: pointer;
            }

            .link_editar {
                cursor: pointer;
            }

            .link_eliminar {
                margin-left: 30px;
            }

            .link_habilitar {
                margin-left: 30px;
            }

            .nav-pills > li.active > a, .nav-pills > li.open > a {
                background-color: #566fc9 !important;
            }

            .page-wrapper {
                margin-left: 300px;
            }

            .separador {
                border-top: 1px solid #dadada;
                padding: 10px 0;
            }

            .side-nav {
                width: 100% !important;
            }

            .side-nav li a {
                text-transform: none;
            }

            .side-nav li a:hover {
                color: #566fc9;
            }

            .slide-nav-toggle .fixed-sidebar-left {
                margin-left: -300px;
            }

            .switchery {
                margin-top: 0;
            }

            .tab-pane {
                margin-top: 8px;
                margin-left: 20px;
            }

            .tabla_conceptos th, .tabla_conceptos td {
                padding-left: 3px;
                padding-right: 3px;
            }

            th, td {
                color: #2f2c2c !important;
                font-size: 10px;
            }

            @media (max-width: 1400px) {
                .fixed-sidebar-left {
                    margin-left: 0;
                }

                .slide-nav-toggle .page-wrapper {
                    left: 0;
                }
            }

            <?php if ($esMovil) { ?>
                .card-view {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }

                .page-wrapper, .container-fluid {
                    padding-left: 5px !important;
                    padding-right: 5px !important;
                }

                .panel-body {
                    padding-left: 5px !important;
                    padding-right: 5px !important;
                }
            <?php } ?>
        </style>
