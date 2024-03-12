<?PHP

require "config.php";
require "GetExpressCheckoutDetails.php";

//start the session
session_start();
				
//turn php errors on
ini_set("track_errors", true);
			
//Get Information from the querystring
$TOKEN = $_GET['token'];
$payer_id = $_GET['PayerID'];

// PayPal API Credentials
$API_UserName = $USER;
$API_Password = $PWD;
$API_Signature = $SIGNATURE;

//Define the PayPal Redirect URLs.
//This is the URL that the buyer is first sent to do authorize payment with their paypal account
//change the URL depending if you are testing on the sandbox or the live PayPal site
$API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";

//Format the other parameters that were stored in the session from the previous calls	
$token 				= urlencode($TOKEN);
$paymentType 		= urlencode($_SESSION["PaymentType"]);
$currencyCodeType 	= urlencode($_SESSION["currencyCodeType"]);
$payerID 			= urlencode($payer_id);
$FinalPaymentAmt    = urlencode($_SESSION["PaymentAmount"]);

$nvpstr  = '&TOKEN=' . $token . '&PAYERID=' . $payerID . '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymentType . '&PAYMENTREQUEST_0_AMT=' . $FinalPaymentAmt . '&PAYMENTREQUEST_0_CURRENCYCODE=' . $currencyCodeType;
		
//NVPRequest for submitting to server
$nvpreq = "METHOD=DoExpressCheckoutPayment" . "&VERSION=204" . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpstr;

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
  echo $v . "</br>";
endforeach;	

//get order details
//GetExpressCheckout($TOKEN);  //remove first "//" to get order details

// Finally, destroy the session.
session_destroy();

?>	
