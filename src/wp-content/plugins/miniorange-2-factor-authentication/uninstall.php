<?php

	//if uninstall not called from WordPress exit
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
		exit();
		global $wpdb;
		$sql = "DELETE FROM {$wpdb->prefix}options WHERE `option_name` LIKE \"mo2f_%\"";
        $wpdb->query( $sql );
	$value = get_option('mo_wpns_registration_status');
	if( isset( $value ) || !empty( $value ) ) {	
		delete_option('mo2f_email');
	}
	update_option('mo2f_activate_plugin', 1);

	
	delete_option('mo_wpns_transactionId');
	delete_option('mo_wpns_registration_status');
    delete_option('mo_wpns_customer_token');
	
	delete_option('mo_wpns_message');
	delete_option('mo_wpns_transactionId');
	delete_option('mo_wpns_registration_status');
	
	delete_site_option('EmailTransactionCurrent');

	delete_site_option('mo2fa_free_plan_new_user_methods');
	delete_site_option('mo2fa_free_plan_existing_user_methods');

	delete_option('mo2fa_reconfiguration_via_email');
	delete_option('mo2fa_userProfile_method');
	
	delete_option('mo_wpns_enable_brute_force');
	delete_option('mo_wpns_show_remaining_attempts');
	delete_option('mo_wpns_enable_ip_blocked_email_to_admin');
	delete_option('mo_wpns_enable_unusual_activity_email_to_user');

	delete_option( 'mo_wpns_company');
	delete_option( 'mo_wpns_firstName' );
 	delete_option( 'mo_wpns_lastName');
 	delete_option( 'mo_wpns_password');
 
 	delete_option( 'mo_wpns_admin_phone');
 	

 	delete_option( 'mo_wpns_registration_status');
 	delete_option( 'mo_wpns_block_chrome');
 	delete_option( 'mo_wpns_block_firefox');
 	delete_option( 'mo_wpns_block_ie');
	delete_option( 'mo_wpns_block_safari');
	delete_option( 'mo_wpns_block_opera');
	delete_option( 'mo_wpns_block_edge');
	delete_site_option(base64_encode("totalUsersCloud"));
	
	delete_option('mo_2factor_user_registration_status');
			
	delete_option( 'mo_2f_switch_all');
	
	delete_option( 'mo_wpns_scan_initialize');
	
	delete_site_option( 'mo_2fa_plan_type');
	delete_site_option( 'mo_2fa_addon_plan_type');
	
	delete_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z');
	
	delete_option( 'mo_wpns_enable_htaccess_blocking');
	delete_option( 'mo_wpns_enable_user_agent_blocking');
	delete_option( 'mo_wpns_countrycodes');
	delete_option( 'mo_wpns_referrers');
	delete_option( 'protect_wp_config');
	delete_option( 'prevent_directory_browsing');
	delete_option( 'disable_file_editing');
	delete_option( 'mo_wpns_enable_comment_spam_blocking');
	delete_option( 'mo_wpns_enable_comment_recaptcha');

	delete_option('mo_wpns_2fa_with_network_security');
	delete_option('mo_wpns_2fa_with_network_security_popup_visible');

	delete_option( 'mo_wpns_slow_down_attacks');
	

 	delete_option( 'mo_wpns_enable_2fa');
 	
	delete_option( 'mo_wpns_activate_recaptcha');

	delete_option( 'mo_wpns_activate_recaptcha_for_login');
	delete_option( 'mo_wpns_activate_recaptcha_for_registration');
	delete_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login');
	delete_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration');
	delete_option( 'mo_wpns_recaptcha_site_key');
 	delete_option( 'mo_wpns_recaptcha_secret_key');

 	delete_option('custom_user_template');
 	delete_option('custom_admin_template');
 	delete_option( 'mo_wpns_enable_fake_domain_blocking');
 	delete_option( 'mo_wpns_enable_advanced_user_verification');
 	delete_option('mo_customer_validation_wp_default_enable');
 	delete_option( 'mo_wpns_enable_social_integration');

 	delete_option( 'mo_wpns_scan_plugins');
 	delete_option( 'mo_wpns_scan_themes');
 	delete_option( 'mo_wpns_check_vulnerable_code');
 	delete_option( 'mo_wpns_check_sql_injection');
 	delete_option( 'mo_wpns_scan_wp_files');
 	delete_option( 'mo_wpns_skip_folders');
 	delete_option( 'mo_wpns_check_external_link');
 	delete_option( 'mo_wpns_scan_files_with_repo');
 	delete_option('mo_wpns_files_scanned');
	delete_option('mo_wpns_infected_files');

 	delete_option('mo_wpns_dbversion');
	
	delete_site_option('mo2fa_superadmin');
	
    delete_site_option('duo_credentials_save_successfully');

 	if(get_option('is_onprem'))
	{
		$users = get_users( array() );
		foreach ( $users as $user ) {
		delete_user_meta( $user->ID, 'currentMethod' );
		delete_user_meta( $user->ID, 'email' );
		delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure');
		delete_user_meta( $user->ID, 'Security Questions');
		delete_user_meta( $user->ID, 'Email Verification');
		delete_user_meta( $user->ID, 'mo2f_kba_challenge');
		delete_user_meta( $user->ID, 'mo2f_2FA_method_to_test');
		delete_user_meta( $user->ID, 'kba_questions_user');
		delete_user_meta( $user->ID, 'Google Authenticator');
		delete_user_meta( $user->ID, 'mo2f_gauth_key');
		delete_user_meta( $user->ID, 'mo2f_get_auth_rnd_string');	
		}
	}

 	$users = get_users( array() );
	foreach ( $users as $user ) {
		delete_user_meta( $user->ID, 'phone_verification_status' );
		delete_user_meta( $user->ID, 'test_2FA' );
		delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );
		delete_user_meta( $user->ID, 'configure_2FA' );
		delete_user_meta( $user->ID, 'mo2f_2FA_method_to_test' );
		delete_user_meta( $user->ID, 'mo2f_phone' );
		delete_user_meta( $user->ID, 'mo_2factor_user_registration_status' );
		delete_user_meta( $user->ID, 'mo2f_external_app_type' );
		delete_user_meta( $user->ID, 'mo2f_user_login_attempts' );
		delete_user_meta( $user->ID, 'mo2f_transactionId');
		delete_user_meta( $user->ID, 'mo2f_user_phone');
		delete_user_meta( $user->ID, 'miniorageqr');		
		delete_user_meta( $user->ID, 'mo2f_google_auth');			
		delete_user_meta( $user->ID, 'mo2f_email_miniOrange');		
		delete_user_meta( $user->ID, 'mo2f_kba_challenge');
		delete_user_meta( $user->ID, 'mo2f_otp_email_code');
		delete_user_meta( $user->ID, 'mo2f_otp_email_time');
		delete_user_meta( $user->ID, 'tempRegEmail');
		delete_user_meta( $user->ID, 'mo2f_EV_txid');
		delete_user_meta( $user->ID, 'mo_backup_code_generated' );
		delete_user_meta( $user->ID, 'mo_backup_code_downloaded' );
		delete_user_meta( $user->ID, 'mo2f_backup_codes' );
		delete_user_meta( $user->ID, 'mo_backup_code_screen_shown' );
		delete_user_meta($user->ID,'user_not_enroll');
	}
 	
	//drop custom db tables
	if(!class_exists('WPSecurityPro'))
	{
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_transactions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_blocked_ips" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_whitelisted_ips" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_email_sent_audit" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_scan_report" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_scan_report_details" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_skip_files" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_hash_file" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_attack_logs" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_ip_rate_details" );
	}

	// Remove all values of 2FA on deactivate
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mo2f_user_details" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mo2f_user_login_info" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mo2f_network_blocked_ips" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mo2f_network_email_sent_audit" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mo2f_network_transactions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mo2f_network_whitelisted_ips" );
	delete_option( 'mo2f_dbversion' );
	delete_site_option("mo_2fa_pnp");

	if ( ! is_multisite() )
	{
	
		delete_option( 'user_phone' );
	
		delete_option( 'mo_2factor_admin_registration_status' );
		
		delete_option( 'mo_2f_login_type_enabled' );
		
		delete_option( 'mo2_admin_last_name' );
	
		delete_option( 'kba_questions' );
	
		delete_option( 'mo2_admin_last_name' );
	
		


	
		delete_option( 'SQLInjection' );
		delete_site_option( 'WAF');
		delete_site_option( 'WAFEnabled' );
		delete_option( 'XSSAttack' );
		delete_option( 'RFIAttack' );
		delete_option( 'LFIAttack' );
		delete_option( 'RCEAttack' );
		delete_option( 'actionRateL' );
		delete_option( 'Rate_limiting' );
		delete_option( 'Rate_request' );
		delete_option( 'limitAttack' );
		delete_option( 'skip_tour' );
		delete_option( 'mo_wpns_new_registration' );
		

	
		
		delete_option( 'mo_wpns_enable_log_requests' );
		
		delete_option( 'mo_wpns_scan_files_extensions' );
		delete_option( 'donot_show_feedback_message' );
		delete_option( 'mo_wpns_enable_rename_login_url' );
		delete_option( 'login_page_url' );
		delete_option( 'mo_wpns_scan_mode' );
		delete_option( 'mo_wpns_malware_scan_in_progress' );
		delete_option( 'scan_failed' );
		delete_option( 'recovery_mode_email_last_sent' );
		
		
		
		//delete all stored key-value pairs for the roles
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		foreach ( $wp_roles->role_names as $id => $name ) {
			delete_option( 'mo2fa_' . $id );
			delete_option( 'mo2fa_' . $id . '_login_url' );
		}
	}
	//delete previous version key-value pairs
	delete_option( 'mo_2factor_admin_mobile_registration_status' );
	delete_option( 'mo_2factor_registration_status' );
	delete_option( 'mo_2factor_temp_status' );
	
	delete_option( 'mo_2factor_login_status' );
	
	delete_option( 'kba_questions' );
	

	delete_option( 'mo_2f_switch_waf');
	delete_option( 'mo_2f_switch_loginspam');
	delete_option( 'mo_2f_switch_malware');
	delete_option( 'mo_2f_switch_adv_block');

	delete_option( 'mo_wpns_last_themes');
	delete_option( 'mo_wpns_last_plugins');
	delete_option( 'mo_wpns_last_scan_time');
	delete_option( 'infected_dismiss');
	delete_option( 'weekly_dismiss');
	delete_option( 'donot_show_infected_file_notice');
	delete_option( 'donot_show_new_plugin_theme_notice');
	delete_option( 'donot_show_weekly_scan_notice');
	
?>
