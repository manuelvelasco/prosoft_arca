<?php
    header("Access-Control-Allow-Origin: *");

    include("../comunes/constantes.php");
    include("../comunes/funciones.php");

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        // Obtiene conexion a base de datos

        $conexion = obtenConexion();

        // Obtiene parametros de request

        $id = sanitiza($conexion, filter_input(INPUT_POST, "id"));

        // Consulta base de datos

        $post_BD = consulta($conexion, "SELECT * FROM post WHERE id = " . $id . " AND habilitado = 1");

        $resultado .= "<posts>";

        while ($post = obtenResultado($post_BD)) {
            $resultado .= "<post>";

            foreach (array_keys($post) as $llave) {
                $resultado .= "<" . $llave . ">" . $post[$llave] . "</" . $llave . ">";
            }

            // Complementa con post anterior

            $postAnterior_BD = consulta($conexion, "SELECT id, titulo FROM post WHERE id < " . $id . " AND habilitado = 1 ORDER BY id DESC LIMIT 1");

            if (cuentaResultados($postAnterior_BD) == 1) {
                $postAnterior = obtenResultado($postAnterior_BD);

                $resultado .= "<postAnterior_id>" . $postAnterior["id"] . "</postAnterior_id>";
                $resultado .= "<postAnterior_titulo>" . $postAnterior["titulo"] . "</postAnterior_titulo>";
            } else {
                $resultado .= "<postAnterior_id></postAnterior_id>";
                $resultado .= "<postAnterior_titulo></postAnterior_titulo>";
            }

            // Complementa con post posterior

            $postPosterior_BD = consulta($conexion, "SELECT id, titulo FROM post WHERE id > " . $id . " AND habilitado = 1 ORDER BY id LIMIT 1");

            if (cuentaResultados($postPosterior_BD) == 1) {
                $postPosterior = obtenResultado($postPosterior_BD);

                $resultado .= "<postPosterior_id>" . $postPosterior["id"] . "</postPosterior_id>";
                $resultado .= "<postPosterior_titulo>" . $postPosterior["titulo"] . "</postPosterior_titulo>";
            } else {
                $resultado .= "<postPosterior_id></postPosterior_id>";
                $resultado .= "<postPosterior_titulo></postPosterior_titulo>";
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
