<?php
	
	global $moWpnsUtility,$mo2f_dirName;

	if(isset($_POST['option']) and sanitize_text_field($_POST['option'])=='mo_wpns_manual_clear'){
		global $wpdb;
		$wpdb->query("DELETE FROM ".$wpdb->prefix."mo2f_network_transactions WHERE Status='success' or Status= 'pastfailed' or Status='failed' ");

	}



	if(isset($_POST['option']) and sanitize_text_field($_POST['option'])=='mo_wpns_manual_errorclear'){
		global $wpdb;
		$wpdb->query("DELETE FROM ".$wpdb->prefix."mo2f_network_transactions WHERE Status='accessDenied'");

	}

	$mo_wpns_handler   = new MoWpnsHandler();
	$logintranscations = $mo_wpns_handler->get_login_transaction_report();
	$errortranscations = $mo_wpns_handler->get_error_transaction_report();

	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'reports.php';

?>