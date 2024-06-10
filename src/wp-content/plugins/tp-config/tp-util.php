<?php

// PHP < 5.5.0
if (!function_exists('array_column')) {
	function array_column($input, $column_key, $index_key = null)
	{
		if ($index_key !== null) {
			// Collect the keys
			$keys = array();
			$i = 0; // Counter for numerical keys when key does not exist

			foreach ($input as $row) {
				if (array_key_exists($index_key, $row)) {
					// Update counter for numerical keys
					if (is_numeric($row[$index_key]) || is_bool($row[$index_key])) {
						$i = max($i, (int) $row[$index_key] + 1);
					}

					// Get the key from a single column of the array
					$keys[] = $row[$index_key];
				} else {
					// The key does not exist, use numerical indexing
					$keys[] = $i++;
				}
			}
		}

		if ($column_key !== null) {
			// Collect the values
			$values = array();
			$i = 0; // Counter for removing keys
			if (is_array($input))
			{
				foreach ($input as $row) {
					if (array_key_exists($column_key, $row)) {
						// Get the values from a single column of the input array
						$values[] = $row[$column_key];
						$i++;
					} elseif (isset($keys)) {
						// Values does not exist, also drop the key for it
						array_splice($keys, $i, 1);
					}
				}
			}
		} else {
			// Get the full arrays
			$values = array_values($input);
		}

		if ($index_key !== null) {
			return array_combine($keys, $values);
		}

		return $values;
	}
}

function tp_log($msg)
{
	error_log($_SERVER["SERVER_NAME"] . ' ' . $_SERVER["REQUEST_URI"] . ' ' . $msg);
}

function tp_get_url($optionname, $lang = false)
{
	if ( $lang == false )
		return rtrim(get_option($optionname), " /");
	else {
		$ext = tp_cur_language_exts();
		$ex = ( $ext == null || $ext == "" ) ? "" : "_" . $ext;
		return rtrim(get_option($optionname . $ex), " /");
	}
}

function tp_page_protocol()
{
	return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
}

function tp_read_plugin_file($filename)
{
	$js = 'null';
	$fileurl = plugins_url($filename, __FILE__);
	$filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $filename;

	if (file_exists($filepath))
	{
		$js = file_get_contents($filepath);
		if (empty($js))
		{
            $error = error_get_last();
            error_log( "HTTP request failed. Error was: " . $error['message'] );
			$js = 'null';
		}
	}
	return $js;
}

function tpEnc($str, $len, $pad=0)
{
	return isset($pad) ? str_pad(urlencode($str), $len, $pad, STR_PAD_LEFT) : substr(urlencode($str), 0, $len);
}

function tpEncEmail($str, $len)
{
	$s = str_replace('%40', '@', urlencode($str));
	return isset($len) && strlen($s) > $len ? substr($s, 0, $len) : $s;
}

function tpXmlEscape($str, $maxlen)
{
	$s = strtr(trim($str), array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;', '"' => '&quot;', "'" => '&apos;'));
	return isset($maxlen) && strlen($s) > $maxlen ? strstr($s, 0, $maxlen) : $s;
}

function tpXmlExtractSingle($xmlstr, $tag)
{
	if (empty($xmlstr))
	{
		return null;
	}
	else
	{
		$result = preg_match('/[<]' . $tag . '[>](.*?)[<][\/]' . $tag . '[>]/i', $xmlstr, $matches);
		return $result === 1 ? $matches[1] : null;
	}
}

function tpXmlExtractAttr($xmlstr, $tag, $attr)
{
	if (empty($xmlstr))
	{
		return null;
	}
	else
	{
		$result = preg_match('/[<]' . $tag . ' (.*?)[>]/i', $xmlstr, $matches);
		$tagattrs =($result === 1 ? $matches[1] : null);
		if ($tagattrs == null)
		{
			return null;
		}
		$result = preg_match('/ ' . $attr . '="(.*?)"/i', ' ' . $tagattrs, $matches);
		return $result === 1 ? $matches[1] : null;
	}
}

// or sprintf("%01.2f", $money)
function tpDollars($cents)
{
	$roundCents = is_numeric($cents) ? round($cents, 0, PHP_ROUND_HALF_DOWN) : 0;
	return number_format($roundCents/100.0, 2, '.', '');
}



function tpSetCart($jsonStr)
{
	$sessionStartedInHere = false;
	if (session_status() == PHP_SESSION_NONE) {
		tp_log('Called session_start() on tpSetCart');
		$sessionStartedInHere = true;
    	session_start();
	}
	$_SESSION['tpshoppingcart'] = $jsonStr;
	tp_log('tpSetCart tpshoppingcart Session [' . session_id() . '] ' . print_r($_SESSION['tpshoppingcart'], true));
	if ( $sessionStartedInHere ) {
		session_write_close();
	}
	return stripslashes($jsonStr);
}

function tpGetCart()
{
	if ( !isset($_SESSION['tpshoppingcart']) )
		tp_log('tpGetCart tpshoppingcart Session [' . session_id() . '] - tpshoppingcart not found');
	return isset($_SESSION['tpshoppingcart']) ? ($_SESSION['tpshoppingcart']) : tpSetCart('{}');
}

function tpGetCartArray()
{
	return json_decode(tpGetCart(), true);
}

function tpCartIsEmpty($cartArray = null)
{
	$cart = isset($cartArray) ? $cartArray : tpGetCartArray();
	return empty($cart) || empty($cart['servicelines']);
}

function tpParseDate($datestr)
{
	$dt = strtotime($datestr);
	if ($dt !== false)
	{
		return $dt;
	}
	return null;
}

function tpDateAgeDays($datestr)
{
	$dt = tpParseDate($datestr);
	return isset($dt) ? date_diff(new DateTime(date("c", $dt)), new DateTime())->days : 0;
}

function tpGetConfigs()
{
	$configsfilename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configs.js';
	if ( file_exists( $configsfilename) ) {
		$configsstr = file_get_contents( $configsfilename );
		return json_decode($configsstr, true);
	}
	tp_log("tp-util: configs.js file not found");
	return array();
}

function is_supplier_level($srb)
{
	$issupplierlevel = true;
	$configs = tpGetConfigs();
	foreach ($configs as $cfgs)
	{
		$name = $cfgs['serviceButton'];
		if (empty($name))
		{
			foreach ($cfgs['config'] as $cfg)
			{
				if ($cfg['name']==='resultsByProduct')
				{
					$issupplierlevel = ($cfg['value']==='false');
					break;
				}
			}
		}
	}

	foreach ($configs as $cfgs)
	{
		if ($cfgs['serviceButton'] === $srb)
		{
			foreach ($cfgs['config'] as $cfg)
			{
				if ($cfg['name']==='resultsByProduct')
				{
					$issupplierlevel = ($cfg['value']==='false');
					break;
				}
			}
		}
	}
	return $issupplierlevel;
}

function tp_customfield($name)
{
	global $post;
	return trim(get_post_meta($post->ID, $name, true));
}

/*
* paymentalt - ie. banktransfer allow bookng to be made onrequest and payment made out-of-band.
* Several Tourplan Settings are needed: tp_payment_alt='true', and tp_payment_gateway!=none
* the checkout page customfield tpCart should have attribute usePaymentAltParam="true"
* finally the cart must have all servicedates after tp_payment_alt_start_days
* but serviceButtons that skip servicedate checking are configured by isPaymentAltProduct="true" in configs.js
*/
function tp_is_payment_alt_enabled()
{
	return get_option('tp_payment_alt') === 'true' && get_option('tp_payment_gateway') !== 'none';
}

function tp_use_payment_alt_param()
{
	$tpcartcustom = tp_customfield('tpCart');
	return isset($tpcartcustom) && stripos($tpcartcustom, 'usePaymentAltParam="true"') !== false;
}

function tp_is_payment_alt_param()
{
	return is_page( tp_checkout_pagename() ) && tp_is_payment_alt_enabled() && tp_use_payment_alt_param() && $_GET['tppaymentalt'] === 'true';
}

function tp_is_payment_alt_product($serviceline)
{
	$servicetype = $serviceline['servicetype'];
	if ($servicetype === 'Fee')
	{
		return false;
	}

	$defaultPaymentAltProduct = false;
	$configs = tpGetConfigs();
	foreach ($configs as $cfgs)
	{
		$srb = $cfgs['serviceButton'];
		if (empty($srb))
		{
			foreach ($cfgs['config'] as $cfg)
			{
				if ($cfg['name']==='isPaymentAltProduct')
				{
					$defaultPaymentAltProduct = $cfg['value']==='true';
				}
			}
		}
		else if ($srb === $servicetype)
		{
			foreach ($cfgs['config'] as $cfg)
			{
				if ($cfg['name']==='isPaymentAltProduct')
				{
					return $cfg['value']==='true';
				}
			}
		}
	}
	return $defaultPaymentAltProduct;
}


function tp_is_payment_alt_cart()
{
	$cart = tpGetCartArray();
	if (tpCartIsEmpty($cart))
	{
		return false;
	}

	$startdays = get_option('tp_payment_alt_start_days');
	if (isset($startdays))
	{
		foreach ($cart['servicelines'] as $line)
		{
			if (!tp_is_payment_alt_product($line))
			{
				$serviceDate = $line['date'];
				//error_log('tp_is_payment_alt_cart ' . $serviceDate . ' ' . tpDateAgeDays($serviceDate) . ' ' . $startdays);
				if (isset($serviceDate) && tpDateAgeDays($serviceDate) < intval($startdays))
				{
					return false;
				}
			}
		}
	}
	return true;
}

function tp_get_supplier_image_list($extension = '.jpg')
{
	$url = null;
	$baseurl = get_option('tp_supplier_images_url');
	$default = get_option('tp_default_image_url');

	$code = tp_supplier_data('code');
	if ($code !== '') {
		$url = $baseurl . '/Supplier_' . $code . '/' . $code;
	}

	$imglist = array();

	if (isset($url)) {
		for ($i = 1; $i <= 10; $i++) {
			$imglist[] = $url . '.' . $i . $extension;
		}
	}

	return $imglist;

}