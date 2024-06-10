<?php
	
	global $moWpnsUtility,$mo2f_dirName;

	$username = $user->data->user_login;
	$message  = isset($newpassword) && ($newpassword != $confirmpassword) ? "Both Passwords do not match." : "Please enter a stronger password.";
	$css_file = plugins_url('wp-security-pro/includes/css/style_settings.css',$mo2f_dirName);

	$js_file  = plugins_url('wp-security-pro/includes/js/settings_page.js',$mo2f_dirName);

	wp_register_script('mo2f_setting_page_js',$js_file,[],MO2F_VERSION);
	wp_register_style( 'mo2f_seetings_style',$css_file,[],MO2F_VERSION);

	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'change-password.php';
	exit;

