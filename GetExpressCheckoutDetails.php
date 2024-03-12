<?php

//function get order information
function GetExpressCheckout($TOKEN) {

require "config.php";

// PayPal API Credentials
$API_UserName = $USER;
$API_Password = $PWD;
$API_Signature = $SIGNATURE;

//Define the PayPal Redirect URLs.
//This is the URL that the buyer is first sent to do authorize payment with their paypal account
//change the URL depending if you are testing on the sandbox or the live PayPal site
$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";

// Construct the parameter string that describes the GetExpressCheckoutDetails API Call
$nvpstr = "&TOKEN=" . $TOKEN;

//NVPRequest for submitting to server
$nvpreq = "METHOD=GetExpressCheckoutDetails" . "&VERSION=204" . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpstr;

//setting the curl parameters.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$API_Endpoint);
curl_setopt($ch, CURLOPT_VERBOSE, 1);

//turning off the server and peer verification(TrustManager Concept).
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1);

//setting the nvpreq as POST FIELD to curl
curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

//getting response from server
$response = curl_exec($ch);
$decodeResponse = rawurldecode($response);

//parse the response data
$var = explode('&', $decodeResponse);

foreach($var as $v):
  if (strpos($v, 'TOKEN') === 0) {
    $msg = explode('=', $v);
  }	
  echo $v . "</br>"; //remove first "//" to print to screen
endforeach;

}

?>
