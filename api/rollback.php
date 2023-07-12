<?php
session_start();

try {
    $ReservId = isset($_SESSION['ReservId']) ? $_SESSION['ReservId'] : 2;
    $Currency = "UGX";
    $SecurityKey = "Dvf054kpsdkjv05mN50z055454054K29";
    $Sum = 10000 / 100;

    $XApiKey = MD5("$Sum:$SecurityKey:$ReservId");
    // echo $XApiKey;

    // takes raw data from the request 
    $json = file_get_contents('php://input');
    // Converts it into a PHP object 
    $data = json_decode($json, true);

    // print_r($data);
    // var_dump($_SERVER['HTTP_X_APIKEY'], $XApiKey);
    // var_dump(($data['ReservId'] == $ReservId), ($data['KeySess'] == $ReservId), ($_SERVER['HTTP_X_APIKEY'] == $XApiKey) );
    
    /**
     * If there is no return for this ReservId, 
     * then Sum deposit to back to the user's balance and in case of successful enrollment the JSON is returned:
     */
    $balance = $_SESSION['balance'] ?? 100.11;
    $rolledback = 0;

    if (!isset($_SESSION['rolledback'])) {
        $balance = ($_SESSION['balance'] + $Sum);
        $_SESSION['balance'] = $balance;
        $rolledback++;
        $_SESSION['rolledback'] = $rolledback;
    }

    $responseKey = MD5("$balance:$SecurityKey");
    header('X-ApiKey:' . $responseKey);

    if (($data['ReservId'] == $ReservId) &&
        ($_SERVER['HTTP_X_APIKEY'] == $XApiKey)
    ) {
        $response = ["balance" => $balance];
        echo json_encode($response);
    } else {
        $response = '{
            "ErrorId": 1,
            "ErrorDescription": "User not found or blocked, incorrect ReservId, KeySess or X-APIKEY",
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