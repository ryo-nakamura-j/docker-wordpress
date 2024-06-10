 <?php
/**
 * Plugin Name: Tourplan Config plugin
 * Plugin URI: http://tourplan.com/P
 * Description: A tourplan retail plugin that includes the retail config meta data into the DOM.
 * Version: 0.1
 * Author: Justin Barton
 * Author URI: http://www.tourplan.com
 */

require_once('tp-util.php');
require_once('tp-shortcodes.php');

// ---------------- Session timeout ----------------------

function tpSetupSession($tp_timeout_idle)
{
  if (isset($tp_timeout_idle) && is_int($tp_timeout_idle) && $tp_timeout_idle > 0)
  {
    if (!isset($_SESSION['timeout_idle']))
    {
      session_start();
    }
    if (!isset($_SESSION['timeout_idle']))
    {
      session_destroy();
      session_start();
      session_regenerate_id();
      $_SESSION = array();
    }
    $_SESSION['timeout_idle'] = time() + $tp_timeout_idle;
  }
}

if (!isset($_SESSION['tp_timeout_idle']))
{
    $tpidle = get_option('tp_timeout_idle'); // eg 1440 == 20mins
    $_SESSION['tp_timeout_idle'] = isset($tpidle) && is_numeric($tpidle) ? intval($tpidle) : 0;
}
tpSetupSession($_SESSION['tp_timeout_idle']);

// ------------------  languages -----------------------

function tp_current_language()
{
	return function_exists('pll_current_language') ? pll_current_language() : '';
}

function tp_languages()
{
	return explode(',', get_option('tp_languages'));
}

// the language that will use tourplans labels directly (not translated) 
// could be any latin language tourplan supports eg french german
function tp_system_language()
{
	$langs = tp_languages();
	$firstlang = $langs[0];
	return empty($firstlang) ? 'en' : $firstlang;
}
// ------------------  retail pages urls -----------------------

function tp_home_url($language = true)
{
	return $language ? site_url(tp_current_language()) : site_url();
}

function tp_itinerary_url()
{
	return  tp_home_url(false) . '/' .  tp_get_url('tp_itinerary_url');
}

function tp_checkout_url()
{
	return tp_home_url() . '/' .  tp_get_url('tp_checkout_url');
}

function tp_payment_url()
{
	return tp_home_url(false) . '/' .  tp_get_url('tp_payment_url');
}

function tp_payment_success_url()
{
	return tp_home_url(false) . '/' .  tp_get_url('tp_payment_success_url');
}

function tp_payment_failed_url()
{
	return tp_home_url(false) . '/' . tp_get_url('tp_payment_failed_url');
}

function tp_booking_confirmed_url()
{
	return tp_home_url() . '/booking-confirmed';
}

function tp_booking_requested_url()
{
	return tp_home_url() . '/booking-requested';
}

function tp_booking_failed_url()
{
	return tp_home_url() . '/booking-failed';
}

function tp_booking_status_url($status)
{
	if ($status == 'confirmed') {
		return tp_booking_confirmed_url();
	} else if ($status == 'requested') { 
		return tp_booking_requested_url();
	} else {
		return tp_booking_failed_url();
	}
}

// remove the wordpress auto formatting of content (adds spurious <p> tags and <br>s)
remove_filter('the_content','wpautop');
remove_filter('the_entry','wpautop');

/*
 * add tourplan actions for admin pages and for wp_head
 */

if (is_admin())
{
  tp_log('tp-config is_admin');
  require_once('tp-admin.php');
}
else
{
  add_action('template_redirect','tp_page_logic');
}

function tp_page_logic()
{
	global $post;

	tp_log('tp-config tp_page_logic');
	
    if (is_page('tp-app')) 
	{
		tp_app_page();
	}
	else if (is_page('shopping-cart') || is_page('shopping-cart-secure'))
	{
		require_once('tp-cart.php');
		tp_shopping_cart_page();
	}
	else if (is_page('checkout') || tp_page_contains_section('tourplan_checkout_page'))
	{
		require_once('tp-checkout.php');
		add_action('wp_head', 'tp_config_head', 5);
		tp_checkout_page();
	}
	else if (is_page('payment-response'))
	{
		tp_log('handling payment-response page ' . $_SERVER['HTTP_METHOD'] . ' ' . print_r($_POST,true));
		require_once('tp-payment.php');
		tp_payment_response_page();
	}
	else if (is_page('payment-success'))
	{
		tp_log('handling payment-success page ' . $_SERVER['HTTP_METHOD']);
		require_once('tp-payment.php');
		tp_payment_success_page();
	}
	else if (is_page('payment-failed'))
	{
		tp_log('handling payment-failed page ' . $_SERVER['HTTP_METHOD']);
		require_once('tp-payment.php');
		tp_payment_failed_page();
	}
	else if (is_page('ipay88-backend') && get_option('tp_payment_gateway') == 'ipay88')
	{
		tp_log('handling ipay88-backend page ' . print_r($_POST, true));
		require_once('tp-ipay88.php');
		tpIPay88BackendPage();
	}
	else if (is_page('book'))
	{
		tp_log('booking');
		require_once('tp-book.php');
		tp_book_rail();
	}
	else
	{
	    tp_log('tp_page_logic add_action tp_config_head');
		add_action('wp_head', 'tp_config_head', 5);
	}
}

function tp_add_search_scripts()
{
    wp_register_script('jquery.loadmask', get_template_directory_uri() . '/js/jquery.loadmask.min.js', array('jquery'), '0.4');
    wp_enqueue_script('jquery.loadmask');

    wp_register_script('tp_search', plugins_url('search/search.nocache.js', __FILE__));
    wp_enqueue_script('tp_search');
}

function tp_add_product_scripts()
{
    wp_register_script('jquery.loadmask', get_template_directory_uri() . '/js/jquery.loadmask.min.js', array('jquery'), '0.4');
    wp_enqueue_script('jquery.loadmask');

    wp_register_script('tp_product', plugins_url('product/product.nocache.js', __FILE__));
    wp_enqueue_script('tp_product');
}

function tp_add_cart_scripts()
{
    wp_register_script('tp_cart', plugins_url('cart/cart.nocache.js', __FILE__));
    wp_enqueue_script('tp_cart');
}

function tp_config_inject($cfgparams)
{
	echo '<script type="text/javascript">';
	
	$lang = tp_current_language();

	echo 'window.tpServiceButtonConfigs=' . tp_servicebuttonconfigs() . ';';
	echo 'window.tpBookingFees=' . tp_read_plugin_file('bookingfees.js') . ';';
	echo 'window.tpDeliveryFees=' . tp_read_plugin_file('deliveryfees.js') . ';';
	echo 'window.tpPaymentFees=' . tp_read_plugin_file('paymentfees.js') . ';';
	echo 'window.tpSearchParams=' . tp_search_params() . ';';
	echo '</script>';
	echo '<meta id="tourplanRetailConfig" ' . $cfgparams . '/>';
}

function tp_config_head()
{
	global $post;

	$cfgparams = 'serverUrl="' . tp_get_url('tp_app_url') . '"'
	   . ' language="' . tp_current_language() . '"' 
	   . ' imagesUrl="'.tp_get_url('tp_supplier_images_url') . '"'
	   . ' defaultImage="' . get_option('tp_default_image_url') . '"'
	   . ' loadingImage="' . tp_get_url('tp_search_images_url') . '/loading.gif"'
	   . ' calendarIcon="' . get_template_directory_uri()  . '/images/calendar.png"'
	   . ' searchImagesUrl="' . tp_get_url('tp_search_images_url') . '"'
	   . ' itineraryPage="' . tp_itinerary_url() .'"'
	   . ' checkoutPage="' . (is_page('itinerary') || tp_page_contains_section("tourplan_itinerary") ? tp_checkout_url()  : '') . '"'
	   . ' currency="' . get_option('tp_currency') . '"'
	   . ' reqTimeout="' . get_option('tp_request_timeout', 20000) . '"';

	if (get_option('tp_payment_alt') === 'true' && get_option('tp_payment_gateway') !== 'none')
	{
	   $cfgparams .= ' checkoutAltPage="' . (is_page('itinerary') && tp_is_payment_alt_cart() ?  tp_checkout_url() . '?tppaymentalt=true' : '') . '"';
	}
	
	$proddefaults = tp_customfield('tpProductDefaults');
	$searchdefaults = tp_customfield('tpSearchDefaults');
	$prodcfg = tp_customfield('tpProduct');
	$searchcfg = tp_customfield('tpSearch');
	$cartcfg = tp_customfield('tpCart');

	if (isset($searchdefaults) && strlen($searchdefaults)>0)
	{
	   $cfgparams .= ' searchParamsDefaults="' . $searchdefaults . '"';
	}
	if (isset($searchcfg) && strlen($searchcfg)>0)
	{
	   $cfgparams .= ' ' . $searchcfg;
	}

	if (isset($proddefaults) && strlen($proddefaults)>0)
	{
	   $cfgparams .= ' productDefaults="' . $proddefaults . '"';
	}
	if (isset($prodcfg) && strlen($prodcfg)>0)
	{
	   $cfgparams .= ' ' . $prodcfg;
	}
	
	// $requires = tp_page_requires_configs() ? "YES" : "false";

	// tp_log("JZ_DEBUG REQUIRES: " . $requires);

	if (isset($proddefaults) && strlen($proddefaults)>0 || isset($prodcfg) && strlen($prodcfg)>0
	   ||isset($searchdefaults) && strlen($searchdefaults)>0 || isset($searchcfg) && strlen($searchcfg)>0 )
	   // || tp_page_requires_configs())
	{
	   $cfgparams .= ' cartUrl="' . site_url(tp_get_url('tp_cart_url')) . '/"';
	   tp_config_inject($cfgparams);
	}
	if (isset($proddefaults) && strlen($proddefaults)>0 || isset($prodcfg) && strlen($prodcfg)>0)
	{
	   tp_add_product_scripts();
	   tp_setup_product_data();
	}
	if (isset($searchdefaults) && strlen($searchdefaults)>0 || isset($searchcfg) && strlen($searchcfg)>0)
	{
		tp_add_search_scripts();
	}

    if (is_page('itinerary') || is_page('checkout'))
    {
	   $cfgparams .= ' cartUrl="' . site_url(tp_get_url('tp_cart_url'), tp_page_protocol() == 'https' ? 'https' : 'http') . '/"';

	   if (isset($cartcfg) && strlen($cartcfg)>0)
	   {
		   $cfgparams .= ' ' . $cartcfg;
	   }
       tp_config_inject($cfgparams);
       tp_add_cart_scripts();
    }

    $acf_page = tp_page_requires_configs();
    if ($acf_page) {
	    $cfgparams .= ' cartUrl="' . site_url(tp_get_url('tp_cart_url')) . '/"';
		tp_config_inject($cfgparams);
    }
}

function tp_page_requires_configs() {
	global $post;

	$res = false;
	if (function_exists("have_rows")) {
		if (have_rows("sections", $post->ID)):
			while (have_rows('sections', $post->ID)) : the_row();
				switch (get_row_layout()) {
					case "tourplan_non-accommodation_product":
					case "tourplan_two_product_rail_pass":
					case "tourplan_two_supplier_rail_pass":
					case "tourplan_itinerary":
					case "tourplan_product_search":
					case "tourplan_product_page":
					case "tourplan_jtb_tour_page":
					case "tourplan_checkout_page":
					case "tourplan_multiple_product_single_book":
						$res = true;
					default:
						break;
				}
			endwhile;
		endif;
	}
	return $res;
}

function tp_page_contains_section($section) {
	global $post;

	$contains = false;
	if (function_exists("have_rows")){
		while (have_rows('sections', $post->ID)) : the_row();
			if (get_row_layout() == $section) { $contains = true; }
		endwhile;
	}

	return $contains;
}

function tp_servicebuttonconfigs()
{
	$lang = tp_current_language();
	if (empty($lang))
	{
		$lang = tp_system_language();
	}

	$configs = get_transient('tpservicebuttonconfigs-' . $lang);
	if ($configs === false)
	{
		tp_refresh_servicebuttonconfigs();
		$configs = get_transient('tpservicebuttonconfigs-' . $lang);
	}
	return $configs;
}

function tp_refresh_servicebuttonconfigs()
{
	$labelsstr = get_option('tp_labels_json');
	$configsstr = get_option('tp_configs_json');
	$labelsjson = json_decode(urldecode($labelsstr), true);
	$configsjson = json_decode(urldecode($configsstr), true);

	$langs = tp_languages();
	foreach ($langs as $lang)
	{
		$json = array();
		$lbls = $labelsjson[$lang];
		$cfgs = $configsjson[$lang];
		foreach ($lbls as $lbl)
		{
			foreach ($cfgs as $cfg)
			{
				if ($cfg['serviceButton'] === $lbl['serviceButton'])
				{
					$json[] = array('serviceButton' => $cfg['serviceButton'], 'config' => array_merge($lbl['config'], $cfg['config']));
					break;
				}
			}
		}
		set_transient('tpservicebuttonconfigs-' . $lang, json_encode($json), 24*60*60);
	}
}

function tp_fetch_searchparams()
{
	$serversearchparamsurl = tp_get_url('tp_app_url') . '/searchparams';
	$searchparams = wp_remote_get($serversearchparamsurl, array('timeout' => 20, 'sslverify' => false));
	if (is_wp_error($searchparams) || $searchparams['response']['code'] !== 200 || strlen($searchparams['body'])==0 || strpos($searchparams['body'], '{"lookups":')!==0)
	{
		$searchparams = wp_remote_get($serversearchparamsurl, array('timeout' => 20, 'sslverify' => false));
	}
	return (!is_wp_error($searchparams))
			&& $searchparams['response']['code'] === 200
			&& strlen($searchparams['body'])>0
			&& strpos($searchparams['body'], '{"lookups":') == 0
			? $searchparams['body']
			: false;
}

function tp_refresh_searchparams()
{
	$tplookups = get_option('tplookups');
	$tplookupmaps = get_option('tplookupmaps');
	foreach ($tplookups as $lang => $lookups)
	{
		$lookupsjs = array();
		foreach ($lookups as $type => $codename)
		{
			foreach ($codename as $code => $name)
			{
				$lookupsjs[] = array('type' => (string)$type, 'code' => (string)$code, 'name' => (string)$name);
			}
		}
		$json = json_encode(array('lookups' => $lookupsjs, 'lookupmaps' => $tplookupmaps), true);
		set_transient('tpsearchparams-' . $lang, $json, 60*60);
	}
}

function tp_refresh_translations() 
{
	$tplookups = get_option('tplookups');
	$tptranslationsjson = get_option('tp_translations_json');
	if ($tplookups !== false && $tptranslationsjson !== false)
	{
		$tptranslations = json_decode(urldecode($tptranslationsjson), true);
		$tplookups = array_merge($tplookups, $tptranslations);
		update_option('tplookups', $tplookups);
	}
}

function tp_reload_searchparams()
{	
	$searchparams = tp_fetch_searchparams();
	if ($searchparams !== false)
	{
		$defaultparams = json_decode($searchparams, true);
			
		$defmap = array();
		foreach ($defaultparams['lookups'] as $p)
		{
			$p['code'] = (string) $p['code'];
			if (!array_key_exists($p['type'], $defmap))
			{
				$defmap[$p['type']] = array();
			}
			$defmap[$p['type']][$p['code']] = $p['name'];
		}

		
		$tplookups = array(tp_system_language() => $defmap);
		$tplookupmaps = $defaultparams['lookupmaps'];
		
		$tptranslationsjson = get_option('tp_translations_json');
		if ($tptranslationsjson !== false)
		{
		    $tptranslations = json_decode(urldecode($tptranslationsjson), true);
		    $tplookups = array_merge($tplookups, $tptranslations);  
		}
		
		update_option('tplookups', $tplookups);
		update_option('tplookupmaps', $tplookupmaps);
	}
	
	tp_refresh_searchparams();
}

function tp_search_params()
{
	$lang = tp_current_language();
	if (empty($lang))
	{
		$lang = tp_system_language();
	}
	

	$searchparams = get_transient('tpsearchparams-' . $lang);
	if ($searchparams === false)
	{
		tp_reload_searchparams();
		$searchparams = get_transient('tpsearchparams-' . $lang);
	}
	return $searchparams;
}

function tp_lookups()
{
	$lookups = get_option('tplookups');
	if (empty($lookups))
	{
		$searchparams = tp_search_params();
		$lookups = $searchparams['lookups'];
	}
	else 
	{
		$lang = tp_current_language();
		$lookups = $lookups[empty($lang) ? 'Default' : $lang];
	}
	return $lookups;
}


function tp_lookup($type, $code)
{
	$lookups = tp_lookups();
    $lookuptype = $lookups[$type];
	$lookup = $lookuptype[$code];
	return isset($lookup) ? $lookup : null;
}

function tp_normalise_segment($segment)
{
	return urlencode(preg_replace(array('/[^a-z0-9\-]/', '/\-+/'), array('', '-'), str_replace(' ', '-', strtolower(trim($segment)))));
}

// setup the product data for product content pages static content
// use the url, or query params or custom fields
function tp_setup_product_data()
{
	$tptitle = null;
	$tpkeywords = null;
	$tpdescription = null;
	$srb = null;
	$supplierid = null;
	$productid = null;

	$seosupplier = null;
	$seoproduct = null;
	
	$proddefaults = tp_customfield('tpProductDefaults');
    if (isset($proddefaults) && strlen($proddefaults)>0)
	{
		foreach (explode('&',$proddefaults) as $proddef)
		{
			$defsplit = explode('=', $proddef);
			if ($defsplit[0]=='srb')
			{
				$srb = $defsplit[1];
			}
			else if ($defsplit[0]=='supplierid')
			{
				$supplierid = $defsplit[1];
			}
			else if ($defsplit[0]=='productid')
			{
				$productid = $defsplit[1];
			}
		}
	}
	
	if (isset($_GET["srb"]))
	{
		$srb = $_GET["srb"];
	}
	if (isset($_GET["supplierid"]))
	{
		$supplierid = $_GET["supplierid"];
	}
	if (isset($_GET["productid"]))
	{
		$productid = $_GET["productid"];
	}
	
	if (isset($srb) && is_supplier_level($srb) && isset($supplierid))
	{
		$url = str_replace('https://','http://',tp_get_url('tp_app_url')) . '/supplier/' . $supplierid;

		$resp = wp_remote_get($url);
		if (is_wp_error($resp))
		{
			tp_log('tp-product-content.php - error getting ' . $url . ': ' . print_r( $resp, true ));
		}
		else
		{
			$json = $resp['body'];
			if ($json !== FALSE)
			{
				$supp = json_decode($json)->supplier;
				if (isset($supp))
				{
					$suppmap = json_decode($json, true);
					$prodmap = $suppmap['products'][0];

					if (isset($productid))
					{
						foreach ($suppmap['products'] as $p)
						{
							if ($p['productid'] == $productid)
							{
								$prodmap = $p;
							}
						}
					}

					// set to request scope so its valid only for each page
					$_REQUEST['tp-supplier'] = $suppmap['supplier'];
					$_REQUEST['tp-product'] = $prodmap;
				}
			}
		}
	}
	elseif (isset($srb) && isset($productid))
	{
		$url = str_replace('https://','http://',tp_get_url('tp_app_url')) . '/product/' . $productid;

		$resp = wp_remote_get($url);
		if (is_wp_error($resp))
		{
			tp_log('tp-product-content.php - error getting ' . $url . ': ' . print_r( $resp, true ));
		}
		else
		{
			$json = $resp['body'];
			if ($json !== FALSE)
			{
				$suppprod = json_decode($json);
				if (isset($suppprod))
				{
					$prod = $suppprod->products[0];
					if (isset($prod))
					{
						$prodmap = json_decode($json, true);

						// set to request scope so its valid only for each page
						$_REQUEST['tp-supplier'] = $prodmap['supplier'];
						$_REQUEST['tp-product'] = $prodmap['products'][0];
					}
				}
			}
		}
	}
}

/* TODO: not used yet - but idea is this will allow wordpress to be the engine proxy */
function tp_app_page()
{
    $uri = $_SERVER['REQUEST_URI'];

	// proxy to retail engine so that clients talk to wordpress only
	//TODO need to add routing of search/supplier/product to tp-app page
	if (strpos($uri, '/tp-app/search')==0)
	{
	}
	else if (strpos($uri, '/tp-app/supplier')==0)
	{
	}
	else if (strpos($uri, 'tp-app/product')==0)
	{
	}
	else
	{
		// NOT allowed
	}
}

function tpBookingFees()
{
	return json_decode(tp_read_plugin_file('bookingfees.js'), true);
}

function tpDeliveryFees()
{
	return json_decode(tp_read_plugin_file('deliveryfees.js'), true);
}

function custom_post_types()
{
	$labels = array(
		'name' => 'Tickets',
		'singular_name' => 'Ticket',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Ticket',
		'edit_item' => 'Edit Ticket',
		'new_item' => 'New Ticket',
		'view_item' => 'View Ticket',
		'search_items' => 'Search Tickets',
		'not_found' => 'No tickets found',
		'not_found_in_trash' => 'No tickets found in trash',
		'parent_item_colon' => 'Parent Page',
		'all_items' => 'All Tickets',
	);


	$args = array(
		'label'               => __( 'Ticket', 'text_domain' ),
		'description'         => __( 'Ticket Description', 'text_domain' ),
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor', 'author', 'custom-fields', 'page-attributes' ),
		'rewrite'            => array( 'slug' => '/tickets123', 'with_front' => false)
	);

	register_post_type('tp_ticket_product', $args);

	$labels = array(
		'name' => 'Rail Passes',
		'singular_name' => 'Rail Pass',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Rail Pass', 'edit_item' => 'Edit Rail Pass',
		'new_item' => 'New Rail Pass',
		'view_item' => 'View Rail Pass',
		'search_items' => 'Search Rail Passes',
		'not_found' => 'No Rail Passes found',
		'not_found_in_trash' => 'No Rail Passes found in trash',
		'parent_item_colon' => 'Parent Page',
		'all_items' => 'All Rail Passes',
	);


	$args = array(
		'label'               => __( 'Rail Passes', 'text_domain' ),
		'description'         => __( 'Rail Pass Description', 'text_domain' ),
		'labels'              => $labels,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor', 'author', 'custom-fields', 'page-attributes' ),
		'rewrite'            => array( 'slug' => '/rail', 'with_front' => false)
	);

	register_post_type('tp_rail_pass_product', $args);

	$labels = array(
		'name' => 'Accommodation',
		'singular_name' => 'Accommodation',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Accommodation', 'edit_item' => 'Edit Accommodation',
		'new_item' => 'New Accommodation',
		'view_item' => 'View Accommodation',
		'search_items' => 'Search Accommodation',
		'not_found' => 'No Accommodation found',
		'not_found_in_trash' => 'No Accommodation found in trash',
		'parent_item_colon' => 'Parent Page',
		'all_items' => 'All Accommodation',
	);

	$args = array(
		'label'               => __( 'Accommodation', 'text_domain' ),
		'description'         => __( 'Accommodation Description', 'text_domain' ),
		'labels'              => $labels,
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor', 'author', 'custom-fields', 'page-attributes' ),
		'rewrite'            => array( 'slug' => '/ryokan-hotels2')
	);

	register_post_type('tp_accom_product', $args);

	// flush_rewrite_rules(false);
}
//add_action('init', 'custom_post_types', 0);

//include('tp-acf-types.php');

?>