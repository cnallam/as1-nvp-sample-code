<?php

require "config.php";

//start the session
session_start();
				
//turn php errors on
ini_set("track_errors", true);

// PayPal API Credentials
$API_UserName = $USER;
$API_Password = $PWD;
$API_Signature = $SIGNATURE;

//Define the PayPal Redirect URLs.
//This is the URL that the buyer is first sent to do authorize payment with their paypal account
//change the URL depending if you are testing on the sandbox or the live PayPal site
$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
$PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
$returnURL = "https://rmcgovernppl-tech.com/qa/QLeap/create_sale_NVPexample/DoExpressCheckoutPayment.php";
$cancelURL = "https://rmcgovernppl-tech.com/qa/Main.html";

// Construct the parameter string that describes the SetExpressCheckout API call
$paymentAmount = $_REQUEST["PaymentAmount"];
  $_SESSION["PaymentAmount"] = $paymentAmount; //store for next API call 
$paymentType = $_REQUEST["PaymentType"];
  $_SESSION["PaymentType"] = $paymentType;		//store for next API call 
$currencyCodeType = $_REQUEST["currencyCodeType"];
  $_SESSION["currencyCodeType"] = $currencyCodeType;	// store for next API call 


$nvpstr = "&PAYMENTREQUEST_0_AMT=". $paymentAmount;
$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
$nvpstr = $nvpstr . "&RETURNURL=" . $returnURL;
$nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;


//NVPRequest for submitting to server
$nvpreq = "METHOD=SetExpressCheckout" . "&VERSION=204" . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpstr;

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
		foreach($msg as $m):
			$TOKEN = $m;
		endforeach;
	}	
	//echo $v . "</br>"; //remove first "//" to print to screen
endforeach;

// Redirect to paypal.com here
$payPalURL = $PAYPAL_URL . $TOKEN;
header("Location: ".$payPalURL);
exit;

?>
