<?php

require_once('tp-payment.php');

// Page handler
function tp_checkout_page()
{
	$cart = tpGetCartArray();
	$emptycart = !(isset($cart) && isset($cart['servicelines']));

	$agentref = tpAgentRef();
	set_transient("lang_" . $agentref, tp_cur_language_exts(), HOUR_IN_SECONDS);
	tp_log('Page language -' . tp_cur_language_exts() . ' for agent -' . $agentref . ' -' . get_transient("lang_" . $agentref) );

	if ($emptycart)
	{
		tp_log('ERROR! tp-checkout empty cart');
		wp_redirect(tp_itinerary_url());
		exit;
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'GET')
	{
		tp_checkout_show($cart, $agentref);
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preproc']) && $_POST['preproc'] === 'true')
	{
		tp_generate_signature($_POST['formdata']);
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		tp_checkout_process($cart, $agentref);
	}
}

function tp_generate_signature($payload) {
	$paymentgateway = get_option('tp_payment_gateway');

	switch ($paymentgateway) {
		case 'paynamics':
			echo tpPaynamicsPreData($payload);
			exit;
			break;
		case 'ipay88':
			echo tpIPay88Preprocess($payload);
			exit;
			break;
		default:
			break;
	}
}

function tp_checkout_show($cart, $agentref)
{
	$priceCents = tpPriceBooking($cart);
	$_SESSION['tpprice'] = $priceCents;
	if (!isset($priceCents) || $priceCents<=0)
	{
		tp_log('ERROR! tp-checkout invalid price for: ' . print_r($cart, true));
		wp_redirect(tp_itinerary_url());
		exit;
	}

	$paymentgateway = get_option('tp_payment_gateway');
	
	switch ($paymentgateway)
	{
		case 'axes':
			$_SESSION['tpagentref'] = $agentref;
			break;

		case 'cfb':
			$_SESSION['tpagentref'] = $agentref;
			break;

		case 'eway':
			//wait for payment-response postback
			break;

		case 'dps':
			//wait for payment-response postback
			break;

		case 'pbb':
			//wait for payment-response postback
			break;
			
		case 'aeon':
			//wait for payment-response postback
			break;
			
		case 'credomatic':
			//wait for payment-response postback
			break;

		case 'paynamics':
			$_SESSION['tpagentref'] = $agentref;
			break;

		case 'ipay88':
			$_SESSION['tpagentref'] = $agentref;
			break;

		case 'stripe':
			$_SESSION['tpagentref'] = $agentref;

		default: break;
	}
}

function tp_checkout_process($cart, $agentref)
{
	if (tpHasBookingFees($cart))
	{
		tp_log('cart has booking fees ' . print_r($cart, true));
		$cart = tpAddBookingFees($cart);
	}
	if (tpHasDeliveryFees($cart))
	{
		tp_log('tp-checkout (POST) cart has delivery fees deliveryMethod=' . $cart['deliveryMethod'] . ', cart=' . print_r($cart, true));
		$cart = tpAddDeliveryFees($cart, $cart['deliveryMethod']);
	}

	$priceCents = tpPriceBooking($cart);
	$_SESSION['tpprice'] = $priceCents;
	if (!isset($priceCents) || $priceCents<=0)
	{
		tp_log('ERROR! tp-checkout invalid price for cart=' . print_r($cart, true));
		wp_redirect(tp_itinerary_url());
		exit;
	}

	$_SESSION['tpagentref'] = $agentref;
	$paymentgateway = get_option('tp_payment_gateway');
	$paymentalt = get_option('tp_payment_alt') === 'true' && $_REQUEST['tppaymentalt'] === 'true';
	tp_log('DEBUG: processing checkout for agentref=' . $_SESSION['tpagentref'] . ' priceCents=' . $priceCents . ' gateway=' . $paymentgateway
	. ' paymentalt=' . get_option('tp_payment_alt') . ' paymentaltcheckbox=' . (isset($_POST['tppaymentalt']) ? $_REQUEST['tppaymentalt']:''));

	$onRequest = tpCartIsOnRequest();
	
	if ($paymentgateway === 'none' || $paymentalt || 
		(
			(	$paymentgateway === 'ipay88' || 
				$paymentgateway === 'stripe' ||
				$paymentgateway === 'paynamics' ||
				$paymentgateway === 'paydollar'
			)
			&& $onRequest
		)
	){
		tp_log('cart: ' . print_r($cart, true));
		
		if ($paymentalt) 
		{
			$cart['onrequest'] = true;
		}
		
		$bookingresp = tpMakeBooking($cart , $agentref);
		tp_log('bookingresp: ' . print_r($bookingresp, true));

		$bookingref = tpBookingRef($bookingresp);
		tp_log('bookingref: ' . print_r($bookingref, true));
		$status = tpBookingRetailStatus($bookingresp);

		if ($status !== 'failed') {
			tpSetCart('{}');
			// unset($_SESSION['tpPreAuthResponse']);
		}
		
		wp_redirect(tp_booking_status_url($status));

		exit;
	}
	else if (get_option('tp_deposit_only') === 'true' && $paymentgateway === 'eway')
	{
		$depositpercent = floatval(get_option('tp_deposit_percent', '20.0'));
		$depositsurcharge = floatval(get_option('tp_deposit_surcharge', '1.5'));
		$depositamount = intval($priceCents) * ($depositpercent / 100.0);
		$surchargeamount = $depositamount * ($depositsurcharge /100.0);
		$depositCents = round($depositamount + $surchargeamount, 0, PHP_ROUND_HALF_DOWN);

		tp_log('DEBUG: deposit only payment for agentref=' . $_SESSION['tpagentref']
		. ' deposit%=' . $depositpercent . ' depositSurcharge=' . $depositsurcharge
		. ' depositAmount=' . $depositamount . ' surchargeAmount=' . $surchargeamount
		. ' (priceCents=' . $priceCents . ') depositCents=' . $depositCents);

		$eWayPGW = new eWayPaymentGateway();
		$eWayPGW -> start( $cart, $agentref, $depositCents, true );
	}
	else
	{
		if (tpHasPaymentFees($cart))
		{
			//change cart total and add paymentfee json
			$agentTax = $cart['paymentfee']['AgentTax'];
			$paymentfee = $cart['paymentfee']['AgentExclusive'] + $agentTax;
			if ($paymentfee > 0)
			{
				$priceCents += $paymentfee;
				$_SESSION['tpprice'] = $priceCents;
				$cart['price'] = $priceCents;
				tp_log('DEBUG: add payment fee agentref=' . $_SESSION['tpagentref'] . ' fee=' . $paymentfee . ' tax='. $agentTax . ' priceCents=' . $priceCents . ' gateway=' . $paymentgateway . ' cardtype=' . $cardtype);
			}
		}

		switch ($paymentgateway)
		{
			case 'axes':
				//goes to hosted page
				break;

			case 'cfb':
				//goes to hosted page
				break;

			case 'paynamics':
				// tpPaynamicsCheckout($cart, $agentref, $pricecents);
				break;

			case 'eway':
				$eWayPGW = new eWayPaymentGateway();
				$eWayPGW -> start( $cart, $agentref, $priceCents, false );
				break;

			case 'dps':
				tpDPSCheckout($cart, $agentref, $priceCents);
				break;

			case 'pbb':
				tpPBBCheckout($cart, $agentref, $priceCents);
				break;
				
			case 'aeon':
				tpAEONCheckout($cart, $agentref, $priceCents);
				break;

			case 'stripe':
				tpStripeCheckout($cart, $agentref, $priceCents, $_POST['token']);
				break;
				
			case 'credomatic':
				tpCredomaticCheckout($cart, $agentref, $priceCents);
				break;

			case 'paydollar':
				tpPayDollarCheckout($cart, $agentref, $priceCents);
				break;
				
			default: break;
		}
	}
}

function tp_creditcard_form_template() {
	$cardform = '<!-- card default -->';
	$defaultCardform = '
	<div class="row">
		<div class="col-xs-12">
			<h3>Credit Card Details</h3>
			<div class="ribon-red-desktop"></div>
		</div>
	</div>
	<form id="tpcreditcardform" class="form-horizontal" method="POST">
		<div class="form-group required">
			<label for="creditcard_number" class="col-sm-3 col-md-2 control-label">Card Number</label>
			<div class="col-sm-9 col-md-8">
				<input 
					id="creditcard_number" 
					type="text" 
					name="CARDNUMBER" 
					class="form-control" 
					maxlength="16"
					/>
			</div>
		</div>
		<div class="form-group required">
			<label for="creditcard_holder" class="col-sm-3 col-md-2 control-label">Card Holder</label>
			<div class="col-sm-9 col-md-8">
				<input 
					id="creditcard_holder" 
					type="text" 
					name="CARDNAME" 
					class="form-control" 
					/>
			</div>
		</div>
		<div class="form-group required">
			<label class="col-sm-3 col-md-2 control-label">Expiry Date</label>
			<div class="col-sm-9 col-md-8">
				<select 
					id="creditcard_expirymonth" 
					name="CARDEXPIRYMONTH" 
					class="form-control">
				</select>
				<select 
					id="creditcard_expiryyear" 
					name="CARDEXPIRYYEAR" 
					class="form-control">
				</select>
			</div>
		</div>
		<div class="form-group required">
			<label for="creditcard_cvn" class="col-sm-3 col-md-2 control-label">Card Verification</label>
			<div class="col-sm-9 col-md-8">
				<input 
					id="creditcard_cvn" 
					type="text" 
					name="CARDCVN" 
					class="form-control" 
					maxlength="4"
					/>
			</div>
		</div>
	</form>';
    $paymentgateway = get_option('tp_payment_gateway');
	switch ($paymentgateway)
	{
   		case 'pbb':
   			$cardform = '<div id="tpcreditcardsection">' . $defaultCardform . '</div>';
   			break;

		case 'cfb':
			$cart = tpGetCartArray();
			$priceCents = tpPriceBooking($cart);
			$cardform = tpCFBCheckoutForm($priceCents);
			break;

        case 'axes':
	      	$cart = tpGetCartArray();
      	  	$priceCents = tpPriceBooking($cart);
			$agentref = $_SESSION['tpagentref'];
			$email = $cart['email'];
			$phone = $cart['phone'];
		    $cardform = tpAxesCheckoutFormTemplate($priceCents, $agentref, $email, $phone);
		  	break;

		case 'paynamics':
			$cart = tpGetCartArray();
			$priceCents = tpPriceBooking($cart);
			$agentref = $_SESSION['tpagentref'];
			$cardform = tpPaynamicsCheckoutFormTemplate($priceCents, $agentref);
			break;

		case 'ipay88':
			$cart = tpGetCartArray();
			$priceCents = tpPriceBooking($cart);
			$agentref = $_SESSION['tpagentref'];
			$onRequest = tpCartIsOnRequest();
			$cardform = $onRequest ? '':tpIPay88RequestTemplate($agentref, $priceCents);
			break;

		case 'stripe':
			$cart = tpGetCartArray();
			$priceCents = tpPriceBooking($cart);
			$cardform = tpStripeRequestTemplate($priceCents);
			break;
			
		default: break;
	}
	return $cardform;
}

function tp_creditcard_form_script() {
    $paymentgateway = get_option('tp_payment_gateway');
	switch ($paymentgateway)
	{
   		case 'pbb':
   			$cardform = '<script>window.tpCartSubmit = function() { $("#tpcreditcardform").submit(); }</script>';
   			break;

        case 'axes':
		    $cardform = tpAxesCheckoutFormScript();
		  	break;

		case 'paynamics':
			$cardform = tpPaynamicsCheckoutFormScript();
			break;

		case 'ipay88':
			$onRequest = tpCartIsOnRequest();
			$cardform = $onRequest ? '':tpIPay88RequestScript();
			break;

		case 'stripe':
			$agentref = $_SESSION['tpagentref'];
			$cardform = tpStripeRequestScript($agentref);
			break;
			
		default: break;
	}
	return $cardform;
}

// Shortcode for creditcard form
function tp_creditcard_form()
{
	// Separate the template and the script to enable the possibility of integrating payment gateways with Vue
	return tp_creditcard_form_template() . tp_creditcard_form_script();
}
