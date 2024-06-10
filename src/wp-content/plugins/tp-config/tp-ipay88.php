<?php

function tpIPay88RequestTemplate($agentref, $amount) {

	return	'<form id="tpcardform" method="POST" action="'. get_option('tp_ipay88_url') .'">'
		.	'<h3>Billing Details</h3>'
		.	'<div class="form-horizontal">'
			.	'<div class="form-group">'
				.	'<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-8">'
				.	'<input id=copyButton" type="button" onclick="copyFromCustomer(); return false;" value="Copy values from above"  class="btn btn-block"/>'
				.	'</div>'
			.	'</div>'
			.	'<input type="hidden" name="MerchantCode" value="' . get_option('tp_ipay88_merchant_code') . '" />'
			.	'<input type="hidden" name="PaymentId" />'
			.	'<input type="hidden" name="RefNo" value="' . $agentref . '" />'
			.	'<input type="hidden" name="Amount" value="' . $amount . '" />'
			.	'<input type="hidden" name="Currency" value="' . get_option('tp_currency') . '"/>'
			.	'<input type="hidden" name="ProdDesc" value="Order ' . $agentref . '" />'
			.	'<input type="hidden" name="Remark" />'
			.	'<input type="hidden" name="Lang" />'
			.	'<input type="hidden" name="Signature" />'
			.	'<input type="hidden" name="ResponseURL" value="' . tp_payment_url() . '"/>'
			.	'<input type="hidden" name="BackendURL" value="' . tp_home_url(false) . '/' . get_option('tp_ipay88_backend_url') . '"/>'
			.	'<div class="form-group">'
				.	'<div class="col-sm-9 col-md-8">'
				.	'<label class="control-label" for="iPay88UserName">Name</label>'
				.	'<input class="form-control" type="text" name="UserName" value="" />'
				. 	'</div>'
				.	'<div class="col-sm-9 col-md-8">'
				.	'<label class="control-label" for="iPay88UserEmail">Email</label>'
				.	'<input class="form-control" type="text" name="UserEmail" value="" />'
				.	'</div>'
				.	'<div class="col-sm-9 col-md-8">'
				.	'<label class="control-label" for="iPay88UserContact">Phone</label>'
				.	'<input class="form-control" type="text" name="UserContact" value="" />'
				.	'</div>'
			.	'</div>'
		.	'</div>'
		.'</form>';
}

function tpIPay88RequestScript() {
	// The form need to rewrite into 'tp-paymanics' style so that we can debug on submit.
	// It's not clear that why currently the form is not functioning
	// JTB Jakarta planned to use this payment gateway, but then abandon it for some reason
	// https://www.jtbjakarta.com/
	return	'<script>'
		.'var fullName = function(fname, mname, lname) { return (fname ? fname + " " : "") + (mname ? mname + " " : "") + (lname ? lname + " " : ""); };'
		.'var copyFromCustomer = function() {'
		.	'$("#tpcardform input[name=UserName]").val(fullName($(".customerSection input[name=firstname]").val(), $(".customerSection input[name=middlename]").val(), $(".customerSection input[name=lastname]").val()));'
		.	'$("#tpcardform input[name=UserEmail]").val($(".customerSection input[name=email]").val());'
		.	'$("#tpcardform input[name=UserContact]").val($(".customerSection input[name=phone]").val());'
		.'};'
		.'var submitIPay88 = function() {'
		.	'var formData = {};'
		. 	'$.map($("#tpcardform").serializeArray(), function(x) { formData[x.name] = x.value; });'
		.	'console.log(formData);'
		.	'var controls = ["input[name=UserName]", "input[name=UserEmail]", "input[name=UserContact]"];'
		.	'valid = true;'
		.	'for (var i = 0; i < controls.length; i++) {'
		.		'var control = $(controls[i]);'
		.		'if (control.val() == "") {'
		.			'control.addClass("invalid");'
		.			'valid = false;'
		.		'} else {'
		.			'control.removeClass("invalid");'
		.		'}'
		.	'};'
		.	'if (valid) {'
		.		'$.ajax({'
		.			'type:"post",'
		.			'data:{'
		.				'preproc:"true",'
		.				'formdata:formData'
		.			'},'
		.			'success:function(x) {'
		.				'$("#tpcardform input[name=Signature]").val(x);'
		.				'$("#tpcardform").submit();'
		.			'}'
		.		'});'
		.	'}'
		.	'else {'
		.		'templatesHelper.resetCheckoutPage();'
		.	'}'
		.'};'
		.'var repriceCallback = function(x) { $("#tpcardform input[name=Amount]").val(x); };'
		.'</script>';
}

function tpIPay88Preprocess($payload) {
	$concat = get_option('tp_ipay88_merchant_key')
		. $payload['MerchantCode']
		. $payload['RefNo']
		. $payload['Amount']
		. $payload['Currency'];

	$signature = hash('sha1', $concat, true);

	$base64Encoded = base64_encode($signature);
	tp_log("ipay88 s payload: " . print_r($payload, true));
	tp_log("ipay88 signature: " . $signature);
	tp_log("ipay88 encoded s: " . $base64Encoded);
	return $base64Encoded;
}

function tpIPay88VerifySignature($response) {
	$concat = get_option('tp_ipay88_merchant_key')
		. $response['MerchantCode']
		. $response['PaymentId']
		. $response['RefNo']
		. $response['Amount']
		. $response['Currency']
		. $response['Status'];

	$testSignature = base64_encode(hash('sha1',$concat,true));
	return $testSignature == $response['Signature'];
}

function tpIPay88PaymentResponse() {
	$agentref = $_SESSION['tpagentref'];

	if (tpIPay88VerifySignature($_POST) && $_POST['Status'] == 1) {
		tp_log('tp-payment-success ipay88 tpagentref=' . $agentref);

		$cartArr = tpGetCartArray();
		$cart = tpAddFees($cartArr, $cartArr['deliveryMethod']);
		$cart['payment'] = print_r($_POST, true);

		tp_log("JZ_DEBUG cart: " . print_r($cart, true));

		$bookingResp = tpMakeBooking($cart, $agentref);
		tp_log('bookingresp: ' . print_r($bookingResp, true));

		$bookingRef = tpBookingRef($bookingResp);
		tp_log('bookingref: ' . print_r($bookingref, true));

		$status = tpBookingRetailStatus($bookingResp);
		
		tpSetCart('{}');

		wp_redirect(tp_booking_status_url($status));
		exit;
	} else {
		tp_log('tp-payment-fail ipay88 tpagentref=' . $agentref);
		wp_redirect(tp_payment_failed_url() . '/?error=Payment Failed: ' . $_POST['ErrDesc']);
	}
}

function tpIPay88BackendPage() {
	tp_log('tp-payment ipay88 backendpost: ' . print_r($_POST, true));
	echo "RECEIVEOK";
	exit();
}