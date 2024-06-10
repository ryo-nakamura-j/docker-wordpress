<?php
class LoginHandler
	{
		function __construct()
		{
			add_action( 'init' , array( $this, 'mo_wpns_init' ) );
			if(get_site_option('mo2f_restrict_restAPI')){
				add_action('rest_api_init' , array($this , 'mo_block_restAPI' ) );
			}
			if(MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords', 'get_option') || get_option('mo_wpns_activate_recaptcha_for_login') 
				|| get_option('mo_wpns_activate_recaptcha_for_woocommerce_login'))
			{

				remove_filter('authenticate'		 , 'wp_authenticate_username_password'				 ,20 );
				add_filter   ('authenticate'		 , array( $this, 'custom_authenticate'		       ) ,1, 3 );
			} 
			
				add_action('wp_login'				 , array( $this, 'mo_wpns_login_success' 	       )		);
				add_action('wp_login_failed'		 , array( $this, 'mo_wpns_login_failed'	 	       ) 	    );
				//add_action('auth_cookie_bad_username', array( $this, 'mo_wpns_login_failed'	 	   )		);
				//add_action('auth_cookie_bad_hash'	 , array( $this, 'mo_wpns_login_failed'	 	  	   )		);
		
                        if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration') ){
				add_action( 'woocommerce_register_post', array( $this,'wooc_validate_user_captcha_register'), 1, 3);
			} 
		}	

		function mo_block_restAPI(){
			global $moWpnsUtility,$mo2f_dirName;
			if(strpos($_SERVER['REQUEST_URI'], '/wp-json/wp/v2/users')){
				include_once("mo-block.html");
				exit;
			}
		}

		function mo_wpns_init()
		{
			add_action( 'show_user_profile', array($this,'twofa_on_user_profile') ,10,3);
			add_action( 'edit_user_profile', array($this,'twofa_on_user_profile') ,10,3);
			add_action( 'personal_options_update', array( $this, 'user_two_factor_options_update' ) ,10,3);
			add_action( 'edit_user_profile_update', array( $this, 'user_two_factor_options_update' ) ,10,3);
			global $moWpnsUtility,$mo2f_dirName;
			$WAFEnabled = get_option('WAFEnabled');
			$WAFLevel = get_option('WAF');
			$pass2fa_login       = new Miniorange_Password_2Factor_Login();
			if(class_exists('UM_Functions') && get_site_option('mo2f_enable_2fa_prompt_on_login_page'))
				add_action('um_after_login_fields',array($pass2fa_login,'mo2f_ultimate_member_custom_login'));
			$mo2f_scanner_parts = new mo2f_scanner_parts();
			$mo2f_scanner_parts->file_cron_scan();

			if($WAFEnabled == 1)
			{
				if($WAFLevel == 'PluginLevel')
				{
					if(file_exists($mo2f_dirName .'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php'))
						include_once($mo2f_dirName .'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php');
				}
			}
		

			$userIp 			= $moWpnsUtility->get_client_ip();
			$userIp = sanitize_text_field( $userIp );
			$mo_wpns_config = new MoWpnsHandler();
			$isWhitelisted   = $mo_wpns_config->is_whitelisted($userIp);
			$isIpBlocked = false;
			if(!$isWhitelisted){
			$isIpBlocked = $mo_wpns_config->is_ip_blocked_in_anyway($userIp);
			}
			 if($isIpBlocked){
			 	include_once("mo-block.html");
			 	exit;
			 }

			$requested_uri = sanitize_text_field($_SERVER["REQUEST_URI"]);
			$option = false;
			if (is_user_logged_in()) { //chr?
				if (strpos($requested_uri, chr(get_option('login_page_url'))) != false) {
					wp_safe_redirect(site_url());
					exit;
				}
			} else {
				$option = get_option('mo_wpns_enable_rename_login_url');
			}
			if ($option) {
                if (strpos($requested_uri, '/wp-login.php?checkemail=confirm') !== false) {
                    $requested_uri = str_replace("wp-login.php","",$requested_uri);
                    wp_safe_redirect($requested_uri);
                    exit;
                } elseif (strpos($requested_uri, '/wp-login.php?checkemail=registered') !== false) {
                    $requested_uri = str_replace("wp-login.php","",$requested_uri);
                    wp_safe_redirect($requested_uri);
                    exit;
                }
                
                if (strpos($requested_uri, '/wp-login.php') !== false) {
					wp_safe_redirect(site_url());
					exit;
				}
				elseif (strpos($requested_uri, get_option('login_page_url')) !== false ) {
					@require_once ABSPATH . 'wp-login.php';
					die;
				}
			}
			
			if(isset($_POST['option']))
			{
					switch(sanitize_text_field(wp_unslash($_POST['option'])))
					{
						case "mo_wpns_change_password":
							$this->handle_change_password(sanitize_user($_POST['username'])
								,sanitize_text_field($_POST['new_password']), sanitize_text_field($_POST['confirm_password']));		
							break;
					}
			}

		}
		function twofa_on_user_profile( $user ) {
			global $mo2f_dirName;
			if(file_exists($mo2f_dirName .'handler'.DIRECTORY_SEPARATOR.'user-profile-2fa.php')){
				include_once($mo2f_dirName .'handler'.DIRECTORY_SEPARATOR.'user-profile-2fa.php');
			}
		}
		function user_two_factor_options_update( $user ) {
			global $mo2f_dirName;
			if(file_exists($mo2f_dirName .'handler'.DIRECTORY_SEPARATOR.'user-profile-2fa-update.php')){
				include_once($mo2f_dirName .'handler'.DIRECTORY_SEPARATOR.'user-profile-2fa-update.php');
			}
		}

		function mo2f_IP_email_send()
    		  	{
    		  		global $moWpnsUtility, $Mo2fdbQueries;
    		  		$userIp = $moWpnsUtility->get_client_ip();	
					$userIp = sanitize_text_field( $userIp );
					$user  =  wp_get_current_user();
					$user_id = $user->ID;
 					$meta_key = 'mo2f_user_IP';
					add_user_meta($user->ID, $meta_key,$userIp);
     				$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID);
     				if (empty($email)) {
     					$email = $user->user_email;
     				}
					if(get_user_meta($user->ID,$meta_key))
					{
    		  		$check_Ip = get_user_meta($user->ID,$meta_key)[0];

			 		if ($check_Ip != $userIp) 
			 		{	
			 			$subject ="Alert: New IP Detected";
			 			$message = mo_IP_template();
			 			$headers=array('Content-Type: text/html; charset=UTF-8');
            			if(is_email($email))
            			{
							wp_mail( $email,$subject,$message,$headers);		
						}	
			 		} 	
					}		
    		}

		function wooc_validate_user_captcha_register($username, $email, $validation_errors) {
			
			if (empty($_POST['g-recaptcha-response'])) {
				$validation_errors->add( 'woocommerce_recaptcha_error', __('Please verify the captcha', 'woocommerce' ) );
			}
		}

		//Function to Handle Change Password Form
		function handle_change_password($username,$newpassword,$confirmpassword)
		{
			global $mo2f_dirName;
			$user  = get_user_by("login",$username);
			$error = wp_authenticate_username_password($user,$username,$newpassword);
			
			if(is_wp_error($error))
			{
				$this->mo_wpns_login_failed($username);
				return $error;
			}

			if($this->update_strong_password($username,$newpassword,$confirmpassword)=="success")
			{
				wp_set_auth_cookie($user->ID,false,false);
				$this->mo_wpns_login_success($username);
				wp_redirect(get_site_option('siteurl'),301);
			} 
		}


		//Function to Update User password
		function update_strong_password($username,$newpassword,$confirmpassword)
		{
			global $mo2f_dirName;
			
			if(strlen($newpassword) > 5 && preg_match("#[0-9]+#", $newpassword) && preg_match("#[a-zA-Z]+#", $newpassword) 
				&& preg_match('/[^a-zA-Z\d]/', $newpassword) && $newpassword==$confirmpassword)
			{
				$user = get_user_by("login",$username);
				wp_set_password($newpassword,$user->ID);
				return "success";
			} 
			else
				include $mo2f_dirName . 'controllers'.DIRECTORY_SEPARATOR.'change-password.php';
		}


		//Our custom logic for user authentication
		function custom_authenticate($user, $username, $password)
		{
			global $moWpnsUtility;
			$error = new WP_Error();

			if(empty($username) && empty ($password))
				return $error;

			if(empty($username)) {
                $error->add('empty_username', __('<strong>ERROR</strong>: Invalid username or Password.'));
			}
			if(empty($password)) {
                $error->add('empty_password', __('<strong>ERROR</strong>: Invalid username or Password.'));
            }

            $user = wp_authenticate_username_password( $user, $username, $password );

			if ( is_wp_error( $user ) ) {
                $error->add('empty_username', __('<strong>ERROR</strong>: Invalid username or Password.'));
                return $user;
            }
            if(empty($error->errors))
			{
				$user  = get_user_by("login",$username);

				if($user)
				{
					$moCURL=new MocURL;
					if(get_option('mo_wpns_activate_recaptcha_for_login'))
					{
						$captcha_version=get_option('mo_wpns_recaptcha_version');
						if($captcha_version=='reCAPTCHA_v3')
							$recaptchaError = $moWpnsUtility->verify_recaptcha_3(sanitize_text_field($_POST['g-recaptcha-response']));
						else if($captcha_version=='reCAPTCHA_v2')
							$recaptchaError = $moWpnsUtility->verify_recaptcha(sanitize_text_field($_POST['g-recaptcha-response']));
					     
					}
				}

					if(!empty($recaptchaError->errors))
						$error = $recaptchaError;
 					if(empty($error->errors)){
						if(!MoWpnsUtility::get_mo2f_db_option('mo2f_enable_brute_force', 'get_option'))
						{
						   $this->mo_wpns_login_success($username);
						}
						return $user;
					}
				}
				else
					$error->add('empty_password', __('<strong>ERROR</strong>: Invalid Username or password.'));
            return $error;

			}

			


		//Function to check user password 
		function check_password($user,$error,$password)
		{
			global $moWpnsUtility, $mo2f_dirName;
			if ( wp_check_password( $password, $user->data->user_pass, $user->ID) )
			{
				if($moWpnsUtility->check_user_password_strength($user,$password,"")=="success")
				{
					if(MoWpnsUtility::get_mo2f_db_option('mo2f_enable_brute_force', 'get_option'))
						$this->mo_wpns_login_success($user->data->user_login);
					return $user;
				}
				else
					include $mo2f_dirName . 'controllers'.DIRECTORY_SEPARATOR.'change-password.php';
			}
			else
				$error->add('empty_password', __('<strong>ERROR</strong>: Wrong password.'));

			return $error;
		}


		//Function to handle successful user login
		function mo_wpns_login_success($username)
		{
			global $moWpnsUtility;
				if(get_site_option('mo2f_mail_notify') == 'on')
			 	{
			 		$this->mo2f_IP_email_send();
			 	}

				$mo_wpns_config = new MoWpnsHandler();
				$userIp 		= $moWpnsUtility->get_client_ip();
				$userIp = sanitize_text_field( $userIp );
				$mo_wpns_config->move_failed_transactions_to_past_failed($userIp);

				if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
					$moWpnsUtility->sendNotificationToUserForUnusualActivities($username, $userIp, MoWpnsConstants::LOGGED_IN_FROM_NEW_IP);


				$mo_wpns_config->add_transactions($userIp, $username, MoWpnsConstants::LOGIN_TRANSACTION, MoWpnsConstants::SUCCESS);
				
				if(isset($_POST['log']) && isset($_POST['pwd'])){
				$username =  sanitize_text_field($_POST['log']);
				$pass = $_POST['pwd'];
				$user = get_user_by('login',$username);
						
				if(!MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords', 'get_option')){
					if(!class_miniorange_2fa_strong_password::mo2f_isStrongPasswd($pass, $username)){
						if(!get_user_meta($user->ID,'password_strong?')){
							update_user_meta($user->ID,'password_strong?', true);
							$count = get_site_option('users_with_weak_pass');
							$count = $count + 1;
							update_site_option('users_with_weak_pass', $count);
						}
					}
					else{
						if(get_user_meta($user->ID,'password_strong?')){
							$count = get_site_option('users_with_weak_pass');
							$count = $count - 1;
						update_site_option('users_with_weak_pass', $count);
						}
						delete_user_meta($user->ID,'password_strong?');
					}
					

				}

			}
		}


		//Function to handle failed user login attempt
		function mo_wpns_login_failed($username)
		{
			global $moWpnsUtility;
				$userIp 		= $moWpnsUtility->get_client_ip();
				$userIp = sanitize_text_field( $userIp );
				if(empty($userIp) || empty($username) || !MoWpnsUtility::get_mo2f_db_option('mo2f_enable_brute_force', 'get_option'))
					return;

				$mo_wpns_config = new MoWpnsHandler();
				$isWhitelisted  = $mo_wpns_config->is_whitelisted($userIp);
					
				$mo_wpns_config->add_transactions($userIp, $username, MoWpnsConstants::LOGIN_TRANSACTION, MoWpnsConstants::FAILED);

				if(!$isWhitelisted)
				{
					

					if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
							$moWpnsUtility->sendNotificationToUserForUnusualActivities($username, $userIp, MoWpnsConstants::FAILED_LOGIN_ATTEMPTS_FROM_NEW_IP);
					
					$failedAttempts 	 = $mo_wpns_config->get_failed_attempts_count($userIp);
					$allowedLoginAttepts = get_option('mo2f_allwed_login_attempts') ? get_option('mo2f_allwed_login_attempts') : 10;
						
					if($allowedLoginAttepts - $failedAttempts<=0)
						$this->handle_login_attempt_exceeded($userIp);
					else if(MoWpnsUtility::get_mo2f_db_option('mo2f_show_remaining_attempts', 'get_option')) 
						$this->show_limit_login_left($allowedLoginAttepts,$failedAttempts);
				}
			
		}


		


		//Function to show number of attempts remaining
		function show_limit_login_left($allowedLoginAttepts,$failedAttempts)
		{
			global $error;
			$diff = $allowedLoginAttepts - $failedAttempts;
			$error = "<br>You have <b>".$diff."</b> login attempts remaining.";
		}


		//Function to handle login limit exceeded
		function handle_login_attempt_exceeded($userIp)
		{
			global $moWpnsUtility, $mo2f_dirName;
			$mo_wpns_config = new MoWpnsHandler();
			$mo_wpns_config->mo_wpns_block_ip($userIp, MoWpnsConstants::LOGIN_ATTEMPTS_EXCEEDED, false);
			include_once("mo-block.html");
			exit;

		}

		function setup_registration_closed($user){
			global $Mo2fdbQueries;
			if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_registration_closed' ) {
				$nonce = sanitize_text_field($_POST['mo2f_registration_closed_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'mo2f-registration-closed-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
				} else {
					if(!$Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status', $user->ID) =='MO_2_FACTOR_PLUGIN_SETTINGS'){
						//$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => '' ) );
						delete_user_meta( $user->ID, 'register_account_popup' );
						
					}
				}
			}
		}

	}
	new LoginHandler;
