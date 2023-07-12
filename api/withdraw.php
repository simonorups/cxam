<?php
session_start();

try {
    $UserId = 2;
    $ReservId = 2;
    $Currency = "UGX";
    $SecurityKey = "Dvf054kpsdkjv05mN50z055454054K29";
    // takes raw data from the request 
    $json = file_get_contents('php://input');
    // Converts it into a PHP object 
    $data = json_decode($json, true);
    //The amount of withdraw from the balance or return to the user's balance in 
    $balance = $_SESSION['balance'] ?? 100.11;
    $Sum = ($data['Sum'] > $balance) ?? $balance;
    // echo $Sum . "\n";

    $XApiKey = MD5("$Sum:$UserId:$SecurityKey:$Currency:$ReservId");
    $Sum /= 100;
    // echo $Sum . "\n";
    // echo $XApiKey;
    // var_dump($json, $data);
    // var_dump($_SERVER['HTTP_X_APIKEY'], $XApiKey);
    // var_dump($data['ReservId'], $ReservId);
    // var_dump(($data['ReservId'] == $ReservId), ($data['KeySess'] == $ReservId), ($_SERVER['HTTP_X_APIKEY'] == $XApiKey));

    /**
     * If the balance of the user is enough for a Sum bid and the partner's service successfully withdraw,
     * this amount off the balance and the JSON of the form is returned.
     * do not withdraw off the amount of the reserve more than once from the user's balance
     */
    $withdraw = 0;

    if (!isset($_SESSION['withdraw'])) {
        $balance = ($_SESSION['balance'] - $Sum);
        $_SESSION['balance'] = $balance;
        $withdraw++;
        $_SESSION['withdraw'] = $withdraw;
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
