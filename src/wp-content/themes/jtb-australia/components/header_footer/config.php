<?php
	$dist = '/components/header_footer';
	// get protocol.
	$url = get_template_directory_uri() . $dist;
	$protocol = empty($_SERVER["HTTPS"]) ? 'http://' : 'https://';
	
	// get host.
	$app_url = $url;
	define('APP_URL', $app_url);
	define('APP_PATH', dirname(__FILE__).'/');
	define("APP_URL_SHORT", "");
	define("APP_URL_HTTPS", $url);
	define('APP_ASSETS', get_template_directory_uri() . $dist . '/assets/');
	define('APP_PATH_WP', dirname(__FILE__).'/wp/');
	
	define("APP_SP_URL",  APP_URL."sp/");
	define("APP_SP_PATH", APP_PATH."sp/");

	define('GOOGLE_MAP_API_KEY', '');
	define('GOOGLE_RECAPTCHA_KEY_API', '');
	define('GOOGLE_RECAPTCHA_KEY_SECRET', '');
?>