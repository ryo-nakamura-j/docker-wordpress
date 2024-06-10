<?php 

function tpPayDollarCheckout($cart, $agentref, $priceCents) {
	$priceDollars = number_format(($priceCents / 100.00), 2, '.', '');
	$dftLang = "E";
	$languageMapping = array( 
		"" => $dftLang,
		"en" => "E",
		"ch" => "X",
		"zh" => "C",
		"ja" => "J",
		"th" => "T",
		"vi" => "V" );
	$langPayment = $languageMapping[ tp_cur_language_exts() ];
	if ( !isset($langPayment) )
		$langPayment = $dftLang;

	$inputs = array(
		"orderRef" => $agentref,
		"currCode" => tpPayDollarCurrency(get_option("tp_currency")),
		"amount" => $priceDollars,
		"lang" => $langPayment,
		"cancelUrl" => tp_payment_failed_url(),
		"failUrl" => tp_payment_failed_url(),
		"successUrl" => tp_payment_success_url(),
		"merchantId" => get_option("tp_paydollar_merchant_id"),
		"payType" => "N",
		"payMethod" => "ALL"//,
		// "secureHash" => tpPayDollarSecureHash($priceDollars)
	);

	if (!empty(get_option("tp_paydollar_secure_hash_secret"))) {
		$inputs["secureHash"] = tpPayDollarSecureHash($agentref, $priceDollars);
	}

	$formInputs = array_map("tpInputElement", array_keys($inputs), array_values($inputs));
	
	$form = '<form id="tpcardform" method="POST" action="' . get_option('tp_paydollar_payment_url') . '">'
		  . implode("", $formInputs)
		  . '</form>'
		  . tpAutoSubmitFormScript("tpcardform");

	error_log("PAYDOLLAR FORM: " . $form);

	set_transient("cart_" . $agentref, $cart, HOUR_IN_SECONDS);

	echo $form;
	exit;
}

function tpPayDollarCurrency($currencyCode) {
	$currencies = array(
		"HKD" => "344",
		"CNY" => "156",
		"RMB" => "156",
		"AUD" => "036",
		"CAD" => "124",
		"THB" => "764",
		"KRW" => "410",
		"AED" => "784",
		"INR" => "356",
		"USD" => "840",
		"JPY" => "392",
		"ERU" => "978",
		"MOP" => "446",
		"MYR" => "458",
		"SAR" => "682",
		"BND" => "096",
		"SGD" => "702",
		"TWD" => "901",
		"GBP" => "826",
		"PHP" => "608",
		"IDR" => "360",
		"NZD" => "554",
		"VND" => "704"
	);

	return $currencies[$currencyCode];
}

function tpPayDollarSecureHash($agentref, $priceDollars) {
	$hashInputs = array(
		get_option("tp_paydollar_merchant_id"),
		$agentref,
		tpPayDollarCurrency(get_option("tp_currency")),
		$priceDollars,
		"N",
		get_option("tp_paydollar_secure_hash_secret"));

	return sha1(implode("|", $hashInputs));
}

function tpInputElement($name, $value) {
	return '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
}

function tpAutoSubmitFormScript($formName) {
	return '<script>document.forms["' . $formName . '"].submit();</script>';
}

function tpPayDollarPaymentResponse() {
	if ($_SERVER['REQUEST_METHOD'] === 'GET' || empty($_POST)) {
		tp_log("PAYDOLLAR BROWSER_REDIRECT");
		error_log("PAYDOLLAR GET: " . print_r($_GET, true));

		$agentRef = $_GET["Ref"];

		$bookingResponse = false;
		$start_time = time();

		error_log("PAYDOLLAR load transient saved by PAYDOLLAR postback. If it times out, it's most likely that PAYDOLLAR account is not setting up posting to this site. Waiting for " . $agentRef);
		while ($bookingResponse == false && (time() - $start_time < 180)) {
			$bookingResponse = get_transient("booking_" . $agentRef);
		}

		$booking = is_array($bookingResponse) && $bookingResponse["booking"];

		if (!$bookingResponse || !$booking) {
			wp_redirect(tp_payment_failed_url());
		}
		else {
			$bookingRef = tpBookingRef($bookingResponse);

			$status = tpBookingRetailStatus($bookingResponse);
			tpSetCart('{}');
			wp_redirect(tp_booking_status_url($status));
		}
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		ob_start();
		echo "OK";
		$size = ob_get_length();
		header("Content-Encoding: none");
		header("Content-Length: {$size}");
		header("Connection: close");
		ob_end_flush();
		ob_flush();
		flush();

		tp_log("JZ_DEBUG PAYNAMICS POSTBACK");
		error_log("PAYDOLLAR_POST: " . print_r($_POST, true));
		
		$agentref = $_POST["Ref"];


		if ($_POST["successcode"] == 0) {	
			$cartName = "cart_" . $agentref;
			$cartArr = get_transient($cartName);

			$cart = tpAddFees($cartArr, $cartArr["deliveryMethod"]);
			$cart["payment"] = $_POST;

			$bookingResp = tpMakeBooking($cart, $agentref);
			tp_log('bookingresp: ' . print_r($bookingResp, true));

			$bookingRef = tpBookingRef($bookingResp);
			tp_log('bookingref: .' . print_r($bookingRef, true));

			set_transient("booking_" . $agentref, $bookingResp, HOUR_IN_SECONDS);

			$status = tpBookingRetailStatus($bookingResp);

		} else {
			set_transient("booking_" . $agentref, $_POST, HOUR_IN_SECONDS);
		}


		exit;
	}

}

function tpPayDollarErrorMessage($prcVal, $srcVal) {
	$primaryResponseCodes = array(
		"0" => "Success",
		"1" => "Rejected by Payment Bank",
		"3" => "Rejected due to Payer Authentication Failure (3D)",
		"-1" => "Rejected due to Input Parameters Incorrect",
		"-2" => "Rejected due to Server Access Error",
		"-8" => "Rejected due to PayDollar Internal/Fraud Prevention Checking",
		"-9" => "Rejected by Host Access Error"
	);

	$secondaryResponseCodes = array(
		"1" => array(
			"01" => "Bank Decline",
			"02" => "Bank Decline",
			"03" => "Other",
			"04" => "Other",
			"05" => "Bank Decline",
			"12" => "Other",
			"13" => "Other",
			"14" => "Input Error",
			"19" => "Other",
			"25" => "Other",
			"30" => "Other",
			"31" => "Other",
			"41" => "Lost / Stolen Card",
			"43" => "Lost / Stolen Card",
			"51" => "Bank Decline",
			"54" => "Input Error",
			"55" => "Other",
			"58" => "Other",
			"76" => "Other",
			"77" => "Other",
			"78" => "Other",
			"80" => "Other",
			"89" => "Other",
			"91" => "Other",
			"94" => "Other",
			"95" => "Other",
			"96" => "Other",
			"99" => "Other",
			"2000" => "Other"
		),
		"-8" => array(
			"999" => "Other",
			"1000" => "Skipped transaction",
			"2000" => "Blacklist error",
			"2001" => "Blacklist card by system",
			"2002" => "Blacklist card by merchant",
			"2003" => "Black IP by system",
			"2004" => "Black IP by merchant",
			"2005" => "Invalid cardholder name",
			"2006" => "Same card used more than 6 times a day",
			"2007" => "Duplicate merchant reference no.",
			"2008" => "Empty merchant reference no.",
			"2011" => "Other",
			"2012" => "Card verification failed",
			"2013" => "Card already registered",
			"2014" => "High risk country",
			"2016" => "Same payer IP attempted more than pre-definedno. a day.",
			"2017" => "Invalid card number",
			"2018" => "Multi-card attempt",
			"2019" => "Issuing Bank not match",
			"2020" => "Single transaction limit exceeded",
			"2021" => "Daily transaction limit exceeded",
			"2022" => "Monthly transaction limit exceeded",
			"2023" => "Invalid channel type",
			"2099" => "Non testing card",
			"2031" => "System rejected(TN)",
			"2032" => "System rejected(TA)",
			"2033" => "System rejected(TR)"
		)
	);

	$prc = $primaryResponseCodes[$prcVal];
	$src = null;

	if ($prc == "1" || $prc == "-8") {
		$src = $secondaryResponseCodes[$prcVal][$srcVal];
	}

	$msg = $prc;
	if ($src != null) {
		$msg += " (" + $src + ")";
	}

	return $msg;
}