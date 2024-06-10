<?php

/* ********************************* Axes Solutions (Singapore) ************************************** */
/* the Axes gateway has a custom hidden form on the checkout page that posts price and mechant info
 * to Axes hosted pages for the client to enter credit card data. Axes redirects back to payment-success
 * or payament-failed pages. Axes will also POST serverside confirmation details to payment-response.
 */

function tpAxesRequest($auth, $amount, $agentref, $email, $phone)
{
	return '<form id="tpcardform" method="POST" action="' . get_option('tp_axes_url') . '" style="display:none;">'
		. '<input type="hidden" name="site_code" value="' . get_option('tp_axes_site_code') . '" />'
		. '<input type="hidden" name="order_code" value="' . $agentref . '" />'
		. '<input type="hidden" name="amount" value="' . $amount . '" />'
		. '<input type="hidden" name="currency" value="' .  get_option('tp_currency') . '" />'
		. '<input type="hidden" name="lang" value="en">'
		. '<input type="hidden" name="email" value="' . tpEncEmail($email, null) . '">'
		. '<input type="hidden" name="telephone_no" value="' . tpEnc($phone) . '">'
		. '<input type="hidden" name="success_url" value="' . tp_payment_success_url() . '" >'
		. '<input type="hidden" name="failure_url" value="' . tp_payment_failed_url() . '" >'
		. ($auth ? '<input type="hidden" name="request value="auth">' : '')
		. '</form>';
}

function tpAxesComplete($amountDollars, $agentref, $email, $phone)
{
   return tpAxesRequest(false, $amountDollars, $agentref, $email, $phone);
}

function tpAxesAuthorise($amountDollars, $agentref, $email, $phone)
{
   return tpAxesRequest(true, $amountDollars, $agentref, $email, $phone);
}

/*
 * construct the creditcard form that posts to Axes and is hidden on the checkout page
 */
function tpAxesCheckoutFormTemplate($priceCents, $agentref, $email, $phone)
{
  return tpCartIsConfirmed()
	     ? tpAxesComplete(tpDollars($priceCents), $agentref, $email, $phone)
	     : tpAxesAuthorise(tpDollars($priceCents), $agentref, $email, $phone);
}

function tpAxesCheckoutFormScript()
{
  return '<script>'
		. 'function repriceCallback(priceCents) {'
		. 'if (priceCents) {'
		. '$("form#tpcardform input[name=amount]").val((priceCents/100).toFixed(2))'
		. '}'
		. '}'
		. '</script>';
}

function tpAxesResponse($response)
{
	if (empty($response))
	{
		return array('status'=>'failed', 'error' => 'Missing Payment Response');
	}
	else
	{
		return array('status' => $response['result'] === '[success]' ? 'success' : 'failed',
				     'transactionId'=> $response['order_code'],
				     'error' => $response['result'] !== '[success]' ? $response['result'] : '',
				     'response' => print_r($response, true));
	}
}

/*
 * handle payment-failed page (which is in customers session)
*/
function tpAxesPaymentFailed()
{
	// remove the payment_response

	$agentref = $_SESSION['tpagentref'];
	tp_log('tp-payment-failed axes tpagentref=' . $agentref);
}

/*
 * handle payment-success page (which is in customers session)
*/
function tpAxesPaymentSuccess()
{
	// call and wait for the paymentresponse to make sure it really got success/failed

	$agentref = $_SESSION['tpagentref'];
	tp_log('tp-payment-success axes tpagentref=' . $agentref);

	$cartarr = tpGetCartArray();
	$cart = tpAddFees($cartarr, $cartarr['deliveryMethod']);  // ?? is this needed??
	$cart['payment'] = print_r($_POST['response'],true);

	$bookingresp = tpMakeBooking($cart , $agentref);
	$bookingref = tpBookingRef($bookingresp);
	$status = tpBookingRetailStatus($bookingresp);
	tp_log('tp-payment-success axes bookingref=' . print_r($bookingref, true) . ' bookingstatus=' . $status);
	tpSetCart('{}');

	wp_redirect(tp_booking_status_url($status));
	exit;
}

/*
 * handle payment-response CGI page (which has no customer session - CGI from axes)
*/
function tpAxesPaymentResponse()
{
	// store response for a given time

	tp_log('tp-payment-response axes response=' . print_r($_POST, true));
}

?>
