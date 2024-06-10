<?php
class wpns_ajax
{
	function __construct(){
		//add comment here
		add_action( 'admin_init'  , array( $this, 'mo_login_security_ajax' ) );
		add_action('init', array( $this, 'mo2fa_elementor_ajax_fun' ));
	}

	function mo_login_security_ajax(){
		 
		add_action( 'wp_ajax_wpns_login_security', array($this,'wpns_login_security') );
		add_action( 'wp_ajax_mo2f_ajax', array($this,'mo2f_ajax') );
		add_action( 'wp_ajax_nopriv_mo2f_ajax', array($this,'mo2f_ajax') );
	}

	function mo2f_ajax(){
		$GLOBALS['mo2f_is_ajax_request'] = true;
		switch (sanitize_text_field(wp_unslash($_POST['mo2f_ajax_option']))) {
				case "mo2f_ajax_kba":
					$this->mo2f_ajax_kba();break;
				case "mo2f_ajax_login":
					$this->mo2f_ajax_login(); break;
				case "mo2f_ajax_otp":
					$this->mo2f_ajax_otp(); break;
		}
	}
	 function mo2fa_elementor_ajax_fun()
    	{
		   		
    		 if (isset( $_POST['miniorange_elementor_login_nonce'])){
	    		$nonce = sanitize_text_field($_POST['miniorange_elementor_login_nonce']);
		   		if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-login-nonce' ) ){
		   			wp_send_json('ERROR');
		   		   		}
	       		 if(isset($_POST['mo2fa_elementor_user_password']) && !empty($_POST['mo2fa_elementor_user_password']) && isset($_POST['mo2fa_elementor_user_name']))
	        	{
	        		$info = array();
	            	$info['user_login'] = sanitize_text_field($_POST['mo2fa_elementor_user_name']);
	            	$info['user_password'] = $_POST['mo2fa_elementor_user_password'];
	            	$info['remember'] = false;
	            	$user_signon = wp_signon($info, false);
	            	if (is_wp_error($user_signon)) {
	                wp_send_json(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
	           		}  		
	        	}
	        }
    	}
		function wpns_login_security(){
			switch(sanitize_text_field(wp_unslash($_POST['wpns_loginsecurity_ajax'])))
			{
				case "wpns_bruteforce_form":
					$this->wpns_handle_bf_configuration_form();	break;
				case "wpns_save_captcha":
					$this->wpns_captcha_settings();break;
				case "save_strong_password":
					$this->wpns_strong_password_settings();break;
					case 'wpns_ManualIPBlock_form':
					$this->wpns_handle_IP_blocking();break;
				case 'wpns_WhitelistIP_form':
					$this->wpns_whitelist_ip(); break;
				case 'wpns_waf_settings_form':
					$this->wpns_waf_settings_form(); break;
				case 'wpns_waf_rate_limiting_form':
					$this->wpns_waf_rate_limiting_form(); break;	
				case 'wpns_waf_realtime_ip_block_free':
					$this->wpns_waf_realtime_ip_block_free();break;
				case 'wpns_ip_lookup':
					$this->wpns_ip_lookup(); 	break;	
				case 'wpns_all_plans':
					$this->wpns_all_plans(); 	break;	
				case 'wpns_logout_form':
					$this->wpns_logout_form();	break;
				case 'wpns_check_transaction':
					$this->wpns_check_transaction(); break;
				case 'waf_settings_mail_form_notify':
					$this->waf_settings_mail_form_notify();	break;
				case 'waf_settings_IP_mail_form':
						$this->waf_settings_IP_mail_form();break;
				case 'update_plan':
					$this->update_plan();		break;
			}
		}

		function update_plan(){
			$mo2f_all_plannames = sanitize_text_field($_POST['planname']);
			$mo_2fa_plan_type	= sanitize_text_field($_POST['planType']);
				update_site_option('mo2f_planname', $mo2f_all_plannames);
			if ($mo2f_all_plannames == 'addon_plan') 
			{
				update_site_option('mo2f_planname', 'addon_plan');
				update_site_option('mo_2fa_addon_plan_type',$mo_2fa_plan_type);
			}
			elseif ($mo2f_all_plannames == '2fa_plan') 
			{
				update_site_option('mo2f_planname', '2fa_plan');
				update_site_option('mo_2fa_plan_type',$mo_2fa_plan_type);
			}	
		}


		function mo2f_ajax_otp(){
			$obj = new Miniorange_Password_2Factor_Login();
			$obj->check_miniorange_soft_token($_POST);	
		}
		function mo2f_ajax_kba(){
			$obj = new Miniorange_Password_2Factor_Login();
			$obj->check_kba_validation($_POST);			
		}

		function wpns_check_transaction()
		{
			$customerT = new Customer_Cloud_Setup();
			$content = json_decode( $customerT->get_customer_transactions( get_option( 'mo2f_customerKey' ), get_option('mo2f_api_key'),'WP_OTP_VERIFICATION_PLUGIN)' ), true );

			if($content['status'] == 'SUCCESS')
			{
				update_site_option('mo2f_license_type','PREMIUM');
			}
			else
			{
				update_site_option('mo2f_license_type','DEMO');
				$content = json_decode( $customerT->get_customer_transactions( get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ),'DEMO' ), true );
			}
			if(isset($content['smsRemaining']))
				update_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z',$content['smsRemaining']);
			else if($content['status'] =='SUCCESS')
				update_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z',0);


			if(isset($content['emailRemaining']))
			{
				$available_transaction = get_site_option('EmailTransactionCurrent', 30);
				if($content['emailRemaining']>$available_transaction and $content['emailRemaining']>10)
				{
					$currentTransaction = $content['emailRemaining']+get_site_option('cmVtYWluaW5nT1RQ');
					if($available_transaction>30)
						$currentTransaction = $currentTransaction-$available_transaction;
					
					update_site_option('cmVtYWluaW5nT1RQ',$currentTransaction);
					update_site_option('EmailTransactionCurrent',$content['emailRemaining']);
				}
				
			}
			
		}

		function mo2f_ajax_login()
		{	
			if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']),'miniorange-2-factor-login-nonce'))
			{
				wp_send_json("ERROR");
				exit;
			}
			else
			{
				$username = sanitize_text_field($_POST['username']);
				$password = sanitize_text_field($_POST['password'] );
				apply_filters( 'authenticate', null, $username, $password );
			}
		}
			function wpns_logout_form()
		{
			global $moWpnsUtility;
			if( !$moWpnsUtility->check_empty_or_null( get_option('mo_wpns_registration_status') ) ) {
				delete_option('mo2f_email');
			}
			delete_option('mo2f_customerKey');
			delete_option('mo2f_api_key');
			delete_option('mo2f_customer_token');
			delete_option('mo_wpns_transactionId');
			delete_site_option('EmailTransactionCurrent');
			delete_option('mo_wpns_registration_status');
			delete_option( 'mo_2factor_admin_registration_status' );

			$two_fa_settings = new Miniorange_Authentication();
            $two_fa_settings->mo_auth_deactivate();

		}

		function waf_settings_mail_form_notify()
		{
			
			$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'WAFsettingNonce' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}
	   		$mo2f_all_mail_noyifying = '';
	   		if(isset($_POST['S_mail']))
	   		{
	   		$mo2f_all_mail_noyifying = sanitize_text_field(($_POST['S_mail']));
			update_site_option('mo2f_mail_notify_new_release', $mo2f_all_mail_noyifying);
				wp_send_json('true');
	   		}
			else{
			    update_site_option('mo2f_mail_notify_new_release', $mo2f_all_mail_noyifying);
				wp_send_json('false');

			} 
		}
		function waf_settings_IP_mail_form()
		{
			$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'WAFsettingNonce' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}

			$mo2f_mail_noyifying_IP = '';
	   		if(isset($_POST['Smail']))
	   		{
	   		$mo2f_mail_noyifying_IP = sanitize_text_field(($_POST['Smail']));
			update_site_option('mo2f_mail_notify', $mo2f_mail_noyifying_IP);
				wp_send_json('true');
	   		}
			else{
			    update_site_option('mo2f_mail_notify', $mo2f_mail_noyifying_IP);
				wp_send_json('false');

			} 
		}
		function wpns_all_plans()
		{
			$mo2f_all_plannames = sanitize_text_field($_POST['planname']);
			$mo_2fa_plan_type	= sanitize_text_field($_POST['planType']);
				update_site_option('mo2f_planname', $mo2f_all_plannames);
			if ($mo2f_all_plannames == 'addon_plan') 
			{
				update_site_option('mo2f_planname', 'addon_plan');
				update_site_option('mo_2fa_addon_plan_type',$mo_2fa_plan_type);
			}
			elseif ($mo2f_all_plannames == '2fa_plan') 
			{
				update_site_option('mo2f_planname', '2fa_plan');
				update_site_option('mo_2fa_plan_type',$mo_2fa_plan_type);
			}	
		}
	    function wpns_handle_bf_configuration_form(){

	   		$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'wpns-brute-force' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}
	   		$brute_force        =sanitize_text_field($_POST['bf_enabled/disabled']);
	  		if($brute_force == 'true'){$brute_force = "on";}else if($brute_force == 'false') {$brute_force = "";}  
			$login_attempts 	= sanitize_text_field($_POST['allwed_login_attempts']);
			$blocking_type  	= sanitize_text_field($_POST['time_of_blocking_type']);
			$blocking_value 	= isset($_POST['time_of_blocking_val'])	 ? sanitize_text_field($_POST['time_of_blocking_val'])	: false;
			$show_login_attempts= sanitize_text_field($_POST['show_remaining_attempts']);
			if($show_login_attempts == 'true'){$show_login_attempts = "on";} else if($show_login_attempts == 'false') { $show_login_attempts = "";}
			if($brute_force == 'on' && $login_attempts == "" ){
				wp_send_json('empty');
				return;
			}
	  		update_option( 'mo2f_enable_brute_force' 		, $brute_force 		  	  );
			update_option( 'mo2f_allwed_login_attempts'		, $login_attempts 		  );
			update_option( 'mo_wpns_time_of_blocking_type'	, $blocking_type 		  );
			update_option( 'mo_wpns_time_of_blocking_val' 	, $blocking_value   	  );
			update_option('mo2f_show_remaining_attempts' 	, $show_login_attempts    );
			if($brute_force == "on"){
				update_site_option('bruteforce_notification_option',1);
				wp_send_json('true');
			}
			else if($brute_force == ""){
				wp_send_json('false');
			} 
			
		}
	function wpns_handle_IP_blocking()
	{
		
	
		global $mo2f_dirName;	
		if(!wp_verify_nonce($_POST['nonce'],'manualIPBlockingNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{	
			
			include_once($mo2f_dirName.'controllers'.DIRECTORY_SEPARATOR.'ip-blocking.php');
		}

	}
	function wpns_whitelist_ip()
	{
		global $mo2f_dirName;
		if(!wp_verify_nonce($_POST['nonce'],'IPWhiteListingNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{
			include_once($mo2f_dirName.'controllers'.DIRECTORY_SEPARATOR.'ip-blocking.php');
		}
	}
	
	function wpns_ip_lookup()
	{

		

		if(!wp_verify_nonce($_POST['nonce'],'IPLookUPNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{


			$ip  = sanitize_text_field($_POST['IP']);
	        if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/',$ip))
			{
				echo("INVALID_IP_FORMAT");
				exit;
			}
			else if(! filter_var($ip, FILTER_VALIDATE_IP)){
				echo("INVALID_IP");
				exit;
			}
	        $result=wp_remote_get("http://www.geoplugin.net/json.gp?ip=".$ip);

			
			

			if( !is_wp_error( $result ) ) {
				$result=json_decode(wp_remote_retrieve_body( $result), true);
			}


			try{
	            $timeoffset	= timezone_offset_get(new DateTimeZone($result["geoplugin_timezone"]),new DateTime('now'));
	            $timeoffset = $timeoffset/3600;

	        }catch(Exception $e){
	            $result["geoplugin_timezone"]="";
	            $timeoffset="";
	        }
			$ipLookUpTemplate  = MoWpnsConstants::IP_LOOKUP_TEMPLATE;
			if($result['geoplugin_request']==$ip) {

	            $ipLookUpTemplate = str_replace("{{status}}", $result["geoplugin_status"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{ip}}", $result["geoplugin_request"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{region}}", $result["geoplugin_region"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{country}}", $result["geoplugin_countryName"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{city}}", $result["geoplugin_city"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{continent}}", $result["geoplugin_continentName"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{latitude}}", $result["geoplugin_latitude"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{longitude}}", $result["geoplugin_longitude"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{timezone}}", $result["geoplugin_timezone"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{curreny_code}}", $result["geoplugin_currencyCode"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{curreny_symbol}}", $result["geoplugin_currencySymbol"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{per_dollar_value}}", $result["geoplugin_currencyConverter"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{hostname}}", $hostname, $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{offset}}", $timeoffset, $ipLookUpTemplate);

	            $result['ipDetails'] = $ipLookUpTemplate;
	        }else{
	            $result["ipDetails"]["status"]="ERROR";
	        }
	      	wp_send_json( $result );
		}
	}
	function wpns_waf_settings_form()
	{
		$dir_name =  dirname(__FILE__);
		$dir_name1 = explode('wp-content', $dir_name);
		$dir_name = $dir_name1[0];
		$filepath = str_replace('\\', '/', $dir_name1[0]);
		$fileName = $filepath.'/wp-includes/mo-waf-config.php';
		
		if(!file_exists($fileName))
		{
			$file = fopen($fileName, "a+");
			$string = "<?php".PHP_EOL;
			$string .= '$SQL=1;'.PHP_EOL;
			$string .= '$XSS=1;'.PHP_EOL;
			$string .= '$RCE=0;'.PHP_EOL;
			$string .= '$LFI=0;'.PHP_EOL;
			$string .= '$RFI=0;'.PHP_EOL;
			$string .= '$RateLimiting=1;'.PHP_EOL;
			$string .= '$RequestsPMin=120;'.PHP_EOL;
			$string .= '$actionRateL="ThrottleIP";'.PHP_EOL;
			$string .= '?>'.PHP_EOL;
			
			fwrite($file, $string);
			fclose($file);
		}
		else
		{
			if(!is_writable($fileName) or !is_readable($fileName))
			{
				echo "FilePermissionDenied";
				exit;
			}
		}
		
		if(!wp_verify_nonce($_POST['nonce'],'WAFsettingNonce'))
		{
			var_dump("NonceDidNotMatch");
			exit;
		}
		else
		{
			switch (sanitize_text_field(wp_unslash($_POST['optionValue']))) {
				case "SQL": 
					$this->savesql();			break;
				case "XSS": 
					$this->savexss();			break;
				case "RCE": 
					$this->saverce();			break;
				case "RFI": 
					$this->saverfi();			break;
				case "LFI": 
					$this->savelfi();			break;
				case "WAF": 
					$this->saveWAF();			break;
				case "HWAF": 
					$this->saveHWAF();			break;
				case "backupHtaccess":
					$this->backupHtaccess();	break;
				case "limitAttack":
					$this->limitAttack();		break;
				default:
					break;
			}
				
		}	

	}
	function wpns_waf_realtime_ip_block_free()
	{
		$nonce = sanitize_text_field($_POST['nonce']);
		if(!wp_verify_nonce($nonce,'mo2f_realtime_ip_block_free'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{	
			$mo2f_realtime_ip_block_free = sanitize_text_field($_POST['mo2f_realtime_ip_block_free']);
			
			if($mo2f_realtime_ip_block_free == 'on')
			{
				update_site_option('mo2f_realtime_ip_block_free',1);
				if (!wp_next_scheduled( 'mo2f_realtime_ip_block_free_hook')) {
             		wp_schedule_event( time(), 'mo2f_realtime_ipblock_free', 'mo2f_realtime_ip_block_free_hook' );
            	}
				wp_send_json('realtime_block_free_enable');
			}
			else
			{
				update_site_option('mo2f_realtime_ip_block_free',0);
				$timestamp = wp_next_scheduled( 'mo2f_realtime_ip_block_free_hook' );
				wp_unschedule_event( $timestamp, 'mo2f_realtime_ip_block_free_hook' );
				wp_send_json('realtime_block_free_disable');
			}
		

		}

	}
    function wpns_waf_rate_limiting_form()
	{
		if(!wp_verify_nonce($_POST['nonce'],'RateLimitingNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{
			if(get_site_option('WAFEnabled') != 1)
			{
				echo "WAFNotEnabled";
				exit;
			}

			if(sanitize_text_field($_POST['Requests'])!='')
			{
				if(is_numeric($_POST['Requests']))
				{
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';
				
				$file = file_get_contents($fileName);
				$data = $file;
			
				$req  =	sanitize_text_field($_POST['Requests']);
				if($req >1)
				{
					update_option('Rate_request',$req);
					if(isset($_POST['rateCheck']))
					{
						if(sanitize_text_field($_POST['rateCheck']) == 'on')
						{
							update_option('Rate_limiting','1');
							echo "RateEnabled";
							if(strpos($file, 'RateLimiting')!=false)
							{
								$file = str_replace('$RateLimiting=0;', '$RateLimiting=1;', $file);
								$data = $file;
								file_put_contents($fileName,$file);	
								
							}
							else
							{
								$content = explode('?>', $file);
								$file = $content[0];
								$file .= PHP_EOL;
								$file .= '$RateLimiting=1;'.PHP_EOL;
								$file .='?>';
								file_put_contents($fileName,$file);
								$data = $file;
							}
						

						}
					}	
					else
					{
						update_option('Rate_limiting','0');
						echo "Ratedisabled";
						if(strpos($file, 'RateLimiting')!=false)
						{
							$file = str_replace('$RateLimiting=1;', '$RateLimiting=0;', $file);
							$data = $file;
							file_put_contents($fileName,$file);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$RateLimiting=0;'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
							$data = $file;
						}

					}				

					
					$file = $data;
					if(strpos($file, 'RequestsPMin')!=false)
					{
						$content = explode(PHP_EOL, $file);
						$con = '';
						$len =  sizeof($content);
						
						for($i=0;$i<$len;$i++)
						{
							if(strpos($content[$i], 'RequestsPMin')!=false)
							{
								$con.='$RequestsPMin='.$req.';'.PHP_EOL;
							}
							else
							{
								$con .= $content[$i].PHP_EOL;
							}
						}
					
						file_put_contents($fileName,$con);
						$data = $con;
						
					}

					else
					{
						$content = explode('?>', $file);
						$file = $content[0];
						$file .= PHP_EOL;
						$file .= '$RequestsPMin='.$req.';'.PHP_EOL;
						$file .='?>';
						file_put_contents($fileName,$file);
						$data = $file;
					}
				
					if(sanitize_text_field($_POST['actionOnLimitE'])=='BlockIP' || sanitize_text_field($_POST['actionOnLimitE']) == 1)
					{
						update_option('actionRateL',1);

						$file = $data;
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="BlockIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$actionRateL="BlockIP";'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
							$file = $data;
						}
					}
					else if(sanitize_text_field($_POST['actionOnLimitE'])=='ThrottleIP' || sanitize_text_field($_POST['actionOnLimitE']) == 0)
					{

						$file = $data;
						update_option('actionRateL',0);
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="ThrottleIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$actionRateL="ThrottleIP";'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
						}	
					}

			}
			exit;
		}
		
			
			
		}
		echo("Error");
		exit;
		}
		
		
	}

	private function saveWAF()
	{	
		if(isset($_POST['pluginWAF']))
		{
			if(sanitize_text_field($_POST['pluginWAF'])=='on')
			{
				update_option('WAF','PluginLevel');
				update_option('WAFEnabled','1');
				echo("PWAFenabled");exit;
			}
		}
		else
		{
			update_option('WAFEnabled','0');
			update_option('WAF','wafDisable');
			echo("PWAFdisabled");exit;
		}
	}
	private function saveHWAF()
	{
		if(!function_exists('mysqli_connect'))
		{
			echo "mysqliDoesNotExit";
			exit;
		}
		if(isset($_POST['htaccessWAF']))
		{
			if(sanitize_text_field($_POST['htaccessWAF'])=='on')
			{
				update_option('WAF','HtaccessLevel');
				update_option('WAFEnabled','1');
				$dir_name =  dirname(__FILE__);
				$dirN = $dir_name;
				$dirN = str_replace('\\', '/', $dirN);
				$dirN = str_replace('controllers', 'handler', $dirN);
				
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$dir_name1 = str_replace('\\', '/', $dir_name1[0]);
				$dir_name .='.htaccess';
			 	$file =  file_get_contents($dir_name);
			 	if(strpos($file, 'php_value auto_prepend_file')!=false)
			 	{
			 		echo("WAFConflicts");
			 		exit;
			 	}

			 	$cont 	 = $file.PHP_EOL.'# BEGIN miniOrange WAF'.PHP_EOL;
			 	$cont 	.= 'php_value auto_prepend_file '.$dir_name1.'mo-check.php'.PHP_EOL;
			 	$cont 	.= '# END miniOrange WAF'.PHP_EOL;
			 	file_put_contents($dir_name, $cont);

				$filecontent = file_get_contents($dir_name);

				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'mo-check.php';
				$file = fopen($fileName, 'w+');
				$dir_name = dirname(__FILE__);
				$filepath = str_replace('\\', '/', $dir_name);
				$filepath = explode('controllers', $filepath);
				$filepath = $filepath[0].'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf.php';	

				$string   = '<?php'.PHP_EOL;
				$string  .= 'if(file_exists("'.$filepath.'"))'.PHP_EOL;
				$string  .= 'include_once("'.$filepath.'");'.PHP_EOL;
				$string  .= '?>'.PHP_EOL;
							
				fwrite($file, $string);
				fclose($file);

				if(strpos($filecontent,'mo-check.php')!=false)
				{
					echo "HWAFEnabled";
					exit;
				}
				else
				{
					echo "HWAFEnabledFailed";
					exit;
				}
			}
		}
		else
		{
			update_option('WAF','wafDisable');
			if(isset($_POST['pluginWAF']))
			{
				if(sanitize_text_field($_POST['pluginWAF']) == 'on')
				{
					update_option('WAFEnabled',1);
					update_option('WAF','PluginLevel');
				}
			}
			else
				update_option('WAFEnabled',0);
			$dir_name 	=  dirname(__FILE__);
			$dirN 		= $dir_name;
			$dirN 		= str_replace('\\', '/', $dirN);
			$dirN 		= explode('wp-content', $dirN);
			$dir_name1 	= explode('wp-content', $dir_name);
			$dir_name 	= $dir_name1[0];
			$dir_name1 	= str_replace('\\', '/', $dir_name1[0]);
			$dir_name00 = $dir_name1; 
			$dir_name1 .='.htaccess';
		 	$file 		=  file_get_contents($dir_name1);

		 	$cont 	 = PHP_EOL.'# BEGIN miniOrange WAF'.PHP_EOL;
		 	$cont 	.= 'php_value auto_prepend_file '.$dir_name00.'mo-check.php'.PHP_EOL;
		 	$cont 	.= '# END miniOrange WAF'.PHP_EOL;
		 	$file =str_replace($cont,'',$file);
			file_put_contents($dir_name1, $file);

			$filecontent = file_get_contents($dir_name1);
			if(strpos($filecontent,'mo-check.php')==false)
			{
				echo "HWAFdisabled";
				exit;
			}
			else
			{
				echo "HWAFdisabledFailed";
				exit;
			}
		}


	}
	private function savesql()
	{
		if(isset($_POST['SQL']))
		{
			if(sanitize_text_field($_POST['SQL'])=='on')
			{
				update_option('SQLInjection',1);
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';

			$file = file_get_contents($fileName);
			if(strpos($file, 'SQL')!=false)
			{
				$file = str_replace('$SQL=0;', '$SQL=1;', $file);
				file_put_contents($fileName,$file);	
			}
			else
			{
				$content = explode('?>', $file);
				$file = $content[0];
				$file .= PHP_EOL;
				$file .= '$SQL=1;'.PHP_EOL;
				$file .='?>';
				file_put_contents($fileName,$file);
			}
			echo("SQLenable");
			exit;

			}
		}
		else
		{
			update_option('SQLInjection',0);

			$dir_name =  dirname(__FILE__);
			$dir_name1 = explode('wp-content', $dir_name);
			$dir_name = $dir_name1[0];
			$filepath = str_replace('\\', '/', $dir_name1[0]);
			$fileName = $filepath.'/wp-includes/mo-waf-config.php';

			$file = file_get_contents($fileName);
			if(strpos($file, '$SQL')!=false)
			{
				$file = str_replace('$SQL=1;', '$SQL=0;', $file);
				file_put_contents($fileName,$file);	
			}
			else
			{
				$content = explode('?>', $file);
				$file = $content[0];
				$file .= PHP_EOL;
				$file .= '$SQL=0;'.PHP_EOL;
				$file .='?>';
				file_put_contents($fileName,$file);
			}
	
			echo("SQLdisable");
			exit;

		}

	}
	private function saverce()
	{
		if(isset($_POST['RCE']))
		{
			if(sanitize_text_field($_POST['RCE'])=='on')
			{
				update_option('RCEAttack',1);
				
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';

				$file = file_get_contents($fileName);
				if(strpos($file, 'RCE')!=false)
				{
					$file = str_replace('$RCE=0;', '$RCE=1;', $file);
					file_put_contents($fileName,$file);	
				}
				else
				{
					$content = explode('?>', $file);
					$file = $content[0];
					$file .= PHP_EOL;
					$file .= '$RCE=1;'.PHP_EOL;
					$file .='?>';
					file_put_contents($fileName,$file);
				}
				echo("RCEenable");
				exit;
			}
		}
		else
		{
			update_option('RCEAttack',0);

			$dir_name =  dirname(__FILE__);
			$dir_name1 = explode('wp-content', $dir_name);
			$dir_name = $dir_name1[0];
			$filepath = str_replace('\\', '/', $dir_name1[0]);
			$fileName = $filepath.'/wp-includes/mo-waf-config.php';

			$file = file_get_contents($fileName);
			if(strpos($file, '$RCE')!=false)
			{
				$file = str_replace('$RCE=1;', '$RCE=0;', $file);
				file_put_contents($fileName,$file);	
			}
			else
			{
				$content = explode('?>', $file);
				$file = $content[0];
				$file .= PHP_EOL;
				$file .= '$RCE=0;'.PHP_EOL;
				$file .='?>';
				file_put_contents($fileName,$file);
			}	
			echo("RCEdisable");
			exit;

		}

	}
	private function savexss()
	{
		if(isset($_POST['XSS']))
		{
			if(sanitize_text_field($_POST['XSS'])=='on')
			{
				update_option('XSSAttack',1);
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';
				
				$file = file_get_contents($fileName);
				if(strpos($file, 'XSS')!=false)
				{
					$file = str_replace('$XSS=0;', '$XSS=1;', $file);
					file_put_contents($fileName,$file);	
				}
				else
				{
					$content = explode('?>', $file);
					$file = $content[0];
					$file .= PHP_EOL;
					$file .= '$XSS=1;'.PHP_EOL;
					$file .='?>';
					file_put_contents($fileName,$file);
				}
				echo("XSSenable");
				exit;
			}
		}
		else
		{
			update_option('XSSAttack',0);
			$dir_name =  dirname(__FILE__);
			$dir_name1 = explode('wp-content', $dir_name);
			$dir_name = $dir_name1[0];
			$filepath = str_replace('\\', '/', $dir_name1[0]);
			$fileName = $filepath.'/wp-includes/mo-waf-config.php';

			$file = file_get_contents($fileName);
			if(strpos($file, '$XSS')!=false)
			{
				$file = str_replace('$XSS=1;', '$XSS=0;', $file);
				file_put_contents($fileName,$file);	
			}
			else
			{
				$content = explode('?>', $file);
				$file = $content[0];
				$file .= PHP_EOL;
				$file .= '$XSS=0;'.PHP_EOL;
				$file .='?>';
				file_put_contents($fileName,$file);
			}	
			echo("XSSdisable");
			exit;	
		}

	}
	private function savelfi()
	{
		if(isset($_POST['LFI']))
		{
			if(sanitize_text_field($_POST['LFI'])=='on')
			{
				update_option('LFIAttack',1);
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';
		
				$file = file_get_contents($fileName);
				if(strpos($file, 'LFI')!=false)
				{
					$file = str_replace("LFI=0;", "LFI=1;", $file);
					file_put_contents($fileName,$file);	
				}
				else
				{
					$content = explode('?>', $file);
					$file = $content[0];
					$file .= PHP_EOL;
					$file .= '$LFI=1;'.PHP_EOL;
					$file .='?>';
					file_put_contents($fileName,$file);
				}
				$file = file_get_contents($fileName);
				
				echo("LFIenable");
				exit;
			}
		}
		else
		{
			update_option('LFIAttack',0);
			$dir_name =  dirname(__FILE__);
			$dir_name1 = explode('wp-content', $dir_name);
			$dir_name = $dir_name1[0];
			$filepath = str_replace('\\', '/', $dir_name1[0]);
			$fileName = $filepath.'/wp-includes/mo-waf-config.php';

			$file = file_get_contents($fileName);
			if(strpos($file, '$LFI')!=false)
			{
				$file = str_replace('$LFI=1;', '$LFI=0;', $file);
				file_put_contents($fileName,$file);	
			}
			else
			{
				$content = explode('?>', $file);
				$file = $content[0];
				$file .= PHP_EOL;
				$file .= '$LFI=0;'.PHP_EOL;
				$file .='?>';
				file_put_contents($fileName,$file);
			}
			echo("LFIdisable");
			exit;		
		}

	}
	private function saverfi()
	{
		if(isset($_POST['RFI']))
		{
			if(sanitize_text_field($_POST['RFI'])=='on')
			{
				update_option('RFIAttack',1);
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';
				
				$file = file_get_contents($fileName);
				if(strpos($file, 'RFI')!=false)
				{
					$file = str_replace('$RFI=0;', '$RFI=1;', $file);
					file_put_contents($fileName,$file);	
				}
				else
				{
					$content = explode('?>', $file);
					$file = $content[0];
					$file .= PHP_EOL;
					$file .= '$RFI=1;'.PHP_EOL;
					$file .='?>';
					file_put_contents($fileName,$file);
				}
				echo("RFIenable");
				exit;
			}
		}
		else
		{
			update_option('RFIAttack',0);
			$dir_name =  dirname(__FILE__);
			$dir_name1 = explode('wp-content', $dir_name);
			$dir_name = $dir_name1[0];
			$filepath = str_replace('\\', '/', $dir_name1[0]);
			$fileName = $filepath.'/wp-includes/mo-waf-config.php';

			$file = file_get_contents($fileName);
			if(strpos($file, '$RFI')!=false)
			{
				$file = str_replace('$RFI=1;', '$RFI=0;', $file);
				file_put_contents($fileName,$file);	
			}
			else
			{
				$content = explode('?>', $file);
				$file = $content[0];
				$file .= PHP_EOL;
				$file .= '$RFI=0;'.PHP_EOL;
				$file .='?>';
				file_put_contents($fileName,$file);
			}	
			echo("RFIdisable");
			exit;		
		}

	}
	private function saveRateL()
	{
		
		if(sanitize_text_field($_POST['time'])!='' && sanitize_text_field($_POST['req'])!='')
		{
			if(is_numeric($_POST['time']) && is_numeric($_POST['req']))
			{
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';
				
				$file = file_get_contents($fileName);
				$data = $file;
				$time = sanitize_text_field($_POST['time']);
				$req  =	sanitize_text_field($_POST['req']);
				if($time>0 && $req >0)
				{
					update_option('Rate_time',$time);
					update_option('Rate_request',$req);
					update_option('Rate_limiting','1');

					if(strpos($file, 'RateLimiting')!=false)
					{
						$file = str_replace('$RateLimiting=0;', '$RateLimiting=1;', $file);
						$data = $file;
						file_put_contents($fileName,$file);	
					}
					else
					{
						$content = explode('?>', $file);
						$file = $content[0];
						$file .= PHP_EOL;
						$file .= '$RateLimiting=1;'.PHP_EOL;
						$file .='?>';
						file_put_contents($fileName,$file);
						$data = $file;
					}
					
					$file = $data;
					if(strpos($file, 'RequestsPMin')!=false)
					{
						$content = explode(PHP_EOL, $file);
						$con = '';
						$len =  sizeof($content);
						
						for($i=0;$i<$len;$i++)
						{
							if(strpos($content[$i], 'RequestsPMin')!=false)
							{
								$con.='$RequestsPMin='.$req.';'.PHP_EOL;
							}
							else
							{
								$con .= $content[$i].PHP_EOL;
							}
						}
						
						file_put_contents($fileName,$con);
						$data = $con;
						
					}

					else
					{
						$content = explode('?>', $file);
						$file = $content[0];
						$file .= PHP_EOL;
						$file .= '$RequestsPMin='.$req.';'.PHP_EOL;
						$file .='?>';
						file_put_contents($fileName,$file);
						$data = $file;
					}
				

					
					if(sanitize_text_field($_POST['action'])=='BlockIP')
					{
						update_option('actionRateL',1);

						$file = $data;
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="BlockIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$actionRateL="BlockIP";'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
							$file = $data;
						}
					}
					elseif(sanitize_text_field($_POST['action'])=='ThrottleIP')
					{
						$file = $data;
						update_option('actionRateL',0);
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="ThrottleIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$actionRateL="ThrottleIP";'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
						}	
					}

			}

		}	
			
		}

	}
	private function disableRL()
	{
		update_option('Rate_limiting',0);

		$dir_name =  dirname(__FILE__);
		$dir_name1 = explode('wp-content', $dir_name);
		$dir_name = $dir_name1[0];
		$filepath = str_replace('\\', '/', $dir_name1[0]);
		$fileName = $filepath.'/wp-includes/mo-waf-config.php';
		$file = file_get_contents($fileName);
			
		if(strpos($file, 'RateLimiting')!=false)
		{
			$file = str_replace('$RateLimiting=1;', '$RateLimiting=0;', $file);
			file_put_contents($fileName,$file);	
		}
		else
		{
			$content = explode('?>', $file);
			$file = $content[0];
			$file .= PHP_EOL;
			$file .= '$RateLimiting=0;'.PHP_EOL;
			$file .='?>';
			file_put_contents($fileName,$file);
		}

	}
	private function backupHtaccess()
	{
		if(isset($_POST['htaccessWAF']))
		{
			if(sanitize_text_field($_POST['htaccessWAF'])=='on')
			{
				$dir_name =  dirname(__FILE__);
				$dirN = $dir_name;
				$dirN = str_replace('\\', '/', $dirN);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$dir_name1 = str_replace('\\', '/', $dir_name1[0]);
				$dir_name =$dir_name1.'.htaccess';
			 	$file =  file_get_contents($dir_name);
				$dir_backup = $dir_name1.'htaccess';
				$handle = fopen($dir_backup, 'c+');
				fwrite($handle,$file);
			}
		}
	}
	private function limitAttack()
	{
		if(isset($_POST['limitAttack']))
		{
			$value = sanitize_text_field($_POST['limitAttack']);
			if($value>1)
			{
				update_option('limitAttack',$value);
				echo "limitSaved";
				exit;
			}
			else 
			{
				echo "limitIsLT1";
				exit;
			}

		}
	}
	

	
	function wpns_captcha_settings(){

		$nonce=sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'wpns-captcha' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}

		$site_key = sanitize_text_field($_POST['site_key']);
		$secret_key = sanitize_text_field($_POST['secret_key']);
		$enable_captcha = sanitize_text_field($_POST['enable_captcha']);
		$login_form_captcha = sanitize_text_field($_POST['login_form']);
		$reg_form_captcha = sanitize_text_field($_POST['registeration_form']);

		if((isset($_POST['version'])))
	   	{
	   		$mo2f_g_version = sanitize_text_field($_POST['version']);
	   	}
        else $mo2f_g_version='reCAPTCHA_v3';

		if($enable_captcha == 'true') $enable_captcha = "on";
		else if($enable_captcha == 'false') $enable_captcha = "";
		
		if($login_form_captcha == 'true') $login_form_captcha = "on";
		else if($login_form_captcha == 'false') $login_form_captcha = "";
		
		if($reg_form_captcha == 'true') $reg_form_captcha = "on";
		else if($reg_form_captcha == 'false') $reg_form_captcha = "";

		if(($site_key == "" || $secret_key == "") and $enable_captcha == 'on'){
			wp_send_json('empty');
			return;
		}


		if((($login_form_captcha == "on") || ($enable_captcha=="on")) && $mo2f_g_version==""){
		 wp_send_json('version_select');
		 return;
		 }
        if($mo2f_g_version=='reCAPTCHA_v2')
		{
            
			update_option( 'mo_wpns_recaptcha_site_key'			 		, $site_key     );
		    update_option( 'mo_wpns_recaptcha_secret_key'				, $secret_key   );
		}
		if($mo2f_g_version=='reCAPTCHA_v3')
		{
			
			update_option( 'mo_wpns_recaptcha_site_key_v3'			 	, $site_key     );
		    update_option( 'mo_wpns_recaptcha_secret_key_v3'				, $secret_key   );
		}
        
		update_option( 'mo_wpns_activate_recaptcha'			 		,  $enable_captcha );
		update_option( 'mo_wpns_recaptcha_version'			 		,  $mo2f_g_version );

		
		if($enable_captcha == "on"){
				update_option( 'mo_wpns_activate_recaptcha_for_login'	, $login_form_captcha );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login', $login_form_captcha );
				update_option('mo_wpns_activate_recaptcha_for_registration', $reg_form_captcha   );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration',$reg_form_captcha   );
				update_site_option('recaptcha_notification_option',1);
				wp_send_json('true');
			}
			else if($enable_captcha == ""){
				update_option( 'mo_wpns_activate_recaptcha_for_login'	, '' );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login', '' );
				update_option('mo_wpns_activate_recaptcha_for_registration', ''   );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration','' );
				wp_send_json('false');
			}
		
	}	

	function wpns_strong_password_settings(){
		$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'wpns-strn-pass' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}
		$enable_strong_pass = $_POST['enable_strong_pass'];
		if($enable_strong_pass == 'true'){$enable_strong_pass = 1;}else if($enable_strong_pass == 'false') {$enable_strong_pass = 0;}
		$strong_pass_accounts = $_POST['accounts_strong_pass'];
		update_option('mo2f_enforce_strong_passswords_for_accounts',$strong_pass_accounts);  
		update_option('mo2f_enforce_strong_passswords' , $enable_strong_pass);
		if($enable_strong_pass){
			update_option('mo_wpns_enable_rename_login_url',"");
				wp_send_json('true');
			}
			else{
				wp_send_json('false');
			}
	}
	
}
new wpns_ajax;

?>
