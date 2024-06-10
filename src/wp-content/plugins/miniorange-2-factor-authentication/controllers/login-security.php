<?php 

	global $moWpnsUtility,$mo2f_dirName;

	$twofactor_url 		 	= add_query_arg( 
										array('page' => 'mo_2fa_two_fa')
										, sanitize_url($_SERVER['REQUEST_URI']) 
							);
	


	if(current_user_can( 'manage_options' ) && isset($_REQUEST['option']))
	{
		switch(sanitize_text_field($_REQUEST['option']))
		{
			case "mo_wpns_enable_brute_force":
				wpns_handle_bf_enable_form($_POST);				break;
			case "mo_wpns_brute_force_configuration":
				wpns_handle_bf_configuration_form($_POST);		break;
			case "mo_wpns_slow_down_attacks":
				wpns_handle_dos_enable_form($_POST);			break;
			case "mo_wpns_slow_down_attacks_config":
				wpns_handle_dos_configuration($_POST);			break;
			case "mo_wpns_enable_2fa":
				wpns_handle_enable_2fa($_POST);					break;
			case "mo2f_enforce_strong_passswords":
				wpns_handle_enable_strong_password($_POST);		break;
			case "mo_wpns_rba_enable_2fa":
				wpns_handle_enable_rba();						break;
			case "mo_wpns_recaptcha_settings":
				wpns_handle_recaptcha_configuration($_POST);	break;
			case "mo_wpns_enable_rename_login_url":
				wpns_handle_enable_rename_login_url($_POST);	break;
			
		}
	}

	$allwed_login_attempts 	= get_option('mo2f_allwed_login_attempts')	  	  ? get_option('mo2f_allwed_login_attempts')  	 : 10;
	$time_of_blocking_type 	= get_option('mo2f_time_of_blocking_type')	 	  ? get_option('mo2f_time_of_blocking_type')  	 : "permanent";
	$time_of_blocking_val 	= get_option('mo2f_time_of_blocking_val')	  	  ? get_option('mo2f_time_of_blocking_val')   	 : 3;
	$brute_force_enabled 	= MoWpnsUtility::get_mo2f_db_option('mo2f_enable_brute_force', 'get_option') 		  ? "checked" 								  	 : "";
	$remaining_attempts 	= MoWpnsUtility::get_mo2f_db_option('mo2f_show_remaining_attempts', 'get_option') 	  ? "checked" 								  	 : "";
	$slow_down_attacks	= get_option('mo_wpns_slow_down_attacks') 		  ? "checked" 								  	 : "";
	$enable_2fa		= get_option('mo_wpns_enable_2fa')				  ? "checked" 								  	 : "";
	
	$enforce_strong_password= MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords', 'get_option') ? "checked" 									 : "";
	$attack_delay 			= get_option('mo_wpns_slow_down_attacks_delay')   ? get_option('mo_wpns_slow_down_attacks_delay'): 2 ;
	$google_recaptcha		= get_option('mo_wpns_activate_recaptcha')		  ? "checked": "";
    
   
	
	$test_recaptcha_url		= add_query_arg( array('option'=>'testrecaptchaconfig'), sanitize_url($_SERVER['REQUEST_URI']));

	
	$test_recaptcha_url_v3		= add_query_arg( array('option'=>'testrecaptchaconfig3'), sanitize_url($_SERVER['REQUEST_URI']));
		$captcha_url_v2		= 'https://www.google.com/recaptcha/admin#list';
		$captcha_url_v3      = 'https://www.google.com/recaptcha/admin/create';

		if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v2'){
		$captcha_site_key	= get_option('mo_wpns_recaptcha_site_key');
		$captcha_secret_key = get_option('mo_wpns_recaptcha_secret_key');}

		else if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v3'){
		$captcha_site_key	= get_option('mo_wpns_recaptcha_site_key_v3');
		$captcha_secret_key = get_option('mo_wpns_recaptcha_secret_key_v3');
		}
		
		$captcha_login		= get_option('mo_wpns_activate_recaptcha_for_login') 		? "checked" : "";
		$captcha_reg		= get_option('mo_wpns_activate_recaptcha_for_registration') ? "checked" : "";
	
	
	$strong_password_account= MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords_for_accounts', 'get_option') ? MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords_for_accounts', 'get_option') : "all";

	
		$mo2FAPlugin = new TwoFAPlugin();
		$twofa_status= $mo2FAPlugin->getstatus();
		switch ($twofa_status)
		{
			
			case "ACTIVE":
				$mo2FAPlugin->updatePluginConfiguration();
				break;
			case "INSTALLED":
				$path 			 = "miniorange-2-factor-authentication/miniorange_2_factor_settings.php";
				$activateUrl 	 = wp_nonce_url(admin_url('plugins.php?action=activate&plugin='.$path), 'activate-plugin_'.$path);
				break;
			default:
				$action 	  	 = 'install-plugin';
				$slug 		  	 = 'miniorange-2-factor-authentication';
				$install_link 	 =  wp_nonce_url(
										add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ),
										$action.'_'.$slug
									);
				break;
		}
		
	

	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'login-security.php';



/** LOGIN SECURITY RELATED FUNCTIONS **/

	//Function to handle enabling and disabling of brute force protection
	function wpns_handle_bf_enable_form($postData)
	{
		$enable  =  isset($postData['enable_brute_force_protection']) ? sanitize_text_field($postData['enable_brute_force_protection']) : false;
		update_option( 'mo2f_enable_brute_force', $enable );

		if($enable)
			do_action('wpns_show_message',MoWpnsMessages::showMessage('BRUTE_FORCE_ENABLED'),'SUCCESS');
		else
			do_action('wpns_show_message',MoWpnsMessages::showMessage('BRUTE_FORCE_DISABLED'),'ERROR');
	}


	//Function to handle brute force configuration
	function wpns_handle_bf_configuration_form($postData)
	{
		$login_attempts 	= sanitize_text_field($postData['allwed_login_attempts']);
		$blocking_type  	= sanitize_text_field($postData['time_of_blocking_type']);
		$blocking_value 	= isset($postData['time_of_blocking_val'])	 ? sanitize_text_field($postData['time_of_blocking_val'])	: false;
		$remaining_attempts = isset($postData['show_remaining_attempts'])? sanitize_text_field($postData['show_remaining_attempts']) : false;

		update_option( 'mo2f_allwed_login_attempts'		, $login_attempts );
		update_option( 'mo2f_time_of_blocking_type'		, $blocking_type );
		update_option( 'mo2f_time_of_blocking_val' 	, $blocking_value  );
		update_option( 'mo2f_show_remaining_attempts', $remaining_attempts );

		do_action('wpns_show_message',MoWpnsMessages::showMessage('CONFIG_SAVED'),'SUCCESS');
	}


	


	//Function to handle enabling and disabling of two factor
	function wpns_handle_enable_2fa($postData)
	{
		$enable_2fa = isset($postData['mo_wpns_enable_2fa']) ? true : false;
		update_option( 'mo_wpns_enable_2fa',  $enable_2fa);

		if($enable_2fa)
			do_action('wpns_show_message',MoWpnsMessages::showMessage('TWOFA_ENABLED'),'SUCCESS');
		else
			do_action('wpns_show_message',MoWpnsMessages::showMessage('TWOFA_DISABLED'),'ERROR');
	}


	//Function to handle enabling and disabling enforcement of strong password
	function wpns_handle_enable_strong_password($postData)
	{
		$set = isset($postData['mo2f_enforce_strong_passswords']) ? sanitize_text_field($postData['mo2f_enforce_strong_passswords']) : 0;
		update_option( 'mo2f_enforce_strong_passswords'			   ,  $set);
		update_option( 'mo2f_enforce_strong_passswords_for_accounts',  sanitize_text_field($postData['mo2f_enforce_strong_passswords_for_accounts']));
		if($set)
			do_action('wpns_show_message',MoWpnsMessages::showMessage('STRONG_PASS_ENABLED'),'SUCCESS');
		else
			do_action('wpns_show_message',MoWpnsMessages::showMessage('STRONG_PASS_DISABLED'),'ERROR');
	}


	//Function to handle enabling and disabling RBA
	function wpns_handle_enable_rba()
	{
		update_option( 'mo_wpns_enable_2fa'		  , 1);
		update_option( 'mo2f_activate_plugin'	  , 1);
		update_option( 'mo_wpns_risk_based_access', 1);
		do_action('wpns_show_message',MoWpnsMessages::showMessage('RBA_ENABLED'),'SUCCESS');
	}


	//Function to handle RBA configuration
	

	//Function to handle enabling and disabling google recaptcha
	function wpns_handle_enable_recaptcha($postData)
	{
		$enable = isset($postData['mo_wpns_activate_recaptcha']) ? sanitize_text_field($postData['mo_wpns_activate_recaptcha']) : false;
		update_option( 'mo_wpns_activate_recaptcha', $enable );

		if($enable)
			do_action('wpns_show_message',MoWpnsMessages::showMessage('RECAPTCHA_ENABLED'),'SUCCESS');
		else
		{
			update_option( 'mo_wpns_activate_recaptcha_for_login'		, false );
			update_option( 'mo_wpns_activate_recaptcha_for_registration', false );
            update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login'		, false );
			update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration', false );
			do_action('wpns_show_message',MoWpnsMessages::showMessage('RECAPTCHA_DISABLED'),'ERROR');
		}
	}


	//Function to handle recaptcha configuration
	
	
	

	function wpns_handle_enable_rename_login_url($postData){
		$enable_rename_login_url_checkbox = false;
		if(isset($postData['enable_rename_login_url_checkbox'])  && $postData['enable_rename_login_url_checkbox']){
			$enable_rename_login_url_checkbox = sanitize_text_field($postData['enable_rename_login_url_checkbox']);
			do_action('wpns_show_message','Rename Admin Login Page URL is enabled.','SUCCESS');
		}else {
			do_action('wpns_show_message','Rename Admin Login Page URL is disabled.','SUCCESS');
		}
		$loginurl = get_option('login_page_url');
		if ($loginurl == "") {
			update_option('login_page_url', "mylogin");
		}
		update_option( 'mo_wpns_enable_rename_login_url', $enable_rename_login_url_checkbox);
	}
	
	
