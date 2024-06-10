<?php

/* ********************************* DPS - Direct Payment Solutions ************************************** */
/* the DPS gateway has a (should be https) creditcard form on the checkout page for the customer to enter card details.
 * The form posts back to the checkout-page to collect the data to then send details in an Authorize XML request as (https) DPS webservice.
 * If the authorizes request just returns DPS details so if it succeeds then the checkout form redirects the client to the DPS url 
 * that is returned from the authorize request. The DPS URL in turn will in turn redirect to payment-response. 
 * The payment will be redirected to booking-requested (booking on request) 
 * or Completed (via XML request) and redirected to booking-confirmed
 * or redirected to booking-failed if there was an issue mooking via iCom. 
 */

function tpDPSAmount($priceCents)
{
	return isset($priceCents) && $priceCents > 0 ? number_format($priceCents/100, 2, '.', '') : null;
}

// 16chars uniq id using time and agentref
function tpDPSTransactionId($agentref)
{
	return ((string) time()) . tpEnc($agentref, 5, '0');
}

function tpDPSPxSubmit($data)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/xml; charset=utf-8', 'Accept: application/xml'));
	curl_setopt($curl, CURLOPT_URL, get_option('tp_dps_px_url'));
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

function tpDPSParseAuth($resp)
{
  return array(
		'valid' => tpXmlExtractAttr($resp, 'request', 'valid'),
		'URI' => tpXmlExtractSingle($resp, 'URI')
	);
}

/*
 *  send the payment authorise request to DPS
*/
function tpDPSAuthorise($cart, $agentref, $carddetails, $priceCents)
{
	$dollars = tpDPSAmount($priceCents);
	if (!isset($dollars))
	{
		return array('success' => false, 'error' => 'invalid amount', 'response' => 'tpDPSAuthorise invalid amount ' . $priceCents);
	}

	$txnid = tpDPSTransactionId($agentref);
	$merchantref = tpXmlEscape(session_id(), 50);

	$authxml = '<GenerateRequest>'
	  . '<PxPayUserId>' . get_option('tp_dps_px_userid') . '</PxPayUserId>'
	  . '<PxPayKey>' . get_option('tp_dps_px_key')  . '</PxPayKey>'
		. '<TxnType>Auth</TxnType>'
		. '<AmountInput>' . $dollars . '</AmountInput>'
		. '<CurrencyInput>' . get_option('tp_currency') . '</CurrencyInput>'
		. '<TxnId>' . $txnid . '</TxnId>'
		. '<MerchantReference>' . $merchantref . '</MerchantReference>'
		. '<UrlSuccess>' . tp_payment_success_url() . '</UrlSuccess>'
		. '<UrlFail>' . tp_payment_failed_url() . '</UrlFail>'
		. '</GenerateRequest>';

    tp_log('tpDPSAuthorise sending DPS authorise request for agentref=' . $agentref . ' request=' . $authxml);

  $resp = tpDPSPxSubmit($authxml);
  tp_log('tpDPSAuthorise resp for agentref=' . $agentref . ': ' . $resp);
	$respArr = tpDPSParseAuth($resp);
	tp_log('tpDPSAuthorise resp parsed=' . print_r($respArr, true));
	if ($respArr['valid'] === '1')
	{
	    return array('success' => true, 'URI' => $respArr['URI'], 'response' => $resp);
	}
	return array('success' => false, 'error' => 'error generating authorization request', 'response' => $resp);
}

function tpDPSParseProcess($resp)
{
	return array(
			'valid' => tpXmlExtractAttr($resp, 'Response', 'valid'),
			'DpsTxnRef' => tpXmlExtractSingle($resp, 'DpsTxnRef'),
			'AuthCode' => tpXmlExtractSingle($resp, 'AuthCode'),
			'ResponseText' => tpXmlExtractSingle($resp, 'ResponseText'),
			'TxnId' => tpXmlExtractSingle($resp, 'TxnId'));
}

// called after redirected back from paymentexpress
function tpDPSProcess($result)
{
	$statusxml = '<ProcessResponse>'
		. '<PxPayUserId>' . get_option('tp_dps_px_userid') . '</PxPayUserId>'
		. '<PxPayKey>' . get_option('tp_dps_px_key') . '</PxPayKey>'
		. '<Response>' . $result . '</Response>'
		. '</ProcessResponse>';

	$resp = tpDPSPxSubmit($statusxml);
	tp_log('tpDPSGetStatus resp for txnid ' . $txnid . ': ' . $resp);
	$respArr = tpDPSParseProcess($resp);
    if ($respArr['valid'] === '1')
	{
	    return array('success' => true, 'transactionId' => $respArr['DpsTxnRef'], 'response' => $resp, 'agentRef' => $respArr['TxnId']);
	}
	return array('success' => false, 'error' => $respArr['AuthCode'] . ' ' . $respArr['ResponseText'], 'response' => $resp);

}

function tpDPSWsSubmit($soapaction, $data)
{
	$soapreq = '<?xml version="1.0" encoding="utf-8"?>'
	    . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">'
	    . '<soap:Body>'
	    . $data
	    . '</soap:Body>'
		. '</soap:Envelope>';

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array('Content-Type:text/xml; charset=utf-8', 'Accept: text/xml', 'SOAPAction: "' . $soapaction . '"'));
	curl_setopt($curl, CURLOPT_URL, get_option('tp_dps_ws_url'));
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $soapreq);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
	curl_setopt($curl, CURLOPT_TIMEOUT,        20);
	$resp = curl_exec($curl);
	curl_close($curl);
	return $resp;
}

function tpDPSParseComplete($resp)
{
	return array('statusRequired' => tpXmlExtractSingle($resp, 'statusRequired'),
		    'authorized' => tpXmlExtractSingle($resp, 'authorized'),
			'txnRef' => tpXmlExtractSingle($resp, 'txnRef'),
			'reco' => tpXmlExtractSingle($resp, 'reco'),
			'cardHolderResponseText' => tpXmlExtractSingle($resp, 'cardHolderResponseText'),
			'cardHolderHelpText' => tpXmlExtractSingle($resp, 'cardHolderHelpText')
	);
}

function tpDPSGetStatus($txnref)
{
	$statusxml = '<GetStatus xmlns="http://PaymentExpress.com">'
		. '<postUsername>' . get_option('tp_dps_ws_username') . '</postUsername>'
		. '<postPassword>' . get_option('tp_dps_ws_password')  . '</postPassword>'
		. '<txnRef>' . $txnref . '</txnRef>'
		. '</GetStatus>';

	$resp = tpDPSWsSubmit('http://PaymentExpress.com/GetStatus', $statusxml);
	tp_log('tpDPSGetStatus resp for txnref=' . $txnref . ': ' . $resp);
	return tpDPSParseComplete($resp);
}

/*
 *  send the payment complete request to DPS
 */
function tpDPSComplete($cart, $dpstxref, $bookingref, $agentref, $carddetails, $priceCents)
{
	$dollars = tpDPSAmount($priceCents);
	if (!isset($dollars))
	{
		return array('success' => false, 'error' => 'invalid amount', 'response' => 'tpDPSComplete invalid amount');
	}
	else if (!isset($dpstxref))
	{
		return array('success' => false, 'error' => 'invalid payment reference dpstxref', 'response' => 'tpDPSComplete missing dpstxref');
	}

	$cardnum = tpXmlEscape($carddetails['cardnumber'], 20);
	$txnref = tpDPSTransactionId($agentref);

	$compxml = '<SubmitTransaction xmlns="http://PaymentExpress.com">'
		. '<postUsername>' . get_option('tp_dps_ws_username') . '</postUsername>'
		. '<postPassword>' . get_option('tp_dps_ws_password') . '</postPassword>'
		. '<transactionDetails>'
		. '<amount>' . $dollars . '</amount>'
	    . '<txnType>Complete</txnType>'
		. '<dpsTxnRef>' . $dpstxref . '</dpsTxnRef>'
		. '<txnRef>' . $txnref . '</txnRef>'
		. '</transactionDetails>'
		. '</SubmitTransaction>';

    tp_log('tpDPSComplete sending DPS complete request for agentref=' . $agentref
    		. ' dpsTxnRef=' . $dpstxref . ' txnRef=' . $txnref . ' : cardmasked=' . tpCensorCardNumber($compxml, $cardnum));

    $resp = tpDPSWsSubmit('http://PaymentExpress.com/SubmitTransaction', $compxml);
    tp_log('tpDPSComplete resp for agentref ' . $agentref . ': ' . $resp);
	$respArr = tpDPSParseComplete($resp);
	tp_log('tpDPSComplete resp parsed ' . print_r($respArr, true));
	if ($respArr['authorized'] === '1')
	{
		return array('success' => true, 'transactionId' => $respArr['DpsTxnRef'], 'response' => $resp);
	}
    else if ($respArr['statusRequired'] === '1')
	{
	    $respArr = tpDPSGetStatus($txnref);
		if ($respArr['Success'] === '1')
		{
			return array('success' => true, 'transactionId' => $respArr['DpsTxnRef'], 'response' => $resp);
		}
	}

	return array('success' => false, 'error' => $respArr['response'], 'response' => $resp);
}

/*
 *  Handle the payment-response page
 */
function tpDPSPaymentResponse()
{
	$result = $_REQUEST['result'];
	if (isset($result))
	{
		$authresp = tpDPSProcess($result);
		if ($authresp['success'])
		{
			$txnref = $authresp['transactionId'];
	
			// payment was Authorised  so make booking
			tp_log('tp-payment-response auth resp ' . print_r($authresp, true));
	
			$_SESSION['tpPreAuthResponse'] = $authresp;
			$agentref = $_SESSION['tpagentref'];
			$priceCents = $_SESSION['tpprice'];
	
			// $cartarr = tpGetCartArray();
			$cartTransientName = "cart_";
			$cartTransientName .= $agentref;
			$cartarr = get_transient($cartTransientName);

			tp_log('tp-payment-response cartarray ' . print_r($cartarr , true));
			$cart = tpAddFees(tpGetCartArray(), $cartarr['deliveryMethod']);
			$cart['payment'] = $authresp['response'];
			tp_log('tp-payment-response cart ' . print_r($cart , true));
			$carddetails = array();
	
			tp_log('tp-payment-response cart: ' . print_r($cart, true));
			$bookingresp = tpMakeBooking($cart , $agentref);
			tp_log('bookingresp: ' . print_r($bookingresp, true));
	
			$bookingref = tpBookingRef($bookingresp);
			tp_log('bookingref: ' . print_r($bookingref, true));
			$status = tpBookingRetailStatus($bookingresp);
			if ($status == 'confirmed')
			{
				delete_transient($cartTransientName);
				$completeresp = tpDPSComplete($cart, $txnref, $bookingref, $agentref, $carddetails, $priceCents);
				if (!isset($completeresp) || !$completeresp['success'])
				{
					//ERROR settling - send email
					tp_log('payment completion failed for bookingref: ' . $bookingref);
				    tpSetCart('{}');
					unset($_SESSION['tpPreAuthResponse']);
					wp_redirect(tp_payment_failed_url() . '/?error=Payment Completion Failed: ' . $completeresp['error']);
					exit;
				}
			}
	
			tpSetCart('{}');
			unset($_SESSION['tpPreAuthResponse']);
			wp_redirect(tp_booking_status_url($status));
			exit;
		}
	}	
}

function tpDPSCheckout($cart, $agentref, $priceCents)
{
	$authresp = tpDPSAuthorise($cart, $agentref, array(), $priceCents);
	if (isset($authresp) && $authresp['success'])
	{
		$cartTransientName = "cart_" . $agentref;

		set_transient($cartTransientName, $cart, HOUR_IN_SECONDS);

		$dpsurl = $authresp['URI'];
		tp_log('INFO: checkout page - redirect to DPS "' . $dpsurl . '" for agentref=' . $agentref);
		wp_redirect($dpsurl);
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

?>
