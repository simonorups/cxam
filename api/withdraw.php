<?php
session_start();

try {
    $SecurityKey = "Dvf054kpsdkjv05mN50z055454054K29";
    // takes raw data from the request 
    $json = file_get_contents('php://input');
    // Converts it into a PHP object 
    $data = json_decode($json, true);
    //The amount of withdraw from the balance or return to the user's balance in 
    $balance = $_SESSION['balance'] ?? 100.11;
    $responseKey = MD5("$balance:$SecurityKey");
    header('X-ApiKey:' . $responseKey);
    $withdraw = $balance;
    $Sum = ($data['Sum'] > $balance) ? $balance : $data['Sum'];
    // echo $Sum . "\n";
    $UserId   = $data['UserId'] ?? 2;
    $Currency = $data['Currency'] ?? "UGX";
    $ReservId = $data['ReservId'] ?? 2;

    $XApiKey = MD5("$Sum:$UserId:$SecurityKey:$Currency:$ReservId");
    $Sum /= 100;
    // echo $Sum . "\n";
    // echo $XApiKey;
    // var_dump($json, $data);
    // var_dump($_SERVER['HTTP_X_APIKEY'], $XApiKey);
    // var_dump($data['ReservId'], $ReservId);
    // var_dump(($data['ReservId'] == $ReservId), ($data['KeySess'] == $ReservId), ($_SERVER['HTTP_X_APIKEY'] == $XApiKey));
    
    if ($_SERVER['HTTP_X_APIKEY'] != $XApiKey) {
        $response = '{
            "ErrorId": 1,
            "ErrorDescription": "User not found or blocked, incorrect ReservId, KeySess or X-APIKEY",
            "Balance": 0.00
        }';
        echo $response;
        exit;
    }

    /**
     * If the balance of the user is enough for a Sum bid and the partner's service successfully withdraw,
     * this amount off the balance and the JSON of the form is returned.
     * do not withdraw off the amount of the reserve more than once from the user's balance
     */

    if (!isset($_SESSION['withdraw'][$ReservId])) {
        $balance = ($_SESSION['balance'] - $Sum);
        $_SESSION['balance'] = $balance;
        $_SESSION['withdraw'][$ReservId] = $Sum;
    }
    // echo  $_SESSION['withdraw'];exit;
    // print_r($_SESSION);
    $response = ["balance" => $balance];
    echo json_encode($response);
    
} catch (Exception $ex) {
    $response = '{
        "ErrorId": 10,
        "ErrorDescription": " Uncertain error.",
        "Balance": 0.00
    }';
    echo $response;
}
