<?php
global $moWpnsUtility,$mo2f_dirName;

if(current_user_can( 'manage_options' )  && isset($_POST['option']))
{		
	switch(sanitize_text_field($_POST['option']))
	{
		case "mo_wpns_content_protection":
			wpns_handle_content_protection($_POST);						break;
		case "mo_wpns_enable_comment_spam_blocking":
			wpns_handle_comment_spam_blocking($_POST);					break;
		case "mo_wpns_enable_comment_recaptcha":
			wpns_handle_comment_recaptcha($_POST);						break;
		case "mo_wpns_comment_recaptcha_settings":
			wpns_save_comment_recaptcha($_POST);						break;		
	}
}


$protect_wp_config 		= get_option('mo2f_protect_wp_config') 		   		 ? "checked" : "";
$protect_wp_uploads		= get_option('mo2f_prevent_directory_browsing') 	 ? "checked" : "";
$disable_file_editing	= get_option('mo2f_disable_file_editing') 	   		 ? "checked" : ""; 
$comment_spam_protect	= get_option('mo_wpns_enable_comment_spam_blocking') ? "checked" : "";
$enable_recaptcha 		= get_option('mo_wpns_enable_comment_recaptcha')     ? "checked" : "";
$htaccess_file 			= get_option('mo2f_htaccess_file') 					 ? "checked" : "";
$restAPI 				= get_site_option('mo2f_restrict_restAPI') 				 ? "checked" : "";
$test_recaptcha_url		= "";
$test_recaptcha_url_v3   = "";
$wp_config 		   		= site_url().'/wp-config.php';
$wp_uploads 	   		= get_site_url().'/wp-content/uploads';
$plugin_editor			= get_site_url().'/wp-admin/plugin-editor.php';
$restAPI_link 			= rest_url().'wp'.DIRECTORY_SEPARATOR.'v2'.DIRECTORY_SEPARATOR.'users';
$restApiPlugin			= 'https:'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'www.wordpress.org'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'wp-rest-api-authentication';
if($enable_recaptcha)
{
	$test_recaptcha_url	= add_query_arg( array('option'=>'testrecaptchaconfig'), sanitize_url($_SERVER['REQUEST_URI'] ));	
	$captcha_site_key	= get_option('mo_wpns_recaptcha_site_key'  );
	$captcha_secret_key = get_option('mo_wpns_recaptcha_secret_key');
}

include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'content-protection.php';

/* CONTENT PROTECTION FUNCTIONS */

//Function to save content protection settings
function wpns_handle_content_protection()
{		
	isset($_POST['protect_wp_config']) 			? update_option('mo2f_protect_wp_config'		 , sanitize_text_field($_POST['protect_wp_config'])	)		 : update_option('mo2f_protect_wp_config'			,0);
	isset($_POST['prevent_directory_browsing']) ? update_option('mo2f_prevent_directory_browsing', sanitize_text_field($_POST['prevent_directory_browsing'])): update_option('mo2f_prevent_directory_browsing',0);
	isset($_POST['disable_file_editing']) 		? update_option('mo2f_disable_file_editing'		 , sanitize_text_field($_POST['disable_file_editing']))		 : update_option('mo2f_disable_file_editing'		,0);
	isset($_POST['mo2f_htaccess_file']) 		? update_option('mo2f_htaccess_file'			 , sanitize_text_field($_POST['mo2f_htaccess_file'])) 		 : update_option('mo2f_htaccess_file',0);
	if(isset($_POST['restrictAPI'])){
		update_site_option('mo2f_restrict_restAPI', 1);	
	}
	else{
		update_site_option('mo2f_restrict_restAPI',0);	
	}
	


					
	$mo_wpns_htaccess_handler = new MoWpnsHandler();
	$mo_wpns_htaccess_handler->update_htaccess_configuration();
	do_action('wpns_show_message',MoWpnsMessages::showMessage('CONTENT_PROTECTION_ENABLED'),'SUCCESS');
}


//Function to handle comment spam blocking
function wpns_handle_comment_spam_blocking($postvalue)
{
	$enable  = isset($postvalue['mo_wpns_enable_comment_spam_blocking']) ? true : false;
	update_option('mo_wpns_enable_comment_spam_blocking', $enable);
	if($enable)
		do_action('wpns_show_message',MoWpnsMessages::showMessage('CONTENT_SPAM_BLOCKING'),'SUCCESS');
	else
		do_action('wpns_show_message',MoWpnsMessages::showMessage('CONTENT_SPAM_BLOCKING_DISABLED'),'ERROR');
}


//Function to handle reCAPTCHA for comments
function wpns_handle_comment_recaptcha($postvalue)
{
	$enable  = isset($postvalue['mo_wpns_enable_comment_recaptcha']) ? true : false;
	update_option('mo_wpns_enable_comment_recaptcha', $enable);
	if($enable)
		do_action('wpns_show_message',MoWpnsMessages::showMessage('CONTENT_RECAPTCHA'),'SUCCESS');
	else
		do_action('wpns_show_message',MoWpnsMessages::showMessage('CONTENT_RECAPTCHA_DISABLED'),'ERROR');
}

function wpns_save_comment_recaptcha($postvalue){
	update_option('mo_wpns_recaptcha_site_key', sanitize_post($postvalue['mo_wpns_recaptcha_site_key']));
	update_option('mo_wpns_recaptcha_secret_key', sanitize_post($postvalue['mo_wpns_recaptcha_secret_key']));
	do_action('wpns_show_message',MoWpnsMessages::showMessage('RECAPTCHA_ENABLED'),'SUCCESS');
}