<?php
	
	global $moWpnsUtility,$mo2f_dirName;
	if(current_user_can( 'manage_options' ) && isset($_POST['option']))
	{
		switch(sanitize_text_field(wp_unslash($_POST['option'])))
		{
			case "mo_wpns_2fa_with_network_security":
				$security_features = new Mo_2fa_security_features();
				$security_features->wpns_2fa_with_network_security($_POST);			break;
		}
	}
	$network_security_features= MoWpnsUtility::get_mo2f_db_option('mo_wpns_2fa_with_network_security', 'get_option')		? "checked" : "";

	if( isset( $_GET[ 'page' ])){
		$tab_count= get_site_option('mo2f_tab_count', 0);
		if($tab_count == 5)
				update_site_option('mo_2f_switch_all', 1);
		else
			update_site_option('mo_2f_switch_all', 0);
		switch(sanitize_text_field($_GET['page']))
		{
            case 'mo_2fa_login_and_spam':
            	update_option('mo_2f_switch_loginspam', 1);
            	if($tab_count < 5 && !get_site_option('mo_2f_switch_loginspam'))
            		update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
																	break;
			case 'mo_2fa_waf':
				update_option('mo_2f_switch_waf', 1);
				if($tab_count < 5 && !get_site_option('mo_2f_switch_waf'))
					update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
														    		break;
			case 'mo_2fa_advancedblocking':
				update_option('mo_2f_switch_adv_block', 1);
				if($tab_count < 5 && !get_site_option('mo_2f_switch_adv_block'))
					update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
																	break;
			
			case 'mo_2fa_malwarescan':
				update_option('mo_2f_switch_malware', 1);
				if($tab_count < 5 && !get_site_option('mo_2f_switch_malware'))
					update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
																	break;
		}
	}
	$profile_url	= esc_url(add_query_arg( array('page' => 'mo_2fa_account'		), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$login_security	= esc_url(add_query_arg( array('page' => 'default'			), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$waf			= esc_url(add_query_arg( array('page' => 'mo_2fa_waf'				), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$login_and_spam = esc_url(add_query_arg( array('page' => 'mo_2fa_login_and_spam'   ), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$register_url	= esc_url(add_query_arg( array('page' => 'registration'		), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$blocked_ips	= esc_url(add_query_arg( array('page' => 'mo_2fa_blockedips'		), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$advance_block	= esc_url(add_query_arg( array('page' => 'mo_2fa_advancedblocking'	), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$notif_url		= esc_url(add_query_arg( array('page' => 'mo_2fa_notifications'	), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$reports_url	= esc_url(add_query_arg( array('page' => 'mo_2fa_reports'			), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$license_url	= esc_url(add_query_arg( array('page' => 'mo_2fa_upgrade'  		), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$help_url		= esc_url(add_query_arg( array('page' => 'mo_2fa_troubleshooting'	), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$addons_url		= esc_url(add_query_arg( array('page' => 'mo_2fa_addons'			), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$content_protect= esc_url(add_query_arg( array('page' => 'content_protect'	), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	
	$scan_url       = esc_url(add_query_arg( array('page' => 'mo_2fa_malwarescan'      ), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	$two_fa         = esc_url(add_query_arg( array('page' => 'mo_2fa_two_fa'           ), sanitize_text_field($_SERVER['REQUEST_URI'] )));
	//Added for new design
    $dashboard_url	= esc_url(add_query_arg(array('page' => 'mo_2fa_dashboard'			), sanitize_text_field($_SERVER['REQUEST_URI'])));
    $upgrade_url	= esc_url(add_query_arg(array('page' => 'mo_2fa_upgrade'				), sanitize_text_field($_SERVER['REQUEST_URI'])));
    $request_demo_url = esc_url(add_query_arg(array('page' => 'mo_2fa_request_demo'				), sanitize_text_field($_SERVER['REQUEST_URI']))); 
    $request_offer_url = esc_url(add_query_arg(array('page' => 'mo_2fa_request_offer'				), sanitize_text_field($_SERVER['REQUEST_URI'])));
    $trial_url			= esc_url(add_query_arg( array('page' => 'mo_2fa_trial'			), sanitize_text_field($_SERVER['REQUEST_URI'] )));

   //dynamic
    $logo_url = plugin_dir_url(dirname(__FILE__)) . 'includes/images/miniorange_logo.png';
    $login_with_usename_only_url = plugin_dir_url(dirname(__FILE__)) . 'includes/images/login-with-2fa-and-password.png';
    $hide_login_form_url = plugin_dir_url(dirname(__FILE__)) . 'includes/images/hide_default_login_form.png';
    $new_url = plugin_dir_url(dirname(__FILE__)) . 'includes/images/new.png';
    $shw_feedback	= get_option('donot_show_feedback_message') ? false: true;
    
    $moPluginHandler= new MoWpnsHandler();
    $safe			= $moPluginHandler->is_whitelisted($moWpnsUtility->get_client_ip());

    $active_tab 	= sanitize_text_field($_GET['page']);

    $user_id = get_current_user_id();
    $mo2f_two_fa_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user_id );
    $backup_codes_remaining = get_user_meta($user_id, 'mo2f_backup_codes', true);
	if(is_array($backup_codes_remaining)){
		$backup_codes_remaining = sizeof($backup_codes_remaining);
	}else{
		$backup_codes_remaining = 0;
	}

	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'navbar.php';
