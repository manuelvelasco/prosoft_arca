<?php

    $json = '{
        "destination": "521' . $telefono . '",
        "messageText": "' . $mensaje . '"
    }';

/*
    $json = '{
        "key": "89e42108c0292fdab98c7725d557ac5ac7b031c7dbfd5e4a8fc957f6c576e40a",
        "secret": "df7d14926120badf19783f88b4b453cb37b681fbcbc8717a1df0f1c6e9b5aeb5"
    }';
*/
    $canal = curl_init();

    curl_setopt($canal, CURLOPT_URL, "https://app.intelimotor.com/api/valuations?apiKey=89e42108c0292fdab98c7725d557ac5ac7b031c7dbfd5e4a8fc957f6c576e40a&apiSecret=df7d14926120badf19783f88b4b453cb37b681fbcbc8717a1df0f1c6e9b5aeb5");
    //curl_setopt($canal, CURLOPT_USERPWD, "89e42108c0292fdab98c7725d557ac5ac7b031c7dbfd5e4a8fc957f6c576e40a:df7d14926120badf19783f88b4b453cb37b681fbcbc8717a1df0f1c6e9b5aeb5");
    curl_setopt($canal, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($canal, CURLOPT_HEADER, FALSE);
    curl_setopt($canal, CURLOPT_POST, TRUE);
    curl_setopt($canal, CURLOPT_POSTFIELDS, $json);
    curl_setopt($canal, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

    $respuesta = curl_exec($canal);

    curl_close($canal);

echo $respuesta
?>