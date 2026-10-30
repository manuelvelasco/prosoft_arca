<?php
    header("Access-Control-Allow-Origin: *");

    session_start();

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {
        $usuario_id = isset($_SESSION["usuario_id"]) ? sanitiza($conexion, $_SESSION["usuario_id"]) : "";
        $usuario_rol = isset($_SESSION["usuario_rol"]) ? sanitiza($conexion, $_SESSION["usuario_rol"]) : "";

        // Inicializa variables para cotejamiento de permisos de acceso

        $esUsuarioMaster = ($usuario_rol === "Master");
        $esUsuarioAdministrador = ($usuario_rol === "Administrador");

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Consulta base de datos

        if ($esUsuarioMaster || $esUsuarioAdministrador) {
            $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE habilitado = 1 and eliminado = 0 ORDER BY nombreComercial");
        } else {
            $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = (SELECT idConcesionario FROM usuario WHERE id = " . $usuario_id. ") ORDER BY nombreComercial");
        }

        $resultado .= "<concesionarios>";

        while ($concesionario = obtenResultado($concesionarios_BD)) {
            $resultado .= "<concesionario>";
            $resultado .= "<id>" . $concesionario["id"] . "</id>";
            $resultado .= "<nombre>" . $concesionario["nombre"] . "</nombre>";
            $resultado .= "</concesionario>";
        }

        $resultado .= "</concesionarios>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
