<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $cantidad = sanitiza($conexion, filter_input(INPUT_POST, "cantidad"));
        $pagina = sanitiza($conexion, filter_input(INPUT_POST, "pagina"));

        // Inicializa variables

        $tamanoPagina = 9;

        // Arma restricciones

        $restricciones = "";
        $ordenamiento = " ORDER BY id DESC";
        $limitante = "";

        if (!estaVacio($cantidad) && $cantidad > 0) {
            $limitante = " LIMIT " . $cantidad;
        } else {
            if (estaVacio($pagina)) {
                $pagina = 1;
            }

            $limitante = " LIMIT " . (($pagina - 1) * $tamanoPagina) . ", " . $tamanoPagina;
        }

        // Consulta base de datos

        $postsEncontrados = obtenResultado(consulta($conexion, "SELECT COUNT(*) AS postsEncontrados FROM post WHERE habilitado = 1 " . $restricciones))["postsEncontrados"];

        $posts_BD = consulta($conexion, "SELECT id, fecha, categoria, titulo, resumen, imagenPrincipal FROM post WHERE habilitado = 1 " . $restricciones . $ordenamiento . $limitante);

        $resultado .= "<posts total='" . $postsEncontrados . "'>";

        while ($post = obtenResultado($posts_BD)) {
            $resultado .= "<post>";

            foreach (array_keys($post) as $llave) {
                $resultado .= "<" . $llave . ">" . $post[$llave] . "</" . $llave . ">";
            }

            $resultado .= "</post>";
        }

        $resultado .= "</posts>";

        // Cierra la conexion con base de datos y libera recursos

        liberaConexion($conexion);
    } catch (Exception $ex) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>
