<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $palabraClave = sanitiza($conexion, filter_input(INPUT_POST, "palabraClave"));
        $pagina = sanitiza($conexion, filter_input(INPUT_POST, "pagina"));
        $tamanoPagina = sanitiza($conexion, filter_input(INPUT_POST, "tamanoPagina"));
        $municipio = sanitiza($conexion, filter_input(INPUT_POST, "municipio"));

        // Inicializa variables

        if (estaVacio($pagina)) {
            $pagina = 1;
        }

        if (estaVacio($tamanoPagina)) {
            $tamanoPagina = 10;
        }

        // Arma restricciones

        $restricciones = "";
        $ordenamiento = "";
        $limitante = "";

        if (!estaVacio($palabraClave)) {
            $restricciones .= " AND (LOWER(nombreComercial) LIKE '%" . strtolower($palabraClave) . "%' OR LOWER(resumen) LIKE '%" . strtolower($palabraClave) . "%' OR LOWER(descripcion) LIKE '%" . strtolower($palabraClave) . "%' OR LOWER(calle) LIKE '%" . strtolower($palabraClave) . "%' OR LOWER(colonia) LIKE '%" . strtolower($palabraClave) . "%' OR LOWER(municipio) LIKE '%" . strtolower($palabraClave) . "%')";
        }

        if (!estaVacio($municipio) && strpos($municipio, "VERTODO") === false) {
            $municipio = str_replace("\\", "", $municipio);

            $restricciones .= " AND municipio IN (" . $municipio . ")";
        }

        $limitante = " LIMIT " . (($pagina - 1) * $tamanoPagina) . ", " . $tamanoPagina;

        // Consulta base de datos
        //echo "SELECT COUNT(*) AS concesionariosEncontrados FROM concesionario WHERE habilitado = 1 AND eliminado = 0 " . $restricciones;
        $concesionariosEncontrados = obtenResultado(consulta($conexion, "SELECT COUNT(*) AS concesionariosEncontrados FROM concesionario WHERE habilitado = 1 AND eliminado = 0 " . $restricciones))["concesionariosEncontrados"];

        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE habilitado = 1 " . $restricciones . $limitante);

        $resultado .= "<concesionarios total='" . $concesionariosEncontrados . "'>";

        while ($concesionario = obtenResultado($concesionarios_BD)) {
            $resultado .= "<concesionario>";

            foreach (array_keys($concesionario) as $llave) {
                $resultado .= "<" . $llave . ">" . $concesionario[$llave] . "</" . $llave . ">";
            }

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