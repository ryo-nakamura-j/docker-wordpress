<?php 

function tpPaynamicsRequestTemplate($auth, $priceDollars, $agentref){
	$countryOptions = "";

	foreach(explode(',', get_option('tp_paynamics_country_list')) as $country) {
		$countryOptions .= "<option value='" . substr($country, 0, 2) . "'>" . $country . "</option>";
	}

	if (tpCartIsOnRequest()) { 

		return <<<HTML
		<form id="tpcardform" method="POST" action=""></form>
HTML;
	}

	else { 
		ob_start();
	?>
	<!-- <div class="row"> -->
	<form id="tpcardform" class="paynamics" method="POST">
		<h3>Billing Details</h3>
		<div  class="form-horizontal">
		<div class="form-group">
			<div class="col-sm-offset-3 col-md-offset-2 col-sm-9 col-md-8">
				<input id="copyButton" type="button" class="btn btn-block" onclick="copyFromCustomer(); return false;" value="Copy values from above"/>
			</div>	
		</div>
		<input type="hidden" name="mid" value="<?php echo get_option('tp_paynamics_merchant_id'); ?>" />
		<input type="hidden" name="request_id" value="<?php echo $agentref; ?>" />
		<input type="hidden" name="notification_url" value="<?php echo tp_payment_url(); ?>" />
		<input type="hidden" name="response_url" value="<?php echo tp_payment_url(); ?>" />
		<input type="hidden" name="cancel_url" value="<?php echo tp_payment_failed_url(); ?>" />
		<input type="hidden" name="descriptor_note" value="<?php echo get_option('tp_paynamics_descriptor_note'); ?>" />
		<input type="hidden" name="ip_address" value="<?php echo get_option('tp_paynamics_site_ip'); ?>" />
		<div class="fnamesection form-group">
			<label for="tpcardformfname" class="required col-sm-3 col-md-2 control-label">First Name</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformfname" type="text" name="fname" value="" class="form-control"/>
			</div>	
		</div>
		<div class="lnamesection form-group" class="required">
			<label for="tpcardformlname" class="required col-sm-3 col-md-2 control-label">Last Name</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformlname" type="text" name="lname" value=""  class="form-control"/>
			</div>
		</div>
		<div class="address1section form-group">
			<label for="tpcardformaddress1" class="required col-sm-3 col-md-2 control-label">Address</label>
			<div class="col-sm-9 col-md-8">
			<input id="tpcardformaddress1" type="text" name="address1" value=""  class="form-control"/>
		</div>
		</div>
		<div class="address2section form-group">
			<label for="tpcardformaddress2" class="col-sm-3 col-md-2 control-label">Address 2</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformaddress2" type="text" name="address2" value=""  class="form-control"/>
			</div>
		</div>
		<div class="citysection form-group">
			<label for="tpcardformcity" class="required col-sm-3 col-md-2 control-label">City</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformcity" type="text" name="city" value=""  class="form-control"/>
			</div>
		</div>
		<div class="statesection form-group">
			<label for="tpcardformstate" class="required col-sm-3 col-md-2 control-label">State/Region</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformstate" type="text" name="state" value=""  class="form-control"/>
			</div>
		</div>
		<div class="countrysection form-group">
			<label for="tpcardformcountry" class="required col-sm-3 col-md-2 control-label">Country</label>
			<div class="col-sm-9 col-md-8">
				<select id="tpcardformcountry" name="country" class="form-control"><?php echo $countryOptions; ?>
				</select>
			</div>
		</div>
		<div class="postcodesection form-group">
			<label for="tpcardformpostcode" class="required col-sm-3 col-md-2 control-label">Post Code</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformpostcode" type="text" name="zip" value=""  class="form-control"/>
			</div>
		</div>
		<div class="emailsection form-group">
			<label for="tpcardformemail" class="required col-sm-3 col-md-2 control-label">Email Address</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformemail" type="text" name="email" value=""  class="form-control"/>
			</div>
		</div>
		<div class="phonenumbersection form-group">
			<label for="tpcardformphonenumber" class="required col-sm-3 col-md-2 control-label">Phone Number</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformphonenumber" type="text" name="phone" value=""  class="form-control"/>
			</div>
		</div>
		<div class="mobilenumbersection form-group">
			<label for="tpcardformmobilenumber" class="col-sm-3 col-md-2 control-label">Mobile Number</label>
			<div class="col-sm-9 col-md-8">
				<input id="tpcardformmobilenumber" type="text" name="mobile" value=""  class="form-control"/>
			</div>
		</div>
<!-- 		<div class="paymenttypesection form-group">
			<label for="tpcardformpaymenttype" class="col-sm-3 col-md-2 control-label">Payment Method</label>
			<div class="col-sm-9 col-md-8">
			<select id="tpcardformpaymenttype" class="form-control"></select>
		</div>
		</div> -->
		<input type="hidden" name="amount" value="<?php echo $priceDollars; ?>" />
		<input type="hidden" name="currency" value="<?php echo get_option('tp_currency'); ?>" />
		<input type="hidden" name="trxtype" value="<?php echo ($auth ? 'auth' : 'sale'); ?>" />
		<input type="hidden" name="pmethod" value="" />
		<input type="hidden" name="orders" value="" />
		<input type="hidden" name="secure3d" value="try3d" />
		<input type="hidden" name="signature" value="" />
	</div>
		</form>
	<!-- </div> -->
		<form id="tppaynamicsform" action="<?php echo get_option("tp_paynamics_url"); ?>" method="POST" style="display:none">
			<input type="hidden" name="paymentrequest" value="" />
		</form>
		<div class="cardValidationSection"><div class="validationMessage"></div></div>

	<?php
	return ob_get_clean();
	}
}


function tpPaynamicsRequestScript(){
	if (tpCartIsOnRequest()) { 

		return <<<HTML
		<script>var submitPaynamics = function() { $("#tpcardform").submit(); }</script>
HTML;
	}

	else { 
		ob_start();
	?>
	<script>
	 	var copyFromCustomer = function() {
			var controlPairs = [
				{customer: '.customerSection #checkout_firstname', cardform: '#tpcardformfname'},
				{customer: '.customerSection #checkout_middlename', cardform: '#tpcardformmname'},
				{customer: '.customerSection #checkout_lastname', cardform: '#tpcardformlname'},
				{customer: '.customerSection #checkout_address1', cardform: '#tpcardformaddress1'},
				{customer: '.customerSection #checkout_address2', cardform: '#tpcardformaddress2'},
				{customer: '.customerSection #checkout_address3', cardform: '#tpcardformcity'},
				{customer: '.customerSection #checkout_postCode', cardform: '#tpcardformpostcode'},
				{customer: '.customerSection #checkout_email', cardform: '#tpcardformemail'},
				{customer: '.customerSection #checkout_phone', cardform: '#tpcardformphonenumber'},
			];
			for (var i = 0; i < controlPairs.length; i++) { $(controlPairs[i].cardform).val($(controlPairs[i].customer).val()); }
		};
		
		var validateInputs=function(e,f){var c=$(f);c.empty();for(var c=$(f),g=!0,d=0;d<e.length;d++){var a=e[d],b=$(a.input);b.removeClass("error");if(a.required&&1>b.val().length||null!=a.isNumber&&a.isNumber&&isNaN(Number(b.val()))||null!=a.len&&(b.val().length<a.len[0]||b.val().length>a.len[1]))b.addClass("error"),c.append("<p>"+a.message+"</p>"),g=!1}return g};
		rules = [{input:"#tpcardformfname",required:true,len:null,type:null,message:"First name must be provided."	},
				{input:"#tpcardformlname",required:true,len:null,type:null,message:"Surname must be provided."		},
				{input:"#tpcardformaddress1",required:true,len:null,type:null,message:"Address must be provided"	},
				{input:"#tpcardformcity",required:true,len:null,message:"City must be provided."					},
				{input:"#tpcardformstate",required:true,len:null,message:"State/Region must be provided."			},
				{input:"#tpcardformpostcode",required:true,len:null,message:"Post code must be provided."			},
				{input:"#tpcardformemail",required:true,message:"Email address must be provided."					},
				{input:"#tpcardformphonenumber",required:true,isNumber:true,message:"Phone number must be provided."}];
		 
	 	var submitPaynamics = function() {
			console.log("submitPaynamics");
			var inputsValid = validateInputs(rules, ".cardValidationSection .validationMessage");
			console.log(inputsValid);
			if (!inputsValid) {
				templatesHelper.resetCheckoutPage();
				return false;
			}
			var formData = {};
			$.map($("#tpcardform").serializeArray(), function(x) { formData[x.name] = x.value; });
			$.ajax({
				type:"post",
				url:window.location.href,
				data:{
					preproc:true,
					formdata: formData
				},
				success:function(data){
					data = JSON.parse(data);
					console.log(data);
					$("#tppaynamicsform input[name=paymentrequest]").val(data.paymentrequest);
					$("#tppaynamicsform").submit();
				}
			})
		}

		window.tpCartSubmit = submitPaynamics;

		var repriceCallback = function(x) { $("#tpcardform input[name=amount]").val((x/100).toFixed(2));};

		var paymentFeeCallback = function(x) { 
			console.log("PFC: " + x); 
			$("#tpcardform input[name=amount]").val((x/100).toFixed(2));
			$("#tpcardform input[name=pmethod]").val($("[name=paymenttype]:checked").val());
		};

		window.tpCartRepriceCallback = repriceCallback;
		</script>

	<?php
	return ob_get_clean();
	}
}

function tpPaynamicsPreData($formdata) {
	tp_log('JZ_DEBUG PRE_DATA :' . print_r($formdata, true));
	$items = tpPaynamicsGenerateOrderXML("Order " . $formdata['request_id'], '1', $formdata['amount']);
	$xmlString = '<?xml version="1.0" encoding="utf-8" ?>'
		. '<Request>'
		. tpPaynamicsWrapXML('orders', $items)
		. tpPaynamicsWrapXML('mid', $formdata['mid'])
		. tpPaynamicsWrapXML('request_id', $formdata['request_id'])
		. tpPaynamicsWrapXML('ip_address', $formdata['ip_address'])
		. tpPaynamicsWrapXML('notification_url', $formdata['notification_url'])
		. tpPaynamicsWrapXML('response_url', $formdata['response_url'])
		. tpPaynamicsWrapXML('cancel_url', $formdata['cancel_url'])
		. tpPaynamicsWrapXML('mtac_url', get_option('tp_paynamics_mtac_url'))
		. tpPaynamicsWrapXML('descriptor_note', get_option('tp_paynamics_descriptor_note'))
		. tpPaynamicsWrapXML('fname', $formdata['fname'])
		. tpPaynamicsWrapXML('lname', $formdata['lname'])
		. tpPaynamicsWrapXML('mname', $formdata['mname'])
		. tpPaynamicsWrapXML('address1', $formdata['address1'])
		. tpPaynamicsWrapXML('address2', $formdata['address2'])
		. tpPaynamicsWrapXML('city', $formdata['city'])
		. tpPaynamicsWrapXML('state', $formdata['state'])
		. tpPaynamicsWrapXML('country', $formdata['country'])
		. tpPaynamicsWrapXML('zip', $formdata['zip'])
		. tpPaynamicsWrapXML('email', $formdata['email'])
		. tpPaynamicsWrapXML('phone', $formdata['phone'])
		. tpPaynamicsWrapXML('mobile', $formdata['mobile'])
		. tpPaynamicsWrapXML('amount', $formdata['amount'])
		. tpPaynamicsWrapXML('currency', get_option('tp_currency'))
		. tpPaynamicsWrapXML('pmethod', $formdata['pmethod'])
		. tpPaynamicsWrapXML('trxtype', $formdata['trxtype'])
		. tpPaynamicsWrapXML('mlogo_url', get_option('tp_paynamics_mlogo_url'))
		. tpPaynamicsWrapXML('secure3d', $formdata['secure3d'])
		. tpPaynamicsWrapXML('signature', tpPaynamicsGenerateSignature($formdata))
		. '</Request>';

		$base64encoded = base64_encode($xmlString);

	$returnPayload = array('paymentrequest' => $base64encoded);

	return json_encode($returnPayload);
}

function tpPaynamicsWrapXML($nodeName, $nodeValue) {
	if ($nodeValue != "") {
		return "<" . $nodeName . ">" . $nodeValue . "</" . $nodeName . ">";
	} else {
		return "<" . $nodeName . " />";
	}
}

function tpPaynamicsGenerateOrderXML($orderName, $quantity, $amount) {
	$items = '<items><Items>'
		. '<itemname>' . $orderName . '</itemname>'
		. '<quantity>' . $quantity . '</quantity>'
		. '<amount>' . $amount . '</amount>'
		. '</Items></items>';
	return $items; 
}

function tpPaynamicsGenerateSignature($formdata) {
	tp_log("JZ_DEBUG IP_ADDRESS: " . $formdata['ip_address']);

	$alwaysRQPMethods = explode(',',get_option('tp_paynamics_always_rq_pmethods'));



	$concat = $formdata['mid'] 
		. $formdata['request_id']
		. $formdata['ip_address']
		. $formdata['notification_url']
		. $formdata['response_url']
		. $formdata['fname'] 
		. $formdata['lname'] 
		. $formdata['mname']
		. $formdata['address1'] 
		. $formdata['address2']
		. $formdata['city']
		. $formdata['state']
		. $formdata['country']
		. $formdata['zip']
		. $formdata['email']
		. $formdata['phone']
		. $formdata['amount']
		. $formdata['currency'] 
		. $formdata['secure3d']
		. get_option('tp_paynamics_merchant_key');

	tp_log("JZ_DEBUG GEN_SIG CONCAT: " . $concat);

	$tempvar = null;
	if (in_array($formdata['pmethod'], $alwaysRQPMethods)) {
		$tempvar = 'order_RQ';
	} else {
		$tempvar = 'order';
	}

	set_transient($tempvar . $formdata['request_id'],
		tpGetCartArray(), 
		HOUR_IN_SECONDS);

	$signature = hash('sha512', $concat);

	tp_log("JZ_DEBUG GEN_SIG SIGNAT: " . $signature);

	return $signature;
}

function tpPaynamicsCheckoutFormTemplate($priceCents, $agentref) {
	return tpCartIsConfirmed()
		? tpPaynamicsRequestTemplate(false, tpDollars($priceCents), $agentref)
		: tpPaynamicsRequestTemplate(true, tpDollars($priceCents), $agentref);
}

function tpPaynamicsCheckoutFormScript() {
	return tpPaynamicsRequestScript();
}

function tpPaynamicsPaymentResponse() {
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		tp_log("JZ_DEBUG PAYNAMICS BROWSER_REDIRECT");
		$request_id = base64_decode($_GET['requestid']);
		$response_id = base64_decode($_GET['responseid']);
		tp_log("JZ_DEBUG PAYNAMICS $RESPONSEID: " . $response_id);
		$start_time = time();
		$bookingResponse = false;
		$alwaysRQCart = get_transient('order_RQ' . $request_id);

		if ($alwaysRQCart != false) {
			$cart = tpAddFees($alwaysRQCart, $alwaysRQCart['deliveryMethod']);

			$cart['onrequest'] = true;

			$bookingResp = tpMakeBooking($cart, $request_id);
			$bookingRef = tpBookingRef($bookingResp);
			$status = tpBookingRetailStatus($bookingResp);
			tpSetCart('{}');
			wp_redirect(tp_booking_status_url($status));
		}
		else {

			while ($bookingResponse === false && (time() - $start_time < 180)) {
				$bookingResponse = get_transient($response_id);
				sleep(2);
			}

			$booking = is_array($bookingResponse) && $bookingResponse['booking'];

			tp_log("JZ_DEBUG: " . $booking);

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
	}
	else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		tp_log("JZ_DEBUG PAYNAMICS POSTBACK");
		$returnedXML = base64_decode(str_replace(' ', '+', $_REQUEST['paymentresponse']));

		$xml = simplexml_load_string($returnedXML);
		$json = json_encode($xml);
		$array = json_decode($json);

		tp_log('tp-payment-response paynamics response=' . print_r($array, true));

	 	$requestid = $array->application->request_id;
	 	$requestStatus = $array->responseStatus->response_code;

	 	if ($requestStatus == "GR001" || $requestStatus == "GR002" | $requestStatus == "GR033") {

	 		$cartArr = get_transient('order' . $requestid);
		 	$cart = tpAddFees($cartArr, $cartArr['deliveryMethod']);

		 	$cart['payment'] = $array;

		 	tp_log('JZ_DEBUG cart: ' . print_r($cart, true));

		 	$bookingResp = tpMakeBooking($cart, $requestid);
		 	tp_log('bookingresp: ' . print_r($bookingResp, true));

		 	$bookingRef = tpBookingRef($bookingResp);
		 	tp_log('bookingref: ' . print_r($bookingRef, true));

		 	set_transient($array->application->response_id, $bookingResp, HOUR_IN_SECONDS);
		}
		else {
			set_transient($array->application->response_id, $array);
		}

	}

}

?>