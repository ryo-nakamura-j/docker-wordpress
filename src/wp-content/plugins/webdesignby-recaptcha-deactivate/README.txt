=== Plugin Name ===
Contributors: websitedesignby
Donate link: https://www.webdesignby.com/dontate/
Tags: captcha, recaptcha, re-captcha, wp-admin, security, secure, google
Requires at least: 3.0.1
Tested up to: 4.6
Stable tag: 1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Google’s simple checkbox reCAPTCHA to WordPress wp-admin login page. 

== Description ==

Add Google's latest simple checkbox reCAPTCHA to Wordpress default login page. This plugin will help secure your wordpress administration area against brute force hacking attacks using the latest, simple and elegant 'No Captcha' reCaptcha checkbox from Google.

This plugin was created because some of the others I tested broke my login page on activation. This plugin is very easy to install and set up. I use it on multiple websites so it is tested regularly.
Simply install, activate and enter your credentials from Google.
https://www.google.com/recaptcha/admin

__PHP 5 >= PHP 5.3.0 or  Required__

== Installation ==

1. Upload `webdesignby-recaptcha` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. [Get reCaptcha API Keys from Google](https://www.google.com/recaptcha/admin)
4. Enter Keys in WP Admin -> Settings -> reCaptcha

== Frequently Asked Questions ==

= I am getting the error "Invalid domain for site key" on my login page =

This means you need to generate a new set of API keys for your domain:
<https://www.google.com/recaptcha/admin>

* You will need to manually remove the plugin in order to log into wp-admin, re-install the plugin and enter the new keys.

= I see the following message after installing the plugin: Parse error: syntax error, unexpected T_STRING in /wordpress/wp-content/plugins/webdesignby-recaptcha/webdesignby-recaptcha.php on line 11 =

Check your PHP version. __PHP 5 >= PHP 5.3.0 Required__

== Screenshots ==

1. wp-admin with Google reCaptcha