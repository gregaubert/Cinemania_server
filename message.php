<?php

$response = sendNotification( 
    "AIzaSyBYnDXaLxYWU-MbHhxQn3TysXWAgyEBuOs", 
    getRegIds(), 
    array('message' => "the test message" )
);
 
echo $response;

function sendNotification( $apiKey, $registrationIdsArray, $messageData )
{   
    $headers = array("Content-Type:" . "application/json", "Authorization:" . "key=" . $apiKey);
    $data = array(
        'data' => $messageData,
        'registration_ids' => $registrationIdsArray
    );
 
   $ch = curl_init();
 
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); 
    curl_setopt( $ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send" );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
 
    $response = curl_exec($ch);
    curl_close($ch);
 
    return $response;
}

function getRegIds(){
    $regID = isset($_REQUEST['regid']) ? $_REQUEST['regid'] :
        isset($_GET['regid']) ? $_GET['regid'] :
        isset($_POST['regid']) ? $_POST['regid'] : "";

    //return array($regID);
    
    return array(
// HTC
"APA91bF3M8EUsyEp3iFcnOK1zwepzFAmzILzwEIv9yiyRLy-NVNMRkHudFT7tg8-PA-abiYFnvN56AX6OGFEY73Kv_r-H4BFifVS9BHMfBGqeZPuuqkSMq3GtCFKX4sdN8NCF0LiKWXiQJWjg8kDK_8xrGmgH93PjQ"
        );
         
}
 
?>