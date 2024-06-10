<?php

/* ********************************* AEON Public Bank ************************************** */
/* the AEON gateway has credictcard form on the checkout page for the customer to enter card data.
 * the checkout-page posts back to itself so that the card data is sent in an authorize request
 * (by http post) to AEON and subsequent complete request.
 */


function tpAEONPayment($cart, $agentref, $priceCents, $bookingref)
{
	$data = '<form id="tpcardform" method="POST" action="' . get_option('tp_aeon_payment_url') . '" style="display:none;">'
		. '<input type="text" id="paymentID" name="paymentID" value="' .  get_option('tp_aeon_payment_id') . '" />'
		. '<input type="text" id="merchantID" name="merchantID" value="' . get_option('tp_aeon_merchant_id') . '" />'
		. '<input type="text" id="invoiceNo" name="invoiceNo" value="' . $agentref . '" />'
		. '<input type="text" id="productDesc" name="productDesc" value="JTB Booking - ' . $bookingref . '" />'
		. '<input type="text" id="currencyCode" name="currencyCode" value="' . get_option('tp_aeon_currency_code') . '" />'
		. '<input type="text" id="amount" name="amount" value="' . tpEnc($priceCents, 12, '0') . '" />'
		. '<input type="text" id="hashValue" name="hashValue" value="' . hashValue(get_option('tp_aeon_payment_id') . get_option('tp_aeon_merchant_id') . $invoiceNo . $agentref  . get_option('tp_aeon_currency_code') . tpEnc($priceCents, 12, '0') ) . '" />'
		. '</form>';

	tp_log('tpAEONPayment POSTING AEON payment request for agentref ' . $agentref . ' : ' . $data);

	echo $data . '<script type="text/javascript">document.forms["tpcardform"].submit();</script>';
    exit;
}


function hashValue ($data)
{
	$signdata = hash_hmac(get_option('tp_aeon_hash_algorithm'), $data, get_option('tp_aeon_hash_key') , false);
	$signdata = strtoupper($signdata);
	return urlencode($signdata);
}

/*
 * handle payment-response page
*/
function tpAEONCheckout($cart, $agentref, $priceCents)
{
	//global $aeonCodes;
	$agentref = $_SESSION['tpagentref'];
	$priceCents = $_SESSION['tpprice'];
	$_SESSION['tpcart'] = $cart;
	$cart['agentref'] = $agentref;
	$_SESSION['cart'] = $cart;
	
	
	if (tpCartIsConfirmed())
	{
		// Booking confirmed so make payment
		tpAEONPayment($cart, $agentref, $priceCents, $bookingref);
	}
	else
	{
		tpAEONBooking('');
	}
}

function tpAEONPaymentResponse()
{
	$respCode = $_POST['respCode'];
	$respArr = tpAEONResponse($respCode);
	if ($respCode != '00')
	{
		wp_redirect(tp_payment_failed_url() . '/?error=Payment Completion Failed: ' . $respArr['error']);
		exit;
	}
	else
	{
		tpAEONBooking($respArr);
	}
}


function tpAEONBooking($respArr)
{
    $cart = $_SESSION['tpcart'];
	$agentref = $_SESSION['tpagentref'];
	tp_log('cart: ' . print_r($cart, true));

	$agentref = $_SESSION['tpagentref'];
	tp_log('tp-payment-success aeon tpagentref=' . $agentref);

	$cartarr = tpGetCartArray();
	$cart = tpAddFees($cartarr, $cartarr['deliveryMethod']);
	$cart['payment'] = print_r($respArr,true);

	$bookingresp = tpMakeBooking($cart , $agentref);
	tp_log('bookingresp: ' . print_r($bookingresp, true));
	$bookingref = tpBookingRef($bookingresp);
	tp_log('bookingref: ' . print_r($bookingref, true));
	$status = tpBookingRetailStatus($bookingresp);
	tp_log('tp-payment-success aeon bookingref=' . print_r($bookingref, true) . ' bookingstatus=' . $status);
	tpSetCart('{}');

	wp_redirect(tp_booking_status_url($status));
	exit;
}


function tpAEONResponse($respCode)
{	
	if (empty($respCode))
	{
		$respArr = array('status'=>'failed', 'error' => 'Missing Payment Response');
	}
	else
	{
		$respArr = array('status' => $respCode === '00' ? 'success' : 'failed',
				     'transactionId'=> $_POST['transref'],
				     'error' => $respCode !== '[00]' ? $response['failReason'] : '',
				     'response' => getAllResponse());
	}
	
	tp_log('tpAEONPayment resp parsed ' . print_r($respArr, true));
	
	return $respArr;
}

function getAllResponse()
{
	$resp = "";
	foreach($_POST as $name => $value)
	{
		$resp = $resp . $name . ":" . $value . ",";
	}
	return $resp;
}


function tpAEONClearSession($clearCart)
{
	unset($_SESSION['tppriceCents']);
	unset($_SESSION['tpinvoiceNo']);
	unset($_SESSION['tpcart']);
	if ($clearCart) {
	    tpSetCart('{}');
	}
}


?>
