<?php 

try {

    $intelimotor_apiKey = "89e42108c0292fdab98c7725d557ac5ac7b031c7dbfd5e4a8fc957f6c576e40a";
    $intelimotor_apiSecret = "df7d14926120badf19783f88b4b453cb37b681fbcbc8717a1df0f1c6e9b5aeb5";

    $marca = $_GET["marca"];
    $modelo = $_GET["modelo"];
    $ano = $_GET["ano"];

   $curl = curl_init();
   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://app.intelimotor.com/api/brands/' . $marca . '/models/' . $modelo . '/years/' . $ano . '/trims?apiKey=' . $intelimotor_apiKey . '&apiSecret=' . $intelimotor_apiSecret . '&getAll=true/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);


curl_close($curl);
echo $response;
}catch (Exception $e) {
    echo $e->getMessage();
    echo "<br />";
}

?>