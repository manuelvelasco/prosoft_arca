<?php
    setlocale(LC_ALL, "es_MX");     // Desarrollo
    //setlocale(LC_ALL, "esm");     // Servidor
    date_default_timezone_set('America/Monterrey');


    // Carga plugin Mailjet


    //require "/Users/mvelasco/Socialware/Proyectos/Web/arca/backend/plugins/mailjet/vendor/autoload.php";
    //require "/var/www/html/arca/backend/plugins/mailjet/vendor/autoload.php";
    require "/home/1678977.cloudwaysapps.com/zxkxdcbsvk/public_html/backend/plugins/mailjet/vendor/autoload.php";

    use Mailjet\Resources;


    /*
     * Comunicacion con base de datos
     */


    function consulta($conexion, $consulta) {
        $conexion->query("SET NAMES 'utf8'");
        return $conexion->query($consulta);
    }


    function cuentaResultados($resultados) {
        if ($resultados == false) {
            return 0;
        } else {
            return mysqli_num_rows($resultados);
        }
    }


    function escapa($conexion, $valor) {
        return mysqli_real_escape_string($conexion, $valor);
    }


    function liberaConexion($conexion) {
        $conexion->close();
    }


    function liberaResultados($resultados) {
        $resultados->free();
    }


    function obtenConexion() {
        //return new mysqli("localhost", "root", "root", "arca");
        //return new mysqli("stagedb-2.c7vpephv5j2g.us-east-1.rds.amazonaws.com", "app_respaldos_dbuser", "2brv7b24rV,8sdu8c", "arca");
        //return new mysqli("stagedb-2.c7vpephv5j2g.us-east-1.rds.amazonaws.com", "app_respaldos_dbuser", "n28r75vRNcaSN91BCta_wuxbvAj", "arca");
        //return new mysqli("production.c7vpephv5j2g.us-east-1.rds.amazonaws.com", "app_respaldos_dbuser", "2brv7b24rV,8sdu8c", "arca");
        return new mysqli("localhost", "zxkxdcbsvk", "SATj6tM6RQ", "zxkxdcbsvk");
    }


    function obtenResultado($resultados) {
        if ($resultados != null) {
            return $resultados->fetch_assoc();
        } else {
            return null;
        }
    }


    function registraEvento($evento) {

        // Inicializa variables

        $fechaActual = date("Y-m-d H:i:s");
        $idUsuario = $_SESSION["usuario_id"];

        // Obtiene conexion a base de datos

	$conexion = obtenConexion();

        // Registra evento

        consulta($conexion, "INSERT INTO log (fecha, evento, idUsuario) VALUES ('" . $fechaActual . "', '" . $evento . "', " . $idUsuario . ")");
    }


    function reiniciaResultados($resultados) {
        $resultados->data_seek(0);
    }


    /*
     * Control de datos
     */


    function estaVacio($valor) {
        //return (!isset($valor) || trim($valor) === "");

        // 1. Immediate check if the value is null (works for any type)
        if ($valor === null) {
            return true;
        }

        if (is_array($valor)) {
            return (sizeof($valor) == 0);
        }

        // 2. If it's a string, check if it's empty after removing whitespace
        if (is_string($valor)) {
            return trim($valor) === "";
        }

        // 3. For any other type (instantiated objects, booleans, non-zero numbers), it is not empty
        return false;
    }


    function esFecha($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }


    function esNumero($valor) {
        return is_numeric($valor);
    }


    function objetoAArreglo($d) {
	if (is_object($d)) {
	    return get_object_vars($d);
	} else if (is_array($d)) {
	    return array_map(__FUNCTION__, $d);
	} else {
	    return $d;
	}
    }


    function sanitiza($conexion, $valor) {
        if (!estaVacio($valor) && is_string($valor)) {
            //$valor = str_replace("SELECT", "", strtoupper($valor));

            // Elimina SQL Injection

            $valor = preg_replace("/\bDATABASE\b/iu", "", $valor);
            $valor = preg_replace("/\bEXECUTE\b/iu", "", $valor);
            $valor = preg_replace("/\bSHOW\b/iu", "", $valor);
            $valor = preg_replace("/\bSLEEP\b/iu", "", $valor);
            $valor = preg_replace("/\bSELECT\b/iu", "", $valor);
            $valor = preg_replace("/\bFROM\b/iu", "", $valor);
            $valor = preg_replace("/\bAND\b/iu", "", $valor);
            $valor = preg_replace("/\bOR\b/iu", "", $valor);
            $valor = preg_replace("/\bNOT\b/iu", "", $valor);
            $valor = preg_replace("/\bIN\b/iu", "", $valor);
            $valor = preg_replace("/\bJOIN\b/iu", "", $valor);
            $valor = preg_replace("/\bINSERT\b/iu", "", $valor);
            $valor = preg_replace("/\bUPDATE\b/iu", "", $valor);
            $valor = preg_replace("/\bDELETE\b/iu", "", $valor);
            $valor = preg_replace("/\bTRUNCATE\b/iu", "", $valor);
            $valor = preg_replace("/\bDROP\b/iu", "", $valor);

            // Escapa caracteres especiales

            $valor = mysqli_real_escape_string($conexion, $valor);

            // Corrige generacion de saltos de linea de mysqli_real_escape_string

            $valor = str_replace(array('\r','\n'), '', $valor);
        }

        return $valor;
    }


    /*
     * Formato de datos
     */


    function eliminaCaracteresEspeciales($cadena){
        $caracteresOriginales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $caracteresModificados = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBSaaaaaaaceeeeiiiidnoooooouuuyybyRr';

        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($caracteresOriginales), $caracteresModificados);

        return utf8_encode($cadena);
    }


    function formatoDecimal($valor, $decimales = 2) {
        if (!estaVacio($valor)) {
            return number_format((float) $valor, $decimales);
        } else {
            return "$0.00";
        }
    }


    function formatoMillaresLetra($valor) {
        if (!estaVacio($valor)) {
            if ($valor > 1000) {
                if ($valor % 1000 == 0) {
                    $decimales = 0;
                } else {
                    $decimales = 1;
                }

                return number_format((float) $valor / 1000, $decimales) . "mil";
            } else {
                return number_format((float) $valor, 0);
            }
        } else {
            return "0";
        }
    }


    function formatoMoneda($valor, $decimales = 2) {
        if (!estaVacio($valor)) {
            return "$" . number_format((float) $valor, $decimales);
        } else {
            return "$0.00";
        }
    }


    function normalizaTelefono($telefono) {
        if (!estaVacio($telefono)) {
            return preg_replace('/\D/', '', $telefono);
        } else {
            return null;
        }
    }


    /*
     * Utilerias
     */


    function generaCadenaAleatoria($length = 3) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }


    function calculaTiempoDeInactividad($fechaInicial, $fechaFinal) {
        $tiempoInactividad = $fechaFinal->diff($fechaInicial);
        return $tiempoInactividad->d . " dias, " . $tiempoInactividad->h . " horas, " . $tiempoInactividad->i . " minutos";
    }


    function muestraBloqueo() {
        echo "<div class='table-struct full-width full-height'>"
                . "<div class='table-cell vertical-align-middle'>"
                    . "<div class='auth-form  ml-auto mr-auto no-float'>"
                        . "<div class='panel panel-default card-view mb-0'>"
                            . "<div class='panel-wrapper collapse in'>"
                                . "<div class='panel-body'>"
                                    . "<div class='row'>"
                                        . "<div class='col-sm-12 col-xs-12 text-center'>"
                                            . "<h3 class='mb-20 txt-danger'>Permisos insuficientes</h3>"
                                            . "<p class='font-18 txt-dark mb-15'>Tu usuario no cuenta con permiso para utilizar esta funci&oacute;n</p>"
                                            . "<p>Estos intentos quedan registrados autom&aacute;ticamente</p>"
                                        . "</div>"
                                    . "</div>"
                                . "</div>"
                            . "</div>"
                        . "</div>"
                    . "</div>"
                . "</div>"
            . "</div>";
    }

/*
    function borraDirectorio($directorio) {
        if ($directorio !== "/" && is_dir($directorio)) {
            $objetos = scandir($directorio);

            foreach ($objetos as $objeto) {
                if ($objeto != "." && $objeto != "..") {
                    if (is_dir($directorio . "/" . $objeto))
                        borraDirectorio($directorio . "/" . $objeto);
                    else
                        unlink($directorio . "/" . $objeto);
                }
            }

            rmdir($directorio);
        }
    }
*/

    function registraAnalitica($url, $selector, $evento) {

        // Inicializa variables

        $fechaActual = date("Y-m-d H:i:s");

        // Obtiene conexion a base de datos

	$conexion = obtenConexion();

        // Registra evento

        consulta($conexion, "INSERT INTO analitica (fechaRegistro, url, selector, evento) VALUES ('" . $fechaActual . "', '" . $url . "', " . (estaVacio($selector) ? "NULL" : "'" . $selector . "'") . ", " . (estaVacio($evento) ? "NULL" : "'" . $evento . "'") . ")");
    }
?>
