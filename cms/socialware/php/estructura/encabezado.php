            <!-- Barra superior -->

            <nav class="navbar navbar-inverse navbar-fixed-top">
                <a id="toggle_nav_btn" class="toggle-left-nav-btn inline-block mr-20 pull-left" href="javascript:void(0);">
                    <i class="fa fa-bars"></i>
                </a>

                <a href="inicio.php">
                    <!--img alt="" class="brand-img pull-left" src="socialware/img/logotipo.jpg" /-->
                    <span style="color: orangered; font-family: 'Kodchasan', cursive; font-size: 18px; line-height: 60px; margin-left: 15px">
                        Administraci&oacute;n de Operaciones
                    </span>
                </a>

                <ul class="nav navbar-right top-nav pull-right">
                    <li style="border-right: 1px solid #ddd; padding-right: 10px">
                        <a href="javascript:;" style="cursor: default; ">
                            <?php echo $usuario_nombre; ?>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:;" id="link_cerrarSesion">
                            <i class="fa fa-fw fa-power-off"></i>
                            Cerrar sesi&oacute;n
                        </a>
                    </li>

                    <!--li class="dropdown">
                        <a href="#" class="dropdown-toggle pr-0" data-toggle="dropdown">
                            <img alt="" class="user-auth-img img-circle" src="dist/img/user1.png" />
                            <span class="user-online-status"></span>
                        </a>

                        <ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            <!--li class="divider"></li- ->

                            <li>
                                <a href="javascript:;" id="link_cerrarSesion">
                                    <i class="fa fa-fw fa-power-off"></i>
                                    Cerrar sesi&oacute;n
                                </a>
                            </li>
                        </ul>
                    </li-->
                </ul>
            </nav>
