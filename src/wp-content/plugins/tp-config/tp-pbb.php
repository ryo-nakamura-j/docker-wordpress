<?php

/* ********************************* PBB Public Bank ************************************** */
/* the PBB gateway has credictcard form on the checkout page for the customer to enter card data.
 * the checkout-page posts back to itself so that the card data is sent in an authorize request
 * (by http post) to PBB and subsequent complete request.
 */

function tpPBBSubmit($data)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded; charset=utf-8'));
    curl_setopt($curl, CURLOPT_URL, get_option('tp_pbb_process_url'));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_TIMEOUT,        20);
	$resp = curl_exec($curl);
    curl_close($curl);
	return $resp;
}

function tpPBBAuthResponse($resp)
{
   return array(
	   'response' => substr($resp, 0, 2),
	   'authCode' => substr($resp, 2, 6),
	   'invoiceNo' => substr($resp, 8, 20),
	   'PAN' => substr($resp, 28, 4),
	   'ExpDate' => substr($resp, 32, 4),
	   'Amount' => substr($resp, 36, 12),
	   'ECI' => substr($resp, 48, 2)
   );
}

function tpPBBCaptureResponse($resp)
{
   return array(
	   'response' => substr($resp, 0, 2),
	   'authCode' => substr($resp, 2, 6),
	   'invoiceNo' => substr($resp, 8, 20),
	   'PAN' => substr($resp, 28, 4),
	   'ExpDate' => substr($resp, 32, 4),
	   'Amount' => substr($resp, 36, 12)
   );
}

function tpPBBInvoiceNo($agentref, $pan)
{
    return ((string) time()) . tpEnc($pan, 5, '0') . tpEnc($agentref, 5, '0');
}

$pbbCodes = array(
		'00' => 'SUCCESS: Approved',
		'01' => 'DECLINE: Refer to Card Issuer',
		'02' => 'DECLINE: Refer to Issuer\'s Special Conditions',
		'03' => 'DECLINE: Invalid Merchant ID',
		'04' => 'DECLINE: Pick Up Card',
		'05' => 'DECLINE: Do Not Honour',
		'06' => 'FAIL: Error',
		'07' => 'DECLINE: Pick Up Card, Special Conditions',
		'08' => 'DECLINE: Honour with ID',
		'09' => 'FAIL: Request in Progress',
		'10' => 'DECLINE: Partial Amount Approved',
		'12' => 'DECLINE: Invalid Transaction',
		'13' => 'DECLINE: Invalid Amount',
		'14' => 'DECLINE: Invalid Card Number',
		'17' => 'DECLINE: Customer Cancellation',
		'18' => 'DECLINE: Customer Dispute',
		'19' => 'FAIL: Re-enter Transaction',
		'30' => 'FAIL: Format Error',
		'31' => 'FAIL: Bank not Supported by Switch',
		'32' => 'DECLINE: Completed Partially',
		'33' => 'DECLINE: Expired Card - Pick Up',
		'34' => 'DECLINE: Suspected Fraud - Pick Up',
		'35' => 'DECLINE: Contact Acquirer - Pick Up',
		'36' => 'DECLINE: Restricted Card - Pick Up',
		'37' => 'DECLINE: Call Acquirer Security - Pick Up',
		'39' => 'DECLINE: No Credit Account',
		'40' => 'DECLINE: Requested Function not Supported',
		'41' => 'DECLINE: Lost Card - Pick Up',
		'43' => 'DECLINE: Stolen Card - Pick Up',
		'51' => 'DECLINE: Insufficient Funds',
		'54' => 'DECLINE: Expired Card',
		'57' => 'DECLINE: Trans. not Permitted to Cardholder',
		'58' => 'DECLINE: Transaction not Permitted to Terminal',
		'59' => 'DECLINE: Suspected Fraud',
		'60' => 'DECLINE: Card Acceptor Contact Acquirer',
		'62' => 'DECLINE: Restricted Card',
		'63' => 'DECLINE: Security Violation',
		'65' => 'DECLINE: Exceeds Withdrawal Frequency Limit',
		'66' => 'DECLINE: Card Acceptor Call Acquirer Security',
		'68' => 'FAIL: Response Received Too Late',
		'90' => 'FAIL: Cut-off in Progress',
		'91' => 'FAIL: Issuer or Switch is Inoperative',
		'92' => 'DECLINE: Financial Institution not Found',
		'93' => 'DECLINE: Trans Cannot be Completed',
		'94' => 'DECLINE: Duplicate Transmission',
		'95' => 'DECLINE: Reconcile Error',
		'96' => 'DECLINE: System Malfunction',
		'97' => 'DECLINE: Reconciliation Totals Reset',
		'98' => 'DECLINE: MAC Error',
		'N7' => 'DECLINE: Card details error'
	);

function tpPBBIsVisaCard($cardnumber)
{
   return $cardnumber[0] === '4';
}

function tpPBBMerchant($cardnumber)
{
   return get_option(tpPBBIsVisaCard($cardnumber) ? 'tp_pbb_merchant_visa' : 'tp_pbb_merchant_mastercard');
}

/*
 * construct the creditcard form that posts to CFB and is hidden on the checkout page
*/
function tpPBBCheckoutForm($priceCents)
{
	return tpCartIsConfirmed() ? tpCFBComplete($priceCents) : tpCFBAuthorise($priceCents);
}

function tpPBBAuthorise($cart, $agentref, $carddetails, $priceCents, $invoiceNo)
{
	$cardnum = tpEnc($carddetails['cardnumber'], 16);
	$authdata = '<form id="tpcardform" method="POST" action="' . get_option('tp_pbb_process_url') . '" style="display:none;">'
		. '<input type="hidden" name="merID" value="' . tpEnc(tpPBBMerchant($cardnum), 10) . '" />'
		. '<input type="hidden" name="PAN" value="'. $cardnum . '" />'
		. '<input type="hidden" name="expiryDate" value="' . tpEnc($carddetails['cardexpirymonth'], 2) . tpEnc($carddetails['cardexpiryyear'], 2) . '" />'
		. '<input type="hidden" name="CVV2" value="' . tpEnc($carddetails['cardcvn'], 3) . '" />'
		. '<input type="hidden" name="invoiceNo" value="' . tpEnc($invoiceNo, 20, '0') . '" >'
		. '<input type="hidden" name="amount" value="' . tpEnc($priceCents, 12, '0') . '" />'
		. '<input type="hidden" name="postURL" value="' . tp_payment_success_url() . '" >'
		. '</form>';

	tp_log('tpPBBAuthorise POSTING PBB pay request for agentref ' . $agentref . ' : ' . tpCensorCardNumber($authdata, $cardnum));

	return $authdata . '<script type="text/javascript">document.forms["tpcardform"].submit();</script>';
}

function tpPBBComplete($cart, $txid, $bookingref, $agentref, $carddetails, $priceCents, $invoiceNo)
{
    global $pbbCodes;

  	$cardnum = tpEnc($carddetails['cardnumber'], 16);

  //   $capturedata = 'merID=' . tpEnc(tpPBBMerchant($cardnum), 10)
		// . '&transactionType=0220'
		// . '&XID=' . tpEnc($txid, 6, ' ')
		// . '&amount=' . tpEnc($priceCents, 12, '0')
		// . '&PAN=' . $cardnum
		// . '&invoiceNo=' . tpEnc($invoiceNo, 20, '0') ;

	$capturedata = 'transactionType=0220'
		. '&merID=' . tpEnc(tpPBBMerchant($cardnum), 10)
		. '&PAN=' . $cardnum
		. '&invoiceNo=' . tpEnc($invoiceNo, 20, '0')
		. '&amount=' . tpEnc($priceCents, 12, '0')
		. '&approvalCode=' . $txid;

    tp_log('tpPBBComplete sending PBB capture request for agentref ' . $agentref . ' : ' . tpCensorCardNumber($capturedata, $cardnum));

    $resp = tpPBBSubmit($capturedata);
    if ($resp === false)
	   tp_log('ERROR: tpPBBComplete failed for agentref ' . $agentref . ': ' . get_option('tp_pbb_process_url'));
	else
	   tp_log('tpPBBComplete resp for agentref ' . $agentref . ': ' . $resp);

	$respArr = tpPBBCaptureResponse($resp);
	tp_log('tpPBBComplete resp parsed ' . print_r($respArr, true));
	if ($respArr['response'] === '00')
	{
		return array('success' => true, 'transactionId' => $txid, 'response' => $resp);
	}
    else
	{
		return array('success' => false, 'error' => $respArr['response'] . ' ' . $pbbCodes[$respArr['response']], 'response' => $resp);
	}
}

function tpPBBCheckout($cart, $agentref, $priceCents)
{
	/*
	 * Note for PCI compliance - never log the cardnumber
	*/
	$carddetails = array('cardname' => trim($_POST['CARDNAME']),
			'cardnumber' => trim($_POST['CARDNUMBER']),
			'cardcvn' => trim($_POST['CARDCVN']),
			'cardexpirymonth' => trim($_POST['CARDEXPIRYMONTH']),
			'cardexpiryyear' => trim($_POST['CARDEXPIRYYEAR']));

	if (empty($carddetails['cardname']) || empty($carddetails['cardnumber']) || empty($carddetails['cardexpirymonth']) || empty($carddetails['cardexpiryyear']))
	{
		tp_log('ERROR! checkout page - A required payment field was empty for agentref=' . $agentref . ' carddetails=' . $carddetails . ' post=' .  print_r($_POST, true));
		wp_redirect(tp_payment_failed_url() . '/?error=A required payment field was empty');
		exit;
	}

	$invoiceNo = tpPBBInvoiceNo($agentref, substr($carddetails['cardnumber'], -4));

	$_SESSION['tpcarddetails'] = $carddetails;
	$_SESSION['tppriceCents'] = $priceCents;
	$_SESSION['tpinvoiceNo'] = $invoiceNo;

	echo tpPBBAuthorise($cart, $agentref, $carddetails, $priceCents, $invoiceNo);
    exit;
}

function tpPBBClearSession($clearCart)
{
	unset($_SESSION['tpPreAuthResponse']);
	unset($_SESSION['tpcarddetails']);
	unset($_SESSION['tppriceCents']);
	unset($_SESSION['tpinvoiceNo']);
	if ($clearCart) {
	    tpSetCart('{}');
	}
}

/*
 * handle payment-response page
*/
function tpPBBPaymentResponse()
{
	global $pbbCodes;
	$agentref = $_SESSION['tpagentref'];
	$resp = $_POST['result'];
	if (!empty($resp))
	{
		$respArr = tpPBBAuthResponse($resp);
		tp_log('tpCPBBPaymentResponse (Pay) resp parsed ' . print_r($respArr, true));
		if ($respArr['response'] === '00')
		{
			$authresp = array('success' => true, 'transactionId' => $respArr['authCode'], 'response' => $resp);
			// payment was paid  so make booking
			tp_log('checkout pay resp ' . print_r($authresp, true));

			$_SESSION['tpPreAuthResponse'] = $authresp;
			$cartarr = tpGetCartArray();
			$cart = tpAddFees($cartarr, $cartarr['deliveryMethod']);

			$cart['agentref'] = $agentref;
			$cart['payment'] = $resp;

			tp_log('cart: ' . print_r($cart, true));
			$bookingresp = tpMakeBooking($cart , $agentref);
			tp_log('bookingresp: ' . print_r($bookingresp, true));

			$carddetails = $_SESSION['tpcarddetails'];
	        $priceCents = $_SESSION['tppriceCents'];
		    $invoiceNo = $_SESSION['tpinvoiceNo'];

			$bookingref = tpBookingRef($bookingresp);
			tp_log('bookingref: ' . print_r($bookingref, true));
			$status = tpBookingRetailStatus($bookingresp);

			tpPBBClearSession(true);
			wp_redirect(tp_booking_status_url($status));
			exit;
		}
	}

	// pay failed - but customer may try again with same cart
	$authresp = array('success' => false, 'error' => $respArr['response'] . ' : ' . $pbbCodes[$respArr['response']], 'response' => $resp);
	tp_log('tpPBBPaymentResponse failed for agentref=' . $agentref . ' : ' . print_r($authresp, true));
    tpPBBClearSession(false);
	wp_redirect(tp_payment_failed_url() . '/?error=Authorisation failed: ' . $authresp['error']);
	exit;
}

?>
