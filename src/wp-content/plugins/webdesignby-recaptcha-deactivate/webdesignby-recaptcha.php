<?php
/*
Plugin Name: Webdesignby Recaptcha
Plugin URI: http://www.webdesignby.com/programming/wordpress-no-captcha-recaptcha-plugin/
Description: Add reCAPTCHA to Wordpress default login page.
Author: webdesignby.com
Version: 1.7
Author URI: http://www.webdesignby.com/
*/

namespace Webdesignby;

// Includes Classes
require_once( plugin_basename( '/class/RecaptchaOptionsPage.php') );
require_once( plugin_basename( '/class/Recaptcha.php') );

$config = array();

$opt = get_option('webdesignby_recaptcha');

// Get credentials from: https://www.google.com/recaptcha/admin
// Uncomment below to manually override the recaptcha settings.
/* 
    $opt = array(
        'g_site_key'    =>  'YOUR SITE KEY',
        'g_secret_key'  =>  'YOUR SECRET KEY',
    );

*/

if( ! empty($opt) && is_array($opt)){
    $config['site_key']     = $opt['g_site_key'];
    $config['secret_key']   = $opt['g_secret_key'];
}

$recaptcha = new \Webdesignby\Recaptcha($config);


\register_deactivation_hook( __FILE__, array($recaptcha, 'uninstall'));


