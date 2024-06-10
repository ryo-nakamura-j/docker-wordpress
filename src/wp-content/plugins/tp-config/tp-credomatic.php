<?php

/* ********************************* CREDOMATIC ************************************** */
// Only used in Swisstravel project, not integrated for JTB

function tpCredomaticCheckout($cart, $agentref, $priceCents)
{
	// Credomatic test creditcard never worked, but the live one does work.
	// It's an issue from payment gateway API provider.
	// We use the $testing variable here to fake a creditcard response for testing.
	$testing = false; 
	/*
	 * Note for PCI compliance - never log the cardnumber
	*/
	$carddetails = array('cardname' => trim($_POST['CARDNAME']),
			'cardnumber' => trim($_POST['CARDNUMBER']),
			'cardcvn' => trim($_POST['CARDCVN']),
			'cardexpirymonth' => trim($_POST['CARDEXPIRYMONTH']),
			'cardexpiryyear' => trim($_POST['CARDEXPIRYYEAR']));
			
	error_log('cart to save: ' . print_r($cart, true));
	set_transient("order_" . $agentref, $cart);

	if ( $testing ) {
		wp_redirect( get_site_url() . "/en/payment-response/?response=1&responsetext=Transaction%20received%20and%20approved%3A%3A656327&authcode=656327&transactionid=4261934026&avsresponse=U&cvvresponse=P&orderid=" . $agentref . "&type=sale&response_code=100&username=6744511&time=1535564880&amount=36.00&hash=60a87dc2cfe7af59eeb6e510947a61d6");
		exit;
	}
	tpCredomaticAuthorise($cart, $agentref, $carddetails, $priceCents);
	exit;
}


function tpCredomaticAuthorise($cart, $agentref, $carddetails, $priceCents)
{
	$time = time();
	$pricestr = number_format((float)$priceCents * 0.01, 2, '.', '');
    $authxml = '<form id="tpcardform" method="POST" action="' . get_option('tp_credomatic_url') . '" style="display:none;">'
		. '<input type="text" id="type" name="type" value="sale" />'
		. '<input type="text" id="key_id" name="key_id" value="' . get_option('tp_credomatic_key_id') . '" />'
		. '<input type="text" id="orderid" name="orderid" value="' . $agentref . '" />'
		. '<input type="text" id="time" name="time" value="' . $time  . '" />'
		/*. '<input type="text" id="redirect" name="redirect" value="' . tp_payment_url() . '" />'*/. 
		'<input type="text" id="redirect" name="redirect" value="' . get_site_url() . '/' . get_option('tp_payment_url') . '" />'
		. '<input type="text" id="ccnumber" name="ccnumber" value="' . tpXmlEscape($carddetails['cardnumber'], 20) . '" />'
		. '<input type="text" id="ccexp" name="ccexp" value="' . tpXmlEscape($carddetails['cardexpirymonth'], 2) . tpXmlEscape($carddetails['cardexpiryyear'], 2)  . '" />'
		. '<input type="text" id="cvv" name="cvv" value=""/>' //' . tpXmlEscape($carddetails['cardcvn'], 4) . '" />'
		. '<input type="text" id="amount" name="amount" value="' . $pricestr . '" />'
		. '<input type="text" id="hash" name="hash" value="' . md5( $agentref . '|' . $pricestr . '|' .$time . '|' . get_option('tp_credomatic_key' ) ) . '" />'
		. '<input type="text" id="address" name="address" value="" />'
		. '<input type="text" id="processor_id" name="processor_id" value="swisstravelvm" />'
		. '</form>';
		
    tp_log('tpCredomaticAuthorise POSTING CREDOMATIC payment request for agentref ' . $agentref . ' : ' . $authxml);
    tp_log('tpCredomaticAuthorise POSTING CREDOMATIC payment request for key ' . get_option('tp_credomatic_key_id') . ' | ' . get_option('tp_credomatic_key'));
	
	echo $authxml . '<script type="text/javascript">document.forms["tpcardform"].submit();</script>';
	
}

/*
 *  Handle the payment-response page
 */
function tpCredomaticPaymentResponse()
{
	tp_log('========tpCredomaticPaymentResponse!!!====================');
	
	tp_log('tpCredomaticPaymentResponse for agentref ' . $agentref . ' responsetext=' . $_GET['responsetext']
	 . ' response=' . $_GET['response'] . ' response_code=' . $_GET['response_code'] . ' orderid=' . $_GET['orderid']);

	$agentref =  $_GET['orderid'];
	$order_transient_name = "order_" . $agentref;
	$cart = get_transient($order_transient_name);

	$authresp = tpCredomaticResponse( $_GET['response'] );
	if (isset($authresp) && $authresp['success'])
	{
		// payment was Authorised so make booking	
		$cart['agentref'] = $agentref;
		$cart['payment'] = $authresp['response'];
	
		error_log('cart: ' . print_r($cart, true));
		$bookingresp = tpMakeBooking($cart , $agentref);
		error_log('bookingresp: ' . print_r($bookingresp, true));
	
		$bookingref = tpBookingRef($bookingresp);
		error_log('bookingref: ' . print_r($bookingref, true));
		$status = tpBookingRetailStatus($bookingresp);
		
		if ($status == 'confirmed' || $status == 'requested')
		{
			delete_transient($order_transient_name);
			tpSetCart('{}');
			unset($_SESSION['tpPreAuthResponse']);
			wp_redirect(tp_booking_status_url($status));
			exit;
		}

		error_log('payment completion failed for bookingref: ' . tpBookingRef());
		unset($_SESSION['tpPreAuthResponse']);
		wp_redirect(tp_payment_failed_url() . '/?error=Payment Completion Failed: ' . $completeresp['error']);
		exit;
	}
	else
	{
		// auth failed - but customer may try again with same cart
		unset($_SESSION['tpPreAuthResponse']);
		wp_redirect(tp_payment_failed_url() . '/?error=Authorisation failed: ' . $authresp['error']);
		exit;
	}
}


function tpCredomaticResponse($respCode)
{	
	tp_log('========tpCredomaticResponse!!!====================');
	
	if (empty($respCode))
		$respArr = array('status'=>'failed', 'error' => 'Missing Payment Response');
	else if ($respCode == 1 || $respCode == '1')
		$respArr = array('success' => true, 'transactionId' => $respCode, 'response' => getAllResponse());
	else
		$respArr = array('success' => false, 'error' => $_GET['responsetext'], 'response' => getAllResponse());
	
	tp_log('tpCredomaticResponse resp parsed ' . print_r($respArr, true));
	
	return $respArr;
}

function getAllResponse()
{
	$resp = "";
	foreach($_GET as $name => $value)
	{
		$resp = $resp . $name . ":" . $value . ",";
	}
	return $resp;
}

?>
