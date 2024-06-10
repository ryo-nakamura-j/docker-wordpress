<?php
	
	global $moWpnsUtility, $mo2f_dirName;


	if(current_user_can( 'manage_options' ) && isset($_POST['option']))
	{
		switch(sanitize_text_field(wp_unslash($_POST['option'])))
		{
			case "mo_wpns_enable_fake_domain_blocking":
				wpns_handle_domain_blocking($_POST);						break;
			case "mo_wpns_advanced_user_verification":
				wpns_handle_advanced_user_verification($_POST);				break;
			case "mo_wpns_social_integration":
				wpns_handle_enable_social_login($_POST);					break;
			
		}
	}

	$otpVerify_url 	= add_query_arg( array('page' => 'mosettings', 'tab'=>'settings'), sanitize_url($_SERVER['REQUEST_URI'] ));
	$openid_url 	= add_query_arg( array('page' => 'mo_openid_settings'								 ), sanitize_url($_SERVER['REQUEST_URI'] ));
	$domain_blocking= get_option('mo_wpns_enable_fake_domain_blocking') 		? "checked" : "";
	$user_verify	= get_option('mo_wpns_enable_advanced_user_verification') 	? "checked" : "";	
	$social_login	= get_option('mo_wpns_enable_social_integration') 			? "checked" : "";
	
function mo2f_user_verify() {
	if ( $user_verify ) {
		$moOTPPlugin = new OTPPlugin();
		$status      = $moOTPPlugin->getstatus();
		switch ( $status ) {
			case "ACTIVE":
				echo "<br><a href='" . esc_url($otpVerify_url) . "'>Click here to configure.</a>";
				$moOTPPlugin->updatePluginConfiguration();
				break;
			case "INSTALLED":
				$path        = "miniorange-otp-verification/miniorange_validation_settings.php";
				$activateUrl = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $path ), 'activate-plugin_' . $path );
				echo '<br><span style="color:red">For Advanced User Verification you need to have miniOrange OTP Verification plugin activated.</span><br><a href="' . esc_url($activateUrl) . '">Click here to activate OTP Verification Plugin</a>';
				break;
			default:
				$action       = 'install-plugin';
				$slug         = 'miniorange-otp-verification';
				$install_link = wp_nonce_url(
					add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ),
					$action . '_' . $slug
				);
				echo '<br><span style="color:red">For Advanced User Verification you need to have miniOrange OTP Verification plugin installed.</span><br><a href="' . esc_url($install_link) . '">Install OTP Verification plugin</a>';
				break;
		}
	}
}

function mo2f_social_login() {
		$moSocialLogin = new SocialPlugin();
		$status        = $moSocialLogin->getstatus();
		switch ( $status ) {
			case "ACTIVE":
				echo "<br><a href='" . esc_url( $openid_url ) . "'>Click here to configure.</a>";
				break;
			case "INSTALLED":
				$path        = "miniorange-login-openid/miniorange_openid_sso_settings.php";
				$activateUrl = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $path ), 'activate-plugin_' . $path );
				echo '<br><span style="color:red">For Social Login Integration you need to have miniOrange Social Login, Sharing plugin activated.</span><br><a href="' . esc_url( $activateUrl ) . '">Click here to activate Social Login, Sharing Plugin</a>';
				break;
			default:
				$action       = 'install-plugin';
				$slug         = 'miniorange-login-openid';
				$install_link = wp_nonce_url(
					add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ),
					$action . '_' . $slug
				);
				echo '<br><span style="color:red">For Social Login Integration you need to have miniOrange Social Login, Sharing plugin installed.</span><br><a href="' . esc_url( $install_link ) . '">Install Social Login, Sharing plugin</a>';
				break;
		}
}
	
	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'registration-security.php';




	/* REGISTRATION SECURITY RELATED FUNCTIONS*/

	//Function to handle enabling and disabling domain blocking
	function wpns_handle_domain_blocking($postvalue)
	{
		$enable_fake_emails = isset($postvalue['mo_wpns_enable_fake_domain_blocking']) ? true : false;
		update_option( 'mo_wpns_enable_fake_domain_blocking', $enable_fake_emails);

		if($enable_fake_emails)
			do_action('wpns_show_message',MoWpnsMessages::showMessage('DOMAIN_BLOCKING_ENABLED'),'SUCCESS');
		else
			do_action('wpns_show_message',MoWpnsMessages::showMessage('DOMAIN_BLOCKING_DISABLED'),'ERROR');
	}


	//Function to enable and disable User Verification for the Default Registration Page
	function wpns_handle_advanced_user_verification($postvalue)
	{
		$enable_advanced_user_verification = isset($postvalue['mo_wpns_enable_advanced_user_verification']) ? true : false;
		update_option( 'mo_wpns_enable_advanced_user_verification',  $enable_advanced_user_verification);

		if($enable_advanced_user_verification)
		{
			update_option('mo_customer_validation_wp_default_enable',1);
			do_action('wpns_show_message',MoWpnsMessages::showMessage('ENABLE_ADVANCED_USER_VERIFY'),'SUCCESS');
		}
		else
		{
			update_option('mo_customer_validation_wp_default_enable',0);
			do_action('wpns_show_message',MoWpnsMessages::showMessage('DISABLE_ADVANCED_USER_VERIFY'),'ERROR');
		}
	}


	//Function to enable and disable Social Login
	function wpns_handle_enable_social_login($postvalue)
	{
		$social_login = isset($postvalue['mo_wpns_enable_social_integration']) ? true : false;
		update_option( 'mo_wpns_enable_social_integration',  $social_login);

		if($social_login)
			do_action('wpns_show_message',MoWpnsMessages::showMessage('ENABLE_SOCIAL_LOGIN'),'SUCCESS');
		else
			do_action('wpns_show_message',MoWpnsMessages::showMessage('DISABLE_SOCIAL_LOGIN'),'ERROR');
	}