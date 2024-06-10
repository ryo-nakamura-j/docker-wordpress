<?php

require_once('libs/stripe/Stripe.php');

function tpStripeRequestTemplate($amount) { ?>

<form id="tpcardform" method="POST" action="">
	<input type="hidden" name="token" value="" />
	<input type="hidden" name="amount" value="<?php echo $amount; ?>" />
</form>

<?php 
}


function tpStripeRequestScript($agentref) { ?>

<script src="https://checkout.stripe.com/checkout.js"></script>

<script>
	var handler = StripeCheckout.configure({
		key: "<?php echo get_option('tp_stripe_merchant_public_key'); ?>",
		image: "<?php echo get_option('tp_stripe_merchant_image'); ?>",
		locale: "auto",
		token: function(token) {
			$("body").mask("");
			$("#tpcardform input[name=token]").val(token.id);
			$("#tpcardform").submit();
		}
	});

	function tpStripeSubmit() {
		<?php
		if (tpCartIsOnRequest()) { ?>
			$("#tpcardform").submit();
		<?php } else { ?>
			handler.open({
				name: "<?php echo get_option('tp_stripe_merchant_name'); ?>",
				description: "Order <?php echo $agentref; ?>",
				amount: $("#tpcardform input[name=amount]").val(),
				currency: "<?php echo get_option('tp_currency'); ?>",
				closed: function() {
					templatesHelper.resetCheckoutPage();
				}
			});
		<?php
		}
		?>
	}

	function repriceCallback(x) {
		$("#tpcardform input[name=amount]").val(x);
	}

	window.tpCartSubmit = tpStripeSubmit;
	window.tpCartRepriceCallback = repriceCallback;
</script>

<?php 
}

function tpStripeCheckout($cart, $agentRef, $priceCents, $token) {

	try {
		Stripe::setApiKey(get_option('tp_stripe_merchant_secret_key'));
		$authStatus = Stripe_Charge::create(array(
			"amount" => $priceCents,
			"currency" => get_option('tp_currency'),
			"source" => $token,
			"description" => "Order " . $agentRef,
			"capture" => false
			)
		);

		if ($authStatus->status === "succeeded") {
			tp_log('tp-auth-success stripe tpagentref=' . $agentRef);

			$cartArr = tpGetCartArray();
			$cart = tpAddFees($cartArr, $cartArr['deliveryMethod']);
			$cart['payment'] = print_r($authStatus, true);

			$bookingResp = tpMakeBooking($cart, $agentRef);
			tp_log('bookingresp: ' . print_r($bookingResp, true));

			$bookingRef = tpBookingRef($bookingResp);
			tp_log('bookingref: ' . print_r($bookingRef, true));

			$status = tpBookingRetailStatus($bookingResp);

			if ($status == 'confirmed') {
				$charge = Stripe_Charge::retrieve($authStatus->id);

				$captureStatus = $charge->capture();

				if ($captureStatus->status != 'succeeded') {
					tp_log('stripe capture failed: ' . print_r($captureStatus, true));
					wp_redirect(tp_payment_failed_url() . '/?error=Payment Completion Failed' . $captureStatus->failure_message);
					exit;
				}
			}

			tpSetCart('{}');
			wp_redirect(tp_booking_status_url($status));
		} else {
			tp_log('stripe auth failed: ' . print_r($captureStatus, true));
			wp_redirect(tp_payment_failed_url() . '/?error=Payment Completion Failed' . $authStatus->failure_message);
		}
	}
	catch (Stripe_CardError $e) {
		$body = $e->getJsonBody();
		$err = $body['error'];

		tp_log('stripe card failed: ' . print_r($err,true));
		wp_redirect(tp_payment_failed_url() . '?error=' . urlencode($err['message']));
	}
	catch (Exception $e) {

		tp_log('stripe payment exception: ' . print_r($e, true));
		wp_redirect(tp_payment_failed_url() . '?error=Payment Failed');
	}
}