<!-- Encabezado -->


    <div class="app-header header sticky">
        <div class="container-fluid main-container">
            <div class="d-flex">
                <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="javascript:void(0)"></a>

                <a class="logo-horizontal" href="dashboard.html">
                    <img alt="ARCA" class="header-brand-img desktop-logo" src="../personalizado/img/Logotipo-ARCA-01-Web.png" />
                    <img alt="ARCA" class="header-brand-img light-logo1" src="../personalizado/img/Logotipo-ARCA-01-Web.png" />
                </a>

                <div class="d-flex order-lg-2 ms-auto header-right-icons">
                    <div class="navbar navbar-collapse responsive-navbar p-0">
                        <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                            <div class="d-flex order-lg-2">


                                <!-- Switch pantalla completa -->


                                <div class="dropdown d-flex">
                                    <a class="nav-link icon full-screen-link nav-link-bg">
                                        <i class="fe fe-minimize fullscreen-button"></i>
                                    </a>
                                </div>


                                <!-- Switch modo obscuro -->


                                <div class="dropdown d-flex">
                                    <a class="nav-link icon theme-layout nav-link-bg layout-setting">
                                        <span class="dark-layout"><i class="fe fe-moon"></i></span>
                                        <span class="light-layout"><i class="fe fe-sun"></i></span>
                                    </a>
                                </div>


                                <!-- Menu usuario -->


                                <div class="dropdown d-flex profile-1">
                                    <a class="nav-link leading-none d-flex" data-bs-toggle="dropdown" href="javascript:void(0)">
                                        <img alt="" class="avatar profile-user brround cover-image" src="assets/images/users/21.jpg" />
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow perfilUsuario">
                                        <div class="drop-heading">
                                            <div class="text-center">
                                                <h5 class="text-dark mb-0 fs-14 fw-semibold" id="contenedor_encabezado_usuario"><?php echo $usuario_nombre;?></h5>
                                                <small class="text-muted">@ARCA</small>
                                            </div>
                                        </div>

                                        <div class="dropdown-divider m-0"></div>

                                        <a class="dropdown-item" href="javascript:;" id="link_cerrarSesion">
                                            <i class="dropdown-icon fe fe-alert-circle"></i> Cerrar sesión
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
