<?php 
	
	global $moWpnsUtility,$mo2f_dirName,$Mo2fdbQueries;

	if(isset($_POST['option']))
	{
		$option = trim(sanitize_text_field($_POST['option']));
		switch($option)
		{
			case "mo_wpns_register_customer":
				_register_customer($_POST);																	   break;
			case "mo_wpns_verify_customer":
				_verify_customer($_POST);																	   break;
			case "mo_wpns_cancel":
				_revert_back_registration();																   break;
			case "mo_wpns_reset_password":
				_reset_password(); 																		  	   break;
		    case "mo2f_goto_verifycustomer":
		        _goto_sign_in_page();   break;
		}
	} 

	$user   = wp_get_current_user();
	$mo2f_current_registration_status = get_option( 'mo_2factor_user_registration_status');
 
	if((get_option('mo_wpns_registration_status') == 'MO_OTP_DELIVERED_SUCCESS' 
		|| get_option('mo_wpns_registration_status')  == 'MO_OTP_VALIDATION_FAILURE' 
		|| get_option('mo_wpns_registration_status')  == 'MO_OTP_DELIVERED_FAILURE') && in_array($mo2f_current_registration_status, array("MO_2_FACTOR_OTP_DELIVERED_SUCCESS", "MO_2_FACTOR_OTP_DELIVERED_FAILURE")))
	{
		$admin_phone = get_option('mo_wpns_admin_phone') ? get_option('mo_wpns_admin_phone') : "";
		include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'verify.php';
	} 
	else if ((get_option ( 'mo_wpns_verify_customer' ) == 'true' || (get_option('mo2f_email') && !get_option('mo2f_customerKey'))) && $mo2f_current_registration_status == "MO_2_FACTOR_VERIFY_CUSTOMER")
	{
		$admin_email = get_option('mo2f_email') ? get_option('mo2f_email') : "";		
		include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'login.php';
	}
	else if (! $moWpnsUtility->icr()) 
	{
		delete_option ( 'password_mismatch' );
		update_option ( 'mo_wpns_new_registration', 'true' );
		update_option('mo_2factor_user_registration_status','REGISTRATION_STARTED');
		include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'register.php';
	} 
	else
	{
		$email 				= get_option('mo2f_email');
		$key   				= get_option('mo2f_customerKey');
		$api   				= get_option('mo2f_api_key');
		$token 				= get_option('mo2f_customer_token');
		$EmailTransactions  = MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option');
		$EmailTransactions 	= $EmailTransactions? $EmailTransactions : 0;
		$SMSTransactions 	= get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z')?get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z'):0; 
		include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'profile.php';
	}

	/* REGISTRATION RELATED FUNCTIONS */

	//Function to register new customer
	function _register_customer($post)
	{
		//validate and sanitize
		global $moWpnsUtility, $Mo2fdbQueries;
		$user   = wp_get_current_user();
		$email 			 = sanitize_email($post['email']);
		$company 		 = sanitize_text_field($_SERVER["SERVER_NAME"]);

		$password 		 = sanitize_text_field($post['password']);
		$confirmPassword = sanitize_text_field($post['confirmPassword']);

		if( strlen( $password ) < 6 || strlen( $confirmPassword ) < 6)
		{
			do_action('wpns_show_message',MoWpnsMessages::showMessage('PASS_LENGTH'),'ERROR');
			return;
		}
		
		if( $password != $confirmPassword )
		{
			do_action('wpns_show_message',MoWpnsMessages::showMessage('PASS_MISMATCH'),'ERROR');
			return;
		}
		if( MoWpnsUtility::check_empty_or_null( $email ) || MoWpnsUtility::check_empty_or_null( $password ) 
			|| MoWpnsUtility::check_empty_or_null( $confirmPassword ) ) 
		{
			do_action('wpns_show_message',MoWpnsMessages::showMessage('REQUIRED_FIELDS'),'ERROR');
			return;
		} 

		update_option( 'mo2f_email', $email );
		
		update_option( 'mo_wpns_company'    , $company );
		
		update_option( 'mo_wpns_password'   , $password );

		$customer = new MocURL();
		$content  = json_decode($customer->check_customer($email), true);
		$Mo2fdbQueries->insert_user( $user->ID );
		switch ($content['status'])
		{
			case 'CUSTOMER_NOT_FOUND':
			      $customerKey = json_decode($customer->create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = ''), true);
				  
			   if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) 
				{
					update_site_option(base64_encode("totalUsersCloud"),get_site_option(base64_encode("totalUsersCloud"))+1);
					update_option( 'mo2f_email', $email );
					save_success_customer_config($email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
					_get_current_customer($email,$password);
				}
				
				break;
			default:
				_get_current_customer($email,$password);
				break;
		}

	}


   function _goto_sign_in_page(){
   	   global  $Mo2fdbQueries;
   	   $user   = wp_get_current_user();
   	   update_option('mo_wpns_verify_customer','true');
   	   update_option('mo_2factor_user_registration_status','MO_2_FACTOR_VERIFY_CUSTOMER');
   }

	//Function to go back to the registration page
	function _revert_back_registration()
	{
		global $Mo2fdbQueries;
		$user   = wp_get_current_user();
		delete_option('mo2f_email');
		delete_option('mo_wpns_registration_status');
		delete_option('mo_wpns_verify_customer');
		update_option('mo_2factor_user_registration_status','');
	}


	//Function to reset customer's password
	function _reset_password()
	{
		$customer = new MocURL();
		$forgot_password_response = json_decode($customer->mo_wpns_forgot_password());
		if($forgot_password_response->status == 'SUCCESS')
			do_action('wpns_show_message',MoWpnsMessages::showMessage('RESET_PASS'),'SUCCESS');
	}


	//Function to verify customer
	function _verify_customer($post)
	{
		global $moWpnsUtility;
		$email 	  = sanitize_email( $post['email'] );
		$password = sanitize_text_field( $post['password'] );

		if( $moWpnsUtility->check_empty_or_null( $email ) || $moWpnsUtility->check_empty_or_null( $password ) ) 
		{
			do_action('wpns_show_message',MoWpnsMessages::showMessage('REQUIRED_FIELDS'),'ERROR');
			return;
		} 
		_get_current_customer($email,$password);
	}


	//Function to get customer details
	function _get_current_customer($email,$password)
	{
		global $Mo2fdbQueries;
		$user   = wp_get_current_user();
		$customer 	 = new MocURL();
		$content     = $customer->get_customer_key($email, $password);
		$customerKey = json_decode($content, true);
		if(json_last_error() == JSON_ERROR_NONE) 
		{
			if(isset($customerKey['phone'])){
				update_option( 'mo_wpns_admin_phone', $customerKey['phone'] );
			}
			update_option('mo2f_email',$email);

			save_success_customer_config($email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
			do_action('wpns_show_message',MoWpnsMessages::showMessage('REG_SUCCESS'),'SUCCESS');
			update_site_option(base64_encode("totalUsersCloud"),get_site_option(base64_encode("totalUsersCloud"))+1);
			$customerT = new Customer_Cloud_Setup();
			$content = json_decode( $customerT->get_customer_transactions( get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ),'PREMIUM' ), true );
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
				if($content['emailRemaining']>30)
				{
					$currentTransaction = $content['emailRemaining'];
					update_site_option('cmVtYWluaW5nT1RQ',$currentTransaction);
					update_site_option('EmailTransactionCurrent',$content['emailRemaining']);				
				}
				else if($content['emailRemaining'] == 10 and get_site_option('cmVtYWluaW5nT1RQ')>30)
				{
					update_site_option('cmVtYWluaW5nT1RQ',30);
				}
			}
						
		} 
		else 
		{
			update_option('mo_2factor_user_registration_status','MO_2_FACTOR_VERIFY_CUSTOMER' );
			update_option('mo_wpns_verify_customer', 'true');
			delete_option('mo_wpns_new_registration');
			do_action('wpns_show_message',MoWpnsMessages::showMessage('ACCOUNT_EXISTS'),'ERROR');
		}
	}
	
		
	//Save all required fields on customer registration/retrieval complete.
	function save_success_customer_config($email, $id, $apiKey, $token, $appSecret)
	{
		global $Mo2fdbQueries;

		$user   = wp_get_current_user();
		update_option( 'mo2f_customerKey'  , $id 		  );
		update_option( 'mo2f_api_key'       , $apiKey    );
		update_option( 'mo2f_customer_token'		 , $token 	  );
		update_option( 'mo2f_app_secret'			 , $appSecret );
		update_option( 'mo_wpns_enable_log_requests' , true 	  );
		update_option( 'mo2f_miniorange_admin', $user->ID );
		update_option( 'mo_2factor_admin_registration_status', 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' );
		update_option( 'mo_2factor_user_registration_status', 'MO_2_FACTOR_PLUGIN_SETTINGS' );

		 $Mo2fdbQueries->update_user_details( $user->ID, array(
		 							'mo2f_user_email'                      => $email,
		 							'user_registration_with_miniorange'    => 'SUCCESS'
		 						) );
		$enduser               = new Two_Factor_Setup();
		$userinfo              = json_decode( $enduser->mo2f_get_userinfo( $email ), true );
		
		$mo2f_second_factor = 'NONE';
		if ( json_last_error() == JSON_ERROR_NONE) {
			if ( $userinfo['status'] == 'SUCCESS' ) {
				$mo2f_second_factor = mo2f_update_and_sync_user_two_factor( $user->ID, $userinfo );
			}
		}
		$configured_2FA_method='';
		if( $mo2f_second_factor == 'EMAIL'){
			$enduser->mo2f_update_userinfo( $email, 'NONE', null, '', true );
			 $configured_2FA_method = 'NONE';
		}else if ( $mo2f_second_factor != 'NONE' ) {
			$configured_2FA_method = MO2f_Utility::mo2f_decode_2_factor( $mo2f_second_factor, "servertowpdb" );
			if ( MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') == 0 ) {
				$auth_method_abr = str_replace( ' ', '', $configured_2FA_method );
			} else {
				if ( in_array( $configured_2FA_method, array(
					'Email Verification',
					'Authy Authenticator',
					'OTP over SMS'
				) ) ) {
					$enduser->mo2f_update_userinfo( $email, 'NONE', null, '', true );
				}
			}
		}

		$mo2f_message = Mo2fConstants:: langTranslate( "ACCOUNT_RETRIEVED_SUCCESSFULLY" );
		if ( $configured_2FA_method != 'NONE' && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') == 0 ) {
			$mo2f_message .= ' <b>' . $configured_2FA_method . '</b> ' . Mo2fConstants:: langTranslate( "DEFAULT_2ND_FACTOR" ) . '. ';
		}
		$mo2f_message .= '<a href=\"admin.php?page=mo_2fa_two_fa\" >' . Mo2fConstants:: langTranslate( "CLICK_HERE" ) . '</a> ' . Mo2fConstants:: langTranslate( "CONFIGURE_2FA" );

		delete_user_meta( $user->ID, 'register_account' );

		$mo2f_customer_selected_plan = get_option( 'mo2f_customer_selected_plan' );
		if ( ! empty( $mo2f_customer_selected_plan ) ) {
			delete_option( 'mo2f_customer_selected_plan' );
			
			if (MoWpnsUtility::get_mo2f_db_option('mo2f_planname', 'site_option') == 'addon_plan') 
			{
				?><script>window.location.href="admin.php?page=mo_2fa_addons";</script><?php
			}
			else
			{
				?><script>window.location.href="admin.php?page=mo_2fa_upgrade";</script><?php
			}

		} else if ( $mo2f_second_factor == 'NONE' ) {
			if(get_user_meta( $user->ID, 'register_account_popup', true)){
				update_user_meta( $user->ID, 'configure_2FA', 1 );
			}
		}

		update_option( 'mo2f_message', $mo2f_message );
		delete_user_meta( $user->ID, 'register_account_popup' 	  );
		delete_option( 'mo_wpns_verify_customer'				  );
		delete_option( 'mo_wpns_registration_status'			  );
		delete_option( 'mo_wpns_password'						  );
	}
