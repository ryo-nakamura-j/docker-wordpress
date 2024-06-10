<?php
	
	$email_registered = 1;
	global $Mo2fdbQueries;
	$email =$Mo2fdbQueries->get_user_detail( 'mo2f_user_email', get_current_user_id() );
	if($email == '' or !isset($email))
		$email = wp_get_current_user()->user_email;

	if(isset($email))
		$email_registered = 1;
	else
		$email_registered = 0;

	$upgrade_url	= add_query_arg(array('page' => 'mo_2fa_upgrade'				), sanitize_url($_SERVER['REQUEST_URI']));

	if(current_user_can( 'manage_options' ) && isset($_POST['option']))
	{
	switch(sanitize_text_field(wp_unslash($_POST['option'])))
	{
	case "mo2f_enable_2FA_on_login_page_option":
	wpns_handle_enable_2fa_login_prompt(($_POST));
	break;
	}
	}
	include_once $mo2f_dirName .DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'setup_twofa.php';

	function wpns_handle_enable_2fa_login_prompt($postvalue)
	{
	    if(isset($postvalue['mo2f_enable_2fa_prompt_on_login_page']))
        {
            update_site_option('mo2f_enable_2fa_prompt_on_login_page',sanitize_text_field($postvalue['mo2f_enable_2fa_prompt_on_login_page']));

        }
	    else update_site_option('mo2f_enable_2fa_prompt_on_login_page',0 );


        if( MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'get_site_option') == 1 )
        {
            do_action('wpns_show_message',MoWpnsMessages::showMessage('TWO_FA_ON_LOGIN_PROMPT_ENABLED'),'SUCCESS');
        }
		else{

			if(isset($postvalue['mo2f_enable_2fa_prompt_on_login_page'])){
				do_action('wpns_show_message',MoWpnsMessages::showMessage('TWO_FA_PROMPT_LOGIN_PAGE'),'ERROR');
			}else{
			do_action('wpns_show_message',MoWpnsMessages::showMessage('TWO_FA_ON_LOGIN_PROMPT_DISABLED'),'ERROR');
	 		}
	 	}
	}
