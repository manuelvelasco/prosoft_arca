<?php
    /*
     * Controla el ciclo de vida de sesion de un usuario
     */

    session_start();

    if (isset($_SESSION["sesion_horaDeExpiracion"])) {
	if ($_SESSION["sesion_horaDeExpiracion"] >= time()) {

	    // Renueva la sesion

	    //$_SESSION["sesion_horaDeExpiracion"] = time() + (30 * 60);
	    $_SESSION["sesion_horaDeExpiracion"] = time() + (12 * 60 * 60);
	} else {

	    // Destruye la sesion

	    session_unset();
	    session_destroy();

	    header("Location: acceso.php");
            die();
	}
    } else {
        header("Location: acceso.php");
        die();
    }
?>
