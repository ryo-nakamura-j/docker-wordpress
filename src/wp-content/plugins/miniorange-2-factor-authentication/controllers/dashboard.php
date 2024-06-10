<?php



    //all the variables and links
	$wpns_database = new MoWpnsDB;
	$wpns_count_ips_blocked = $wpns_database->get_count_of_blocked_ips();
	$wpns_count_ips_whitelisted = $wpns_database->get_number_of_whitelisted_ips();
	$wpns_attacks_blocked = $wpns_database->get_count_of_attacks_blocked();

	$mo_wpns_handler 	= new MoWpnsHandler();
	$sqlC 			= $mo_wpns_handler->get_blocked_attacks_count("SQL");
	$rceC 			= $mo_wpns_handler->get_blocked_attacks_count("RCE");
	$rfiC 			= $mo_wpns_handler->get_blocked_attacks_count("RFI");
	$lfiC 			= $mo_wpns_handler->get_blocked_attacks_count("LFI");
	$xssC 			= $mo_wpns_handler->get_blocked_attacks_count("XSS");
	$totalAttacks	= $sqlC+$lfiC+$rfiC+$xssC+$rceC;
	$total_malicious=$wpns_database->count_malicious_files();
	if($total_malicious > 999){
		$total_malicious=($total_malicious/1000);
		$total_malicious= round($total_malicious,1)."k";
	}
    include_once $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'dashboard.php';