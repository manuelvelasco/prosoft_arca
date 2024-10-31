<?php
    /*
     * Maneja el inicio de sesion de un usuario
     */

    session_start();

    include("../comunes/funciones.php");

    // Obtiene parametros de request

    $correoElectronico = filter_input(INPUT_POST, 'correoElectronico');
    $contrasena = filter_input(INPUT_POST, 'contrasena');

    // Inicializa variables

    $fechaActual = date("Y-m-d H:i:s");

    if (!estaVacio($correoElectronico) && !estaVacio($contrasena)) {

	// Busca al usuario en la base de datos

	$conexion = obtenConexion();

	$usuario_DB = consulta($conexion, "SELECT * FROM usuario WHERE correoElectronico  = '". $correoElectronico . "' AND contrasena = '" . md5($contrasena) . "' AND habilitado = 1");

        if (cuentaResultados($usuario_DB) > 0) {
	    $usuario = obtenResultado($usuario_DB);

            // Registra evento

            consulta($conexion, "INSERT INTO log (fecha, evento, idUsuario) VALUES ('" . $fechaActual . "', 'Inicio de sesion', " . $usuario["id"] . ")");

	    // Inicializa una nueva sesion

	    $_SESSION["usuario_id"] = $usuario["id"];
	    $_SESSION["usuario_rol"] = $usuario["rol"];
	    $_SESSION["usuario_nombre"] = $usuario["nombre"];
	    $_SESSION["usuario_correoElectronico"] = $usuario["correoElectronico"];
	    $_SESSION['sesion_horaDeInicio'] = time();
	    $_SESSION['sesion_horaDeExpiracion'] = $_SESSION['sesion_horaDeInicio'] + (30 * 60);

            $_SESSION["usuario_permisoConsultarVehiculos"] = $usuario["permisoConsultarVehiculos"];
            $_SESSION["usuario_permisoEditarVehiculos"] = $usuario["permisoEditarVehiculos"];
            $_SESSION["usuario_permisoConsultarConcesionarios"] = $usuario["permisoConsultarConcesionarios"];
            $_SESSION["usuario_permisoEditarConcesionarios"] = $usuario["permisoEditarConcesionarios"];

            echo "ok";
	} else {

	    // Mensaje de error en caso de que el usuario no haya sido encontrado

	    echo "No le hemos encontrado en nuestra base de datos o su contrase&ntilde;a no concuerda, por favor rectifique sus datos de acceso";
	}
    } else {

	// Mensaje de error general

	echo "Por favor proporcione sus datos de acceso";
    }
?>
