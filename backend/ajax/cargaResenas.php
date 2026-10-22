<?php

    // Inicializa variable de salida

    $resultado = "<resultado>";

    try {

        /*
         * Enlace para obtener el placeId
         * https://developers.google.com/places/place-id
         * 
         * https://reviewsonmywebsite.com/google-review-link?placeid=ChIJu_goKmu-YoYRVg17O3NhyGs
         */

        $apiKey = "AIzaSyCLpz5zJViQWYjFwLCzOg6AV19cQSzkG9I";
        $placeId = "ChIJdQECddS_YoYRBBCEPB3M9Ac";

        $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $placeId . "&key=" . $apiKey . "&language=es";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        if (isset($response["result"]["reviews"])) {

            // Arma resultado

            $resultado .= "<reviews>";

            $reviews = $response["result"]["reviews"];

            shuffle($reviews);

            foreach ($reviews as $review) {
                $resultado .= "<review>";
                    $resultado .= "<author_name>" . $review["author_name"] . "</author_name>";
                    $resultado .= "<author_url>" . $review["author_url"] . "</author_url>";
                    $resultado .= "<profile_photo_url>" . $review["profile_photo_url"] . "</profile_photo_url>";
                    $resultado .= "<rating>" . $review["rating"] . "</rating>";
                    $resultado .= "<relative_time_description>" . $review["relative_time_description"] . "</relative_time_description>";
                    $resultado .= "<text>" . $review["text"] . "</text>";
                $resultado .= "</review>";
            }

            $resultado .= "</reviews>";
        }
    } catch (Exception $e) {
    }

    $resultado .= "</resultado>";

    // Regresa el resultado

    echo $resultado;
?>