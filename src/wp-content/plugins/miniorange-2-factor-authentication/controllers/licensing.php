<?php
	
	global $moWpnsUtility,$mo2f_dirName;

	$default_url 	  	= add_query_arg( array('page' => 'default'	), sanitize_url($_SERVER['REQUEST_URI'] ));
	$form_action 	  	= MoWpnsConstants::HOST_NAME.'/moas/login';
	$admin_email 	  	= get_option('mo2f_email');
	$redirect_url	  	= MoWpnsConstants::HOST_NAME .'/moas/initializepayment';

	$basic_plan_price 	= '$9 / year'; 
	$premium_plan_price	= '$9 / year + One Time Setup Fees';


	$basic_plan_features= array(
		"Brute Force Protection ( Login Security and Monitoring - Limit Login Attempts and track user logins. )",
		"User Registration Security - Disallow Disposable / Fake email addresses",
		"IP Blocking:(manual and automatic) [Blaclisting and whitelisting included",
		"Advanced Blocking based on: IP range",
		"Protection for WP files",
		"Security Log - Logs Blocked IPs, Spammers, Bots, HTTP 404,403 and 400 logging",
		"Database Backup",
		"Google reCAPTCHA",
		"Password protection - Enforce Strong Password : Check Password strength for all users",
		"Mobile authentication based on QR code, OTP over SMS and email, Push, Soft token (15+ methods to choose from)<br>For Unlimited Users",
		"Advanced activity logs	auditing and reporting",
		"Risk based access - Contextual authentication based on device, location, time of access and user behavior",
		"Advanced User Verification",
		"Social Login Integration",
		""
	);

	$premium_plan_features= array(
		"Brute Force Protection ( Login Security and Monitoring - Limit Login Attempts and track user logins. )",
		"User Registration Security - Disallow Disposable / Fake email addresses",
		"IP Blocking:(manual and automatic) [Blaclisting and whitelisting included",
		"Advanced Blocking based on: IP range",
		"Protection for WP files",
		"Security Log - Logs Blocked IPs, Spammers, Bots, HTTP 404,403 and 400 logging",
		"Database Backup",
		"Google reCAPTCHA",
		"Password protection - Enforce Strong Password : Check Password strength for all users",
		"Mobile authentication based on QR code, OTP over SMS and email, Push, Soft token (15+ methods to choose from)<br>For Unlimited Users",
		"Advanced activity logs	auditing and reporting",
		"Risk based access - Contextual authentication based on device, location, time of access and user behavior",
		"Advanced User Verification",
		"Social Login Integration",
		'End to End Integration Support'
	);

	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'licensing.php';