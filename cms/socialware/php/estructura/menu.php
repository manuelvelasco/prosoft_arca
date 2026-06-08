<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>

    <div class="app-sidebar">
        <div class="side-header">
            <a class="header-brand1" href="inicio.php">
                <img class="header-brand-img desktop-logo" alt="ARCA" src="../personalizado/img/Logotipo-ARCA-01-Web.png" />
                <img class="header-brand-img toggle-logo" alt="ARCA" src="../personalizado/img/Logotipo-ARCA-01-Web.png" />
                <img class="header-brand-img light-logo" alt="ARCA" src="../personalizado/img/Logotipo-ARCA-01-Web.png" />
                <img class="header-brand-img light-logo1" alt="ARCA" src="../personalizado/img/Logotipo-ARCA-01-Web.png" />
            </a>
        </div>

        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"/></svg></div>

            <ul class="side-menu" id="contenedor_menu">
                <li><a class="side-menu__item" href="inicio.php"><span class="side-menu__label">Inicio</span></a></li>
                <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                    <li class="slide">
                            <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i class="side-menu__icon fa fa-users"></i><span class="side-menu__label">Usuarios y permisos</span><i class="angle fe fe-chevron-right"></i></a>
                            <ul class="slide-menu open" style="display: block;">
                                <li class="side-menu-label1"><a href="javascript:void(0)">Crones</a></li>
                                <li><a class="sub-slide-item enlace_cron" href="usuarios.php">Consultar</a></li>
                                <li><a class="sub-slide-item enlace_cron" data-fancybox data-type="iframe" data-preload="false" data-height="900" href="usuario.php">Agregar</a></li>
                            </ul>
                    </li>
                <?php } ?>
                <?php if($esUsuarioMaster || $usuario_permisoConsultarConcesionarios){ ?>
                    <li><a class="side-menu__item" href="concesionarios.php"><i class="side-menu__icon fa fa-building"></i><span class="side-menu__label">Agencias</span></a></li>
                <?php } ?>

                <?php if($esUsuarioMaster || $usuario_permisoConsultarVehiculos){ ?>
                    <li><a class="side-menu__item" href="vehiculos.php"><i class="side-menu__icon fa fa-car"></i><span class="side-menu__label">Veh&iacute;culos</span></a></li>
                <?php } ?>

                <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                    <li><a class="side-menu__item" href="blogs.php"><i class="side-menu__icon fa fa-comments"></i><span class="side-menu__label">Blogs</span></a></li>
                <?php } ?>

                <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                    <li><a class="side-menu__item" href="contacto.php"><i class="side-menu__icon fa fa-envelope"></i><span class="side-menu__label">Contacto</span></a></li>
                <?php } ?>

                 <?php if ($esUsuarioMaster || $usuario_permisoConsultarDelegacionVirtual) { ?>
                    <li class="slide">
                            <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i class="side-menu__icon fa fa-file-contract"></i><span class="side-menu__label">Delegación virtual</span><i class="angle fe fe-chevron-right"></i></a>
                            <ul class="slide-menu open" style="display: block;">
                                <li><a class="sub-slide-item enlace_cron" href="mensajeros.php">Mensajeros</a></li>
                            </ul>
                            <ul class="slide-menu open" style="display: block;">
                                <li><a class="sub-slide-item enlace_cron" href="tramites.php">Trámites</a></li>
                            </ul>
                    </li>
                <?php } ?>
            </ul>

            <div class="slide-right" id="slide-right"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg></div>
        </div>
    </div>
</div>