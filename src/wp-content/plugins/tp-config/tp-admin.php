<?php

// register_activation_hook(__FILE__, 'tp_plugin_activation');

function tp_plugin_activation()
{
	tp_log('tp_plugin_activation');
	$tppages = array (
			array('post_name' => 'shopping-cart', 'post_title' => 'shopping-cart', 'post_content' => '', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'shopping-cart-secure', 'post_title' => 'shopping-cart', 'post_content' => '', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'itinerary', 'post_title' => 'Itinerary', 'post_content' => '[tp-cart]', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'checkout', 'post_title' => 'Checkout', 'post_content' => '[tp-customer] [tp-cart] [tp-creditcard-form]', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'payment-response', 'post_title' => 'Payment', 'post_content' => '', 'post_status' => 'publish' , 'post_type'=>'page'),
			array('post_name' => 'payment-success', 'post_title' => 'Payment Success', 'post_content' => '', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'payment-failed', 'post_title' => 'Payment Failed', 'post_content' => '', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'booking-confirmed', 'post_title' => 'Booking Confirmed', 'post_content' => 'Reference: [tp-booking-ref]', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'booking-requested', 'post_title' => 'Booking Requested', 'post_content' => 'Reference: [tp-booking-ref]', 'post_status' => 'publish', 'post_type'=>'page'),
			array('post_name' => 'booking-failed', 'post_title' => 'Booking Failed', 'post_content' => '[tp-error] - Reference: [tp-booking-ref]', 'post_status' => 'publish', 'post_type'=>'page')

	);
	foreach ($tppages as $tppage)
	{
		$tppagename = $tppage['post_name'];
		tp_log('checking page ' . $tppagename);
		$found = get_posts(array('name' => $tppagename,'post_type'=>'page','posts_per_page'=>1));
		if (empty($found))
		{
			$tppageid = wp_insert_post($tppage);
			tp_log('new page ' . $tppagename . ' id=' . $tppageid);
			if ($tppagename == 'itinerary')
			{
				add_post_meta($tppageid, 'tpCart', 'cartItemsReadOnly="false"', true);
			}
			else if ($tppagename == 'checkout')
			{
				add_post_meta($tppageid, 'tpCart', 'cartItemsReadOnly="true"', true);
			}
		}
	}
}

add_action('admin_head', 'tp_settings_head');
add_action('admin_menu', 'tp_settings_menu');

function tp_product_pages()
{
    $q = new WP_Query(array(
	 'post_type' => array('page','post'),
	 'post_status' => 'publish',
	 'nopaging' => true,
	 'posts_per_page' => -1,
	 'meta_key' => 'tpProduct',
	 'meta_value' => 'show="false"',
	 'meta_compare' => '!='));

    $q2 = new WP_Query(array(
    	'post_type' => array('page','post'),
    	'post_status' => 'publish',
    	'meta_key' => 'product_page',
    	'meta_value' => true,
    	'posts_per_page' => -1
    	)
    );

    // return array_map('get_page_uri', $q->get_posts());

    return array_merge(
    	array_map('get_page_uri', $q->get_posts()),
    	array_map('get_page_uri', $q2->get_posts())
    );
}

add_filter('posts_where', 'tp_posts_where');
function tp_posts_where($where) {
	$where = str_replace("meta_key = 'product_page", "meta_key LIKE '%product_page%", $where);
	return $where;
}

add_filter('publish_page', 'tp_rewrites_flush');
function tp_rewrites_flush()
{
   global $wp_rewrite;
   $productpages = implode('|', tp_product_pages());
   if (!empty($productpages) && $productpages !== get_option('tp_product_pages'))
   {
      tp_log('INFO tp_rewrites_flush: ' . $productpages);
      $wp_rewrite->flush_rules();
      update_option('tp_product_pages', $productpages);
   }
}

/*
 * add custom rewrite rules for product content pages to wordpress
 * these rewrite rules are to allow for supplier/product pages to be suburls of pages
 */
add_filter('generate_rewrite_rules', 'tp_rewrite_rules');
function tp_rewrite_rules($wp_rewrite)
{
   $productpages = tp_product_pages();
   if (!empty($productpages))
   {
      $pagenames =  implode('|', $productpages);
      $wp_rewrite->rules = array('(' . $pagenames . ')/(.*)' => 'index.php?pagename=$matches[1]') + $wp_rewrite->rules;
      tp_log('INFO tp_rewrite_rules: ' . $pagenames);
   }
}


add_filter('acf/load_field/name=service_button', 'acf_load_service_button_choices');
function acf_load_service_button_choices($field)
{
	$field['choices'] = array();

	$serviceButtons = explode(',', get_option('tp_service_buttons'));

	foreach ($serviceButtons as $sb) {
		$field['choices'][$sb] = $sb;
	}
	return $field;
}

add_filter('acf/load_field/name=product_config_setting', 'acf_load_product_config_setting_choices');
function acf_load_product_config_setting_choices($field)
{
	$field['choices'] = array();
	$choices = array(
		'searchdateoffset' => 'Search Date Offset',
		'wpadultprice' => 'Wordpress Adult Price',
		'wpchildprice' => 'Wordpress Child Price',
		'wpinfantprice' => 'Wordpress Infant Price',
		'wpcurrency' => 'Wordpress Currency'
	);

	foreach ($choices as $value => $label) {
		$field['choices'][$value] = $label;
	}
	return $field;
}


/*
function update_option_tp_labels_json($oldval, $newval)
{
	error_log('tp_configs_json_updated');
	tp_refresh_servicebuttonconfigs();
}
function update_option_tp_configs_json($oldval, $newval)
{
	error_log('tp_configs_json_updated');
	tp_refresh_servicebuttonconfigs();
}
*/
// ==================================== ADMIN Pages ======================================================

function tp_settings_head()
{
    echo '<style> .radiobutton { text-align: left; margin-left:10px; margin-right: 30px; } .optiondisabled { color: gray; } </style>';
}

function tp_settings_menu()
{
  add_options_page('Tourplan', 'Tourplan Settings', 'administrator', __FILE__, 'tp_settings_page');
  add_action('admin_init', 'tp_register_settings');
}

function tp_register_settings()
{
	// Main Panel

	register_setting( 'tp-settings-group', 'tp_app_url','');
	register_setting( 'tp-settings-group', 'tp_default_image_url','');
	register_setting( 'tp-settings-group', 'tp_search_images_url','');
	register_setting( 'tp-settings-group', 'tp_supplier_images_url','');
	register_setting( 'tp-settings-group', 'tp_languages','');
	
	register_setting( 'tp-settings-group', 'tp_product_content_url','');
	register_setting( 'tp-settings-group', 'tp_cart_url','');

	foreach( tp_language_exts() as $ext ) {
		$ex = $ext == "" || $ext == null ? "" : "_" . $ext;
		register_setting( 'tp-settings-group', 'tp_itinerary_url' . $ex,'');
		register_setting( 'tp-settings-group', 'tp_checkout_url' . $ex,'');
		register_setting( 'tp-settings-group', 'tp_search_url' . $ex, '');;	

		register_setting( 'tp-settings-group', 'tp_booking_confirmed_url' . $ex,'');
		register_setting( 'tp-settings-group', 'tp_booking_requested_url' . $ex,'');
		register_setting( 'tp-settings-group', 'tp_booking_payment_failed_url' . $ex,'');
		register_setting( 'tp-settings-group', 'tp_booking_failed_url' . $ex,'');

		register_setting( 'tp-settings-group', 'tp_payment_url' . $ex,'');
		register_setting( 'tp-settings-group', 'tp_payment_success_url' . $ex,'');
		register_setting( 'tp-settings-group', 'tp_payment_failed_url' . $ex,'');
	}


	// Labels 
	
	register_setting( 'tp-labels-group', 'tp_labels_json','');

	// Configs
	
	register_setting( 'tp-configs-group', 'tp_configs_json','');

	// Translations
	
	register_setting( 'tp-translations-group', 'tp_translations_json','');

	// Fees

	// register_setting( 'tp-fees-group', 'tp_fees_json', '');
	register_setting( 'tp-fees-group', 'tp_delivery_price_selection');

	// Payment Panel

	register_setting( 'tp-payment-group', 'tp_payment_enabled','');
	register_setting( 'tp-payment-group', 'tp_checkout_singlepage_enabled','');

	register_setting( 'tp-payment-group', 'tp_payment_gateway',''); //none, eway, dps, pbb, cfb
	register_setting( 'tp-payment-group', 'tp_payment_alt','');
	register_setting( 'tp-payment-group', 'tp_payment_alt_start_days','');
	
	register_setting( 'tp-payment-group', 'tp_deposit_only','');
	register_setting( 'tp-payment-group', 'tp_deposit_percent','');
	register_setting( 'tp-payment-group', 'tp_deposit_surcharge','');

	register_setting( 'tp-payment-group', 'tp_eway_api_url','');
	register_setting( 'tp-payment-group', 'tp_eway_redirect_url','');
	register_setting( 'tp-payment-group', 'tp_eway_cancel_url','');
	register_setting( 'tp-payment-group', 'tp_eway_customer','');
	register_setting( 'tp-payment-group', 'tp_eway_username','');
	register_setting( 'tp-payment-group', 'tp_eway_password','');
	register_setting( 'tp-payment-group', 'tp_eway_currency','');
	register_setting( 'tp-payment-group', 'tp_eway_country','');
	register_setting( 'tp-payment-group', 'tp_eway_logo_url','');
	register_setting( 'tp-payment-group', 'tp_eway_theme','');

	// register_setting( 'tp-payment-group', 'tp_pbb_auth_url','');
	// register_setting( 'tp-payment-group', 'tp_pbb_complete_url','');
	register_setting( 'tp-payment-group', 'tp_pbb_process_url', '');
	register_setting( 'tp-payment-group', 'tp_pbb_merchant_visa','');
 	register_setting( 'tp-payment-group', 'tp_pbb_merchant_mastercard','');

    register_setting( 'tp-payment-group', 'tp_dps_px_url','');
	register_setting( 'tp-payment-group', 'tp_dps_px_userid','');
    register_setting( 'tp-payment-group', 'tp_dps_px_key','');

    register_setting( 'tp-payment-group', 'tp_dps_ws_url','');
    register_setting( 'tp-payment-group', 'tp_dps_ws_username','');
    register_setting( 'tp-payment-group', 'tp_dps_ws_password','');

	register_setting( 'tp-payment-group', 'tp_cfb_merchant','');
	register_setting( 'tp-payment-group', 'tp_cfb_terminal','');
    register_setting( 'tp-payment-group', 'tp_cfb_merid','');
    register_setting( 'tp-payment-group', 'tp_cfb_merchantname','');
    register_setting( 'tp-payment-group', 'tp_cfb_url','');

  	register_setting( 'tp-payment-group', 'tp_axes_site_code','');
    register_setting( 'tp-payment-group', 'tp_axes_url','');
	
	register_setting( 'tp-payment-group', 'tp_aeon_payment_url','');
	register_setting( 'tp-payment-group', 'tp_aeon_payment_id','');
 	register_setting( 'tp-payment-group', 'tp_aeon_merchant_id','');
	register_setting( 'tp-payment-group', 'tp_aeon_currency_code','');
	register_setting( 'tp-payment-group', 'tp_aeon_hash_algorithm','');
	register_setting( 'tp-payment-group', 'tp_aeon_hash_key','');

	register_setting( 'tp-payment-group', 'tp_paydollar_payment_url','');
 	register_setting( 'tp-payment-group', 'tp_paydollar_merchant_id','');
	register_setting( 'tp-payment-group', 'tp_paydollar_secure_hash_secret','');
	// register_setting( 'tp-payment-group', 'tp_paydollar_api_url','');
 // 	register_setting( 'tp-payment-group', 'tp_paydollar_merchant_login','');
	// register_setting( 'tp-payment-group', 'tp_paydollar_merchant_password','');

    register_setting( 'tp-payment-group', 'tp_paynamics_url', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_merchant_id', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_merchant_name', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_site_ip', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_company_name', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_merchant_key', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_mtac_url', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_descriptor_note', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_country_list', '');
    register_setting( 'tp-payment-group', 'tp_paynamics_always_rq_pmethods', '');

    register_setting( 'tp-payment-group', 'tp_ipay88_url', '');
    register_setting( 'tp-payment-group', 'tp_ipay88_merchant_code', '');
    register_setting( 'tp-payment-group', 'tp_ipay88_merchant_key', '');
    register_setting( 'tp-payment-group', 'tp_ipay88_backend_url', '');

    register_setting( 'tp-payment-group', 'tp_stripe_merchant_secret_key', '');
    register_setting( 'tp-payment-group', 'tp_stripe_merchant_public_key', '');
    register_setting( 'tp-payment-group', 'tp_stripe_merchant_name', '');
    register_setting( 'tp-payment-group', 'tp_stripe_merchant_image', '');
	
	register_setting( 'tp-payment-group', 'tp_credomatic_url','');
	register_setting( 'tp-payment-group', 'tp_credomatic_key','');
	register_setting( 'tp-payment-group', 'tp_credomatic_key_id','');
	
	
    // Other Panel

    register_setting( 'tp-other-group', 'tp_booking_supplier_confirmation_url','');
    register_setting( 'tp-other-group', 'tp_need_it_now_url','');
    register_setting( 'tp-other-group', 'tp_news_url','');
    register_setting( 'tp-other-group', 'tp_newsletter_url','');
    register_setting( 'tp-other-group', 'tp_currency','');
    register_setting( 'tp-other-group', 'tp_timeout_idle','');
    register_setting( 'tp-other-group', 'tp_request_timeout', '');
    register_setting( 'tp-other-group', 'tp_getCheckoutSynchMs', '');

    register_setting( 'tp-other-group', 'tp_facebook_url','');
    register_setting( 'tp-other-group', 'tp_twitter_url','');
    register_setting( 'tp-other-group', 'tp_tollfree_phone','');
    register_setting( 'tp-other-group', 'tp_country_code','');
	register_setting( 'tp-other-group', 'tp_promotion_title','');
	register_setting( 'tp-other-group', 'tp_service_buttons', '');
}


function tp_other_settings_panel() { ?>
   <table class="form-table">
		<tr valign="top"><th colspan="2"><h3>Other Settings</h3></th></tr>

		<tr valign="top">
			<th scope="row">News Page URL</th>
			<td><input type="text" class="regular-text"  name="tp_news_url"
				value="<?php echo get_option('tp_news_url') ; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row">Newsletter Page URL</th>
			<td><input type="text" class="regular-text"  name="tp_newsletter_url"
				value="<?php echo get_option('tp_newsletter_url') ; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row">Need It Now URL</th>
			<td><input type="text" class="regular-text"  name="tp_need_it_now_url"
				value="<?php echo get_option('tp_need_it_now_url') ; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row">Currency</th>
			<td><input type="text" class="regular-text"  name="tp_currency"
				value="<?php echo get_option('tp_currency') ; ?>" /></td>
		</tr>
        <tr valign="top">
               <th scope="row">Session Idle Timeout</th>
               <td><input type="text" class="regular-text"  name="tp_timeout_idle" value="<?php echo get_option('tp_timeout_idle') ; ?>" /></td>
      	</tr>

        <tr valign="top">
               <th scope="row">Engine Request Timeout</th>
               <td><input type="text" class="regular-text"  name="tp_request_timeout" value="<?php echo get_option('tp_request_timeout') ; ?>" /></td>
       	</tr>

        <tr valign="top">
               <th scope="row">Checkout Synch Timeout</th>
               <td><input type="text" class="regular-text"  name="tp_getCheckoutSynchMs" value="<?php echo get_option('tp_getCheckoutSynchMs') ; ?>" /></td>
       	</tr>

    <tr valign="top"><th colspan="2"><h3>General Website Settings</h3></th>
        <tr valign="top">
			<th scope="row">Facebook URL<br /><span style="font-style:italic;font-size:90%;">Include 'http://'</span></th>
			<td><input type="text" class="regular-text"  name="tp_facebook_url" value="<?php echo get_option('tp_facebook_url') ; ?>" /></td>
		</tr>
        <tr valign="top">
			<th scope="row">Twitter URL<br /><span style="font-style:italic;font-size:90%;">Include 'http://'</span></th>
			<td><input type="text" class="regular-text"  name="tp_twitter_url" value="<?php echo get_option('tp_twitter_url') ; ?>" /></td>
		</tr>

        <tr valign="top">
			<th scope="row">Tollfree Phone Number</th>
			<td><input type="text" class="regular-text"  name="tp_tollfree_phone" value="<?php echo get_option('tp_tollfree_phone') ; ?>" /></td>
		</tr>

        <tr valign="top">
			<th scope="row">Country Code</th>
			<td><input type="text" class="regular-text"  name="tp_country_code"
				value="<?php echo get_option('tp_country_code') ; ?>" /></td>
		</tr>

       <tr valign="top">
			<th scope="row">Promotion Title</th>
			<td><input type="text" class="regular-text"  name="tp_promotion_title"
				value="<?php echo get_option('tp_promotion_title') ; ?>" /></td>
		</tr>
       <tr valign="top">
			<th scope="row">Product Pages</th>
			<td><?php echo get_option('tp_product_pages') ; ?></td>
		</tr>

		<tr valign="top">
			<th scope="row">Service Buttons</th>
			<td>
				<input type="text" class="regular-text" name="tp_service_buttons" value="<?php echo get_option('tp_service_buttons'); ?>"/>
			</td>
		</tr>

	</table>
<?php
}

function tp_payment_settings_panel() { ?>

   	<script>
	      function tpSetPaymentGateway(radio)
	      {
	        var settings = ['eway', 'dps', 'pbb', 'cfb', 'axes', 'aeon', 'paydollar', 'paynamics', 'ipay88', 'stripe', 'credomatic'];
	        for (var i in settings)
	        {
	          var setting = settings[i];
	          document.getElementById(setting + 'settings').setAttribute('style', 'display:' + (setting == radio.value ? 'block' : 'none'));
	        }
	        return true;
	      }
	   </script>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Payment Gateway</th>
			<td>
                <span class="radiobutton">
	                <input type="radio" name="tp_payment_gateway" value="eway" <?php echo get_option('tp_payment_gateway') === 'eway' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                    Eway
                </span>
				<span class="radiobutton">
	                <input type="radio" name="tp_payment_gateway" value="dps" <?php echo get_option('tp_payment_gateway') === 'dps' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                    DPS
                </span>
                <span class="radiobutton">
	                <input type="radio" name="tp_payment_gateway" value="pbb" <?php echo get_option('tp_payment_gateway') === 'pbb' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                    PBB
                </span>
                 <span class="radiobutton">
	                <input type="radio" name="tp_payment_gateway" value="cfb" <?php echo get_option('tp_payment_gateway') === 'cfb' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />

                    CFB
                </span>
     			<span class="radiobutton">
	                <input type="radio" name="tp_payment_gateway" value="axes" <?php echo get_option('tp_payment_gateway') === 'axes' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                    Axes
                </span>
                <span class="radiobutton">
	                <input type="radio" name="tp_payment_gateway" value="aeon" <?php echo get_option('tp_payment_gateway') === 'aeon' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                    AEON
                </span>
                <span class="radiobutton">
                	<input type="radio" name="tp_payment_gateway" value="paydollar" <?php echo get_option('tp_payment_gateway') == 'paydollar' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                	PayDollar
                </span>
                <span class="radiobutton">
                	<input type="radio" name="tp_payment_gateway" value="paynamics" <?php echo get_option('tp_payment_gateway') == 'paynamics' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                	Paynamics
                </span>
				<span class="radiobutton">
                	<input type="radio" name="tp_payment_gateway" value="ipay88" <?php echo get_option('tp_payment_gateway') == 'ipay88' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                	iPay88
                </span>
                <span class="radiobutton">
                	<input type="radio" name="tp_payment_gateway" value="stripe" <?php echo get_option('tp_payment_gateway') == 'stripe' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                	Stripe
                </span>
                <span class="radiobutton">
                	<input type="radio" name="tp_payment_gateway" value="credomatic" <?php echo get_option('tp_payment_gateway') == 'credomatic' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                	Credomatic
                </span>
				<span class="radiobutton">
	                <input type="radio" name="tp_payment_gateway" value="none" <?php echo get_option('tp_payment_gateway') == null || get_option('tp_payment_gateway') === '' || get_option('tp_payment_gateway') === 'none' ? 'checked="checked"' : ''; ?> onclick="tpSetPaymentGateway(this);" />
                    none
                </span>
            </td>
	   </tr>

		<tr valign="top">
         <th scope="row">Payment Alt</th>
         <td><input type="checkbox"
                    name="tp_payment_alt"
                    <?php echo get_option('tp_payment_alt') === 'true' ? 'checked="checked"' : ''; ?>
                    value="<?php echo get_option('tp_payment_alt') == 'true' ? 'true' : 'false'; ?>"
                    onclick="javascript:this.value=(this.value=='true'?'false':'true');" /></td>
        </tr>
	    <tr valign="top">
 	               <th scope="row">Payment Alt Start Days</th>
                       <td><input type="text" class="regular-text"  name="tp_payment_alt_start_days" value="<?php echo get_option('tp_payment_alt_start_days') ; ?>" /></td>
        </tr>
    </table>

	<table id="ewaysettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'eway' ? '' : 'style="display:none"'; ?>>
		<tr valign="top" ><th colspan="2"><h3>Eway Settings</h3></th></tr>
		<tr valign="top">
            <th scope="row" class="paymentoption">Deposit Only</th>
            <td class="paymentoption">
                <input type="checkbox" name="tp_deposit_only" <?php echo get_option('tp_deposit_only') === 'true' ? 'checked="checked"' : ''; ?>
                    value="<?php echo get_option('tp_deposit_only') == 'true' ? 'true' : 'false'; ?>"
                    onclick="javascript:this.value=(this.value=='true'?'false':'true');" />
                <div><span style="display:inline-block;width:200px;">Deposit %:</span><input type="text" class="regular-text" style="width:150px" name="tp_deposit_percent" value="<?php echo get_option('tp_deposit_percent') ; ?>"/></div>
                <div><span style="display:inline-block;width:200px;">Deposit Surcharge %:</span><input type="text" class="regular-text" style="width:150px" name="tp_deposit_surcharge" value="<?php echo get_option('tp_deposit_surcharge') ; ?>"/></div>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Username (API key)</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_eway_username" value="<?php echo get_option('tp_eway_username') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Password</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_eway_password" value="<?php echo get_option('tp_eway_password') ; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">API URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_eway_api_url" value="<?php echo get_option('tp_eway_api_url') ; ?>" /></td>
        </tr>
		<tr valign="top">
            <th scope="row" class="paymentoption">Currency Code</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_eway_currency" value="<?php echo get_option('tp_eway_currency') ; ?>" /></td>
        </tr>
		<tr valign="top">
            <th scope="row" class="paymentoption">Country Code</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_eway_country" value="<?php echo get_option('tp_eway_country') ; ?>" /></td>
        </tr>
		<tr valign="top">
            <th scope="row" class="paymentoption">Logo url</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_eway_logo_url" value="<?php echo get_option('tp_eway_logo_url') ; ?>" /></td>
        </tr>
		<tr valign="top">
            <th scope="row" class="paymentoption">Theme</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_eway_theme" value="<?php echo get_option('tp_eway_theme') ; ?>" /></td>
        </tr>
	</table>

	<table id="dpssettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'dps' ? '' : 'style="display:none"'; ?>>
		<tr valign="top" ><th colspan="2"><h3>DPS Settings</h3></th></tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">UserId</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_dps_px_userid" value="<?php echo get_option('tp_dps_px_userid') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Key</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_dps_px_key" value="<?php echo get_option('tp_dps_px_key') ; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Payment URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_dps_px_url" value="<?php echo get_option('tp_dps_px_url') ; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Username</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_dps_ws_username" value="<?php echo get_option('tp_dps_ws_username') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Password</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_dps_ws_password" value="<?php echo get_option('tp_dps_ws_password') ; ?>" /></td>
        </tr>

        <tr valign="top">
            <th scope="row" class="paymentoption">Webservice URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_dps_ws_url" value="<?php echo get_option('tp_dps_ws_url') ; ?>" /></td>
        </tr>
	</table>

	<table id="pbbsettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'pbb' ? '' : 'style="display:none"'; ?>>
		<tr valign="top" ><th colspan="2"><h3>PBB Settings</h3></th></tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Merchant Id Visa</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_pbb_merchant_visa" value="<?php echo get_option('tp_pbb_merchant_visa') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Merchant Id Mastercard</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_pbb_merchant_mastercard" value="<?php echo get_option('tp_pbb_merchant_mastercard') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Process URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_pbb_process_url" value="<?php echo get_option('tp_pbb_process_url') ; ?>" /></td>
        </tr>
	</table>

    <table id="cfbsettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'cfb' ? '' : 'style="display:none"'; ?>>
		<tr valign="top" ><th colspan="2"><h3>CFB Settings</h3></th></tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Merchant Id</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_cfb_merchant" value="<?php echo get_option('tp_cfb_merchant') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Terminal Id</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_cfb_terminal" value="<?php echo get_option('tp_cfb_terminal') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">merId</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_cfb_merid" value="<?php echo get_option('tp_cfb_merid') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Merchant Name</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_cfb_merchantname" value="<?php echo get_option('tp_cfb_merchantname') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_cfb_url" value="<?php echo get_option('tp_cfb_url') ; ?>" /></td>
        </tr>
	</table>

    <table id="axessettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'axes' ? '' : 'style="display:none"'; ?>>
		   <tr valign="top" ><th colspan="2"><h3>Axes Settings</h3></th></tr>

        <tr valign="top">
            <th scope="row" class="paymentoption">Site Code</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_axes_site_code" value="<?php echo get_option('tp_axes_site_code') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_axes_url" value="<?php echo get_option('tp_axes_url') ; ?>" /></td>
        </tr>
	</table>
    
    <table id="aeonsettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'aeon' ? '' : 'style="display:none"'; ?>>
		<tr valign="top" ><th colspan="2"><h3>AEON Settings</h3></th></tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Payment Id</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_aeon_payment_id" value="<?php echo get_option('tp_aeon_payment_id') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Merchant Id</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_aeon_merchant_id" value="<?php echo get_option('tp_aeon_merchant_id') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Payment URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_aeon_payment_url" value="<?php echo get_option('tp_aeon_payment_url') ; ?>" /></td>
        </tr>
         <tr valign="top">
            <th scope="row" class="paymentoption">Currency Code</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_aeon_currency_code" value="<?php echo get_option('tp_aeon_currency_code') ; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Hash Algorithm</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_aeon_hash_algorithm" value="<?php echo get_option('tp_aeon_hash_algorithm') ; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Hash Key</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_aeon_hash_key" value="<?php echo get_option('tp_aeon_hash_key') ; ?>" /></td>
        </tr>
	</table>

	<table id="paydollarsettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'paydollar' ? '' : 'style="display:none"'; ?>>
		<tr valign="top" ><th colspan="2"><h3>PayDollar Settings</h3></th></tr>

        <tr valign="top">
            <th scope="row" class="paymentoption">Payment URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_paydollar_payment_url" value="<?php echo get_option('tp_paydollar_payment_url') ; ?>"/></td>
        </tr>
        <!-- <tr valign="top">
            <th scope="row" class="paymentoption">Merchant API URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_paydollar_api_url" value="<?php echo get_option('tp_paydollar_api_url') ; ?>"/></td>
        </tr> -->
        <tr valign="top">
            <th scope="row" class="paymentoption">Merchant ID</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_paydollar_merchant_id" value="<?php echo get_option('tp_paydollar_merchant_id') ; ?>" /></td>
        </tr>
        <!-- <tr valign="top">
            <th scope="row" class="paymentoption">Merchant API Login</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_paydollar_merchant_login" value="<?php echo get_option('tp_paydollar_merchant_login') ; ?>" /></td>
        </tr> -->
        <!-- <tr valign="top">
            <th scope="row" class="paymentoption">Merchant API Password</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_paydollar_merchant_password" value="<?php echo get_option('tp_paydollar_merchant_password') ; ?>" /></td>
        </tr> -->
         <tr valign="top">
            <th scope="row" class="paymentoption">Secure Hash Secret</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_paydollar_secure_hash_secret" value="<?php echo get_option('tp_paydollar_secure_hash_secret') ; ?>" /></td>
        </tr>
	</table>

	<table id="paynamicssettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'paynamics' ? '' : 'style="display:none"'; ?>>
		<tr valign="top"><th colspan="2"><h3>Paynamics Settings</h3></th></tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Paynamics URL</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_url" value="<?php echo get_option('tp_paynamics_url'); ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant ID</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_merchant_id" value="<?php echo get_option('tp_paynamics_merchant_id'); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Name</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_merchant_name" value="<?php echo get_option('tp_paynamics_merchant_name'); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="paymentoption">Site IP</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_site_ip" value="<?php echo get_option('tp_paynamics_site_ip'); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="paymentoption">Company Name</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_company_name" value="<?php echo get_option('tp_paynamics_company_name'); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Key</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_merchant_key" value="<?php echo get_option('tp_paynamics_merchant_key'); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="paymentoption">Terms and Conditions URL</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_mtac_url" value="<?php echo get_option('tp_paynamics_mtac_url'); ?>"/></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="paymentoption">Descriptor Note</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_descriptor_note" value="<?php echo get_option('tp_paynamics_descriptor_note'); ?>"/></td>
		</tr>
		<tr valign="top">	
			<th scope="row" class="paymentoption">Country List</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_country_list" value="<?php echo get_option('tp_paynamics_country_list'); ?>"/></td>
		</tr>
		<tr valign="top">	
			<th scope="row" class="paymentoption">Always On Request Payment Methods</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_paynamics_always_rq_pmethods" value="<?php echo get_option('tp_paynamics_always_rq_pmethods'); ?>"/></td>
		</tr>
	</table>

	<table id="ipay88settings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'ipay88' ? '' : 'style="display:none"'; ?>>
		<tr valign="top"><th colspan="2"><h3>iPay88 Settings</h3></th></tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">URL</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_ipay88_url" value="<?php echo get_option('tp_ipay88_url'); ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Code</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_ipay88_merchant_code" value="<?php echo get_option('tp_ipay88_merchant_code'); ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Key</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_ipay88_merchant_key" value="<?php echo get_option('tp_ipay88_merchant_key'); ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Backend URL</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_ipay88_backend_url" value="<?php echo get_option('tp_ipay88_backend_url'); ?>" /></td>
		</tr>
	</table>

	<table id="stripesettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'stripe' ? '' : 'style="display:none"'; ?>>
		<tr valign="top"><th colspan="2"><h3>Stripe Settings</h3></th></tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Name</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_stripe_merchant_name" value="<?php echo get_option('tp_stripe_merchant_name'); ?>"></td>
		</tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Image</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_stripe_merchant_image" value="<?php echo get_option('tp_stripe_merchant_image'); ?>"></td>
		</tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Secret Key</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_stripe_merchant_secret_key" value="<?php echo get_option('tp_stripe_merchant_secret_key'); ?>"></td>
		</tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Merchant Public Key</th>
			<td class="paymentoption"><input type="text" class="regular-text" name="tp_stripe_merchant_public_key" value="<?php echo get_option('tp_stripe_merchant_public_key'); ?>"></td>
		</tr>

	</table>
    
    <table id="credomaticsettings" class="form-table" <?php echo get_option('tp_payment_gateway') === 'credomatic' ? 'style="display:block"' : 'style="display:none"'; ?>>
		<tr valign="top" ><th colspan="2"><h3>Credomatic Settings</h3></th></tr>
 		<tr valign="top">
            <th scope="row" class="paymentoption">URL</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_credomatic_url" value="<?php echo get_option('tp_credomatic_url') ; ?>"/></td>
        </tr>
        <tr valign="top">
            <th scope="row" class="paymentoption">Security Key ID</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_credomatic_key_id" value="<?php echo get_option('tp_credomatic_key_id') ; ?>"/></td>
        </tr>
            <th scope="row" class="paymentoption">Security Key</th>
            <td class="paymentoption"><input type="text" class="regular-text" name="tp_credomatic_key" value="<?php echo get_option('tp_credomatic_key') ; ?>"/></td>
		<tr valign="top">
        </tr>
	</table>

<?php
}

function tp_main_settings_panel() { ?>
	
	<table class="form-table">

	   <tr valign="top">
		<th colspan="2"><h3>General Application Engine Settings</h3></th>
	   </tr>

	   <tr valign="top">
        <th scope="row">Tourplan Appengine URL</th>
			<td><input type="text" class="regular-text" name="tp_app_url"
				value="<?php echo get_option('tp_app_url') ; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row">Default Image URL</th>
			<td><input type="text" class="regular-text"  name="tp_default_image_url"
				value="<?php echo get_option('tp_default_image_url') ; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row">Search Images URL</th>
			<td><input type="text" class="regular-text" name="tp_search_images_url"
				value="<?php echo get_option('tp_search_images_url') ; ?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row">Supplier Images URL</th>
			<td><input type="text" class="regular-text"  name="tp_supplier_images_url"
				value="<?php echo get_option('tp_supplier_images_url') ; ?>" /></td>
		</tr>
		
		<tr valign="top">
			<th scope="row">Language (codes)</th>
			<td><input type="text" class="regular-text"  name="tp_languages"
				value="<?php echo get_option('tp_languages') ; ?>" /></td>
		</tr>

	   <tr valign="top">
		<th colspan="2"><h3>Tourplan Booking process</h3></th>
	   </tr>

	   <tr valign="top">
			<th scope="row">Shopping Cart Url</th>
			<td><input type="text" class="regular-text"  name="tp_cart_url"
				value="<?php echo get_option('tp_cart_url'); ?>" /></td>
		</tr>

	   <?php 
		foreach( tp_language_exts() as $ext ) {
			$ex = $ext == "" || $ext == null ? "" : "_" . $ext; ?>

	   <tr valign="top">
			<th scope="row">Itinerary Page <?php echo strtoupper($ext); ?></th>
			<td><input type="text" class="regular-text"  name="<?php echo 'tp_itinerary_url' . $ex; ?>"
				value="<?php echo get_option('tp_itinerary_url' . $ex) ; ?>" /></td>
		</tr>

	   <tr valign="top">
			<th scope="row">Checkout Page <?php echo strtoupper($ext); ?></th>
			<td><input type="text" class="regular-text"  name="<?php echo 'tp_checkout_url' . $ex; ?>"
				value="<?php echo get_option('tp_checkout_url' . $ex) ; ?>" /></td>
		</tr>

		<tr valign="top">
	    	<th scope="row">Search Page <?php echo strtoupper($ext); ?></th>
	    	<td><input type="text" class="regular-text"  name="<?php echo 'tp_search_url' . $ex; ?>"
	    		value="<?php echo get_option('tp_search_url' . $ex); ?>" /></td>
    	</tr>

		<tr valign="top">
			<th scope="row" class="paymentoption">Payment Page <?php echo strtoupper($ext); ?></th>
			<td class="paymentoption"><input type="text" class="regular-text"  name="<?php echo 'tp_payment_url' . $ex; ?>"
				value="<?php echo get_option('tp_payment_url' . $ex) ; ?>" /></td>
		</tr>

	   <tr valign="top">
			<th scope="row" class="paymentoption">Payment Success Page <?php echo strtoupper($ext); ?></th>
			<td class="paymentoption"><input type="text" class="regular-text"  name="<?php echo 'tp_payment_success_url' . $ex; ?>"
				value="<?php echo get_option('tp_payment_success_url' . $ex) ; ?>" /></td>
		</tr>

	   <tr valign="top">
			<th scope="row" class="paymentoption">Payment Failed Page <?php echo strtoupper($ext); ?></th>
			<td class="paymentoption"><input type="text" class="regular-text"  name="<?php echo 'tp_payment_failed_url' . $ex; ?>"
				value="<?php echo get_option('tp_payment_failed_url' . $ex) ; ?>" /></td>
	   </tr>

	   <tr valign="top">
			<th scope="row">Booking Confirmed Page <?php echo strtoupper($ext); ?></th>
			<td ><input type="text" class="regular-text"  name="<?php echo 'tp_booking_confirmed_url' . $ex; ?>"
				value="<?php echo get_option('tp_booking_confirmed_url' . $ex) ; ?>" /></td>
	   </tr>

	   <tr valign="top">
			<th scope="row">Booking Requested Page <?php echo strtoupper($ext); ?></th>
			<td ><input type="text" class="regular-text"  name="<?php echo 'tp_booking_requested_url' . $ex; ?>"
				value="<?php echo get_option('tp_booking_requested_url' . $ex) ; ?>" /></td>
	   </tr>

	   <tr valign="top">
			<th scope="row">Booking Failed Page <?php echo strtoupper($ext); ?></th>
			<td ><input type="text" class="regular-text"  name="<?php echo 'tp_booking_failed_url' . $ex; ?>"
				value="<?php echo get_option('tp_booking_failed_url' . $ex) ; ?>" /></td>
	   </tr>

	   <?php } ?>
	   </table>
<?php
}

function tp_fees_panel() {

	echo '<h3>Delivery Fees</h3>';
	tp_delivery_fees_admin();
	echo '<h3>Booking Fees</h3>';
	tp_booking_fees_admin();
}

function tp_settings_page() { ?>

	<div class="wrap">
	<h1>Tourplan Settings</h1>
	<div id="icon-themes" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper">
	<?php 
		$tabs = array( 'main' => 'Main', 'labels' => 'Labels', 'translations' => 'Translations', 'configs' => 'Configs', 'payment' => 'Payment', 'fees' => 'Fees', 'other' => 'Other');
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $_GET['tab'] || $tab == 'main' && $_GET['tab'] == '') ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=tp-config/tp-admin.php&tab=$tab'>$name</a>";
	    }
    ?>
    </h2>
    
    <form method="post" action="options.php">

	<?php 
	    
	    if ($_GET['tab']==='configs') {
	    	settings_fields('tp-configs-group');
	    	tp_configs_panel();
	    } else if ($_GET['tab']==='labels') {
	    	settings_fields('tp-labels-group');
	    	tp_labels_panel();
	    } else if ($_GET['tab']==='translations') {
	    	settings_fields('tp-translations-group');
	    	tp_translations_panel();
	    } else if ($_GET['tab']==='fees') {
	    	settings_fields('tp-fees-group');
	    	tp_fees_panel();
	    } else if ($_GET['tab']==='other') {
	    	settings_fields('tp-other-group');
	    	tp_other_settings_panel();
	    } else if ($_GET['tab']==='payment') {
	    	settings_fields('tp-payment-group');
	    	tp_payment_settings_panel();
	    } else {
	    	settings_fields('tp-settings-group');
	        tp_main_settings_panel();
	    }
	?>
	<p class="submit"><input id="tpsavebutton" type="submit" class="button-primary" value="Save Changes" /></p>
	</form>
	</div>
<?php
}

function tpConfigOption($srb, $labels, $name, $value, $trans)
{
	echo '<tr>';
	echo '<td><select onchange="tpConfigOptionChange(this)">';
	foreach ($labels as $label)
	{
		echo '<option' . ($label === $name ? ' selected=\"selected\"' : '') . '>' . $label . '</option>';
	}
	echo '</select></td>';
	$syslang = tp_system_language();
	echo '<td><input class="regular-text" type="text" data-lang="' . $syslang . '" data-srb="'.$srb.'" name="'.$name.'" value="'.$value.'" /></td>';
	
    $langs = tp_languages();
	foreach ($langs as $lang)
	{
	    if ($lang !== $syslang)
		{
			if ( array_key_exists($lang, $trans) )
				$translist = $trans[$lang];
			else
				$translist = $trans[$syslang];
			$default = true;
			foreach ($translist as $srbcfg)
			{
				if ($srbcfg['serviceButton'] === $srb)
				{
					foreach ($srbcfg['config'] as $cfg)
					{
						if ($cfg['name'] === $name)
						{
							echo '<td><input class="regular-text" type="text" data-srb="'.$srb.'" data-lang="'.$lang.'" name="'.$name.'" value="' . $cfg['value']. '" /></td>';
							$default = false;
							break;
						}
					}
					break;
				}
			}
			if ( $default ) {
				echo '<td><input class="regular-text" type="text" data-srb="'.$srb.'" data-lang="'.$lang.'" name="'.$name.'" value="' . $cfg['value']. '" /></td>';
			}
		}
	}

	echo '<td><span onclick="jQuery(this).parents(\'tr\').first().remove();return false;">x</span></td></tr>';
}


function tp_labels_panel()
{
	tp_refresh_servicebuttonconfigs();
	
	
	$labels = array(
		'ExtOptionDescrLabel'
		,'ExtOptionIdLabel'
		,'ExtRatePlanDescrLabel'
		,'MealLabel'
		,'OccupancyLabel'
		,'additionalDetailsHeader'
		,'addressLabel'
		,'address1Label'
		,'address2Label'
		,'address3Label'
		,'address4Label'
		,'address5Label'
		,'address6Label'
		,'address7Label'
		,'adultCountLabel'
		,'adultsLabel'
		,'bookButtonLabel'
		,'cartTotalPricePrefix'
		,'cartItemTotalLabel'
		,'cartPricePrefix'
		,'checkoutButton'
		,'childCountLabel'
		,'childrenLabel'
		,'classesLabel'
		,'clearCartLabel'
		,'countriesLabel'
		,'countryLabel'
		,'customerHeader'
		,'dateInLabel'
		,'description' 
		,'deliveryAddressLabel'
		,'deliveryAddressNote'
		,'deliveryLabel'
		,'deliveryDescription'
		,'departureDateDescription'
		,'departureDateLabel'
		,'destinationsLabel'
		,'dobLabel'
		,'dropoffLabel'
		,'externalDeadlineLabel'
		,'externalDetailSections'
		,'externalRateplanLabel'
		,'filter1Label'
		,'filter2Label'
		,'firstNameLabel'
		,'firstNameLangLabel'
		,'fromDateLabel'
		,'guestRatingLabel'
		,'hoteldescription'
		,'itineraryNoticeLabel'
		,'lastNameLabel'
		,'lastNameLangLabel'
		,'loadingAvailabilityMessage'
		,'loadingResultsMessage'
		,'localitiesLabel'
		,'middleNameLabel'
		,'nationalityLabel'
		,'noAvailParamsLabel'
		,'notFoundLabel'
		,'passportLabel'
		,'paymentFeeLabel'
		,'phoneLabel'
		,'pickupLabel'
		,'postCodeLabel'
		,'preference1Label' 
		,'productDetailAmenitiesLabel'
		,'productDetailNotesLabel'
		,'productMoreDetailHeader'
		,'productNameLabel'
		,'productPricePrefix'
		,'qtyLabel'
		,'refineButtonLabel'
		,'refineSearchHeader'
		,'refreshAvailabilityLabel'
		,'remarksHeading'
		,'remarksNote'
		,'removeButton'
		,'scuLabel'
		,'searchAvailabilityLabel'
		,'searchButtonLabel'
		,'searchParamsTitle'
		,'searchPricePrefix'
		,'searchPriceSuffix'
		,'searchResultButtonLabel'
		,'titleLabel'
		,'toDateLabel'
		,'updateButton'
		,'updateHeading'
		,'youthCountLabel'
	);

	$configsjson = array();
	$langs = tp_languages();
	$syslang = tp_system_language();
	$trans = array();
	$tplabelsjson = get_option('tp_labels_json');
	if (empty($tplabelsjson)) 
	{
		if (!empty($langs))
		{
			foreach ($langs as $lang)
			{
				$configsstr = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $lang . '-configs.js');
				if (!empty($configsstr)) {
					$trans[$lang] = json_decode($configsstr, true);
				}
			}
		}
	}
	else 
	{
		$configsstr = urldecode($tplabelsjson);
		$trans = json_decode($configsstr, true);
	}
	
	$configs = $trans[$syslang];
	$srbOnly = array_map(function($srbcfg){ return $srbcfg['serviceButton']; }, $configs);
	$exploded = explode(',', get_option('tp_service_buttons'));
	$missingSrbs = array_diff($exploded, $srbOnly);
	foreach ($configs as $srbcfg)
	{
		$srb = $srbcfg['serviceButton'];
		echo '<table id="tplabelsgroup"><tr>';
		echo '<th>' . (empty($srb) ? 'Default' : $srb) . '</th>';
		if (empty($langs)) 
		{
			echo '<th></th>';
		}
		else
		{
			foreach ($langs as $lang)
			{
				echo '<th>' . $lang . '</th>';
			}
		}
		echo '<th><span class="tpaddlabel">+</span></th></tr>';
		
		$cfgs = $srbcfg['config'];
		foreach ($labels as $label)
		{
			foreach ($cfgs as $cfg)
			{
				if ($cfg['name']===$label)
				{
					tpConfigOption($srb, $labels, $label, $cfg['value'], $trans);
					break;
				}
			}
		}
		echo '</table>';
	}
	foreach ($missingSrbs as $srb) {
		echo '<table id="tplabelsgroup"><tr>';
		echo '<th>' . $srb . '</th>';
		if (empty($langs))
		{
			echo '<th></th>';
		}
		else
		{
			foreach ($langs as $lang)
			{
				echo '<th>' . $lang . '</th>';
			}
		}
		echo '<th><span class="tpaddconfig">+</span></th></tr>';
		tpConfigOption($srb, $labels, $labels[0], "", $trans);
		echo '</table>';
	}
	?>
	<input id="tp_labels_json" type="hidden" name="tp_labels_json" value="<?php echo urlencode(json_encode($trans)); ?>">
	<script type="text/javascript">
	function tpConfigOptionChange(sel)
	{
		var newval = jQuery(sel).val();
		jQuery(sel).parents('tr').first().find('input').attr('name',newval);
		return true;
	}
	jQuery(function() {
		jQuery('#tplabelsgroup .tpaddlabel').click(function(e){
			var f = jQuery(e.target).parents('tr').first().siblings().first();
			var n = jQuery('<tr>'  + f.html() + '</tr>');
			f.before(n);
		});
		jQuery('#tpsavebutton').click(function(){
		    var xs = jQuery('#tplabelsgroup input').map(function(i,e){ 
			    var x = jQuery(e);
			    return {lang: x.data('lang'), srb: x.data('srb'), name: x.attr('name'), value: x.val()};
			});
			var xx = {};
			xs.map(function(i,x){
				var lang = x.lang;
				if (xx[lang] == undefined) {
					xx[lang] = {};
				}
				var srb = x.srb || 'Default';
				if (xx[lang][srb] == undefined) {
					xx[lang][srb] = [];
				}
				xx[lang][srb].push({name:x.name, value:x.value});
		    });
	        var json = {};
			for (lang in xx) 
			{ 
				json[lang] = []; 
				for (srb in xx[lang]) 
				{ 
					json[lang].push({'serviceButton': (srb == 'Default' ? '': srb), 'config': xx[lang][srb]});
				}
			}
			jQuery('#tp_labels_json').val(encodeURIComponent(JSON.stringify(json)));
			return true;
		});
	});
	</script>
<?php
}


function tp_configs_panel()
{
	tp_refresh_servicebuttonconfigs();
	
	$opts = array(
		'addressSections'
		,'amenitySections'
		,'availabilityTimeout'
		,'branches'
		,'cardTypes'
		,'cartItemsReadOnly'
		,'cartSections'
		,'collapsibleAmenities'
		,'collapsibleSearch'
		,'countries'
		,'currency'
		,'defaultDeliveryMethod'
		,'defaultQty'
		,'defaultRoomType'
		,'departureDateOffset'
		,'departureDateMaxOffset'
		,'detailAmenities'
		,'detailNotes'
		,'filter1'
		,'filter2'
		,'galleryLoadedCallback'
		,'hotelamenities'
		,'infoNotes'
		,'jsonpCallback'
		,'loadMaskTargetId'
		,'mapNote'
		,'mapNoteProduct'
		,'multiDateAvailability'
		,'notes'
		,'notesSections'
		,'paxNameFormat'
		,'paxNameFormatLang'
		,'paxNationalities'
		,'paxSections'
		,'productInfoPage'
		,'productSortByName'
		,'productNameLangNote'
		,'qtyConfig'
		,'refinesearchsection'
		,'relatedSearchCategories'
		,'resultsByProduct'
		,'resultsPerRoomPerScu'
		,'resultsSections'
		,'searchAmenities'
		,'searchDateOffset'
		,'searchNotes'
		,'searchPage'
		,'searchResultsPage'
		,'searchSections'
		,'showExternalDeadlines'
		,'showExternalRateplan'
		,'showPaxRates'
		,'showRates'
		,'showStaticProductContent'
		,'sortDefault'
		,'sortKeys'
		,'supplierNameLangNote'
		,'termsconditions'
		,'titles'
		,'useAdditionalCustomerDetails'
		,'useDepartureDate'
		,'useRoomPax'
		,'useTariffAvailability'
		,'qty'
		,'scu'
	);

	$configsjson = array();
	$langs = tp_languages();
	$syslang = tp_system_language();
	$trans = array();
	$tpconfigsjson = get_option('tp_configs_json');
	if (empty($tpconfigsjson))
	{
		if (!empty($langs))
		{
			foreach ($langs as $lang)
			{
				$configsstr = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $lang . '-configs.js');
				if (!empty($configsstr)) {
					$trans[$lang] = json_decode($configsstr, true);
				}
			}
		}
	}
	else
	{
		$configsstr = urldecode($tpconfigsjson);
		$trans = json_decode($configsstr, true);
	}
	
	$configs = $trans[$syslang];
	$srbOnly = array_map(function($srbcfg){ return $srbcfg['serviceButton']; }, $configs);
	$exploded = explode(',', get_option('tp_service_buttons'));
	$missingSrbs = array_diff($exploded, $srbOnly);
	foreach ($configs as $srbcfg)
	{
		$srb = $srbcfg['serviceButton'];
		echo '<table id="tpconfigsgroup"><tr>';
		echo '<th>' . (empty($srb) ? 'Default' : $srb) . '</th>';
		if (empty($langs))
		{
			echo '<th></th>';
		}
		else
		{
			foreach ($langs as $lang)
			{
				echo '<th>' . $lang . '</th>';
			}
		}
		echo '<th><span class="tpaddconfig">+</span></th></tr>';

		$cfgs = $srbcfg['config'];
		foreach ($opts as $opt)
		{
			foreach ($cfgs as $cfg)
			{
				if ($cfg['name']===$opt)
				{
					tpConfigOption($srb, $opts, $opt, $cfg['value'], $trans);
					break;
				}
			}
		}
		echo '</table>';
	}
	foreach ($missingSrbs as $srb) {
		echo '<table id="tpconfigsgroup"><tr>';
		echo '<th>' . $srb . '</th>';
		if (empty($langs))
		{
			echo '<th></th>';
		}
		else
		{
			foreach ($langs as $lang)
			{
				echo '<th>' . $lang . '</th>';
			}
		}
		echo '<th><span class="tpaddconfig">+</span></th></tr>';
		tpConfigOption($srb, $opts, $opts[0], "", $trans);
		echo '</table>';
	}

	?>
	<input id="tp_configs_json" type="hidden" name="tp_configs_json" value="<?php echo urlencode(json_encode($trans)); ?>">
	<script type="text/javascript">
	
	function tpConfigOptionChange(sel)
	{
		var newval = jQuery(sel).val();
		jQuery(sel).parents('tr').first().find('input').attr('name',newval);
		return true;
	}
	jQuery(function() {
		jQuery('#tpconfigsgroup .tpaddconfig').click(function(e){
			var f = jQuery(e.target).parents('tr').first().siblings().first();
			var n = jQuery('<tr>'  + f.html() + '</tr>');
			f.before(n);
		});

		jQuery('#tpsavebutton').click(function(){
		    var xs = jQuery('#tpconfigsgroup input').map(function(i,e){ 
			    var x = jQuery(e);
			    return {lang: x.data('lang'), srb: x.data('srb'), name: x.attr('name'), value: x.val()};
			});
			var xx = {};
			xs.map(function(i,x){
				var lang = x.lang;
				if (xx[lang] == undefined) {
					xx[lang] = {};
				}
				var srb = x.srb || 'Default';
				if (xx[lang][srb] == undefined) {
					xx[lang][srb] = [];
				}
				xx[lang][srb].push({name:x.name, value:x.value});
		    });
	        var json = {};
			for (lang in xx) 
			{ 
				json[lang] = []; 
				for (srb in xx[lang]) 
				{ 
					json[lang].push({'serviceButton': (srb == 'Default' ? '': srb), 'config': xx[lang][srb]});
				}
			}
			jQuery('#tp_configs_json').val(encodeURIComponent(JSON.stringify(json)));
			return true;
		});
	});
	</script>
<?php
}

function tp_translations_panel()
{
	tp_refresh_translations();
	tp_reload_searchparams();
	
	$tptranslations = get_option('tp_translations_json');	
	$langs = tp_languages();
	echo '<table id="tptranslationsgroup"><tr><th>Type</th><th>Code</th>';
	foreach ($langs as $lang)
	{
		echo '<th>' . $lang . '</th>';
	}
	echo '</tr>';
	$syslang = tp_system_language();
	
	$tplookups = get_option('tplookups');
	$defaultlookups = $tplookups[$syslang];
	foreach ($defaultlookups as $type => $codename)
	{
		foreach ($codename as $code => $name)
		{
			echo '<tr><td>' .  $type . '</td><td>' . $code . '</td><td>' . $name . '</td>';
			
			foreach ($langs as $lang)
			{
			    if ($lang !== $syslang)
				{
					if ( array_key_exists($lang, $tplookups) )
						$lookup = $tplookups[$lang];
					else
						$lookup = $tplookups[$syslang];
					$langname = isset($lookup) && array_key_exists($type, $lookup) && isset($lookup[$type]) && array_key_exists($code, $lookup[$type]) ? $lookup[$type][$code] : $name;
					echo '<td><input type="text" data-lang="' . $lang . '" data-type="' . $type . '" name="' . $code . '" value="' . $langname . '"/></td>';
					break;
				}
			}
		}
		echo '</tr>';
	}

	echo '</table>';

	?>
	<input id="tp_translations_json" type="hidden" name="tp_translations_json" value="<?php echo urlencode(json_encode($tptranslations)); ?>" />
	<script type="text/javascript">
	jQuery(function() {

		jQuery('#tpsavebutton').click(function(){
		    var xs = jQuery('#tptranslationsgroup input').map(function(i,e){ 
			    var x = jQuery(e);
			    return {lang: x.data('lang'), type: x.data('type'), code: x.attr('name'), name: x.val()};
			});
			var json = {};
			xs.map(function(i, x){
				var lang = x.lang || 'Default';
				if (json[lang] == undefined) {
					json[lang] = {};
				}
				if (json[lang][x.type] == undefined) {
					json[lang][x.type] = {};
				}
				json[lang][x.type][x.code] = x.name;
		    });
			jQuery('#tp_translations_json').val(encodeURIComponent(JSON.stringify(json)));
			return true;
		});
	});
	</script>
<?php 
}


/*
 [{feeid: 123, label: 'Museum Fee', srbs: ['Tickets'], suppliercodes: ['GILMUS', 'GILPRK'], productcodes: ['TICK01', 'TICK06']}]
 [{feeid: 123, label: 'Museum Fee', srbs: 'Tickets', suppliercodes: 'GILMUS,GILPRK', productcodes: 'TICK01,TICK06'}]
 */
function tp_booking_fees_admin()
{
    $fees = tpBookingFees();
	foreach ($fees as $fee)
	{
		echo '<h3>[' . $fee['id'] . '] ' . $fee['label'] . '</h3>';
		echo '<label>Label</label><input type="text" name="feelabel" value="' . $fee['label'] . '">';
		echo '<label>Fee Product Id</label><input type="text" name="id" value="' . $fee['id'] . '">';
		echo '<label>Service Buttons</label><input type="text" name="feesrbs" value="' . $fee['srbs'] . '">';
		echo '<label>Supplier Codes</label><input type="text" name="feesuppliercodes" value="' . $fee['suppliercodes'] . '">';
		echo '<label>Product Codes</label><input type="text" name="feeproductcodes" value="' . $fee['productcodes'] . '">';
	}
}

/*
[{feeid: 123, label: 'Courier Fee', srbs: ['JR Pass']},
 {feeid: 345, label: 'Courier Fee', srbs: ['Airport Transfers']},
 {feeid: 567, label: 'Courier Fee', srbs: ['Tickets'], suppliercodes: ['DISNEY', 'UNIVER','GHIBLI']}
 ]
*/
function tp_delivery_fees_admin()
{
	$fees = tpDeliveryFees();

	tp_log('tp_delivery_fees_admin' . print_r($fees, true)); ?>

	<table>
		 <tr valign="top">
			<th scope="row">Delivery Fee Price Selection Method</th>
			<td>
				<!-- <input type="text" class="regular-text"  name="tp_delivery_price_selection" value="<?php echo get_option('tp_delivery_price_selection') ; ?>" /> -->
				<select name="tp_delivery_price_selection">
					<option value="min" <?php echo get_option('tp_delivery_price_selection', 'max') == 'min' ? 'selected="selected"' : ''?>>Min Price</option>
					<option value="max" <?php echo get_option('tp_delivery_price_selection', 'max') == 'max' ? 'selected="selected"' : ''?>>Max Price</option>
				</select>
			</td>
		</tr>
	</table>


	<?php


	foreach ($fees as $fee)
	{
		echo '<h3>[' . $fee['id'] . '] ' . $fee['label'] . '</h3>';
		echo '<label>Label</label><input type="text" name="feelabel" value="' . $fee['label'] . '">';
		echo '<label>Fee Product Id</label><input type="text" name="id" value="' . $fee['id'] . '">';
		echo '<label>Service Buttons</label><input type="text" name="feesrbs" value="' . $fee['srbs'] . '">';
		echo '<label>Supplier Codes</label><input type="text" name="feesuppliercodes" value="' . $fee['suppliercodes'] . '">';
		echo '<label>Product Codes</label><input type="text" name="feeproductcodes" value="' . $fee['productcodes'] . '">';
	}

}

?>