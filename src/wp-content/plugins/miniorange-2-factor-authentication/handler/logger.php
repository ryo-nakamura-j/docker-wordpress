<?php

class mo2f_Logger
{
	function __construct()
	{
		add_action( 'log_403' , array( $this, 'log_403' ) );
		add_action( 'template_redirect', array( $this, 'log_404' ) );
	}	


	function log_403()
	{
		global $moWpnsUtility;
			$mo_wpns_config = new MoWpnsHandler();
			$userIp 		= $moWpnsUtility->get_client_ip();
			$userIp = sanitize_text_field( $userIp );
			$url			= $moWpnsUtility->get_current_url();
			$user  			= wp_get_current_user();
			$username		= is_user_logged_in() ? $user->user_login : 'GUEST';
			$mo_wpns_config->add_transactions($userIp,$username,MoWpnsConstants::ERR_403, MoWpnsConstants::ACCESS_DENIED,$url);
	}

	function log_404()
	{
		global $moWpnsUtility;

		if(!is_404())
			return;
			$mo_wpns_config = new MoWpnsHandler();
			$userIp 		= $moWpnsUtility->get_client_ip();
			$userIp = sanitize_text_field( $userIp );
			$url			= $moWpnsUtility->get_current_url();
			$user  			= wp_get_current_user();
			$username		= is_user_logged_in() ? $user->user_login : 'GUEST';
			$mo_wpns_config->add_transactions($userIp,$username,MoWpnsConstants::ERR_404, MoWpnsConstants::ACCESS_DENIED,$url);
	}
}
new mo2f_Logger;