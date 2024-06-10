<?php

/* ********************************* EWAY ************************************** */
/* the EWAY gateway has credit card form on the checkout-page
 * to EWAY hosted pages for the client to enter credit card data. EWAY redirects back to payment-response.
* payment-response will make booking.
*/

/*
 * Note for PCI compliance - never log the cardnumber
*/

// http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php?rq=1
function get_ip_address(){
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
function formatXmlString($xml){
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

class EwayPaymentGateWay {
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
		$this->apiUrl = get_option('tp_eway_api_url');
		$this->redirectUrl = tp_payment_success_url();
		$this->cancelUrl = tp_checkout_url();
		$this->tp_payment_failed_url = tp_payment_failed_url();
		$cart = tpGetCart();
		// read sessions
		// if ( $_SESSION['EWAY_CART'] )			$this->cart = $_SESSION['EWAY_CART'];
		// if ( isset($_SESSION['EWAY_AGENTREF']) )		$this->agentref = $_SESSION['EWAY_AGENTREF'];
		// if ( isset($_SESSION['EWAY_PRICECENTS']) )		$this->priceCents = $_SESSION['EWAY_PRICECENTS'];
		// if ( isset($_SESSION['EWAY_DEPOSITONLY']) )	$this->depositonly = $_SESSION['EWAY_DEPOSITONLY'];
		// error_log( '=== EWAY === construct end. ' 		
		// 	. 'Agentref-' . $this->agentref . ', '
		// 	. 'PriceCents-' . $this->priceCents . ', '
		// 	. 'Depositonly-' . $this->depositonly );
	}

	function clearSession( $agentref ) {
		// delete_transient('EWAY_CART' );
		error_log( '=== EWAY === delete transient ' 		
		. 'Agentref-' . $agentref );
		delete_transient('EWAY_AGENTREF' . $agentref );
		delete_transient('EWAY_DEPOSITONLY' . $agentref );
		delete_transient('EWAY_PRICECENTS' . $agentref );
	}

	public function start( $cart, $agentref, $priceCents, $depositonly ) {
		$this->cart = $cart;
		$this->agentref = $agentref;
		$this->priceCents = $priceCents;
		$this->depositonly = $depositonly;
		// set_transient("order_" . $agentref, tpGetCartArray());
		set_transient("order_" . $agentref, $cart);
		// $_SESSION['EWAY_CART'] = $cart;
		set_transient("EWAY_AGENTREF" . $agentref, $agentref);
		set_transient("EWAY_PRICECENTS" . $agentref, $priceCents);
		set_transient("EWAY_DEPOSITONLY" . $agentref, $depositonly);
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
			// $this->clearSession();
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
			// $this->clearSession();
			wp_redirect( $this->tp_payment_failed_url . '/?error=Payment failed: ' . $errorMesssage );
			exit;
		}
	}

	private function makeBookingAndCompletePayment( $response ) {
		// ### Payment was Authorised  so make booking
		// Add extra stuff to cart
		$ewayAgentRef = tpXmlExtractSingle($response, 'InvoiceNumber');
		$order_transient_name = "order_" . $ewayAgentRef;

		$this->agentref = get_transient('EWAY_AGENTREF' . $ewayAgentRef);
		$this->priceCents = get_transient('EWAY_PRICECENTS' . $ewayAgentRef);
		$this->depositonly = get_transient('EWAY_DEPOSITONLY' . $ewayAgentRef);
		// if ( isset($_SESSION['EWAY_AGENTREF']) )		$this->agentref = $_SESSION['EWAY_AGENTREF'];
		// if ( isset($_SESSION['EWAY_PRICECENTS']) )		$this->priceCents = $_SESSION['EWAY_PRICECENTS'];
		// if ( isset($_SESSION['EWAY_DEPOSITONLY']) )	$this->depositonly = $_SESSION['EWAY_DEPOSITONLY'];
		error_log( '=== EWAY === construct end. ' 		
			. 'Agentref-' . $this->agentref . ', '
			. 'PriceCents-' . $this->priceCents . ', '
			. 'Depositonly-' . $this->depositonly );

		
		$this->cart = get_transient($order_transient_name);
		$this->cart['agentref'] = $ewayAgentRef;
		// $this->cart['agentref'] = $this->agentref;
		$this->cart['payment'] = $response;	
		error_log('cart: ' . print_r($this->cart, true));
		// Make Booking
		$bookingresp = tpMakeBooking($this->cart , $this->agentref);
		error_log('bookingresp: ' . print_r($bookingresp, true));
		$bookingref = tpBookingRef($bookingresp);
		error_log('bookingref: ' . print_r($bookingref, true));
		$status = tpBookingRetailStatus($bookingresp);
		error_log('bookingstatus: ' . print_r($status, true));
		// Success
		if ($this->depositonly)
			$status = $status === 'confirmed' ? 'requested' : $status; 
		else if ($status == 'confirmed')
			$this->completePayment( $response );
		// Clean up
		if ( $status == 'confirmed' || $status == 'requested' ) {
			delete_transient($order_transient_name);
			tpSetCart('{}');
		}
		$this->clearSession( $ewayAgentRef );
		wp_redirect(tp_booking_status_url($status));
		exit;
	}

	private function completePayment( $paymentAuthResponse) {
		// Send COMP request
		error_log( '=== EWAY === ' . 'Send complete request');
		$TransactionID = tpXmlExtractSingle( $paymentAuthResponse, 'TransactionID' );
		$data = ''
				. '<TransactionID>'		. $TransactionID							. '</TransactionID>'
		    	. '<Payment>'
					. '<TotalAmount>'		. $this->priceCents							. '</TotalAmount>'
					. '<CurrencyCode>'		. get_option('tp_eway_currency')			. '</CurrencyCode>'
					. '<InvoiceNumber>' 		. tpXmlEscape( $this->agentref , 50) 	. '</InvoiceNumber>'
		    		. '<InvoiceDescription>'	
		    			. 'Booking ' 			. tpXmlEscape($this->cart['lastname'], 50)
						. ' for ' 				. count($this->cart['servicelines']) 
						. ' services on ' 		. tpXmlEscape($this->cart['servicelines'][0]['date'], 10)
					. '</InvoiceDescription>'
					. '<InvoiceReference>' 		. tpXmlEscape(session_id(), 50) 		. '</InvoiceReference>'
		    	. '</Payment>';
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
			$this->clearSession($this->agentref);
			wp_redirect($this->tp_payment_failed_url . '/?error=Payment Completion Failed: ' . $errorMesssage );
			exit;
		}
	}

	private function redirectToSharedPaymentUrl() {
	    $data = '';
        if ( get_option('tp_eway_logo_url') )
        	$data = $data 
        		. '<LogoUrl>'			. get_option('tp_eway_logo_url')	. '</LogoUrl>' 	;
        if ( get_option('tp_eway_theme') )
        	$data = $data 
        		. '<CustomView>'		. get_option('tp_eway_theme')		. '</CustomView>' ;

        $data = $data
	    		// Eway will check IP with country for security. Internal IP is always sent blank. 
	    		// If the validation failed, we will get a Invalid Customer IP response.
	    		. '<CustomerIP>' 		. get_ip_address()					. '</CustomerIP>'		

	    		. '<CustomerReadOnly>'	. 'true'							. '</CustomerReadOnly>'
		    	. '<Method>' 			. 'Authorise' 						. '</Method>'
		    	. '<TransactionType>' 	. 'Purchase'						. '</TransactionType>'
		    	. '<RedirectUrl>'		. $this->redirectUrl				. '</RedirectUrl>'
		    	. '<CancelUrl>'			. $this->cancelUrl					. '</CancelUrl>'
		    	. '<Customer>'
		    	//  . '<TokenCustomerID>'		. get_option('tp_eway_customer')					. '</TokenCustomerID>'
					. '<Reference>' 			. tpXmlEscape( $this->agentref , 50) 				. '</Reference>'
		    		. '<Title>'					. tpXmlEscape( $this->cart['title'], 50 )			. '</Title>'
		    		. '<FirstName>'				. tpXmlEscape( $this->cart['firstname'], 50 )		. '</FirstName>'
		    		. '<LastName>'				. tpXmlEscape( $this->cart['lastname'], 50 )		. '</LastName>'
		    		. '<Email>'					. tpXmlEscape( $this->cart['email'], 50 )			. '</Email>'
		    		. '<Street1>'				. tpXmlEscape( $this->cart['address1'], 255 )		. '</Street1>'
		    		. '<Street2>'				. tpXmlEscape( $this->cart['address2'], 255 )		. '</Street2>'
		    		. '<City>'					. tpXmlEscape( $this->cart['address3'], 255 )		. '</City>'
		    		. '<State>'					. tpXmlEscape( $this->cart['branch_label'], 255 )		. '</State>'
		    		. '<PostalCode>'			. tpXmlEscape( $this->cart['postCode'], 6 )			. '</PostalCode>'
		    		. '<Country>'				. strtolower( get_option('tp_eway_country')	)		. '</Country>'
		    		. '<Phone>'					. tpXmlEscape( $this->cart['phone'], 32 )			. '</Phone>'
		    	. '</Customer>'
		    	. '<Payment>'
		    		. '<TotalAmount>'			. $this->priceCents							. '</TotalAmount>'
		    		. '<CurrencyCode>'			. get_option('tp_eway_currency')			. '</CurrencyCode>'
					. '<InvoiceNumber>' 		. tpXmlEscape( $this->agentref , 50) 		. '</InvoiceNumber>'
		    		. '<InvoiceDescription>'	
		    			. 'Booking ' 			. tpXmlEscape($this->cart['lastname'], 50)
						. ' for ' 				. count($this->cart['servicelines']) 
						. ' services on ' 		. tpXmlEscape($this->cart['servicelines'][0]['date'], 10)
					. '</InvoiceDescription>'
					. '<InvoiceReference>' 		. tpXmlEscape(session_id(), 50) 		. '</InvoiceReference>'
		    	. '</Payment>'
		    	. '<ShippingAddress>'
		    		. '<ShippingMethod>'		.	'DesignatedByCustomer'							. '</ShippingMethod>'
	    			. '<FirstName>'				. tpXmlEscape( $this->cart['firstname'], 50 )		. '</FirstName>'
		    		. '<LastName>'				. tpXmlEscape( $this->cart['lastname'], 50 )		. '</LastName>'
		    		. '<Email>'					. tpXmlEscape( $this->cart['email'], 50 )			. '</Email>'
		    		. '<Street1>'				. tpXmlEscape( $this->cart['deliveryAddress1'], 255 )		. '</Street1>'
		    		. '<Street2>'				. tpXmlEscape( $this->cart['deliveryAddress2'], 255 )		. '</Street2>'
		    		. '<State>'					. tpXmlEscape( $this->cart['deliveryCountry'], 255 )		. '</State>'
		    		. '<PostalCode>'			. tpXmlEscape( $this->cart['deliveryPostCode'], 6 )			. '</PostalCode>'
		    		. '<Country>'				. strtolower( get_option('tp_eway_country')	)		. '</Country>'
		    		. '<Phone>'					. tpXmlEscape( $this->cart['phone'], 32 )			. '</Phone>'
		    	. '</ShippingAddress>'
		    	. '<Options>'
		    		. '<Option>'
		    			. '<Value>'				. 'ServiceLineCount ' . count($this->cart['servicelines'])					. '</Value>'
		    		. '</Option>'
		    		. '<Option>'
		    			. '<Value>'				. 'TravelDate ' . tpXmlEscape($this->cart['servicelines'][0]['date'], 10)	. '</Value>'
		    		. '</Option>'
		    		. '<Option>'
		    			. '<Value>'				. 'IPaddr ' . get_ip_address()												. '</Value>'
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
		error_log( '=== EWAY === ' . formatXmlString( $req ) );

		// Submit request
		$response = $this->submitRequest( $this->apiUrl, $req, $this->getAuthDetail() );
		error_log( '=== EWAY === ' . formatXmlString( $response ) );

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

	private function getAuthDetail()
	{
	   return get_option('tp_eway_username') . ':' . get_option('tp_eway_password');
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



?>