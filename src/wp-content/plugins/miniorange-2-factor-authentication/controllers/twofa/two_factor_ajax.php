<?php
class mo_2f_ajax
{
	function __construct(){

		add_action( 'admin_init'  , array( $this, 'mo_2f_two_factor' ) );
	}

	function mo_2f_two_factor(){
		add_action( 'wp_ajax_mo_two_factor_ajax', array($this,'mo_two_factor_ajax') );
		add_action( 'wp_ajax_nopriv_mo_two_factor_ajax', array($this,'mo_two_factor_ajax') );
	}

	function mo_two_factor_ajax(){
		$GLOBALS['mo2f_is_ajax_request'] = true;
		switch (sanitize_text_field(wp_unslash($_POST['mo_2f_two_factor_ajax']))) {
			case 'mo2f_ajax_login_redirect':
				$this->mo2f_ajax_login_redirect(); break;
			case 'mo2f_save_email_verification':
				$this->mo2f_save_email_verification();	break;
			case 'mo2f_unlimitted_user':
				$this->mo2f_unlimitted_user();break;
			case 'mo2f_check_user_exist_miniOrange':
				$this->mo2f_check_user_exist_miniOrange();break;
			case 'mo2f_single_user':
				$this->mo2f_single_user();break;
			case 'CheckEVStatus':
				$this->CheckEVStatus();		break;
			case 'mo2f_role_based_2_factor':
				$this->mo2f_role_based_2_factor();break;
			case 'mo2f_enable_disable_twofactor':
				$this->mo2f_enable_disable_twofactor();	break;
			case 'mo2f_enable_disable_inline':
				$this->mo2f_enable_disable_inline();	break;
			case 'mo2f_enable_disable_configurd_methods':
				$this->mo2f_enable_disable_configurd_methods();	break;
			case 'mo2f_shift_to_onprem':
				$this->mo2f_shift_to_onprem();break;
			case 'mo2f_enable_disable_twofactor_prompt_on_login':
				$this->mo2f_enable_disable_twofactor_prompt_on_login();break;
			case 'mo2f_save_custom_form_settings':
				$this ->mo2f_save_custom_form_settings();
				break;
			case 'mo2f_enable_disable_debug_log':
				$this ->mo2f_enable_disable_debug_log();
				break;
			case 'mo2f_delete_log_file':
				$this->mo2f_delete_log_file();
				break;
			case 'mo2f_grace_period_save':
				$this->mo2f_grace_period_save();
				break;
				case 'select_method_setup_wizard':
				$this->mo2f_select_method_setup_wizard();
				break;
            case 'mo2f_skiptwofactor_wizard':
				$this->mo2f_skiptwofactor_wizard();
				break;
			case 'mo_wpns_register_verify_customer':
				$this->mo_wpns_register_verify_customer();
				break;
			case 'mo_2fa_configure_GA_setup_wizard':
				$this->mo_2fa_configure_GA_setup_wizard();
				break;
			case 'mo_2fa_verify_GA_setup_wizard':
				$this->mo_2fa_verify_GA_setup_wizard();
				break;
			case 'mo_2fa_configure_OTPOverSMS_setup_wizard':
				$this->mo_2fa_configure_OTPOverSMS_setup_wizard();
				break;
			case 'mo_2fa_configure_OTPOverEmail_setup_wizard':
				$this->mo_2fa_configure_OTPOverEmail_setup_wizard();
				break;
			case 'mo_2fa_verify_OTPOverEmail_setup_wizard':
				$this->mo_2fa_verify_OTPOverEmail_setup_wizard();
				break;
			case 'mo_2fa_verify_OTPOverSMS_setup_wizard':
				$this->mo_2fa_verify_OTPOverSMS_setup_wizard();
				break;
			case 'mo_2fa_configure_KBA_setup_wizard':
				$this->mo_2fa_configure_KBA_setup_wizard();
				break;
			case 'mo_2fa_verify_KBA_setup_wizard':
				$this->mo_2fa_verify_KBA_setup_wizard();
				break;
			case 'mo_2fa_send_otp_token':
				$this->mo_2fa_send_otp_token();
				break;
			case "mo2f_set_otp_over_sms":
				$this->mo2f_set_otp_over_sms();	break;
			case "mo2f_set_miniorange_methods":
				$this->mo2f_set_miniorange_methods();	break;
			case "mo2f_set_GA":
				$this->mo2f_set_GA();	break;
		}
	}

	function mo2f_grace_period_save()
    {
	    	$nonce= isset($_POST['mo2f_grace_period_nonce'])?sanitize_text_field($_POST['mo2f_grace_period_nonce']):'';
			if(!wp_verify_nonce($nonce,'mo2f-nonce-enable-grace-period'))
			{
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				wp_send_json('false');

 	        }
			else
			{  
                
				$enable=isset($_POST['mo2f_graceperiod_use'])?sanitize_text_field($_POST['mo2f_graceperiod_use']):'';
				if($enable=="true")
				{
					update_site_option('mo2f_grace_period',"on");
					$grace_type=isset($_POST['mo2f_graceperiod_hour'])?sanitize_text_field($_POST['mo2f_graceperiod_hour']):'';
					if($grace_type=="true")
					{
						update_site_option('mo2f_grace_period_type',"hours");
					}
					else
					{
						update_site_option('mo2f_grace_period_type',"days");
					}
					if(isset($_POST['mo2f_graceperiod_value']) && $_POST['mo2f_graceperiod_value'] > 0 && $_POST['mo2f_graceperiod_value'] <=10){
						update_site_option('mo2f_grace_period_value',sanitize_text_field($_POST['mo2f_graceperiod_value']));
					}else{
						update_site_option('mo2f_grace_period_value',1);
						wp_send_json('invalid_input');
					}
				}
				else
				{
					update_site_option('mo2f_grace_period',"off");
					
					
				        update_site_option('mo2f_inline_registration',1);
					

				}
				wp_send_json('true');
			}
	}
	function mo_2fa_verify_KBA_setup_wizard()
	{
		global $Mo2fdbQueries;
		$kba_q1 = sanitize_text_field($_POST['mo2f_kbaquestion_1']);
		$kba_a1 = sanitize_text_field( $_POST['mo2f_kba_ans1'] );
		$kba_q2 = sanitize_text_field($_POST['mo2f_kbaquestion_2']);
		$kba_a2 = sanitize_text_field( $_POST['mo2f_kba_ans2'] );
		$kba_q3 = sanitize_text_field( $_POST['mo2f_kbaquestion_3'] );
		$kba_a3 = sanitize_text_field( $_POST['mo2f_kba_ans3'] );
		$user   = wp_get_current_user();
		$this->mo2f_check_and_create_user($user->ID);
		if ( MO2f_Utility::mo2f_check_empty_or_null( $kba_q1 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a1 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_q2 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a2) || MO2f_Utility::mo2f_check_empty_or_null( $kba_q3) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a3) ) {
				wp_send_json("Invalid Questions or Answers");
			}
		if ( strcasecmp( $kba_q1, $kba_q2 ) == 0 || strcasecmp( $kba_q2, $kba_q3 ) == 0 || strcasecmp( $kba_q3, $kba_q1 ) == 0 ) {
			wp_send_json("The questions you select must be unique.");
		}
		$kba_q1 = addcslashes( stripslashes( $kba_q1 ), '"\\' );
		$kba_q2 = addcslashes( stripslashes( $kba_q2 ), '"\\' );
		$kba_q3 = addcslashes( stripslashes( $kba_q3 ), '"\\' );
		$kba_a1 = addcslashes( stripslashes( $kba_a1 ), '"\\' );
		$kba_a2 = addcslashes( stripslashes( $kba_a2 ), '"\\' );
		$kba_a3 = addcslashes( stripslashes( $kba_a3 ), '"\\' );
		$email            = $user->user_email;
		$kba_registration = new Two_Factor_Setup();
		$Mo2fdbQueries->update_user_details( $user->ID, array(
			'mo2f_SecurityQuestions_config_status'  => true,
			'mo_2factor_user_registration_status'   => 'MO_2_FACTOR_PLUGIN_SETTINGS',
			'mo2f_user_email'						=> $email
			));
		$kba_reg_reponse  = json_decode( $kba_registration->register_kba_details( $email, $kba_q1, $kba_a1, $kba_q2, $kba_a2, $kba_q3, $kba_a3, $user->ID ), true );
	
		if($kba_reg_reponse['status']=='SUCCESS')
		{
			wp_send_json("SUCCESS");
		}
		else 
		{
			wp_send_json("An error has occured while saving KBA details. Please try again.");
		}
	}
	function mo_2fa_send_otp_token()
	{
		$enduser 	  		 = new Customer_Setup();
		$customer_key 		 = get_site_option('mo2f_customerKey');
		$api_key 	  		 = get_site_option('mo2f_api_key');
		$selected_2FA_method = sanitize_text_field($_POST['selected_2FA_method']);
		$user_id  			 = wp_get_current_user()->ID;
		$contact_info = '';
		if($selected_2FA_method == 'OTP Over Email')
		{
			$contact_info	  		 = sanitize_email($_POST['mo2f_contact_info']);
			update_user_meta($user_id,'tempRegEmail',$contact_info);
			if (!filter_var($contact_info, FILTER_VALIDATE_EMAIL)) {
			  $emailErr = "Invalid email format";
			  echo $emailErr;
			  exit;
			}
		}
		else if(strpos($selected_2FA_method,"SMS") !== false)
		{
			$contact_info   	  		 = sanitize_text_field($_POST['mo2f_contact_info']);
			$contact_info = str_replace(' ', '', $contact_info);
		}
		$content 	  		= $enduser->send_otp_token($contact_info,$selected_2FA_method,$customer_key,$api_key);
		$content 			= json_decode($content);
		
		if($content->status =='SUCCESS')
		{
			update_user_meta($user_id,'txId',$content->txId);
			update_user_meta($user_id,'tempRegPhone',$contact_info);
			wp_send_json('SUCCESS');
		}else if($content->status == "FAILED" && $selected_2FA_method == 'OTP Over Email')
		{
			wp_send_json('SMTPNOTSET');
		}
		else 
		wp_send_json("An error has occured while sending the OTP.");	
	}
	function mo2f_check_and_create_user($user_id)
	{	
		global $Mo2fdbQueries;
		$twofactor_transactions = new Mo2fDB;			
		$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user_id);
		if($exceeded){
			echo 'User Limit has been exceeded';
			exit;
		}
		$Mo2fdbQueries->insert_user( $user_id );			
	}
	function mo_2fa_verify_OTPOverSMS_setup_wizard()
	{
		global $Mo2fdbQueries;
		$enduser 	  		= new Customer_Setup();
		$current_user 		= wp_get_current_user();
		$otpToken 			= sanitize_text_field($_POST['mo2f_otp_token']);
		$user_id    	 	= wp_get_current_user()->ID;
		$email 				= get_user_meta($user_id,'tempRegPhone',true);
		$content = json_decode($enduser->validate_otp_token( 'SMS', null, get_user_meta($user_id,'txId',true), $otpToken, get_site_option('mo2f_customerKey'), get_site_option('mo2f_api_key') ),true);
		
		if($content['status'] == 'SUCCESS')
		{
			$this->mo2f_check_and_create_user($user_id);
			$Mo2fdbQueries->update_user_details( $user_id, array(
				'mo2f_OTPOverSMS_config_status' => true,
				'mo2f_configured_2FA_method'             => "OTP Over SMS",
				'mo2f_user_phone'						 => $email,
				'user_registration_with_miniorange'      => 'SUCCESS',
				'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS'
			) );
			wp_send_json("SUCCESS");
		} 
		else
		{
			wp_send_json("Invalid OTP");
		}
		exit;
	
	}
	function mo_2fa_verify_OTPOverEmail_setup_wizard()
	{
		global $Mo2fdbQueries;
		$enduser 	  		= new Customer_Setup();
		$current_user 		= wp_get_current_user();
		$otpToken 			= sanitize_text_field($_POST['mo2f_otp_token']);
		$user_id    	 	= wp_get_current_user()->ID;
		$email 				= get_user_meta($user_id,'tempRegEmail',true);
		$content = json_decode($enduser->validate_otp_token( 'OTP_OVER_EMAIL', null, get_user_meta($current_user->ID,'mo2f_transactionId',true), $otpToken, get_site_option('mo2f_customerKey'), get_site_option('mo2f_api_key') ),true);
		
		if($content['status'] == 'SUCCESS')
		{
			$this->mo2f_check_and_create_user($user_id);
			$Mo2fdbQueries->update_user_details( $user_id, array(
				'mo2f_OTPOverEmail_config_status' => true,
				'mo2f_configured_2FA_method'             => "OTP Over Email",
				'mo2f_user_email'						 => $email,
				'user_registration_with_miniorange'      => 'SUCCESS',
				'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS'
			) );
			wp_send_json("SUCCESS");
		} 
		else
		{
			wp_send_json("Invalid OTP");
		}
		exit;
	}
	function mo_2fa_verify_GA_setup_wizard()
	{
		global $Mo2fdbQueries;
		$path = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'gaonprem.php';
		include_once $path;
		$obj_google_auth = new Google_auth_onpremise();
		$user_id = wp_get_current_user()->ID;
		$otpToken = sanitize_text_field($_POST['mo2f_google_auth_code']);
		$session_id_encrypt = isset($_POST['mo2f_session_id']) ? sanitize_text_field($_POST['mo2f_session_id']) : null;
		$secret= $obj_google_auth->mo_GAuth_get_secret($user_id);
		if($session_id_encrypt){
			$secret = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'secret_ga');
		}
		$content = $obj_google_auth->verifyCode($secret, $otpToken);
		$content = json_decode($content);
		if($content->status== 'false')
			wp_send_json("Invalid One time Passcode. Please enter again");
		else
		{
			$obj_google_auth->mo_GAuth_set_secret($user_id,$secret);
			$this->mo2f_check_and_create_user($user_id);
			$Mo2fdbQueries->update_user_details( $user_id, array(
				'mo2f_GoogleAuthenticator_config_status' => true,
				'mo2f_AuthyAuthenticator_config_status'  => false,
				'mo2f_configured_2FA_method'             => "Google Authenticator",
				'user_registration_with_miniorange'      => 'SUCCESS',
				'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS'
			) );

			wp_send_json('SUCCESS');	
		}
		exit;
	}
	function mo_2fa_configure_GA_setup_wizard()
	{
		$path = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'gaonprem.php';
		include_once $path;
		$obj_google_auth = new Google_auth_onpremise();
		update_option('mo2f_google_appname',sanitize_text_field($_SERVER['SERVER_NAME']));
		update_option('mo2f_wizard_selected_method', 'GA');
        $obj_google_auth->mo_GAuth_get_details(true);
		exit;
	}
	function mo_2fa_configure_OTPOverSMS_setup_wizard()
	{
		global $Mo2fdbQueries;
		$user = wp_get_current_user();
		$mo2f_user_phone 	 = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
		$user_phone      	 = $mo2f_user_phone ? $mo2f_user_phone : get_option( 'user_phone_temp' );
    	$session_id_encrypt  = MO2f_Utility::random_str(20);
		update_option('mo2f_wizard_selected_method', 'SMS-OTP');
		?>
		<div class="mo2f-inline-block">
			<h4> Remaining SMS Transactions: <b><?php echo intval(esc_html(get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z')));?> </b></h4>
		</div>
		<form name="f" method="post" action="" id="mo2f_verifyphone_form">
	        <input type="hidden" name="option" value="mo2f_configure_otp_over_sms_send_otp"/>
	        <input type="hidden" name="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt) ?>"/>
			<input type="hidden" name="mo2f_configure_otp_over_sms_send_otp_nonce"
							value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-otp-over-sms-send-otp-nonce" )) ?>"/>

	        <div style="display:inline;">
			<b>Phone no.: </b>
	            <input class="mo2f_table_textbox_phone" style="width:200px;height: 30px;" type="text" name="phone" id="mo2f_contact_info"
	                   value="<?php echo esc_html($user_phone) ?>" pattern="[\+]?[0-9]{1,4}\s?[0-9]{7,12}"
	                   title="<?php echo mo2f_lt( 'Enter phone number without any space or dashes' ); ?>"/><br>
	            <input type="button" name="mo2f_send_otp" id="mo2f_send_otp" class="mo2f-modal__btn button"
	                   value="<?php echo mo2f_lt( 'Send OTP' ); ?>"/>
	        </div>
	    </form>
	    <br>
	    <form name="f" method="post" action="" id="mo2f_validateotp_form">
	        <input type="hidden" name="option" value="mo2f_configure_otp_over_sms_validate"/>
	        <input type="hidden" name="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt) ?>"/>
			<input type="hidden" name="mo2f_configure_otp_over_sms_validate_nonce"
							value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-otp-over-sms-validate-nonce" )) ?>"/>
	        <p><?php echo mo2f_lt( 'Enter One Time Passcode' ); ?></p>
	        <input class="mo2f_table_textbox_phone" style="width:200px;height: 30px" autofocus="true" type="text" name="mo2f_otp_token" id="mo2f_otp_token"
	               placeholder="<?php echo mo2f_lt( 'Enter OTP' ); ?>" style="width:95%;"/>
	        <br><br>
	    </form><br>
    
		<?php
		exit;
	}
	function mo_2fa_configure_OTPOverEmail_setup_wizard()
	{
		$session_id_encrypt  = MO2f_Utility::random_str(20);
        $user_email 		 = wp_get_current_user()->user_email;
		update_option('mo2f_wizard_selected_method', 'Email-OTP');
		?>
		<div class="mo2f-inline-block">
			<h4> Remaining Email Transactions: <b><?php echo intval(esc_html(get_site_option('cmVtYWluaW5nT1RQ')));?> </b></h4>
		</div>
	    <form name="f" method="post" action="" id="mo2f_verifyemail_form">
	        <input type="hidden" name="option" value="mo2f_configure_otp_over_email_send_otp"/>
	        <input type="hidden" name="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt) ?>"/>
			<input type="hidden" name="mo2f_configure_otp_over_email_send_otp_nonce"
							value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-otp-over-email-send-otp-nonce" )) ?>"/>

	        <div style="display:inline;">
	            <b>Email Address: </b>
	            <input class="mo2f_table_textbox" style="width:280px;height: 30px;" type="text" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" name="verify_phone" id="mo2f_contact_info"
	                   value="<?php echo esc_html($user_email) ?>" 
	                   title="<?php echo mo2f_lt( 'Enter your email address without any space or dashes' ); ?>"/><br><br>
	            <input type="button" name="mo2f_send_otp" id="mo2f_send_otp" class="mo2f-modal__btn button"
	                   value="<?php echo mo2f_lt( 'Send OTP' ); ?>"/>
	        </div>
	    </form>
	    <br><br>
	    <form name="f" method="post" action="" id="mo2f_validateotp_form">
	        <input type="hidden" name="option" value="mo2f_configure_otp_over_sms_validate"/>
	        <input type="hidden" name="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt) ?>"/>
			<input type="hidden" name="mo2f_configure_otp_over_email_validate_nonce"
							value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-otp-over-email-validate-nonce" )) ?>"/>
	        <b><?php echo mo2f_lt( 'Enter One Time Passcode:' ); ?>
	        <input class="mo2f_table_textbox" style="width:200px;height: 30px;"  type="text"  name="mo2f_otp_token" id ="mo2f_otp_token" 
	               placeholder="<?php echo mo2f_lt( 'Enter OTP' ); ?>" style="width:95%;"/></b>
	        <br><br>
	    </form><br>
    	<script>
			 var input = jQuery("#mo2f_contact_info");
                var len = input.val().length;
                input[0].focus();
                input[0].setSelectionRange(len, len);
		</script>
		<?php
		exit;
	}
	function mo_2fa_configure_KBA_setup_wizard()
	{
		update_option('mo2f_wizard_selected_method', 'KBA');
		?>
		    <div class="mo2f_kba_header"><?php echo mo2f_lt( 'Please choose 3 questions' ); ?></div>
    <br>
    <table cellspacing="10">
        <tr class="mo2f_kba_header">
            <th style="width: 10%;">
				<?php echo mo2f_lt( 'Sr. No.' ); ?>
            </th>
            <th class="mo2f_kba_tb_data">
				<?php echo mo2f_lt( 'Questions' ); ?>
            </th>
            <th>
				<?php echo mo2f_lt( 'Answers' ); ?>
            </th>
        </tr>
        <tr class="mo2f_kba_body">
            <td>
                <center>1.</center>
            </td>
            <td class="mo2f_kba_tb_data">
                <select name="mo2f_kbaquestion_1" id="mo2f_kbaquestion_1" class="mo2f_kba_ques" required="true"
                        >
                    <option value="" selected disabled>
                        ------------<?php echo mo2f_lt( 'Select your question' ); ?>
                        ------------
                    </option>
                    <option id="mq1_1"
                            value="What is your first company name?"><?php echo mo2f_lt( 'What is your first company name?' ); ?></option>
                    <option id="mq2_1"
                            value="What was your childhood nickname?"><?php echo mo2f_lt( 'What was your childhood nickname?' ); ?></option>
                    <option id="mq3_1"
                            value="In what city did you meet your spouse/significant other?"><?php echo mo2f_lt( 'In what city did you meet your spouse/significant other?' ); ?></option>
                    <option id="mq4_1"
                            value="What is the name of your favorite childhood friend?"><?php echo mo2f_lt( 'What is the name of your favorite childhood friend?' ); ?></option>
                    <option id="mq5_1"
                            value="What school did you attend for sixth grade?"><?php echo mo2f_lt( 'What school did you attend for sixth grade?' ); ?></option>
                    <option id="mq6_1"
                            value="In what city or town was your first job?"><?php echo mo2f_lt( 'In what city or town was your first job?' ); ?></option>
                    <option id="mq7_1"
                            value="What is your favourite sport?"><?php echo mo2f_lt( 'What is your favourite sport?' ); ?></option>
                    <option id="mq8_1"
                            value="Who is your favourite sports player?"><?php echo mo2f_lt( 'Who is your favourite sports player?' ); ?></option>
                    <option id="mq9_1"
                            value="What is your grandmother's maiden name?"><?php echo mo2f_lt( "What is your grandmother's maiden name?" ); ?></option>
                    <option id="mq10_1"
                            value="What was your first vehicle's registration number?"><?php echo mo2f_lt( "What was your first vehicle's registration number?" ); ?></option>
                </select>
            </td>
            <td style="text-align: end;">
                <input class="mo2f_table_textbox_KBA" type="password" name="mo2f_kba_ans1" id="mo2f_kba_ans1"
                       title="<?php echo mo2f_lt( 'Only alphanumeric letters with special characters(_@.$#&amp;+-) are allowed.' ); ?>"
                       pattern="(?=\S)[A-Za-z0-9_@.$#&amp;+\-\s]{1,100}" required="true" 
                       placeholder="<?php echo mo2f_lt( 'Enter your answer' ); ?>"/>
            </td>
        </tr>
        <tr class="mo2f_kba_body">
            <td>
                <center>2.</center>
            </td>
            <td class="mo2f_kba_tb_data">
                <select name="mo2f_kbaquestion_2" id="mo2f_kbaquestion_2" class="mo2f_kba_ques" required="true"
                        >
                    <option value="" selected disabled>
                        ------------<?php echo mo2f_lt( 'Select your question' ); ?>
                        ------------
                    </option>
                    <option id="mq1_2"
                            value="What is your first company name?"><?php echo mo2f_lt( 'What is your first company name?' ); ?></option>
                    <option id="mq2_2"
                            value="What was your childhood nickname?"><?php echo mo2f_lt( 'What was your childhood nickname?' ); ?></option>
                    <option id="mq3_2"
                            value="In what city did you meet your spouse/significant other?"><?php echo mo2f_lt( 'In what city did you meet your spouse/significant other?' ); ?></option>
                    <option id="mq4_2"
                            value="What is the name of your favorite childhood friend?"><?php echo mo2f_lt( 'What is the name of your favorite childhood friend?' ); ?></option>
                    <option id="mq5_2"
                            value="What school did you attend for sixth grade?"><?php echo mo2f_lt( 'What school did you attend for sixth grade?' ); ?></option>
                    <option id="mq6_2"
                            value="In what city or town was your first job?"><?php echo mo2f_lt( 'In what city or town was your first job?' ); ?></option>
                    <option id="mq7_2"
                            value="What is your favourite sport?"><?php echo mo2f_lt( 'What is your favourite sport?' ); ?></option>
                    <option id="mq8_2"
                            value="Who is your favourite sports player?"><?php echo mo2f_lt( 'Who is your favourite sports player?' ); ?></option>
                    <option id="mq9_2"
                            value="What is your grandmother's maiden name?"><?php echo mo2f_lt( 'What is your grandmother\'s maiden name?' ); ?></option>
                    <option id="mq10_2"
                            value="What was your first vehicle's registration number?"><?php echo mo2f_lt( 'What was your first vehicle\'s registration number?' ); ?></option>
                </select>
            </td>
            <td style="text-align: end;">
                <input class="mo2f_table_textbox_KBA" type="password" name="mo2f_kba_ans2" id="mo2f_kba_ans2"
                       title="<?php echo mo2f_lt( 'Only alphanumeric letters with special characters(_@.$#&amp;+-) are allowed.' ); ?>"
                       pattern="(?=\S)[A-Za-z0-9_@.$#&amp;+\-\s]{1,100}" required="true"
                       placeholder="<?php echo mo2f_lt( 'Enter your answer' ); ?>"/>
            </td>
        </tr>
        <tr class="mo2f_kba_body">
            <td>
                <center>3.</center>
            </td>
            <td class="mo2f_kba_tb_data">
                <input class="mo2f_kba_ques" type="text" style="width: 100%;"name="mo2f_kbaquestion_3" id="mo2f_kbaquestion_3"
                       required="true"
                       placeholder="<?php echo mo2f_lt( 'Enter your custom question here' ); ?>"/>
            </td>
            <td style="text-align: end;">
                <input class="mo2f_table_textbox_KBA" type="password" name="mo2f_kba_ans3" id="mo2f_kba_ans3"
                       title="<?php echo mo2f_lt( 'Only alphanumeric letters with special characters(_@.$#&amp;+-) are allowed.' ); ?>"
                       pattern="(?=\S)[A-Za-z0-9_@.$#&amp;+\-\s]{1,100}" required="true"
                       placeholder="<?php echo mo2f_lt( 'Enter your answer' ); ?>"/>
            </td>
        </tr>
    </table>
    <script type="text/javascript">
    	var mo_option_to_hide1;
        //hidden element in dropdown list 2
        var mo_option_to_hide2;

        function mo_option_hide(list) {
            //grab the team selected by the user in the dropdown list
            var list_selected = document.getElementById("mo2f_kbaquestion_" + list).selectedIndex;
            //if an element is currently hidden, unhide it
            if (typeof (mo_option_to_hide1) != "undefined" && mo_option_to_hide1 !== null && list == 2) {
                mo_option_to_hide1.style.display = 'block';
            } else if (typeof (mo_option_to_hide2) != "undefined" && mo_option_to_hide2 !== null && list == 1) {
                mo_option_to_hide2.style.display = 'block';
            }
            //select the element to hide and then hide it
            if (list == 1) {
                if (list_selected != 0) {
                    mo_option_to_hide2 = document.getElementById("mq" + list_selected + "_2");
                    mo_option_to_hide2.style.display = 'none';
                }
            }
            if (list == 2) {
                if (list_selected != 0) {
                    mo_option_to_hide1 = document.getElementById("mq" + list_selected + "_1");
                    mo_option_to_hide1.style.display = 'none';
                }
            }
        }
  

    </script>

			<?php
			exit;
	}

	function mo2f_register_customer($post)
	{
		//validate and sanitize
		global $moWpnsUtility, $Mo2fdbQueries;
		$user   		 = wp_get_current_user();
		$email 			 = sanitize_email($post['email']);
		$company 		 = sanitize_text_field($_SERVER["SERVER_NAME"]);

		$password 		 = $post['password'];
		$confirmPassword = $post['confirmPassword'];

		if( strlen( $password ) < 6 || strlen( $confirmPassword ) < 6)
		{
			return "Password length is less then expected";
		}
		
		if( $password != $confirmPassword )
		{
			return "Password and confirm Password does not match.";
		}
		if( MoWpnsUtility::check_empty_or_null( $email ) || MoWpnsUtility::check_empty_or_null( $password ) 
			|| MoWpnsUtility::check_empty_or_null( $confirmPassword ) ) 
		{
			return "Unknown Error has occured.";
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
					$this->save_success_customer_config($email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
					$this->_get_current_customer($email,$password);
					return "SUCCESS";
				}
				
				break;
			default:
				$res = $this->_get_current_customer($email,$password);
				if($res == "SUCCESS")
					return $res;
				return "Email is already registered in miniOrange. Please try to login to your account.";
				
		}
		return "Error Occured while registration";

	}
	function _verify_customer($post)
	{
		global $moWpnsUtility;
		$email 	  = sanitize_email( $post['email'] );
		$password = sanitize_text_field( $post['password'] );

		if( $moWpnsUtility->check_empty_or_null( $email ) || $moWpnsUtility->check_empty_or_null( $password ) ) 
		{
			return "Username or Password is missing.";
		} 
		return $this->_get_current_customer($email,$password);
	}
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

			$this->save_success_customer_config($email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
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
			return "SUCCESS";			
		} 
		else 
		{
			update_option('mo_2factor_user_registration_status','MO_2_FACTOR_VERIFY_CUSTOMER' );
			update_option('mo_wpns_verify_customer', 'true');
			delete_option('mo_wpns_new_registration');
			return "Invalid Username or Password";
		}
	}
	

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
		
		
		delete_option( 'mo_wpns_verify_customer'				  );
		delete_option( 'mo_wpns_registration_status'			  );
		delete_option( 'mo_wpns_password'						  );
	}

	function mo_wpns_register_verify_customer()
	{
		$res ="";
		if(isset($_POST['Login_and_Continue']) && sanitize_text_field($_POST['Login_and_Continue']) =='Login and Continue')
			$res = $this->_verify_customer($_POST);
		
		else
			$res = $this->mo2f_register_customer($_POST);
		wp_send_json($res);
	}
	function mo2f_select_method_setup_wizard()
	{
		global $Mo2fdbQueries;
		if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']),'miniorange-select-method-setup-wizard'))
		{
			wp_send_json("ERROR");
		}
		
		$current_user = wp_get_current_user();
		$selected_2FA_method = sanitize_text_field($_POST['mo2f_method']);
		update_option('mo2f_wizard_selected_method', 'DUO/Telegram');
  
		if(!MO2F_IS_ONPREM)
		{
			update_option('mo_2factor_user_registration_status','REGISTRATION_STARTED');
			update_user_meta( $current_user->ID, 'register_account_popup', 1 );
			update_user_meta( $current_user->ID, 'mo2f_2FA_method_to_configure', $selected_2FA_method );
			wp_send_json("SUCCESS");			
	
		}

		
		$exceeded = $Mo2fdbQueries->check_alluser_limit_exceeded($current_user->ID);
		if(!$exceeded)
			$Mo2fdbQueries->insert_user( $current_user->ID );	
		
		if($selected_2FA_method == 'OTP Over Email')
		{
			wp_send_json("SUCCESS");			
		}
		update_user_meta( $current_user->ID, 'mo2f_2FA_method_to_configure', $selected_2FA_method );
		
		$mo_2factor_admin_registration_status = get_option('mo_2factor_admin_registration_status');
		if($selected_2FA_method == 'OTP Over SMS' && $mo_2factor_admin_registration_status != 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS')
		{
			update_option('mo_2factor_user_registration_status','REGISTRATION_STARTED');
			update_user_meta( $current_user->ID, 'register_account_popup', 1 );
		}
		else
			update_user_meta( $current_user->ID, 'configure_2FA', 1);
		wp_send_json("SUCCESS");
	}

    function mo2f_skiptwofactor_wizard()
    {
	    $nonce = sanitize_text_field($_POST['nonce']);
	    if ( ! wp_verify_nonce( $nonce, 'mo2fskiptwofactornonce' ) ) {
		    $error = new WP_Error();
		    $error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
		    wp_send_json_error($error);
		    exit;
	    } else{
            $skip_wizard_2fa_stage = sanitize_text_field($_POST['twofactorskippedon']);
            
            update_option('mo2f_wizard_skipped', $skip_wizard_2fa_stage);
        }
    }
    
	function mo2f_set_miniorange_methods(){
		$nonce = sanitize_text_field($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'mo2f-update-mobile-nonce' ) ) {
			$error = new WP_Error();
			$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
			wp_send_json_error($error);
			exit;
		}
		global $Mo2fdbQueries;
		$transient_id=sanitize_text_field($_POST['transient_id']);
		$user_id = MO2f_Utility::mo2f_get_transient($transient_id, 'mo2f_user_id');
		if(empty($user_id)){
			wp_send_json('UserIdNotFound');
		}
		$user = get_user_by('id',$user_id);
		$email = !empty($Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id ))?$Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id ):$user->user_email;
		$otpToken=sanitize_text_field($_POST['code']);
		$customer = new Customer_Setup();
		$content  = json_decode( $customer->validate_otp_token( 'SOFT TOKEN', $email, null, $otpToken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
		wp_send_json($content);
	}
	function mo2f_set_otp_over_sms(){
		$nonce = sanitize_text_field($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'mo2f-update-mobile-nonce' ) ) {
			$error = new WP_Error();
			$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
			wp_send_json_error($error);
			exit;
		}
		global $Mo2fdbQueries;
		$transient_id=sanitize_text_field($_POST['transient_id']);
		$user_id = MO2f_Utility::mo2f_get_transient($transient_id, 'mo2f_user_id');
		if(empty($user_id)){
			wp_send_json('UserIdNotFound');
		}
		$user = get_user_by('id',$user_id);
		$new_phone = sanitize_text_field($_POST['phone']);
		$new_phone = str_replace(' ','',$new_phone);
		$Mo2fdbQueries->update_user_details($user_id, array("mo2f_user_phone" => $new_phone) );
		$user_phone = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user_id );
		wp_send_json($user_phone);
	}
	function mo2f_set_GA(){
		$nonce = sanitize_text_field($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'mo2f-update-mobile-nonce' ) ) {
			$error = new WP_Error();
			$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
			wp_send_json_error($error);
			exit;
		}
		include_once dirname(dirname(dirname( __FILE__ ))) .DIRECTORY_SEPARATOR.'handler'. DIRECTORY_SEPARATOR.'twofa'. DIRECTORY_SEPARATOR. 'gaonprem.php';
		global $Mo2fdbQueries;
		$transient_id=sanitize_text_field($_POST['transient_id']);
		$user_id = MO2f_Utility::mo2f_get_transient($transient_id, 'mo2f_user_id');
		if(empty($user_id)){
			wp_send_json('UserIdNotFound');
		}
		$google_auth = new Miniorange_Rba_Attributes();
		$user = get_user_by('id',$user_id);
		$email = !empty($Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id ))?$Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id ):$user->user_email;
		$otpToken = sanitize_text_field($_POST['code']);
		$ga_secret = sanitize_text_field($_POST['ga_secret']);
		if(MO2F_IS_ONPREM){
			$gauth_obj = new Google_auth_onpremise();
			$gauth_obj->mo_GAuth_set_secret($user_id, $ga_secret);
		}else{

			$google_auth = new Miniorange_Rba_Attributes();
			$google_response = json_decode( $google_auth->mo2f_google_auth_service( $email, 'miniOrangeAu' ), true );
		}
		$google_response = json_decode($google_auth->mo2f_validate_google_auth($email,$otpToken,$ga_secret),true);
		wp_send_json($google_response['status']);
	}
	function mo2f_ajax_login_redirect()
		{	
			if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']),'miniorange-2-factor-login-nonce'))
			{
				wp_send_json("ERROR");
				exit;
			}
			$username = sanitize_text_field($_POST['username']);
			$password = $_POST['password'];
			apply_filters( 'authenticate', null, $username, $password );
		}
	function mo2f_save_custom_form_settings()
	{
		$customForm = false;
		$nonce = sanitize_text_field($_POST['mo2f_nonce_save_form_settings']);

		if ( ! wp_verify_nonce( $nonce, 'mo2f-nonce-save-form-settings' ) ) {
			$error = new WP_Error();
			$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
			wp_send_json('error');
		}
		if(!current_user_can( 'administrator' ))
				wp_send_json('error');
		if(isset($_POST['submit_selector']) and
			isset($_POST['email_selector']) and
			isset($_POST['authType']) and
			isset($_POST['customForm']) and
			isset($_POST['form_selector']) and

			sanitize_text_field($_POST['submit_selector'])!="" and
			sanitize_text_field($_POST['email_selector'])!="" and
			sanitize_text_field($_POST['customForm'])!="" and
			sanitize_text_field($_POST['form_selector'])!="")
		{
			$submit_selector 				= sanitize_text_field($_POST['submit_selector']);
			$form_selector					= sanitize_text_field($_POST['form_selector']);
			$email_selector 				= sanitize_text_field($_POST['email_selector']);
			$phone_selector 				= sanitize_text_field($_POST['phone_selector']);
			$authType 						= sanitize_text_field($_POST['authType']);
			$customForm 					= sanitize_text_field( $_POST['customForm']);
			$enableShortcode 				= sanitize_text_field($_POST['enableShortcode']);

			switch ($form_selector)
			{
				case '.bbp-login-form':
					update_site_option('mo2f_custom_reg_bbpress',true);
					update_site_option('mo2f_custom_reg_wocommerce',false);
					update_site_option('mo2f_custom_reg_custom',false);
					break;
				case '.woocommerce-form woocommerce-form-register':
					update_site_option('mo2f_custom_reg_bbpress',false);
					update_site_option('mo2f_custom_reg_wocommerce',true);
					update_site_option('mo2f_custom_reg_custom',false);
					break;
				default:
					update_site_option('mo2f_custom_reg_bbpress',false);
					update_site_option('mo2f_custom_reg_wocommerce',false);
					update_site_option('mo2f_custom_reg_custom',true);
			}

			update_site_option('mo2f_custom_form_name', $form_selector);
			update_site_option('mo2f_custom_email_selector', $email_selector);
			update_site_option('mo2f_custom_phone_selector', $phone_selector);
			update_site_option('mo2f_custom_submit_selector', $submit_selector);
			update_site_option('mo2f_custom_auth_type', $authType);

			update_site_option('enable_form_shortcode',$enableShortcode);
			$saved = true;
		}
		else
		{
			$submit_selector = 'NA';
			$form_selector = 'NA';
			$email_selector = 'NA';
			$authType ='NA';
			$saved = false;
		}
		$return = array(
			'authType' => $authType,
			'submit' => $submit_selector,
			'emailSelector' => $email_selector,
			'phone_selector' => $phone_selector,
			'form' => $form_selector,
			'saved' => $saved,
			'customForm' => $customForm,
			'enableShortcode' => $enableShortcode
		);

		return wp_send_json($return);
	}

	function mo2f_check_user_exist_miniOrange()
	{
		$nonce = sanitize_text_field($_POST['nonce']);

		if ( ! wp_verify_nonce( $nonce, 'checkuserinminiOrangeNonce' ) ) {
			$error = new WP_Error();
			$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
			echo "NonceDidNotMatch";
			exit;
		}

		if(!get_option('mo2f_customerKey')){
			echo "NOTLOGGEDIN";
			exit;
		}
		$user = wp_get_current_user();
		global $Mo2fdbQueries;
		$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
    	if($email == '' or is_null($email))
    		$email = $user->user_email;



    	if(isset($_POST['email']))
    	{
    		$email  = sanitize_email($_POST['email']);
    	}

    	$enduser    = new Two_Factor_Setup();
		$check_user = json_decode( $enduser->mo_check_user_already_exist( $email ), true );


		if(strcasecmp($check_user['status'], 'USER_FOUND_UNDER_DIFFERENT_CUSTOMER') == 0 ){
           echo "alreadyExist";
           exit;
	    }
	    else
	    {

	    	update_user_meta($user->ID,'mo2f_email_miniOrange',$email);
	    	echo "USERCANBECREATED";
	    	exit;
	    }

	}
function mo2f_shift_to_onprem(){

		$current_user 	= wp_get_current_user();
		$current_userID = $current_user->ID;
		$miniorangeID = get_option( 'mo2f_miniorange_admin' );
		if(is_null($miniorangeID) or $miniorangeID =='')
			$is_customer_admin = true;
		else
			$is_customer_admin = $miniorangeID == $current_userID ? true : false;
		if($is_customer_admin)
		{
			update_option('is_onprem', 1);
			update_option( 'mo2f_remember_device',0);
			wp_send_json('true');
		}
		else
		{
			$adminUser = get_user_by('id',$miniorangeID);
			$email = $adminUser->user_email;
			wp_send_json($email);
		}

	}

     
    function mo2f_delete_log_file(){
         $nonce = sanitize_text_field($_POST['mo2f_nonce_delete_log']);
            
            if ( ! wp_verify_nonce( $nonce, 'mo2f-nonce-delete-log' ) ) {
                $error = new WP_Error();
                $error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

            }else{
                $debug_log_path = wp_upload_dir();
                $debug_log_path = $debug_log_path['basedir'];
                $file_name = 'miniorange_debug_log.txt';
                $status = file_exists( $debug_log_path.DIRECTORY_SEPARATOR.$file_name);
               if($status){
                  unlink($debug_log_path.DIRECTORY_SEPARATOR.$file_name);
                  wp_send_json('true');
                }
                else{
                  wp_send_json('false');
                }
             }   
    }
     function mo2f_enable_disable_debug_log(){

			$nonce = sanitize_text_field($_POST['mo2f_nonce_enable_debug_log']);
            
			if ( ! wp_verify_nonce( $nonce, 'mo2f-nonce-enable-debug-log' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

			}

			$enable = sanitize_text_field($_POST['mo2f_enable_debug_log']);
			if($enable == 'true'){
				update_site_option('mo2f_enable_debug_log' , 1);
				wp_send_json('true');
			}
			else{
				update_site_option('mo2f_enable_debug_log' , 0);
				wp_send_json('false');
			}
		}

		function mo2f_enable_disable_twofactor(){
			$nonce = sanitize_text_field($_POST['mo2f_nonce_enable_2FA']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-nonce-enable-2FA' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				wp_send_json("error");
			}
			if(!current_user_can( 'administrator' ))
				wp_send_json('error');
			$enable = sanitize_text_field($_POST['mo2f_enable_2fa']);
			if($enable == 'true'){
				update_option('mo2f_activate_plugin' , 1);
				wp_send_json('true');
			}
			else{
				update_option('mo2f_activate_plugin' , 0);
				wp_send_json('false');
			}
		}

		function mo2f_enable_disable_twofactor_prompt_on_login(){
			
			global $Mo2fdbQueries;
			$user = wp_get_current_user();
			$nonce = sanitize_text_field($_POST['mo2f_nonce_enable_2FA_prompt_on_login']);
			$auth_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
			if ( ! wp_verify_nonce( $nonce, 'mo2f-enable-2FA-on-login-page-option-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

			}
			$enable= sanitize_text_field($_POST['mo2f_enable_2fa_prompt_on_login']);
			if(!($auth_method == "Google Authenticator" || $auth_method =="miniOrange Soft Token" || $auth_method == "Authy Authenticator"))
			{
			update_site_option('mo2f_enable_2fa_prompt_on_login_page' , false);
			if(!MO2F_IS_ONPREM)
				wp_send_json('false_method_cloud');
			else
				wp_send_json('false_method_onprem');

			}
			else if($enable == 'true'){
				update_site_option('mo2f_enable_2fa_prompt_on_login_page' , true);
				wp_send_json('true');
			}
			else{
				update_site_option('mo2f_enable_2fa_prompt_on_login_page' , false);
				wp_send_json('false');
			}
		}

		function mo2f_enable_disable_inline(){
			$nonce = sanitize_text_field($_POST['mo2f_nonce_enable_inline']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-nonce-enable-inline' ) ) {
				wp_send_json("error");
			}
			$enable = sanitize_text_field($_POST['mo2f_inline_registration']);
			if($enable == 'true'){
				update_site_option('mo2f_inline_registration' , 1);
				wp_send_json('true');
			}
			else{
				update_site_option('mo2f_inline_registration' , 0);
				wp_send_json('false');
			}
		}
		function mo2f_enable_disable_configurd_methods(){
			$nonce = sanitize_text_field($_POST['nonce']);

			if ( ! wp_verify_nonce( $nonce, 'WAFsettingNonce_configurd_methods' ) ) {
				wp_send_json_error("error");
			}
			$enable = sanitize_text_field($_POST['mo2f_nonce_enable_configured_methods']);

			if($enable == 'true'){
				update_site_option('mo2f_nonce_enable_configured_methods' ,true);
				wp_send_json('true');
			}
			else{
				update_site_option('mo2f_nonce_enable_configured_methods' , false);
				wp_send_json('false');
			}
		}

		function mo2f_role_based_2_factor(){
			if ( !wp_verify_nonce($_POST['nonce'],'unlimittedUserNonce') ){
    			   			wp_send_json('ERROR');
    			   			return;
                        }
					    global $wp_roles;
		                if (!isset($wp_roles))
			             $wp_roles = new WP_Roles();
                        foreach($wp_roles->role_names as $id => $name) {
                        	update_option('mo2fa_'.$id, 0);
                        }
                        if(isset($_POST['enabledrole'])){
                        $enabledrole = wp_unslash($_POST['enabledrole']);
                         }
                         else{
                         	$enabledrole = array();
                         }

                         foreach($enabledrole as $role){
   							 update_option($role, 1);
  						}
                        wp_send_json('true');
                        return;
		 }
		function mo2f_single_user()
		{
			if(!wp_verify_nonce($_POST['nonce'],'singleUserNonce'))
			{
				echo "NonceDidNotMatch";
				exit;
			}
			else
			{
				$current_user 	= wp_get_current_user();
				$current_userID = $current_user->ID;
				$miniorangeID = get_option( 'mo2f_miniorange_admin' );
				$is_customer_admin = $miniorangeID == $current_userID ? true : false;

				if(is_null($miniorangeID) or $miniorangeID =='')
					$is_customer_admin = true;

				if($is_customer_admin)
				{
					update_option('is_onprem', 0);
					wp_send_json('true');
				}
				else
				{
					$adminUser = get_user_by('id',$miniorangeID);
					$email = $adminUser->user_email;
					wp_send_json($email);
				}

			}
		}

		function mo2f_unlimitted_user()
		{
			if(!wp_verify_nonce($_POST['nonce'],'unlimittedUserNonce'))
			{
				echo "NonceDidNotMatch";
				exit;
			}
			else
			{
				if(sanitize_text_field($_POST['enableOnPremise']) == 'on')
				{
					global $wp_roles;
					if (!isset($wp_roles))
						$wp_roles = new WP_Roles();
					foreach($wp_roles->role_names as $id => $name) {
					add_site_option('mo2fa_'.$id, 1);
						if($id == 'administrator'){
							add_option('mo2fa_'.$id.'_login_url',admin_url());
						}else{
							add_option('mo2fa_'.$id.'_login_url',home_url());
						}
					}
					echo "OnPremiseActive";
					exit;
				}
				else
				{
					echo "OnPremiseDeactive";
					exit;
				}
			}
		}

function mo2f_save_email_verification()
	{
                
			if(!wp_verify_nonce($_POST['nonce'],'EmailVerificationSaveNonce'))
			{
			echo "NonceDidNotMatch";
			exit;
			}
			else
			{
				$user_id = get_current_user_id();              
				$twofactor_transactions = new Mo2fDB;
				$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user_id);

				if($exceeded){
				echo "USER_LIMIT_EXCEEDED";
				exit;
				}
				$email = sanitize_email($_POST['email']);
				$currentMethod = sanitize_text_field($_POST['current_method']);
				$error = false;
				
				$customer_key               = get_site_option( 'mo2f_customerKey' );
				$api_key                    = get_site_option( 'mo2f_api_key' );

			  
						if (!filter_var($email, FILTER_VALIDATE_EMAIL))
						{
						$error = true;
						}
		if($email!='' && !$error)
		{
		global $Mo2fdbQueries;
		if($currentMethod == 'EmailVerification')
		{


			
			if(MO2F_IS_ONPREM){
		
			update_user_meta($user_id,'tempEmail',$email);
			$enduser = new Customer_Setup();
			$content = $enduser->send_otp_token($email,'OUT OF BAND EMAIL',$customer_key,$api_key);
			$decoded = json_decode($content,true);
			if($decoded['status'] == 'FAILED'){
			echo "smtpnotset";
			exit;
			}
			
			update_user_meta($user_id,'Mo2fTxid',$decoded['txId']);
			$otpToken = '';
			$otpToken .= rand(0,9);
			update_user_meta($user_id,'Mo2fOtpToken',$otpToken);
			
			}



			//for cloud
			if(! MO2F_IS_ONPREM){
			$enduser = new Two_Factor_Setup();
			$enduser->mo2f_update_userinfo($email, "OUT OF BAND EMAIL",null,null,null);
			}
			   // }

			echo "settingsSaved";
			exit;
			}
		elseif ($currentMethod == 'OTPOverEmail')
		{
				update_user_meta($user_id,'tempEmail',$email);
				$enduser = new Customer_Setup();
				                  $content = $enduser->send_otp_token($email,"OTP Over Email",$customer_key,$api_key);

				                  $decoded = json_decode($content,true);
				 if($decoded['status'] == 'FAILED'){


				echo "smtpnotset";
				exit;
				               
				}
				    MO2f_Utility::mo2f_debug_file('OTP has been sent successfully over Email');
				    update_user_meta( $user_id, 'configure_2FA', 1 );
				    update_user_meta($user_id,'Mo2fOtpOverEmailtxId',$decoded['txId']);
				                

		}
			update_user_meta($user_id,'tempRegEmail',$email);
			echo "settingsSaved";
			exit;
			}
		else
		{
		echo "invalidEmail";
		exit;
		}

	}

}

		function CheckEVStatus()
		{
			if(isset($_POST['txid']))
			{
				$txid = sanitize_text_field($_POST['txid']);
				$status = get_site_option($txid);
				if($status ==1 || $status ==0)
				delete_site_option($txid);
				echo esc_html($status);
				exit();
			}
			echo "empty txid";
			exit;
		}


}

new mo_2f_ajax;
?>
