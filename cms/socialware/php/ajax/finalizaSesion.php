<?php
    /*
     * Maneja el cierre de sesion de un usuario
     */

    session_start();

    // Destruye la sesion

    session_unset();
    session_destroy();
?>
