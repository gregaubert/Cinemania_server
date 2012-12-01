<?php
$message      = "the test message";
$tickerText   = "ticker text message";
$contentTitle = "content title";
$contentText  = "content body";
 
$regID = isset($_REQUEST['regid']) ? $_REQUEST['regid'] :
          isset($_GET['regid']) ? $_GET['regid'] :
          isset($_POST['regid']) ? $_POST['regid'] : "";
if (!$regID){
  //die ("not a valid regid provided!");
}

$registrationIds = array($regID);
$apiKey = "AIzaSyA7tWbN7LMNmOOthCFI2TA99X8WTEGEGIA";

$response = sendNotification( 
                $apiKey, 
                $registrationIds, 
                array('message' => $message, 'tickerText' => $tickerText, 'contentTitle' => $contentTitle, "contentText" => $contentText) );
 
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
 
?>