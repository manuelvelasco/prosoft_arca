            <!-- Menu -->

            <div class="fixed-sidebar-left">
                <ul class="nav navbar-nav side-nav nicescroll-bar">
                    <li>
                        <a href="inicio.php">
                            Inicio
                        </a>
                    </li>

                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                        <li>
                            <a data-toggle="collapse" data-target="#menu_usuarios" href="javascript:void(0);">
                                Usuarios y permisos

                                <span class="pull-right">
                                    <span class="label label-default mr-10">2</span>
                                    <i class="fa fa-fw fa-angle-down"></i>
                                </span>
                            </a>

                            <ul class="collapse collapse-level-1" id="menu_usuarios">
                                <li><a href="usuarios.php">Consultar</a></li>
                                <li><a href="usuario.php">Agregar</a></li>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($usuario_permisoConsultarVehiculos || $usuario_permisoEditarVehiculos) { ?>
                        <li>
                            <a href="vehiculos.php">
                                Veh&iacute;culos
                            </a>
                            <!--
                            <a data-toggle="collapse" data-target="#menu_vehiculos" href="javascript:void(0);">
                                Veh&iacute;culos

                                <span class="pull-right">
                                    <span class="label label-default mr-10">2</span>
                                    <i class="fa fa-fw fa-angle-down"></i>
                                </span>
                            </a>

                            <ul class="collapse collapse-level-1" id="menu_vehiculos">
                                <li><a href="vehiculos.php">Consultar</a></li>
                                <li><a href="vehiculo.php">Agregar</a></li>
                            </ul>
                            -->
                        </li>
                    <?php } ?>

                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                        <li>
                            <a href="financiamientos.php">
                                Financiamientos
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                        <li>
                            <a href="blogs.php">
                                Blogs
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                        <li>
                            <a href="contacto.php">
                                Contacto
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                        <li>
                            <a href="intencionventa.php">
                                Intención Venta
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                        <li>
                            <a href="citas.php">
                                Citas
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($esUsuarioMaster || $esUsuarioAdministrador) { ?>
                        <li>
                            <a href="sucursales.php">
                                Sucursales
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($usuario_permisoConsultarConcesionarios || $usuario_permisoEditarConcesionarios) { ?>
                        <li>
                            <a href="concesionarios.php">
                                Concesionarios
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
