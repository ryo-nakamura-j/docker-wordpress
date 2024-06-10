<?php

switch (get_option('tp_payment_gateway'))
{
  case 'eway': require_once('tp-eway.php'); break;
  case 'pbb': require_once('tp-pbb.php'); break;
  case 'dps': require_once('tp-dps.php'); break;
  case 'cfb': require_once('tp-cfb.php'); break;
  case 'axes': require_once('tp-axes.php'); break;
  case 'aeon': require_once('tp-aeon.php'); break;
  case 'paynamics': require_once('tp-paynamics.php'); break;
  case 'ipay88': require_once('tp-ipay88.php'); break;
  case 'stripe': require_once('tp-stripe.php'); break;
  case 'credomatic': require_once('tp-credomatic.php'); break;
  case 'paydollar': require_once('tp-paydollar.php'); break;
  default: break;
}

// ================================================ SEARCH ===========================================================

add_shortcode('tp-search', 'tp_search_shortcode');
function tp_search_shortcode($atts, $content)
{
    $section = $atts['section'];
	$class = $atts['class'];

	$html = '';
	if (empty($section) || $section == 'params')
	{
	   $html .= '<div id="searchparamssection" ' . (empty($class) ? '' : 'class="'. $class . '"') . '></div>';
	}
    if (empty($section) || $section == 'refine')
	{
	   $html .= '<div id="refinesearchsection"></div>';
	}
	if (empty($section) || $section == 'sort')
	{
	   $html .= '<div id="sortresultssection"></div>';
	}
	if (empty($section) || $section == 'results')
	{
	   $html .= '<div id="searchresultssection"></div>';
    }
	return $html;
}

add_shortcode('tp-tour-search', 'tp_tour_search_shortcode');
function tp_tour_search_shortcode($atts, $content) {
	$html = "<div id='" . $atts["id"] . "'></div>\n";
	$json = str_replace(array("\r", "\n"), '', tp_read_plugin_file('templates\tour-search.json'));
	$html .= "<script>\n";
	$html .= "$(window).ready(function() {\n";
	$html .= "loadHandlebarsTemplate('" . plugin_dir_url(__FILE__) . "templates/tour-search.hbs', '" . $atts['id'] . "', " . $json . ");\n";
	$html .= "})\n";
	$html .= "</script>\n";
	return $html;
}

// ================================================ BOOKING ===========================================================

add_shortcode('tp-booking-ref', 'tp_booking_ref_shortcode');
function tp_booking_ref_shortcode($atts, $content)
{
	return isset($_SESSION['tpbookingref']) ? $_SESSION['tpbookingref'] : '(Errored)';
}

add_shortcode('tp-error', 'tp_error_shortcode');
function tp_error_shortcode($atts, $content)
{
	switch (get_option('tp_payment_gateway')) {
		case "paydollar":
			$ref = $_REQUEST["Ref"];
			$errObj = get_transient("booking_" . $ref);
			$errMsg = tpPayDollarErrorMessage($errObj["prc"], $errObj["src"]);
			return empty($errMsg) ? "(Unknown)" : $errMsg;

		default:
			return isset($_REQUEST['error']) ? $_REQUEST['error'] : '(Unknown)';
	}
	
}

// ================================================ CHECKOUT ===========================================================

add_shortcode('tp-customer', 'tp_customer_shortcode');
function tp_customer_shortcode($atts, $content)
{
    return '<div id="tpcustomersection"></div>';
}

// older versions of wordpress cant handle a short code of just [tp-cart]
// since it is a subset of tp-cart-empty tp-cart-confirmed etc and gets injected in wrong places
add_shortcode('tp-cart-main', 'tp_cart_main_shortcode'); 
function tp_cart_main_shortcode($atts, $content)
{
    return is_page(tp_itinerary_pagename()) || is_page(tp_checkout_pagename()) ? 
	  '<div id="tpcartsection" class="' . (is_page(tp_itinerary_pagename()) ? 'itinerary' : 'checkout'). '"></div>'
	  : '<!-- cart -->';
}

add_shortcode('tp-cart-footer', 'tp_cart_footer_shortcode'); 
function tp_cart_footer_shortcode($atts, $content)
{
    return is_page(tp_itinerary_pagename()) || is_page(tp_checkout_pagename()) ? 
	  '<div id="tpcartfootersection" class="' . (is_page(tp_itinerary_pagename()) ? 'itinerary' : 'checkout'). '"></div>'
	  : '<!-- cartfooter -->';
}

add_shortcode('tp-cart-empty', 'tp_cart_empty_shortcode');
function tp_cart_empty_shortcode($atts, $content)
{
   return tpCartIsEmpty() ? '<div id="tpcartempty">' . $content . '</div>' : '<!-- empty -->';
}

add_shortcode('tp-cart-onrequest', 'tp_cart_onrequest_shortcode');
function tp_cart_onrequest_shortcode($atts, $content)
{
   return tpCartIsOnRequest() ? '<div id="tpcartonrequest">' . $content . '</div>' : '<!-- onrequest-->';
}

add_shortcode('tp-cart-confirmed', 'tp_cart_confirmed_shortcode');
function tp_cart_confirmed_shortcode($atts, $content)
{
   return tpCartIsConfirmed() ? '<div id="tpcartconfirmed">' . $content . '</div>' : '<!-- confirmed -->';
}

/*
 * Insert a credit card form (only valid in checkout page)
*/
add_shortcode('tp-creditcard-form', 'tp_creditcard_form_shortcode');
function tp_creditcard_form_shortcode($atts, $content)
{
	return is_page(tp_checkout_pagename()) && (!tp_use_payment_alt_param() || !tp_is_payment_alt_param())  
	       ? tp_creditcard_form() : '<!-- card -->';
}

add_shortcode('tp-creditcard-form-template', 'tp_creditcard_form_template_shortcode');
function tp_creditcard_form_template_shortcode($atts, $content) {
	return is_page(tp_checkout_pagename()) && (!tp_use_payment_alt_param() || !tp_is_payment_alt_param())  
	       ? tp_creditcard_form_template() : '<!-- card template -->';
}

add_shortcode('tp-creditcard-form-script', 'tp_creditcard_form_script_shortcode');
function tp_creditcard_form_script_shortcode($atts, $content) {
	return is_page(tp_checkout_pagename()) && (!tp_use_payment_alt_param() || !tp_is_payment_alt_param())  
	       ? tp_creditcard_form_script() : '<!-- card script -->';
}

/*
 *   Checkout page total and deposits
 */
add_shortcode('tp-cart-total', 'tp_cart_total_shortcode');
function tp_cart_total_shortcode($atts, $content)
{
	if (is_page(tp_checkout_pagename()) && !empty($_SESSION['tpprice']))
	{
	    return '<div id="tpcarttotal">'
	       	  . (empty($content) ? '' : '<span class="tplabel">' . $content . '</span>')
		      . (empty($atts['prefix']) ? '' : '<span class="tpprefix">' . $atts['prefix'] . '</span>') 
	          . '</span><span class="tpmoney">' . tpDollars(intval($_SESSION['tpprice'])) . '</span></div>';
	}
	else
	{
		return '<!-- tp-cart-total -->';
	}
}

add_shortcode('tp-deposit-subtotal', 'tp_deposit_subtotal_shortcode');
function tp_deposit_subtotal_shortcode($atts, $content)
{
	if (is_page(tp_checkout_pagename()) && get_option('tp_deposit_only') === 'true' && !empty($_SESSION['tpprice']))
	{
		$depositpercent = floatval(get_option('tp_deposit_percent', '20.0'));
		$depositsurcharge = floatval(get_option('tp_deposit_surcharge', '1.5'));
		$depositamount = intval($_SESSION['tpprice']) * ($depositpercent / 100.0);
		$surchargeamount = $depositamount * ($depositsurcharge /100.0);
		$depositCents = $depositamount + $surchargeamount;
	
		return  '<div id="tpdepositsubtotal">'
			. (empty($content) ? '' : '<span class="tplabel">' . $content . '</span>')
			. (empty($atts['prefix']) ? '' : '<span class="tpprefix">' . $atts['prefix'] . '</span>')
			. '<span class="tpmoney">' . tpDollars($depositamount) . '</span></div>';
	}
	else 
	{
	    return '<!-- tp-deposit-subtotal -->';
	}
}

add_shortcode('tp-surcharge', 'tp_surcharge_shortcode');
function tp_surcharge_shortcode($atts, $content)
{
	if (is_page(tp_checkout_pagename()) && get_option('tp_deposit_only') === 'true' && !empty($_SESSION['tpprice']))
	{
		$depositpercent = floatval(get_option('tp_deposit_percent', '20.0'));
		$depositsurcharge = floatval(get_option('tp_deposit_surcharge', '1.5'));
		$depositamount = intval($_SESSION['tpprice']) * ($depositpercent / 100.0);
		$surchargeamount = $depositamount * ($depositsurcharge /100.0);
		$depositCents = $depositamount + $surchargeamount;

		return  '<div id="tpsurcharge">'
			. (empty($content) ? '' : '<span class="tplabel">' . $content . '</span>')
			. (empty($atts['prefix']) ? '' : '<span class="tpprefix">' . $atts['prefix'] . '</span>')
			. '<span class="tpmoney">' . tpDollars($surchargeamount) . '</span></div>';
	}
	else 
	{
	    return '<!-- tp-surcharge -->';
	}
}

add_shortcode('tp-deposit-total', 'tp_deposit_total_shortcode');
function tp_deposit_total_shortcode($atts, $content)
{
	if (is_page(tp_checkout_pagename()) && get_option('tp_deposit_only') === 'true' && !empty($_SESSION['tpprice']))
	{
		$depositpercent = floatval(get_option('tp_deposit_percent', '20.0'));
		$depositsurcharge = floatval(get_option('tp_deposit_surcharge', '1.5'));
		$depositamount = intval($_SESSION['tpprice']) * ($depositpercent / 100.0);
		$surchargeamount = $depositamount * ($depositsurcharge /100.0);
		$depositCents = $depositamount + $surchargeamount;

		return  '<div id="tpdeposittotal">'
			. (empty($content) ? '' : '<span class="tplabel">' . $content . '</span>')
			. (empty($atts['prefix']) ? '' : '<span class="tpprefix">' . $atts['prefix'] . '</span>')
			. '<span class="tpmoney">' . tpDollars($depositCents) . '</span></div>';
	}
	else
	{
		return '<!-- tp-deposit-total -->';
	}
}

add_shortcode('tp-payment-fee', 'tp_payment_fee_shortcode');
function tp_payment_fee_shortcode($atts, $content)
{
	return !is_page(tp_checkout_pagename()) || (tp_is_payment_alt_enabled() && (tp_is_payment_alt_param() || !tp_use_payment_alt_param())) 
	       ? '<!-- tppaymentfeesection -->' 
	       : '<div id="tppaymentfeesection"></div>';
}

function tp_payment_alt_checkbox($content)
{
	$checkbox = '<script>'
	    . 'function tpPaymentAltClick(checkbox){var deps=jQuery("#tpcreditauthorise,#tpcreditcharge,#tpcreditcardsection"); '
	    . 'if (checkbox.checked) deps.hide(); else deps.show();return true;}'
	    . '</script>'
	    . '<input type="checkbox" id="tppaymentalt" name="tppaymentalt" value="false" onclick="tpPaymentAltClick(this);">';
    return preg_replace('/\[tp-checkbox\]/', $checkbox, $content);
}


/*
   Only valid on checkout page. Does nothing unless tp_payment_alt db option enabled.
*/
add_shortcode('tp-payment-alt', 'tp_payment_alt_shortcode');
function tp_payment_alt_shortcode($atts, $content)
{
	return is_page(tp_checkout_pagename()) && tp_is_payment_alt_enabled() && (tp_is_payment_alt_param() || !tp_use_payment_alt_param())
	       ? tp_payment_alt_checkbox($content) 
	       : (tp_is_payment_alt_param() && tp_is_payment_alt_cart()
	          ? '<input type="checkbox" id="tppaymentalt" name="tppaymentalt" value="true" checked="checked" style="display:none">'
	          : '<!-- alt -->');
}

/*
 * creditcard will be authorised
 * cart is onrequest
 * AND payment_option is not none
 * AND (payment_alt is disabled
 * OR (payment_alt is enabled AND not checked))
 */
add_shortcode('tp-credit-authorise', 'tp_credit_authorise_shortcode');
function tp_credit_authorise_shortcode($atts, $content)
{
   return tpCartIsOnRequest() && get_option('tp_payment_gateway') !== 'none' && (!tp_use_payment_alt_param() || !tp_is_payment_alt_param())
        ? '<div id="tpcreditauthorise">' . $content . '</div>' : '<!-- authorise -->';
}

/*
 * credit card will be charge
 * cart is confirmed
 * AND payment_option is not none
 * AND (payment_alt is disabled
 * OR (payment_alt is enabled AND not checked))
 *
 * but payment_alt checked needs to be clientside so we need to add id=tpcreditcharge
 * and let clientside toggle it
 */
add_shortcode('tp-credit-charge', 'tp_credit_charge_shortcode');
function tp_credit_charge_shortcode($atts, $content)
{
    return tpCartIsConfirmed() && get_option('tp_payment_gateway') !== 'none' && (!tp_use_payment_alt_param() || !tp_is_payment_alt_param())
    ? '<div id="tpcreditcharge">' . $content . '</div>' : '<!-- charge -->';
}

function tp_confirm_checkout_checkbox($content)
{
	return preg_replace('/\[tp-checkbox\]/', '<input type="checkbox" id="tpcheckoutconfirmcheckbox" name="tpcheckoutconfirmcheckbox" value="false" >', $content);
}

/*
 Only valid on checkout page. checkbox for terms and conditions.
*/
add_shortcode('tp-confirm-checkout', 'tp_confirm_checkout_shortcode');
function tp_confirm_checkout_shortcode($atts, $content)
{
	return is_page(tp_checkout_pagename()) ? '<div id="tpcheckoutconfirm">' . do_shortcode(tp_confirm_checkout_checkbox($content)) . '</div>' : '<!-- confirmcheckout -->';
}

// ================================================ SUPPLIER ===========================================================

function tp_hide_deps($deps)
{
	return isset($deps) && strlen($deps)>0 ? "<script>jQuery('" . $deps . "').hide();</script>" : "";
}

/* Get current supplier form request - page has to set the supplier early on (ie before header)
 * the key can be an array (currently only upto 2) in which case its nested
 */
function tp_supplier_data($key, $deps = null)
{
	$supp = is_array($key) && count($key) == 2
	      ? $_REQUEST['tp-supplier'][$key[0]][$key[1]]
	      : $_REQUEST['tp-supplier'][$key];

	return isset($supp) ? $supp : tp_hide_deps($deps);
}

function tp_supplier_note($code)
{
	if (isset($code))
	{
		$notes = tp_supplier_data('notes');
		if (isset($notes) && is_array($notes) && count($notes) > 0)
		{
			foreach ($notes as $note)
			{
				if ($note['code'] == $code)
				{
					return $note;
				}
			}
		}
	}
	return null;
}

/* getting class */
add_shortcode('tp-supplier-cls', 'tp_supplier_cls_shortcode');
function tp_supplier_cls_shortcode($atts, $content)
{
	$class =  tp_supplier_data(array('analyses', 2), $atts['deps']);
	return substr($class, -1);
}

add_shortcode('tp-product-cls', 'tp_product_cls_shortcode');
function tp_product_cls_shortcode($atts, $content)
{
	return tp_product_data('cls', $atts['deps']);
}

add_shortcode('tp-supplier-name', 'tp_supplier_name_shortcode');
function tp_supplier_name_shortcode($atts, $content)
{
	return tp_supplier_data('name', $atts['deps']);
}

add_shortcode('tp-supplier-address', 'tp_supplier_address_shortcode');
function tp_supplier_address_shortcode($atts, $content)
{
	$addr =  'address' . (isset($atts['line']) ? $atts['line'] : '');
	return tp_supplier_data(array('contact', $addr), $atts['deps']);
}

add_shortcode('tp-supplier-postcode', 'tp_supplier_postcode_shortcode');
function tp_supplier_postcode_shortcode($atts, $content)
{
	return tp_supplier_data(array('contact', 'postcode'), $atts['deps']);
}

/*
 * get a specific supplier note
 * code - note category
 * format - text or html (defaul html)
 * deps - ids of dependent elements (eg #mynotesection) to hide if note isnt found
 */
add_shortcode('tp-supplier-note', 'tp_supplier_note_shortcode');
function tp_supplier_note_shortcode($atts, $content)
{
	$note = tp_supplier_note($atts['code']);
	if (isset($note))
	{
		return $note['text' === $atts['format'] ? 'text' : 'html'];
	}
	else
	{
		return tp_hide_deps($atts['deps']);
	}
}

/* get the supplier image or show default
 * optional attributes:
 *    mode - currently only supppliercode (ODP style - default) or suppliercode-flat (thumnail style) are supported
 *           (could also use supplierid or name) Supplier_
 *    suffix - added to end of file name before ext - eg '.1' or 'tn'
 *    ext - file extension eg .jpg or .gif
 *    baseurl - base of the url (default is tp_promos_supplier_image_url)
 *    default - default image to show if none found
 *    deps - ids of dependent elements (eg #myimagesection) to hide if no code is given or mode isnt found
 */
add_shortcode('tp-supplier-image', 'tp_supplier_image_shortcode');
function tp_supplier_image_shortcode($atts, $content)
{
	$url = null;
	$suffix = isset($atts['suffix']) ? $atts['suffix'] : ""; // '.1' or 'tn'
	$ext = isset($atts['ext']) ? $atts['ext'] : ".jpg";
	$baseurl = isset($atts['baseurl']) ? $atts['baseurl'] : get_option('tp_supplier_images_url');
	$default = isset($atts['default']) ? $baseurl . '/' . $atts['default'] : get_option('tp_default_image_url');
	$defaultid = null;
	$code = tp_supplier_data('code');

	if ($code !== '' && (!isset($atts['mode']) || $atts['mode'] === 'suppliercode'))
	{
		$url = $baseurl . '/Supplier_' . $code . '/' . $code . $suffix . $ext;
		$defaultid = str_replace('.', '_', 'defaultSupplier' . $code . $suffix);
	}
	else if ($code !== '' && $atts['mode'] === 'suppliercode-flat')
	{
		$url = $baseurl . '/' . $code . $suffix . $ext;
		$defaultid = str_replace('.', '_', 'defaultSupplier' . $code . $suffix);
	}

	if (isset($url))
	{
		return '<img id="' . $defaultid . '" src="' . $default . '" alt="'. $default . '" />'
		     . '<img src="' . $url . '" alt="'. $url . '" onerror="javascript: jQuery(this).hide();" onload="javascript: jQuery(\'#' . $defaultid . '\').hide();" />';
	}
	else
	{
		return tp_hide_deps($atts['deps']);
	}
}

/* get the supplier image list
 * optional attributes:
*    mode - currently only supppliercode (ODP style - default) or suppliercode-flat (thumnail style) are supported
*           (could also use supplierid or name)
*    suffix - added to end of file name before ext - eg '.1', 'tn' or 'promo' for context images (default is empty string)
*    ext - file extension eg .jpg or .gif (default .jpg)
*    baseurl - base of the url (default is tp_promos_supplier_image_url)
*    default - default image to show if none found (default is tp_promos_default_image_url)
*    callback - callback javscript function name - to be called when gallery is loaded
*    id - element id of the ul list (default galleryImages)
*    max - maximum number of images to load (default 10)
*    deps - ids of dependent elements (eg #myimagesection) to hide if no code is given or mode isnt found
*
*    first image is special case that should load a default image if no gallery images found
*     callback on error since thats the end of the list
*    note that we need to bind the error events before assinging the src attributes.
*/
add_shortcode('tp-supplier-imagelist', 'tp_supplier_imagelist_shortcode');
function tp_supplier_imagelist_shortcode($atts, $content)
{
	$url = null;
	$suffix = isset($atts['suffix']) ? $atts['suffix'] : '';
	$ext = isset($atts['ext']) ? $atts['ext'] : ".jpg";
	$baseurl = isset($atts['baseurl']) ? $atts['baseurl'] : get_option('tp_supplier_images_url');
	$max = isset($atts['max']) ? intval($atts['max']) : 10;
	$default = isset($atts['default']) ? $baseurl . '/' . $atts['default'] : get_option('tp_default_image_url');
	$id = isset($atts['id']) ? $atts['id'] : 'galleryImages';
	$callback = $atts['callback'];

	$code = tp_supplier_data('code');
	if ($code !== '' && (!isset($atts['mode']) || $atts['mode'] === 'suppliercode'))
	{
		$url = $baseurl . '/Supplier_' . $code . '/' . $code;
	}
	else if ($code !== '' && $atts['mode'] === 'suppliercode-flat')
	{
		$url = $baseurl . '/' . $code;
	}

	if (isset($url))
	{
		$imglist = '<ul id="'.$id.'">';
		for ($i=1; $i <= $max; $i++)
		{
			$src = $url . $suffix . '.' . $i . $ext;
			$imglist .= '<li><img '. ($i==1?'class="tpGalleryFirstImage"':'') . ' alt="' . $src . '" /></li>';
		}
		$imglist .= '</ul>';

		$imglist .= '<script>jQuery(document).ready(function() {
		var tpGalleryImageCount = 0;
		jQuery("#'.$id.' li img").load(function(){
		   if (++tpGalleryImageCount>=10){
		     '.(isset($callback) ? trim($callback) . '(\''. $id .'\');': '').'
		   }
	    }).error(function(){
	        if (jQuery(this).is(".tpGalleryFirstImage")){
	            jQuery(\'#'.$id .'\').append(\'<li><img src="'.$default.'" alt="default supplier image" /></li>\');
	        }
		    jQuery(this).parent().remove();
	        if (++tpGalleryImageCount>=10){
		      '.(isset($callback) ? trim($callback) . '(\''. $id .'\');': '').'
		    }
	    }).each(function(i,e){
	        jQuery(e).attr("src", jQuery(e).attr("alt"));
	    });
	    });</script>';

	    return $imglist;
	}
	else
	{
	    return tp_hide_deps($atts['deps']);
	}
}

function tp_google_map($mapid, $title, $notetext)
{
    if (isset($notetext) && preg_match('/-?\d*\.\d+\s*,\s*-?\d*\.\d+/', trim($notetext)))
	{
		$coords = explode(',', $notetext);
		$lat = trim($coords[0]);
		$lng = trim($coords[1]);
		return '<div id="' . $mapid . '"></div>'
		. '<script>function tpinitmap(){'
		. 'var tpcoords = new google.maps.LatLng("'. $lat . '","' . $lng . '");'
		. 'var tpmap = new google.maps.Map(document.getElementById("' . $mapid . '"), {scrollwheel:false, zoom: 14, center: tpcoords, mapTypeId: google.maps.MapTypeId.ROADMAP});'
		. 'var tpmarker = new google.maps.Marker({position: tpcoords, map: tpmap, title: "' . $title . '"});'
		. '}</script>'
		. '<script src="http://maps.google.com/maps/api/js?sensor=false&callback=tpinitmap"></script>';
	}
	return null;
}

/*
 * Insert a google map
 * id - (default mapsection)
 * title - (defaults to supplier's name)
 * code - note category for map coordinates (default MAP)
 */
add_shortcode('tp-supplier-map', 'tp_supplier_map_shortcode');
function tp_supplier_map_shortcode($atts, $content)
{
	$id = isset($atts['id']) ? $atts['id'] : 'mapsection';
	$title = isset($atts['title']) ? $atts['title'] : tp_supplier_data('name');
	$code = isset($atts['code']) ? $atts['code'] : 'MAP';
	$note = tp_supplier_note($code);
	$map = tp_google_map($id, $title, $note['text']);
	return isset($map) ? $map : tp_hide_deps($atts['deps']);
}

/*
 * TODO : filter on amenitie category
 *        map to descriptions (need to load lookups/maps)
*/
add_shortcode('tp-supplier-amenities', 'tp_supplier_amenities_shortcode');
function tp_supplier_amenities_shortcode($atts, $content)
{
	$amns = tp_supplier_data('amenities');
	if (!empty($amns))
	{
		$amenities = '<ul>';
		foreach ($amns as $amn)
		{
			$descr = tp_lookup('AMN', $amn);
			$amenities .= '<li>' . (isset($descr) ? $descr : $amn) . '</li>';
		}
		$amenities .= '</ul>';
		return $amenities;
	}
	return tp_hide_deps($atts['deps']);
}

add_shortcode('tp-supplier-video', 'tp_supplier_video_shortcode');
function tp_supplier_video_shortcode($atts, $content)
{
	$checkimage = false;
	$id = isset($atts['id']) ? $atts['id'] : 'videosection';
	$url = null;
	if (!isset($atts['url']))
	{
		$checkimage = true;
		$baseurl = isset($atts['baseurl']) ? $atts['baseurl'] : get_option('tp_promos_supplier_image_url');
		$suffix = isset($atts['suffix']) ? $atts['suffix'] : '.1';
		$ext = isset($atts['ext']) ? $atts['ext'] : '.flv';
		$code = tp_supplier_data('code');

		if ($code !== '' && (!isset($atts['mode']) || $atts['mode'] === 'suppliercode'))
		{
			$url = $baseurl . '/Supplier_' . $code . '/' . $code . $suffix . $ext;
		}
		else if ($code !== '' && $atts['mode'] === 'suppliercode-flat')
		{
			$url = $baseurl . '/' . $code . $suffix . $ext;
		}
	}

	$img = isset($atts['img']) ? $atts['img'] : $url . '.jpg';
	if (isset($atts['img']))
	{
		$checkimage = true;
	}

	$player = isset($atts['player']) ? $atts['player'] : plugins_url() . '/tp-product/player.swf';

	return '<div id="'. $id . '"></div>'
	      . '<script>function tpsuppliervideo(){'
	      . 'var sect = document.getElementById(\'' . $id . '\');'
	      . 'if (jwplayer && sect) { jwplayer(sect).setup({flashplayer: \'' . $player . '\', file: \'' . $url . '\''
	      . ', image: \''. $img . '\', height: sect.offsetHeight, width: sect.offsetWidth});};'
	      . '}</script>'
	      . ($checkimage ? '<img src="' . $img . '" alt="video image" onload="javascript:tpsuppliervideo();this.parentNode.removeChild(this);" />'
	      		         : '<script>tpsuppliervideo();</script>');
}


// ================================================ PRODUCT ===========================================================

/* Get current product from request - page has to set the product early on (ie before header)
 * the key can be an array (currently only upto 2) in which case its nested
*/
function tp_product_data($key, $deps = null)
{
	$prod = is_array($key) && count($key) == 2
	? $_REQUEST['tp-product'][$key[0]][$key[1]]
	: $_REQUEST['tp-product'][$key];

	return isset($prod) ? $prod : tp_hide_deps($deps);
}

function tp_product_note($code)
{
	if (isset($code))
	{
		$notes = tp_product_data('notes');
		if (isset($notes) && is_array($notes))
		{
			foreach ($notes as $note)
			{
				if ($note['code'] == $code)
				{
					return $note;
				}
			}
		}
	}
	return null;
}

add_shortcode('tp-product-name', 'tp_product_name_shortcode');
function tp_product_name_shortcode($atts, $content)
{
	return tp_product_data('name', $atts['deps']);
}

add_shortcode('tp-product-comment', 'tp_product_comment_shortcode');
function tp_product_comment_shortcode($atts, $content)
{
	return tp_product_data('comment', $atts['deps']);
}

/*
 * get the product code
*/
add_shortcode('tp-product-code', 'tp_product_code_shortcode');
function tp_product_code_shortcode($atts, $content)
{
	return tp_product_data('code', $atts['deps']);
}

/*
 * get the product class-code
*/
add_shortcode('tp-product-class', 'tp_product_class_shortcode');
function tp_product_class_shortcode($atts, $content)
{
    return tp_product_data('cls', $atts['deps']);
}

/*
 * get the product destination
*/
add_shortcode('tp-product-destination', 'tp_product_destination_shortcode');
function tp_product_destination_shortcode($atts, $content)
{
	return tp_product_data('dst', $atts['deps']);
}

/*
 * get a specific supplier note
* code - note category
* format - text or html (defaul html)
* deps - ids of dependent elements (eg #mynotesection) to hide if note isnt found
*/
add_shortcode('tp-product-note', 'tp_product_note_shortcode');
function tp_product_note_shortcode($atts, $content)
{
	$note = tp_product_note($atts['code']);
	if (isset($note))
	{
		return $note['text' === $atts['format'] ? 'text' : 'html'];
	}
	else
	{
		return tp_hide_deps($atts['deps']);
	}
}

/* get the product image or show default
 * optional attributes:
*    mode - currently only productcode (ODP style - default) or productcode-flat (thumnail style) are supported
*           (could also use productid or name)
*    suffix - added to end of file name before ext - eg '.1' or 'tn'
*    ext - file extension eg .jpg or .gif
*    baseurl - base of the url (default is tp_supplier_images_url)
*    default - default image to show if none found
*    deps - ids of dependent elements (eg #myimagesection) to hide if no code is given or mode isnt found
*/
add_shortcode('tp-product-image', 'tp_product_image_shortcode');
function tp_product_image_shortcode($atts, $content)
{
	$url = null;
	$suffix = isset($atts['suffix']) ? $atts['suffix'] : ""; // '.1' or 'tn'
	$ext = isset($atts['ext']) ? $atts['ext'] : ".jpg";
	$baseurl = isset($atts['baseurl']) ? $atts['baseurl'] : get_option('tp_supplier_images_url');
	$default = isset($atts['default']) ? $baseurl . '/' . $atts['default'] : get_option('tp_default_image_url');
	$defaultid = null;

	$code = tp_product_data('code');
	$suppcode = substr($code, 5, 6);

	if ($code !== '' && (!isset($atts['mode']) || $atts['mode'] === 'productcode'))
	{
		$url = $baseurl . '/Supplier_' . $suppcode . '/Option_' . $code . '/'. $code . $suffix . $ext;
		$defaultid = str_replace('.', '_', 'defaultProduct' . $code . $suffix);
	}
	else if ($code !== '' && $atts['mode'] === 'productcode-flat')
	{
		$url = $baseurl . '/' . $code . $suffix . $ext;
		$defaultid = str_replace('.', '_', 'defaultProduct' . $code . $suffix);
	}

	if (isset($url))
	{
		return '<img id="' . $defaultid . '" src="' . $default . '" alt="'. $default . '" />'
		. '<img src="' . $url . '" alt="'. $url . '" onerror="javascript: jQuery(this).hide();" onload="javascript: jQuery(\'#' . $defaultid . '\').hide();" />';
	}
	else
	{
		return tp_hide_deps($atts['deps']);
	}
}

/* get the product image list
 * optional attributes:
*    mode - currently only productcode (ODP style - default) or productcode-flat (thumnail style) are supported
*           (could also use productid or name)
*    suffix - added to end of file name before ext - eg '.1', 'tn' or 'promo' for context images (default is empty string)
*    ext - file extension eg .jpg or .gif (default .jpg)
*    baseurl - base of the url (default is tp_promos_supplier_image_url)
*    default - default image to show if none found (default is tp_promos_default_image_url)
*    callback - callback javscript function name - to be called when gallery is loaded
*    id - element id of the ul list (default galleryImages)
*    max - maximum number of images to load (default 10)
*    deps - ids of dependent elements (eg #myimagesection) to hide if no code is given or mode isnt found
*
*    first image is special case that should load a default image if no gallery images found
*     callback on error since thats the end of the list
*    note that we need to bind the error events before assinging the src attributes.
*/
add_shortcode('tp-product-imagelist', 'tp_product_imagelist_shortcode');
function tp_product_imagelist_shortcode($atts, $content)
{
	$url = null;
	$suffix = isset($atts['suffix']) ? $atts['suffix'] : '';
	$ext = isset($atts['ext']) ? $atts['ext'] : ".jpg";
	$baseurl = isset($atts['baseurl']) ? $atts['baseurl'] : get_option('tp_supplier_images_url');
	$max = isset($atts['max']) ? intval($atts['max']) : 10;
	$default = isset($atts['default']) ? $baseurl . '/' . $atts['default'] : get_option('tp_default_image_url');
	$id = isset($atts['id']) ? $atts['id'] : 'galleryImages';
	$callback = $atts['callback'];
	$defaultid = null;

    $code = tp_product_data('code');
	$suppcode = substr($code, 5, 6);

	if ($code !== '' && (!isset($atts['mode']) || $atts['mode'] === 'productcode'))
	{
		$url = $baseurl . '/Supplier_' . $suppcode . '/Option_' . $code . '/'. $code;
	}
	else if ($code !== '' && $atts['mode'] === 'productcode-flat')
	{
		$url = $baseurl . '/' . $code;
	}

	if (isset($url))
	{
		$imglist = '<ul id="'.$id.'">';
		for ($i=1; $i <= $max; $i++)
		{
			$src = $url . $suffix . '.' . $i . $ext;
			$imglist .= '<li><img '. ($i==1?'class="tpGalleryFirstImage"':'') . ' alt="' . $src . '" /></li>';
		}
		$imglist .= '</ul>';

		$imglist .= '<script>jQuery(document).ready(function() {
		var tpGalleryImageCount = 0;
		jQuery("#'.$id.' li img").load(function(){
		   if (++tpGalleryImageCount>=10){
		     '.(isset($callback) ? trim($callback) . '(\''. $id .'\');': '').'
		   }
	    }).error(function(){
	        if (jQuery(this).is(".tpGalleryFirstImage")){
	            jQuery(\'#'.$id .'\').append(\'<li><img src="'.$default.'" alt="default product image" /></li>\');
	        }
		    jQuery(this).parent().remove();
	        if (++tpGalleryImageCount>=10){
		      '.(isset($callback) ? trim($callback) . '(\''. $id .'\');': '').'
		    }
	    }).each(function(i,e){
	        jQuery(e).attr("src", jQuery(e).attr("alt"));
	    });
	    });</script>';

	    return $imglist;
	}
	else
	{
	    return tp_hide_deps($atts['deps']);
	}
}



/*
 * Insert a google map
 * id - (default mapsection)
 * title - (defaults to product's name)
 * code - note category for map coordinates (default MAP)
*/
add_shortcode('tp-product-map', 'tp_product_map_shortcode');
function tp_product_map_shortcode($atts, $content)
{
	$id = isset($atts['id']) ? $atts['id'] : 'mapsection';
	$title = isset($atts['title']) ? $atts['title'] : tp_product_data('name');
	$code = isset($atts['code']) ? $atts['code'] : 'MAP';
	$note = tp_product_note($code);
	$map = tp_google_map($id, $title, $note['text']);
	return isset($map) ? $map : tp_hide_deps($atts['deps']);
}

add_shortcode('tp-payment-processing', 'tp_payment_processing_shortcode');
function tp_payment_processing_shortcode($atts, $content) {
	$getParams = implode("&", array_map(function($getKey, $getVal) {
		return $getKey . '=' . $getVal;
	}, array_keys($_GET), $_GET));

	$formContent = "";

	$a = shortcode_atts(array(
		'method' => 'POST'), $atts);

	$src = ($a['method'] == 'POST' ? $_POST : $_GET);

	foreach($src as $postKey => $postVal) {
		$formContent .= "<input type='hidden' name='" . $postKey . "' value='" . $postVal . "' />";
	}
	
	$autoSubmitScript = "<script>" .
						"$(document).ready( function() { " .
							"$('#processForm').submit(); " .
						"});" .
						"</script>";

	return "<form id='processForm' method='" . $a['method'] . "' action='" . tp_payment_url() . "?" . $getParams	. "'>" .
			$formContent .
			"</form>" . $autoSubmitScript;

}

/*
// SAMPLE page:

<h1>[tp-supplier-name]</h1>

<div style="position:relative;top:0;left:0;">
    <div class="className className[tp-supplier-class]">[tp-supplier-class]</div>
    <br/><br /><br/>
</div>

<div style="position:relative;top:0;left:0;">
  <div class="product_content_section_wrapper">
     <div id="gallerysection">
        [tp-supplier-imagelist callback="tpGalleryLoaded"]
     </div>
     <div class="tpproduct-accommodation">
        [tp-supplier-map code="MAP"]
     </div>
  </div>
</div>

<p>
    [tp-supplier-address line="1"]<br />
    [tp-supplier-address line="2"]<br />
    [tp-supplier-address line="3"]<br />
    [tp-supplier-postcode]
</p>

<h4 id="ntusection">
    <span id="ntulabel">Nearest Tube:</span>  <em>[tp-supplier-note code="NTU" deps="#ntusection"]</em>
</h4>


<h2>Hotel Description</h2>
<p id="ciosection">
    <em><span id="ciolabel">Checkin/out: </span>[tp-supplier-note code="CIO" deps="#ciosection"]</em>
</p>

<p id="dlosection">
    [tp-supplier-note code="DLO" deps="#dlosection"]
</p>


-------------------- Theatre --------
<h1>[tp-supplier-name]</h1>
<div style="position: relative; top: 0; left: 0;">
<div class="product_content_section_wrapper">
<p>[tp-supplier-image suffix=".1"]</p>
<div class="product_content_section_hightlight_right">
<div id="videosection_wrapper">[tp-supplier-video]</div>
<div id="contextimagessection">[tp-supplier-image suffix=".promo.1"]</div>
</div>
</div>
</div>
<p class="addressline">[tp-supplier-address line="1"]</p>
<p class="addressline">[tp-supplier-address line="2"]</p>
<p class="addressline">[tp-supplier-address line="3"]</p>
<p class="postcode">[tp-supplier-postcode]</p>
<h4 id="ntusection"><span id="ntulabel">Nearest Tube:</span> <em>[tp-supplier-note code="NTU" deps="#ntusection"]</em></h4>
<div id="hoteldescriptionsection">
<h2>Theatre Description</h2>
<div class="tpproduct-theatre">[tp-supplier-map code="MAP"]</div>
<p id="dlosection">[tp-supplier-note code="DLO" deps="#dlosection"]</p>
</div>

 */