<?php
class Mo_2fa_security_features
{
	function wpns_2fa_features_only()
	{
		update_option( 'mo_wpns_2fa_with_network_security', 0);
		update_option( 'mo_wpns_2fa_with_network_security_popup_visible', 0);
		?><script>window.location.href="admin.php?page=mo_2fa_two_fa";</script><?php

	}

	function wpns_2fa_with_network_security($postvalue)
	{
		$nonce= sanitize_text_field(wp_unslash($_POST['mo_security_features_nonce']));

		if ( wp_verify_nonce( $nonce, 'mo_2fa_security_features_nonce' ) )
		{
			$enable_newtwork_security_features = isset($postvalue['mo_wpns_2fa_with_network_security']) ? true : false;

			update_option( 'mo_wpns_2fa_with_network_security', $enable_newtwork_security_features);

			if ($enable_newtwork_security_features) 
			{	
				$mo2f_enable_all_enable = new Mo2f_ajax_dashboard();
				$mo2f_enable_all_enable -> mo2f_handle_all_enable(1);
			}



			update_option( 'mo_wpns_2fa_with_network_security_popup_visible', 0);
			if(MoWpnsUtility::get_mo2f_db_option('mo_wpns_2fa_with_network_security', 'get_option'))
			{
				?><script>window.location.href="admin.php?page=mo_2fa_dashboard";</script><?php
			}
			else
			{
				?><script>window.location.href="admin.php?page=mo_2fa_two_fa";</script><?php
			}

		
		}
		else
				do_action('wpns_show_message',MoWpnsMessages::showMessage('NONCE_ERROR'),'ERROR');

	}
}new Mo_2fa_security_features;
?>
