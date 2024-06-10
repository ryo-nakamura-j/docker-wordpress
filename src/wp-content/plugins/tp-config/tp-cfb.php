<?php

/* ********************************* CFB - Chinese First Bank ************************************** */
/* the CFB gateway has a hidden form on the checkout page that posts price and mechant info 
 * to CFB hosted pages for the client to enter credit card data. CFB redirects back to payment-response.
 * payment-response will make booking.
 */

$cfbStatuses = array(
	'0' => 'success',
	'4' => 'rejected',
	'8' => 'error'
);

$cfbErrors = array(
    '00' => 'SUCCESS: Transaction Approved',
	'01' => 'FAILED: Refer to Issuer',
	'02' => 'FAILED: Refer to Issuer, special',
	'03' => 'FAILED: INVALID Merchant',
	'04' => 'FAILED: Pick Up Card Closed',
	'05' => 'FAILED: Do Not Honour',
	'06' => 'FAILED: Error',
	'07' => 'FAILED: CAPTURE CARD',
	'08' => 'FAILED: REVERSED TO ISO USE',
	'09' => 'FAILED: REVERSED TO ISO USE',
	'10' => 'FAILED: REVERSED TO ISO USE',
	'11' => 'FAILED: REVERSED TO ISO USE',
	'12' => 'FAILED: REVERSED TO ISO USE',
	'13' => 'FAILED: Invalid Amount',
	'14' => 'FAILED: Invalid Card Number',
	'15' => 'FAILED: INVALID Issuer',
	'16' => 'FAILED: REVERSED TO ISO USE',
	'17' => 'FAILED: REVERSED TO ISO USE',
	'18' => 'FAILED: REVERSED TO ISO USE',
	'19' => 'FAILED: Re-enter Transaction',
	'20' => 'FAILED: INVALD RESPONSE',
	'30' => 'FAILED: Timeout/Format Error',
	'31' => 'FAILED: Bank Not Supported By Switch',
	'32' => 'FAILED: COMPLETED PARTIALLY',
	'33' => 'FAILED: Expired Card, Capture',
	'34' => 'FAILED: Suspected Fraud, Retain Card',
	'35' => 'FAILED: CALL HELP',
	'36' => 'FAILED: Restricted Card, Retain Card',
	'37' => 'FAILED: CALL HELP',
	'38' => 'FAILED: PIN Tries Exceeded, Capture',
	'39' => 'FAILED: No Credit Account',
	'40' => 'FAILED: Request Not Supported',
	'41' => 'FAILED: Lost Card',
	'42' => 'FAILED: No Universal Account',
	'43' => 'FAILED: Stolen Card',
	'44' => 'FAILED: No Investment Account',
	'45' => 'FAILED: REVERSED TO ISO USE',
	'46' => 'FAILED: REVERSED TO ISO USE',
	'47' => 'FAILED: REVERSED TO ISO USE',
	'48' => 'FAILED: REVERSED TO ISO USE',
	'49' => 'FAILED: REVERSED TO ISO USE',
	'50' => 'FAILED: REVERSED TO ISO USE',
	'51' => 'FAILED: Insufficient Funds',
	'52' => 'FAILED: REVERSED TO ISO USE',
	'54' => 'FAILED: Card not open/Expired',
	'55' => 'FAILED: Incorrect PIN',
	'56' => 'FAILED: No Card Record',
	'57' => 'FAILED: Transaction Not Permitted to Cardholder',
	'58' => 'FAILED: Transaction Not Permitted to Terminal',
	'59' => 'FAILED: Suspected Card',
	'60' => 'FAILED: Call Help',
	'61' => 'FAILED: Exceeds Withdrawal',
	'62' => 'FAILED: Restricted Card',
	'63' => 'FAILED: CVC Error',
	'64' => 'FAILED: Original Amount Incorrect',
	'65' => 'FAILED: Exceeds Withdrawal',
	'66' => 'FAILED: Call Help',
	'67' => 'FAILED: Hard Capture',
	'68' => 'Response Recieved Too Late',
	'69' => 'FAILED: REVERSED TO ISO USE',
	'70' => 'FAILED: REVERSED TO ISO USE',
	'71' => 'FAILED: REVERSED TO ISO USE',
	'72' => 'FAILED: REVERSED TO ISO USE',
	'73' => 'FAILED: REVERSED TO ISO USE',
	'74' => 'FAILED: REVERSED TO ISO USE',
	'75' => 'FAILED: PIN Tries Exceeded',
	'76' => 'FAILED: REVERSED TO ISO USE',
	'77' => 'FAILED: REVERSED TO ISO USE',
	'78' => 'FAILED: REVERSED TO ISO USE',
	'79' => 'FAILED: REVERSED TO ISO USE',
	'80' => 'FAILED: REVERSED TO ISO USE',
	'81' => 'FAILED: REVERSED TO ISO USE',
	'82' => 'FAILED: REVERSED TO ISO USE',
	'83' => 'FAILED: REVERSED TO ISO USE',
	'84' => 'FAILED: REVERSED TO ISO USE',
	'85' => 'FAILED: REVERSED TO ISO USE',
	'86' => 'FAILED: REVERSED TO ISO USE',
	'87' => 'FAILED: REVERSED TO ISO USE',
	'88' => 'FAILED: REVERSED TO ISO USE',
	'89' => 'FAILED: Invalid Terminal',
	'90' => 'FAILED: System Not Available',
	'91' => 'FAILED: Issuer or Switch is Inoperative',
	'92' => 'FAILED: Network Routing Error',
	'93' => 'FAILED: Violation Of Law',
	'94' => 'FAILED: Duplicate Transmission',
	'95' => 'FAILED: Reconcile Error',
	'96' => 'FAILED: Financial Institude or Intermediate network facility can not be found',
	'N7' => 'FAILED: Card details error',
	'I1' => 'FAILED: Unsupported installment transaction',
	'I2' => 'FAILED: Unsupported installment period',
	'I3' => 'FAILED: Unsupported Card Number',
	'I4' => 'FAILED: Expired Card',
	'I5' => 'FAILED: Installment not active',
	'I6' => 'FAILED: Original Transaction Cancelled',
	'I7' => 'FAILED: Original Transaction Not Found',
	'I8' => 'FAILED: System or Error',
	'I9' => 'FAILED: Card details error',
	'IA' => 'FAILED: Card details error',
	'Others' => 'FAILED: Call Issuer Bank Service');

function tpCFBRequest($autoCap, $purchAmt)
{
   return '<form id="tpcardform" method="POST" action="' . get_option('tp_cfb_url') . '" style="display:none;">'
	    . '<input type="hidden" name="MerchantID" value="' . get_option('tp_cfb_merchant') . '" />'
	    . '<input type="hidden" name="TerminalID" value="' . get_option('tp_cfb_terminal') . '" />'
	    . '<input type="hidden" name="merID" utf8_decode value= "' . get_option('tp_cfb_merid') . '" />'
	    . '<input type="hidden" name="MerchantName" value= "' . get_option('tp_cfb_merchantname') . '" size= 100 />'
	    . '<input type="hidden" name="customize" value="2" />'
	    . '<input type="hidden" name="purchAmt" value="' . $purchAmt . '" />'
	    . '<input type="hidden" name="lidm" value="123456" />'
	    . '<input type="hidden" name="AuthResURL" value="' . tp_payment_url() . '" >'
	    . '<input type="hidden" name="AutoCap" value="'. $autoCap .'" />'
	    . '</form>';
}

function tpCFBComplete($amount)
{
   return tpCFBRequest('1', $amount);
}

function tpCFBAuthorise($amount)
{
   return tpCFBRequest('0', $amount);
}

/*
 * construct the creditcard form that posts to CFB and is hidden on the checkout page
 */
function tpCFBCheckoutForm($priceCents)
{
  return tpCartIsConfirmed() ? tpCFBComplete($priceCents) : tpCFBAuthorise($priceCents);
}

function tpCFBResponse($response)
{
	if (empty($response))
	{
		return array('status'=>'failed', 'error' => 'Missing Payment Response');
	}
	else
	{
		$status = $response['status'];
		$errcode = $response['errcode'];
		return array('status' => $status === '0' && $errcode === '00' ? 'success' : 'failed', 				
				     'transactionId'=> $response['xid'],
				     'error' => $cfbStatuses[$status] . ' : ' . $cfbErrors[$errcode],
				     'response' => print_r($response, true));
	}
}

/*
 * handle payment-response page
*/
function tpCFBPaymentResponse()
{
	$resp = tpCFBResponse($_POST);
	$agentref = $_SESSION['tpagentref'];
    $_SESSION['tppaymentresponse'] = '';
	
	tp_log('tpCFBPaymentResponse cfb tpagentref=' . $agentref . ' status=' . $resp['status'] . ' response=' . print_r($resp, true));
	
	if ($resp['status'] === 'success')
	{
		$_SESSION['tppaymentresponse'] = print_r($_POST,true);
		tp_log('tpCFBPaymentResponse cfb tpagentref=' . $agentref . ' clientside redirect=' . tp_payment_success_url());
        echo '<script>window.location="' .  tp_payment_success_url() . '";</script>';
		exit;
	}
	else
	{
		tp_log('tp-payment-response.php cfb payment-failure agentref=' . $agentref . ' response=' . print_r($resp, true));
		wp_redirect(tp_payment_failed_url() . '/?error=Payment Completion Failed: ' . $resp['error']);
		exit;
	}
}

function tpCFBPaymentSuccess() 
{
    $agentref = $_SESSION['tpagentref'];
    $paymentresp = $_SESSION['tppaymentresponse'];
    if (!empty($agentref) && !empty($paymentresp))
    {
      $cartarr = tpGetCartArray();
      $cart = tpAddFees($cartarr, $cartarr['deliveryMethod']);
      $cart['payment'] = $paymentresp;
      $bookingresp = tpMakeBooking($cart , $agentref);
      $bookingref = tpBookingRef($bookingresp);
      
      $status = tpBookingRetailStatus($bookingresp);
      tp_log('tpCFBPaymentSuccess bookingref=' . print_r($bookingref, true) . ' bookingstatus=' . $status);
      tpSetCart('{}');
      wp_redirect(tp_booking_status_url($status));
      exit;
    }
}

?>