<?php
	
	global $moWpnsUtility,$mo2f_dirName;

	if(current_user_can( 'manage_options' )  && isset($_POST['option']) )
	{
		switch(sanitize_text_field($_POST['option']))
		{
			case "mo_wpns_block_ip_range":
				wpns_handle_range_blocking($_POST);												break;
			case "mo_wpns_browser_blocking":
				wpns_handle_browser_blocking($_POST);											break;
			case "mo_wpns_enable_htaccess_blocking":
				wpns_handle_htaccess_blocking($_POST);											break;
			case "mo_wpns_enable_user_agent_blocking":
				wpns_handle_user_agent_blocking($_POST);										break;
			case "mo_wpns_block_countries":
				wpns_handle_country_block($_POST);											    break;
			case "mo_wpns_block_referrer":
				wpns_handle_block_referrer($_POST);												break;
			
		}
	}

	$range_count 	= is_numeric(get_option('mo_wpns_iprange_count'))
					&& intval(get_option('mo_wpns_iprange_count')) !=0  ? intval(get_option('mo_wpns_iprange_count')) : 1;
	$htaccess_block = get_option('mo_wpns_enable_htaccess_blocking')   	? "checked" : "";
	$user_agent 	= get_option('mo_wpns_enable_user_agent_blocking') 	? "checked" : "";
	$block_chrome 	= get_option('mo_wpns_block_chrome') 			   	? "checked" : "";
	$block_ie 		= get_option('mo_wpns_block_ie')			   	   	? "checked" : "";
	$block_firefox 	= get_option('mo_wpns_block_firefox') 			   	? "checked" : "";
	$block_safari	= get_option('mo_wpns_block_safari') 			   	? "checked" : "";
	$block_opera	= get_option('mo_wpns_block_opera') 			   	? "checked" : "";
	$block_edge		= get_option('mo_wpns_block_edge') 			   	   	? "checked" : "";
	$country		= MoWpnsConstants::$country;
	$codes			= get_option( "mo_wpns_countrycodes");
	$referrers 		= get_option( 'mo_wpns_referrers');
	$referrers 		= explode(";",$referrers);
	$current_browser= $moWpnsUtility->getCurrentBrowser();
	$start = array();
	$end   = array();
	for($i = 1 ; $i <= $range_count ; $i++){
		$ip_range = get_option("mo_wpns_iprange_range_".$i);
		if($ip_range){
			$a = explode('-', $ip_range);

			$start[$i] = $a[0];
			$end[$i] = $a[1]; 
		}

	}
	if(!isset($start[1])){
		$start[1] = '';
	}
	if(!isset($end[1])){
		$end[1] = '';
	}

	switch($current_browser)
	{
		case "chrome":
			$block_chrome = 'disabled';		break;
		case "ie":
			$block_ie 	  = 'disabled';		break;
		case "firefox":
			$block_firefox= 'disabled';		break;
		case "safari":
			$block_safari = 'disabled';		break;
		case "edge":
			$block_edge	  = 'disabled';		break;
		case "opera":
			$block_opera  = 'disabled';		break;	
	}

	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'advanced-blocking.php';


	/* ADVANCD BLOCKING FUNCTIONS */

	//Function to save range of ips
	function wpns_handle_range_blocking($postedValue)
	{
		
		$flag=0;
		$max_allowed_ranges = 100;
		$added_mappings_ranges  = 0 ;
		for($i=1;$i<=$max_allowed_ranges;$i++){
			if(isset($postedValue['start_'.$i]) && isset($postedValue['end_'.$i]) && !empty($postedValue['start_'.$i]) && !empty($postedValue['end_'.$i])){

				$postedValue['start_'.$i] = sanitize_text_field($postedValue['start_'.$i]);
				$postedValue['end_'.$i]   = sanitize_text_field($postedValue['end_'.$i]);


				if(filter_var($postedValue['start_'.$i] , FILTER_VALIDATE_IP )  &&  filter_var($postedValue['end_'.$i] , FILTER_VALIDATE_IP ) && (ip2long($postedValue['end_'.$i]) > ip2long($postedValue['start_'.$i])) ){
					$range = '';
					$range = sanitize_text_field($postedValue['start_'.$i]);
					$range .= '-';
					$range .=  sanitize_text_field($postedValue['end_'.$i]);
					$added_mappings_ranges++;
					update_option( 'mo_wpns_iprange_range_'.$added_mappings_ranges, $range );

				}
				else{
					$flag = 1;
					do_action('wpns_show_message',MoWpnsMessages::showMessage('INVALID_IP'),'ERROR');
					return;
				}	
			}
        }
        

		if($added_mappings_ranges==0)
			update_option( 'mo_wpns_iprange_range_1','');
		update_option( 'mo_wpns_iprange_count', $added_mappings_ranges);
		if($flag == 0){
			do_action('wpns_show_message',MoWpnsMessages::showMessage('IP_PERMANENTLY_BLOCKED'),'SUCCESS');
		}		
	}

	//Function to handle browser blocking
	function wpns_handle_browser_blocking($postedValue)
	{
		isset($postedValue['mo_wpns_block_chrome'])		? update_option( 'mo_wpns_block_chrome' 	,  sanitize_text_field($postedValue['mo_wpns_block_chrome'] ))	: update_option( 'mo_wpns_block_chrome'  ,  false );
		isset($postedValue['mo_wpns_block_firefox'])	? update_option( 'mo_wpns_block_firefox' 	,  sanitize_text_field($postedValue['mo_wpns_block_firefox'] ))	: update_option( 'mo_wpns_block_firefox' ,  false );
		isset($postedValue['mo_wpns_block_ie'])			? update_option( 'mo_wpns_block_ie' 		,  sanitize_text_field($postedValue['mo_wpns_block_ie'] ))		: update_option( 'mo_wpns_block_ie' 	 ,  false );
		isset($postedValue['mo_wpns_block_safari'])		? update_option( 'mo_wpns_block_safari' 	,  sanitize_text_field($postedValue['mo_wpns_block_safari'] ))	: update_option( 'mo_wpns_block_safari'  ,  false );
		isset($postedValue['mo_wpns_block_opera'])		? update_option( 'mo_wpns_block_opera' 		,  sanitize_text_field($postedValue['mo_wpns_block_opera'] ))	: update_option( 'mo_wpns_block_opera' 	 ,  false );
		isset($postedValue['mo_wpns_block_edge'])		? update_option( 'mo_wpns_block_edge' 		,  sanitize_text_field($postedValue['mo_wpns_block_edge'] )	)	: update_option( 'mo_wpns_block_edge' 	 ,  false );
		do_action('wpns_show_message',MoWpnsMessages::showMessage('CONFIG_SAVED'),'SUCCESS');
	}


	//Function to handle Htaccess blocking
	function wpns_handle_htaccess_blocking($postdata)
	{
		$htaccess = isset($postdata['mo_wpns_enable_htaccess_blocking']) ? true : false;
		update_option( 'mo_wpns_enable_htaccess_blocking',  $htaccess);
		$mo_wpns_config = new MoWpnsHandler();
		if($htaccess)
		{
			$mo_wpns_config->add_htaccess_ips();
			do_action('wpns_show_message',MoWpnsMessages::showMessage('HTACCESS_ENABLED'),'SUCCESS');
		}
		else 
		{
			$mo_wpns_config->remove_htaccess_ips();
			do_action('wpns_show_message',MoWpnsMessages::showMessage('HTACCESS_DISABLED'),'ERROR');
		}
	}


	//Function to handle user agent blocking
	function wpns_handle_user_agent_blocking($postvalue)
	{
		$user_agent = isset($postvalue['mo_wpns_enable_user_agent_blocking']) ? true : false;
		update_option( 'mo_wpns_enable_user_agent_blocking',  $user_agent);
		if($user_agent)
			do_action('wpns_show_message',MoWpnsMessages::showMessage('USER_AGENT_BLOCK_ENABLED'),'SUCCESS');
		else
			do_action('wpns_show_message',MoWpnsMessages::showMessage('USER_AGENT_BLOCK_DISABLED'),'ERROR');
	}


	//Function to handle country block
	function wpns_handle_country_block($post)
	{
		$countrycodes = "";
		foreach($post as $countrycode=>$value){
			if($countrycode!="option")
				$countrycodes .= $countrycode.";";
		}
		update_option( 'mo_wpns_countrycodes', $countrycodes);
		do_action('wpns_show_message',MoWpnsMessages::showMessage('CONFIG_SAVED'),'SUCCESS');
	}


	//Function to handle block referrer
	function wpns_handle_block_referrer($post)
	{
		
		$referrers = "";
		foreach($post as $key => $value)
		{
			if(strpos($key, 'referrer_') !== false)
				if(!empty($value))
					$referrers .= sanitize_url($value).";";
		}
		update_option( 'mo_wpns_referrers', $referrers);
	}
