<?php

/*
    tpshoppingcart should have the following structure:

    [title] => Mr.
    [firstname] => Justin
    [middlename] => MM
    [lastname] => TESTNAME
    [paxtype] => A
    [name] => TESTNAME Justin (Mr.)
    [email] => justin.barton@tourplan.com
    [address1] => 123 Bill STr bla bla bla
    [address2] =>
    [address3] => chch
    [address4] => canty
    [postCode] => 987
    [country] => NZ
    [address] => 123 Bill STr bla bla bla,chch,canty,987,NZ
    [servicelines] => Array (
            [0] => Array (
                    [productid] => 273
                    [rateid] => Default
                    [datein] => Mon 25 Feb 2013
                    [dateout] => Tue 26 Feb 2013
                    [date] => 2013-02-25
                    [scu] => 1
                    [pricedisplay] => $304.00
                    [price] => 30400
                    [currency] => AUD
                    [qty] => db
                    [availability] => On Request
                    [servicetype] => Accommodation
                    [suppliername] => ANA Intercontinental Hotel Tokyo
                    [productname] => Standard Room (Classic Category)
                    [productcomment] => Room only
                    [imageURL] => http://agent.nx.jtbtravel.com.au/websiteimages/Supplier_TYAN01/TYAN01.1.jpg
                    [classCode] => 4ST
                    [className] => 4 Star
                    [remarks] =>
                    [pickup] =>
                    [dropoff] =>
                    [configs] => Array
                        (
                            [0] => Array
                                (
                                    [type] => db
                                    [adults] => 2
                                    [children] => 0
                                    [infants] => 0
                                    [pax] => Array()
                                )
                        )
                )
        )

    [cardholder] => Justin TEST1
    [agentref] => 402
    [payment] =>
*/


function tp_shopping_cart_page()
{

  tp_log('tp_shopping_cart_page');

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$postinput = file_get_contents("php://input");
		if ($postinput!== FALSE)
		{
			tp_log('tpshoppingcart POSTED [' . session_id() . '] ' . print_r($postinput, true));
			tpSetCart($postinput);
		}
	}
	else if ($_SERVER['REQUEST_METHOD'] == 'GET')
	{
		if ( isset( $_GET['clear'] ) && $_GET['clear'] == 'true')
		{
			tpSetCart('{}');
		}
		else if (isset($_GET['product']))
		{
		  echo tpProductDetails($_GET['productid'], $_GET['date'], $_GET['scu'], $_GET['qty'], $_GET['jsonp']);
		}
		else if (isset($_GET['pricing']))
		{
      $cartArray = tpGetCartArray();
      $pricing = $_GET['pricing'];
      $addFees = tpAddFees($cartArray, $pricing);
		  echo tpPriceBooking( $addFees, false );
		}
		else
		{
			tp_log('tpshoppingcart GET [' . session_id() . ']');
			echo tpGetCart();
		}
	}
  exit();
}

function tpCartIsConfirmed()
{
    $cart = tpGetCartArray();
    if (tpCartIsEmpty($cart))
    {
        return false;
    }
    $availables = 0;
    $services = $cart['servicelines'];
    foreach ($services as $service)
    {
	 if ($service['availability'] == 'Available')
	 {
	     $availables++;
	 }
    }
    return $availables == count($services);
}

function tpCartIsOnRequest()
{
    $cart = tpGetCartArray();
    if (tpCartIsEmpty($cart))
    {
        return false;
    }
    foreach ($cart['servicelines'] as $service)
    {
	 if ($service['availability'] !== 'Available')
	 {
	     return true;
	 }
    }
    return false;
}

function tpFindFees($services, $fees, $firstonly)
{
  $servicefeemap = array();
  if (is_array($services) && is_array($fees))
  {
	  foreach ($services as $i => $service)
	  {
	    foreach ($fees as $fee)
	    {
	      $srbs = empty($fee['srbs']) ? null : explode(',', $fee['srbs']);
	      $suppcodes = empty($fee['suppliercodes']) ? null : explode(',', $fee['suppliercodes']);
	      $prodcodes = empty($fee['productcodes']) ? null : explode(',', $fee['productcodes']);

	      // tp_log('tpFindFees fee=' . $fee['label'] . ' srbs=' . print_r($srbs, true) );
       //  tp_log('tpFindFees fee=' . $fee['label'] . ' suppcodes=' . print_r($suppcodes, true) );
       //  tp_log('tpFindFees fee=' . $fee['label'] . ' prodcodes=' . print_r($prodcodes, true) );

	      $srb = $service['servicetype'];
	      $suppliercode = $service['suppliercode'];
	      $productcode = $service['productcode'];

	      if ((isset($srbs) || isset($suppliercodes) || isset($productcodes))
	        && (!isset($srbs) || in_array($srb, $srbs))
	        && (!isset($suppcodes) || in_array($suppliercode, $suppcodes))
	        && (!isset($prodcodes) || in_array($productcode, $prodcodes)))
	      {
	        // $servicefeemap[$i] = $fee;
          array_push($servicefeemap, $fee);
	        if ($firstonly)
	        {
            // tp_log("tpFindFees firstonly: " . ($firstonly ? "true":"false"));
	          return $servicefeemap;
	        }
	      }
	    }
	  }
  }
  return $servicefeemap;
}

function tpClearFees(&$cart, $fees)
{
  if ( empty($fees) )
    return;
  $feeids = array_unique(array_column($fees, 'id'));
  foreach ($cart['servicelines'] as $i => $service)
  {
    if (in_array($service['productid'], $feeids))
    {
      tp_log('tpClearFees unset existing delivery charge ' . $service['productid']);
      unset($cart['servicelines'][$i]);
    }
  }
  $cart['servicelines'] = array_values($cart['servicelines']);
}

function tpHasBookingFees($cart)
{
   if (!empty($cart) && isset($cart['servicelines']) && count($cart['servicelines']) > 0)
   {
   	  $fees = tpFindFees($cart['servicelines'], tpBookingFees(), true);
      return !empty($fees);
   }
   return false;
}


function tpFeeProduct($fee, $serviceDate, $qty, $configs)
{
  return array('productid' => $fee['id'],
         'rateid' => 'Default',
         'date' => isset($serviceDate) ? $serviceDate : date('Y-m-d'),
         'scu' => '1',
         'qty' => isset($qty) ? $qty : '1A',
         'configs' => isset($configs) ? $configs : array(array('adults' => 1, 'type' => 'sg')),
         'hidden' => true,
         'servicetype' => 'Fee');
}

function tpFeesFilterLabel($fees, $label)
{
   $results = array();
   if (!empty($fees))
   {
	   foreach ($fees as $fee)
	   {
	      if ($fee['label'] === $label)
		  {
		      array_push($results, $fee);
		  }
	   }
   }
   return $results;
}

function tpHasDeliveryFees($cart, $deliveryMethod=null)
{
   if (!empty($cart) && isset($cart['servicelines']) && count($cart['servicelines']) > 0)
   {
      $deliveryfees =  empty($deliveryMethod) ? tpDeliveryFees() : tpFeesFilterLabel(tpDeliveryFees(), $deliveryMethod);
      $fees = tpFindFees($cart['servicelines'], $deliveryfees, true);
      return !empty($fees);
   }
   return false;
}

function tpAddDeliveryFees(&$cart, $deliveryMethod)
{
   if (!empty($cart) && isset($cart['servicelines']) && count($cart['servicelines']) > 0)
   {
      $allfees = tpDeliveryFees();
      tpClearFees($cart, $allfees);
      $compare = get_option('tp_delivery_price_selection', "max");

      // tp_log('tpAddDeliveryFees $services=' . print_r($cart['servicelines'], true));
      // tp_log('tpAddDeliveryFees $allfees=' . print_r($allfees, true));

      $fees =  tpFeesFilterLabel($allfees, $deliveryMethod);


      $topFeeProduct = null;
      $topPrice = null;
      $serviceProductId = null;
      $serviceDate = null;
      $feemap = tpFindFees($cart['servicelines'], $fees, false);
      tp_log('tpAddDeliveryFees $feemap=' . print_r($feemap, true));
      foreach ($feemap as $i => $fee)
      {
      	 // tp_log('tpAddDeliveryFees $feemap $i=' . $i);
      	 // tp_log('tpAddDeliveryFees $feemap $fee=' . print_r($fee, true));
         $service = $cart['servicelines'][$i];
         // tp_log('tpAddDeliveryFees $service=' . print_r($service, true));
         $feeProduct = tpFeeProduct($fee, $service['date']);
         // tp_log('tpAddDeliveryFees ' . print_r($feeProduct, true));
         $feePrice = tpPriceBooking(array('servicelines' => array($feeProduct)));
         if (!isset($topPrice) || ($compare == 'max' && $feePrice > $topPrice) || ($compare == 'min' && $feePrice < $topPrice))
         {
            $topFeeProduct = $feeProduct;
            $topPrice = $feePrice;
            $serviceDate = $service['date'];
            $serviceProductId = $service['productid'];
         }
      }

      if ($topFeeProduct)
      {
	      $topFeeProduct['price'] = $topPrice;
          tp_log('tpAddDeliveryFees add ' . $deliveryMethod . ' fee id=' . $topFeeProduct['productid'] . ' price=' . $topPrice . ' for productid=' . $serviceProductId);
          array_push($cart['servicelines'], $topFeeProduct);
      }
    }
   return $cart;
}

function tpAddBookingFees(&$cart)
{
   if (!empty($cart) && isset($cart['servicelines']) && count($cart['servicelines']) > 0)
   {
      $fees =  tpBookingFees();
      tpClearFees($cart, $fees);
      $topFeeProduct = null;
      $topPrice = null;
	  $serviceProductId = null;
      $serviceDate = null;
      $feemap = tpFindFees($cart['servicelines'], $fees, false);
      tp_log('tpAddBookingFees $feemap=' . print_r($feemap, true));

      // Booking Fee will perform a bit different from Delivery Fee
      // As booking fee can have many options or duplications, but delivery can only have one option at all times.
      foreach ($feemap as $i => $fee)
      {
        // tp_log('tpAddBookingFees $feemap $i=' . $i);
        // tp_log('tpAddBookingFees $feemap $fee=' . print_r($fee, true));
        $service = $cart['servicelines'][$i];
        // tp_log('tpAddBookingFees $service=' . print_r($service, true));
        if ( isset( $fee['isUsingQty'] ) && $fee['isUsingQty'] == true )
          $feeProduct = tpFeeProduct($fee, $service['date'], $service['qty'], $service['configs']);
        else
          $feeProduct = tpFeeProduct($fee, $service['date']);
        // tp_log('tpAddBookingFees ' . print_r($feeProduct, true));
        $feePrice = tpPriceBooking(array('servicelines' => array($feeProduct)));
        array_push($cart['servicelines'], $feeProduct);
      }
    }
   return $cart;
}

function tpHasPaymentFees(&$cart)
{
    if (!empty($cart) && isset($cart['paymentfee']))
    {
    	return true;
    }	
    return false;
}

function tpAddFees(&$cart, $deliveryMethod)
{
	tpAddDeliveryFees($cart, $deliveryMethod);
	tpAddBookingFees($cart);
	tp_log('tpAddFees cart=' . print_r($cart, true));
	return $cart;
}

function tpAgentRef()
{
  global $wpdb;
  // $oldref = intval(get_option('tp_agentref', '0'));
  // $agentref = strval($oldref+1);
  // update_option('tp_agentref', $agentref);

  $wpdb->query($wpdb->prepare(
    "update wp_options set option_value = LAST_INSERT_ID(option_value + 1) where option_name = %s", "tp_agentref"
  ));

  $agentref = $wpdb->get_var($wpdb->prepare(
    "select LAST_INSERT_ID()", array()
  ));
  
  tp_log('tpAgentRef getNewAgentRef ' . $agentref);

  return $agentref;
}

function tpTourplanSubmit($url, $data)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json; charset=utf-8', 'Accept:application/json; charset=utf-8'));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_TIMEOUT,        60);
    $resp = curl_exec($curl);
    curl_close($curl);
	return $resp;
}

function tpProductDetails($productid, $date, $scu, $qty, $jsonp)
{
    $url = tp_get_url('tp_app_url') . '/product/' . $productid . '?date=' . $date . '&scu=' . $scu . '&qty=' . $qty
		. (empty($jsonp) ? '' : '&jsonp=' . $jsonp);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json; charset=utf-8', 'Accept:application/json; charset=utf-8'));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_TIMEOUT,        60);
    $resp = curl_exec($curl);
    curl_close($curl);
	return $resp;
}

function tpProductDetailsByCode($code, $date, $scu, $qty, $jsonp)
{
    $url = tp_get_url('tp_app_url') . '/product?code=' . $code . '&date=' . $date . '&scu=' . $scu . '&qty=' . $qty
    . (empty($jsonp) ? '' : '&jsonp=' . $jsonp);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json; charset=utf-8', 'Accept:application/json; charset=utf-8'));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_TIMEOUT,        60);
    $resp = curl_exec($curl);
    curl_close($curl);
  return $resp;
}

function tpPriceBooking($cart, $showTotal = true)
{
    $cartJSON = json_encode(array('booking' => $cart));
    $tpurl = tp_get_url('tp_app_url') . '/pricing';
    $resp = tpTourplanSubmit($tpurl, $cartJSON);
    if ($resp !== false)
    {
  		if ($showTotal)
  		{
  			$jsresp = json_decode($resp, true);
  			return $jsresp['price'];
  		}
  		else
  		{
  			return $resp;
		  }
    }
    else
    {
    	return null;
    }
}

function tpMakeBooking($cart, $agentref)
{
  if (empty($cart))
  {
    tp_log('tpMakeBooking invalid args cart is empty for agentref=' . $agentref);
    return null;
  }

  $cart['agentref'] = $agentref;

  $bookingExecTime = 90;
  $bookingResultExpireTime = 1800;
  $FAILED_TEXT = "BOOKING_FAILED";
  $LAST_BOOKING_AGENT_REF = "LAST_BOOKING_AGENT_REF";

  // * We'll save two transients for a booking process
  // * One will mark that the booking process has started
  // * The other one will mark the booking process result
  // * For 1 agentref, we should always have 1 booking result.

  // Try to get the agentref, if it's not valid, pull up the last agentref recored in database
  if ( $agentref == "" || $agentref == false || $agentref == 0 )
    $agentref = get_transient( $LAST_BOOKING_AGENT_REF );
  else
    set_transient( $LAST_BOOKING_AGENT_REF, $agentref, $bookingResultExpireTime );
  // Check whether the booking is already in progress.
  // If it is, we should wait until the result coming back.
  $bookingInProgressId = "booking_in_progress_" . $agentref;
  $bookingInProgressResultId = "booking_result_" . $agentref;
  $bookingInProgressTs = get_transient($bookingInProgressId);
  $sleepInterval = 3;
  if ( $bookingInProgressTs != false ) {
    // If there is a booking in process
    error_log("tpMakeBooking. There is a booking in process. agentref:" . $agentref );

    $start_time = time();
    $bookingResult = false;
    error_log("Detected a booking in progress, waiting booking result to come back.");
    while ( $bookingResult == false 
      && ( time() - $start_time < $bookingExecTime ) ) {
      sleep( $sleepInterval );
      $bookingResult = get_transient( $bookingInProgressResultId );
    }
    if ( $bookingResult == false )
      error_log("Unexpectly failed to fetch result for the booking.");
    else {
      $status = tpBookingRetailStatus($bookingResult);
      error_log("Retreiving booking status as: " . $status);
      wp_redirect(tp_booking_status_url($status));
      exit;
    }
  }
  else {
    // If there's no booking in process yet
    error_log("tpMakeBooking. There's no booking in process yet.");
    set_transient($bookingInProgressId, $agentref, $bookingExecTime);

    $mergedcart = unserialize(serialize($cart));

    foreach ($mergedcart["servicelines"] as $keysl => $serviceline) {
      foreach ($serviceline["configs"] as $keyconf => $config) {
        foreach ($config["pax"] as $keypax => $pax) {
          if (!empty($pax["middlename"])) {
            $mergedcart["servicelines"][$keysl]["configs"][$keyconf]["pax"][$keypax]["firstname"] = $pax["firstname"] . " " . $pax["middlename"];

            $mergedcart["servicelines"][$keysl]["configs"][$keyconf]["pax"][$keypax]["middlename"] = "";
          }
        }
      }
    }

    $curLang = tp_cur_language_exts();
    $agentLangRecord = get_transient( "lang_" . $agentref );
    if ( $agentLangRecord != null && $agentLangRecord != '' )
      $curLang = $agentLangRecord;
    tp_log('tpMakeBooking language: ' . $curLang );
    if ( $curLang != "" )
      $mergedcart["lang"] = $curLang;

    $cartJSON = json_encode( array('booking' => $mergedcart) );
    $tpurl = tp_get_url('tp_app_url') . '/booking';

    tp_log('tpMakeBooking sending to ' . $tpurl  . ' : ' . $cartJSON );
    $resp = tpTourplanSubmit($tpurl, $cartJSON);
    tp_log('tpMakeBooking response: ' . $resp);

    $engineTimeout = (get_option("tp_request_timeout", 60000) / 1000);
    $existingTimeout = ini_get("max_execution_time");
    ini_set("max_execution_time", $bookingExecTime);

    if ($resp != false)
    {
      $js = json_decode($resp, true);
      if (isset($js) && isset($js['reqid']))
      {
        $bookingurl = $tpurl . '?reqid=' . $js['reqid'] . '&agentref=' . $agentref;
        for ($retries = 0; $retries < 5; $retries++)
        {
          $resp = wp_remote_get($bookingurl, array("timeout" => $engineTimeout, 'sslverify' => false));
          tp_log($bookingurl . ' : ' . print_r($resp,true));
          if (!is_wp_error($resp) && $resp['response']['code'] === 200 && strlen($resp['body'])>0)
          {
            $js = json_decode($resp['body'], true);
            if (isset($js) && !isset($js['reqid']))
            {
              set_transient($bookingInProgressResultId, $js, $bookingResultExpireTime);
              return $js;
            }
          }
        }
      }
    }

    ini_set("max_execution_time", $existingTimeout);
    tp_log('tpMakeBooking response was null');

    set_transient($bookingInProgressResultId, $FAILED_TEXT, $bookingResultExpireTime);
    return null;
  }
  return null;
}

function tpBookingRef($booking = null)
{
    if (isset($booking) && isset($booking['booking']))
    {
      $_SESSION['tpbookingref'] = $booking['booking']['ref'];
    }
    return $_SESSION['tpbookingref'];
}

function tpBookingRetailStatus($booking)
{
   if (isset($booking) && isset($booking['booking'])
       && isset($booking['booking']['servicelines'])
	   && count($booking['booking']['servicelines'] > 0)
	   && isset($booking['booking']['pdf+'])
	   && $booking['booking']['pdf+'] !== '(0 chars)'
     && $booking['booking']['pdf+'] !== '(0 bytes)'
    )
   {
	  foreach ($booking['booking']['servicelines'] as $service)
	  {
		 if ($service['status'] !== 'OK')
		 {
			return 'requested';
		 }
	  }
	  return isset($booking['booking']['receiptref']) ? 'confirmed' : 'failed';
   }
   return 'failed';
}

// remove cardnum from str (for debug logging)
function tpCensorCardNumber($str, $cardnum)
{
    return strlen($cardnum) > 10 ? str_replace($cardnum, str_repeat('x',  strlen($cardnum) - 4) . substr($cardnum, - 4), $str) : $str;
}