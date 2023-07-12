<?php
session_start();

try {
    $Userid = isset($_SESSION['Userid']) ? $_SESSION['Userid'] : 2;
    $Currency = "UGX";
    $SecurityKey = "Dvf054kpsdkjv05mN50z055454054K29";
    $XApiKey = MD5("$Userid:$SecurityKey:$Currency");
    // echo $XApiKey;
    $balance = $_SESSION['balance'] ?? 100.11;
    $responseKey =  MD5("$balance:$SecurityKey");
    header('X-ApiKey:' . $responseKey);

    // takes raw data from the request 
    $json = file_get_contents('php://input');
    // Converts it into a PHP object 
    $data = json_decode($json, true);

    // print_r($data);
    // var_dump($_SERVER['HTTP_X_APIKEY'], $XApiKey);
    // var_dump(($data['UserId'] == $Userid), ($data['KeySess'] == $Userid), ($_SERVER['HTTP_X_APIKEY'] == $XApiKey) );
    /**
     * If the user is found, KeySess and ApiKey are correct, then the JSON of the form is returned:
     */
    if (($data['UserId'] == $Userid) &&
        ($data['KeySess'] == $Userid) &&
        ($_SERVER['HTTP_X_APIKEY'] == $XApiKey)
    ) {
        $response = ["balance" => $balance];
        echo json_encode($response);
    } else {
        $response = '{
            "ErrorId": 1,
            "ErrorDescription": "User not found or blocked, incorrect UserId, KeySess or X-APIKEY",
            "Balance": 0.00
        }';

        echo $response;
    }
} catch (Exception $ex) {
    $response = '{
        "ErrorId": 10,
        "ErrorDescription": " Uncertain error.",
        "Balance": 0.00
    }';
    echo $response;
}
// session_destroy();
