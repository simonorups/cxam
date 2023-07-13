<?php
session_start();

try {
    $Currency = "UGX";
    $SecurityKey = "Dvf054kpsdkjv05mN50z055454054K29";
    $balance = $_SESSION['balance'] ?? 100.11;
    $responseKey = MD5("$balance:$SecurityKey");
    header('X-ApiKey:' . $responseKey);

    // takes raw data from the request 
    $json = file_get_contents('php://input');
    // Converts it into a PHP object 
    $data = json_decode($json, true);
    
    $ReservId = $data['ReservId'] ?? 2;
    $Win      = $data['Win'] ?? 10000;
    $XApiKey  = MD5("$Win:$SecurityKey:$ReservId");
    // print_r($data);
    // print_r($_SESSION);
    // var_dump(($data['ReservId'] == $ReservId), ($_SERVER['HTTP_X_APIKEY'] == $XApiKey) );
    $Win /= 100;

    if ($_SERVER['HTTP_X_APIKEY'] != $XApiKey){
        $response = '{
            "ErrorId": 1,
            "ErrorDescription": "User not found or blocked, incorrect ReservId, KeySess or X-APIKEY",
            "Balance": 0.00
        }';
        echo $response;
        exit;
    }
   
    /**
     * If the balance has not been deposited by this ReservId,
     * the Win amount deposit to the user's balance and if successful in the JSON is returned:
     */

    /**
     * If a return has been made earlier (https://YOUR_SERVICE.com/api/rollback) for the all amount of the reserve,
     *  then in this case ReservId method of the partner https://YOUR_SERVICE.com/api/deposit will not be called!
     */
    if (!isset($_SESSION['deposited'][$ReservId]) && !isset($_SESSION['rolledback'][$ReservId])) {
        $balance += $Win;
        $_SESSION['balance']  = $balance;
        $_SESSION['deposited'][$ReservId] = $Win;
    }
    // echo  $_SESSION['deposited'];exit;
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

// unset($_SESSION['deposited']);
// session_destroy();
