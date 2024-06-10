<?php


/* PHP ERROR PRINTING */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
define( 'WP_DEBUG', true );
*/

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );


if(!$_GET["AccessCode"]):

$errormessage = "";
$amount= $_POST["amount"];
$name= $_POST["name"];
$lastname= $_POST["lastname"];
//$email= $_POST["email"];
$refno= $_POST["refno"];
//$state2 = $_POST["your-recipient7"];
$phoneno = $_POST["phoneno"];

$branch = $_POST["branch"];
$issuer = $_POST["issuer"];
$cardcompany = $_POST["cardcompany"];









if( (strlen($name)<1)||  (strlen($refno)<1)||(strlen($amount)<1) ){
	$errormessage .= "<p>Please fill in all the required fields.</p>";
}
//if(strlen($email)<6){$errormessage .= "<p>Please enter an email address.</p>";}
//if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {	$errormessage .= "<p>Please enter a valid email address.</p>";}
//if (($state2==false)||($state2==null)||($state2=="x")){ $errormessage .= "<p>Please select your state of residence.</p>";}

if (($branch==false)||($branch==null)||($branch=="x")){ 
	$errormessage .= "<p>Please select your branch.</p>";
}

if (($issuer==false)||($issuer==null)||($issuer=="x")){ 
	$errormessage .= "<p>Please select the card issuer.</p>";
}

if (($cardcompany==false)||($cardcompany==null)||($cardcompany=="x")){ 
	$errormessage .= "<p>Please select the credit card company.</p>";
}



if(strlen($phoneno)<4){
	$phoneno = "";
}

if (strpos($amount, '.') == false) {
    $amount=$amount.'00';
}else{
	if (substr($amount,-2,1)=="."){
		$amount = $amount.'0';
	}
	if(substr($amount,-3,1) != "."){
		$errormessage .= "<p>Please enter a valid price in the form '$10' or '10' or '10.00'</p>";
	}
	echo "<br>".substr($amount,-3,1)."<br>";
	$pos = strrpos($amount, ".");
	$amount = substr_replace($amount, "", $pos, strlen("."));
	if (strpos($amount,".")!= false){
		$errormessage .= "<p>Please enter a valid price in the form '$10' or '10' or '10.00'</p>";
	}
}
$amount=preg_replace("/[^0-9]/", "", $amount);
$amount = (float)$amount;



$surcharge=0;
if(($cardcompany!="amex")&&($cardcompany!="nocharge")&&($issuer=="au")){
$surcharge = 0.01 *(float)$amount;
}else if($cardcompany=="nocharge"){
$surcharge = 0;
}else{
$surcharge = 0.02 *(float)$amount;
}
$amount = (int)((float)$amount + (float)$surcharge);



$_POST["amount"]=$amount;



if(strlen($errormessage)>3){//if there is an error message - close booking and print it
	echo '<!DOCTYPE html> <html> <head> 	<title>error</title> </head> <body> <h3>' . $errormessage . '</h3><br /><br />   <button onclick="window.history.back();">Back</button>  </body> </html> ' ;
	exit();
}else{
	//$_SESSION['email2'] = $email;
	$_SESSION['name2'] = $name;
	$_SESSION['lastname2'] = $lastname;
	//$_SESSION['state2']=$state2;
	$_SESSION['phoneno2']=$phoneno;

$_SESSION['phoneno']=$phoneno;

$_SESSION['branch']=$branch;
$_SESSION['issuer']=$issuer;
$_SESSION['cardcompany']=$cardcompany;

}


endif;



/* ********************************* EWAY ************************************** */
/* the EWAY gateway has credit card form on the checkout-page
 * to EWAY hosted pages for the client to enter credit card data. EWAY redirects back to payment-response.
* payment-response will make booking.
*/

/*
 * Note for PCI compliance - never log the cardnumber
*/

// http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php?rq=1
function get_ip_address2(){
    foreach ( array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key ){
        if (array_key_exists($key, $_SERVER) === true){
        	error_log( 'Checking IP: ' . $key . '-' . $_SERVER[ $key ] );
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

// http://stackoverflow.com/questions/3616540/format-xml-string
function formatXmlString2($xml){
    $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
    $token      = strtok($xml, "\n");
    $result     = '';
    $pad        = 0; 
    $matches    = array();
    while ($token !== false) : 
        if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
          $indent=0;
        elseif (preg_match('/^<\/\w/', $token, $matches)) :
          $pad--;
          $indent = 0;
        elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
          $indent=1;
        else :
          $indent = 0; 
        endif;
        $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
        $result .= $line . "\n";
        $token   = strtok("\n");
        $pad    += $indent;
    endwhile; 
    return $result;
}

class EwayPaymentGateWay2 {
	// API doc: https://eway.io/api-v3/?xml#responsive-shared-page

	var $apiUrl;
	var $redirectUrl;
	var $cancelUrl;
	var $tp_payment_failed_url;

	var $cart;
	var $agentref;
	var $priceCents;
	var $depositonly;

	function __construct() {
		error_log( '=== EWAY === construct' );
		// read configs

		//sandbox
		//$this->apiUrl = 'https://api.sandbox.ewaypayments.com/soap.asmx';
		//live
		$this->apiUrl = 'https://api.ewaypayments.com/soap.asmx';

		//'https://api.ewaypayments.com/soap.asmx';
		$this->redirectUrl = 'https://www.nx.jtbtravel.com.au/wp-content/plugins/jtb-australia-cust/docs/a-eway-inbound.php';
		$this->cancelUrl = 'https://www.nx.jtbtravel.com.au/?payment=canceled';
		$this->tp_payment_failed_url = 'https://www.nx.jtbtravel.com.au/payment/failed/';
		//$cart = tpGetCart();
		// read sessions
		//if ( $_SESSION['EWAY_CART'] )			$this->cart = array();
		//if ( $_SESSION['EWAY_AGENTREF'] )		$this->agentref = 'freepayment';
		$this->cart = array();
		if(!$_GET["AccessCode"]):
		$this->agentref = $_POST["refno"];
		$this->priceCents = $_POST["amount"];
		else:
		$this->agentref = "t";
		$this->priceCents = "t";
		endif;
		//if ( $_SESSION['EWAY_PRICECENTS'] )		$this->priceCents = ;
		//if ( $_SESSION['EWAY_DEPOSITONLY'] )	$this->depositonly = $_SESSION['EWAY_DEPOSITONLY'];
		//error_log( '=== EWAY === construct end. ' 		
		//	. 'Agentref-' . $this->agentref . ', '
		//	. 'PriceCents-' . $this->priceCents . ', '
		//	. 'Depositonly-' . $this->depositonly );
	}

	function clearSession() {
		//unset( $_SESSION['EWAY_CART'] );
		//unset( $_SESSION['EWAY_AGENTREF'] );
		//unset( $_SESSION['EWAY_DEPOSITONLY'] );
		//unset( $_SESSION['EWAY_PRICECENTS'] );
	}

	public function start( $cart, $agentref, $priceCents, $depositonly ) {
		//$this->cart = $cart;

		if(!$_GET["AccessCode"]):
		$this->agentref = $_POST["refno"];
		$this->priceCents = $_POST["amount"];
		else:
		$this->agentref = "t";
		$this->priceCents = "t";
		endif;
		//$this->depositonly = $depositonly;
		//$_SESSION['EWAY_CART'] = $cart;
		//$_SESSION['EWAY_AGENTREF'] = $agentref;
		//$_SESSION['EWAY_PRICECENTS'] = $priceCents;
		//$_SESSION['EWAY_DEPOSITONLY'] = $depositonly;
		error_log( '=== EWAY === start ' 		
			. 'Agentref-' . $this->agentref . ', '
			. 'PriceCents-' . $this->priceCents . ', '
			. 'Depositonly-' . $this->depositonly );
		// start
		$this->redirectToSharedPaymentUrl();
	}

	public function processingRedirect() {
		// Get AccessCode
		$AccessCode = $_GET["AccessCode"];
		// Error handel
		if ( empty( $AccessCode ) ){
			error_log( '=== EWAY === ' . 'Error: ' . 'No AccessCode attached' );
			$this->clearSession();
			wp_redirect( $this->cancelUrl );
			exit;
		}
		// Send GetAccessCodeResult request
		$data = '<AccessCode>'			. $AccessCode 			. '</AccessCode>';
		$function = 'GetAccessCodeResult';
		$response = $this->generateAndSubmitRequest( $function, $data );
		// Processing response
		$responseMessage = tpXmlExtractSingle( $response, 'ResponseMessage' );
		error_log( '=== EWAY === ' . 'GetAccessCodeResult: ' . $responseMessage . ' - ' . $this->getEwayErrorCode( $responseMessage ) );
		$isApproved = preg_match('/A2/',$responseMessage);
		if ( $isApproved ) {
			$isBookingSuccess = $this->makeBookingAndCompletePayment( $response );
		}
		else {
			// Payment failed - but customer may try again with same cart
			$errorMesssage = $this->ewayErrorString( $responseMessage );
			error_log('payment failed: ' . $errorMesssage );
			error_log('Redirecting to: ' . $this->tp_payment_failed_url . '/?error=Payment Completion Failed: ' . $errorMesssage );
			$this->clearSession();
			wp_redirect( $this->tp_payment_failed_url . '/?error=Payment failed: ' . $errorMesssage );
			exit;
		}
	}

	private function makeBookingAndCompletePayment( $response ) {
		// ### Payment was Authorised  so make booking
		// Add extra stuff to cart 
		$name = "";
		$lastname="";
		if(isset($_SESSION['name2'])) {
			$name = $_SESSION['name2'];
		}if(isset($_SESSION['lastname2'])) {
			$lastname= $_SESSION['lastname2'];
		} 
		//<InvoiceReference>syrt123123
		//<TotalAmount>2100
		//<Value>IPaddr 119.17.50.226

		preg_match_all('{<InvoiceReference.*?</}',$response,$invoice2);
		preg_match_all('{<TotalAmount.*?</}',$response,$amnt2);
		preg_match_all('{<Value>IPaddr.*?</}',$response,$ip2);


		preg_match_all('{<TransactionStatus.*?</}',$response,$status2);
		preg_match_all('{<ResponseMessage.*?</}',$response,$statusmessage2);
		preg_match_all('{<ResponseCode.*?</}',$response,$code2);
		preg_match_all('{<TransactionID.*?</}',$response,$ewayref2);

/*
A2000	Transaction Approved	Successful*
A2008	Honour With Identification	Successful
A2010	Approved For Partial Amount	Successful
A2011	Approved, VIP	Successful
A2016	Approved, Update Track 3	Successful
*/
		//$status2 $code2 $ewayref2 statusmessage2
		

		$status = $status2[0][0];
		$status = str_replace("<TransactionStatus>", "", str_replace("</", "", $status));
		$statusmessage = $statusmessage2[0][0];
		$statusmessage = str_replace("<ResponseMessage>", "", str_replace("</", "", $statusmessage));
		$code = $code2[0][0];
		$code = str_replace("<ResponseCode>", "", str_replace("</", "", $code));
		$ewayref = $ewayref2[0][0];
		$ewayref = str_replace("<TransactionID>", "", str_replace("</", "", $ewayref));

		//if ($status == "false" || $status == false){
			//wp_redirect("https://www.nx.jtbtravel.com.au/payment__trashed/failed/?error=payment_failed_AUST_cards_only_" . $code ."_".$statusmessage);
			//exit;
			
		//}
 $code = $code . " " . $statusmessage;
		if (strpos($statusmessage, 'A2010') !== false) {
		    $ewayref = $ewayref . " - Approved For Partial Amount ";
		}

		$invoice = $invoice2[0][0];
		$invoice = str_replace("<InvoiceReference>", "", str_replace("</", "", $invoice));
		$amnt = $amnt2[0][0];
		$ip = $ip2[0][0];
		$ip = str_replace("<Value>", "", str_replace("</", "", $ip));
		$amnt = intval(preg_replace('/[^0-9]+/', '', $amnt), 10);
		$amnt = substr($amnt,0,strlen($amnt)-2) . "." . substr($amnt,-2);
		$_SESSION['amnt2']=$amnt;
		$jtbemail="webmaster.au@jtbap.com"; 


//@@@EMAILS@@@
if(isset($_SESSION['branch'])) {
	if($_SESSION['branch']=="syd"){
		$jtbemail="sydcs.au@jtbap.com";
	}else if($_SESSION['branch']=="mel"){
		$jtbemail="melcs.au@jtbap.com";
	}else if($_SESSION['branch']=="ool"){
		$jtbemail="pcentre.au@jtbap.com";
	}else if($_SESSION['branch']=="cns"){
		$jtbemail="cnscs.au@jtbap.com";
	}else{
		$jtbemail="webmaster.au@jtbap.com";
	}
}


		/*
		if(isset($_SESSION['state2'])) {
			if(($_SESSION['state2']=="VIC")||($_SESSION['state2']=="TravelAgents")){
				$jtbemail="melres.au@jtbap.com";
			}else if($_SESSION['state2']=="x"){
				$jtbemail="webmaster.au@jtbap.com";
			}else{
				$jtbemail="sydres.au@jtbap.com";
			}
		}
		*/
		 


add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
wp_mail( $jtbemail,"Inbound Payment Received - ".$lastname . " - " .$invoice, "<img src=\"https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/admin-section.png\" style=\"position:relative;float:right;display:inline-block;width:100px;height:auto;\" />An offline payment has been received through eWay.<br /><br /><b>Transaction ID:</b> ".$ewayref."<br /><br /><b>Customer Name:</b> ".$name ." ".$lastname."<br /><b>Amount:</b> $".$amnt."<br /><b>Invoice Reference:</b> ".$invoice."<br /><br /><b>Customer Email:</b> ".$email2 ."<br /><b>Contact number:</b> ".$_SESSION['phoneno2'] ."<br /><b>Branch:</b> ".$_SESSION['branch'] ."<br /><b>Issuer:</b> ".$_SESSION['issuer'] ."<br /><b>Card Company:</b> ".$_SESSION['cardcompany']."<br /><b>Error/ Success Code:</b> ". $code  , "From: JTB Australia <mailer@nx.jtbtravel.com.au>\r\nReply-To: ". $email2 ."\r\nCc: webmaster.au@jtbap.com" );


//wp_mail( "accounts.au@jtbap.com","JTB Australia Payment - ".$lastname . " - " .$invoice,"<img src=\"https://www.nx.jtbtravel.com.au/wp-content/themes/jtb-australia/images/logo.png\" style=\"position:relative;float:right;display:inline-block;width:100px;height:auto;\" />New offline payment - nx.jtbtravel.com.au<br /><br /><b>Transaction ID:</b> ".$ewayref."<br /><br /><b>Customer Name:</b> ".$name ." ".$lastname."<br /><b>Amount:</b> $".$amnt."<br /><b>Invoice Reference:</b> ".$invoice."<br /><br /><b>Customer Email:</b> ".$email2 ."<br /><b>Contact number:</b> ".$_SESSION['phoneno2']  , "From: JTB Australia <sydres@nx.jtbtravel.com.au>\r\nReply-To: ".$jtbemail  );
		//$bookingresp = tpMakeBooking($this->cart , $this->agentref);
		//$status = tpBookingRetailStatus($bookingresp);
		remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
		wp_redirect('https://www.nx.jtbtravel.com.au/payment/sucess/');

		exit;
	}

	private function completePayment( $paymentAuthResponse) {
		// Send COMP request
		error_log( '=== EWAY === ' . 'Send complete request');
		$TransactionID = tpXmlExtractSingle( $paymentAuthResponse, 'TransactionID' );
		$data = ''
				. '<TransactionID>'		. $TransactionID							. '</TransactionID>'
				. '<TotalAmount>'		. $this->priceCents							. '</TotalAmount>'
				. '<CurrencyCode>'		. get_option('tp_eway_currency')			. '</CurrencyCode>'
				. '<InvoiceNumber>' 		. tpXmlEscape( $this->agentref , 50) 	. '</InvoiceNumber>'
	    		. '<InvoiceDescription>OfflineBooking</InvoiceDescription>'
				. '<InvoiceReference>' 		. tpXmlEscape(session_id(), 50) 		. '</InvoiceReference>';
		$function = 'CapturePayment';
		$response = $this->generateAndSubmitRequest( $function, $data );
		// Analyise response
		$isSuccess = tpXmlExtractSingle( $response, 'TransactionStatus' );
		$Errors = tpXmlExtractSingle( $response, 'Errors' );
		$ResponseMessage = tpXmlExtractSingle( $response, 'ResponseMessage' );
		if ( $isSuccess !== 'true' )
		{
			//ERROR settling - send email
			$errorMesssage = $this->ewayErrorString( $responseMessage );
			error_log('payment completion failed for bookingref: ' . tpBookingRef() . '-' . $errorMesssage );
			tpSetCart('{}');
			$this->clearSession();
			wp_redirect($this->tp_payment_failed_url . '/?error=Payment Completion Failed: ' . $errorMesssage );
			exit;
		}
	}

	private function redirectToSharedPaymentUrl() {
	    $data = '';
        $data = $data . '<LogoUrl>https://www.jtbaustralia.com.au/wp-content/uploads/2017/08/admin-section.png</LogoUrl>' 	;
        $data = $data  . '<CustomView>BootstrapCerulean</CustomView>' ;

        $data = $data
	    		// Eway will check IP with country for security. Internal IP is always sent blank. 
	    		// If the validation failed, we will get a Invalid Customer IP response.
	    		. '<CustomerIP>' 		. get_ip_address2()					. '</CustomerIP>'
	    		. '<CustomerReadOnly>true</CustomerReadOnly>'
		    	. '<Method>ProcessPayment</Method>'
		    	. '<TransactionType>Purchase</TransactionType>'
		    	. '<RedirectUrl>'		. $this->redirectUrl				. '</RedirectUrl>'
		    	. '<CancelUrl>'			. $this->cancelUrl					. '</CancelUrl>'
		    	. '<Customer>'
		    	//  . '<TokenCustomerID>'		. get_option('tp_eway_customer')					. '</TokenCustomerID>'
					. '<Reference>' 			. $_POST["refno"]				. '</Reference>'
		    		. '<Title> </Title>'
		    		. '<FirstName>'				.$_POST["name"] 		. '</FirstName>'
		    		. '<LastName>'				.$_POST["lastname"]  . '</LastName>'
		    		. '<Email></Email>'
		    		. '<Street1></Street1>'
		    		. '<Street2></Street2>'
		    		. '<City></City>'
		    		. '<State></State>'
		    		. '<PostalCode></PostalCode>'
		    		. '<Country></Country>'
		    		. '<Phone>'				.$_POST["phoneno"]  . '</Phone>'
		    	. '</Customer>'
		    	. '<Payment>'
		    		. '<TotalAmount>'			. $_POST["amount"]  . '</TotalAmount>'
		    		. '<CurrencyCode>AUD</CurrencyCode>'
					. '<InvoiceNumber>' 		. $_POST["refno"] . '</InvoiceNumber>'
		    		. '<InvoiceDescription>InboundEWAY-' .date("Y-m-d")  ."-" . $_POST["refno"]
					. '</InvoiceDescription>'
					. '<InvoiceReference>' 		. $_POST["refno"]		. '</InvoiceReference>'
		    	. '</Payment>'
		    	. '<Options>'
		    		. '<Option>'
		    			. '<Value>ServiceLineCount 1</Value>'
		    		. '</Option>'
		    		. '<Option>'
		    			. '<Value>'				. 'TravelDate N/A</Value>'
		    		. '</Option>'
		    		. '<Option>'
		    			. '<Value>'				. 'IPaddr ' . get_ip_address2()												. '</Value>'
		    		. '</Option>'
		    	. '</Options>';

	    $function = 'CreateAccessCodeShared';
		$response = $this->generateAndSubmitRequest( $function, $data );

		if ( $response ) {
			// Redirect
			$SharedPaymentUrl = tpXmlExtractSingle( $response, 'SharedPaymentUrl' );
			wp_redirect( $SharedPaymentUrl );
			exit;	// If we don't call 'exit' in here, it will cause a session loss in PHP 5.3
		}
	}

	private function ewayErrorString( $errors ) {
		$errorArray = explode( ', ', $errors );
		$errorMessage = '';
		foreach( $errorArray as $err ) {
			$errorMessage = $errorMessage . $err . ' - ' . $this->getEwayErrorCode( $err ) . ', ';
		} 
		return $errorMessage;
	}

	private function generateAndSubmitRequest( $function, $data ) {
		// Generate request
	    $req = $this->generateSoapRequest( $function, $data );
		error_log( '=== EWAY === ' . formatXmlString2( $req ) );

		// Submit request
		$response = $this->submitRequest( $this->apiUrl, $req, $this->getAuthDetail() );
		error_log( '=== EWAY === ' . formatXmlString2( $response ) );

		// Error handling
		if ( empty( $response ) ) {
			$errorMesssage = 'No response detected. Eway API username and password may not match.';
			error_log( '=== EWAY === ' . 'Error: ' . $errorMesssage );
			wp_redirect($this->cancelUrl . '/?error=Payment Auth Failed: ' . $errorMesssage );
			exit;
		}

		$Errors = tpXmlExtractSingle( $response, 'Errors' );
		if ( isset( $Errors ) ) {
			$errorMesssage = '' . $Errors . ',' . $this->getEwayErrorCode( $Errors );
			error_log( '=== EWAY === ' . 'Error: ' . $errorMesssage );
			wp_redirect($this->cancelUrl . '/?error=Payment Auth Failed: ' . $errorMesssage );

			exit;
		}

		// Success
		return $response;
	}

	private function generateSoapRequest( $function, $data ) {
		$request = '<?xml version="1.0" encoding="utf-8"?>
					<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
					    <soap:Body>
					        <' . $function . ' xmlns="https://api.ewaypayments.com/">
					            <request>
					                ' . $data . '
					            </request>
					        </' . $function . '>
					    </soap:Body>
					</soap:Envelope>';
		return $request;
	}

	private function getAuthDetail() //@@@ API KEY
	{



//@@@API@@@

if($_POST["branch"]=="syd"){
//LIVE 
	    //60CF3AeW4cRergNQmgkhwrZuQqox5t4v7z1GyDSdwuRvL2QV8mySgNszSGkVhdlg9YRayP
return '60CF3AeW4cRergNQmgkhwrZuQqox5t4v7z1GyDSdwuRvL2QV8mySgNszSGkVhdlg9YRayP:lcOZb1uA';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';
}else if($_POST["branch"]=="mel"){
//LIVE 
return 'C3AB9AbBFI88jU1H+8gF2NgDm367lseklO0jRAMZllky51uKcasrTBlcdR0+RFQxEfC6Gz:Jvn8WVuT';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';
}else if($_POST["branch"]=="ool"){
//LIVE - LIVE
return 'A1001AqSx1n3lihXWEM+vajTIfykfiuL2f9DM0Ebey1To5gEALbRVjHCr+stCedx9SinV6:A4Vah2Sw';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';
}else if($_POST["branch"]=="cns"){
//LIVE - LIVE
return 'A1001A88eMb6vkm1IBw9aYhYi2QzKFyOxMqe682miRT6v1yE4s7Rduw2l0NCHO/jZYLZzv:q4gYEeYt';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';
}else if($_SESSION['branch']=="syd"){

//LIVE 
	    //60CF3AeW4cRergNQmgkhwrZuQqox5t4v7z1GyDSdwuRvL2QV8mySgNszSGkVhdlg9YRayP
return '60CF3AeW4cRergNQmgkhwrZuQqox5t4v7z1GyDSdwuRvL2QV8mySgNszSGkVhdlg9YRayP:lcOZb1uA';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';

}else if($_SESSION['branch']=="mel"){

//LIVE 
return 'C3AB9AbBFI88jU1H+8gF2NgDm367lseklO0jRAMZllky51uKcasrTBlcdR0+RFQxEfC6Gz:Jvn8WVuT';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';


}else if($_SESSION['branch']=="ool"){

//LIVE - LIVE
return 'A1001AqSx1n3lihXWEM+vajTIfykfiuL2f9DM0Ebey1To5gEALbRVjHCr+stCedx9SinV6:A4Vah2Sw';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';


}else if($_SESSION['branch']=="cns"){

//LIVE - LIVE
return 'A1001A88eMb6vkm1IBw9aYhYi2QzKFyOxMqe682miRT6v1yE4s7Rduw2l0NCHO/jZYLZzv:q4gYEeYt';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';

}else{
	//LIVE - default to sydney
return '60CF3AeW4cRergNQmgkhwrZuQqox5t4v7z1GyDSdwuRvL2QV8mySgNszSGkVhdlg9YRayP:B6vz570z';
//SANDBOX:
//return 'C3AB9C7vp+UKQ9z1BkHZUe3lC3vR31dVOhKKgnSw8m2+2Bt2kHml1crwj8Wz7JqoUqifNz:x1FqzcV2';
}







	}

	private function submitRequest($url, $data, $auth)
	{
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:text/xml; charset=utf-8'));
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
	    curl_setopt($curl, CURLOPT_TIMEOUT,        20);
		if (isset($auth))
		{
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $auth);
	    }
		$resp = curl_exec($curl);
	    curl_close($curl);
		return $resp;
	}

	private function getEwayErrorCode( $index ) {
		$table = array( 
			'A2000' => 'Transaction Approved',
			'A2008' => 'Honour With Identification',
			'A2010' => 'Approved For Partial Amount',
			'A2011' => 'Approved, VIP',
			'A2016' => 'Approved, Update Track 3',
			'D4401' => 'Refer to Issuer',
			'D4402' => 'Refer to Issuer, special',
			'D4403' => 'No Merchant',
			'D4404' => 'Pick Up Card',
			'D4405' => 'Do Not Honour',
			'D4406' => 'Error',
			'D4407' => 'Pick Up Card, Special',
			'D4409' => 'Request In Progress',
			'D4412' => 'Invalid Transaction',
			'D4413' => 'Invalid Amount',
			'D4414' => 'Invalid Card Number',
			'D4415' => 'No Issuer',
			'D4419' => 'Re-enter Last Transaction',
			'D4421' => 'No Action Taken',
			'D4422' => 'Suspected Malfunction',
			'D4423' => 'Unacceptable Transaction Fee',
			'D4425' => 'Unable to Locate Record On File',
			'D4430' => 'Format Error',
			'D4431' => 'Bank Not Supported By Switch',
			'D4433' => 'Expired Card, Capture',
			'D4434' => 'Suspected Fraud, Retain Card',
			'D4435' => 'Card Acceptor, Contact Acquirer, Retain Card',
			'D4436' => 'Restricted Card, Retain Card',
			'D4437' => 'Contact Acquirer Security Department, Retain Card',
			'D4438' => 'PIN Tries Exceeded, Capture',
			'D4439' => 'No Credit Account',
			'D4440' => 'Function Not Supported',
			'D4441' => 'Lost Card',
			'D4442' => 'No Universal Account',
			'D4443' => 'Stolen Card',
			'D4444' => 'No Investment Account',
			'D4450' => 'Visa Checkout Transaction Error',
			'D4451' => 'Insufficient Funds',
			'D4452' => 'No Cheque Account',
			'D4453' => 'No Savings Account',
			'D4454' => 'Expired Card',
			'D4455' => 'Incorrect PIN',
			'D4456' => 'No Card Record',
			'D4457' => 'Function Not Permitted to Cardholder',
			'D4458' => 'Function Not Permitted to Terminal',
			'D4459' => 'Suspected Fraud',
			'D4460' => 'Acceptor Contact Acquirer',
			'D4461' => 'Exceeds Withdrawal Limit',
			'D4462' => 'Restricted Card',
			'D4463' => 'Security Violation',
			'D4464' => 'Original Amount Incorrect',
			'D4466' => 'Acceptor Contact Acquirer, Security',
			'D4467' => 'Capture Card',
			'D4475' => 'PIN Tries Exceeded',
			'D4482' => 'CVV Validation Error',
			'D4490' => 'Cut off In Progress',
			'D4491' => 'Card Issuer Unavailable',
			'D4492' => 'Unable To Route Transaction',
			'D4493' => 'Cannot Complete, Violation Of The Law',
			'D4494' => 'Duplicate Transaction',
			'D4495' => 'Amex Declined',
			'D4496' => 'System Error',
			'D4497' => 'MasterPass Error',
			'D4498' => 'PayPal Create Transaction Error',
			'D4499' => 'Invalid Transaction for Auth/Void',
			'F7000' => 'Undefined Fraud Error',
			'F7001' => 'Challenged Fraud',
			'F7002' => 'Country Match Fraud',
			'F7003' => 'High Risk Country Fraud',
			'F7004' => 'Anonymous Proxy Fraud',
			'F7005' => 'Transparent Proxy Fraud',
			'F7006' => 'Free Email Fraud',
			'F7007' => 'International Transaction Fraud',
			'F7008' => 'Risk Score Fraud',
			'F7009' => 'Denied Fraud',
			'F7010' => 'Denied by PayPal Fraud Rules',
			'F9001' => 'Custom Fraud Rule',
			'F9010' => 'High Risk Billing Country',
			'F9011' => 'High Risk Credit Card Country',
			'F9012' => 'High Risk Customer IP Address',
			'F9013' => 'High Risk Email Address',
			'F9014' => 'High Risk Shipping Country',
			'F9015' => 'Multiple card numbers for single email address',
			'F9016' => 'Multiple card numbers for single location',
			'F9017' => 'Multiple email addresses for single card number',
			'F9018' => 'Multiple email addresses for single location',
			'F9019' => 'Multiple locations for single card number',
			'F9020' => 'Multiple locations for single email address',
			'F9021' => 'Suspicious Customer First Name',
			'F9022' => 'Suspicious Customer Last Name',
			'F9023' => 'Transaction Declined',
			'F9024' => 'Multiple transactions for same address with known credit card',
			'F9025' => 'Multiple transactions for same address with new credit card',
			'F9026' => 'Multiple transactions for same email with new credit card',
			'F9027' => 'Multiple transactions for same email with known credit card',
			'F9028' => 'Multiple transactions for new credit card',
			'F9029' => 'Multiple transactions for known credit card',
			'F9030' => 'Multiple transactions for same email address',
			'F9031' => 'Multiple transactions for same credit card',
			'F9032' => 'Invalid Customer Last Name',
			'F9033' => 'Invalid Billing Street',
			'F9034' => 'Invalid Shipping Street',
			'F9037' => 'Suspicious Customer Email Address',
			'F9050' => 'High Risk Email Address and amount',
			'F9113' => 'Card issuing country differs from IP address country',
			'S5000' => 'System Error',
			'S5011' => 'PayPal Connection Error',
			'S5012' => 'PayPal Settings Error',
			'S5085' => 'Started 3dSecure',
			'S5086' => 'Routed 3dSecure',
			'S5087' => 'Completed 3dSecure',
			'S5088' => 'PayPal Transaction Created',
			'S5099' => 'Incomplete (Access Code in progress/incomplete)',
			'S5010' => 'Unknown error returned by gateway',
			'V6000' => 'Validation error',
			'V6001' => 'Invalid CustomerIP',
			'V6002' => 'Invalid DeviceID',
			'V6003' => 'Invalid Request PartnerID',
			'V6004' => 'Invalid Request Method',
			'V6010' => 'Invalid TransactionType, account not certified for eCome only MOTO or Recurring available',
			'V6011' => 'Invalid Payment TotalAmount',
			'V6012' => 'Invalid Payment InvoiceDescription',
			'V6013' => 'Invalid Payment InvoiceNumber',
			'V6014' => 'Invalid Payment InvoiceReference',
			'V6015' => 'Invalid Payment CurrencyCode',
			'V6016' => 'Payment Required',
			'V6017' => 'Payment CurrencyCode Required',
			'V6018' => 'Unknown Payment CurrencyCode',
			'V6021' => 'Cardholder Name Required',
			'V6022' => 'Card Number Required',
			'V6023' => 'Card CVN Required',
			'V6033' => 'Invalid Expiry Date',
			'V6034' => 'Invalid Issue Number',
			'V6035' => 'Invalid Valid From Date',
			'V6040' => 'Invalid TokenCustomerID',
			'V6041' => 'Customer Required',
			'V6042' => 'Customer FirstName Required',
			'V6043' => 'Customer LastName Required',
			'V6044' => 'Customer CountryCode Required',
			'V6045' => 'Customer Title Required',
			'V6046' => 'TokenCustomerID Required',
			'V6047' => 'RedirectURL Required',
			'V6048' => 'CheckoutURL Required when CheckoutPayment specified',
			'V6049' => 'Invalid Checkout URL',
			'V6051' => 'Invalid Customer FirstName',
			'V6052' => 'Invalid Customer LastName',
			'V6053' => 'Invalid Customer CountryCode',
			'V6058' => 'Invalid Customer Title',
			'V6059' => 'Invalid RedirectURL',
			'V6060' => 'Invalid TokenCustomerID',
			'V6061' => 'Invalid Customer Reference',
			'V6062' => 'Invalid Customer CompanyName',
			'V6063' => 'Invalid Customer JobDescription',
			'V6064' => 'Invalid Customer Street1',
			'V6065' => 'Invalid Customer Street2',
			'V6066' => 'Invalid Customer City',
			'V6067' => 'Invalid Customer State',
			'V6068' => 'Invalid Customer PostalCode',
			'V6069' => 'Invalid Customer Email',
			'V6070' => 'Invalid Customer Phone',
			'V6071' => 'Invalid Customer Mobile',
			'V6072' => 'Invalid Customer Comments',
			'V6073' => 'Invalid Customer Fax',
			'V6074' => 'Invalid Customer URL',
			'V6075' => 'Invalid ShippingAddress FirstName',
			'V6076' => 'Invalid ShippingAddress LastName',
			'V6077' => 'Invalid ShippingAddress Street1',
			'V6078' => 'Invalid ShippingAddress Street2',
			'V6079' => 'Invalid ShippingAddress City',
			'V6080' => 'Invalid ShippingAddress State',
			'V6081' => 'Invalid ShippingAddress PostalCode',
			'V6082' => 'Invalid ShippingAddress Email',
			'V6083' => 'Invalid ShippingAddress Phone',
			'V6084' => 'Invalid ShippingAddress Country',
			'V6085' => 'Invalid ShippingAddress ShippingMethod',
			'V6086' => 'Invalid ShippingAddress Fax',
			'V6091' => 'Unknown Customer CountryCode',
			'V6092' => 'Unknown ShippingAddress CountryCode',
			'V6100' => 'Invalid EWAY_CARDNAME',
			'V6101' => 'Invalid EWAY_CARDEXPIRYMONTH',
			'V6102' => 'Invalid EWAY_CARDEXPIRYYEAR',
			'V6103' => 'Invalid EWAY_CARDSTARTMONTH',
			'V6104' => 'Invalid EWAY_CARDSTARTYEAR',
			'V6105' => 'Invalid EWAY_CARDISSUENUMBER',
			'V6106' => 'Invalid EWAY_CARDCVN',
			'V6107' => 'Invalid EWAY_ACCESSCODE',
			'V6108' => 'Invalid CustomerHostAddress',
			'V6109' => 'Invalid UserAgent',
			'V6110' => 'Invalid EWAY_CARDNUMBER',
			'V6111' => 'Unauthorised API Access, Account Not PCI Certified',
			'V6112' => 'Redundant card details other than expiry year and month',
			'V6113' => 'Invalid transaction for refund',
			'V6114' => 'Gateway validation error',
			'V6115' => 'Invalid DirectRefundRequest, Transaction ID',
			'V6116' => 'Invalid card data on original TransactionID',
			'V6117' => 'Invalid CreateAccessCodeSharedRequest, FooterText',
			'V6118' => 'Invalid CreateAccessCodeSharedRequest, HeaderText',
			'V6119' => 'Invalid CreateAccessCodeSharedRequest, Language',
			'V6120' => 'Invalid CreateAccessCodeSharedRequest, LogoUrl',
			'V6121' => 'Invalid TransactionSearch, Filter Match Type',
			'V6122' => 'Invalid TransactionSearch, Non numeric Transaction ID',
			'V6123' => 'Invalid TransactionSearch,no TransactionID or AccessCode specified',
			'V6124' => 'Invalid Line Items. The line items have been provided however the totals do not match the TotalAmount field',
			'V6125' => 'Selected Payment Type not enabled',
			'V6126' => 'Invalid encrypted card number, decryption failed',
			'V6127' => 'Invalid encrypted cvn, decryption failed',
			'V6128' => 'Invalid Method for Payment Type',
			'V6129' => 'Transaction has not been authorised for Capture/Cancellation',
			'V6130' => 'Generic customer information error',
			'V6131' => 'Generic shipping information error',
			'V6132' => 'Transaction has already been completed or voided, operation not permitted',
			'V6133' => 'Checkout not available for Payment Type',
			'V6134' => 'Invalid Auth Transaction ID for Capture/Void',
			'V6135' => 'PayPal Error Processing Refund',
			'V6140' => 'Merchant account is suspended',
			'V6141' => 'Invalid PayPal account details or API signature',
			'V6142' => 'Authorise not available for Bank/Branch',
			'V6143' => 'Invalid Public Key',
			'V6146' => 'Client Side Encryption Key Missing or Invalid',
			'V6147' => 'Unable to Create One Time Code for Secure Field',
			'V6148' => 'Secure Field has Expired',
			'V6149' => 'Invalid Secure Field One Time Code',
			'V6150' => 'Invalid Refund Amount',
			'V6151' => 'Refund amount greater than original transaction',
			'V6152' => 'Original transaction already refunded for total amount',
			'V6153' => 'Card type not support by merchant',
			'V6160' => 'Encryption Method Not Supported',
			'V6161' => 'Encryption failed, missing or invalid key',
			'V6165' => 'Invalid Visa Checkout data or decryption failed',
			'V6170' => 'Invalid TransactionSearch, Invoice Number is not unique',
			'V6171' => 'Invalid TransactionSearch, Invoice Number not found',
			'V6210' => 'Secure Field Invalid Type',
			'V6211' => 'Secure Field Invalid Div',
			'V6212' => 'Invalid Style string for Secure Field',
			'S9900' => 'eWAY library has encountered unknown exception',
			'S9901' => 'eWAY library has encountered invalid JSON response from server',
			'S9902' => 'eWAY library has encountered empty response from server',
			'S9903' => 'eWAY library has encountered unexpected method call',
			'S9904' => 'eWAY library has encountered invalid data provided to models',
			'S9990' => 'eWAY library does not have an endpoint initialised, or not initialise to a URL',
			'S9991' => 'eWAY library does not have API Key or password, or are invalid',
			'S9992' => 'eWAY library has encountered a problem connecting to Rapid',
			'S9993' => 'eWAY library has encountered an invalid API key or password',
			'S9995' => 'eWAY library has encountered invalid argument in method call',
			'S9996' => 'eWAY library has encountered an Rapid server error',
		 );
		if ( isset( $index ) ) {
			$value = array_search( $index, $table );
			if ( isset( $value ) )
				return $table[ $index ];
		}
		return "#" . $index . ' UNDEFINED';
	}
}





if($_GET["AccessCode"]){
	$run = new EwayPaymentGateWay2();
	$run->processingRedirect();
}else{
	$run = new EwayPaymentGateWay2();
	$run->start( array(), $refno, $amount, true );
}




?>