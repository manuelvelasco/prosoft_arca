<?php include("socialware/php/comunes/manejaSesion.php"); ?>


<!DOCTYPE html>


<html lang="es">
    <head>
        <?php include("socialware/php/comunes/constantes.php"); ?>

        <?php include("socialware/php/comunes/funciones.php"); ?>

        <?php include("socialware/php/estructura/head.php"); ?>

        <style>

            /* Botones */

            #boton_cerrar {
                margin-right: 0;
            }

            #boton_eliminar {
                margin-left: 0;
            }

            #contenedor_botonesDerechos {
                text-align: right;
            }

            #contenedor_botonesIzquierdos {
                text-align: left;
            }

            /* Despliegue embebido sin margen izquierdo por ausencia de menu */

            .app-content {
                margin-left: 0;
            }

            /* Tablas de permisos */

            .tabla_permisos {
                width: 100%;
            }

            .tabla_permisos th {
                text-align: left;
                width: 50%;
            }

            /* Varios */

            hr {
                border-color: #888;
            }
        </style>
    </head>


    <body class="app ltr">
        <?php
            require("socialware/php/plugins/fpdf181/fpdf.php");

            // Check if magic_quotes_runtime is active
            ini_set('magic_quotes_runtime', 0);
            //HTML2PDF by Clément Lavoillotte
            //ac.lavoillotte@noos.fr
            //webmaster@streetpc.tk
            //http://www.streetpc.tk

            //function hex2dec
            //returns an associative array (keys: R,G,B) from
            //a hex html code (e.g. #3FE5AA)
            
            function hex2dec($couleur = "#000000"){
                $R = substr($couleur, 1, 2);
                $rouge = hexdec($R);
                $V = substr($couleur, 3, 2);
                $vert = hexdec($V);
                $B = substr($couleur, 5, 2);
                $bleu = hexdec($B);
                $tbl_couleur = array();
                $tbl_couleur['R']=$rouge;
                $tbl_couleur['V']=$vert;
                $tbl_couleur['B']=$bleu;
                return $tbl_couleur;
            }

            //conversion pixel -> millimeter at 72 dpi
            function px2mm($px){
                return $px*25.4/72;
            }

            function txtentities($html){
                $trans = get_html_translation_table(HTML_ENTITIES);
                $trans = array_flip($trans);
                return strtr($html, $trans);
            }
            ////////////////////////////////////

            class PDF_HTML extends FPDF
            {
                //variables of html parser
                protected $B;
                protected $I;
                protected $U;
                protected $HREF;
                protected $fontList;
                protected $issetfont;
                protected $issetcolor;

                function __construct($orientation='P', $unit='mm', $format='A4')
                {
                    //Call parent constructor
                    parent::__construct($orientation,$unit,$format);
                    //Initialization
                    $this->B=0;
                    $this->I=0;
                    $this->U=0;
                    $this->HREF='';
                    $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
                    $this->issetfont=false;
                    $this->issetcolor=false;
                }

                function WriteHTML($html)
                {
                    //HTML parser

                    $html = trim($html);

                    $html = str_replace("&aacute;", "á", $html);
                    $html = str_replace("&eacute;", "é", $html);
                    $html = str_replace("&iacute;", "í", $html);
                    $html = str_replace("&oacute;", "ó", $html);
                    $html = str_replace("&uacute;", "ú", $html);
                    $html = str_replace("&ntilde;", "ñ", $html);

                    $html = str_replace("&Aacute;", "Á", $html);
                    $html = str_replace("&Eacute;", "É", $html);
                    $html = str_replace("&Iacute;", "Í", $html);
                    $html = str_replace("&Oacute;", "Ó", $html);
                    $html = str_replace("&Uacute;", "Ú", $html);
                    $html = str_replace("&Ntilde;", "Ñ", $html);

                    $html = str_replace("</li><li>", "<br>*  ", $html);
                    $html = str_replace("<li>", "<br><br>*  ", $html);
                    $html = str_replace("</li>", "<br><br>", $html);

                    $html = utf8_decode($html);


                    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
                    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
                    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
                    foreach($a as $i=>$e)
                    {
                        if($i%2==0)
                        {
                            //Text
                            if ($this->HREF) {
                                $this->PutLink($this->HREF,$e);
                            } else {
                                $this->SetRightMargin(100);
                                //$this->Write(5, stripslashes(txtentities($e)));

                                $cadena = trim(stripslashes(txtentities($e)));

                                $palabras = explode(" ", $cadena);
                                $subCadena = "";
                                $caracteresPorFila = 45;

                                foreach ($palabras as $palabra) {
                                    $palabra = trim($palabra);

                                    if (strlen($subCadena) + 1 + strlen($palabra) < $caracteresPorFila) {
                                        $subCadena .= " " . $palabra;
                                    } else if (strlen($subCadena) + 1 + strlen($palabra) == $caracteresPorFila) {
                                        $subCadena .= " " . $palabra;
                                        //$subCadena = $this->justificaCadena($subCadena, $caracteresPorFila);

                                        $this->Write(5, $subCadena);
                                        $this->Ln(5);

                                        $subCadena = "";
                                    } else {
                                        $subCadena = $this->justificaCadena($subCadena, $caracteresPorFila);

                                        $this->Write(5, $subCadena);
                                        $this->Ln(5);

                                        $subCadena = $palabra;
                                    }
                                }

                                if (strlen($subCadena) > 0) {
                                    $this->Write(5, trim($subCadena));
                                }




                            }
                        } else {
                            //Tag
                            if($e[0]=='/')
                                $this->CloseTag(strtoupper(substr($e,1)));
                            else
                            {
                                //Extract attributes
                                $a2=explode(' ',$e);
                                $tag=strtoupper(array_shift($a2));
                                $attr=array();
                                foreach($a2 as $v)
                                {
                                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                                        $attr[strtoupper($a3[1])]=$a3[2];
                                }
                                $this->OpenTag($tag,$attr);
                            }
                        }
                    }
                }

                function justificaCadena($cadena, $caracteresPorFila) {
                    $cadena = trim($cadena);

                    $espaciosFaltantes = $caracteresPorFila - strlen($cadena);

                    if ($espaciosFaltantes > 0) {
                        $palabras = explode(" ", $cadena);
                        $cadena = "";

                        foreach ($palabras as $palabra) {
                            $palabra = trim($palabra);
                            $cadena .= $palabra . " ";

                            if ($espaciosFaltantes > 0) {
                                $cadena .= " ";
                                $espaciosFaltantes--;
                            }
                        }

                        if ($espaciosFaltantes > 0) {
                            $cadena = $this->justificaCadena($cadena, $caracteresPorFila);
                        }
                    }

                    return $cadena;
                }

                function OpenTag($tag, $attr)
                {
                    //Opening tag
                    switch($tag){
                        case 'STRONG':
                            $this->SetStyle('B',true);
                            break;
                        case 'EM':
                            $this->SetStyle('I',true);
                            break;
                        case 'B':
                        case 'I':
                        case 'U':
                            $this->SetStyle($tag,true);
                            break;
                        case 'A':
                            $this->HREF=$attr['HREF'];
                            break;
                        case 'IMG':
                            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                                if(!isset($attr['WIDTH']))
                                    $attr['WIDTH'] = 0;
                                if(!isset($attr['HEIGHT']))
                                    $attr['HEIGHT'] = 0;
                                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                            }
                            break;
                        case 'TR':
                        case 'BLOCKQUOTE':
                        case 'BR':
                            //$this->Ln(5);
                            $this->Ln(1);
                            break;
                        case 'P':
                            //$this->Ln(10);
                            $this->Ln(5);
                            break;
                        case 'FONT':
                            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                                $coul=hex2dec($attr['COLOR']);
                                $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
                                $this->issetcolor=true;
                            }
                            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                                $this->SetFont(strtolower($attr['FACE']));
                                $this->issetfont=true;
                            }
                            break;
                    }
                }

                function CloseTag($tag)
                {
                    //Closing tag
                    if($tag=='STRONG')
                        $tag='B';
                    if($tag=='EM')
                        $tag='I';
                    if($tag=='B' || $tag=='I' || $tag=='U')
                        $this->SetStyle($tag,false);
                    if($tag=='A')
                        $this->HREF='';
                    if($tag=='FONT'){
                        if ($this->issetcolor==true) {
                            $this->SetTextColor(0);
                        }
                        if ($this->issetfont) {
                            $this->SetFont('arial');
                            $this->issetfont=false;
                        }
                    }
                }

                function SetStyle($tag, $enable)
                {
                    //Modify style and select corresponding font
                    $this->$tag+=($enable ? 1 : -1);
                    $style='';
                    foreach(array('B','I','U') as $s)
                    {
                        if($this->$s>0)
                            $style.=$s;
                    }
                    $this->SetFont('',$style);
                }

                function PutLink($URL, $txt)
                {
                    //Put a hyperlink
                    $this->SetTextColor(0,0,255);
                    $this->SetStyle('U',true);
                    $this->Write(5,$txt,$URL);
                    $this->SetStyle('U',false);
                    $this->SetTextColor(0);
                }


                function Header() {
                    //$this->Image("personalizado/plantillaPropiedad_encabezado.png", 10, 8, 190);
                    $this->SetFillColor(0, 0,0);
                    $this->setDrawColor(0,0,0);
                    $this->Rect(0,0,220,10,'F'); 
                    $this->Image('../socialware/img/logotipo_blanco.png',15,0,40);
                    
                    $this->Image('socialware/img/whatsapp_icono.png',77,3,3);
                    $this->SetY(2);
                    $this->SetX(80);
                    $this->SetTextColor(255,255,255);
                    $this->SetFont("Arial", "", 9);
                    $this->SetDrawColor(255,255,255);
                    $this->Cell(40,6,'(81) 1816 8933');
                    $this->Cell(40,6,'arca.admon2021@gmail.com','','','',false,'arca.admon2021@gmail.com');
                    $this->Cell(40,6,'seminuevosarca.com.mx','','','',false,'seminuevosarca.com.mx');




                }
            }//end of class|

//$rutaLog = "/var/www/html/arca/error.log";
/*error_log("\n\n[" . date("Y-m-d H:i:s") . "]", 3, $rutaLog);
error_log("\nidUsuario = " . $usuario_id, 3, $rutaLog);*/

            // Obtiene parametros de request

            $esSubmit = sanitiza($conexion, filter_input(INPUT_POST, "esSubmit"));
            $id = sanitiza($conexion, filter_input(INPUT_GET, "id"));

            if ($esSubmit == 1) {
                $id = sanitiza($conexion, filter_input(INPUT_POST, "id"));
            }

            $publicado = sanitiza($conexion, filter_input(INPUT_POST, "publicado"));
            $destacado = sanitiza($conexion, filter_input(INPUT_POST, "destacado"));
            $certificado = sanitiza($conexion, filter_input(INPUT_POST, "certificado"));
            $descuentoEspecial = sanitiza($conexion, filter_input(INPUT_POST, "descuentoEspecial"));
            $tipo = sanitiza($conexion, filter_input(INPUT_POST, "tipo"));
            $marca = sanitiza($conexion, filter_input(INPUT_POST, "marca"));
            $modelo = sanitiza($conexion, filter_input(INPUT_POST, "modelo"));
            $version = sanitiza($conexion, filter_input(INPUT_POST, "version"));
            $ano = sanitiza($conexion, filter_input(INPUT_POST, "ano"));
            $color = sanitiza($conexion, filter_input(INPUT_POST, "color"));
            $precio = sanitiza($conexion, filter_input(INPUT_POST, "precio"));
            $enganche = sanitiza($conexion, filter_input(INPUT_POST, "enganche"));
            $litros = sanitiza($conexion, filter_input(INPUT_POST, "litros")); //nullable
            $combustible = sanitiza($conexion, filter_input(INPUT_POST, "combustible")); //nullable
            $transmision = sanitiza($conexion, filter_input(INPUT_POST, "transmision")); //nullable
            $puertas = sanitiza($conexion, filter_input(INPUT_POST, "puertas")); //nullable
            $asientos = sanitiza($conexion, filter_input(INPUT_POST, "asientos")); //nullable
            $bolsasAire = sanitiza($conexion, filter_input(INPUT_POST, "bolsasAire")); //nullable
            $kilometraje = sanitiza($conexion, filter_input(INPUT_POST, "kilometraje")); //nullable
            $tipoFactura = sanitiza($conexion, filter_input(INPUT_POST, "tipoFactura")); //nullable
            $numeroLlaves = sanitiza($conexion, filter_input(INPUT_POST, "numeroLlaves")); //nullable
            $vidaUtilLlantas = sanitiza($conexion, filter_input(INPUT_POST, "vidaUtilLlantas")); //nullable
            $facturaAgencia = sanitiza($conexion, filter_input(INPUT_POST, "facturaAgencia")) == "on" ? 1 : 0;
            $mantenimientosAgencia = sanitiza($conexion, filter_input(INPUT_POST, "mantenimientosAgencia")) == "on" ? 1 : 0;
            $impuestosCorriente = sanitiza($conexion, filter_input(INPUT_POST, "impuestosCorriente")) == "on" ? 1 : 0;
            $mantenimientosCorriente = sanitiza($conexion, filter_input(INPUT_POST, "mantenimientosCorriente")) == "on" ? 1 : 0;
            $manosLibres = sanitiza($conexion, filter_input(INPUT_POST, "manosLibres")) == "on" ? 1 : 0;
            $descripcion = sanitiza($conexion, filter_input(INPUT_POST, "descripcion"));
            $puntosDestacados = sanitiza($conexion, filter_input(INPUT_POST, "puntosDestacados"));
            $video_titulo = sanitiza($conexion, filter_input(INPUT_POST, "video_titulo"));
            $video_resumen = sanitiza($conexion, filter_input(INPUT_POST, "video_resumen"));
            $video_detalle = sanitiza($conexion, filter_input(INPUT_POST, "video_detalle"));
            $video_url = sanitiza($conexion, filter_input(INPUT_POST, "video_url"));
            $video_publicado = sanitiza($conexion, filter_input(INPUT_POST, "video_publicado"));
            $idSucursal = sanitiza($conexion, filter_input(INPUT_POST, "idSucursal"));
            $idConcesionario = sanitiza($conexion, filter_input(INPUT_POST, "idConcesionario"));

            $colorInterior = sanitiza($conexion, filter_input(INPUT_POST, "colorInterior"));
            $direccion = sanitiza($conexion, filter_input(INPUT_POST, "direccion"));
            $tieneAireAcondicionado = sanitiza($conexion, filter_input(INPUT_POST, "tieneAireAcondicionado")) == "on" ? 1 : 0;
            $tieneAlarma = sanitiza($conexion, filter_input(INPUT_POST, "tieneAlarma")) == "on" ? 1 : 0;
            $tieneAlfombrillaLlantaRefaccion = sanitiza($conexion, filter_input(INPUT_POST, "tieneAlfombrillaLlantaRefaccion")) == "on" ? 1 : 0;
            $tieneAperturaRemotaCajuela = sanitiza($conexion, filter_input(INPUT_POST, "tieneAperturaRemotaCajuela")) == "on" ? 1 : 0;
            $tieneAsientoConductorAjusteAltura = sanitiza($conexion, filter_input(INPUT_POST, "tieneAsientoConductorAjusteAltura")) == "on" ? 1 : 0;
            $tieneAsientosElectricos = sanitiza($conexion, filter_input(INPUT_POST, "tieneAsientosElectricos")) == "on" ? 1 : 0;
            $tieneAsientosTraserosAbatibles = sanitiza($conexion, filter_input(INPUT_POST, "tieneAsientosTraserosAbatibles")) == "on" ? 1 : 0;
            $tieneAsistenciaFrenado = sanitiza($conexion, filter_input(INPUT_POST, "tieneAsistenciaFrenado")) == "on" ? 1 : 0;
            $tieneBandejaLlantaRefaccion = sanitiza($conexion, filter_input(INPUT_POST, "tieneBandejaLlantaRefaccion")) == "on" ? 1 : 0;
            $tieneBarraAntivuelco = sanitiza($conexion, filter_input(INPUT_POST, "tieneBarraAntivuelco")) == "on" ? 1 : 0;
            $esBlindado = sanitiza($conexion, filter_input(INPUT_POST, "esBlindado")) == "on" ? 1 : 0;
            $tieneBluetooth = sanitiza($conexion, filter_input(INPUT_POST, "tieneBluetooth")) == "on" ? 1 : 0;
            $tieneBolsaAireConductor = sanitiza($conexion, filter_input(INPUT_POST, "tieneBolsaAireConductor")) == "on" ? 1 : 0;
            $tieneBolsaAirePasajero = sanitiza($conexion, filter_input(INPUT_POST, "tieneBolsaAirePasajero")) == "on" ? 1 : 0;
            $tieneBolsasAireLaterales = sanitiza($conexion, filter_input(INPUT_POST, "tieneBolsasAireLaterales")) == "on" ? 1 : 0;
            $tieneBolsasAireCortina = sanitiza($conexion, filter_input(INPUT_POST, "tieneBolsasAireCortina")) == "on" ? 1 : 0;
            $tieneCabecerasAsientosTraseros = sanitiza($conexion, filter_input(INPUT_POST, "tieneCabecerasAsientosTraseros")) == "on" ? 1 : 0;
            $tieneComputadoraAbordo = sanitiza($conexion, filter_input(INPUT_POST, "tieneComputadoraAbordo")) == "on" ? 1 : 0;
            $tieneControlTemperatura = sanitiza($conexion, filter_input(INPUT_POST, "tieneControlTemperatura")) == "on" ? 1 : 0;
            $tieneControlEstabilidad = sanitiza($conexion, filter_input(INPUT_POST, "tieneControlEstabilidad")) == "on" ? 1 : 0;
            $tieneControlLucesDelanteras = sanitiza($conexion, filter_input(INPUT_POST, "tieneControlLucesDelanteras")) == "on" ? 1 : 0;
            $tieneControlVolante = sanitiza($conexion, filter_input(INPUT_POST, "tieneControlVolante")) == "on" ? 1 : 0;
            $tieneDefensasColorCarroceria = sanitiza($conexion, filter_input(INPUT_POST, "tieneDefensasColorCarroceria")) == "on" ? 1 : 0;
            $tieneDesempanadorTrasero = sanitiza($conexion, filter_input(INPUT_POST, "tieneDesempanadorTrasero")) == "on" ? 1 : 0;
            $tieneEspejosElectricos = sanitiza($conexion, filter_input(INPUT_POST, "tieneEspejosElectricos")) == "on" ? 1 : 0;
            $tieneFarosNiebla = sanitiza($conexion, filter_input(INPUT_POST, "tieneFarosNiebla")) == "on" ? 1 : 0;
            $tieneFrenosABS = sanitiza($conexion, filter_input(INPUT_POST, "tieneFrenosABS")) == "on" ? 1 : 0;
            $tieneGps = sanitiza($conexion, filter_input(INPUT_POST, "tieneGps")) == "on" ? 1 : 0;
            $esImportado = sanitiza($conexion, filter_input(INPUT_POST, "esImportado")) == "on" ? 1 : 0;
            $tieneInmovilizador = sanitiza($conexion, filter_input(INPUT_POST, "tieneInmovilizador")) == "on" ? 1 : 0;
            $tieneLimpiaparabrisas = sanitiza($conexion, filter_input(INPUT_POST, "tieneLimpiaparabrisas")) == "on" ? 1 : 0;
            $tieneLlantaRefaccion = sanitiza($conexion, filter_input(INPUT_POST, "tieneLlantaRefaccion")) == "on" ? 1 : 0;
            $tieneLucesNieblaDelanteras = sanitiza($conexion, filter_input(INPUT_POST, "tieneLucesNieblaDelanteras")) == "on" ? 1 : 0;
            $tieneLucesNieblaTraseras = sanitiza($conexion, filter_input(INPUT_POST, "tieneLucesNieblaTraseras")) == "on" ? 1 : 0;
            $tieneLucesXenon = sanitiza($conexion, filter_input(INPUT_POST, "tieneLucesXenon")) == "on" ? 1 : 0;
            $tieneParachoques = sanitiza($conexion, filter_input(INPUT_POST, "tieneParachoques")) == "on" ? 1 : 0;
            $tienePilotoAutomatico = sanitiza($conexion, filter_input(INPUT_POST, "tienePilotoAutomatico")) == "on" ? 1 : 0;
            $tienePortavasos = sanitiza($conexion, filter_input(INPUT_POST, "tienePortavasos")) == "on" ? 1 : 0;
            $tieneQuemacocos = sanitiza($conexion, filter_input(INPUT_POST, "tieneQuemacocos")) == "on" ? 1 : 0;
            $tieneRadioAMFM = sanitiza($conexion, filter_input(INPUT_POST, "tieneRadioAMFM")) == "on" ? 1 : 0;
            $tieneRecordatorioEncendidoLuces = sanitiza($conexion, filter_input(INPUT_POST, "tieneRecordatorioEncendidoLuces")) == "on" ? 1 : 0;
            $tieneReproductorCD = sanitiza($conexion, filter_input(INPUT_POST, "tieneReproductorCD")) == "on" ? 1 : 0;
            $tieneReproductorDVD = sanitiza($conexion, filter_input(INPUT_POST, "tieneReproductorDVD")) == "on" ? 1 : 0;
            $tieneReproductorMP3 = sanitiza($conexion, filter_input(INPUT_POST, "tieneReproductorMP3")) == "on" ? 1 : 0;
            $tieneRespadosTraseros = sanitiza($conexion, filter_input(INPUT_POST, "tieneRespadosTraseros")) == "on" ? 1 : 0;
            $tieneRinesAleacion = sanitiza($conexion, filter_input(INPUT_POST, "tieneRinesAleacion")) == "on" ? 1 : 0;
            $tieneSegurosElectricosCentralizados = sanitiza($conexion, filter_input(INPUT_POST, "tieneSegurosElectricosCentralizados")) == "on" ? 1 : 0;
            $tieneSensorLluvia = sanitiza($conexion, filter_input(INPUT_POST, "tieneSensorLluvia")) == "on" ? 1 : 0;
            $tieneSensoresLuz = sanitiza($conexion, filter_input(INPUT_POST, "tieneSensoresLuz")) == "on" ? 1 : 0;
            $tieneSensoresReversa = sanitiza($conexion, filter_input(INPUT_POST, "tieneSensoresReversa")) == "on" ? 1 : 0;
            $tieneTapiceriaPiel = sanitiza($conexion, filter_input(INPUT_POST, "tieneTapiceriaPiel")) == "on" ? 1 : 0;
            $tieneTarjetaSD = sanitiza($conexion, filter_input(INPUT_POST, "tieneTarjetaSD")) == "on" ? 1 : 0;
            $tieneTerceraLuzFrenado = sanitiza($conexion, filter_input(INPUT_POST, "tieneTerceraLuzFrenado")) == "on" ? 1 : 0;
            $unicoDueno = sanitiza($conexion, filter_input(INPUT_POST, "unicoDueno")) == "on" ? 1 : 0;
            $tieneUsb = sanitiza($conexion, filter_input(INPUT_POST, "tieneUsb")) == "on" ? 1 : 0;
            $tieneVidriosElectricos = sanitiza($conexion, filter_input(INPUT_POST, "tieneVidriosElectricos")) == "on" ? 1 : 0;

            // Parametros enviados por origen

            $origen = sanitiza($conexion, filter_input(INPUT_POST, "origen"));
            $origen_marca = sanitiza($conexion, filter_input(INPUT_POST, "origen_marca"));
            $origen_ano = sanitiza($conexion, filter_input(INPUT_POST, "origen_ano"));
            $origen_tipo = sanitiza($conexion, filter_input(INPUT_POST, "origen_tipo"));
            $origen_transmision = sanitiza($conexion, filter_input(INPUT_POST, "origen_transmision"));
            $origen_publicado = sanitiza($conexion, filter_input(INPUT_POST, "origen_publicado"));
            $origen_concesionario = sanitiza($conexion, filter_input(INPUT_POST, "origen_concesionario"));

            // Inicializa variables

            $fechaActual = date("Y-m-d H:i:s");
            $publicado = estaVacio($publicado) ? 0 : 1;
            $destacado = estaVacio($destacado) ? 0 : 1;
            $certificado = estaVacio($certificado) ? 0 : 1;
            $descuentoEspecial = estaVacio($descuentoEspecial) ? 0 : 1;
            $mensaje = "";
            $imagenPrincipal = "";
            $numeroLlaves = (estaVacio($numeroLlaves) ? 1 : $numeroLlaves);
            $video_vistaPrevia = "";
            $video_publicado = estaVacio($video_publicado) ? 0 : 1;

            // Procesa el request

            if (!estaVacio($esSubmit) && $esSubmit === "1") {

                // Valida los campos obligatorios

                if (estaVacio($tipo)) {
                    $mensaje .= "* Tipo<br />";
                }

                if (estaVacio($marca)) {
                    $mensaje .= "* Marca<br />";
                }

                if (estaVacio($modelo)) {
                    $mensaje .= "* Modelo<br />";
                }

                if (estaVacio($version)) {
                    $mensaje .= "* Versión<br />";
                }

                if (estaVacio($ano)) {
                    $mensaje .= "* Año<br />";
                }

                if (estaVacio($color)) {
                    $mensaje .= "* Color<br />";
                }
                
                if (estaVacio($precio)) {
                    $mensaje .= "* Precio<br />";
                }
/*
                if (estaVacio($enganche)) {
                    $mensaje .= "* Enganche<br />";
                }
                
                if (estaVacio($descripcion)) {
                    $mensaje .= "* Descripción<br />";
                }
*/
                if (!estaVacio($mensaje)) {
                    $mensaje = "Proporcione los siguientes datos:<br /><br />" . $mensaje;
                } else {
                    if (estaVacio($id)) {

                        // Es insercion

                        consulta($conexion, "INSERT INTO vehiculo ("
                                . "fechaRegistro"
                                . ", publicado"
                                . ", destacado"
                                . ", certificado"
                                . ", descuentoEspecial"
                                . ", tipo"
                                . ", marca"
                                . ", modelo"
                                . ", version"
                                . ", ano"
                                . ", color"
                                . ", precio"
                                . ", enganche"
                                . ", litros"
                                . ", combustible"
                                . ", transmision"
                                . ", puertas"
                                . ", asientos"
                                . ", bolsasAire"
                                . ", kilometraje"
                                . ", tipoFactura"
                                . ", numeroLlaves"
                                . ", vidaUtilLlantas"
                                . ", facturaAgencia"
                                . ", mantenimientosAgencia"
                                . ", impuestosCorriente"
                                . ", mantenimientosCorriente"
                                . ", manosLibres"
                                . ", descripcion"
                                . ", puntosDestacados"
                                . ", video_titulo"
                                . ", video_resumen"
                                . ", video_detalle"
                                . ", video_url"
                                . ", video_fechaPublicacion"
                                . ", video_publicado"
                                . ", idSucursal"
                                . ", idConcesionario"

                                . ", colorInterior"
                                . ", direccion"
                                . ", tieneAireAcondicionado"
                                . ", tieneAlarma"
                                . ", tieneAlfombrillaLlantaRefaccion"
                                . ", tieneAperturaRemotaCajuela"
                                . ", tieneAsientoConductorAjusteAltura"
                                . ", tieneAsientosElectricos"
                                . ", tieneAsientosTraserosAbatibles"
                                . ", tieneAsistenciaFrenado"
                                . ", tieneBandejaLlantaRefaccion"
                                . ", tieneBarraAntivuelco"
                                . ", esBlindado"
                                . ", tieneBluetooth"
                                . ", tieneBolsaAireConductor"
                                . ", tieneBolsaAirePasajero"
                                . ", tieneBolsasAireLaterales"
                                . ", tieneBolsasAireCortina"
                                . ", tieneCabecerasAsientosTraseros"
                                . ", tieneComputadoraAbordo"
                                . ", tieneControlTemperatura"
                                . ", tieneControlEstabilidad"
                                . ", tieneControlLucesDelanteras"
                                . ", tieneControlVolante"
                                . ", tieneDefensasColorCarroceria"
                                . ", tieneDesempanadorTrasero"
                                . ", tieneEspejosElectricos"
                                . ", tieneFarosNiebla"
                                . ", tieneFrenosABS"
                                . ", tieneGps"
                                . ", esImportado"
                                . ", tieneInmovilizador"
                                . ", tieneLimpiaparabrisas"
                                . ", tieneLlantaRefaccion"
                                . ", tieneLucesNieblaDelanteras"
                                . ", tieneLucesNieblaTraseras"
                                . ", tieneLucesXenon"
                                . ", tieneParachoques"
                                . ", tienePilotoAutomatico"
                                . ", tienePortavasos"
                                . ", tieneQuemacocos"
                                . ", tieneRadioAMFM"
                                . ", tieneRecordatorioEncendidoLuces"
                                . ", tieneReproductorCD"
                                . ", tieneReproductorDVD"
                                . ", tieneReproductorMP3"
                                . ", tieneRespadosTraseros"
                                . ", tieneRinesAleacion"
                                . ", tieneSegurosElectricosCentralizados"
                                . ", tieneSensorLluvia"
                                . ", tieneSensoresLuz"
                                . ", tieneSensoresReversa"
                                . ", tieneTapiceriaPiel"
                                . ", tieneTarjetaSD"
                                . ", tieneTerceraLuzFrenado"
                                . ", unicoDueno"
                                . ", tieneUsb"
                                . ", tieneVidriosElectricos"
                            . ") VALUES ("
                                . "'" . $fechaActual . "'"
                                . ", " . $publicado
                                . ", " . $destacado
                                . ", " . $certificado
                                . ", " . $descuentoEspecial
                                . ", '" . $tipo ."'"
                                . ", '" . $marca ."'"
                                . ", '" . $modelo ."'"
                                . ", '" . $version ."'"
                                . ", " . $ano
                                . ", '" . $color ."'"
                                . ", " . $precio
                                . ", " . (estaVacio($enganche) ? "NULL" : $enganche)
                                . ", " . (estaVacio($litros) ? "NULL" : $litros)
                                . ", " . (estaVacio($combustible) ? "NULL" : "'" . $combustible . "'")
                                . ", " . (estaVacio($transmision) ? "NULL" : "'" . $transmision . "'")
                                . ", " . (estaVacio($puertas) ? "NULL" : $puertas)
                                . ", " . (estaVacio($asientos) ? "NULL" : $asientos)
                                . ", " . (estaVacio($bolsasAire) ? "NULL" : $bolsasAire)
                                . ", " . (estaVacio($kilometraje) ? "NULL" : $kilometraje)
                                . ", " . (estaVacio($tipoFactura) ? "NULL" : "'" . $tipoFactura . "'")
                                . ", " . $numeroLlaves
                                . ", " . (estaVacio($vidaUtilLlantas) ? "NULL" : $vidaUtilLlantas)
                                . ", " . (estaVacio($facturaAgencia) ? "NULL" : $facturaAgencia)
                                . ", " . (estaVacio($mantenimientosAgencia) ? "NULL" : $mantenimientosAgencia)
                                . ", " . (estaVacio($impuestosCorriente) ? "NULL" : $impuestosCorriente)
                                . ", " . (estaVacio($mantenimientosCorriente) ? "NULL" : $mantenimientosCorriente)
                                . ", " . (estaVacio($manosLibres) ? "NULL" : $manosLibres)
                                . ", " . (estaVacio($descripcion) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $descripcion) . "'")
                                . ", " . (estaVacio($puntosDestacados) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $puntosDestacados) . "'")
                                . ", " . (estaVacio($video_titulo) ? "NULL" : "'" . $video_titulo . "'")
                                . ", " . (estaVacio($video_resumen) ? "NULL" : "'" . $video_resumen . "'")
                                . ", " . (estaVacio($video_detalle) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $video_detalle) . "'")
                                . ", " . (estaVacio($video_url) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $video_url) . "'")
                                . ", " . (!estaVacio($video_titulo) && !estaVacio($video_resumen) ? "'" . $fechaActual . "'" : "NULL")
                                . ", " . $video_publicado
                                . ", " . (estaVacio($idSucursal) ? "NULL" : $idSucursal)
                                . ", " . $idConcesionario

                                . ", " . (estaVacio($colorInterior) ? "NULL" : "'" . $colorInterior . "'")
                                . ", " . (estaVacio($direccion) ? "NULL" : "'" . $direccion . "'")
                                . ", " . $tieneAireAcondicionado
                                . ", " . $tieneAlarma
                                . ", " . $tieneAlfombrillaLlantaRefaccion
                                . ", " . $tieneAperturaRemotaCajuela
                                . ", " . $tieneAsientoConductorAjusteAltura
                                . ", " . $tieneAsientosElectricos
                                . ", " . $tieneAsientosTraserosAbatibles
                                . ", " . $tieneAsistenciaFrenado
                                . ", " . $tieneBandejaLlantaRefaccion
                                . ", " . $tieneBarraAntivuelco
                                . ", " . $esBlindado
                                . ", " . $tieneBluetooth
                                . ", " . $tieneBolsaAireConductor
                                . ", " . $tieneBolsaAirePasajero
                                . ", " . $tieneBolsasAireLaterales
                                . ", " . $tieneBolsasAireCortina
                                . ", " . $tieneCabecerasAsientosTraseros
                                . ", " . $tieneComputadoraAbordo
                                . ", " . $tieneControlTemperatura
                                . ", " . $tieneControlEstabilidad
                                . ", " . $tieneControlLucesDelanteras
                                . ", " . $tieneControlVolante
                                . ", " . $tieneDefensasColorCarroceria
                                . ", " . $tieneDesempanadorTrasero
                                . ", " . $tieneEspejosElectricos
                                . ", " . $tieneFarosNiebla
                                . ", " . $tieneFrenosABS
                                . ", " . $tieneGps
                                . ", " . $esImportado
                                . ", " . $tieneInmovilizador
                                . ", " . $tieneLimpiaparabrisas
                                . ", " . $tieneLlantaRefaccion
                                . ", " . $tieneLucesNieblaDelanteras
                                . ", " . $tieneLucesNieblaTraseras
                                . ", " . $tieneLucesXenon
                                . ", " . $tieneParachoques
                                . ", " . $tienePilotoAutomatico
                                . ", " . $tienePortavasos
                                . ", " . $tieneQuemacocos
                                . ", " . $tieneRadioAMFM
                                . ", " . $tieneRecordatorioEncendidoLuces
                                . ", " . $tieneReproductorCD
                                . ", " . $tieneReproductorDVD
                                . ", " . $tieneReproductorMP3
                                . ", " . $tieneRespadosTraseros
                                . ", " . $tieneRinesAleacion
                                . ", " . $tieneSegurosElectricosCentralizados
                                . ", " . $tieneSensorLluvia
                                . ", " . $tieneSensoresLuz
                                . ", " . $tieneSensoresReversa
                                . ", " . $tieneTapiceriaPiel
                                . ", " . $tieneTarjetaSD
                                . ", " . $tieneTerceraLuzFrenado
                                . ", " . $unicoDueno
                                . ", " . $tieneUsb
                                . ", " . $tieneVidriosElectricos
                            . ")");

                        $vehiculo_BD = consulta($conexion, "SELECT"
                                . " *"
                            . " FROM"
                                . " vehiculo"
                            . " WHERE"
                                . " fechaRegistro = '" . $fechaActual . "'"
                                . " AND idConcesionario = " . $idConcesionario
                                . " AND tipo = '" . $tipo ."'"
                                . " AND marca = '" . $marca ."'"
                                . " AND modelo = '" . $modelo ."'"
                                . " AND version = '" . $version ."'"
                                . " AND ano = " . $ano
                                . " AND color = '" . $color ."'"
                                . " AND precio = " . $precio);

                        if (cuentaResultados($vehiculo_BD) == 0) {
                            $mensaje = "Ha ocurrido un error al guardar la información de su vehículo. Por favor revise los datos proporcionados y si el problema persiste envíenos un correo a <strong>soporte@socialware.mx</strong>";
                        } else {
                            $vehiculo = obtenResultado($vehiculo_BD);

                            $id = $vehiculo["id"];
                            $publicado = $vehiculo["publicado"];
                            $destacado = $vehiculo["destacado"];
                            $certificado = $vehiculo["certificado"];
                            $descuentoEspecial = $vehiculo["descuentoEspecial"];
                            $tipo = $vehiculo["tipo"];
                            $marca = $vehiculo["marca"];
                            $modelo = $vehiculo["modelo"];
                            $version = $vehiculo["version"];
                            $ano = $vehiculo["ano"];
                            $color = $vehiculo["color"];
                            $precio = $vehiculo["precio"];
                            $enganche = $vehiculo["enganche"];
                            $litros = $vehiculo["litros"];
                            $combustible = $vehiculo["combustible"];
                            $transmision = $vehiculo["transmision"];
                            $puertas = $vehiculo["puertas"];
                            $asientos = $vehiculo["asientos"];
                            $bolsasAire = $vehiculo["bolsasAire"];
                            $kilometraje = $vehiculo["kilometraje"];
                            $tipoFactura = $vehiculo["tipoFactura"];
                            $numeroLlaves = $vehiculo["numeroLlaves"];
                            $vidaUtilLlantas = $vehiculo["vidaUtilLlantas"];
                            $facturaAgencia = $vehiculo["facturaAgencia"];
                            $mantenimientosAgencia = $vehiculo["mantenimientosAgencia"];
                            $impuestosCorriente = $vehiculo["impuestosCorriente"];
                            $mantenimientosCorriente = $vehiculo["mantenimientosCorriente"];
                            $manosLibres = $vehiculo["manosLibres"];
                            $descripcion = $vehiculo["descripcion"];
                            $puntosDestacados = $vehiculo["puntosDestacados"];
                            $imagenPrincipal = $vehiculo["imagenPrincipal"];
                            $video_titulo = $vehiculo["video_titulo"];
                            $video_resumen = $vehiculo["video_resumen"];
                            $video_detalle = $vehiculo["video_detalle"];
                            $video_url = $vehiculo["video_url"];
                            $video_vistaPrevia = $vehiculo["video_vistaPrevia"];
                            $video_publicado = $vehiculo["video_publicado"];
                            $idSucursal = $vehiculo["idSucursal"];
                            $idConcesionario = $vehiculo["idConcesionario"];

                            $colorInterior = $vehiculo["colorInterior"];
                            $direccion = $vehiculo["direccion"];
                            $tieneAireAcondicionado = $vehiculo["tieneAireAcondicionado"];
                            $tieneAlarma = $vehiculo["tieneAlarma"];
                            $tieneAlfombrillaLlantaRefaccion = $vehiculo["tieneAlfombrillaLlantaRefaccion"];
                            $tieneAperturaRemotaCajuela = $vehiculo["tieneAperturaRemotaCajuela"];
                            $tieneAsientoConductorAjusteAltura = $vehiculo["tieneAsientoConductorAjusteAltura"];
                            $tieneAsientosElectricos = $vehiculo["tieneAsientosElectricos"];
                            $tieneAsientosTraserosAbatibles = $vehiculo["tieneAsientosTraserosAbatibles"];
                            $tieneAsistenciaFrenado = $vehiculo["tieneAsistenciaFrenado"];
                            $tieneBandejaLlantaRefaccion = $vehiculo["tieneBandejaLlantaRefaccion"];
                            $tieneBarraAntivuelco = $vehiculo["tieneBarraAntivuelco"];
                            $esBlindado = $vehiculo["esBlindado"];
                            $tieneBluetooth = $vehiculo["tieneBluetooth"];
                            $tieneBolsaAireConductor = $vehiculo["tieneBolsaAireConductor"];
                            $tieneBolsaAirePasajero = $vehiculo["tieneBolsaAirePasajero"];
                            $tieneBolsasAireLaterales = $vehiculo["tieneBolsasAireLaterales"];
                            $tieneBolsasAireCortina = $vehiculo["tieneBolsasAireCortina"];
                            $tieneCabecerasAsientosTraseros = $vehiculo["tieneCabecerasAsientosTraseros"];
                            $tieneComputadoraAbordo = $vehiculo["tieneComputadoraAbordo"];
                            $tieneControlTemperatura = $vehiculo["tieneControlTemperatura"];
                            $tieneControlEstabilidad = $vehiculo["tieneControlEstabilidad"];
                            $tieneControlLucesDelanteras = $vehiculo["tieneControlLucesDelanteras"];
                            $tieneControlVolante = $vehiculo["tieneControlVolante"];
                            $tieneDefensasColorCarroceria = $vehiculo["tieneDefensasColorCarroceria"];
                            $tieneDesempanadorTrasero = $vehiculo["tieneDesempanadorTrasero"];
                            $tieneEspejosElectricos = $vehiculo["tieneEspejosElectricos"];
                            $tieneFarosNiebla = $vehiculo["tieneFarosNiebla"];
                            $tieneFrenosABS = $vehiculo["tieneFrenosABS"];
                            $tieneGps = $vehiculo["tieneGps"];
                            $esImportado = $vehiculo["esImportado"];
                            $tieneInmovilizador = $vehiculo["tieneInmovilizador"];
                            $tieneLimpiaparabrisas = $vehiculo["tieneLimpiaparabrisas"];
                            $tieneLlantaRefaccion = $vehiculo["tieneLlantaRefaccion"];
                            $tieneLucesNieblaDelanteras = $vehiculo["tieneLucesNieblaDelanteras"];
                            $tieneLucesNieblaTraseras = $vehiculo["tieneLucesNieblaTraseras"];
                            $tieneLucesXenon = $vehiculo["tieneLucesXenon"];
                            $tieneParachoques = $vehiculo["tieneParachoques"];
                            $tienePilotoAutomatico = $vehiculo["tienePilotoAutomatico"];
                            $tienePortavasos = $vehiculo["tienePortavasos"];
                            $tieneQuemacocos = $vehiculo["tieneQuemacocos"];
                            $tieneRadioAMFM = $vehiculo["tieneRadioAMFM"];
                            $tieneRecordatorioEncendidoLuces = $vehiculo["tieneRecordatorioEncendidoLuces"];
                            $tieneReproductorCD = $vehiculo["tieneReproductorCD"];
                            $tieneReproductorDVD = $vehiculo["tieneReproductorDVD"];
                            $tieneReproductorMP3 = $vehiculo["tieneReproductorMP3"];
                            $tieneRespadosTraseros = $vehiculo["tieneRespadosTraseros"];
                            $tieneRinesAleacion = $vehiculo["tieneRinesAleacion"];
                            $tieneSegurosElectricosCentralizados = $vehiculo["tieneSegurosElectricosCentralizados"];
                            $tieneSensorLluvia = $vehiculo["tieneSensorLluvia"];
                            $tieneSensoresLuz = $vehiculo["tieneSensoresLuz"];
                            $tieneSensoresReversa = $vehiculo["tieneSensoresReversa"];
                            $tieneTapiceriaPiel = $vehiculo["tieneTapiceriaPiel"];
                            $tieneTarjetaSD = $vehiculo["tieneTarjetaSD"];
                            $tieneTerceraLuzFrenado = $vehiculo["tieneTerceraLuzFrenado"];
                            $unicoDueno = $vehiculo["unicoDueno"];
                            $tieneUsb = $vehiculo["tieneUsb"];
                            $tieneVidriosElectricos = $vehiculo["tieneVidriosElectricos"];

                            $intelimotor_id = $vehiculo["intelimotor_id"];
                            $intelimotor_imported = $vehiculo["intelimotor_imported"];
                            $intelimotor_kms = $vehiculo["intelimotor_kms"];
                            $intelimotor_listPrice = $vehiculo["intelimotor_listPrice"];
                            $intelimotor_title = $vehiculo["intelimotor_title"];
                            $intelimotor_brand = $vehiculo["intelimotor_brand"];
                            $intelimotor_model = $vehiculo["intelimotor_model"];
                            $intelimotor_year = $vehiculo["intelimotor_year"];
                            $intelimotor_trim = $vehiculo["intelimotor_trim"];
                            $intelimotor_transmission = $vehiculo["intelimotor_transmission"];
                            $intelimotor_doors = $vehiculo["intelimotor_doors"];
                            $intelimotor_fuelType = $vehiculo["intelimotor_fuelType"];
                            $intelimotor_steering = $vehiculo["intelimotor_steering"];
                            $intelimotor_tractionControl = $vehiculo["intelimotor_tractionControl"];
                            $intelimotor_vehicleBodyType = $vehiculo["intelimotor_vehicleBodyType"];
                            $intelimotor_engine = $vehiculo["intelimotor_engine"];
                            $intelimotor_exteriorColor = $vehiculo["intelimotor_exteriorColor"];
                            $intelimotor_interiorColor = $vehiculo["intelimotor_interiorColor"];
                            $intelimotor_hasAutopilot = $vehiculo["intelimotor_hasAutopilot"];
                            $intelimotor_hasLightOnReminder = $vehiculo["intelimotor_hasLightOnReminder"];
                            $intelimotor_hasOnboardComputer = $vehiculo["intelimotor_hasOnboardComputer"];
                            $intelimotor_hasRearFoldingSeat = $vehiculo["intelimotor_hasRearFoldingSeat"];
                            $intelimotor_hasSlidingRoof = $vehiculo["intelimotor_hasSlidingRoof"];
                            $intelimotor_hasXenonHeadlights = $vehiculo["intelimotor_hasXenonHeadlights"];
                            $intelimotor_hasCoasters = $vehiculo["intelimotor_hasCoasters"];
                            $intelimotor_hasClimateControl = $vehiculo["intelimotor_hasClimateControl"];
                            $intelimotor_hasAbsBrakes = $vehiculo["intelimotor_hasAbsBrakes"];
                            $intelimotor_hasAlarm = $vehiculo["intelimotor_hasAlarm"];
                            $intelimotor_hasAlloyWheels = $vehiculo["intelimotor_hasAlloyWheels"];
                            $intelimotor_hasDriverAirbag = $vehiculo["intelimotor_hasDriverAirbag"];
                            $intelimotor_hasElectronicBrakeAssist = $vehiculo["intelimotor_hasElectronicBrakeAssist"];
                            $intelimotor_hasEngineInmovilizer = $vehiculo["intelimotor_hasEngineInmovilizer"];
                            $intelimotor_hasFogLight = $vehiculo["intelimotor_hasFogLight"];
                            $intelimotor_hasFrontFoglights = $vehiculo["intelimotor_hasFrontFoglights"];
                            $intelimotor_hasPassengerAirbag = $vehiculo["intelimotor_hasPassengerAirbag"];
                            $intelimotor_hasRainSensor = $vehiculo["intelimotor_hasRainSensor"];
                            $intelimotor_hasRearFoglights = $vehiculo["intelimotor_hasRearFoglights"];
                            $intelimotor_hasRearWindowDefogger = $vehiculo["intelimotor_hasRearWindowDefogger"];
                            $intelimotor_hasRollBar = $vehiculo["intelimotor_hasRollBar"];
                            $intelimotor_hasSideImpactAirbag = $vehiculo["intelimotor_hasSideImpactAirbag"];
                            $intelimotor_hasStabilityControl = $vehiculo["intelimotor_hasStabilityControl"];
                            $intelimotor_hasSteeringWheelControl = $vehiculo["intelimotor_hasSteeringWheelControl"];
                            $intelimotor_hasThirdStop = $vehiculo["intelimotor_hasThirdStop"];
                            $intelimotor_hasCurtainAirbag = $vehiculo["intelimotor_hasCurtainAirbag"];
                            $intelimotor_armored = $vehiculo["intelimotor_armored"];
                            $intelimotor_hasAirConditioning = $vehiculo["intelimotor_hasAirConditioning"];
                            $intelimotor_hasElectricMirrors = $vehiculo["intelimotor_hasElectricMirrors"];
                            $intelimotor_hasGps = $vehiculo["intelimotor_hasGps"];
                            $intelimotor_hasHeadlightControl = $vehiculo["intelimotor_hasHeadlightControl"];
                            $intelimotor_hasHeadrestRearSeat = $vehiculo["intelimotor_hasHeadrestRearSeat"];
                            $intelimotor_hasHeightAdjustableDriverSeat = $vehiculo["intelimotor_hasHeightAdjustableDriverSeat"];
                            $intelimotor_hasLeatherUpholstery = $vehiculo["intelimotor_hasLeatherUpholstery"];
                            $intelimotor_hasLightSensor = $vehiculo["intelimotor_hasLightSensor"];
                            $intelimotor_hasPaintedBumper = $vehiculo["intelimotor_hasPaintedBumper"];
                            $intelimotor_hasParkingSensor = $vehiculo["intelimotor_hasParkingSensor"];
                            $intelimotor_hasPowerWindows = $vehiculo["intelimotor_hasPowerWindows"];
                            $intelimotor_hasRemoteTrunkRelease = $vehiculo["intelimotor_hasRemoteTrunkRelease"];
                            $intelimotor_hasElectricSeats = $vehiculo["intelimotor_hasElectricSeats"];
                            $intelimotor_hasRearBackrest = $vehiculo["intelimotor_hasRearBackrest"];
                            $intelimotor_hasCentralPowerDoorLocks = $vehiculo["intelimotor_hasCentralPowerDoorLocks"];
                            $intelimotor_hasAmfmRadio = $vehiculo["intelimotor_hasAmfmRadio"];
                            $intelimotor_hasBluetooth = $vehiculo["intelimotor_hasBluetooth"];
                            $intelimotor_hasCdPlayer = $vehiculo["intelimotor_hasCdPlayer"];
                            $intelimotor_hasDvd = $vehiculo["intelimotor_hasDvd"];
                            $intelimotor_hasMp3Player = $vehiculo["intelimotor_hasMp3Player"];
                            $intelimotor_hasSdCard = $vehiculo["intelimotor_hasSdCard"];
                            $intelimotor_hasUsb = $vehiculo["intelimotor_hasUsb"];
                            $intelimotor_hasBullBar = $vehiculo["intelimotor_hasBullBar"];
                            $intelimotor_hasSpareTyreSupport = $vehiculo["intelimotor_hasSpareTyreSupport"];
                            $intelimotor_hasTrayCover = $vehiculo["intelimotor_hasTrayCover"];
                            $intelimotor_hasTrayMat = $vehiculo["intelimotor_hasTrayMat"];
                            $intelimotor_hasWindscreenWiper = $vehiculo["intelimotor_hasWindscreenWiper"];
                            $intelimotor_singleOwner = $vehiculo["intelimotor_singleOwner"];
                            $intelimotor_youtubeVideoUrl = $vehiculo["intelimotor_youtubeVideoUrl"];
                            $intelimotor_picture = $vehiculo["intelimotor_picture"];

                            $mensaje = "ok - El vehiculo ha sido registrado.";

                            registraEvento("CMS : Alta de vehículo | id = " . $id);
                        }
                    } else {
                        
                        // Es actualizacion

                        consulta($conexion, "UPDATE vehiculo SET"
                                . " publicado = " . $publicado
                                . ", destacado = " . $destacado
                                . ", certificado = " . $certificado
                                . ", descuentoEspecial = " . $descuentoEspecial
                                . ", tipo = '" . $tipo ."'"
                                . ", marca = '" . $marca ."'"
                                . ", modelo = '" . $modelo ."'"
                                . ", version = '" . $version ."'"
                                . ", ano = " . $ano
                                . ", color = '" . $color ."'"
                                . ", precio = " . $precio
                                . ", enganche = " . (estaVacio($enganche) ? "NULL" : $enganche)
                                . ", litros = " . (estaVacio($litros) ? "NULL" : $litros)
                                . ", combustible = " . (estaVacio($combustible) ? "NULL" : "'" . $combustible . "'")
                                . ", transmision = " . (estaVacio($transmision) ? "NULL" : "'" . $transmision . "'")
                                . ", puertas = " . (estaVacio($puertas) ? "NULL" : $puertas)
                                . ", asientos = " . (estaVacio($asientos) ? "NULL" : $asientos)
                                . ", bolsasAire = " . (estaVacio($bolsasAire) ? "NULL" : $bolsasAire)
                                . ", kilometraje = " . (estaVacio($kilometraje) ? "NULL" : $kilometraje)
                                . ", tipoFactura = " . (estaVacio($tipoFactura) ? "NULL" : "'" . $tipoFactura . "'")
                                . ", numeroLlaves = " . $numeroLlaves
                                . ", vidaUtilLlantas = " . (estaVacio($vidaUtilLlantas) ? "NULL" : $vidaUtilLlantas)
                                . ", facturaAgencia = " . (estaVacio($facturaAgencia) ? "NULL" : $facturaAgencia)
                                . ", mantenimientosAgencia = " . (estaVacio($mantenimientosAgencia) ? "NULL" : $mantenimientosAgencia)
                                . ", impuestosCorriente = " . (estaVacio($impuestosCorriente) ? "NULL" : $impuestosCorriente)
                                . ", mantenimientosCorriente = " . (estaVacio($mantenimientosCorriente) ? "NULL" : $mantenimientosCorriente)
                                . ", manosLibres = " . (estaVacio($manosLibres) ? "NULL" : $manosLibres)
                                . ", descripcion = " . (estaVacio($descripcion) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $descripcion) . "'")
                                . ", puntosDestacados = " . (estaVacio($puntosDestacados) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $puntosDestacados) . "'")
                                . ", video_titulo = " . (estaVacio($video_titulo) ? "NULL" : "'" . $video_titulo . "'")
                                . ", video_resumen = " . (estaVacio($video_resumen) ? "NULL" : "'" . $video_resumen . "'")
                                . ", video_detalle = " . (estaVacio($video_detalle) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $video_detalle) . "'")
                                . ", video_url = " . (estaVacio($video_url) ? "NULL" : "'" . mysqli_real_escape_string($conexion, $video_url) . "'")
                                . ", video_fechaPublicacion = " . (!estaVacio($video_titulo) && !estaVacio($video_resumen) ? "'" . $fechaActual . "'" : "NULL")
                                . ", video_publicado = " . $video_publicado
                                . ", idSucursal = " . (estaVacio($idSucursal) ? "NULL" : $idSucursal)
                                . ", idConcesionario = " . $idConcesionario

                                . ", colorInterior = " . (estaVacio($colorInterior) ? "NULL" : "'" . $colorInterior . "'")
                                . ", direccion = " . (estaVacio($direccion) ? "NULL" : "'" . $direccion . "'")
                                . ", tieneAireAcondicionado = " . $tieneAireAcondicionado
                                . ", tieneAlarma = " . $tieneAlarma
                                . ", tieneAlfombrillaLlantaRefaccion = " . $tieneAlfombrillaLlantaRefaccion
                                . ", tieneAperturaRemotaCajuela = " . $tieneAperturaRemotaCajuela
                                . ", tieneAsientoConductorAjusteAltura = " . $tieneAsientoConductorAjusteAltura
                                . ", tieneAsientosElectricos = " . $tieneAsientosElectricos
                                . ", tieneAsientosTraserosAbatibles = " . $tieneAsientosTraserosAbatibles
                                . ", tieneAsistenciaFrenado = " . $tieneAsistenciaFrenado
                                . ", tieneBandejaLlantaRefaccion = " . $tieneBandejaLlantaRefaccion
                                . ", tieneBarraAntivuelco = " . $tieneBarraAntivuelco
                                . ", esBlindado = " . $esBlindado
                                . ", tieneBluetooth = " . $tieneBluetooth
                                . ", tieneBolsaAireConductor = " . $tieneBolsaAireConductor
                                . ", tieneBolsaAirePasajero = " . $tieneBolsaAirePasajero
                                . ", tieneBolsasAireLaterales = " . $tieneBolsasAireLaterales
                                . ", tieneBolsasAireCortina = " . $tieneBolsasAireCortina
                                . ", tieneCabecerasAsientosTraseros = " . $tieneCabecerasAsientosTraseros
                                . ", tieneComputadoraAbordo = " . $tieneComputadoraAbordo
                                . ", tieneControlTemperatura = " . $tieneControlTemperatura
                                . ", tieneControlEstabilidad = " . $tieneControlEstabilidad
                                . ", tieneControlLucesDelanteras = " . $tieneControlLucesDelanteras
                                . ", tieneControlVolante = " . $tieneControlVolante
                                . ", tieneDefensasColorCarroceria = " . $tieneDefensasColorCarroceria
                                . ", tieneDesempanadorTrasero = " . $tieneDesempanadorTrasero
                                . ", tieneEspejosElectricos = " . $tieneEspejosElectricos
                                . ", tieneFarosNiebla = " . $tieneFarosNiebla
                                . ", tieneFrenosABS = " . $tieneFrenosABS
                                . ", tieneGps = " . $tieneGps
                                . ", esImportado = " . $esImportado
                                . ", tieneInmovilizador = " . $tieneInmovilizador
                                . ", tieneLimpiaparabrisas = " . $tieneLimpiaparabrisas
                                . ", tieneLlantaRefaccion = " . $tieneLlantaRefaccion
                                . ", tieneLucesNieblaDelanteras = " . $tieneLucesNieblaDelanteras
                                . ", tieneLucesNieblaTraseras = " . $tieneLucesNieblaTraseras
                                . ", tieneLucesXenon = " . $tieneLucesXenon
                                . ", tieneParachoques = " . $tieneParachoques
                                . ", tienePilotoAutomatico = " . $tienePilotoAutomatico
                                . ", tienePortavasos = " . $tienePortavasos
                                . ", tieneQuemacocos = " . $tieneQuemacocos
                                . ", tieneRadioAMFM = " . $tieneRadioAMFM
                                . ", tieneRecordatorioEncendidoLuces = " . $tieneRecordatorioEncendidoLuces
                                . ", tieneReproductorCD = " . $tieneReproductorCD
                                . ", tieneReproductorDVD = " . $tieneReproductorDVD
                                . ", tieneReproductorMP3 = " . $tieneReproductorMP3
                                . ", tieneRespadosTraseros = " . $tieneRespadosTraseros
                                . ", tieneRinesAleacion = " . $tieneRinesAleacion
                                . ", tieneSegurosElectricosCentralizados = " . $tieneSegurosElectricosCentralizados
                                . ", tieneSensorLluvia = " . $tieneSensorLluvia
                                . ", tieneSensoresLuz = " . $tieneSensoresLuz
                                . ", tieneSensoresReversa = " . $tieneSensoresReversa
                                . ", tieneTapiceriaPiel = " . $tieneTapiceriaPiel
                                . ", tieneTarjetaSD = " . $tieneTarjetaSD
                                . ", tieneTerceraLuzFrenado = " . $tieneTerceraLuzFrenado
                                . ", unicoDueno = " . $unicoDueno
                                . ", tieneUsb = " . $tieneUsb
                                . ", tieneVidriosElectricos = " . $tieneVidriosElectricos
                            . " WHERE id = " . $id);

                        $vehiculo_BD = consulta($conexion, "SELECT * FROM vehiculo WHERE id = " . $id);
                        $vehiculo = obtenResultado($vehiculo_BD);

                        $id = $vehiculo["id"];
                        $publicado = $vehiculo["publicado"];
                        $destacado = $vehiculo["destacado"];
                        $certificado = $vehiculo["certificado"];
                        $descuentoEspecial = $vehiculo["descuentoEspecial"];
                        $tipo = $vehiculo["tipo"];
                        $marca = $vehiculo["marca"];
                        $modelo = $vehiculo["modelo"];
                        $version = $vehiculo["version"];
                        $ano = $vehiculo["ano"];
                        $color = $vehiculo["color"];
                        $precio = $vehiculo["precio"];
                        $enganche = $vehiculo["enganche"];
                        $litros = $vehiculo["litros"];
                        $combustible = $vehiculo["combustible"];
                        $transmision = $vehiculo["transmision"];
                        $puertas = $vehiculo["puertas"];
                        $asientos = $vehiculo["asientos"];
                        $bolsasAire = $vehiculo["bolsasAire"];
                        $kilometraje = $vehiculo["kilometraje"];
                        $tipoFactura = $vehiculo["tipoFactura"];
                        $numeroLlaves = $vehiculo["numeroLlaves"];
                        $vidaUtilLlantas = $vehiculo["vidaUtilLlantas"];
                        $facturaAgencia = $vehiculo["facturaAgencia"];
                        $mantenimientosAgencia = $vehiculo["mantenimientosAgencia"];
                        $impuestosCorriente = $vehiculo["impuestosCorriente"];
                        $mantenimientosCorriente = $vehiculo["mantenimientosCorriente"];
                        $manosLibres = $vehiculo["manosLibres"];
                        $descripcion = $vehiculo["descripcion"];
                        $puntosDestacados = $vehiculo["puntosDestacados"];
                        $imagenPrincipal = $vehiculo["imagenPrincipal"];
                        $video_titulo = $vehiculo["video_titulo"];
                        $video_resumen = $vehiculo["video_resumen"];
                        $video_detalle = $vehiculo["video_detalle"];
                        $video_url = $vehiculo["video_url"];
                        $video_vistaPrevia = $vehiculo["video_vistaPrevia"];
                        $video_publicado = $vehiculo["video_publicado"];
                        $idSucursal = $vehiculo["idSucursal"];
                        $idConcesionario = $vehiculo["idConcesionario"];

                        $colorInterior = $vehiculo["colorInterior"];
                        $direccion = $vehiculo["direccion"];
                        $tieneAireAcondicionado = $vehiculo["tieneAireAcondicionado"];
                        $tieneAlarma = $vehiculo["tieneAlarma"];
                        $tieneAlfombrillaLlantaRefaccion = $vehiculo["tieneAlfombrillaLlantaRefaccion"];
                        $tieneAperturaRemotaCajuela = $vehiculo["tieneAperturaRemotaCajuela"];
                        $tieneAsientoConductorAjusteAltura = $vehiculo["tieneAsientoConductorAjusteAltura"];
                        $tieneAsientosElectricos = $vehiculo["tieneAsientosElectricos"];
                        $tieneAsientosTraserosAbatibles = $vehiculo["tieneAsientosTraserosAbatibles"];
                        $tieneAsistenciaFrenado = $vehiculo["tieneAsistenciaFrenado"];
                        $tieneBandejaLlantaRefaccion = $vehiculo["tieneBandejaLlantaRefaccion"];
                        $tieneBarraAntivuelco = $vehiculo["tieneBarraAntivuelco"];
                        $esBlindado = $vehiculo["esBlindado"];
                        $tieneBluetooth = $vehiculo["tieneBluetooth"];
                        $tieneBolsaAireConductor = $vehiculo["tieneBolsaAireConductor"];
                        $tieneBolsaAirePasajero = $vehiculo["tieneBolsaAirePasajero"];
                        $tieneBolsasAireLaterales = $vehiculo["tieneBolsasAireLaterales"];
                        $tieneBolsasAireCortina = $vehiculo["tieneBolsasAireCortina"];
                        $tieneCabecerasAsientosTraseros = $vehiculo["tieneCabecerasAsientosTraseros"];
                        $tieneComputadoraAbordo = $vehiculo["tieneComputadoraAbordo"];
                        $tieneControlTemperatura = $vehiculo["tieneControlTemperatura"];
                        $tieneControlEstabilidad = $vehiculo["tieneControlEstabilidad"];
                        $tieneControlLucesDelanteras = $vehiculo["tieneControlLucesDelanteras"];
                        $tieneControlVolante = $vehiculo["tieneControlVolante"];
                        $tieneDefensasColorCarroceria = $vehiculo["tieneDefensasColorCarroceria"];
                        $tieneDesempanadorTrasero = $vehiculo["tieneDesempanadorTrasero"];
                        $tieneEspejosElectricos = $vehiculo["tieneEspejosElectricos"];
                        $tieneFarosNiebla = $vehiculo["tieneFarosNiebla"];
                        $tieneFrenosABS = $vehiculo["tieneFrenosABS"];
                        $tieneGps = $vehiculo["tieneGps"];
                        $esImportado = $vehiculo["esImportado"];
                        $tieneInmovilizador = $vehiculo["tieneInmovilizador"];
                        $tieneLimpiaparabrisas = $vehiculo["tieneLimpiaparabrisas"];
                        $tieneLlantaRefaccion = $vehiculo["tieneLlantaRefaccion"];
                        $tieneLucesNieblaDelanteras = $vehiculo["tieneLucesNieblaDelanteras"];
                        $tieneLucesNieblaTraseras = $vehiculo["tieneLucesNieblaTraseras"];
                        $tieneLucesXenon = $vehiculo["tieneLucesXenon"];
                        $tieneParachoques = $vehiculo["tieneParachoques"];
                        $tienePilotoAutomatico = $vehiculo["tienePilotoAutomatico"];
                        $tienePortavasos = $vehiculo["tienePortavasos"];
                        $tieneQuemacocos = $vehiculo["tieneQuemacocos"];
                        $tieneRadioAMFM = $vehiculo["tieneRadioAMFM"];
                        $tieneRecordatorioEncendidoLuces = $vehiculo["tieneRecordatorioEncendidoLuces"];
                        $tieneReproductorCD = $vehiculo["tieneReproductorCD"];
                        $tieneReproductorDVD = $vehiculo["tieneReproductorDVD"];
                        $tieneReproductorMP3 = $vehiculo["tieneReproductorMP3"];
                        $tieneRespadosTraseros = $vehiculo["tieneRespadosTraseros"];
                        $tieneRinesAleacion = $vehiculo["tieneRinesAleacion"];
                        $tieneSegurosElectricosCentralizados = $vehiculo["tieneSegurosElectricosCentralizados"];
                        $tieneSensorLluvia = $vehiculo["tieneSensorLluvia"];
                        $tieneSensoresLuz = $vehiculo["tieneSensoresLuz"];
                        $tieneSensoresReversa = $vehiculo["tieneSensoresReversa"];
                        $tieneTapiceriaPiel = $vehiculo["tieneTapiceriaPiel"];
                        $tieneTarjetaSD = $vehiculo["tieneTarjetaSD"];
                        $tieneTerceraLuzFrenado = $vehiculo["tieneTerceraLuzFrenado"];
                        $unicoDueno = $vehiculo["unicoDueno"];
                        $tieneUsb = $vehiculo["tieneUsb"];
                        $tieneVidriosElectricos = $vehiculo["tieneVidriosElectricos"];

                        $intelimotor_id = $vehiculo["intelimotor_id"];
                        $intelimotor_imported = $vehiculo["intelimotor_imported"];
                        $intelimotor_kms = $vehiculo["intelimotor_kms"];
                        $intelimotor_listPrice = $vehiculo["intelimotor_listPrice"];
                        $intelimotor_title = $vehiculo["intelimotor_title"];
                        $intelimotor_brand = $vehiculo["intelimotor_brand"];
                        $intelimotor_model = $vehiculo["intelimotor_model"];
                        $intelimotor_year = $vehiculo["intelimotor_year"];
                        $intelimotor_trim = $vehiculo["intelimotor_trim"];
                        $intelimotor_transmission = $vehiculo["intelimotor_transmission"];
                        $intelimotor_doors = $vehiculo["intelimotor_doors"];
                        $intelimotor_fuelType = $vehiculo["intelimotor_fuelType"];
                        $intelimotor_steering = $vehiculo["intelimotor_steering"];
                        $intelimotor_tractionControl = $vehiculo["intelimotor_tractionControl"];
                        $intelimotor_vehicleBodyType = $vehiculo["intelimotor_vehicleBodyType"];
                        $intelimotor_engine = $vehiculo["intelimotor_engine"];
                        $intelimotor_exteriorColor = $vehiculo["intelimotor_exteriorColor"];
                        $intelimotor_interiorColor = $vehiculo["intelimotor_interiorColor"];
                        $intelimotor_hasAutopilot = $vehiculo["intelimotor_hasAutopilot"];
                        $intelimotor_hasLightOnReminder = $vehiculo["intelimotor_hasLightOnReminder"];
                        $intelimotor_hasOnboardComputer = $vehiculo["intelimotor_hasOnboardComputer"];
                        $intelimotor_hasRearFoldingSeat = $vehiculo["intelimotor_hasRearFoldingSeat"];
                        $intelimotor_hasSlidingRoof = $vehiculo["intelimotor_hasSlidingRoof"];
                        $intelimotor_hasXenonHeadlights = $vehiculo["intelimotor_hasXenonHeadlights"];
                        $intelimotor_hasCoasters = $vehiculo["intelimotor_hasCoasters"];
                        $intelimotor_hasClimateControl = $vehiculo["intelimotor_hasClimateControl"];
                        $intelimotor_hasAbsBrakes = $vehiculo["intelimotor_hasAbsBrakes"];
                        $intelimotor_hasAlarm = $vehiculo["intelimotor_hasAlarm"];
                        $intelimotor_hasAlloyWheels = $vehiculo["intelimotor_hasAlloyWheels"];
                        $intelimotor_hasDriverAirbag = $vehiculo["intelimotor_hasDriverAirbag"];
                        $intelimotor_hasElectronicBrakeAssist = $vehiculo["intelimotor_hasElectronicBrakeAssist"];
                        $intelimotor_hasEngineInmovilizer = $vehiculo["intelimotor_hasEngineInmovilizer"];
                        $intelimotor_hasFogLight = $vehiculo["intelimotor_hasFogLight"];
                        $intelimotor_hasFrontFoglights = $vehiculo["intelimotor_hasFrontFoglights"];
                        $intelimotor_hasPassengerAirbag = $vehiculo["intelimotor_hasPassengerAirbag"];
                        $intelimotor_hasRainSensor = $vehiculo["intelimotor_hasRainSensor"];
                        $intelimotor_hasRearFoglights = $vehiculo["intelimotor_hasRearFoglights"];
                        $intelimotor_hasRearWindowDefogger = $vehiculo["intelimotor_hasRearWindowDefogger"];
                        $intelimotor_hasRollBar = $vehiculo["intelimotor_hasRollBar"];
                        $intelimotor_hasSideImpactAirbag = $vehiculo["intelimotor_hasSideImpactAirbag"];
                        $intelimotor_hasStabilityControl = $vehiculo["intelimotor_hasStabilityControl"];
                        $intelimotor_hasSteeringWheelControl = $vehiculo["intelimotor_hasSteeringWheelControl"];
                        $intelimotor_hasThirdStop = $vehiculo["intelimotor_hasThirdStop"];
                        $intelimotor_hasCurtainAirbag = $vehiculo["intelimotor_hasCurtainAirbag"];
                        $intelimotor_armored = $vehiculo["intelimotor_armored"];
                        $intelimotor_hasAirConditioning = $vehiculo["intelimotor_hasAirConditioning"];
                        $intelimotor_hasElectricMirrors = $vehiculo["intelimotor_hasElectricMirrors"];
                        $intelimotor_hasGps = $vehiculo["intelimotor_hasGps"];
                        $intelimotor_hasHeadlightControl = $vehiculo["intelimotor_hasHeadlightControl"];
                        $intelimotor_hasHeadrestRearSeat = $vehiculo["intelimotor_hasHeadrestRearSeat"];
                        $intelimotor_hasHeightAdjustableDriverSeat = $vehiculo["intelimotor_hasHeightAdjustableDriverSeat"];
                        $intelimotor_hasLeatherUpholstery = $vehiculo["intelimotor_hasLeatherUpholstery"];
                        $intelimotor_hasLightSensor = $vehiculo["intelimotor_hasLightSensor"];
                        $intelimotor_hasPaintedBumper = $vehiculo["intelimotor_hasPaintedBumper"];
                        $intelimotor_hasParkingSensor = $vehiculo["intelimotor_hasParkingSensor"];
                        $intelimotor_hasPowerWindows = $vehiculo["intelimotor_hasPowerWindows"];
                        $intelimotor_hasRemoteTrunkRelease = $vehiculo["intelimotor_hasRemoteTrunkRelease"];
                        $intelimotor_hasElectricSeats = $vehiculo["intelimotor_hasElectricSeats"];
                        $intelimotor_hasRearBackrest = $vehiculo["intelimotor_hasRearBackrest"];
                        $intelimotor_hasCentralPowerDoorLocks = $vehiculo["intelimotor_hasCentralPowerDoorLocks"];
                        $intelimotor_hasAmfmRadio = $vehiculo["intelimotor_hasAmfmRadio"];
                        $intelimotor_hasBluetooth = $vehiculo["intelimotor_hasBluetooth"];
                        $intelimotor_hasCdPlayer = $vehiculo["intelimotor_hasCdPlayer"];
                        $intelimotor_hasDvd = $vehiculo["intelimotor_hasDvd"];
                        $intelimotor_hasMp3Player = $vehiculo["intelimotor_hasMp3Player"];
                        $intelimotor_hasSdCard = $vehiculo["intelimotor_hasSdCard"];
                        $intelimotor_hasUsb = $vehiculo["intelimotor_hasUsb"];
                        $intelimotor_hasBullBar = $vehiculo["intelimotor_hasBullBar"];
                        $intelimotor_hasSpareTyreSupport = $vehiculo["intelimotor_hasSpareTyreSupport"];
                        $intelimotor_hasTrayCover = $vehiculo["intelimotor_hasTrayCover"];
                        $intelimotor_hasTrayMat = $vehiculo["intelimotor_hasTrayMat"];
                        $intelimotor_hasWindscreenWiper = $vehiculo["intelimotor_hasWindscreenWiper"];
                        $intelimotor_singleOwner = $vehiculo["intelimotor_singleOwner"];
                        $intelimotor_youtubeVideoUrl = $vehiculo["intelimotor_youtubeVideoUrl"];
                        $intelimotor_picture = $vehiculo["intelimotor_picture"];

                        $mensaje = "ok - Los cambios han sido guardados";

                        registraEvento("CMS : Actualización de vehículo | id = " . $id);
                    }

                    if (!file_exists($constante_rutaVehiculos  . $id)) {
                        try {
                            mkdir($constante_rutaVehiculos  . $id, 0755, true);
                        } catch (Exception $ex) {
                        }
                    }
                    
                    $tamanoLetraTitulo = 17;
                    $tamanoLetraSubtitulo = 11;
                    $tamanoLetraNormal = 8;
                    $saltoSencillo = 5;
                    $saltoDoble = 10;
                    $posicionVerticalPrimaria = "40";
                    $posicionVerticalSecundaria = "80";
                    $posicionHorizontalColumnaIzquierda = "10";
                    $posicionHorizontalColumnaCentral = "30";
                    $posicionHorizontalColumnaDerecha = "80";

                    $unicoDueno_pdf = $unicoDueno == 1 ? "Si" : "No";
                    $facturaAgencia_pdf = $facturaAgencia == 1 ? "Si" : "No";
                    $mantenimientosAgencia_pdf = $mantenimientosAgencia == 1 ? "Si" : "No";
                    $impuestosCorriente_pdf = $impuestosCorriente == 1 ? "Si" : "No";
                    $mantenimientosCorriente_pdf = $mantenimientosCorriente == 1 ? "Si" : "No";
                    $tieneTapiceriaPiel_pdf = $tieneTapiceriaPiel == 1 ? "Si" : "No";
                    $tieneQuemacocos_pdf = $tieneQuemacocos == 1 ? "Si" : "No";
                    $tieneAireAcondicionado_pdf = $tieneAireAcondicionado == 1 ? "Si" : "No";
                    $tieneRadioAMFM_pdf = $tieneRadioAMFM == 1 ? "Si" : "No";
                    $manosLibres_pdf = $manosLibres == 1 ? "Si" : "No";
                    $tieneFarosNiebla_pdf = $tieneFarosNiebla == 1 ? "Si" : "No";

                    try {
                        $pdf = new PDF_HTML();
                    } catch (Exception $ex) {
                        echo "F2 - " . $ex->getMessage() . " -";
                    }

                    // Carga imagen principal

                    if (isset($_FILES["imagenPrincipal"])) {
                        try {
                            $archivo = $_FILES["imagenPrincipal"];

                            if ($archivo["size"] > 0) {
                                $nombreEstandarizado = "vehiculo_" . $id . "_imagenPrincipal_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                $archivoDestino = $constante_rutaVehiculos . "/" . $id . "/" . $nombreEstandarizado;

                                if (!file_exists($constante_rutaVehiculos . "/" . $id)) {
                                    mkdir($constante_rutaVehiculos . "/" . $id, 0755, true);
                                }

                                move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                consulta($conexion, "UPDATE vehiculo SET imagenPrincipal = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                $imagenPrincipal = $nombreEstandarizado;

                                //
                            }
                        } catch (Exception $e) {
                        }
                    }

                    // Carga imagen de galeria

                    if (isset($_FILES["imagenGaleria"])) {
                        try {
                            //$archivo = $_FILES["imagenGaleria"];
                            $archivos = reArrayFiles($_FILES["imagenGaleria"]);

                            foreach ($archivos as $archivo) {
                                if ($archivo["size"] > 0) {
                                    $nombreEstandarizado = "vehiculo_" . $id . "_imagenGaleria_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                    $archivoDestino = $constante_rutaVehiculos . "/" . $id . "/galeria/" . $nombreEstandarizado;

                                    if (!file_exists($constante_rutaVehiculos . "/" . $id . "/galeria")) {
                                        mkdir($constante_rutaVehiculos . "/" . $id . "/galeria", 0755, true);
                                    }

                                    move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                    //$pdf->Cell( 40, 40, $pdf->Image($archivoDestino, $pdf->GetX(), $pdf->GetY(), 33.78), 0, 0, 'L', false );
                                }
                            }
                        } catch (Exception $e) {
                        }
                    }
/*
                    //Generacion PDF Pagina 1

                    $pdf->AddPage();
                    $pdf->SetFont("Arial", "B", $tamanoLetraTitulo);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->Ln(20);
                    $pdf->Cell(5);
                    $pdf->Cell(130, 30, iconv('UTF-8', 'windows-1252', $ano . " " . $marca . " " . $modelo));
                    $pdf->SetTextColor(9, 131, 73);
                    $pdf->Cell(50,30, "$ " . number_format($precio, 2, '.', ','));
                    $archivoDestinoImgPrincipal = $constante_rutaVehiculos . $id . "/" . $imagenPrincipal;
                    $pdf->Ln(25);
                    $pdf->Cell(5);
                    $pdf->Cell( 120, 40, $pdf->Image($archivoDestinoImgPrincipal, $pdf->GetX(), $pdf->GetY(), 120), 0, 0, 'L', false );
                    //$pdf->Cell(200);
                    $pdf->SetY(43);
                    $pdf->SetFont("Arial", "B", $tamanoLetraSubtitulo);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->Cell(135);
                    $pdf->Cell(20,10,"Datos Generales");
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(10);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Marca',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $marca),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Modelo',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $modelo),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,iconv('UTF-8', 'windows-1252', 'Versión'),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $version),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Color',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $color),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Tipo',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $tipo),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,iconv('UTF-8', 'windows-1252', 'Año'),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $ano),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,iconv('UTF-8', 'windows-1252', 'Transmisión'),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $transmision),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,iconv('UTF-8', 'windows-1252', 'Kilometraje'),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', number_format($kilometraje)),0,0,'L',true);

                    //Lineas

                    $pdf->SetLineWidth(0.2);
                    $pdf->SetDrawColor(181,181,181);
                    $pdf->Line(149,62,200,62);
                    $pdf->Line(149,71,200,71);
                    $pdf->Line(149,80,200,80);
                    $pdf->Line(149,89,200,89);
                    $pdf->Line(149,98,200,98);
                    $pdf->Line(149,107,200,107);
                    $pdf->Line(149,116,200,116);

                    //Descripcion y Caracteristicas

                    $pdf->Ln(10);

                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraSubtitulo);
                    $pdf->Cell(5);
                    $pdf->Cell(130, 15, iconv('UTF-8', 'windows-1252', "Descripción"));
                    $pdf->Cell(50, 15, iconv('UTF-8', 'windows-1252', "Características"));
                    $pdf->Ln(10);
                    $pdf->Cell(5);
                    $pdf->SetTextColor(87,86,86);
                    $breaks = array("<br />","<br>","<br/>","</p><p>"); 
                    $descripcionLimpia = str_ireplace($breaks, "\n", $descripcion); 
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);

                    $pdf->MultiCell(120,8,iconv('UTF-8', 'windows-1252',html_entity_decode(strip_tags($descripcionLimpia))),0);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    
                    $pdf->SetTextColor(119,119,119);
                    $pdf->SetY(129);
                    $pdf->Ln(10);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Combustible',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $combustible),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Litros',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $litros),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Puertas',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $puertas),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Asientos',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $asientos),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Bolsas de Aire',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $bolsasAire),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,'Tipo de Factura',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $tipoFactura),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,iconv('UTF-8', 'windows-1252', 'Número de Llaves'),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $numeroLlaves),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(30,9,iconv('UTF-8', 'windows-1252', 'Vida Útil Llantas (%)'),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(25,9,iconv('UTF-8', 'windows-1252', $vidaUtilLlantas),0,0,'L',true);

                    $pdf->SetDrawColor(181,181,181);
                    $pdf->Line(149,148,200,148);
                    $pdf->Line(149,157,200,157);
                    $pdf->Line(149,166,200,166);
                    $pdf->Line(149,175,200,175);
                    $pdf->Line(149,184,200,184);
                    $pdf->Line(149,193,200,193);
                    $pdf->Line(149,202,200,202);

                    // 2da Hoja

                    $pdf->AddPage();
                    $pdf->Ln(25);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraSubtitulo);
                    $pdf->Cell(5);
                    $pdf->Cell(130);
                    $pdf->Cell(50, 15, iconv('UTF-8', 'windows-1252', "Destacados"));
                    $pdf->Ln(10);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    
                    $pdf->SetTextColor(119,119,119);
                    $pdf->SetY(30);
                    $pdf->Ln(10);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,iconv('UTF-8', 'windows-1252', "Único Dueño"),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $unicoDueno_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Factura Agencia',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $facturaAgencia_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);

                    $pdf->Cell(45,9,'Mantenimiento Agencia',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $mantenimientosAgencia_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Impuestos al Corriente',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $impuestosCorriente_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Mantenimiento al Corriente',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $mantenimientosCorriente_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Piel',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $tieneTapiceriaPiel_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Techo corredizo',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $tieneQuemacocos_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Clima',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $tieneAireAcondicionado_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Stereo',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $tieneRadioAMFM_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,'Manos Libres',0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $manosLibres_pdf),0,0,'L',true);

                    $pdf->SetTextColor(119,119,119);
                    $pdf->Ln(9);
                    $pdf->Cell(135);
                    $pdf->SetFillColor(246, 247, 247);
                    $pdf->Cell(3,9,'',0,0,'L',true);
                    $pdf->SetFont("Arial", "", $tamanoLetraNormal);
                    $pdf->Cell(45,9,iconv('UTF-8', 'windows-1252', 'Faros de Niebla'),0,0,'L',true);
                    $pdf->SetTextColor(29,29,27);
                    $pdf->SetFont("Arial", "B", $tamanoLetraNormal);
                    $pdf->Cell(10,9,iconv('UTF-8', 'windows-1252', $tieneFarosNiebla_pdf),0,0,'L',true);

                    $pdf->SetDrawColor(181,181,181);
                    $pdf->Line(149,49,200,49);
                    $pdf->Line(149,58,200,58);
                    $pdf->Line(149,67,200,67);
                    $pdf->Line(149,76,200,76);
                    $pdf->Line(149,85,200,85);
                    $pdf->Line(149,94,200,94);
                    $pdf->Line(149,103,200,103);
                    $pdf->Line(149,112,200,112);
                    $pdf->Line(149,121,200,121);
                    $pdf->Line(149,130,200,130);
                    $pdf->Line(149,139,200,139);
                    $pdf->Line(149,148,200,148);
                    $pdf->Line(149,157,200,157);

                    //echo $constante_rutaVehiculos . $id . "/galeria"; die();

                    $archivosGaleria = scandir($constante_rutaVehiculos . $id . "/galeria");
                    $indice = 0;
                    $pagina = 2;

                    $x = 40;
                    $y = 32;
                    //print_r($archivos);die();

                    foreach ($archivosGaleria as $archivo) {
                        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                        if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {

                            $indice++;
                            if($pagina == 2){
                                if($indice > 10){
                                    $indice = 1;
                                    $pagina++;
                                    $pdf->AddPage();
                                    $y = 40;
                                }
                            }else{
                                if($indice > 15){
                                    $indice = 1;
                                    $pagina++;
                                    $pdf->AddPage();
                                    $y = 40;
                                }
                            }



                            if($pagina == 2){
                                if($indice % 2 == 1){
                                    $x = 15;
                                    if($indice > 2){
                                        $y = $y + 50;
                                    }

                                }else{
                                    $x = 75;
                                    
                                }
                            }else if($pagina > 2){
                                if($indice % 3 == 1){
                                    $x = 15;
                                    if($indice > 2){
                                        $y = $y + 50;
                                    }

                                }else if($indice % 3 == 2){
                                    $x = 75;
                                    
                                }else{
                                    $x = 135;  
                                }

                            }


                            $archivoDestino = $constante_rutaVehiculos . $id . "/galeria/" . $archivo;

                            $pdf->Image($archivoDestino, $x, $y, 50);
                        }

                    }

                    chmod($constante_rutaVehiculos  . $id, 0777);
                    
                    chmod($constante_rutaVehiculos  . $id, 0755);

                    $pdf->Output("F", $constante_rutaVehiculos  . $id . "/FICHA_TECNICA_" . $id . ".pdf");
*/
                    // Carga vista previa del video

                    if (isset($_FILES["video_vistaPrevia"])) {
                        try {
                            $archivo = $_FILES["video_vistaPrevia"];

                            if ($archivo["size"] > 0) {
                                $nombreEstandarizado = "vehiculo_" . $id . "_videoVistaPrevia_" . date("YmdHis") . "_" . rand(100, 999) . "." . pathinfo($archivo["name"], PATHINFO_EXTENSION);
                                $archivoDestino = $constante_rutaVehiculos . "/" . $id . "/" . $nombreEstandarizado;

                                if (!file_exists($constante_rutaVehiculos . "/" . $id)) {
                                    mkdir($constante_rutaVehiculos . "/" . $id, 0755, true);
                                }

                                move_uploaded_file($archivo["tmp_name"], $archivoDestino);

                                consulta($conexion, "UPDATE vehiculo SET video_vistaPrevia = " . (estaVacio($nombreEstandarizado) ? "NULL" : "'" . $nombreEstandarizado . "'") . " WHERE id = " . $id);
                                $video_vistaPrevia = $nombreEstandarizado;
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            } else {
                if (!estaVacio($id)) {

                    // Es consulta

                    $vehiculo_BD = consulta($conexion, "SELECT * FROM vehiculo WHERE id = " . $id);
                    $vehiculo = obtenResultado($vehiculo_BD);

                    $id = $vehiculo["id"];
                    $publicado = $vehiculo["publicado"];
                    $destacado = $vehiculo["destacado"];
                    $certificado = $vehiculo["certificado"];
                    $descuentoEspecial = $vehiculo["descuentoEspecial"];
                    $tipo = $vehiculo["tipo"];
                    $marca = $vehiculo["marca"];
                    $modelo = $vehiculo["modelo"];
                    $version = $vehiculo["version"];
                    $ano = $vehiculo["ano"];
                    $color = $vehiculo["color"];
                    $precio = $vehiculo["precio"];
                    $enganche = $vehiculo["enganche"];
                    $litros = $vehiculo["litros"];
                    $combustible = $vehiculo["combustible"];
                    $transmision = $vehiculo["transmision"];
                    $puertas = $vehiculo["puertas"];
                    $asientos = $vehiculo["asientos"];
                    $bolsasAire = $vehiculo["bolsasAire"];
                    $kilometraje = $vehiculo["kilometraje"];
                    $tipoFactura = $vehiculo["tipoFactura"];
                    $numeroLlaves = $vehiculo["numeroLlaves"];
                    $vidaUtilLlantas = $vehiculo["vidaUtilLlantas"];
                    $facturaAgencia = $vehiculo["facturaAgencia"];
                    $mantenimientosAgencia = $vehiculo["mantenimientosAgencia"];
                    $impuestosCorriente = $vehiculo["impuestosCorriente"];
                    $mantenimientosCorriente = $vehiculo["mantenimientosCorriente"];
                    $manosLibres = $vehiculo["manosLibres"];
                    $descripcion = $vehiculo["descripcion"];
                    $puntosDestacados = $vehiculo["puntosDestacados"];
                    $imagenPrincipal = $vehiculo["imagenPrincipal"];
                    $video_titulo = $vehiculo["video_titulo"];
                    $video_resumen = $vehiculo["video_resumen"];
                    $video_detalle = $vehiculo["video_detalle"];
                    $video_url = $vehiculo["video_url"];
                    $video_vistaPrevia = $vehiculo["video_vistaPrevia"];
                    $video_publicado = $vehiculo["video_publicado"];
                    $idSucursal = $vehiculo["idSucursal"];
                    $idConcesionario = $vehiculo["idConcesionario"];

                    $colorInterior = $vehiculo["colorInterior"];
                    $direccion = $vehiculo["direccion"];
                    $tieneAireAcondicionado = $vehiculo["tieneAireAcondicionado"];
                    $tieneAlarma = $vehiculo["tieneAlarma"];
                    $tieneAlfombrillaLlantaRefaccion = $vehiculo["tieneAlfombrillaLlantaRefaccion"];
                    $tieneAperturaRemotaCajuela = $vehiculo["tieneAperturaRemotaCajuela"];
                    $tieneAsientoConductorAjusteAltura = $vehiculo["tieneAsientoConductorAjusteAltura"];
                    $tieneAsientosElectricos = $vehiculo["tieneAsientosElectricos"];
                    $tieneAsientosTraserosAbatibles = $vehiculo["tieneAsientosTraserosAbatibles"];
                    $tieneAsistenciaFrenado = $vehiculo["tieneAsistenciaFrenado"];
                    $tieneBandejaLlantaRefaccion = $vehiculo["tieneBandejaLlantaRefaccion"];
                    $tieneBarraAntivuelco = $vehiculo["tieneBarraAntivuelco"];
                    $esBlindado = $vehiculo["esBlindado"];
                    $tieneBluetooth = $vehiculo["tieneBluetooth"];
                    $tieneBolsaAireConductor = $vehiculo["tieneBolsaAireConductor"];
                    $tieneBolsaAirePasajero = $vehiculo["tieneBolsaAirePasajero"];
                    $tieneBolsasAireLaterales = $vehiculo["tieneBolsasAireLaterales"];
                    $tieneBolsasAireCortina = $vehiculo["tieneBolsasAireCortina"];
                    $tieneCabecerasAsientosTraseros = $vehiculo["tieneCabecerasAsientosTraseros"];
                    $tieneComputadoraAbordo = $vehiculo["tieneComputadoraAbordo"];
                    $tieneControlTemperatura = $vehiculo["tieneControlTemperatura"];
                    $tieneControlEstabilidad = $vehiculo["tieneControlEstabilidad"];
                    $tieneControlLucesDelanteras = $vehiculo["tieneControlLucesDelanteras"];
                    $tieneControlVolante = $vehiculo["tieneControlVolante"];
                    $tieneDefensasColorCarroceria = $vehiculo["tieneDefensasColorCarroceria"];
                    $tieneDesempanadorTrasero = $vehiculo["tieneDesempanadorTrasero"];
                    $tieneEspejosElectricos = $vehiculo["tieneEspejosElectricos"];
                    $tieneFarosNiebla = $vehiculo["tieneFarosNiebla"];
                    $tieneFrenosABS = $vehiculo["tieneFrenosABS"];
                    $tieneGps = $vehiculo["tieneGps"];
                    $esImportado = $vehiculo["esImportado"];
                    $tieneInmovilizador = $vehiculo["tieneInmovilizador"];
                    $tieneLimpiaparabrisas = $vehiculo["tieneLimpiaparabrisas"];
                    $tieneLlantaRefaccion = $vehiculo["tieneLlantaRefaccion"];
                    $tieneLucesNieblaDelanteras = $vehiculo["tieneLucesNieblaDelanteras"];
                    $tieneLucesNieblaTraseras = $vehiculo["tieneLucesNieblaTraseras"];
                    $tieneLucesXenon = $vehiculo["tieneLucesXenon"];
                    $tieneParachoques = $vehiculo["tieneParachoques"];
                    $tienePilotoAutomatico = $vehiculo["tienePilotoAutomatico"];
                    $tienePortavasos = $vehiculo["tienePortavasos"];
                    $tieneQuemacocos = $vehiculo["tieneQuemacocos"];
                    $tieneRadioAMFM = $vehiculo["tieneRadioAMFM"];
                    $tieneRecordatorioEncendidoLuces = $vehiculo["tieneRecordatorioEncendidoLuces"];
                    $tieneReproductorCD = $vehiculo["tieneReproductorCD"];
                    $tieneReproductorDVD = $vehiculo["tieneReproductorDVD"];
                    $tieneReproductorMP3 = $vehiculo["tieneReproductorMP3"];
                    $tieneRespadosTraseros = $vehiculo["tieneRespadosTraseros"];
                    $tieneRinesAleacion = $vehiculo["tieneRinesAleacion"];
                    $tieneSegurosElectricosCentralizados = $vehiculo["tieneSegurosElectricosCentralizados"];
                    $tieneSensorLluvia = $vehiculo["tieneSensorLluvia"];
                    $tieneSensoresLuz = $vehiculo["tieneSensoresLuz"];
                    $tieneSensoresReversa = $vehiculo["tieneSensoresReversa"];
                    $tieneTapiceriaPiel = $vehiculo["tieneTapiceriaPiel"];
                    $tieneTarjetaSD = $vehiculo["tieneTarjetaSD"];
                    $tieneTerceraLuzFrenado = $vehiculo["tieneTerceraLuzFrenado"];
                    $unicoDueno = $vehiculo["unicoDueno"];
                    $tieneUsb = $vehiculo["tieneUsb"];
                    $tieneVidriosElectricos = $vehiculo["tieneVidriosElectricos"];

                    $intelimotor_id = $vehiculo["intelimotor_id"];
                    $intelimotor_imported = $vehiculo["intelimotor_imported"];
                    $intelimotor_kms = $vehiculo["intelimotor_kms"];
                    $intelimotor_listPrice = $vehiculo["intelimotor_listPrice"];
                    $intelimotor_title = $vehiculo["intelimotor_title"];
                    $intelimotor_brand = $vehiculo["intelimotor_brand"];
                    $intelimotor_model = $vehiculo["intelimotor_model"];
                    $intelimotor_year = $vehiculo["intelimotor_year"];
                    $intelimotor_trim = $vehiculo["intelimotor_trim"];
                    $intelimotor_transmission = $vehiculo["intelimotor_transmission"];
                    $intelimotor_doors = $vehiculo["intelimotor_doors"];
                    $intelimotor_fuelType = $vehiculo["intelimotor_fuelType"];
                    $intelimotor_steering = $vehiculo["intelimotor_steering"];
                    $intelimotor_tractionControl = $vehiculo["intelimotor_tractionControl"];
                    $intelimotor_vehicleBodyType = $vehiculo["intelimotor_vehicleBodyType"];
                    $intelimotor_engine = $vehiculo["intelimotor_engine"];
                    $intelimotor_exteriorColor = $vehiculo["intelimotor_exteriorColor"];
                    $intelimotor_interiorColor = $vehiculo["intelimotor_interiorColor"];
                    $intelimotor_hasAutopilot = $vehiculo["intelimotor_hasAutopilot"];
                    $intelimotor_hasLightOnReminder = $vehiculo["intelimotor_hasLightOnReminder"];
                    $intelimotor_hasOnboardComputer = $vehiculo["intelimotor_hasOnboardComputer"];
                    $intelimotor_hasRearFoldingSeat = $vehiculo["intelimotor_hasRearFoldingSeat"];
                    $intelimotor_hasSlidingRoof = $vehiculo["intelimotor_hasSlidingRoof"];
                    $intelimotor_hasXenonHeadlights = $vehiculo["intelimotor_hasXenonHeadlights"];
                    $intelimotor_hasCoasters = $vehiculo["intelimotor_hasCoasters"];
                    $intelimotor_hasClimateControl = $vehiculo["intelimotor_hasClimateControl"];
                    $intelimotor_hasAbsBrakes = $vehiculo["intelimotor_hasAbsBrakes"];
                    $intelimotor_hasAlarm = $vehiculo["intelimotor_hasAlarm"];
                    $intelimotor_hasAlloyWheels = $vehiculo["intelimotor_hasAlloyWheels"];
                    $intelimotor_hasDriverAirbag = $vehiculo["intelimotor_hasDriverAirbag"];
                    $intelimotor_hasElectronicBrakeAssist = $vehiculo["intelimotor_hasElectronicBrakeAssist"];
                    $intelimotor_hasEngineInmovilizer = $vehiculo["intelimotor_hasEngineInmovilizer"];
                    $intelimotor_hasFogLight = $vehiculo["intelimotor_hasFogLight"];
                    $intelimotor_hasFrontFoglights = $vehiculo["intelimotor_hasFrontFoglights"];
                    $intelimotor_hasPassengerAirbag = $vehiculo["intelimotor_hasPassengerAirbag"];
                    $intelimotor_hasRainSensor = $vehiculo["intelimotor_hasRainSensor"];
                    $intelimotor_hasRearFoglights = $vehiculo["intelimotor_hasRearFoglights"];
                    $intelimotor_hasRearWindowDefogger = $vehiculo["intelimotor_hasRearWindowDefogger"];
                    $intelimotor_hasRollBar = $vehiculo["intelimotor_hasRollBar"];
                    $intelimotor_hasSideImpactAirbag = $vehiculo["intelimotor_hasSideImpactAirbag"];
                    $intelimotor_hasStabilityControl = $vehiculo["intelimotor_hasStabilityControl"];
                    $intelimotor_hasSteeringWheelControl = $vehiculo["intelimotor_hasSteeringWheelControl"];
                    $intelimotor_hasThirdStop = $vehiculo["intelimotor_hasThirdStop"];
                    $intelimotor_hasCurtainAirbag = $vehiculo["intelimotor_hasCurtainAirbag"];
                    $intelimotor_armored = $vehiculo["intelimotor_armored"];
                    $intelimotor_hasAirConditioning = $vehiculo["intelimotor_hasAirConditioning"];
                    $intelimotor_hasElectricMirrors = $vehiculo["intelimotor_hasElectricMirrors"];
                    $intelimotor_hasGps = $vehiculo["intelimotor_hasGps"];
                    $intelimotor_hasHeadlightControl = $vehiculo["intelimotor_hasHeadlightControl"];
                    $intelimotor_hasHeadrestRearSeat = $vehiculo["intelimotor_hasHeadrestRearSeat"];
                    $intelimotor_hasHeightAdjustableDriverSeat = $vehiculo["intelimotor_hasHeightAdjustableDriverSeat"];
                    $intelimotor_hasLeatherUpholstery = $vehiculo["intelimotor_hasLeatherUpholstery"];
                    $intelimotor_hasLightSensor = $vehiculo["intelimotor_hasLightSensor"];
                    $intelimotor_hasPaintedBumper = $vehiculo["intelimotor_hasPaintedBumper"];
                    $intelimotor_hasParkingSensor = $vehiculo["intelimotor_hasParkingSensor"];
                    $intelimotor_hasPowerWindows = $vehiculo["intelimotor_hasPowerWindows"];
                    $intelimotor_hasRemoteTrunkRelease = $vehiculo["intelimotor_hasRemoteTrunkRelease"];
                    $intelimotor_hasElectricSeats = $vehiculo["intelimotor_hasElectricSeats"];
                    $intelimotor_hasRearBackrest = $vehiculo["intelimotor_hasRearBackrest"];
                    $intelimotor_hasCentralPowerDoorLocks = $vehiculo["intelimotor_hasCentralPowerDoorLocks"];
                    $intelimotor_hasAmfmRadio = $vehiculo["intelimotor_hasAmfmRadio"];
                    $intelimotor_hasBluetooth = $vehiculo["intelimotor_hasBluetooth"];
                    $intelimotor_hasCdPlayer = $vehiculo["intelimotor_hasCdPlayer"];
                    $intelimotor_hasDvd = $vehiculo["intelimotor_hasDvd"];
                    $intelimotor_hasMp3Player = $vehiculo["intelimotor_hasMp3Player"];
                    $intelimotor_hasSdCard = $vehiculo["intelimotor_hasSdCard"];
                    $intelimotor_hasUsb = $vehiculo["intelimotor_hasUsb"];
                    $intelimotor_hasBullBar = $vehiculo["intelimotor_hasBullBar"];
                    $intelimotor_hasSpareTyreSupport = $vehiculo["intelimotor_hasSpareTyreSupport"];
                    $intelimotor_hasTrayCover = $vehiculo["intelimotor_hasTrayCover"];
                    $intelimotor_hasTrayMat = $vehiculo["intelimotor_hasTrayMat"];
                    $intelimotor_hasWindscreenWiper = $vehiculo["intelimotor_hasWindscreenWiper"];
                    $intelimotor_singleOwner = $vehiculo["intelimotor_singleOwner"];
                    $intelimotor_youtubeVideoUrl = $vehiculo["intelimotor_youtubeVideoUrl"];
                    $intelimotor_picture = $vehiculo["intelimotor_picture"];

                    registraEvento("CMS : Consulta de vehículo | id = " . $id);
                } else {

                    $publicado = 0;
                    $destacado = 0;
                    $certificado = 0;
                    $descuentoEspecial = 0;
                    $tipo = "";
                    $marca = "";
                    $modelo = "";
                    $version = "";
                    $ano = "";
                    $color = "";
                    $precio = "";
                    $enganche = "";
                    $litros = "";
                    $combustible = "";
                    $transmision = "";
                    $puertas = "";
                    $asientos = "";
                    $bolsasAire = "";
                    $kilometraje = "";
                    $tipoFactura = "";
                    $numeroLlaves = 1;
                    $vidaUtilLlantas = "";
                    $facturaAgencia = 0;
                    $mantenimientosAgencia = 0;
                    $impuestosCorriente = 0;
                    $mantenimientosCorriente = 0;
                    $manosLibres = 0;
                    $descripcion = "";
                    $puntosDestacados = "";
                    $imagenPrincipal = "";
                    $video_titulo = "";
                    $video_resumen = "";
                    $video_detalle = "";
                    $video_url = "";
                    $video_vistaPrevia = "";
                    $video_publicado = 0;
                    $idSucursal = "";
                    $idConcesionario = 0;

                    //Si el usuario es operador se fuerza el concesionario
                    if ($esUsuarioOperador) {
                        $idConcesionario = $usuario_idConcesionario;
                    }

                    $colorInterior = "";
                    $direccion = "";
                    $tieneAireAcondicionado = 0;
                    $tieneAlarma = 0;
                    $tieneAlfombrillaLlantaRefaccion = 0;
                    $tieneAperturaRemotaCajuela = 0;
                    $tieneAsientoConductorAjusteAltura = 0;
                    $tieneAsientosElectricos = 0;
                    $tieneAsientosTraserosAbatibles = 0;
                    $tieneAsistenciaFrenado = 0;
                    $tieneBandejaLlantaRefaccion = 0;
                    $tieneBarraAntivuelco = 0;
                    $esBlindado = 0;
                    $tieneBluetooth = 0;
                    $tieneBolsaAireConductor = 0;
                    $tieneBolsaAirePasajero = 0;
                    $tieneBolsasAireLaterales = 0;
                    $tieneBolsasAireCortina = 0;
                    $tieneCabecerasAsientosTraseros = 0;
                    $tieneComputadoraAbordo = 0;
                    $tieneControlTemperatura = 0;
                    $tieneControlEstabilidad = 0;
                    $tieneControlLucesDelanteras = 0;
                    $tieneControlVolante = 0;
                    $tieneDefensasColorCarroceria = 0;
                    $tieneDesempanadorTrasero = 0;
                    $tieneEspejosElectricos = 0;
                    $tieneFarosNiebla = 0;
                    $tieneFrenosABS = 0;
                    $tieneGps = 0;
                    $esImportado = 0;
                    $tieneInmovilizador = 0;
                    $tieneLimpiaparabrisas = 0;
                    $tieneLlantaRefaccion = 0;
                    $tieneLucesNieblaDelanteras = 0;
                    $tieneLucesNieblaTraseras = 0;
                    $tieneLucesXenon = 0;
                    $tieneParachoques = 0;
                    $tienePilotoAutomatico = 0;
                    $tienePortavasos = 0;
                    $tieneQuemacocos = 0;
                    $tieneRadioAMFM = 0;
                    $tieneRecordatorioEncendidoLuces = 0;
                    $tieneReproductorCD = 0;
                    $tieneReproductorDVD = 0;
                    $tieneReproductorMP3 = 0;
                    $tieneRespadosTraseros = 0;
                    $tieneRinesAleacion = 0;
                    $tieneSegurosElectricosCentralizados = 0;
                    $tieneSensorLluvia = 0;
                    $tieneSensoresLuz = 0;
                    $tieneSensoresReversa = 0;
                    $tieneTapiceriaPiel = 0;
                    $tieneTarjetaSD = 0;
                    $tieneTerceraLuzFrenado = 0;
                    $unicoDueno = 0;
                    $tieneUsb = 0;
                    $tieneVidriosElectricos = 0;

                    $intelimotor_id = "";
                    $intelimotor_imported = "";
                    $intelimotor_kms = "";
                    $intelimotor_listPrice = "";
                    $intelimotor_title = "";
                    $intelimotor_brand = "";
                    $intelimotor_model = "";
                    $intelimotor_year = "";
                    $intelimotor_trim = "";
                    $intelimotor_transmission = "";
                    $intelimotor_doors = "";
                    $intelimotor_fuelType = "";
                    $intelimotor_steering = "";
                    $intelimotor_tractionControl = "";
                    $intelimotor_vehicleBodyType = "";
                    $intelimotor_engine = "";
                    $intelimotor_exteriorColor = "";
                    $intelimotor_interiorColor = "";
                    $intelimotor_hasAutopilot = "";
                    $intelimotor_hasLightOnReminder = "";
                    $intelimotor_hasOnboardComputer = "";
                    $intelimotor_hasRearFoldingSeat = "";
                    $intelimotor_hasSlidingRoof = "";
                    $intelimotor_hasXenonHeadlights = "";
                    $intelimotor_hasCoasters = "";
                    $intelimotor_hasClimateControl = "";
                    $intelimotor_hasAbsBrakes = "";
                    $intelimotor_hasAlarm = "";
                    $intelimotor_hasAlloyWheels = "";
                    $intelimotor_hasDriverAirbag = "";
                    $intelimotor_hasElectronicBrakeAssist = "";
                    $intelimotor_hasEngineInmovilizer = "";
                    $intelimotor_hasFogLight = "";
                    $intelimotor_hasFrontFoglights = "";
                    $intelimotor_hasPassengerAirbag = "";
                    $intelimotor_hasRainSensor = "";
                    $intelimotor_hasRearFoglights = "";
                    $intelimotor_hasRearWindowDefogger = "";
                    $intelimotor_hasRollBar = "";
                    $intelimotor_hasSideImpactAirbag = "";
                    $intelimotor_hasStabilityControl = "";
                    $intelimotor_hasSteeringWheelControl = "";
                    $intelimotor_hasThirdStop = "";
                    $intelimotor_hasCurtainAirbag = "";
                    $intelimotor_armored = "";
                    $intelimotor_hasAirConditioning = "";
                    $intelimotor_hasElectricMirrors = "";
                    $intelimotor_hasGps = "";
                    $intelimotor_hasHeadlightControl = "";
                    $intelimotor_hasHeadrestRearSeat = "";
                    $intelimotor_hasHeightAdjustableDriverSeat = "";
                    $intelimotor_hasLeatherUpholstery = "";
                    $intelimotor_hasLightSensor = "";
                    $intelimotor_hasPaintedBumper = "";
                    $intelimotor_hasParkingSensor = "";
                    $intelimotor_hasPowerWindows = "";
                    $intelimotor_hasRemoteTrunkRelease = "";
                    $intelimotor_hasElectricSeats = "";
                    $intelimotor_hasRearBackrest = "";
                    $intelimotor_hasCentralPowerDoorLocks = "";
                    $intelimotor_hasAmfmRadio = "";
                    $intelimotor_hasBluetooth = "";
                    $intelimotor_hasCdPlayer = "";
                    $intelimotor_hasDvd = "";
                    $intelimotor_hasMp3Player = "";
                    $intelimotor_hasSdCard = "";
                    $intelimotor_hasUsb = "";
                    $intelimotor_hasBullBar = "";
                    $intelimotor_hasSpareTyreSupport = "";
                    $intelimotor_hasTrayCover = "";
                    $intelimotor_hasTrayMat = "";
                    $intelimotor_hasWindscreenWiper = "";
                    $intelimotor_singleOwner = "";
                    $intelimotor_youtubeVideoUrl = "";
                    $intelimotor_picture = "";
                }
            }


            function reArrayFiles($file) {
                $file_ary = array();
                $file_count = count($file['name']);
                $file_key = array_keys($file);

                for ($i=0; $i < $file_count; $i++) {
                    foreach ($file_key as $val) {
                        $file_ary[$i][$val] = $file[$val][$i];
                    }
                }

                return $file_ary;
            }  

            $tieneHabilitadoIntelimotor = 0;
            if ($esUsuarioOperador) {
                $concesionarioAsociadoIntelimotor = consulta($conexion, "SELECT id FROM concesionario WHERE id = " . $usuario_idConcesionario . " AND intelimotor_apiKey != '' AND intelimotor_apiSecret != ''");
                if (cuentaResultados($concesionarioAsociadoIntelimotor) > 0) {
                    $tieneHabilitadoIntelimotor = 1;
                }
            } else {
                $concesionarioAsociadoIntelimotor = consulta($conexion, "SELECT v.id FROM vehiculo v INNER JOIN concesionario c ON v.idConcesionario = c.id WHERE v.id = " . $id . " AND c.intelimotor_apiKey != '' AND c.intelimotor_apiSecret != ''");
                if (cuentaResultados($concesionarioAsociadoIntelimotor) > 0) {
                    $tieneHabilitadoIntelimotor = 1;
                }
            }
        ?>

        <!-- Preloader -->

        <div id="global-loader">
            <img alt="Cargando..." class="loader-img" src="assets/images/loader.svg" />
        </div>

        <div class="page">
            <div class="page-main">


                <!-- Menu oculto -->


                <div class="main-sidemenu" style="display: none;"> 
                    <div class="slide-left disabled" id="slide-left"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"/></svg></div>

                    <ul class="side-menu" id="contenedor_menu"></ul>

                    <div class="slide-right" id="slide-right"><svg fill="#7b8191" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg></div>
                </div>


                <!-- Contenido -->

                <?php if ($esUsuarioMaster || $usuario_permisoConsultarVehiculos ) { ?>

                    <div class="main-content app-content mt-0">
                        <div class="side-app">
                            <div class="main-container container-fluid">
                                <!-- Titulo -->


                                <div class="page-header">
                                    <h1 class="page-title">Proporciona la información del vehículo</h1>

                                    <div>
                                        <!--
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="dashboard.html">Inicio</a></li>
                                            <li class="breadcrumb-item"><a href="javascript:void(0)">Catálogos</a></li>
                                            <li class="breadcrumb-item"><a href="javascript:void(0)">Usuarios</a></li>
                                            <li aria-current="page" class="breadcrumb-item active">Detalle de usuario</li>
                                        </ol>
                                        -->
                                    </div>
                                </div>
                                <form autocomplete="off" enctype="multipart/form-data" id="formulario" method="post" >

                                    <input id="campo_esSubmit" name="esSubmit" type="hidden" value="1" />

                                    <div class="alert" id="contenedor_mensaje">
                                        <span></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5><strong>Información de control</strong></h5>

                                                    <hr />

                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_id">ID</label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" id="campo_id" name="id" readonly type="text" value="<?php echo $id; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_publicado">Publicado</label>
                                                                        <div class="col-md-8">
                                                                            <div class="material-switch">
                                                                                <input <?php echo $publicado == 1 ? "checked" : "" ?> id="campo_publicado" name="publicado" type="checkbox" />
                                                                                <label class="label-info" for="campo_publicado"></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_idConcesionario">Agencia</label>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_idConcesionario" name="idConcesionario">
                                                                                <option <?php echo estaVacio($municipio) ? "selected" : "" ?> value="">Seleccione</option>
                                                                                <?php
                                                                                    if ($esUsuarioOperador) {
                                                                                        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario WHERE id = " . $usuario_idConcesionario . " ORDER BY nombreComercial");
                                                                                    } else {
                                                                                        $concesionarios_BD = consulta($conexion, "SELECT * FROM concesionario ORDER BY nombreComercial");
                                                                                    }
                                                                                    while ($concesionario = obtenResultado($concesionarios_BD)) {
                                                                                        echo "<option " . ($idConcesionario == $concesionario["id"] ? "selected" : "") . " value='" . $concesionario["id"] . "'>" . $concesionario["nombreComercial"] . "</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Puntos destacados</strong></h5>

                                                    <hr />

                                                    <div class="col-md-12 col-sm-12 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <div class="col-md-12">
                                                                        <textarea class="form-control" id="campo_puntosDestacados" name="puntosDestacados"><?php echo $puntosDestacados; ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="<?php echo $tieneHabilitadoIntelimotor == 1 ? "oculta_campos" : ""; ?>">
                                                        <div class="col-md-6 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_precio">Precio <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control campoNumerico" id="campo_precio" name="precio" type="number" value="<?php echo $precio; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12 mb-20">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="row mb-12">
                                                                        <label class="form-label col-md-4" for="campo_enganche">Enganche <span class="txt-danger ml-10">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control campoNumerico" id="campo_enganche" name="enganche" type="number" value="<?php echo $enganche; ?>" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input name="idSucursal" type="hidden" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row <?php echo $tieneHabilitadoIntelimotor == 1 ? "oculta_campos" : ""; ?>">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Datos generales</strong></h5>

                                                    <hr />

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tipo">Tipo <span class="txt-danger ml-10">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_tipo" name="tipo">
                                                                            <option <?php echo estaVacio($tipo) ? "selected" : "" ?> value="">Seleccione</option>
                                                                            <?php
                                                                                $tipos_BD = consulta($conexion, "SELECT DISTINCT intelimotor_vehicleBodyType FROM vehiculo ORDER BY intelimotor_vehicleBodyType");

                                                                                while ($tipo_BD = obtenResultado($tipos_BD)) {
                                                                                    echo "<option " . ($tipo_BD["intelimotor_vehicleBodyType"] == $tipo ? "selected" : "") . " value='" . $tipo_BD["intelimotor_vehicleBodyType"] . "'>" . $tipo_BD["intelimotor_vehicleBodyType"] . "</option>";
                                                                                }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_marca">Marca <span class="txt-danger ml-10">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="select_marca" name="marca">
                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_modelo">Modelo <span class="txt-danger ml-10">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="select_modelo" name="modelo">
                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_ano">Año <span class="txt-danger ml-10">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="select_ano" name="ano">
                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_version">Versión <span class="txt-danger ml-10">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="select_version" name="version">
                                                                            
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="zonaCaracteristicas row <?php echo $tieneHabilitadoIntelimotor == 1 ? "oculta_campos" : ""; ?>">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Características</strong></h5>

                                                    <hr />

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_color">Color <span class="txt-danger ml-10">*</span></label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_color" name="color" type="text" value="<?php echo $color; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_transmision">Transmisión </label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_transmision" name="transmision">
                                                                            <option <?php echo estaVacio($transmision) ? "selected" : "" ?> value="">Seleccione</option>
                                                                            <?php
                                                                                $transmisiones_BD = consulta($conexion, "SELECT DISTINCT intelimotor_transmission FROM vehiculo ORDER BY intelimotor_transmission");

                                                                                while ($transmision_BD = obtenResultado($transmisiones_BD)) {
                                                                                    echo "<option " . ($transmision_BD["intelimotor_transmission"] == $transmision ? "selected" : "") . " value='" . $transmision_BD["intelimotor_transmission"] . "'>" . $transmision_BD["intelimotor_transmission"] . "</option>";
                                                                                }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_colorInterior">Color interior</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_colorInterior" name="colorInterior" type="text" value="<?php echo $colorInterior; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_direccion">Dirección </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_direccion" name="direccion" type="text" value="<?php echo $direccion; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_combustible">Combustible </label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control select2-show-search form-select" data-placeholder="Seleccione" id="campo_combustible" name="combustible">
                                                                            <option <?php echo estaVacio($transmision) ? "selected" : "" ?> value="">Seleccione</option>
                                                                            <?php
                                                                                $combustibles_BD = consulta($conexion, "SELECT DISTINCT intelimotor_fuelType FROM vehiculo ORDER BY intelimotor_fuelType");

                                                                                while ($combustible_BD = obtenResultado($combustibles_BD)) {
                                                                                    echo "<option " . ($combustible_BD["intelimotor_fuelType"] == $combustible ? "selected" : "") . " value='" . $combustible_BD["intelimotor_fuelType"] . "'>" . $combustible_BD["intelimotor_fuelType"] . "</option>";
                                                                                }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_litros">Litros </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_litros" name="litros" type="number" value="<?php echo $litros; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_puertas">Puertas </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_puertas" name="puertas" type="number" value="<?php echo $puertas; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_asientos">Asientos </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_asientos" name="asientos" type="number" value="<?php echo $asientos; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_bolsasAire">Bolsas de aire </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_bolsasAire" name="bolsasAire" type="number" value="<?php echo $bolsasAire; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_kilometraje">Kilometraje </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_kilometraje" name="kilometraje" type="number" value="<?php echo $kilometraje; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tipoFactura">Tipo de factura </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_tipoFactura" name="tipoFactura" type="text" value="<?php echo $tipoFactura; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_numeroLlaves">Número de llaves </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_numeroLlaves" name="numeroLlaves" type="number" value="<?php echo $numeroLlaves; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_vidaUtilLlantas">Vida útil de las llantas (%) </label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_vidaUtilLlantas" name="vidaUtilLlantas" type="number" value="<?php echo $vidaUtilLlantas; ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row zonaDestacados <?php echo $tieneHabilitadoIntelimotor == 1 ? "oculta_campos" : ""; ?>">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Destacados</strong></h5>

                                                    <hr />

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAireAcondicionado">Aire acondicionado</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAireAcondicionado == 1 ? "checked" : "" ?> id="campo_tieneAireAcondicionado" name="tieneAireAcondicionado" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAireAcondicionado"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAlarma">Alarma</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAlarma == 1 ? "checked" : "" ?> id="campo_tieneAlarma" name="tieneAlarma" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAlarma"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAlfombrillaLlantaRefaccion">Alfombrilla de llanta de refacción</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAlfombrillaLlantaRefaccion == 1 ? "checked" : "" ?> id="campo_tieneAlfombrillaLlantaRefaccion" name="tieneAlfombrillaLlantaRefaccion" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAlfombrillaLlantaRefaccion"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAperturaRemotaCajuela">Apertura remota de cajuela</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAperturaRemotaCajuela == 1 ? "checked" : "" ?> id="campo_tieneAperturaRemotaCajuela" name="tieneAperturaRemotaCajuela" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAperturaRemotaCajuela"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAsientoConductorAjusteAltura">Asiento de conductor con ajuste de altura</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAsientoConductorAjusteAltura == 1 ? "checked" : "" ?> id="campo_tieneAsientoConductorAjusteAltura" name="tieneAsientoConductorAjusteAltura" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAsientoConductorAjusteAltura"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAsientosElectricos">Asientos eléctricos</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAsientosElectricos == 1 ? "checked" : "" ?> id="campo_tieneAsientosElectricos" name="tieneAsientosElectricos" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAsientosElectricos"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAsientosTraserosAbatibles">Asientos traseros abatibles</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAsientosTraserosAbatibles == 1 ? "checked" : "" ?> id="campo_tieneAsientosTraserosAbatibles" name="tieneAsientosTraserosAbatibles" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAsientosTraserosAbatibles"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneAsistenciaFrenado">Asistente de frenado</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneAsistenciaFrenado == 1 ? "checked" : "" ?> id="campo_tieneAsistenciaFrenado" name="tieneAsistenciaFrenado" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneAsistenciaFrenado"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneBandejaLlantaRefaccion">Bandeja de llanta de refacción</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneBandejaLlantaRefaccion == 1 ? "checked" : "" ?> id="campo_tieneBandejaLlantaRefaccion" name="tieneBandejaLlantaRefaccion" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneBandejaLlantaRefaccion"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneBarraAntivuelco">Barras porta equipaje</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneBarraAntivuelco == 1 ? "checked" : "" ?> id="campo_tieneBarraAntivuelco" name="tieneBarraAntivuelco" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneBarraAntivuelco"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_esBlindado">Blindado</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $esBlindado == 1 ? "checked" : "" ?> id="campo_esBlindado" name="esBlindado" type="checkbox" />
                                                                            <label class="label-info" for="campo_esBlindado"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneBluetooth">Bluetooth</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneBluetooth == 1 ? "checked" : "" ?> id="campo_tieneBluetooth" name="tieneBluetooth" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneBluetooth"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneBolsaAireConductor">Bolsa de aire conductor</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneBolsaAireConductor == 1 ? "checked" : "" ?> id="campo_tieneBolsaAireConductor" name="tieneBolsaAireConductor" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneBolsaAireConductor"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneBolsaAirePasajero">Bolsa de aire pasajero</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneBolsaAirePasajero == 1 ? "checked" : "" ?> id="campo_tieneBolsaAirePasajero" name="tieneBolsaAirePasajero" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneBolsaAirePasajero"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneBolsasAireLaterales">Bolsas de aire laterales</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneBolsasAireLaterales == 1 ? "checked" : "" ?> id="campo_tieneBolsasAireLaterales" name="tieneBolsasAireLaterales" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneBolsasAireLaterales"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneBolsasAireCortina">Bolsas de aire tipo cortina</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneBolsasAireCortina == 1 ? "checked" : "" ?> id="campo_tieneBolsasAireCortina" name="tieneBolsasAireCortina" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneBolsasAireCortina"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneCabecerasAsientosTraseros">Cabeceras asientos traseros</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneCabecerasAsientosTraseros == 1 ? "checked" : "" ?> id="campo_tieneCabecerasAsientosTraseros" name="tieneCabecerasAsientosTraseros" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneCabecerasAsientosTraseros"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneComputadoraAbordo">Computadora de viaje</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneComputadoraAbordo == 1 ? "checked" : "" ?> id="campo_tieneComputadoraAbordo" name="tieneComputadoraAbordo" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneComputadoraAbordo"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneControlTemperatura">Control de temperatura</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneControlTemperatura == 1 ? "checked" : "" ?> id="campo_tieneControlTemperatura" name="tieneControlTemperatura" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneControlTemperatura"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneControlEstabilidad">Control de estabilidad</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneControlEstabilidad == 1 ? "checked" : "" ?> id="campo_tieneControlEstabilidad" name="tieneControlEstabilidad" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneControlEstabilidad"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneControlLucesDelanteras">Control de luces delanteras</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneControlLucesDelanteras == 1 ? "checked" : "" ?> id="campo_tieneControlLucesDelanteras" name="tieneControlLucesDelanteras" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneControlLucesDelanteras"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneControlVolante">Controles al volante</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneControlVolante == 1 ? "checked" : "" ?> id="campo_tieneControlVolante" name="tieneControlVolante" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneControlVolante"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneDefensasColorCarroceria">Defensas al color de la carrocería</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneDefensasColorCarroceria == 1 ? "checked" : "" ?> id="campo_tieneDefensasColorCarroceria" name="tieneDefensasColorCarroceria" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneDefensasColorCarroceria"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneDesempanadorTrasero">Desempañador trasero</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneDesempanadorTrasero == 1 ? "checked" : "" ?> id="campo_tieneDesempanadorTrasero" name="tieneDesempanadorTrasero" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneDesempanadorTrasero"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneEspejosElectricos">Espejos eléctricos</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneEspejosElectricos == 1 ? "checked" : "" ?> id="campo_tieneEspejosElectricos" name="tieneEspejosElectricos" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneEspejosElectricos"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneFarosNiebla">Faros de niebla</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneFarosNiebla == 1 ? "checked" : "" ?> id="campo_tieneFarosNiebla" name="tieneFarosNiebla" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneFarosNiebla"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneFrenosABS">Frenos ABS</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneFrenosABS == 1 ? "checked" : "" ?> id="campo_tieneFrenosABS" name="tieneFrenosABS" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneFrenosABS"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneGps">GPS</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneGps == 1 ? "checked" : "" ?> id="campo_tieneGps" name="tieneGps" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneGps"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_esImportado">Importado</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $esImportado == 1 ? "checked" : "" ?> id="campo_esImportado" name="esImportado" type="checkbox" />
                                                                            <label class="label-info" for="campo_esImportado"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneInmovilizador">Inmovilizador de motor</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneInmovilizador == 1 ? "checked" : "" ?> id="campo_tieneInmovilizador" name="tieneInmovilizador" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneInmovilizador"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneLimpiaparabrisas">Limpiaparabrisas</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneLimpiaparabrisas == 1 ? "checked" : "" ?> id="campo_tieneLimpiaparabrisas" name="tieneLimpiaparabrisas" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneLimpiaparabrisas"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneLlantaRefaccion">Llanta de refacción</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneLlantaRefaccion == 1 ? "checked" : "" ?> id="campo_tieneLlantaRefaccion" name="tieneLlantaRefaccion" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneLlantaRefaccion"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneLucesNieblaDelanteras">Luces de niebla delanteras</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneLucesNieblaDelanteras == 1 ? "checked" : "" ?> id="campo_tieneLucesNieblaDelanteras" name="tieneLucesNieblaDelanteras" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneLucesNieblaDelanteras"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneLucesNieblaTraseras">Luces de niebla traseras</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneLucesNieblaTraseras == 1 ? "checked" : "" ?> id="campo_tieneLucesNieblaTraseras" name="tieneLucesNieblaTraseras" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneLucesNieblaTraseras"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneLucesXenon">Faros de xenón</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneLucesXenon == 1 ? "checked" : "" ?> id="campo_tieneLucesXenon" name="tieneLucesXenon" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneLucesXenon"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneParachoques">Parachoques</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneParachoques == 1 ? "checked" : "" ?> id="campo_tieneParachoques" name="tieneParachoques" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneParachoques"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tienePilotoAutomatico">Piloto automático</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tienePilotoAutomatico == 1 ? "checked" : "" ?> id="campo_tienePilotoAutomatico" name="tienePilotoAutomatico" type="checkbox" />
                                                                            <label class="label-info" for="campo_tienePilotoAutomatico"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tienePortavasos">Portavasos</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tienePortavasos == 1 ? "checked" : "" ?> id="campo_tienePortavasos" name="tienePortavasos" type="checkbox" />
                                                                            <label class="label-info" for="campo_tienePortavasos"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneQuemacocos">Techo corredizo</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneQuemacocos == 1 ? "checked" : "" ?> id="campo_tieneQuemacocos" name="tieneQuemacocos" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneQuemacocos"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneRadioAMFM">Radio AM/FM</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneRadioAMFM == 1 ? "checked" : "" ?> id="campo_tieneRadioAMFM" name="tieneRadioAMFM" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneRadioAMFM"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneRecordatorioEncendidoLuces">Recordatorio de luces encendidas</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneRecordatorioEncendidoLuces == 1 ? "checked" : "" ?> id="campo_tieneRecordatorioEncendidoLuces" name="tieneRecordatorioEncendidoLuces" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneRecordatorioEncendidoLuces"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneReproductorCD">Reproductor de CD</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneReproductorCD == 1 ? "checked" : "" ?> id="campo_tieneReproductorCD" name="tieneReproductorCD" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneReproductorCD"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneReproductorDVD">Reproductor de DVD</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneReproductorDVD == 1 ? "checked" : "" ?> id="campo_tieneReproductorDVD" name="tieneReproductorDVD" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneReproductorDVD"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneReproductorMP3">Reproductor de MP3</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneReproductorMP3 == 1 ? "checked" : "" ?> id="campo_tieneReproductorMP3" name="tieneReproductorMP3" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneReproductorMP3"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneRespadosTraseros">Respaldos traseros</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneRespadosTraseros == 1 ? "checked" : "" ?> id="campo_tieneRespadosTraseros" name="tieneRespadosTraseros" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneRespadosTraseros"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneRinesAleacion">Rines de aluminio</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneRinesAleacion == 1 ? "checked" : "" ?> id="campo_tieneRinesAleacion" name="tieneRinesAleacion" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneRinesAleacion"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneSegurosElectricosCentralizados">Cerradura de puertas centralizadas</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneSegurosElectricosCentralizados == 1 ? "checked" : "" ?> id="campo_tieneSegurosElectricosCentralizados" name="tieneSegurosElectricosCentralizados" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneSegurosElectricosCentralizados"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneSensorLluvia">Sensor de lluvia</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneSensorLluvia == 1 ? "checked" : "" ?> id="campo_tieneSensorLluvia" name="tieneSensorLluvia" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneSensorLluvia"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneSensoresLuz">Sensor de luz</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneSensoresLuz == 1 ? "checked" : "" ?> id="campo_tieneSensoresLuz" name="tieneSensoresLuz" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneSensoresLuz"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneSensoresReversa">Sensor de reversa</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneSensoresReversa == 1 ? "checked" : "" ?> id="campo_tieneSensoresReversa" name="tieneSensoresReversa" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneSensoresReversa"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneTapiceriaPiel">Tapicería de piel</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneTapiceriaPiel == 1 ? "checked" : "" ?> id="campo_tieneTapiceriaPiel" name="tieneTapiceriaPiel" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneTapiceriaPiel"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneTarjetaSD">Tarjeta SD</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneTarjetaSD == 1 ? "checked" : "" ?> id="campo_tieneTarjetaSD" name="tieneTarjetaSD" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneTarjetaSD"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneTerceraLuzFrenado">Tercera luz de frenado</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneTerceraLuzFrenado == 1 ? "checked" : "" ?> id="campo_tieneTerceraLuzFrenado" name="tieneTerceraLuzFrenado" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneTerceraLuzFrenado"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_unicoDueno">Único dueño</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $unicoDueno == 1 ? "checked" : "" ?> id="campo_unicoDueno" name="unicoDueno" type="checkbox" />
                                                                            <label class="label-info" for="campo_unicoDueno"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneUsb">USB</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneUsb == 1 ? "checked" : "" ?> id="campo_tieneUsb" name="tieneUsb" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneUsb"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_tieneVidriosElectricos">Vidrios eléctricos</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $tieneVidriosElectricos == 1 ? "checked" : "" ?> id="campo_tieneVidriosElectricos" name="tieneVidriosElectricos" type="checkbox" />
                                                                            <label class="label-info" for="campo_tieneVidriosElectricos"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_facturaAgencia">Factura agencia</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $facturaAgencia == 1 ? "checked" : "" ?> id="campo_facturaAgencia" name="facturaAgencia" type="checkbox" />
                                                                            <label class="label-info" for="campo_facturaAgencia"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_impuestosCorriente">Impuestos al corriente</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $impuestosCorriente == 1 ? "checked" : "" ?> id="campo_impuestosCorriente" name="impuestosCorriente" type="checkbox" />
                                                                            <label class="label-info" for="campo_impuestosCorriente"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_manosLibres">Manos libres</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $manosLibres == 1 ? "checked" : "" ?> id="campo_manosLibres" name="manosLibres" type="checkbox" />
                                                                            <label class="label-info" for="campo_manosLibres"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_mantenimientosCorriente">Mantenimientos al corriente</label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input <?php echo $mantenimientosCorriente == 1 ? "checked" : "" ?> id="campo_mantenimientosCorriente" name="mantenimientosCorriente" type="checkbox" />
                                                                            <label class="label-info" for="campo_mantenimientosCorriente"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row <?php echo $tieneHabilitadoIntelimotor == 1 ? "oculta_campos" : ""; ?>">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Descripción</strong></h5>

                                                    <hr />

                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <div class="col-md-12">
                                                                        <textarea class="tinymce" name="descripcion"><?php echo $descripcion ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row <?php echo $tieneHabilitadoIntelimotor == 1 ? "oculta_campos" : ""; ?>">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5><strong>Multimedia</strong></h5>

                                                    <hr />

                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label mb-10">Imagen principal</label>
                                                                        <span>
                                                                            <!--
                                                                            <br />
                                                                            Se muestra en: Menú de la app
                                                                            -->
                                                                            <br />
                                                                            Tamaño preferente: 750 x 420 pixeles
                                                                            <!--
                                                                            <br />
                                                                            Formatos aceptados: .jpg, .jpeg, .png
                                                                            -->
                                                                            <br /><br />
                                                                        </span>
                                                                        <div>
                                                                            <input name="imagenPrincipal" type="file" />
                                                                            <br />
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="panel panel-success card-view">
                                                                                        <div class="panel-wrapper in">
                                                                                            <div class="panel-body">
                                                                                                <ul class="chat-list-wrap">
                                                                                                    <li class="chat-list">
                                                                                                        <div class="chat-body">
                                                                                                            <?php
                                                                                                                if (!estaVacio($imagenPrincipal)) {
                                                                                                                    echo "<div class='chat-data'>";
                                                                                                                    echo "<img class='user-img' src='" . $constante_urlVehiculos . "/" . $id . "/" . $imagenPrincipal . "' />";

                                                                                                                    echo "<div class='user-data'>";
                                                                                                                    echo "<span class='name block capitalize-font'>" . $imagenPrincipal . "</span>";
                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                    echo "<a data-lightbox='imagen' href='" . $constante_urlVehiculos . "/" . $id . "/" . $imagenPrincipal . "'>Ampliar</a>";
                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                    echo "<a download href='" . $constante_urlVehiculos . "/" . $id . "/" . $imagenPrincipal . "'>Descargar</a>";
                                                                                                                    echo "</span>";
                                                                                                                    echo "</div>";
                                                                                                                    echo "<div class='clearfix'></div>";
                                                                                                                    echo "</div>";
                                                                                                                }
                                                                                                            ?>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label mb-10">Galer&iacute;a fotogr&aacute;fica</label>
                                                                        <span>
                                                                            <br />
                                                                            Tamaño preferente: 750 x 420 pixeles
                                                                            <br />
                                                                            <!--
                                                                            Formatos aceptados: .jpg, .jpeg, .png
                                                                            <br />
                                                                            -->
                                                                            <span class="txt-danger">Carga limitada a 100 MB a la vez, aproximadamente 30 imágenes</span>
                                                                            <br /><br />
                                                                        </span>
                                                                        <div>
                                                                            <input id="campo_archivo" multiple name="imagenGaleria[]" type="file" />
                                                                            <br />
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="panel panel-success card-view">
                                                                                        <div class="panel-wrapper in">
                                                                                            <div class="panel-body">
                                                                                                <ul class="chat-list-wrap">
                                                                                                    <li class="chat-list">
                                                                                                        <div class="chat-body">
                                                                                                            <?php
                                                                                                                if (!estaVacio($id)) {
                                                                                                                    try {
                                                                                                                        $archivos = scandir($constante_rutaVehiculos . $id . "/galeria");
                                                                                                                        $indice = 1;

                                                                                                                        foreach ($archivos as $archivo) {
                                                                                                                            $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                                                                                                                            //if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                                                                                                                            if ($extension !== "") {
                                                                                                                                echo "<div class='chat-data' id='contenedor_imagen_" . $indice . "'>";
                                                                                                                                echo "<img class='user-img' src='" . $constante_urlVehiculos . "/" . $id . "/galeria/" . $archivo . "' />";

                                                                                                                                echo "<div class='user-data'>";
                                                                                                                                echo "<span class='name block capitalize-font'>" . $archivo . "</span>";
                                                                                                                                echo "<span class='time block txt-grey'>";
                                                                                                                                echo "<a data-lightbox='imagen' href='" . $constante_urlVehiculos . "/" . $id . "/galeria/" . $archivo . "'>Ampliar</a>";
                                                                                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                echo "<a download href='" . $constante_urlVehiculos . "/" . $id . "/galeria/" . $archivo . "'>Descargar</a>";
                                                                                                                                echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                                echo "<a href='javascript:eliminaImagenGaleria(" . $id . ", \"" . $archivo . "\", " . $indice . ")'>Eliminar</a>";
                                                                                                                                echo "</span>";
                                                                                                                                echo "</div>";
                                                                                                                                echo "<div class='clearfix'></div>";
                                                                                                                                echo "</div>";

                                                                                                                                $indice++;
                                                                                                                            }
                                                                                                                        }
                                                                                                                    } catch (Exception $e) {
                                                                                                                    }
                                                                                                                }
                                                                                                            ?>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>

                                                                                                <div class="alert alert-dismissable" id="contenedor_mensaje" style="display: none">
                                                                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> <span id="contenedor_mensaje_contenido"></span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <input name="video_titulo" type="hidden" value="<?php echo $video_titulo ?>" />
                                        <input name="video_publicado" type="hidden" value="<?php echo $video_publicado ?>" />
                                        <input name="video_resumen" type="hidden" value="<?php echo $video_resumen ?>" />
                                        <input name="video_url" type="hidden" value="<?php echo $video_url ?>" />
                                        <input name="video_detalle" type="hidden" value="<?php echo $video_detalle ?>" />
                                    </div>

                                    <div class="row <?php echo $tieneHabilitadoIntelimotor == 0 ? "oculta_campos" : ""; ?>">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body intelimotor zonaCaracteristicas">

                                                    <h5><strong>Intelimotor</strong></h5>

                                                    <hr />

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_id">id</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_id" readonly type="text" value="<?php echo $intelimotor_id ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_imported">imported</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_imported" readonly type="text" value="<?php echo $intelimotor_imported ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_kms">kms</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_kms" readonly type="text" value="<?php echo $intelimotor_kms ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_listPrice">listPrice</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_listPrice" readonly type="text" value="<?php echo $intelimotor_listPrice ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_title">title</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_title" readonly type="text" value="<?php echo $intelimotor_title ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_brand">brand</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_brand" readonly type="text" value="<?php echo $intelimotor_brand ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_model">model</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_model" readonly type="text" value="<?php echo $intelimotor_model ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_year">year</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_year" readonly type="text" value="<?php echo $intelimotor_year ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_trim">trim</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_trim" readonly type="text" value="<?php echo $intelimotor_trim ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_transmission">transmission</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_transmission" readonly type="text" value="<?php echo $intelimotor_transmission ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_doors">doors</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_doors" readonly type="text" value="<?php echo $intelimotor_doors ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_fuelType">fuelType</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_fuelType" readonly type="text" value="<?php echo $intelimotor_fuelType ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_steering">steering</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_steering" readonly type="text" value="<?php echo $intelimotor_steering ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_tractionControl">tractionControl</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_tractionControl" readonly type="text" value="<?php echo $intelimotor_tractionControl ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_vehicleBodyType">vehicleBodyType</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_vehicleBodyType" readonly type="text" value="<?php echo $intelimotor_vehicleBodyType ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_engine">engine</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_engine" readonly type="text" value="<?php echo $intelimotor_engine ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_exteriorColor">exteriorColor</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_exteriorColor" readonly type="text" value="<?php echo $intelimotor_exteriorColor ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_interiorColor">interiorColor</label>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" id="campo_intelimotor_interiorColor" readonly type="text" value="<?php echo $intelimotor_interiorColor ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasAirConditioning">Aire acondicionado <strong>(hasAirConditioning)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasAirConditioning == 1 ? "checked" : "" ?> id="campo_intelimotor_hasAirConditioning" name="intelimotor_hasAirConditioning" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasAirConditioning"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasAlarm">Alarma <strong>(hasAlarm)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasAlarm == 1 ? "checked" : "" ?> id="campo_intelimotor_hasAlarm" name="intelimotor_hasAlarm" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasAlarm"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasTrayMat">Alfombrilla de llanta de refacción <strong>(hasTrayMat)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasTrayMat == 1 ? "checked" : "" ?> id="campo_intelimotor_hasTrayMat" name="intelimotor_hasTrayMat" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasTrayMat"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasRemoteTrunkRelease">Apertura remota de cajuela <strong>(hasRemoteTrunkRelease)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasRemoteTrunkRelease == 1 ? "checked" : "" ?> id="campo_intelimotor_hasRemoteTrunkRelease" name="intelimotor_hasRemoteTrunkRelease" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasRemoteTrunkRelease"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasHeightAdjustableDriverSeat">Asiento de conductor con ajuste de altura <strong>(hasHeightAdjustableDriverSeat)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasHeightAdjustableDriverSeat == 1 ? "checked" : "" ?> id="campo_intelimotor_hasHeightAdjustableDriverSeat" name="intelimotor_hasHeightAdjustableDriverSeat" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasHeightAdjustableDriverSeat"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasElectricSeats">Asientos eléctricos <strong>(hasElectricSeats)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasElectricSeats == 1 ? "checked" : "" ?> id="campo_intelimotor_hasElectricSeats" name="intelimotor_hasElectricSeats" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasElectricSeats"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasRearFoldingSeat">Asientos traseros abatibles <strong>(hasRearFoldingSeat)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasRearFoldingSeat == 1 ? "checked" : "" ?> id="campo_intelimotor_hasRearFoldingSeat" name="intelimotor_hasRearFoldingSeat" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasRearFoldingSeat"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasElectronicBrakeAssist">Asistente de frenado <strong>(hasElectronicBrakeAssist)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasElectronicBrakeAssist == 1 ? "checked" : "" ?> id="campo_intelimotor_hasElectronicBrakeAssist" name="intelimotor_hasElectronicBrakeAssist" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasElectronicBrakeAssist"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasTrayCover">Bandeja de llanta de refacción <strong>(hasTrayCover)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasTrayCover == 1 ? "checked" : "" ?> id="campo_intelimotor_hasTrayCover" name="intelimotor_hasTrayCover" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasTrayCover"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasRollBar">Barras porta equipaje <strong>(hasRollBar)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasRollBar == 1 ? "checked" : "" ?> id="campo_intelimotor_hasRollBar" name="intelimotor_hasRollBar" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasRollBar"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_armored">Blindado <strong>(armored)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_armored == 1 ? "checked" : "" ?> id="campo_intelimotor_armored" name="intelimotor_armored" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_armored"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasBluetooth">Bluetooth <strong>(hasBluetooth)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasBluetooth == 1 ? "checked" : "" ?> id="campo_intelimotor_hasBluetooth" name="intelimotor_hasBluetooth" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasBluetooth"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasDriverAirbag">Bolsa de aire conductor <strong>(hasDriverAirbag)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasDriverAirbag == 1 ? "checked" : "" ?> id="campo_intelimotor_hasDriverAirbag" name="intelimotor_hasDriverAirbag" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasDriverAirbag"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasPassengerAirbag">Bolsa de aire pasajero <strong>(hasPassengerAirbag)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasPassengerAirbag == 1 ? "checked" : "" ?> id="campo_intelimotor_hasPassengerAirbag" name="intelimotor_hasPassengerAirbag" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasPassengerAirbag"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasSideImpactAirbag">Bolsas de aire laterales <strong>(hasSideImpactAirbag)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasSideImpactAirbag == 1 ? "checked" : "" ?> id="campo_intelimotor_hasSideImpactAirbag" name="intelimotor_hasSideImpactAirbag" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasSideImpactAirbag"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasCurtainAirbag">Bolsas de aire tipo cortina <strong>(hasCurtainAirbag)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasCurtainAirbag == 1 ? "checked" : "" ?> id="campo_intelimotor_hasCurtainAirbag" name="intelimotor_hasCurtainAirbag" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasCurtainAirbag"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasHeadrestRearSeat">Cabeceras asientos traseros <strong>(hasHeadrestRearSeat)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasHeadrestRearSeat == 1 ? "checked" : "" ?> id="campo_intelimotor_hasHeadrestRearSeat" name="intelimotor_hasHeadrestRearSeat" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasHeadrestRearSeat"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasOnboardComputer">Computadora de viaje <strong>(hasOnboardComputer)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasOnboardComputer == 1 ? "checked" : "" ?> id="campo_intelimotor_hasOnboardComputer" name="intelimotor_hasOnboardComputer" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasOnboardComputer"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasClimateControl">Control de temperatura <strong>(hasClimateControl)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasClimateControl == 1 ? "checked" : "" ?> id="campo_intelimotor_hasClimateControl" name="intelimotor_hasClimateControl" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasClimateControl"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasStabilityControl">Control de estabilidad <strong>(hasStabilityControl)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasStabilityControl == 1 ? "checked" : "" ?> id="campo_intelimotor_hasStabilityControl" name="intelimotor_hasStabilityControl" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasStabilityControl"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasHeadlightControl">Control de luces delanteras <strong>(hasHeadlightControl)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasHeadlightControl == 1 ? "checked" : "" ?> id="campo_intelimotor_hasHeadlightControl" name="intelimotor_hasHeadlightControl" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasHeadlightControl"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasSteeringWheelControl">Controles al volante <strong>(hasSteeringWheelControl)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasSteeringWheelControl == 1 ? "checked" : "" ?> id="campo_intelimotor_hasSteeringWheelControl" name="intelimotor_hasSteeringWheelControl" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasSteeringWheelControl"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasPaintedBumper">Defensas al color de la carrocería <strong>(hasPaintedBumper)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasPaintedBumper == 1 ? "checked" : "" ?> id="campo_intelimotor_hasPaintedBumper" name="intelimotor_hasPaintedBumper" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasPaintedBumper"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasRearWindowDefogger">Desempañador trasero <strong>(hasRearWindowDefogger)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasRearWindowDefogger == 1 ? "checked" : "" ?> id="campo_intelimotor_hasRearWindowDefogger" name="intelimotor_hasRearWindowDefogger" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasRearWindowDefogger"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasElectricMirrors">Espejos eléctricos <strong>(hasElectricMirrors)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasElectricMirrors == 1 ? "checked" : "" ?> id="campo_intelimotor_hasElectricMirrors" name="intelimotor_hasElectricMirrors" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasElectricMirrors"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasFrontFoglights">Faros de niebla <strong>(hasFrontFoglights)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasFrontFoglights == 1 ? "checked" : "" ?> id="campo_intelimotor_hasFrontFoglights" name="intelimotor_hasFrontFoglights" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasFrontFoglights"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasAbsBrakes">Frenos ABS <strong>(hasAbsBrakes)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasAbsBrakes == 1 ? "checked" : "" ?> id="campo_intelimotor_hasAbsBrakes" name="intelimotor_hasAbsBrakes" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasAbsBrakes"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasGps">GPS <strong>(hasGps)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasGps == 1 ? "checked" : "" ?> id="campo_intelimotor_hasGps" name="intelimotor_hasGps" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasGps"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasEngineInmovilizer">Inmovilizador de motor <strong>(hasEngineInmovilizer)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasEngineInmovilizer == 1 ? "checked" : "" ?> id="campo_intelimotor_hasEngineInmovilizer" name="intelimotor_hasEngineInmovilizer" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasEngineInmovilizer"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasWindscreenWiper">Limpiaparabrisas <strong>(hasWindscreenWiper)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasWindscreenWiper == 1 ? "checked" : "" ?> id="campo_intelimotor_hasWindscreenWiper" name="intelimotor_hasWindscreenWiper" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasWindscreenWiper"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasSpareTyreSupport">Llanta de refacción <strong>(hasSpareTyreSupport)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasSpareTyreSupport == 1 ? "checked" : "" ?> id="campo_intelimotor_hasSpareTyreSupport" name="intelimotor_hasSpareTyreSupport" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasSpareTyreSupport"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasFogLight">Luces de niebla delanteras <strong>(hasFogLight)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasFogLight == 1 ? "checked" : "" ?> id="campo_intelimotor_hasFogLight" name="intelimotor_hasFogLight" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasFogLight"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasRearFoglights">Luces de niebla traseras <strong>(hasRearFoglights)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasRearFoglights == 1 ? "checked" : "" ?> id="campo_intelimotor_hasRearFoglights" name="intelimotor_hasRearFoglights" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasRearFoglights"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasXenonHeadlights">Faros de xenón <strong>(hasXenonHeadlights)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasXenonHeadlights == 1 ? "checked" : "" ?> id="campo_intelimotor_hasXenonHeadlights" name="intelimotor_hasXenonHeadlights" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasXenonHeadlights"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasBullBar">Parachoques <strong>(hasBullBar)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasBullBar == 1 ? "checked" : "" ?> id="campo_intelimotor_hasBullBar" name="intelimotor_hasBullBar" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasBullBar"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasAutopilot">Piloto automático <strong>(hasAutopilot)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasAutopilot == 1 ? "checked" : "" ?> id="campo_intelimotor_hasAutopilot" name="intelimotor_hasAutopilot" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasAutopilot"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasCoasters">Portavasos <strong>(hasCoasters)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasCoasters == 1 ? "checked" : "" ?> id="campo_intelimotor_hasCoasters" name="intelimotor_hasCoasters" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasCoasters"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasSlidingRoof">Techo corredizo <strong>(hasSlidingRoof)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasSlidingRoof == 1 ? "checked" : "" ?> id="campo_intelimotor_hasSlidingRoof" name="intelimotor_hasSlidingRoof" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasSlidingRoof"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasAmfmRadio">Radio AM/FM <strong>(hasAmfmRadio)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasAmfmRadio == 1 ? "checked" : "" ?> id="campo_intelimotor_hasAmfmRadio" name="intelimotor_hasAmfmRadio" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasAmfmRadio"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasLightOnReminder">Recordatorio de luces encendidas <strong>(hasLightOnReminder)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasLightOnReminder == 1 ? "checked" : "" ?> id="campo_intelimotor_hasLightOnReminder" name="intelimotor_hasLightOnReminder" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasLightOnReminder"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasCdPlayer">Reproductor de CD <strong>(hasCdPlayer)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasCdPlayer == 1 ? "checked" : "" ?> id="campo_intelimotor_hasCdPlayer" name="intelimotor_hasCdPlayer" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasCdPlayer"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasDvd">Reproductor de DVD <strong>(hasDvd)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasDvd == 1 ? "checked" : "" ?> id="campo_intelimotor_hasDvd" name="intelimotor_hasDvd" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasDvd"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasMp3Player">Reproductor de MP3 <strong>(hasMp3Player)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasMp3Player == 1 ? "checked" : "" ?> id="campo_intelimotor_hasMp3Player" name="intelimotor_hasMp3Player" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasMp3Player"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasRearBackrest">Respaldos traseros <strong>(hasRearBackrest)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasRearBackrest == 1 ? "checked" : "" ?> id="campo_intelimotor_hasRearBackrest" name="intelimotor_hasRearBackrest" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasRearBackrest"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasAlloyWheels">Rines de aluminio <strong>(hasAlloyWheels)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasAlloyWheels == 1 ? "checked" : "" ?> id="campo_intelimotor_hasAlloyWheels" name="intelimotor_hasAlloyWheels" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasAlloyWheels"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasCentralPowerDoorLocks">Cerradura de puertas centralizada <strong>(hasCentralPowerDoorLocks)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasCentralPowerDoorLocks == 1 ? "checked" : "" ?> id="campo_intelimotor_hasCentralPowerDoorLocks" name="intelimotor_hasCentralPowerDoorLocks" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasCentralPowerDoorLocks"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasRainSensor">Sensor de lluvia <strong>(hasRainSensor)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasRainSensor == 1 ? "checked" : "" ?> id="campo_intelimotor_hasRainSensor" name="intelimotor_hasRainSensor" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasRainSensor"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasLightSensor">Sensor de luz <strong>(hasLightSensor)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasLightSensor == 1 ? "checked" : "" ?> id="campo_intelimotor_hasLightSensor" name="intelimotor_hasLightSensor" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasLightSensor"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasParkingSensor">Sensor de reversa <strong>(hasParkingSensor)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasParkingSensor == 1 ? "checked" : "" ?> id="campo_intelimotor_hasParkingSensor" name="intelimotor_hasParkingSensor" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasParkingSensor"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasLeatherUpholstery">Tapicería de piel <strong>(hasLeatherUpholstery)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasLeatherUpholstery == 1 ? "checked" : "" ?> id="campo_intelimotor_hasLeatherUpholstery" name="intelimotor_hasLeatherUpholstery" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasLeatherUpholstery"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasSdCard">Tarjeta SD <strong>(hasSdCard)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasSdCard == 1 ? "checked" : "" ?> id="campo_intelimotor_hasSdCard" name="intelimotor_hasSdCard" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasSdCard"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasThirdStop">Tercera luz de frenado <strong>(hasThirdStop)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasThirdStop == 1 ? "checked" : "" ?> id="campo_intelimotor_hasThirdStop" name="intelimotor_hasThirdStop" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasThirdStop"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_singleOwner">Único dueño <strong>(singleOwner)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_singleOwner == 1 ? "checked" : "" ?> id="campo_intelimotor_singleOwner" name="intelimotor_singleOwner" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_singleOwner"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasUsb">USB <strong>(hasUsb)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasUsb == 1 ? "checked" : "" ?> id="campo_intelimotor_hasUsb" name="intelimotor_hasUsb" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasUsb"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-20">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="row mb-12">
                                                                    <label class="form-label col-md-4" for="campo_intelimotor_hasPowerWindows">Vidrios eléctricos <strong>(hasPowerWindows)</strong></label>
                                                                    <div class="col-md-8">
                                                                        <div class="material-switch">
                                                                            <input disabled <?php echo $intelimotor_hasPowerWindows == 1 ? "checked" : "" ?> id="campo_intelimotor_hasPowerWindows" name="intelimotor_hasPowerWindows" type="checkbox" />
                                                                            <label class="label-info" for="campo_intelimotor_hasPowerWindows"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row col-12">
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label mb-10">Video Youtube</label>
                                                                        <div>
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="panel panel-success card-view">
                                                                                        <div class="panel-wrapper collapse in">
                                                                                            <div class="panel-body">
                                                                                                <ul class="chat-list-wrap">
                                                                                                    <li class="chat-list">
                                                                                                        <div class="chat-body">
                                                                                                            <?php
                                                                                                                if (!estaVacio($intelimotor_youtubeVideoUrl)) {
                                                                                                                    echo "<iframe width='560' height='315' src='" . str_replace("watch?v=", "embed/", $intelimotor_youtubeVideoUrl) . "' frameborder='0' allow='encrypted-media; gyroscope; picture-in-picture; web-share' allowfullscreen></iframe>";
                                                                                                                }
                                                                                                            ?>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label mb-10">Imágenes</label>
                                                                        <div>
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="panel panel-success card-view">
                                                                                        <div class="panel-wrapper collapse in">
                                                                                            <div class="panel-body">
                                                                                                <ul class="chat-list-wrap">
                                                                                                    <li class="chat-list">
                                                                                                        <div class="chat-body">
                                                                                                            <?php
                                                                                                                if (!estaVacio($intelimotor_picture)) {
                                                                                                                    echo "<div class='chat-data'>";
                                                                                                                    //echo "<img class='user-img' src='" . $intelimotor_picture . "' style='height: 150px; width: 150px' />";
                                                                                                                    echo "<img class='user-img' src='" . $intelimotor_picture . "' />";

                                                                                                                    echo "<div class='user-data'>";
                                                                                                                    echo "<span class='name block capitalize-font'>" . $intelimotor_picture . "</span>";
                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                    echo "<a data-lightbox='imagen' href='" . $intelimotor_picture . "'>Ampliar</a>";
                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                    echo "<a download href='" . $intelimotor_picture . "'>Descargar</a>";
                                                                                                                    echo "</span>";
                                                                                                                    echo "</div>";
                                                                                                                    echo "<div class='clearfix'></div>";
                                                                                                                    echo "</div>";
                                                                                                                }
                                                                                                            ?>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="panel panel-success card-view">
                                                                                        <div class="panel-wrapper in">
                                                                                            <div class="panel-body">
                                                                                                <ul class="chat-list-wrap">
                                                                                                    <li class="chat-list">
                                                                                                        <div class="chat-body">
                                                                                                            <?php
                                                                                                                $imagenes_BD = consulta($conexion, "SELECT * FROM imagen WHERE intelimotor_id = '" . $intelimotor_id . "'");

                                                                                                                while ($imagen = obtenResultado($imagenes_BD)) {
                                                                                                                    echo "<div class='chat-data'>";
                                                                                                                    //echo "<img class='user-img' src='" . $intelimotor_picture . "' style='height: 150px; width: 150px' />";
                                                                                                                    echo "<img class='user-img' src='" . $imagen["imagen"] . "' />";

                                                                                                                    echo "<div class='user-data'>";
                                                                                                                    echo "<span class='name block capitalize-font'>" . $imagen["imagen"] . "</span>";
                                                                                                                    echo "<span class='time block txt-grey'>";
                                                                                                                    echo "<a data-lightbox='imagen' href='" . $imagen["imagen"] . "'>Ampliar</a>";
                                                                                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp;";
                                                                                                                    echo "<a download href='" . $imagen["imagen"] . "'>Descargar</a>";
                                                                                                                    echo "</span>";
                                                                                                                    echo "</div>";
                                                                                                                    echo "<div class='clearfix'></div>";
                                                                                                                    echo "</div>";
                                                                                                                }
                                                                                                            ?>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row mt-6">
                                        <div class="col-md-8 col-sm-6" id="contenedor_botonesIzquierdos">
                                            <div class="form-group">
                                                <!--a class="btn btn-danger mb-3" href="javascript:void(0)" id="boton_eliminar">Eliminar</a-->
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6" id="contenedor_botonesDerechos">
                                            <div class="form-group">
                                                <?php if ($esUsuarioMaster || $usuario_permisoEditarVehiculos) { ?>
                                                    <button class="btn btn-success mb-3" type="submit" id="boton_guardar">Guardar</button>
                                                <?php } ?>
                                                <a class="btn btn-default mb-3" href="javascript:void(0)" id="boton_cerrar">Cerrar</a>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    } else {
                        registraEvento("CMS : Consulta de vehículo bloqueada | id = " . $id);
                        muestraBloqueo();
                    }
                ?>

            </div>
        </div>


        <?php include("socialware/php/estructura/plugins.php"); ?>

        <!-- Tinymce JavaScript -->
        <script src="vendors/bower_components/tinymce/tinymce.min.js"></script>

        <!-- Tinymce Wysuhtml5 Init JavaScript -->
        <script src="dist/js/tinymce-data.js"></script>

        <!--
         Lightbox
         http://lokeshdhakar.com/projects/lightbox2/
        -->
        <link href="socialware/js/lightbox2-master/dist/css/lightbox.min.css" rel="stylesheet">
        <script src="socialware/js/lightbox2-master/dist/js/lightbox.min.js"></script>

        <?php include("socialware/php/estructura/scripts.php"); ?>


        <!-- Scripts -->


        <script>
//var indiceFormatos= < ?php echo $indiceFormatos ?>;

            $(function() {
                var mensaje = "<?php echo $mensaje ?>";

                if (mensaje !== "") {
                    $("#contenedor_mensaje").hide();
                    $("#contenedor_mensaje").removeClass("alert-success");
                    $("#contenedor_mensaje").removeClass("alert-danger");

                    if (mensaje.startsWith("ok - ")) {
                        $("#contenedor_mensaje span").html(mensaje.substring(5));
                        $("#contenedor_mensaje").addClass("alert-success");
                        $("#contenedor_mensaje").show();
                    } else {
                        $("#contenedor_mensaje span").html(mensaje);
                        $("#contenedor_mensaje").addClass("alert-danger");
                        $("#contenedor_mensaje").show();
                    }

                    $("body").animate({ scrollTop: 0 }, 'slow');
                }

                $(".bs-switch").bootstrapSwitch({
                    handleWidth: 110,
                    labelWidth: 110
                });

                cargaMarcas();
            });

            function cargaMarcas(){
                $.ajax({
                    url: "socialware/php/ajax/cargaMarcas.php",
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var marca = '<?php echo $marca; ?>' ;

                    var json = jQuery.parseJSON(resultado);
                    $.each(json.data,function(i,nodo){
                        if(marca == nodo.name){
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        }else{
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#select_marca").append(contenido);
                    cargaModelos();
                });
            }

            function cargaModelos(){
                var idMarca = $("#select_marca option:selected").attr("data-id");
                $.ajax({
                    url: "socialware/php/ajax/cargaModelos.php?marca=" + idMarca,
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var modelo = '<?php echo $modelo; ?>' ;

                    var json = jQuery.parseJSON(resultado);
                    $.each(json.data,function(i,nodo){
                        if(modelo == nodo.name){
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        }else{
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#select_modelo").html("");
                    $("#select_ano").html("");
                    $("#select_version").html("");

                    $("#select_modelo").append(contenido);
                    cargaAnos();
                });
            }

            function cargaAnos(){
                var idMarca = $("#select_marca option:selected").attr("data-id");
                var idModelo = $("#select_modelo option:selected").attr("data-id");

                $.ajax({
                    url: "socialware/php/ajax/cargaAnos.php?marca=" + idMarca + "&modelo=" + idModelo,
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var ano = '<?php echo $ano; ?>' ;

                    var json = jQuery.parseJSON(resultado);
                    $.each(json.data,function(i,nodo){
                        if(ano == nodo.name){
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        }else{
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#select_ano").html("");
                    $("#select_version").html("");

                    $("#select_ano").append(contenido);
                    cargaVersiones();
                });
            }

            function cargaVersiones(){
                var idMarca = $("#select_marca option:selected").attr("data-id");
                var idModelo = $("#select_modelo option:selected").attr("data-id");
                var idAno = $("#select_ano option:selected").attr("data-id");

                $.ajax({
                    url: "socialware/php/ajax/cargaVersiones.php?marca=" + idMarca + "&modelo=" + idModelo + "&ano=" + idAno,
                    type: "post"
                }).done(function (resultado, textStatus, jqXHR) {
                    var contenido = "";

                    var version = '<?php echo $version; ?>' ;

                    var json = jQuery.parseJSON(resultado);
                    $.each(json.data,function(i,nodo){
                        if(version == nodo.name){
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "' selected>" + nodo.name + "</option>";
                        }else{
                            contenido += "<option value='" + nodo.name + "' data-id='" + nodo.id + "'>" + nodo.name + "</option>";
                        }
                    });

                    $("#select_version").html("");

                    $("#select_version").append(contenido);
                });
            }

            $("#select_marca").on("change", function(){
                cargaModelos();
            });

            $("#select_modelo").on("change", function(){
                cargaAnos();
            });

            $("#select_ano").on("change", function(){
                cargaVersiones();
            });

/*
            $(".link_agregarFormato").click(function() {
                indiceFormatos++;

                var linea = "";

                linea += "<tr>";
                linea += "<td></td>";
                linea += "<td><input class='form-control' id='campo_formato_etiqueta_" + indiceFormatos + "' name='campo_formato_etiqueta_'" + indiceFormatos + "' type='text' /></td>";
                linea += "<td><input class='form-control' name='campo_formato_archivo_" + indiceFormatos + "' type='file' /></td>";
                linea += "<td></td>";
                linea += "</tr>";

                $("#tabla_formatos").append(linea);
            });
*/

            $("#boton_guardar").click(function() {
/*
                var formatos = "";

                if (indiceFormatos > 0) {
                    for (var indice = 1; indice <= indiceFormatos; indice++) {
                        formatos += $("#campo_formato_etiqueta_" + indice).val();

                        if (indice < indiceFormatos) {
                            formatos += "__|__";
                        }
                    }
                }

                $("#campo_formatos").val(formatos);
*/
                $("#formulario").submit();
            });


            // Regresa a la interfaz de origen


            $(".link_origen").click(function() {
                $("#formulario_origen").submit();
            });


            // Elimina vehiculo


            $(".link_eliminar").click(function() {
                if (confirm("Al continuar se eliminará este vehículo y ya no podrá ser administrado desde el CMS, ¿desea proceder?")) {
                    $.ajax({
                        type: "post",
                        url: "socialware/php/ajax/eliminaVehiculo.php",
                        data: { id: "<?php echo $id ?>" },
                        success: function(resultado) {
                            if (resultado === "ok") {
                                location.href ="vehiculos.php";
                            }
                        }
                    });
                }
            });


            // Borra una imagen de la galeria


            function eliminaImagenGaleria(id, imagen, indice) {
                if (confirm("Al continuar se eliminará esta imagen y ya no se mostrará en la galería del vehículo, ¿desea proceder?")) {
                    $.ajax({
                        data: {
                            id: id,
                            imagen: imagen
                        },
                        type: "post",
                        url: "socialware/php/ajax/eliminaImagenGaleria.php",
                        success: function(resultado) {
                            if (resultado === "ok") {
                                $("#contenedor_imagen_" + indice).hide();
                            }
                        }
                    });
                }
            }


            document.querySelector(".campoNumerico").addEventListener("keypress", ({ key, preventDefault }) => {
                if (isNaN(parseInt(key, 10)) && !["Backspace", "Enter"].includes(key)) {
                    preventDefault();
                }
            });
        </script>
    </body>
</html>
