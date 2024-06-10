<?php
/** miniOrange enables user to log in through mobile authentication as an additional layer of security over password.
 * Copyright (C) 2015  miniOrange
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * @package        miniOrange OAuth
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
/**
 * This library is miniOrange Authentication Service.
 * Contains Request Calls to Customer service.
 **/

include 'two_fa_login.php';
class Miniorange_Password_2Factor_Login {

	private $mo2f_kbaquestions;
	private $mo2f_userID;
	private $mo2f_rbastatus;
	private $mo2f_transactionid;

	function mo2f_inline_login(){
		global $moWpnsUtility;
		$email 	  = sanitize_email( $_POST['email'] );
		$password = sanitize_text_field( $_POST['password'] );
		$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
		$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
		if( $moWpnsUtility->check_empty_or_null( $email ) || $moWpnsUtility->check_empty_or_null( $password ) )
		{
            $login_message=MoWpnsMessages::showMessage('REQUIRED_FIELDS');
            $login_status="MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS";
			$this->miniorange_pass2login_form_fields($login_status, $login_message,$redirect_to,null,$session_id_encrypt);
			return;
		} 
		$this->inline_get_current_customer($user_id,$email,$password,$redirect_to,$session_id_encrypt);
	}
	function mo2f_inline_register(){
		global $moWpnsUtility, $Mo2fdbQueries;
		$email 			 = sanitize_email($_POST['email']);
		$company 		 = sanitize_text_field($_SERVER["SERVER_NAME"]);
		$password 		 = sanitize_text_field($_POST['password']);
		$confirmPassword = sanitize_text_field($_POST['confirmPassword']);
		$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
		$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
		if( strlen( $password ) < 6 || strlen( $confirmPassword ) < 6)
		{
			$login_message=MoWpnsMessages::showMessage('PASS_LENGTH');
			$login_status="MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS";
			$this->miniorange_pass2login_form_fields($login_status, $login_message,$redirect_to,null,$session_id_encrypt);
		}
		if( $password != $confirmPassword )
		{
			$login_message=MoWpnsMessages::showMessage('PASS_MISMATCH');
			$login_status="MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS";
			$this->miniorange_pass2login_form_fields($login_status, $login_message,$redirect_to,null,$session_id_encrypt);
		}
		if( MoWpnsUtility::check_empty_or_null( $email ) || MoWpnsUtility::check_empty_or_null( $password ) 
			|| MoWpnsUtility::check_empty_or_null( $confirmPassword ) ) 
		{
			$login_message=MoWpnsMessages::showMessage('REQUIRED_FIELDS');
			$login_status="MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS";
			$this->miniorange_pass2login_form_fields($login_status, $login_message,$redirect_to,null,$session_id_encrypt);
		}

		update_option( 'mo2f_email', $email );
		
		update_option( 'mo_wpns_company'    , $company );
		
		update_option( 'mo_wpns_password'   , $password );

		$customer = new MocURL();
		$content  = json_decode($customer->check_customer($email), true);
		$Mo2fdbQueries->insert_user( $user_id );
		switch ($content['status'])
		{
			case 'CUSTOMER_NOT_FOUND':
			      $customerKey = json_decode($customer->create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = ''), true);
				  
			   if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) 
				{
					$this->inline_save_success_customer_config($user_id,$email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
					$this->inline_get_current_customer($user_id,$email,$password,$redirect_to,$session_id_encrypt);
				}
				
				break;
			default:
				$this->inline_get_current_customer($user_id,$email,$password,$redirect_to,$session_id_encrypt);
				break;
		}

	}

	function mo2f_download_backup_codes_inline(){
		$nonce = sanitize_text_field($_POST['mo2f_inline_backup_nonce']);
		$backups= sanitize_text_field($_POST['mo2f_inline_backup_codes']);
		if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-backup-nonce' ) ) {
			$error = new WP_Error();
			$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
			return $error;
		} else {
			$codes=explode(",", $backups);
			$session_id = sanitize_text_field($_POST['session_id']);
			$id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id, 'mo2f_current_user_id');
			
			MO2f_Utility::mo2f_download_backup_codes($id, $codes);
		}
	}

	function mo2f_goto_wp_dashboard(){
		global $Mo2fdbQueries;
		$nonce = sanitize_text_field($_POST['mo2f_inline_wp_dashboard_nonce']);
		if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-wp-dashboard-nonce' ) ) {
			$error = new WP_Error();
			$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
			return $error;
		} else {
			$pass2fa= new Miniorange_Password_2Factor_Login();
            $pass2fa->mo2fa_pass2login(esc_url_raw($_POST['redirect_to']),sanitize_text_field($_POST['session_id']));
			exit;
		}
	}

	function mo2f_use_backup_codes($POSTED){
		$nonce = sanitize_text_field($POSTED['miniorange_backup_nonce']);
		if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-backup-nonce' ) ) {
			$error = new WP_Error();
			$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
			return $error;
		}else {
			$this->miniorange_pass2login_start_session();
			$session_id_encrypt = isset($POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
			$redirect_to = isset($POSTED[ 'redirect_to' ]) ? esc_url_raw($POSTED[ 'redirect_to' ]) : null;
			$mo2fa_login_message = __('Please provide your backup codes.','miniorange-2-factor-authentication');
					$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
		}
	}

	function check_backup_codes_validation($POSTED){
		global $Mo2fdbQueries;
		$nonce = sanitize_text_field($POSTED['miniorange_validate_backup_nonce']);
		$session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
		if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-validate-backup-nonce' ) ) {
			$error = new WP_Error();
			$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
			return $error;
		} else {
			$this->miniorange_pass2login_start_session();
			$currentuser_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
			$redirect_to = isset($POSTED[ 'redirect_to' ]) ? esc_url_raw($POSTED[ 'redirect_to' ]) : null;
			if(isset($currentuser_id)){
				if(MO2f_Utility::mo2f_check_empty_or_null($POSTED[ 'mo2f_backup_code' ]) ){
					$mo2fa_login_message = __('Please provide backup code.','miniorange-2-factor-authentication');
					$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
				}
				$backup_codes=get_user_meta($currentuser_id,'mo2f_backup_codes',true);
				$mo2f_backup_code= sanitize_text_field($POSTED[ 'mo2f_backup_code' ]);
				$mo2f_user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $currentuser_id );
        
        if(!empty($backup_codes)){
        	$mo2f_backup_code = md5($mo2f_backup_code);
					if(in_array($mo2f_backup_code,$backup_codes)){
						foreach ($backup_codes as $key => $value) {
							if($value==$mo2f_backup_code){
								unset($backup_codes[$key]);
								update_user_meta($currentuser_id,'mo2f_backup_codes', $backup_codes);
								$this->mo2fa_pass2login($redirect_to, $session_id_encrypt);
							}
						}
				   }else{
						 $mo2fa_login_message = __('The code you provided is already used or incorrect.','miniorange-2-factor-authentication');
						 $mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
						 $this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
						}
				}else{   	
				
								if(isset($mo2f_backup_code)){
									   $generate_backup_code = new Customer_Cloud_Setup();
										 $data = $generate_backup_code->mo2f_validate_backup_codes($mo2f_backup_code,$mo2f_user_email);
				                    
                    if( $data == 'success'){
                        $this->mo2fa_pass2login($redirect_to, $session_id_encrypt);
                    }else if($data == 'error_in_validation'){
                     $mo2fa_login_message = __('Error occurred while validating the backup codes.','miniorange-2-factor-authentication');
										$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
										$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
	                }else if($data == 'used_code'){
	                  $mo2fa_login_message = __('The code you provided is already used or incorrect.','miniorange-2-factor-authentication');
				$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
                }else if($data == 'total_code_used'){
                  $mo2fa_login_message = __('You have used all the backup codes. Please contact <a herf="mailto:2fasupport@xecurify.com">2fasupport@xecurify.com</a>','miniorange-2-factor-authentication');
				$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
                }else if($data == 'backup_code_not_generated'){
                 $mo2fa_login_message = __('Backup code has not generated for you.','miniorange-2-factor-authentication');
				$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
                  }else if($data =='TokenNotFound'){
                   $mo2fa_login_message = __('Validation request authentication failed');
				   $mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
                  }else if($data == 'DBConnectionerror'){
                  	 $mo2fa_login_message = __('Error occurred while establising connection.','miniorange-2-factor-authentication');
										$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
										$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);

				                        }else if($data == 'missingparameter'){
				                        	$mo2fa_login_message = __('Some parameters are missing while validating backup codes.');
										$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
										$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
						}else {
							$current_user = get_userdata($currentuser_id);
							if(in_array('administrator', $current_user->roles)){
								$mo2fa_login_message = __('Error occured while connecting to server. Please follow the <a href="https://faq.miniorange.com/knowledgebase/i-am-locked-cant-access-my-account-what-do-i-do/" target="_blank">Locked out guide</a> to get immediate access to your account.','miniorange-2-factor-authentication');
							}else {
								$mo2fa_login_message = __('Error occured while connecting to server. Please contact your administrator.','miniorange-2-factor-authentication');
							}
							$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
							$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
						}
										
									
								}else{
								$mo2fa_login_message = __('Please enter backup code.','miniorange-2-factor-authentication');
										$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
										$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
								}
              }


			}else{
				$this->remove_current_activity($session_id_encrypt);
				return new WP_Error('invalid_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') . '</strong>: ' . __('Please try again..', 'miniorange-2-factor-authentication'));
			}
		}
	}

	function mo2f_create_backup_codes(){
		$nonce = sanitize_text_field($_POST['miniorange_generate_backup_nonce']);
		if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-generate-backup-nonce' ) ) {
			$error = new WP_Error();
			$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
			return $error;
		}else {
			global $Mo2fdbQueries;

			$redirect_to = esc_url_raw($_POST['redirect_to']);
			$session_id = sanitize_text_field($_POST['session_id']);
			$id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id, 'mo2f_current_user_id');
      		$mo2f_user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $id );
            if(empty($mo2f_user_email)){
                $currentuser = get_user_by( 'id', $id );
                $mo2f_user_email = $currentuser->user_email;
            }
            $generate_backup_code = new Customer_Cloud_Setup();
			$codes = $generate_backup_code->mo_2f_generate_backup_codes($mo2f_user_email, site_url());

			if($codes == 'InternetConnectivityError')
			{
				$mo2fa_login_message = "Error in sending backup codes.";
				$mo2fa_login_status = sanitize_text_field($_POST['login_status']);
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null ,$session_id);
			} else if($codes == 'AllUsed') {

				$mo2fa_login_message = "You have already used all the backup codes for this user and domain.";
				$mo2fa_login_status = sanitize_text_field($_POST['login_status']);
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null ,$session_id);
			}else if($codes == 'UserLimitReached') {
             $mo2fa_login_message = "Backup code generation limit has reached for this domain.";
				$mo2fa_login_status = sanitize_text_field($_POST['login_status']);
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null ,$session_id);
			}else if($codes == 'LimitReached'){
              $mo2fa_login_message = "backup code generation limit has reached for this user.";
				$mo2fa_login_status = sanitize_text_field($_POST['login_status']);
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null ,$session_id);
			}else if($codes == 'invalid_request'){
             $mo2fa_login_message = "Invalid request.";
				$mo2fa_login_status = sanitize_text_field($_POST['login_status']);
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null ,$session_id);
			}
			$codes = explode(' ', $codes);

	        $mo2f_user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $id );
	        if(empty($mo2f_user_email)){
	            $currentuser = get_user_by( 'id', $id );
	            $mo2f_user_email = $currentuser->user_email;
	        }
	        $result = MO2f_Utility::mo2f_email_backup_codes($codes, $mo2f_user_email);

			if($result){
				$mo2fa_login_message = "An email containing the backup codes has been sent. Please click on Use backup codes to login using the backup codes.";
	        	update_user_meta($id, 'mo_backup_code_generated', 1);
			}else{
				$mo2fa_login_message = " If you haven\'t configured SMTP, please set your SMTP to get the backup codes on email.";
				update_user_meta($id, 'mo_backup_code_generated', 0);
			}
			
			$mo2fa_login_status = sanitize_text_field($_POST['login_status']);
			
			$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null ,$session_id);
		}
	}

	function inline_get_current_customer($user_id,$email,$password,$redirect_to,$session_id_encrypt)
	{
		global $Mo2fdbQueries;
		$customer 	 = new MocURL();

		$content     = $customer->get_customer_key($email, $password);
		$customerKey = json_decode($content, true);
		if(json_last_error() == JSON_ERROR_NONE) 
		{
			if(isset($customerKey['phone'])){
				update_option( 'mo_wpns_admin_phone', $customerKey['phone'] );
				$Mo2fdbQueries->update_user_details( $user_id, array( 'mo2f_user_phone' => $customerKey['phone'] ) );
			}
			update_option('mo2f_email',$email);
			$this->inline_save_success_customer_config($user_id,$email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
			$login_message=MoWpnsMessages::showMessage('REG_SUCCESS');
			$login_status="MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS";
			$this->miniorange_pass2login_form_fields($login_status, $login_message,$redirect_to,null,$session_id_encrypt);
		} 
		else 
		{
			$Mo2fdbQueries->update_user_details( $user_id, array( 'mo_2factor_user_registration_status' => 'MO_2_FACTOR_VERIFY_CUSTOMER' ) );
			$login_message=MoWpnsMessages::showMessage('ACCOUNT_EXISTS');
			$login_status="MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS";
			$this->miniorange_pass2login_form_fields($login_status, $login_message,$redirect_to,null,$session_id_encrypt);
		}
	}

	function inline_save_success_customer_config($user_id,$email, $id, $apiKey, $token, $appSecret)
	{
		global $Mo2fdbQueries;
		update_option( 'mo2f_customerKey'  , $id 		  );
		update_option( 'mo2f_api_key'       , $apiKey    );
		update_option( 'mo2f_customer_token'		 , $token 	  );
		update_option( 'mo2f_app_secret'			 , $appSecret );
		update_option( 'mo_wpns_enable_log_requests' , true 	  );
		update_option( 'mo2f_miniorange_admin', $id );
		update_option( 'mo_2factor_admin_registration_status', 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' );
		update_option( 'mo_2factor_user_registration_status', 'MO_2_FACTOR_PLUGIN_SETTINGS' );
		$Mo2fdbQueries->update_user_details( $user_id, array(
			'mo2f_user_email' =>sanitize_email($email)
		) );
	}
	function mo2f_inline_validate_otp(){
		if(isset($_POST['miniorange_inline_validate_otp_nonce'])){
			$nonce = sanitize_text_field($_POST['miniorange_inline_validate_otp_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-validate-otp-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$otp_token = '';
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				$mo2fa_login_message = '';
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				if( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
					$mo2fa_login_message =  __('All the fields are required. Please enter valid entries.','miniorange-2-factor-authentication');
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
				} else{
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
				}
				$current_user = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


				$selected_2factor_method = $Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$current_user);
				
				if($selected_2factor_method == 'OTP Over Telegram')
				{

					$userID 		= $current_user;
					$otp 			= $otp_token;
					$otpToken 		= get_user_meta($userID,'mo2f_otp_token',true);
					$time  			= get_user_meta($userID,'mo2f_telegram_time',true);
					$accepted_time	= time()-300;
					$time 			= (int)$time;
					
					
					if($otp == $otpToken)
					{
						if($accepted_time<$time){
							update_user_meta($userID,'mo2f_chat_id',get_user_meta($userID,'mo2f_temp_chatID',true));
							delete_user_meta($userID,'mo2f_temp_chatID');	
							delete_user_meta($userID,'mo2f_otp_token');
							delete_user_meta($userID,'mo2f_telegram_time');
							$Mo2fdbQueries->update_user_details($userID, array(
						        "mo2f_configured_2FA_method" => 'OTP Over Telegram',
						        'mo2f_OTPOverTelegram_config_status'       => true,											
                                'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS',
                        	) );
                        	$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
						}
						else
						{
							delete_user_meta($userID,'mo2f_otp_token');
							delete_user_meta($userID,'mo2f_telegram_time');
							$mo2fa_login_message =  __('OTP has been expired please initiate a new transaction by clicking on verify button.','miniorange-2-factor-authentication');
						}
					}
					else
					{	
						$mo2fa_login_message =  __('Invalid OTP. Please try again.','miniorange-2-factor-authentication');
					}	
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
				}
				
				$user_phone = $Mo2fdbQueries->get_user_detail('mo2f_user_phone',$current_user);
				$customer = new Customer_Setup();
				$content = json_decode($customer->validate_otp_token( $selected_2factor_method, null, get_user_meta($current_user,'mo2f_transactionId',true), $otp_token, get_site_option('mo2f_customerKey'), get_site_option('mo2f_api_key') ),true);
				if($content['status'] == 'ERROR'){
					$mo2fa_login_message = Mo2fConstants::langTranslate($content['message']);
				}else if(strcasecmp($content['status'], 'SUCCESS') == 0) { //OTP validated 					
						$phone = get_user_meta($current_user,'mo2f_user_phone',true) ; 
						if($user_phone && strlen($user_phone) >= 4){
							if($phone != $user_phone ){
								
								$Mo2fdbQueries->update_user_details( $current_user, array(
									'mobile_registration_status' =>false
									) );
							}
						}
						
					$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$current_user);
					if(!($Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$current_user)=='OTP OVER EMAIL')){
						$Mo2fdbQueries->update_user_details( $current_user, array(
						'mo2f_OTPOverSMS_config_status' =>true,
						'mo2f_user_phone' =>$phone
						) );
					}else{
							$Mo2fdbQueries->update_user_details( $current_user, array('mo2f_email_otp_registration_status'=>true) );
							
						}
						$Mo2fdbQueries->update_user_details($current_user, array(
						        "mo2f_configured_2FA_method" => 'OTP Over SMS',
                                'mo_2factor_user_registration_status'               => 'MO_2_FACTOR_PLUGIN_SETTINGS',
                        ) );
						$TwoF_setup = new Two_Factor_Setup();
						$response = json_decode($TwoF_setup->mo2f_update_userinfo($email,'SMS',null,null,null),true);
					$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
								
				}else{  // OTP Validation failed.
						$mo2fa_login_message =  __('Invalid OTP. Please try again.','miniorange-2-factor-authentication');
				}
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
			} 
		}

	}
	function mo2f_inline_send_otp(){
		if(isset($_POST['miniorange_inline_verify_phone_nonce'])){
			$nonce = sanitize_text_field($_POST['miniorange_inline_verify_phone_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-verify-phone-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				if(isset($_POST['verify_phone']))
				$phone = sanitize_text_field( $_POST['verify_phone'] );				
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;

				$current_user = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$customer = new Customer_Setup();
					$selected_2factor_method = $Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$current_user);
				$parameters = array();
					$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$current_user);
				
				$mo2fa_login_message = '';	
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				if($selected_2factor_method=='SMS' || $selected_2factor_method=='PHONE VERIFICATION' ||$selected_2factor_method== 'SMS AND EMAIL'){
					$phone = sanitize_text_field( $_POST['verify_phone'] );
				if( MO2f_Utility::mo2f_check_empty_or_null( $phone ) ){
					$mo2fa_login_message = __('Please enter your phone number.','miniorange-2-factor-authentication');
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
				}
				$phone = str_replace(' ', '', $phone);
				update_user_meta($current_user,'mo2f_user_phone',$phone);
					}
				if($selected_2factor_method == 'OTP_OVER_SMS' || $selected_2factor_method == 'SMS' ){
					$currentMethod = "SMS";
				}else if($selected_2factor_method == 'SMS AND EMAIL'){
					$currentMethod = "OTP_OVER_SMS_AND_EMAIL";
					$parameters = array("phone" => $phone, "email" => $email);
				}else if($selected_2factor_method == 'PHONE VERIFICATION'){
					$currentMethod = "PHONE_VERIFICATION";
				}else if($selected_2factor_method == 'OTP OVER EMAIL'){
					$currentMethod = "OTP_OVER_EMAIL";
					$parameters = $email;
				}

				else if($selected_2factor_method == 'OTP Over Telegram')
				{
					$currentMethod = "OTP Over Telegram";
					$user_id = $current_user;
					$chatID = sanitize_text_field($_POST['verify_chatID']);
					$otpToken 	= '';
					for($i=1;$i<7;$i++)
					{
						$otpToken 	.= rand(0,9);
					}

					update_user_meta($user_id,'mo2f_otp_token',$otpToken);
					update_user_meta($user_id,'mo2f_telegram_time',time());
					update_user_meta($user_id,'mo2f_temp_chatID',$chatID);
					$url = esc_url(MoWpnsConstants::TELEGRAM_OTP_LINK);
					$postdata = array( 'mo2f_otp_token' => $otpToken,
						'mo2f_chatid' => $chatID
					);

					$args = array(
						'method'      => 'POST',
						'timeout'     => 10,
						'sslverify'   => false,
						'headers'     => array(),
						'body'        => $postdata,
					);
	
					$mo2f_api=new Mo2f_Api();
					$data=$mo2f_api->mo2f_wp_remote_post($url,$args);

					if($data == 'SUCCESS')
						$mo2fa_login_message = 'An OTP has been sent to your given chat ID. Please enter it below for verification.';
					else
						$mo2fa_login_message = 'There were an erroe while sending the OTP. Please confirm your chatID and try again.';
					
						$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
					
				}
				if($selected_2factor_method == 'SMS AND EMAIL'){
					$content = json_decode($customer->send_otp_token($parameters,$currentMethod,get_option( 'mo2f_customerKey'),get_option( 'mo2f_api_key')), true);
				}
				else if($selected_2factor_method == 'OTP OVER EMAIL'){
					$content = json_decode($customer->send_otp_token($email,$currentMethod,get_option( 'mo2f_customerKey'),get_option( 'mo2f_api_key')), true);						
				}
				else{
				$content = json_decode($customer->send_otp_token($phone,$currentMethod,get_option( 'mo2f_customerKey'),get_option( 'mo2f_api_key')), true);
				
				}
				if(json_last_error() == JSON_ERROR_NONE) { /* Generate otp token */
					if($content['status'] == 'ERROR'){
						$mo2fa_login_message = Mo2fConstants::langTranslate($content['message']);
					}else if($content['status'] == 'SUCCESS'){
						update_user_meta($current_user,'mo2f_transactionId',$content['txId']);
						if($selected_2factor_method == 'SMS'){
								if(get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z')>0)
								update_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z',get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z')-1);
								$mo2fa_login_message = __('The One Time Passcode has been sent to','miniorange-2-factor-authentication'). $phone . '.' . __('Please enter the one time passcode below to verify your number.','miniorange-2-factor-authentication');
						}else if($selected_2factor_method == 'SMS AND EMAIL'){
								$mo2fa_login_message = 'The One Time Passcode has been sent to ' . $parameters["phone"] . ' and '. $parameters["email"] . '. Please enter the one time passcode sent to your email and phone to verify.';
						}else if($selected_2factor_method == 'OTP OVER EMAIL'){
								$mo2fa_login_message = __('The One Time Passcode has been sent to ','miniorange-2-factor-authentication') .  $parameters . '.' .  __('Please enter the one time passcode sent to your email to verify.','miniorange-2-factor-authentication');
						}else if($selected_2factor_method== 'PHONE VERIFICATION'){
							$mo2fa_login_message = __('You will receive a phone call on this number ','miniorange-2-factor-authentication') . $phone . '.' .  __('Please enter the one time passcode below to verify your number.','miniorange-2-factor-authentication');
						}
					}else if($content['status'] == 'FAILED'){
						$mo2fa_login_message = __($content['message'],'miniorange-2-factor-authentication');
                    }else{
						$mo2fa_login_message = __('An error occured while validating the user. Please Try again.','miniorange-2-factor-authentication');
					}
				}else{
					$mo2fa_login_message = __('Invalid request. Please try again','miniorange-2-factor-authentication');
				}
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
			}
		}

	}
	function mo2f_inline_validate_kba(){
		if(isset($_POST['mo2f_inline_save_kba_nonce'])){
			$nonce = sanitize_text_field($_POST['mo2f_inline_save_kba_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-save-kba-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$mo2fa_login_message = '';
				$mo2fa_login_status = isset($_POST['mo2f_inline_kba_status']) ? 'MO_2_FACTOR_SETUP_SUCCESS' : 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				$temp_array = array(sanitize_text_field($_POST['mo2f_kbaquestion_1']),sanitize_text_field($_POST['mo2f_kbaquestion_2']),sanitize_text_field($_POST['mo2f_kbaquestion_3']));
				$kba_questions = array();
				foreach($temp_array as $question){
					if(MO2f_Utility::mo2f_check_empty_or_null( $question)){
						$mo2fa_login_message =  __('All the fields are required. Please enter valid entries.','miniorange-2-factor-authentication');
						$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
					}else{
						$ques = sanitize_text_field($question);
						$ques = addcslashes(stripslashes($ques), '"\\');
						array_push($kba_questions, $ques);
					}
				}
				if(!(array_unique($kba_questions) == $kba_questions)){
					$mo2fa_login_message = __('The questions you select must be unique.','miniorange-2-factor-authentication');
					$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
				}
				$temp_array_ans = array(sanitize_text_field($_POST['mo2f_kba_ans1']),sanitize_text_field($_POST['mo2f_kba_ans2']),sanitize_text_field($_POST['mo2f_kba_ans3']));
				$kba_answers = array();
				foreach($temp_array_ans as $answer){
					if(MO2f_Utility::mo2f_check_empty_or_null( $answer)){
						$mo2fa_login_message =  __('All the fields are required. Please enter valid entries.','miniorange-2-factor-authentication');
						$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
					}else{
						$ques = sanitize_text_field($answer);
						$answer = strtolower($answer);
						array_push($kba_answers, $answer);
					}
				}
				$size = sizeof($kba_questions);
				$kba_q_a_list = array();
				for($c = 0; $c < $size; $c++){
					array_push($kba_q_a_list, $kba_questions[$c]);
					array_push($kba_q_a_list, $kba_answers[$c]);
				}

				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


				$current_user = get_user_by('id',$user_id);
				$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$current_user->ID);
				$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
				$Mo2fdbQueries->update_user_details( $current_user->ID, array(
							'mo2f_SecurityQuestions_config_status' =>true,
							'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS'
							) );
				if(!MO2F_IS_ONPREM)
				{
					$kba_q1 = sanitize_text_field($_POST['mo2f_kbaquestion_1']);
					$kba_a1 = sanitize_text_field( $_POST['mo2f_kba_ans1'] );
					$kba_q2 = sanitize_text_field($_POST['mo2f_kbaquestion_2']);
					$kba_a2 = sanitize_text_field( $_POST['mo2f_kba_ans2'] );
					$kba_q3 = sanitize_text_field( $_POST['mo2f_kbaquestion_3'] );
					$kba_a3 = sanitize_text_field( $_POST['mo2f_kba_ans3'] );

					$kba_q1 = addcslashes( stripslashes( $kba_q1 ), '"\\' );
					$kba_q2 = addcslashes( stripslashes( $kba_q2 ), '"\\' );
					$kba_q3 = addcslashes( stripslashes( $kba_q3 ), '"\\' );

					$kba_a1 = addcslashes( stripslashes( $kba_a1 ), '"\\' );
					$kba_a2 = addcslashes( stripslashes( $kba_a2 ), '"\\' );
					$kba_a3 = addcslashes( stripslashes( $kba_a3 ), '"\\' );

					$kba_registration = new Two_Factor_Setup();
					$kba_reg_reponse  = json_decode( $kba_registration->register_kba_details( $email, $kba_q1, $kba_a1, $kba_q2, $kba_a2, $kba_q3, $kba_a3, $user_id ), true );

					if ( json_last_error() == JSON_ERROR_NONE ) {
						
						if ( $kba_reg_reponse['status'] == 'SUCCESS' ) {
							$response = json_decode( $kba_registration->mo2f_update_userinfo( $email, 'KBA', null, null, null ), true );
						}

					}
				}
				
				$kba_q1 = $kba_q_a_list[0];
				$kba_a1 = md5($kba_q_a_list[1]);
				$kba_q2 = $kba_q_a_list[2];
				$kba_a2 = md5($kba_q_a_list[3]);
				$kba_q3 = $kba_q_a_list[4];
				$kba_a3 = md5($kba_q_a_list[5]);
						$question_answer  = array($kba_q1 => $kba_a1 ,$kba_q2 => $kba_a2 , $kba_q3 => $kba_a3 );
						update_user_meta( $current_user->ID , 'mo2f_kba_challenge', $question_answer  );
						if(!isset($_POST['mo2f_inline_kba_status'])){	
							update_user_meta($current_user->ID,'mo2f_2FA_method_to_configure','Security Questions');
							$Mo2fdbQueries->update_user_details( $current_user->ID, array( 'mo2f_configured_2FA_method' => 'Security Questions' ) );
						}	
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
			}
		}
	}

	function mo2f_inline_validate_mobile_authentication(){
		if(isset($_POST['mo_auth_inline_mobile_registration_complete_nonce'])){
			$nonce = sanitize_text_field($_POST['mo_auth_inline_mobile_registration_complete_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-mobile-registration-complete-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt );
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$selected_2factor_method = $Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$user_id);
				$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$user_id);
				$mo2fa_login_message = '';
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				$enduser = new Two_Factor_Setup();
				if($selected_2factor_method == 'SOFT TOKEN')
					$selected_2factor_method_onprem = 'miniOrange Soft Token';
				else if($selected_2factor_method == 'PUSH NOTIFICATIONS')
					$selected_2factor_method_onprem = 'miniOrange Push Notification';
				else if($selected_2factor_method == 'MOBILE AUTHENTICATION')
					$selected_2factor_method_onprem = 'miniOrange QR Code Authentication';
			
				$response = json_decode($enduser->mo2f_update_userinfo($email,$selected_2factor_method,null,null,null),true);
				if(json_last_error() == JSON_ERROR_NONE) { /* Generate Qr code */
						if($response['status'] == 'ERROR'){
							$mo2fa_login_message = Mo2fConstants::langTranslate($response['message']);
						}else if($response['status'] == 'SUCCESS'){
							$Mo2fdbQueries->update_user_details( $user_id, array(
									'mobile_registration_status' =>true,
									'mo2f_miniOrangeQRCodeAuthentication_config_status' => true,
									'mo2f_miniOrangeSoftToken_config_status'            => true,
									'mo2f_miniOrangePushNotification_config_status'     => true,
									'mo2f_configured_2FA_method' =>$selected_2factor_method_onprem ,
									'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS',
							) );
//							
							$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
						}else{
							$mo2fa_login_message = __('An error occured while validating the user. Please Try again.','miniorange-2-factor-authentication');
						}
				}else{
						$mo2fa_login_message = __('Invalid request. Please try again','miniorange-2-factor-authentication');
				}
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt);
			}
		}

	}

	function mo2f_duo_mobile_send_push_notification_for_inline_form(){
		if(isset($_POST['duo_mobile_send_push_notification_inline_form_nonce'])){
			$nonce = sanitize_text_field($_POST['duo_mobile_send_push_notification_inline_form_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo2f-send-duo-push-notification-inline-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
                  
                  global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt );
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
			
				$Mo2fdbQueries->update_user_details( $user_id, array(
									'mobile_registration_status' =>true,
							     ) );					
				$mo2fa_login_message = '';

				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
						
				
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt);

			}	
	}
	}

	function mo2f_inline_validate_duo_authentication(){
		if(isset($_POST['mo_auth_inline_duo_auth_mobile_registration_complete_nonce'])){
			$nonce = sanitize_text_field($_POST['mo_auth_inline_duo_auth_mobile_registration_complete_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-duo_auth-registration-complete-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
                  
                  global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt );
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$selected_2factor_method = $Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$user_id);
				$email = sanitize_email($Mo2fdbQueries->get_user_detail('mo2f_user_email',$user_id));
				$Mo2fdbQueries->update_user_details( $user_id, array(
									'mobile_registration_status' =>true,
							     ) );					
				$mo2fa_login_message = '';

				  include_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'two_fa_duo_handler.php';
				        $ikey = get_site_option('mo2f_d_integration_key');
		                $skey = get_site_option('mo2f_d_secret_key');
		                $host = get_site_option('mo2f_d_api_hostname');

				        
                      
                		 $duo_preauth = preauth( $email ,true,  $skey, $ikey, $host);
                		 
                		 
                        if(isset($duo_preauth['response']['stat']) && $duo_preauth['response']['stat'] == 'OK'){

                            if(isset($duo_preauth['response']['response']['status_msg']) && $duo_preauth['response']['response']['status_msg'] == 'Account is active'){
                            	$mo2fa_login_message = $email.' user is already exists, please go for step B duo will send push notification on your configured mobile.';

                            }else if(isset($duo_preauth['response']['response']['enroll_portal_url'])){
                        	$duo_enroll_url = $duo_preauth['response']['response']['enroll_portal_url'];
                            update_user_meta( $user_id , 'user_not_enroll_on_duo_before', $duo_enroll_url  );
                            update_user_meta( $user_id , 'user_not_enroll', true  );

                            }else{
                            $mo2fa_login_message = 'Your account is inactive from duo side, please contact to your administrator.';
                            }
                           
                        }else{
                            $mo2fa_login_message = 'Error through during preauth.';

                        }
						
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
						
				
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt);

			}	
	}
}	

	function mo2f_inline_setup_success($current_user_id,$redirect_to,$session_id){
		global $Mo2fdbQueries;
				$Mo2fdbQueries->update_user_details( $current_user_id, array('mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS') );
		
		$mo2f_user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user_id );
		if(empty($mo2f_user_email)){
			$currentuser = get_user_by( 'id', $current_user_id );
			$mo2f_user_email = $currentuser->user_email;
		}
		$generate_backup_code = new Customer_Cloud_Setup();
		$codes = $generate_backup_code->mo_2f_generate_backup_codes($mo2f_user_email, site_url());
		
		$code_generate = get_user_meta($current_user_id, 'mo_backup_code_generated', false);
		
		
		if(empty($code_generate) && $codes != "InternetConnectivityError" && $codes != 'DBConnectionIssue' && $codes != 'UnableToFetchData' && $codes != 'UserLimitReached' && $codes != 'ERROR' && $codes != 'LimitReached' && $codes != 'AllUsed' && $codes != 'invalid_request'){
			$mo2fa_login_message = '';
			$mo2fa_login_status = 'MO_2_FACTOR_GENERATE_BACKUP_CODES';
			$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id);
		}else{
			$pass2fa= new Miniorange_Password_2Factor_Login();
            $pass2fa->mo2fa_pass2login($redirect_to,$session_id);
			update_user_meta($id, 'error_during_code_generation',$codes);
			exit;
		}
	}

	function mo2f_inline_get_qr_code_for_mobile($email,$id){
		$registerMobile = new Two_Factor_Setup();
		$content = $registerMobile->register_mobile($email);
		$response = json_decode($content, true);
		$message = '';
		$miniorageqr=array();
		if(json_last_error() == JSON_ERROR_NONE) {
			if($response['status'] == 'ERROR'){
				$miniorageqr['message']=Mo2fConstants::langTranslate($response['message']);;
                delete_user_meta( $id, 'miniorageqr' );
			}else{
				if($response['status'] == 'IN_PROGRESS'){

				    $miniorageqr['message']='';
				    $miniorageqr['mo2f-login-qrCode']=$response['qrCode'];
				    $miniorageqr['mo2f-login-transactionId']=$response['txId'];
				    $miniorageqr['mo2f_show_qr_code']='MO_2_FACTOR_SHOW_QR_CODE';
				    update_user_meta($id,'miniorageqr',$miniorageqr);
				}else{
					$miniorageqr['message']=__('An error occured while processing your request. Please Try again.','miniorange-2-factor-authentication');
					delete_user_meta( $id, 'miniorageqr' );
				}
			}
		}
		return $miniorageqr;
	}

	function inline_mobile_configure(){
		if(isset($_POST['miniorange_inline_show_qrcode_nonce'])){
			$nonce = sanitize_text_field($_POST['miniorange_inline_show_qrcode_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-show-qrcode-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$current_user = get_user_by('id',$user_id);
				$mo2fa_login_message = '';
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				$user_registration_status = $Mo2fdbQueries->get_user_detail('mo_2factor_user_registration_status',$current_user->ID);
				if($user_registration_status == 'MO_2_FACTOR_INITIALIZE_TWO_FACTOR') {
					$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$current_user->ID);
					$miniorageqr = $this->mo2f_inline_get_qr_code_for_mobile($email,$current_user->ID);
					$mo2fa_login_message=$miniorageqr['message'];
					MO2f_Utility::mo2f_set_transient( $session_id_encrypt,'mo2f_transactionId', $miniorageqr['mo2f-login-transactionId'] );

					$this->mo2f_transactionid=$miniorageqr['mo2f-login-transactionId'];
				}else{
					$mo2fa_login_message = __('Invalid request. Please register with miniOrange before configuring your mobile.','miniorange-2-factor-authentication');
				}
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,$miniorageqr,$session_id_encrypt);
			}
		}
	}

	function inline_validate_and_set_ga(){
		if(isset($_POST['mo2f_inline_validate_ga_nonce'])){
			$nonce = sanitize_text_field($_POST['mo2f_inline_validate_ga_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-google-auth-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$otpToken = sanitize_text_field($_POST['google_auth_code']);
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


				$current_user = get_user_by('id',$user_id);
				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$ga_secret = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'secret_ga');

				$mo2fa_login_message = '';
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				if(MO2f_Utility::mo2f_check_number_length($otpToken)){
					$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$current_user->ID);
					$google_auth = new Miniorange_Rba_Attributes();
					$google_response = json_decode($google_auth->mo2f_validate_google_auth($email,$otpToken,$ga_secret),true);
					if(json_last_error() == JSON_ERROR_NONE) {
						if($google_response['status'] == 'SUCCESS'){
							$response = $google_response;
							if(json_last_error() == JSON_ERROR_NONE || MO2F_IS_ONPREM) {
								if($response['status'] == 'SUCCESS'){
									$Mo2fdbQueries->update_user_details( $current_user->ID, array(
									'mo2f_GoogleAuthenticator_config_status' => true,
									'mo2f_configured_2FA_method' => 'Google Authenticator',
									'mo2f_AuthyAuthenticator_config_status' => false,
									'mo_2factor_user_registration_status'               => 'MO_2_FACTOR_PLUGIN_SETTINGS'
									) );

									if(MO2F_IS_ONPREM){
										update_user_meta($current_user->ID,'mo2f_2FA_method_to_configure','GOOGLE AUTHENTICATOR');
										$gauth_obj= new Google_auth_onpremise();
										$gauth_obj->mo_GAuth_set_secret($current_user->ID, $ga_secret);
									}
									update_user_meta($current_user->ID,'mo2f_external_app_type','GOOGLE AUTHENTICATOR');
									$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';

									//When user sets method of another admin from USers section.
									if(!empty(get_user_meta($current_user->ID,'mo2fa_set_Authy_inline'))){
										$Mo2fdbQueries->update_user_details( $current_user->ID, array(
										'mo2f_GoogleAuthenticator_config_status' => false,
										'mo2f_AuthyAuthenticator_config_status'  => true,
										'mo2f_configured_2FA_method'             => "Authy Authenticator",
										'user_registration_with_miniorange'      => 'SUCCESS',
										'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS'
									) );
									update_user_meta( $current_user->ID, 'mo2f_external_app_type', "Authy Authenticator" );
									delete_user_meta($current_user->ID ,'mo2fa_set_Authy_inline');
								}
								}else{
									$mo2fa_login_message = __('An error occured while setting up Google/Authy Authenticator. Please Try again.','miniorange-2-factor-authentication');
								}
							}else{
								$mo2fa_login_message = __('An error occured while processing your request. Please Try again.','miniorange-2-factor-authentication');
							}
						}else{
							$mo2fa_login_message = __('An error occured while processing your request. Please Try again.','miniorange-2-factor-authentication');	
						}
					}else{
						$mo2fa_login_message = __('An error occured while validating the user. Please Try again.','miniorange-2-factor-authentication');
					}
				}else{
					$mo2fa_login_message = __('Only digits are allowed. Please enter again.','miniorange-2-factor-authentication');
				}
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id_encrypt);
			}
		}
	}

	function back_to_select_2fa(){
		if( isset($_POST['miniorange_inline_two_factor_setup'])){ /* return back to choose second factor screen */
			$nonce = sanitize_text_field($_POST['miniorange_inline_two_factor_setup']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-setup-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;

				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$current_user = get_user_by('id',$user_id);
				$Mo2fdbQueries->update_user_details( $current_user->ID, array( "mo2f_configured_2FA_method" => '' ) );
				$mo2fa_login_message = '';
				$mo2fa_login_status ='MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
				}
			}
	}

function create_user_in_miniOrange($current_user_id,$email,$currentMethod)
{
	$tempEmail  = get_user_meta($current_user_id,'mo2f_email_miniOrange',true);
    if(isset($tempEmail) and $tempEmail != '')
    	$email = $tempEmail;
    global $Mo2fdbQueries;

        $enduser    = new Two_Factor_Setup();
	if($current_user_id == get_option('mo2f_miniorange_admin'))
		$email = get_option('mo2f_email');

        $check_user = json_decode( $enduser->mo_check_user_already_exist( $email ), true );

        if(json_last_error() == JSON_ERROR_NONE){



            if($check_user['status'] == 'ERROR'){
              return $check_user;

            }
            else if(strcasecmp($check_user['status' ], 'USER_FOUND') == 0){
                                        
                $Mo2fdbQueries->update_user_details( $current_user_id, array(
                'user_registration_with_miniorange' =>'SUCCESS',
                'mo2f_user_email' =>$email,
                'mo_2factor_user_registration_status' =>'MO_2_FACTOR_INITIALIZE_TWO_FACTOR'
                ) );
                update_site_option(base64_encode("totalUsersCloud"),get_site_option(base64_encode("totalUsersCloud"))+1);

                $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
                return $check_user;
            }
            else if(strcasecmp($check_user['status'], 'USER_NOT_FOUND') == 0){
	            $current_user = get_user_by('id',$current_user_id);
                $content = json_decode($enduser->mo_create_user($current_user,$email), true);

                if(json_last_error() == JSON_ERROR_NONE) {
                    if(strcasecmp($content['status'], 'SUCCESS') == 0) {
                    update_site_option(base64_encode("totalUsersCloud"),get_site_option(base64_encode("totalUsersCloud"))+1);
                    $Mo2fdbQueries->update_user_details( $current_user_id, array(
                        'user_registration_with_miniorange' =>'SUCCESS',
                        'mo2f_user_email' =>$email,
                        'mo_2factor_user_registration_status' =>'MO_2_FACTOR_INITIALIZE_TWO_FACTOR'
                    ) );
                    
                        $mo2fa_login_message = '';
                        $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
                        return $check_user;
                    }else{
                        $check_user['status']='ERROR';
                        $check_user['message']='There is an issue in user creation in miniOrange. Please skip and contact miniorange';
                        return $check_user;
                    }
                }
                    

            }
            else if(strcasecmp($check_user['status'], 'USER_FOUND_UNDER_DIFFERENT_CUSTOMER') == 0){
	            $mo2fa_login_message = __('The email associated with your account is already registered. Please contact your admin to change the email.','miniorange-2-factor-authentication');
	            $check_user['status']='ERROR';
	            $check_user['message']=$mo2fa_login_message;
                return $check_user;
            }

        }

}
	function mo2f_skip_2fa_setup()
	{
		if(isset($_POST['miniorange_skip_2fa_nonce'])){
			$nonce = sanitize_text_field($_POST['miniorange_skip_2fa_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-skip-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} 
			else{
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				global $Mo2fdbQueries;
				$redirect_to = esc_url_raw($_POST['redirect_to']);
				$session_id_encrypt = sanitize_text_field($session_id_encrypt);
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
                $currentuser=get_user_by('id',$user_id);
			
				$Mo2fdbQueries->update_user_details( $user_id, array('mo2f_2factor_enable_2fa_byusers' => 1) );
				
				$this->mo2fa_pass2login($redirect_to,$session_id_encrypt);
			}
		}
	}

	function save_inline_2fa_method(){
		if(isset($_POST['miniorange_inline_save_2factor_method_nonce'])){
			$nonce = sanitize_text_field($_POST['miniorange_inline_save_2factor_method_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-inline-save-2factor-method-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
				

				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$mo2fa_login_message = '';
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';

				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');


				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$current_user = get_user_by('id',$user_id);
				$currentUserId = $current_user->ID;
				$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$current_user->ID);
				$user_registration_with_miniorange = $Mo2fdbQueries->get_user_detail('user_registration_with_miniorange',$current_user->ID);
				if($user_registration_with_miniorange == 'SUCCESS'){
					$selected_method = isset($_POST['mo2f_selected_2factor_method']) ? sanitize_text_field($_POST['mo2f_selected_2factor_method']) : 'NONE';

					if($selected_method == 'OUT OF BAND EMAIL'){
						if(!MO2F_IS_ONPREM)
			            {
			                $current_user = get_userdata($currentUserId);
			                $email = $current_user->user_email;
			            	$response = $this->create_user_in_miniOrange($currentUserId,$email,$selected_method);

			            	if($response['status']=='ERROR') {
								  $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
								  $mo2fa_login_message=$response['message'].'Skip the two-factor for login';
                            }
			            	else
			            	{
			            		$enduser = new Two_Factor_Setup();

								$Mo2fdbQueries->update_user_details( $currentUserId, array(
											'mo2f_email_verification_status' =>true,
											'mo2f_configured_2FA_method' =>'Email Verification',
											'mo2f_user_email'                     => $email
											) );
								$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
			            	}
			            }
						else
						{
							$enduser = new Two_Factor_Setup();

							$Mo2fdbQueries->update_user_details( $currentUserId, array(
										'mo2f_email_verification_status' =>true,
										'mo2f_configured_2FA_method' =>'Email Verification',
										'mo2f_user_email'                     => $email
										) );
							$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
						}
					}
					else if($selected_method == 'OTP OVER EMAIL'){
						$email = $current_user->user_email;
						if(!MO2F_IS_ONPREM)
			            {
			                $current_user = get_userdata($currentUserId);
			            	$response = $this->create_user_in_miniOrange($currentUserId,$email,$selected_method);
			            	if($response['status']=='ERROR') {
								  $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
								  $mo2fa_login_message=$response['message'].'Skip the two-factor for login';
                            }
			            	else
			            	{
			            		$user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user->ID );
								if($user_email != '' and !is_null($user_email))
									$email = $user_email;
			            		$this->mo2f_otp_over_email_send($email,$redirect_to,$session_id_encrypt,$current_user);
			  
			            	}
			            }
						else
						{
							$this->mo2f_otp_over_email_send($email,$redirect_to,$session_id_encrypt,$current_user);
						}
					}else if($selected_method == "GOOGLE AUTHENTICATOR"){
						$this->miniorange_pass2login_start_session();
						$mo2fa_login_message = '';
						$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
						$google_auth = new Miniorange_Rba_Attributes();
						
						$gauth_name= get_site_option('mo2f_google_appname');
						$google_account_name= $gauth_name ? $gauth_name : 'miniOrangeAu';

						$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$current_user->ID);

						if ( MO2F_IS_ONPREM ) { //this should not be here
							$Mo2fdbQueries->update_user_details( $current_user->ID, array(
								'mo2f_configured_2FA_method' =>$selected_method,
							) );
							include_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'gaonprem.php';
							$gauth_obj = new Google_auth_onpremise();

							$onpremise_secret              = $gauth_obj->createSecret();
							$issuer                        = get_site_option( 'mo2f_GA_account_name', 'miniOrangeAu' );
							$url                           = $gauth_obj->geturl( $onpremise_secret, $issuer, $email );
							$mo2f_google_auth              = array();
							$mo2f_google_auth['ga_qrCode'] = $url;
							$mo2f_google_auth['ga_secret'] = $onpremise_secret;

							MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'secret_ga', $onpremise_secret);
							MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'ga_qrCode', $url);

						}else{
								$current_user = get_userdata($currentUserId);
								$email = $current_user->user_email;
								$tempemail = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $currentUserId );
								
				    			if(!isset($tempemail) and !is_null($tempemail) and $tempemail != '')
				    			{
				    				$email = $tempemail;
				    			}
				    				
								$response = $this->create_user_in_miniOrange($currentUserId,$email,$selected_method);
                               if($response['status']=='ERROR') {
								    $mo2fa_login_message=$response['message'];
								    $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
								  
                                }else{
	                               $Mo2fdbQueries->update_user_details( $current_user->ID, array(
		                               'mo2f_configured_2FA_method' =>$selected_method,
	                               ) );
	                               $google_response = json_decode( $google_auth->mo2f_google_auth_service( $email, $google_account_name ), true );
	                               if ( json_last_error() == JSON_ERROR_NONE ) {
		                               if ( $google_response['status'] == 'SUCCESS' ) {

										$mo2f_google_auth              = array();
										$mo2f_google_auth['ga_qrCode'] = $google_response['qrCodeData'];
										$mo2f_google_auth['ga_secret'] = $google_response['secret'];

										MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'secret_ga', $mo2f_google_auth['ga_secret']);
										MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'ga_qrCode', $mo2f_google_auth['ga_qrCode']);

		                               } else {
			                               $mo2fa_login_message = __( 'Invalid request. Please register with miniOrange to configure 2 Factor plugin.', 'miniorange-2-factor-authentication' );
		                               }
	                               }
                               }
                        }


					}else if($selected_method == "DUO PUSH NOTIFICATIONS"){
						$this->miniorange_pass2login_start_session();
						$mo2fa_login_message = '';
						$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
						
						$selected_method = "Duo Authenticator";
						
							$Mo2fdbQueries->update_user_details( $current_user->ID, array(
								'mo2f_configured_2FA_method' =>$selected_method
							) );

						

						
					}	
					else{
					    //inline for others
						if(!MO2F_IS_ONPREM or $selected_method == 'MOBILE AUTHENTICATION' or $selected_method == 'PUSH NOTIFICATIONS' or $selected_method == 'SOFT TOKEN' )
						{
							$current_user 	= get_userdata($currentUserId);
							$email 			= $current_user->user_email;
							$response 		= $this->create_user_in_miniOrange($currentUserId,$email,$selected_method);
	                            if(!is_null($response) && $response['status']=='ERROR') {
									    $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
									    $mo2fa_login_message=$response['message'].'Skip the two-factor for login';
	                            }
	                        else {
		                        	if($selected_method == 'OTP OVER TELEGRAM')
									{
										$selected_method = 'OTP Over Telegram';
									}
									$Mo2fdbQueries->update_user_details( $current_user->ID, array('mo2f_configured_2FA_method' =>$selected_method) );
	                             }
						}else{
							if($selected_method == 'OTP OVER TELEGRAM')
							{
								$selected_method = 'OTP Over Telegram';
							}
							$Mo2fdbQueries->update_user_details( $current_user->ID, array(
								'mo2f_configured_2FA_method' =>$selected_method,
							) );
                        }
                    }
				}else{ 
					$mo2fa_login_message = __('Invalid request. Please register with miniOrange to configure 2 Factor plugin.','miniorange-2-factor-authentication');
				}
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id_encrypt);
			}
		}
	}

	function check_kba_validation($POSTED){
		global $moWpnsUtility;
		if ( isset( $POSTED['miniorange_kba_nonce'] ) ) { /*check kba validation*/
				$nonce = $POSTED['miniorange_kba_nonce'];
				if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-kba-nonce' ) ) {
						$error = new WP_Error();
						$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
						return $error;
				}else{
						$this->miniorange_pass2login_start_session();
		                $session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
						$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
						if ( isset( $user_id ) ) {
								if ( MO2f_Utility::mo2f_check_empty_or_null( sanitize_text_field($_POST['mo2f_answer_1']) ) || MO2f_Utility::mo2f_check_empty_or_null( sanitize_text_field($_POST['mo2f_answer_2'] )) ) {
									MO2f_Utility::mo2f_debug_file('Please provide both the answers of KBA'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
									$mo2fa_login_message = 'Please provide both the answers.';
									$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_KBA_AUTHENTICATION';
									$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt );
								}
								$otpToken      = array();
					$kba_questions = MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'mo_2_factor_kba_questions' );
								$otpToken[0] = $kba_questions[0]['question'];
								$otpToken[1] = sanitize_text_field( $_POST['mo2f_answer_1'] );
								$otpToken[2] = $kba_questions[1]['question'];
								$otpToken[3] = sanitize_text_field( $_POST['mo2f_answer_2'] );
								$check_trust_device = isset( $_POST['mo2f_trust_device'] ) ? sanitize_text_field($_POST['mo2f_trust_device']) : 'false';
								//if the php session folder has insufficient permissions, cookies to be used
								$mo2f_login_transaction_id = MO2f_Utility::mo2f_retrieve_user_temp_values( 'mo2f_transactionId', $session_id_encrypt );
								MO2f_Utility::mo2f_debug_file('Transaction Id-'.$mo2f_login_transaction_id.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
								$mo2f_rba_status = MO2f_Utility::mo2f_retrieve_user_temp_values( 'mo2f_rba_status',$session_id_encrypt );
								$kba_validate = new Customer_Setup();
								$kba_validate_response = json_decode( $kba_validate->validate_otp_token( 'KBA', null, $mo2f_login_transaction_id, $otpToken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
								global $Mo2fdbQueries;
								$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id );
								if ( strcasecmp( $kba_validate_response['status'], 'SUCCESS' ) == 0 ) {
									if ( get_option( 'mo2f_remember_device' ) && $check_trust_device == 'on' ) {
										try {
											mo2f_register_profile( $email, 'true', $mo2f_rba_status );
										} catch ( Exception $e ) {
											echo esc_html($e->getMessage());
										}
										MO2f_Utility::mo2f_debug_file('Remeber device logged in successfully'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
										$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
									} else {
										MO2f_Utility::mo2f_debug_file('Logged in successfully'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
										$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
									}
							} else {
								MO2f_Utility::mo2f_debug_file('The answers you have provided for KBA are incorrect'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
								$mo2fa_login_message = 'The answers you have provided are incorrect.';
								$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_KBA_AUTHENTICATION';
								$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt);
							}
						} else {
							  MO2f_Utility::mo2f_debug_file('User id not found'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
								$this->remove_current_activity($session_id_encrypt);
								return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Please try again..' ) );
						}
			}
		}
	}
	function check_rba_cancalation($POSTED){
		$nonce = sanitize_text_field($POSTED['mo2f_trust_device_cancel_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-trust-device-cancel-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
			} else {
					$this->miniorange_pass2login_start_session();
		            $session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
					$redirect_to = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
					$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
			}
	}
	function check_rba_validation($POSTED){
			$nonce = $POSTED['mo2f_trust_device_confirm_nonce'];
				if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-trust-device-confirm-nonce' ) ) {
		                $session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id'] ): null;
		                $this->remove_current_activity($session_id_encrypt);
		                $error = new WP_Error();
		                $error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR ' ) . '</strong>:' . mo2f_lt( 'Invalid Request.' ) );
		                return $error;
	            } else {
		                $this->miniorange_pass2login_start_session();
		                $session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
		                try {
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
		                    Global $Mo2fdbQueries;
		                    $email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id );
		                    $mo2f_rba_status = MO2f_Utility::mo2f_retrieve_user_temp_values( 'mo2f_rba_status',$session_id_encrypt );
		                    mo2f_register_profile( $email, 'true', $mo2f_rba_status );
		                } catch ( Exception $e ) {
		                    echo esc_html($e->getMessage());
		                }
		                $redirect_to = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
						$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
				}
	}

	 function miniorange2f_back_to_inline_registration($POSTED)
	 {	
	 	$nonce = sanitize_text_field($_POST['miniorange_back_inline_reg_nonce']);
		if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-back-inline-reg-nonce' ) ) {
			$error = new WP_Error();
			$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
			return $error;
		} else {
	
			$session_id_encrypt = sanitize_text_field($POSTED['session_id']);
			$redirect_to = esc_url_raw($POSTED['redirect_to']);
			$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
			$mo2fa_login_message = '';
			$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id_encrypt );
		}

	 }

	 function check_miniorange_challenge_forgotphone($POSTED){/*check kba validation*/
	 $nonce = sanitize_text_field($_POST['miniorange_forgotphone']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-forgotphone' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
				return $error;
			} else {
				$mo2fa_login_status  = isset( $_POST['request_origin_method'] ) ? sanitize_text_field($_POST['request_origin_method']) : null;
                $session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$redirect_to         = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$mo2fa_login_message = '';
				$this->miniorange_pass2login_start_session();
				$customer                 = new Customer_Setup();
			$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				Global $Mo2fdbQueries;
				$user_email               = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id );
				$kba_configuration_status = $Mo2fdbQueries->get_user_detail( 'mo2f_SecurityQuestions_config_status', $user_id );
				if ( $kba_configuration_status ) {
					$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_KBA_AND_OTP_OVER_EMAIL';
					$pass2fa_login      = new Miniorange_Password_2Factor_Login();
					$pass2fa_login->mo2f_pass2login_kba_verification( $user_id, $redirect_to,$session_id_encrypt );
				} else {
					$hidden_user_email = MO2f_Utility::mo2f_get_hidden_email( $user_email );
					$content           = json_decode( $customer->send_otp_token( $user_email, 'EMAIL', get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
						$session_cookie_variables = array( 'mo2f-login-qrCode', 'mo2f_transactionId' );
						MO2f_Utility::unset_session_variables( $session_cookie_variables );
						MO2f_Utility::unset_cookie_variables( $session_cookie_variables );
						MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt );
						//if the php session folder has insufficient permissions, cookies to be used
						MO2f_Utility::set_user_values( $session_id_encrypt,'mo2f_login_message', 'A one time passcode has been sent to <b>' . $hidden_user_email . '</b>. Please enter the OTP to verify your identity.' );
						MO2f_Utility::set_user_values( $session_id_encrypt, 'mo2f_transactionId', $content['txId'] );
						$this->mo2f_transactionid=$content['txId'];
						$mo2fa_login_message = 'A one time passcode has been sent to <b>' . $hidden_user_email . '</b>. Please enter the OTP to verify your identity.';
						$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL';
					} else {
						$mo2fa_login_message = 'Error occurred while sending OTP over email. Please try again.';
					}
					$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to, null,$session_id_encrypt );
				}
				$pass2fa_login = new Miniorange_Password_2Factor_Login();
				$pass2fa_login->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt );
			} 
		}
	 function check_miniorange_alternate_login_kba($POSTED){
					$nonce = $POSTED['miniorange_alternate_login_kba_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-alternate-login-kba-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
					return $error;
			} else {
					$this->miniorange_pass2login_start_session();
	                $session_id_encrypt = isset( $POSTED['session_id'] ) ? $POSTED['session_id'] : null;
			$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
					$redirect_to = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
					$this->mo2f_pass2login_kba_verification( $user_id, $redirect_to,$session_id_encrypt );
			}
	}

	function check_miniorange_duo_push_validation($POSTED){
		global $moWpnsUtility;
		$nonce = $POSTED['miniorange_duo_push_validation_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-duo-validation-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
				return $error;
			} else {
                 $this->miniorange_pass2login_start_session();
                $session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
                $user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				
				$redirect_to       = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
				if ( get_option( 'mo2f_remember_device' ) ) {
					    
					     MO2f_Utility::mo2f_debug_file('Remember device- Duo push notification logged in successfully'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
							$mo2fa_login_status = 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE';
							$this->miniorange_pass2login_form_fields( $mo2fa_login_status, null, $redirect_to, null,$session_id_encrypt );
						} else {
							MO2f_Utility::mo2f_debug_file('Duo push notification - Logged in successfully'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
							$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
						}

			}
	}

function check_miniorange_duo_push_validation_failed($POSTED){
       global $moWpnsUtility;
       $nonce = $POSTED['miniorange_duo_push_validation_failed_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-duo-push-validation-failed-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				return $error;
			} else {
				  MO2f_Utility::mo2f_debug_file('Denied duo push notification'.' User_IP-'.$moWpnsUtility->get_client_ip());
				$this->miniorange_pass2login_start_session();
                $session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
				$this->remove_current_activity($session_id_encrypt);
			
			}

}	

	 function check_miniorange_mobile_validation($POSTED){
		/*check mobile validation */
            global $moWpnsUtility;
			$nonce = $POSTED['miniorange_mobile_validation_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-mobile-validation-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
				return $error;
			} else {
				if(MO2F_IS_ONPREM && (isset($POSTED['tx_type']) && $POSTED['tx_type'] !='PN'))
				{
						$txid   = $POSTED['TxidEmail'];
						$status = get_option($txid);
						if($status != '')
						{
							if($status != 1)
							{
								return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Please try again.' ) );	
							}
						}
				}
				$this->miniorange_pass2login_start_session();
                $session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
				//if the php session folder has insufficient permissions, cookies to be used
			$mo2f_login_transaction_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_transactionId');
			 MO2f_Utility::mo2f_debug_file('Transaction_id-'.$mo2f_login_transaction_id.' User_IP-'.$moWpnsUtility->get_client_ip());
				$redirect_to       = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
				$checkMobileStatus = new Two_Factor_Setup();
				$content           = $checkMobileStatus->check_mobile_status( $mo2f_login_transaction_id );
				$response          = json_decode( $content, true );
				if(MO2F_IS_ONPREM)
				{
					  MO2f_Utility::mo2f_debug_file('MO QR-code/push notification auth logged in successfully'.' User_IP-'.$moWpnsUtility->get_client_ip());
					$this->mo2fa_pass2login($redirect_to,$session_id_encrypt);
				}
				if ( json_last_error() == JSON_ERROR_NONE ) {
					if ( $response['status'] == 'SUCCESS' ) {
						if ( get_option( 'mo2f_remember_device' ) ) {
							$mo2fa_login_status = 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE';
							 MO2f_Utility::mo2f_debug_file('Remember device flow prompted'.' User_IP-'.$moWpnsUtility->get_client_ip());
							$this->miniorange_pass2login_form_fields( $mo2fa_login_status, null, $redirect_to, null,$session_id_encrypt );
						} else {
							MO2f_Utility::mo2f_debug_file('MO QR-code/push notification auth logged in successfully'.' User_IP-'.$moWpnsUtility->get_client_ip());
							$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
						}
					} else {
						MO2f_Utility::mo2f_debug_file('Invalid_username'.' User_IP-'.$moWpnsUtility->get_client_ip());
						$this->remove_current_activity($session_id_encrypt);
						return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Please try again.' ) );
					}
				} else {
					MO2f_Utility::mo2f_debug_file('Invalid_username'.' User_IP-'.$moWpnsUtility->get_client_ip());
					$this->remove_current_activity($session_id_encrypt);
					return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Please try again.' ) );
				}
			}
	}
	 function check_miniorange_mobile_validation_failed($POSTED){
		/*Back to miniOrange Login Page if mobile validation failed and from back button of mobile challenge, soft token and default login*/
			$nonce = $POSTED['miniorange_mobile_validation_failed_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-mobile-validation-failed-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				return $error;
			} else {
				 MO2f_Utility::mo2f_debug_file('MO QR-code/push notification auth denied.');
				$this->miniorange_pass2login_start_session();
                $session_id_encrypt = isset( $POSTED['session_id'] ) ? $POSTED['session_id'] : null;
				$this->remove_current_activity($session_id_encrypt);
			
			}
	}

	function check_mo2f_duo_authenticator_success_form($POSTED){
		if(isset($POSTED['mo2f_duo_authenticator_success_nonce'])){
			$nonce = sanitize_text_field($POSTED['mo2f_duo_authenticator_success_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo2f-duo-authenticator-success-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
                  
                 global $Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
				MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt );
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				$redirect_to = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
				$selected_2factor_method = $Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$user_id);
				$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$user_id);
				$mo2fa_login_message = '';
				
				
			    delete_user_meta($user_id,'user_not_enroll');
				delete_site_option('current_user_email');
							$Mo2fdbQueries->update_user_details( $user_id, array(
									'mobile_registration_status' =>true,
									'mo2f_DuoAuthenticator_config_status' => true,
									'mo2f_configured_2FA_method' =>$selected_2factor_method ,
									'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS',
							) );					
				$mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
						
				
				$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt);

			}	
	   }
	}
	function check_inline_mo2f_duo_authenticator_error($POSTED){
		$nonce = $POSTED['mo2f_inline_duo_authentcator_error_nonce'];
		     
			if ( ! wp_verify_nonce( $nonce, 'mo2f-inline-duo-authenticator-error-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				global 	$Mo2fdbQueries;
				$this->miniorange_pass2login_start_session();
				$session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
				MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt );
				$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				
				
				
				$Mo2fdbQueries->update_user_details( $user_id, array(
									'mobile_registration_status' =>false,
							     ) );	
				
			}
	}
	 function check_miniorange_forgotphone($POSTED){
		$nonce = $POSTED['miniorange_forgotphone'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-forgotphone' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
				return $error;
			} else {
			    global $Mo2fdbQueries;
				$mo2fa_login_status  = isset( $POSTED['request_origin_method'] ) ? $POSTED['request_origin_method'] : null;
                $session_id_encrypt = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
				$redirect_to         = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
				$mo2fa_login_message = '';
				$this->miniorange_pass2login_start_session();
				$customer                 = new Customer_Setup();
			$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				$user_email               = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id );
				$kba_configuration_status = $Mo2fdbQueries->get_user_detail( 'mo2f_SecurityQuestions_config_status', $user_id );
				if ( $kba_configuration_status ) {
					$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_KBA_AND_OTP_OVER_EMAIL';
					$pass2fa_login      = new Miniorange_Password_2Factor_Login();
					$pass2fa_login->mo2f_pass2login_kba_verification( $user_id, $redirect_to,$session_id_encrypt );
				} else {
					$hidden_user_email = MO2f_Utility::mo2f_get_hidden_email( $user_email );
					$content           = json_decode( $customer->send_otp_token( $user_email, 'EMAIL', get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
						$session_cookie_variables = array( 'mo2f-login-qrCode', 'mo2f_transactionId' );
						MO2f_Utility::unset_session_variables( $session_cookie_variables );
						MO2f_Utility::unset_cookie_variables( $session_cookie_variables );
						MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt );
						//if the php session folder has insufficient permissions, cookies to be used
						MO2f_Utility::set_user_values( $session_id_encrypt,'mo2f_login_message', 'A one time passcode has been sent to <b>' . $hidden_user_email . '</b>. Please enter the OTP to verify your identity.' );
						MO2f_Utility::set_user_values( $session_id_encrypt, 'mo2f_transactionId', $content['txId'] );
						$this->mo2f_transactionid=$content['txId'];
						$mo2fa_login_message = 'A one time passcode has been sent to <b>' . $hidden_user_email . '</b>. Please enter the OTP to verify your identity.';
						$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL';
					} else {
						$mo2fa_login_message = 'Error occurred while sending OTP over email. Please try again.';
					}
					$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to, null,$session_id_encrypt );
				}
				$pass2fa_login = new Miniorange_Password_2Factor_Login();
				$pass2fa_login->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt );
			}
	}
	 function check_miniorange_softtoken($POSTED){
		/*Click on the link of phone is offline */
			$nonce = $POSTED['miniorange_softtoken'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-softtoken' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
				return $error;
			} else {
				$this->miniorange_pass2login_start_session();
                $session_id_encrypt = isset( $POSTED['session_id'] ) ? $POSTED['session_id'] : null;
				$session_cookie_variables = array( 'mo2f-login-qrCode', 'mo2f_transactionId' );
				MO2f_Utility::unset_session_variables( $session_cookie_variables );
				MO2f_Utility::unset_cookie_variables( $session_cookie_variables );
				MO2f_Utility::unset_temp_user_details_in_table('mo2f_transactionId',$session_id_encrypt );
				$redirect_to         = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
				$mo2fa_login_message = 'Please enter the one time passcode shown in the miniOrange<b> Authenticator</b> app.';
				$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_SOFT_TOKEN';
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt );
			}
	}
	 function check_miniorange_soft_token($POSTED){
		/*Validate Soft Token,OTP over SMS,OTP over EMAIL,Phone verification */
           global $moWpnsUtility;
			$nonce = sanitize_text_field($_POST['miniorange_soft_token_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-soft-token-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
				return $error;
			}else {
				$this->miniorange_pass2login_start_session();
                $session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$mo2fa_login_status = isset( $_POST['request_origin_method'] ) ? sanitize_text_field($_POST['request_origin_method']) : null;
				$redirect_to        = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
				$softtoken          = '';
			$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				$attempts = get_option('mo2f_attempts_before_redirect', 3);
				if ( MO2f_utility::mo2f_check_empty_or_null( $_POST['mo2fa_softtoken'] ) ) {
					if($attempts>1 || $attempts=='disabled')
					{
						update_option('mo2f_attempts_before_redirect', $attempts-1 );
						$mo2fa_login_message = 'Please enter OTP to proceed.';
						MO2f_Utility::mo2f_debug_file('Please enter OTP to proceed'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
						$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt );
					}else{
						$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
						$this->remove_current_activity($session_id_encrypt);
						MO2f_Utility::mo2f_debug_file('Number of attempts exceeded'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
						return new WP_Error( 'limit_exceeded', '<strong>ERROR</strong>: Number of attempts exceeded.');
					}
				} else {
					$softtoken = sanitize_text_field( $_POST['mo2fa_softtoken'] );
					if ( ! MO2f_utility::mo2f_check_number_length( $softtoken ) ) {
						if($attempts>1|| $attempts=='disabled')
						{
							update_option('mo2f_attempts_before_redirect', $attempts-1 );
							$mo2fa_login_message = 'Invalid OTP. Only digits within range 4-8 are allowed. Please try again.';
							MO2f_Utility::mo2f_debug_file('Invalid OTP. Only digits within range 4-8 are allowed'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
							$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt );
						}else{
							$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
							$this->remove_current_activity($session_id_encrypt);
							update_option('mo2f_attempts_before_redirect', 3);
							if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request')){
								$data = array('reload' => "reload", );
								wp_send_json_success($data);
							}
							else{
								MO2f_Utility::mo2f_debug_file('Number of attempts exceeded'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id);
							return new WP_Error( 'limit_exceeded', '<strong>ERROR</strong>: Number of attempts exceeded.');
						    }
						}
					}
				}
				
				global $Mo2fdbQueries;
				$user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id );
				if ( isset( $user_id ) ) {
					$customer = new Customer_Setup();
					$content  = '';
					$current_user = get_userdata($user_id);
					//if the php session folder has insufficient permissions, cookies to be used
				$mo2f_login_transaction_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_transactionId');
				$mo2f_login_transaction_id = isset($_POST['mo2fa_transaction_id'])?sanitize_text_field($_POST['mo2fa_transaction_id']):MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_transactionId');
					MO2f_Utility::mo2f_debug_file('Transaction_id-'.$mo2f_login_transaction_id.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
					if ( isset( $mo2fa_login_status ) && $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL' ) {
						$content = json_decode( $customer->validate_otp_token( 'EMAIL', null, $mo2f_login_transaction_id, $softtoken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ),$current_user ), true );
					}elseif (isset( $mo2fa_login_status ) && $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_OTP_OVER_WHATSAPP' ) {
						
						$otpToken 		= get_user_meta($current_user->ID,'mo2f_otp_token_wa',true);
						$time  			= get_user_meta($current_user->ID,'mo2f_whatsapp_time',true);
						$accepted_time	= time()-600;
						$time 			= (int)$time;
						global $Mo2fdbQueries;
					
						if($softtoken == $otpToken)
						{
							if($accepted_time<$time){
								update_option('mo2f_attempts_before_redirect', 3);
									if ( get_option( 'mo2f_remember_device' ) ) {
										$mo2fa_login_status = 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE';
										MO2f_Utility::mo2f_debug_file('Remeber device setup'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
										$this->miniorange_pass2login_form_fields( $mo2fa_login_status, null, $redirect_to,null,$session_id_encrypt );
									}
									else{
										
										$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
									}
							}
							else
							{
								$this->remove_current_activity($session_id_encrypt);
								
								return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: OTP has been Expired please reinitiate another transaction.' ) );
							
							}
						}
						else
						{ 
							
							update_option('mo2f_attempts_before_redirect', $attempts-1);
							$message = 'Invalid OTP please enter again.';
							$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $message, $redirect_to,null,$session_id_encrypt );
						
						}
					}
					elseif (isset( $mo2fa_login_status ) && $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_OTP_OVER_TELEGRAM' ) {
						
						$otpToken 		= get_user_meta($current_user->ID,'mo2f_otp_token',true);
						$time  			= get_user_meta($current_user->ID,'mo2f_telegram_time',true);
						$accepted_time	= time()-300;
						$time 			= (int)$time;
						global $Mo2fdbQueries;
					
						if($softtoken == $otpToken)
						{
							if($accepted_time<$time){
								update_option('mo2f_attempts_before_redirect', 3);
									if ( get_option( 'mo2f_remember_device' ) ) {
										$mo2fa_login_status = 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE';
										MO2f_Utility::mo2f_debug_file('Remember device flow'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
										$this->miniorange_pass2login_form_fields( $mo2fa_login_status, null, $redirect_to,null,$session_id_encrypt );
									}
									else{
										MO2f_Utility::mo2f_debug_file('OTP over Telegram - Logged in successfully'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
										$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
									}
							}
							else
							{
								$this->remove_current_activity($session_id_encrypt);
								MO2f_Utility::mo2f_debug_file('OTP has been Expired please reinitiate another transaction'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
								return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: OTP has been Expired please reinitiate another transaction.' ) );
							
							}
						}
						else
						{
							if($attempts<=1){
								$this->remove_current_activity($session_id_encrypt);
								update_option('mo2f_attempts_before_redirect', 3);
								return new WP_Error( 'attempts failed try again ', __( '<strong>ERROR</strong>: maximum attempts.' ) );
							}
							MO2f_Utility::mo2f_debug_file('OTP over Telegram - Invalid OTP'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
							update_option('mo2f_attempts_before_redirect', $attempts-1);
							$message = 'Invalid OTP please enter again.';
							$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $message, $redirect_to,null,$session_id_encrypt );
						
						}
					} 
					else if ( isset( $mo2fa_login_status ) && $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_OTP_OVER_SMS' ) {
						$content = json_decode( $customer->validate_otp_token( 'SMS', null, $mo2f_login_transaction_id, $softtoken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
					} else if ( isset( $mo2fa_login_status ) && $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_PHONE_VERIFICATION' ) {
						$content = json_decode( $customer->validate_otp_token( 'PHONE VERIFICATION', null, $mo2f_login_transaction_id, $softtoken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
					} else if ( isset( $mo2fa_login_status ) && $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_SOFT_TOKEN' ) {
						$content = json_decode( $customer->validate_otp_token( 'SOFT TOKEN', $user_email, null, $softtoken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
					} else if ( isset( $mo2fa_login_status ) && $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_GOOGLE_AUTHENTICATION' ) {
								$content = json_decode( $customer->validate_otp_token( 'GOOGLE AUTHENTICATOR', $user_email, null, $softtoken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
					} else {
						$this->remove_current_activity($session_id_encrypt);
						return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Invalid Request. Please try again.' ) );
					}
					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
						update_option('mo2f_attempts_before_redirect', 3);
						if ( get_option( 'mo2f_remember_device' ) ) {
							$mo2fa_login_status = 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE';
							 MO2f_Utility::mo2f_debug_file('Remember device flow'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
							$this->miniorange_pass2login_form_fields( $mo2fa_login_status, null, $redirect_to,null,$session_id_encrypt );
						} else {
							if($mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL')
							{
								$Mo2fdbQueries->update_user_details( $user_id, array('mo2f_configured_2FA_method' =>'OTP Over Email','mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS', 'mo2f_OTPOverEmail_config_status' => 1) );	
								$enduser  = new Two_Factor_Setup();

								$enduser->mo2f_update_userinfo( $user_email, 'OTP Over Email', null, null, null );
							
							}
							  MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' Logged in successfully'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
							$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
						}
					} else {
						if($attempts>1 || $attempts=='disabled')
						{
							 MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' Enter wrong OTP'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
							update_option('mo2f_attempts_before_redirect', $attempts-1);
							$message = $mo2fa_login_status == 'MO_2_FACTOR_CHALLENGE_SOFT_TOKEN' ? 'You have entered an invalid OTP.<br>Please click on <b>Sync Time</b> in the miniOrange Authenticator app to sync your phone time with the miniOrange servers and try again.' : 'Invalid OTP. Please try again.';
							$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $message, $redirect_to,null,$session_id_encrypt );
						}else{
							$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
							$this->remove_current_activity($session_id_encrypt);
							update_option('mo2f_attempts_before_redirect', 3);
							if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request')){
								$data = array('reload' => "reload", );
								wp_send_json_success($data);
							}
							else{
								 MO2f_Utility::mo2f_debug_file('Number of attempts exceeded'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
								 return new WP_Error( 'limit_exceeded', '<strong>ERROR</strong>: Number of attempts exceeded.');
							}
							
						}
					}
				} else {
					$this->remove_current_activity($session_id_encrypt);
					MO2f_Utility::mo2f_debug_file('User id not found'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user_id.' Email-'.$user_email);
					return new WP_Error( 'invalid_username', __( '<strong>ERROR</strong>: Please try again..' ) );
				}
			}
	}
	 function check_miniorange_attribute_collection($POSTED){
		$nonce = $POSTED['miniorange_attribute_collection_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-login-attribute-collection-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
				return $error;
			} else {
				$this->miniorange_pass2login_start_session();
			$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				$currentuser = get_user_by( 'id', $user_id );
				$attributes  = isset( $POSTED['miniorange_rba_attribures'] ) ? $POSTED['miniorange_rba_attribures'] : null;
				$redirect_to = isset( $POSTED['redirect_to'] ) ? esc_url_raw($POSTED['redirect_to']) : null;
				$session_id  = isset( $POSTED['session_id'] ) ? sanitize_text_field($POSTED['session_id']) : null;
				$this->miniorange_initiate_2nd_factor( $currentuser, $attributes, $redirect_to,$session_id );
			}
	}
	function check_miniorange_inline_skip_registration($POSTED){
			$error = new WP_Error();
			$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );
	}
	 function miniorange_pass2login_redirect() {
		do_action('mo2f_network_init');
		global $Mo2fdbQueries;
		
		if ( ! MoWpnsUtility::get_mo2f_db_option('mo2f_login_option', 'get_option') ) {
			if ( isset( $_POST['miniorange_login_nonce'] ) ) {
				$nonce = sanitize_text_field($_POST['miniorange_login_nonce']);
				 $session_id  = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				
                if(is_null($session_id)) {
                    $session_id=$this->create_session();
                }
				
				
				if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-login-nonce' ) ) {
					$this->remove_current_activity($session_id);
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
                } else {
                    $this->miniorange_pass2login_start_session();
					$mobile_login = new Miniorange_Mobile_Login();
					//validation and sanitization
					$username = isset( $_POST['mo2fa_username'] ) ? sanitize_user($_POST['mo2fa_username']) : '';
                    if ( MO2f_Utility::mo2f_check_empty_or_null( $username ) ) {
                        MO2f_Utility::set_user_values($session_id, 'mo2f_login_message', 'Please enter username to proceed' );
                        $mobile_login->mo_auth_show_error_message();
						return;
					} else {
						$username = sanitize_user( $_POST['mo2fa_username'] );
					}
					if ( username_exists( $username ) ) {	 /*if username exists in wp site */
						$user = new WP_User( $username );
						$redirect_to = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw($_REQUEST['redirect_to']) : null;

						MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_current_user_id', $user->ID, 600);
						MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_1stfactor_status', 'VALIDATE_SUCCESS', 600);


						$this->mo2f_userId=$user->ID;
						$this->fstfactor='VALIDATE_SUCCESS';						
						$current_roles = miniorange_get_user_role( $user );
						$mo2f_configured_2FA_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
						$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
						$mo_2factor_user_registration_status = $Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status', $user->ID );
						$kba_configuration_status = $Mo2fdbQueries->get_user_detail( 'mo2f_SecurityQuestions_config_status', $user->ID );
						
						if(MO2F_IS_ONPREM )
						{
							$mo_2factor_user_registration_status = 'MO_2_FACTOR_PLUGIN_SETTINGS';

						}
						if ( $mo2f_configured_2FA_method ) {
							if ( $email && $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' or (MO2F_IS_ONPREM and  $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS')) {
								if ( MO2f_Utility::check_if_request_is_from_mobile_device( $_SERVER['HTTP_USER_AGENT'] ) && $kba_configuration_status ) {
									$this->mo2f_pass2login_kba_verification( $user->ID, $redirect_to, $session_id );
								} else {
									$mo2f_second_factor = '';

									if(MO2F_IS_ONPREM)
									{
										global $Mo2fdbQueries;
										$mo2f_second_factor = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
										if($mo2f_second_factor == 'Security Questions')
										{
											$mo2f_second_factor = 'KBA';
										}
										else if($mo2f_second_factor == 'Google Authenticator')
										{
											$mo2f_second_factor = 'GOOGLE AUTHENTICATOR';
										}
										else if($mo2f_second_factor == 'Email Verification'){
											$mo2f_second_factor = 'Email Verification';
										}
										else if($mo2f_second_factor == 'OTP Over SMS'){
											$mo2f_second_factor = 'SMS';
										}
										else if($mo2f_second_factor == 'OTP Over Email'){
											$mo2f_second_factor = 'EMAIL';
										}
										elseif($mo2f_second_factor == 'miniOrange Soft Token'){
				                            $mo2f_second_factor = "SOFT TOKEN";
										}
				                        else if($mo2f_second_factor == "miniOrange Push Notification"){
				                            $mo2f_second_factor = "PUSH NOTIFICATIONS";
				                        }
				                        else if($mo2f_second_factor == "miniOrange QR Code Authentication"){
				                            $mo2f_second_factor = "MOBILE AUTHENTICATION";
				                        }
									}else{
										$mo2f_second_factor = mo2f_get_user_2ndfactor( $user );
                                    }
									if ( $mo2f_second_factor == 'MOBILE AUTHENTICATION' ) {
										$this->mo2f_pass2login_mobile_verification( $user, $redirect_to, $session_id );
									} else if ( $mo2f_second_factor == 'PUSH NOTIFICATIONS' || $mo2f_second_factor == 'OUT OF BAND EMAIL' ) {
										$this->mo2f_pass2login_push_oobemail_verification( $user, $mo2f_second_factor, $redirect_to, $session_id );
									}
									else if($mo2f_second_factor == 'Email Verification'){
										$this->mo2f_pass2login_push_oobemail_verification( $user, $mo2f_second_factor, $redirect_to, $session_id );
									}
									else if ( $mo2f_second_factor == 'SOFT TOKEN' || $mo2f_second_factor == 'SMS' || $mo2f_second_factor == 'PHONE VERIFICATION' || $mo2f_second_factor == 'GOOGLE AUTHENTICATOR' || $mo2f_second_factor == 'OTP Over Telegram'|| $mo2f_second_factor == 'EMAIL' || $mo2f_second_factor == "OTP Over Email") {
										$this->mo2f_pass2login_otp_verification( $user, $mo2f_second_factor, $redirect_to, $session_id );
									} else if ( $mo2f_second_factor == 'KBA' ) {
										$this->mo2f_pass2login_kba_verification( $user->ID, $redirect_to, $session_id );
									} else {
										$this->remove_current_activity($session_id);
										MO2f_Utility::set_user_values($session_id, 'mo2f_login_message', 'Please try again or contact your admin.' );
										$mobile_login->mo_auth_show_success_message();
									}
								}
							} else {
								MO2f_Utility::set_user_values($session_id, 'mo2f_login_message', 'Please login into your account using password.' );
								$mobile_login->mo_auth_show_success_message('Please login into your account using password.');
								update_user_meta($user->ID,'userMessage','Please login into your account using password.');
								$mobile_login->mo2f_redirectto_wp_login();
							}
						} else {	
							MO2f_Utility::set_user_values( $session_id, "mo2f_login_message", 'Please login into your account using password.' );
							$mobile_login->mo_auth_show_success_message('Please login into your account using password.');
							update_user_meta($user->ID,'userMessage','Please login into your account using password.');
							$mobile_login->mo2f_redirectto_wp_login();
						}
					} else {
						$mobile_login->remove_current_activity($session_id);
						MO2f_Utility::set_user_values( $session_id, "mo2f_login_message", 'Invalid Username.' );
						$mobile_login->mo_auth_show_error_message('Invalid Username.');
					}
				}
			}

		}
		if(isset($_GET['reconfigureMethod']) && is_user_logged_in()){
			$userIDGet 	= get_current_user_id();
			$txidGet 	= isset($_GET['transactionId'])?sanitize_text_field($_GET['transactionId']):'';
			$methodGet 	= isset($_GET['reconfigureMethod'])?sanitize_text_field($_GET['reconfigureMethod']):'';
			if(get_site_option($txidGet) === $userIDGet && ctype_xdigit($txidGet) && ctype_xdigit($methodGet)){
				$method = get_site_option($methodGet);
				$Mo2fdbQueries->update_user_details( $userIDGet, array(
					'mo_2factor_user_registration_status' =>'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS',
					'mo2f_configured_2FA_method' => $method
				) );
				$is_Authy_configured = $Mo2fdbQueries->get_user_detail('mo2f_AuthyAuthenticator_config_status',$userIDGet);
				if($method == 'Google Authenticator' || $is_Authy_configured){
					update_user_meta($userIDGet,'mo2fa_set_Authy_inline',true);
				}
                delete_site_option($txidGet);
            }else{
				$head = "You are not authorized to perform this action";
				$body = "Please contact to your admin";
				$this->display_email_verification($head,$body,'red');
				exit();
			}
		}
		if(isset($_GET['Txid'])&&isset($_GET['accessToken']))
		{
			$userIDGet 	= sanitize_text_field($_GET['userID']);
			$txIdGet   	= sanitize_text_field($_GET['Txid']);
			$otpToken 	= get_site_option($userIDGet);
			$txidstatus = get_site_option($txIdGet);
			$userIDd 	= $userIDGet.'D';
			$otpTokenD 	= get_site_option($userIDd); 
			$mo2f_dirName = dirname(__FILE__);
			$mo2f_dirName = explode('wp-content', $mo2f_dirName);
			$mo2f_dirName = explode('handler', $mo2f_dirName[1]);

			$head = "You are not authorized to perform this action";
			$body = "Please contact to your admin";
			$color = "red";
			if(3 == $txidstatus)
			{
				$time = "time".$txIdGet;
				$currentTimeInMillis = round(microtime(true) * 1000);
				$generatedTimeINMillis  = get_site_option($time); 
				$difference = ($currentTimeInMillis-$generatedTimeINMillis)/1000 ;
				if($difference <= 300)
				{	
					$accessTokenGet = sanitize_text_field($_GET['accessToken']);
					if( $accessTokenGet == $otpToken)
					{
						update_site_option($txIdGet,1);
						$body 	= "Transaction has been successfully validated. Please continue with the transaction.";
						$head 	= "TRANSACTION SUCCESSFUL";
						$color 	= "green"; 
					}
					else if($accessTokenGet==$otpTokenD)
					{
						update_site_option($txIdGet,0);
						$body = "Transaction has been Canceled. Please Try Again.";
						$head = "TRANSACTION DENIED";
					}
				}
				delete_site_option($userIDGet);
				delete_site_option($userIDd);
				delete_site_option($time);
					
			}
			
			$this->display_email_verification($head,$body,$color);
			exit;
					
		}
		elseif (isset($_POST['emailInlineCloud'])) {
			$nonce = sanitize_text_field($_POST['miniorange_emailChange_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-email-change-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				return $error;
            } else {
            	$email = sanitize_text_field($_POST['emailInlineCloud']);
            	$current_user_id = sanitize_text_field($_POST['current_user_id']);
				$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
				$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw($_POST['redirect_to']) : null;
            	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            		global  $Mo2fdbQueries;
            		$Mo2fdbQueries->update_user_details( $current_user_id, array( "mo2f_user_email" => $email, "mo2f_configured_2FA_method" => '' ) );
            		prompt_user_to_select_2factor_mthod_inline($current_user_id,'MO_2_FACTOR_INITIALIZE_TWO_FACTOR','',$redirect_to,$session_id_encrypt,null);
            	}
            }
		}
		else if(isset($_POST['txid']))
		{
			$txidpost 	= sanitize_text_field($_POST['txid']);
			$status 	= get_site_option($txidpost);
			update_option('optionVal1',$status); //??
			if($status ==1 || $status ==0)
				delete_site_option($txidpost);
			echo esc_html($status);
			exit();
		}
		


			else{
			
		$value=isset($_POST['option'])?sanitize_text_field($_POST['option']):false;
	
		switch ($value) {
			case 'miniorange_rba_validate':
			$this->check_rba_validation($_POST);
			break;
			case 'miniorange_mfactor_method':
			$current_userID= MO2f_Utility::mo2f_get_transient($_POST['session_id'], 'mo2f_current_user_id');
			$currentuser = get_user_by('id',$current_userID);	
			$this->mo2fa_select_method($currentuser, sanitize_text_field($_POST['mo2f_selected_mfactor_method']), null,sanitize_text_field($_POST['session_id']), esc_url_raw($_POST['redirect_to']),null);
			break;

			case 'miniorange_rba_cancle':
			
			$this->check_rba_cancalation($_POST);
			break;

			case 'miniorange_forgotphone':
			$this->check_miniorange_challenge_forgotphone($_POST);
			break;

			case 'miniorange2f_back_to_inline_registration':
			$this->miniorange2f_back_to_inline_registration($_POST);
			exit;

			case 'miniorange_alternate_login_kba':
				
			$this->check_miniorange_alternate_login_kba($_POST);
			break;

			case 'miniorange_kba_validate':
			$this->check_kba_validation($_POST);

			break;

			case 'miniorange_mobile_validation':
			$this->check_miniorange_mobile_validation($_POST);
			break;

            case 'miniorange_duo_push_validation':
            $this->check_miniorange_duo_push_validation($_POST);
            break;

            case 'mo2f_inline_duo_authenticator_success_form':
            $this->check_mo2f_duo_authenticator_success_form($_POST);
            break;

            case 'mo2f_inline_duo_authenticator_error':
            $this->check_inline_mo2f_duo_authenticator_error($_POST);
            break;

			case 'miniorange_mobile_validation_failed':
			$this->check_miniorange_mobile_validation_failed($_POST);
			break;

			case 'miniorange_duo_push_validation_failed':
			$this->check_miniorange_duo_push_validation_failed($_POST);
			break;

			case 'miniorange_softtoken':
			$this->check_miniorange_softtoken($_POST);
				
			break;

							
			case 'miniorange_soft_token':
				
			$this->check_miniorange_soft_token($_POST);
			break;

			case 'miniorange_inline_skip_registration': 
			$this->check_miniorange_inline_skip_registration($_POST);
			break;

			case 'miniorange_attribute_collection':
				$this->check_miniorange_attribute_collection($_POST);
				break;

			case 'miniorange_inline_save_2factor_method':
				$this->save_inline_2fa_method();
				break;

			case 'mo2f_skip_2fa_setup':
				$this->mo2f_skip_2fa_setup();
				break;

			case 'miniorange_back_inline':
				$this->back_to_select_2fa();
				break;

			case 'miniorange_inline_ga_validate':
				$this->inline_validate_and_set_ga();
				break;

			case 'miniorange_inline_show_mobile_config':
				$this->inline_mobile_configure();
				break;

			case 'miniorange_inline_complete_mobile':
				$this->mo2f_inline_validate_mobile_authentication();
				break;
			case 'miniorange_inline_duo_auth_mobile_complete':
			    $this->mo2f_inline_validate_duo_authentication();
                break;
            case 'duo_mobile_send_push_notification_for_inline_form':
               $this->mo2f_duo_mobile_send_push_notification_for_inline_form();
               break;    
			case 'mo2f_inline_kba_option':
				$this->mo2f_inline_validate_kba();
				break;

			case 'miniorange_inline_complete_otp_over_sms':
				$this->mo2f_inline_send_otp();
				break;

			case 'miniorange_inline_complete_otp':
				$this->mo2f_inline_validate_otp();
				break;

			case 'miniorange_inline_login':
				$this->mo2f_inline_login();
				break;
			case 'miniorange_inline_register':
				$this->mo2f_inline_register();
				break;
			case 'mo2f_users_backup1':
				$this->mo2f_download_backup_codes_inline();
				break;
			case 'mo2f_goto_wp_dashboard':
				$this->mo2f_goto_wp_dashboard();
				break;
			case 'miniorange_backup_nonce':
				$this->mo2f_use_backup_codes($_POST);
				break;
			case 'miniorange_validate_backup_nonce':
				$this->check_backup_codes_validation($_POST);
				break;
			case 'miniorange_create_backup_codes':
				$this->mo2f_create_backup_codes();
				break;
			default:
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid Request.' ) );

				return $error;
				break;


			}
		}
	}
	
	function deniedMessage($message)
	{
		if(empty($message) && get_option("deniedMessage") )
		{
			delete_option('deniedMessage');
		}
		else
			return $message;
	}
	function remove_current_activity($session_id) {
		global $Mo2fdbQueries;
		$session_variables = array(
			'mo2f_current_user_id',
			'mo2f_1stfactor_status',
			'mo_2factor_login_status',
			'mo2f-login-qrCode',
			'mo2f_transactionId',
			'mo2f_login_message',
			'mo2f_rba_status',
			'mo_2_factor_kba_questions',
			'mo2f_show_qr_code',
			'mo2f_google_auth',
			'mo2f_authy_keys'
		);

		$cookie_variables = array(
			'mo2f_current_user_id',
			'mo2f_1stfactor_status',
			'mo_2factor_login_status',
			'mo2f-login-qrCode',
			'mo2f_transactionId',
			'mo2f_login_message',
			'mo2f_rba_status_status',
			'mo2f_rba_status_sessionUuid',
			'mo2f_rba_status_decision_flag',
			'kba_question1',
			'kba_question2',
			'mo2f_show_qr_code',
			'mo2f_google_auth',
			'mo2f_authy_keys'
		);

		$temp_table_variables = array(
			'session_id',
			'mo2f_current_user_id',
			'mo2f_login_message',
			'mo2f_1stfactor_status',
			'mo2f_transactionId',
			'mo_2_factor_kba_questions',
			'mo2f_rba_status',
			'ts_created'
        );

		MO2f_Utility::unset_session_variables( $session_variables );
		MO2f_Utility::unset_cookie_variables( $cookie_variables );
		$key        = get_option( 'mo2f_encryption_key' );
		$session_id = MO2f_Utility::decrypt_data( $session_id, $key );
		$session_id_hash = md5($session_id);
		$Mo2fdbQueries->save_user_login_details( $session_id_hash, array( 
				
				'mo2f_current_user_id' => '',
				'mo2f_login_message' => '',
				'mo2f_1stfactor_status' => '',
				'mo2f_transactionId' => '',
				'mo_2_factor_kba_questions' => '',
				'mo2f_rba_status' => '',
				'ts_created' => ''
								) );
	

	}
	function mo2f_ultimate_member_custom_login(){
		echo '<div id="mo2f_um_validate_otp" class="um-field um-field-password  um-field-user_password um-field-password um-field-type_password" data-key="user_password"><div class="um-field-label"><label for="mo2f_um_validate_otp">Two factor code*</label><div class="um-clear"></div></div><div class="um-field-area"><input class="um-form-field valid " type="text" name="mo2f_validate_otp_token" id="mo2f_um_validate_otp" value="" placeholder="" data-validate="" data-key="user_password">

						</div></div>';
	}

	public function miniorange_pass2login_start_session() {
		if ( ! session_id() || session_id() == '' || ! isset( $_SESSION ) ) {
			$session_path = ini_get('session.save_path');
			if( is_writable($session_path) && is_readable($session_path) ) {
			    if(session_status() != PHP_SESSION_DISABLED )
				   session_start();
			}
		}
	}

	function mo2f_pass2login_kba_verification( $user_id, $redirect_to, $session_id  ) {
		global $Mo2fdbQueries,$LoginuserID;
		$LoginuserID = $user_id;
		$user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user_id );
		if(is_null($session_id)) {
            $session_id=$this->create_session();
        }
        if(MO2F_IS_ONPREM){
        $question_answers 		= get_user_meta($user_id , 'mo2f_kba_challenge', true);
        $challenge_questions 	= array_keys($question_answers);
		$random_keys 			= array_rand($challenge_questions,2);
		$challenge_ques1 		= $challenge_questions[$random_keys[0]];
		$challenge_ques2 		= $challenge_questions[$random_keys[1]];
		$questions[0]			=  array('question'=>addslashes($challenge_ques1));
		$questions[1]			=  array('question'=>addslashes($challenge_ques2));
		update_user_meta( $user_id, 'kba_questions_user', $questions );
		$mo2fa_login_message 		= 'Please answer the following questions:';
		$mo2fa_login_status  		= 'MO_2_FACTOR_CHALLENGE_KBA_AUTHENTICATION';
		$mo2f_kbaquestions 			= $questions;
			MO2f_Utility::mo2f_set_transient($session_id, 'mo_2_factor_kba_questions', $questions);
		$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id ,$this->mo2f_kbaquestions );
        }

        else{
		$challengeKba = new Customer_Setup();
		$content      = $challengeKba->send_otp_token( $user_email, 'KBA', get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) );
		$response     = json_decode( $content, true );
		if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate Qr code */
			if ( $response['status'] == 'SUCCESS' ) {
				MO2f_Utility::set_user_values( $session_id,"mo2f_transactionId", $response['txId'] );
				$this->mo2f_transactionid = $response['txId'];
				$questions                             = array();
				$questions[0]                          = $response['questions'][0];
				$questions[1]                          = $response['questions'][1];
					MO2f_Utility::mo2f_set_transient($session_id, 'mo_2_factor_kba_questions', $questions);
				$this->mo2f_kbaquestions=$questions;
				$mo2fa_login_message = 'Please answer the following questions:';
				$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_KBA_AUTHENTICATION';
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id ,$this->mo2f_kbaquestions );
			} else if ( $response['status'] == 'ERROR' ) {
				$this->remove_current_activity($session_id);
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: An error occured while processing your request. Please Try again.' ) );

				return $error;
			}
		} else {
			$this->remove_current_activity($session_id);
			$error = new WP_Error();
			$error->add( 'empty_username', __( '<strong>ERROR</strong>: An error occured while processing your request. Please Try again.' ) );

			return $error;
		}
	}
	}

	function miniorange_pass2login_form_fields( $mo2fa_login_status = null, $mo2fa_login_message = null, $redirect_to = null, $qrCode = null, $session_id_encrypt=null,$show_back_button =null ,$mo2fa_transaction_id =false ) {
		
		$login_status  = $mo2fa_login_status;
		$login_message = $mo2fa_login_message;
		switch ($login_status) {
			case 'MO_2_FACTOR_CHALLENGE_MOBILE_AUTHENTICATION':
			$transactionid = $this->mo2f_transactionid ? $this->mo2f_transactionid : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_transactionId' );
			mo2f_get_qrcode_authentication_prompt( $login_status, $login_message, $redirect_to, $qrCode, $session_id_encrypt, $transactionid  );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_SOFT_TOKEN':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
			

			mo2f_get_otp_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id  );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_otp_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id,$show_back_button ,$mo2fa_transaction_id );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_OTP_OVER_TELEGRAM':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_otp_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id  );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_OTP_OVER_WHATSAPP':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_otp_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id  );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_OTP_OVER_SMS':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_otp_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id  );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_PHONE_VERIFICATION':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_otp_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id  );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_GOOGLE_AUTHENTICATION':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_otp_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id  );
			exit;
			break;
			case 'MO_2_FACTOR_CHALLENGE_DUO_PUSH_NOTIFICATIONS':
			   $user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
				mo2f_get_duo_push_authentication_prompt( $login_status, $login_message, $redirect_to, $session_id_encrypt,$user_id  
				);
			exit;	
			break;	

			case 'MO_2_FACTOR_CHALLENGE_KBA_AND_OTP_OVER_EMAIL':
				mo2f_get_forgotphone_form( $login_status, $login_message, $redirect_to, $session_id_encrypt  );
			exit;
			break;

			case 'MO_2_FACTOR_CHALLENGE_PUSH_NOTIFICATIONS':
			$transactionid = $this->mo2f_transactionid ? $this->mo2f_transactionid : MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'mo2f_transactionId' );
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_push_notification_oobemail_prompt( $user_id, $login_status, $login_message, $redirect_to, $session_id_encrypt, $transactionid  );
			exit;
			break;

			case 'MO_2_FACTOR_CHALLENGE_OOB_EMAIL':
			$transactionid = $this->mo2f_transactionid ? $this->mo2f_transactionid : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_transactionId' );
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			mo2f_get_push_notification_oobemail_prompt( $user_id, $login_status, $login_message, $redirect_to, $session_id_encrypt, $transactionid  );
			exit;
			break;

			case 'MO_2_FACTOR_RECONFIG_GOOGLE':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			$this->mo2f_redirect_shortcode_addon( $user_id, $login_status, $login_message, 'reconfigure_google' );
			exit;
			break;

			case 'MO_2_FACTOR_RECONFIG_KBA':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			$this->mo2f_redirect_shortcode_addon( $user_id, $login_status, $login_message, 'reconfigure_kba' );
			exit;
			break;

			case 'MO_2_FACTOR_SETUP_SUCCESS':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');

			$this->mo2f_inline_setup_success($user_id,$redirect_to,$session_id_encrypt);
			break;

			case 'MO_2_FACTOR_GENERATE_BACKUP_CODES':

			mo2f_backup_codes_generate($redirect_to, $session_id_encrypt);
			exit;

			case 'MO_2_FACTOR_CHALLENGE_BACKUP':
				mo2f_backup_form($login_status, $login_message, $redirect_to, $session_id_encrypt);
			exit;

			case 'MO_2_FACTOR_CHALLENGE_KBA_AUTHENTICATION':
			
			if(MO2F_IS_ONPREM){
				$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'mo2f_current_user_id' );

				$ques = get_user_meta( $user_id, 'kba_questions_user');
				mo2f_get_kba_authentication_prompt($login_status, $login_message, $redirect_to, $session_id_encrypt, $ques[0]  );
			}
			else{
				$kbaquestions = $this->mo2f_kbaquestions ? $this->mo2f_kbaquestions : MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo_2_factor_kba_questions');
				mo2f_get_kba_authentication_prompt($login_status, $login_message, $redirect_to, $session_id_encrypt, $kbaquestions  );
				}
			exit;
			break;

			case 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE':
				mo2f_get_device_form( $redirect_to, $session_id_encrypt  );
			exit;
			break;

			case 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS':
			$user_id = $this->mo2f_userID ? $this->mo2f_userID : MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'mo2f_current_user_id' );

			prompt_user_to_select_2factor_mthod_inline($user_id, $login_status, $login_message,$redirect_to,$session_id_encrypt,$qrCode);
			exit;
			break;

			default:
				$this->mo_2_factor_pass2login_show_wp_login_form();

			break;
		}
	}

	function miniorange_pass2login_check_mobile_status( $login_status ) {    //mobile authentication
		if ( $login_status == 'MO_2_FACTOR_CHALLENGE_MOBILE_AUTHENTICATION' ) {
			return true;
		}

		return false;
	}

	function miniorange_pass2login_check_otp_status( $login_status, $sso = false ) {
		if ( $login_status == 'MO_2_FACTOR_CHALLENGE_SOFT_TOKEN' || $login_status == 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL' || $login_status == 'MO_2_FACTOR_CHALLENGE_OTP_OVER_SMS' || $login_status == 'MO_2_FACTOR_CHALLENGE_PHONE_VERIFICATION' || $login_status == 'MO_2_FACTOR_CHALLENGE_GOOGLE_AUTHENTICATION' ) {
			return true;
		}

		return false;
	}

	function miniorange_pass2login_check_forgotphone_status( $login_status ) {  // after clicking on forgotphone link when both kba and email are configured
		if ( $login_status == 'MO_2_FACTOR_CHALLENGE_KBA_AND_OTP_OVER_EMAIL' ) {
			return true;
		}

		return false;
	}

	function miniorange_pass2login_check_push_oobemail_status( $login_status ) {  // for push and out of and email
		if ( $login_status == 'MO_2_FACTOR_CHALLENGE_PUSH_NOTIFICATIONS' || $login_status == 'MO_2_FACTOR_CHALLENGE_OOB_EMAIL' ) {
			return true;
		}

		return false;
	}

	function miniorange_pass2login_reconfig_google( $login_status ) {
		if ( $login_status == 'MO_2_FACTOR_RECONFIG_GOOGLE' ) {
			return true;
		}

		return false;
	}

	function mo2f_redirect_shortcode_addon( $current_user_id, $login_status, $login_message, $identity ) {

		do_action( 'mo2f_shortcode_addon', $current_user_id, $login_status, $login_message, $identity );


	}

	function miniorange_pass2login_reconfig_kba( $login_status ) {
		if ( $login_status == 'MO_2_FACTOR_RECONFIG_KBA' ) {
			return true;
		}

		return false;
	}

	function miniorange_pass2login_check_kba_status( $login_status ) {
		if ( $login_status == 'MO_2_FACTOR_CHALLENGE_KBA_AUTHENTICATION' ) {
			return true;
		}

		return false;
	}

	function miniorange_pass2login_check_trusted_device_status( $login_status ) {

		if ( $login_status == 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE' ) {
			return true;
		}

		return false;
	}

	function mo_2_factor_pass2login_woocommerce(){
		?>
			<input type="hidden" name="mo_woocommerce_login_prompt" value="1">
		<?php
	}
	function mo_2_factor_pass2login_show_wp_login_form() {

        $session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
        if(is_null($session_id_encrypt)) {
            $session_id_encrypt=$this->create_session();
        }
        if(class_exists('Theme_My_Login'))
        {
        	wp_enqueue_script( 'tmlajax_script', plugins_url( 'includes/js/tmlajax.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
        	wp_localize_script( 'tmlajax_script', 'my_ajax_object',
            	array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }
        if(class_exists('LoginWithAjax')){
            wp_enqueue_script( 'login_with_ajax_script', plugins_url( 'includes/js/login_with_ajax.js', dirname(dirname(__FILE__))),[],MO2F_VERSION);
        	wp_localize_script( 'login_with_ajax_script', 'my_ajax_object',
             	array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }
        ?>
        <p><input type="hidden" name="miniorange_login_nonce"
                  value="<?php echo esc_html(wp_create_nonce( 'miniorange-2-factor-login-nonce' )); ?>"/>

            <input type="hidden" id="sessid" name="session_id"
                   value="<?php echo esc_html($session_id_encrypt); ?>"/>

        </p>

		<?php
		if ( get_option( 'mo2f_remember_device' ) ) {
			?>
            <p><input type="hidden" id="miniorange_rba_attribures" name="miniorange_rba_attribures" value=""/></p>
			<?php
			wp_enqueue_script( 'jquery');
			wp_enqueue_script( 'flash_script', plugins_url( 'includes/js/rba/js/jquery.flash.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
			wp_enqueue_script( 'uaparser_script', plugins_url( 'includes/js/rba/js/ua-parser.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
			wp_enqueue_script( 'client_script', plugins_url( 'includes/js/rba/js/client.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
			wp_enqueue_script( 'device_script', plugins_url( 'includes/js/rba/js/device_attributes.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
			wp_enqueue_script( 'swf_script', plugins_url( 'includes/js/rba/js/swfobject.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
			wp_enqueue_script( 'font_script', plugins_url( 'includes/js/rba/js/fontdetect.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
			wp_enqueue_script( 'murmur_script', plugins_url( 'includes/js/rba/js/murmurhash3.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
			wp_enqueue_script( 'miniorange_script', plugins_url( 'includes/js/rba/js/miniorange-fp.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION );
		}else{

			
			if( MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option'))
			{
				echo "<p>";
				echo '<div id="mo2f_backup_code_secton"><label title="'.__('If you don\'t have 2-factor authentication enabled for your WordPress account, leave this field empty.','google-authenticator').'" for="mo2f_2fa_code">'.__('2 Factor Authentication code*','google-authenticator').'</label><span id="google-auth-info"></span><br/>';
				echo '<input type="text" placeholder="No soft Token ? Skip" class="input" style="font-size:15px;margin:0px" name="mo_softtoken" id="mo2f_2fa_code" class="mo2f_2fa_code" style="ime-mode: inactive;" />';
				echo '<p style="color:#2271b1;font-size:12px; margin-bottom:5px">* Skip the authentication code if it doesn\'t apply.</p></div>';
				echo "</p>";
				echo '<input type="checkbox" id="mo2f_use_backup_code" name="mo2f_use_backup_code" onclick="mo2f_handle_backup_codes(this);" value="mo2f_use_backup_code">
						<label for="mo2f_use_backup_code"> Use Backup Codes</label><br><br>';
				echo '<script>
						function mo2f_handle_backup_codes(e){
							if(e.checked)
							document.querySelector("#mo2f_backup_code_secton").style.display="none";
							else
							document.querySelector("#mo2f_backup_code_secton").style.display="block";

						}

					</script>';

			}
        }

	}

	function mo2f_pass2login_mobile_verification( $user, $redirect_to, $session_id_encrypt=null ) {
        global $Mo2fdbQueries,$moWpnsUtility;
        if (is_null($session_id_encrypt)){
            $session_id_encrypt=$this->create_session();
    	}
		$user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
		$useragent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
		 MO2f_Utility::mo2f_debug_file('Check user agent to check request from mobile device '.$useragent);
		if ( MO2f_Utility::check_if_request_is_from_mobile_device( $useragent ) ) {
			$session_cookie_variables = array( 'mo2f-login-qrCode', 'mo2f_transactionId' );

			MO2f_Utility::unset_session_variables( $session_cookie_variables );
			MO2f_Utility::unset_cookie_variables( $session_cookie_variables);
			MO2f_Utility::unset_temp_user_details_in_table( 'mo2f_transactionId',$session_id_encrypt);

			$mo2fa_login_message = 'Please enter the one time passcode shown in the miniOrange<b> Authenticator</b> app.';
			$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_SOFT_TOKEN';
			 MO2f_Utility::mo2f_debug_file('Request from mobile device so promting soft token'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
			$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt );
		} else {
			$challengeMobile = new Customer_Setup();
			$content         = $challengeMobile->send_otp_token( $user_email, 'MOBILE AUTHENTICATION', get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) );
			$response        = json_decode( $content, true );
			if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate Qr code */
				if ( $response['status'] == 'SUCCESS' ) {
					$qrCode = $response['qrCode'];
					MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'mo2f_transactionId', $response['txId']);


					$this->mo2f_transactionid=$response['txId'];
					$mo2fa_login_message = '';
					$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_MOBILE_AUTHENTICATION';
					MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' Sent miniOrange QR code Authentication successfully'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
					$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to, $qrCode,$session_id_encrypt );
				} else if ( $response['status'] == 'ERROR' ) {
					$this->remove_current_activity($session_id_encrypt);
					MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' An error occured while processing your request'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
					$error = new WP_Error();
					$error->add( 'empty_username', __( '<strong>ERROR</strong>: An error occured while processing your request. Please Try again.' ) );

					return $error;
				}
			} else {
				MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' An error occured while processing your request'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
				$this->remove_current_activity($session_id_encrypt);
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: An error occured while processing your request. Please Try again.' ) );

				return $error;
			}
		}

	}

	function mo2f_pass2login_duo_push_verification( $currentuser, $mo2f_second_factor, $redirect_to, $session_id_encrypt ){
			global $Mo2fdbQueries;
			include_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'two_fa_duo_handler.php';
			  if (is_null($session_id_encrypt)){
            $session_id_encrypt=$this->create_session();
    	    }
	       
           $mo2fa_login_message ='';
	       $mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_DUO_PUSH_NOTIFICATIONS';
		   $this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id_encrypt);
		    
	}

	function mo2f_pass2login_push_oobemail_verification( $current_user, $mo2f_second_factor, $redirect_to, $session_id=null ) {
        
   		global $Mo2fdbQueries,$moWpnsUtility;
        if(is_null($session_id)){
            $session_id=$this->create_session();
        }
        $challengeMobile = new Customer_Setup();
		$user_email      = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user->ID );
		if(MO2F_IS_ONPREM && $mo2f_second_factor != "PUSH NOTIFICATIONS"){
			 MO2f_Utility::mo2f_debug_file('Push notification has sent successfully for '.$mo2f_second_factor.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$current_user->ID.' Email-'.$current_user->user_email);
	        include_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'Mo2f_OnPremRedirect.php';
	        $mo2fOnPremRedirect = new Mo2f_OnPremRedirect();
	        $content =  $mo2fOnPremRedirect->mo2f_pass2login_push_email_onpremise($current_user, $redirect_to, $session_id );

        }else {
	        $content = $challengeMobile->send_otp_token( $user_email, $mo2f_second_factor, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) );
        }
       $response        = json_decode( $content, true );
		if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate Qr code */
			if ( $response['status'] == 'SUCCESS' ) {
				MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_transactionId', $response['txId']);
				update_user_meta($current_user->ID,'mo2f_EV_txid',$response['txId']);

                MO2f_Utility::mo2f_debug_file('Push notification has sent successfully for '.$mo2f_second_factor.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$current_user->ID.' Email-'.$current_user->user_email);
				$this->mo2f_transactionid=$response['txId'];

				$mo2fa_login_message = $mo2f_second_factor == 'PUSH NOTIFICATIONS' ? 'A Push Notification has been sent to your phone. We are waiting for your approval.' : 'An email has been sent to ' . MO2f_Utility::mo2f_get_hidden_email( $user_email ) . '. We are waiting for your approval.';
				$mo2fa_login_status  = $mo2f_second_factor == 'PUSH NOTIFICATIONS' ? 'MO_2_FACTOR_CHALLENGE_PUSH_NOTIFICATIONS' : 'MO_2_FACTOR_CHALLENGE_OOB_EMAIL';
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null,$session_id);
			} else if ( $response['status'] == 'ERROR' || $response['status'] == 'FAILED' ) {
				MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_transactionId', $response['txId']);
				update_user_meta($current_user->ID,'mo2f_EV_txid',$response['txId']);

                 MO2f_Utility::mo2f_debug_file('An error occured while sending push notification-'.$mo2f_second_factor.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$current_user->ID.' Email-'.$current_user->user_email);
				$this->mo2f_transactionid=$response['txId'];
				$mo2fa_login_message = $mo2f_second_factor == 'PUSH NOTIFICATIONS' ? 'An error occured while sending push notification to your app. You can click on <b>Phone is Offline</b> button to enter soft token from app or <b>Forgot your phone</b> button to receive OTP to your registered email.' : 'An error occured while sending email. Please try again.';
				$mo2fa_login_status  = $mo2f_second_factor == 'PUSH NOTIFICATIONS' ? 'MO_2_FACTOR_CHALLENGE_PUSH_NOTIFICATIONS' : 'MO_2_FACTOR_CHALLENGE_OOB_EMAIL';
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to, null,$session_id );
			}
		} else {
			MO2f_Utility::mo2f_debug_file('An error occured while processing your request.'. 'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$current_user->ID.' Email-'.$current_user->user_email);
			$this->remove_current_activity($session_id);
			$error = new WP_Error();
			$error->add( 'empty_username', __( '<strong>ERROR</strong>: An error occured while processing your request. Please Try again.' ) );

			return $error;
		}	
	}

	function mo2f_pass2login_otp_verification( $user, $mo2f_second_factor, $redirect_to,$session_id=null ) {
		global $Mo2fdbQueries,$moWpnsUtility;
	
        if(is_null($session_id)){
            $session_id=$this->create_session();
        }
		$mo2f_external_app_type = get_user_meta( $user->ID, 'mo2f_external_app_type', true );
		if($mo2f_second_factor == 'EMAIL')
		{	
			$mo2f_user_phone        = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
			$wdewdeqdqq = get_site_option(base64_encode("remainingOTP"));
			if($wdewdeqdqq >get_site_option('EmailTransactionCurrent', 30) or get_site_option(base64_encode("limitReached")))
			{
				update_site_option(base64_encode("remainingOTP"),0);
			}
		}
		else	
			$mo2f_user_phone        = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
		if ( $mo2f_second_factor == 'SOFT TOKEN' ) {
			$mo2fa_login_message = 'Please enter the one time passcode shown in the miniOrange<b> Authenticator</b> app.';
			$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_SOFT_TOKEN';
			MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
			$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to, null,$session_id );
		} else if ( $mo2f_second_factor == 'GOOGLE AUTHENTICATOR' ) {
			$mo2fa_login_message ='Please enter the one time passcode shown in the <b> Authenticator</b> app.';
			$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_GOOGLE_AUTHENTICATION';
			 MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
			$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to, null,$session_id );
		} elseif ($mo2f_second_factor == 'OTP Over Telegram') {
			$chatID     = get_user_meta($user->ID,'mo2f_chat_id',true);
			$otpToken 	= '';
			for($i=1;$i<7;$i++)
			{
				$otpToken 	.= rand(0,9);
			}

			update_user_meta($user->ID,'mo2f_otp_token',$otpToken);
			update_user_meta($user->ID,'mo2f_telegram_time',time());
		
			$url = esc_url(MoWpnsConstants::TELEGRAM_OTP_LINK);
			$postdata = array( 'mo2f_otp_token' => $otpToken,
				'mo2f_chatid' => $chatID
			);

			$args = array(
				'method'      => 'POST',
				'timeout'     => 10,
				'sslverify'   => false,
				'headers'     => array(),
				'body'        => $postdata,
			);

			$mo2f_api=new Mo2f_Api();
			$data=$mo2f_api->mo2f_wp_remote_post($url,$args);
			
			if($data == 'SUCCESS')
			{
				$mo2fa_login_message ='Please enter the one time passcode sent on your<b> Telegram</b> app.';
				$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_OTP_OVER_TELEGRAM';
				MO2f_Utility::mo2f_debug_file($mo2fa_login_status.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id );
			}

		} 
		else {
			$challengeMobile = new Customer_Setup();
			$content = '';
			$response = [];
			$otpLIMiTE = 0;
			if(MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option')>0 or $mo2f_second_factor != 'EMAIL')
			{
				if($mo2f_second_factor == 'OTP Over SMS')
					$mo2f_second_factor = 'SMS';
				$content         = $challengeMobile->send_otp_token( $mo2f_user_phone, $mo2f_second_factor, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ,$user);
				$response        = json_decode( $content, true );
		
			}
			else
			{
				MO2f_Utility::mo2f_debug_file('Error in sending OTP over Email or SMS.'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
				$response['status'] = 'FAILED';
				$response['message'] = '<p style = "color:red;">OTP limit has been exceeded</p>';
				$otpLIMiTE = 1;
			}
			if ( json_last_error() == JSON_ERROR_NONE ) {
				if ( $response['status'] == 'SUCCESS' ) {
					if($mo2f_second_factor == 'EMAIL')
					{
					MO2f_Utility::mo2f_debug_file(' OTP has been sent successfully over email.'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
						$cmVtYWluaW5nT1RQ = MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option');
						if($cmVtYWluaW5nT1RQ>0)
						update_site_option("cmVtYWluaW5nT1RQ",$cmVtYWluaW5nT1RQ-1);
					}
					elseif($mo2f_second_factor == 'SMS')
					{
						MO2f_Utility::mo2f_debug_file(' OTP has been sent successfully over phone.'.' User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$user->ID.' Email-'.$user->user_email);
						$mo2f_sms = get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z');
						if($mo2f_sms>0)
						update_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z',$mo2f_sms-1);
					}
					if(!isset($response['phoneDelivery']['contact']))
						$response['phoneDelivery']['contact'] = '';
					$message = 'The OTP has been sent to ' . MO2f_Utility::get_hidden_phone( $response['phoneDelivery']['contact'] ) . '. Please enter the OTP you received to Validate.';
					update_option( 'mo2f_number_of_transactions', MoWpnsUtility::get_mo2f_db_option('mo2f_number_of_transactions', 'get_option') - 1 );
					MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_transactionId', $response['txId']);


					$this->mo2f_transactionid=$response['txId'];
					$mo2fa_login_message = $message;
					$currentMethod        = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
					if($mo2f_second_factor == 'EMAIL')
						$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL';
					else	
						$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_OTP_OVER_SMS';
					$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id );
				} else {


					if($response['message'] == 'TEST FAILED.')
						$response['message'] = 'There is an error in sending the OTP.';

					$last_message = 'Or  <a href = " https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=otp_recharge_plan">puchase trascactions</a>';
					
					if($otpLIMiTE ==1)
						$last_message = 'or contact miniOrange';

					else if(MO2F_IS_ONPREM and ($mo2f_second_factor == 'OTP Over Email' or $mo2f_second_factor =='EMAIL' or $mo2f_second_factor == 'Email Verification'))
						$last_message = 'Or check your SMTP Server and remaining transacions.';	
					else
						{
							$last_message = 'Or <a href="'.MoWpnsConstants::VIEW_TRANSACTIONS.'"> Check your remaining transacions </a>';
							if($user->user_email === get_site_option('mo2f_email'))
								$last_message = $last_message . 'or </br><a href="'.MoWpnsConstants::rechargeLink.'">Add SMS Transactions</a> to your account';
						}
					$message = $response['message'] . ' You can click on <a href="https://faq.miniorange.com/knowledgebase/i-am-locked-cant-access-my-account-what-do-i-do/">I am locked out</a> to login via alternate method '.$last_message;
					if(!isset($response['txId']))
						$response['txId'] = '';
					MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_transactionId', $response['txId']);


					$this->mo2f_transactionid=$response['txId'];
					$mo2fa_login_message = $message;
					$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_OTP_OVER_SMS';
					$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id );
				}
			} else {
				$this->remove_current_activity($session_id);
				$error = new WP_Error();
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: An error occured while processing your request. Please Try again.' ) );
				return $error;
			}
		}
	}

	function mo2fa_pass2login( $redirect_to = null, $session_id_encrypted=null ) {
		global $Mo2fdbQueries;
		if(empty($this->mo2f_userID)&&empty($this->fstfactor)){
			$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypted, 'mo2f_current_user_id');
			$mo2f_1stfactor_status = MO2f_Utility::mo2f_get_transient( $session_id_encrypted, 'mo2f_1stfactor_status' );
			
		} else {
			$user_id=$this->mo2f_userID;
			$mo2f_1stfactor_status=$this->fstfactor;
		}

		if ( $user_id && $mo2f_1stfactor_status && ( $mo2f_1stfactor_status == 'VALIDATE_SUCCESS' ) ) {
			$currentuser = get_user_by( 'id', $user_id );
			wp_set_current_user( $user_id, $currentuser->user_login );
			$mobile_login = new Miniorange_Mobile_Login();
			$mobile_login->remove_current_activity($session_id_encrypted);

			delete_expired_transients( true );
			delete_site_option($session_id_encrypted);

			wp_set_auth_cookie( $user_id, true );
			do_action( 'wp_login', $currentuser->user_login, $currentuser );
			redirect_user_to( $currentuser, $redirect_to );
			exit;
		} else {
			$this->remove_current_activity($session_id_encrypted);
		}
	}

	function create_session(){
        global $Mo2fdbQueries;
        $session_id = MO2f_Utility::random_str(20);
		$session_id_hash = md5($session_id);
        $Mo2fdbQueries->insert_user_login_session($session_id_hash);
		$key = get_option('mo2f_encryption_key');
		$session_id_encrypt = MO2f_Utility::encrypt_data($session_id, $key);
        return $session_id_encrypt;
    }

	function miniorange_initiate_2nd_factor( $currentuser, $attributes = null, $redirect_to = null, $otp_token = "",$session_id_encrypt=null ) {
		global $Mo2fdbQueries,$moWpnsUtility;
		 MO2f_Utility::mo2f_debug_file('MO initiate 2nd factor'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
        $this->miniorange_pass2login_start_session();
		if(is_null($session_id_encrypt)) {
			$session_id_encrypt=$this->create_session();
		}

		if(class_exists('UM_Functions'))
		{
			 MO2f_Utility::mo2f_debug_file('Using UM login form.');
			if(!isset($_POST['wp-submit']) and isset($_POST['um_request']))
			{
				$meta = get_option('um_role_'.$currentuser->roles[0].'_meta');
				if(isset($meta) and $meta != '')
				{	
					if(isset($meta['_um_login_redirect_url']))
						$redirect_to = $meta['_um_login_redirect_url'];
					if($redirect_to == '')
					{
						$redirect_to = get_site_url();
					}
				}
				$login_form_url = '';
				if(isset($_POST['redirect_to']))
					$login_form_url = esc_url_raw($_POST['redirect_to']);
				
				if($login_form_url != '' and !is_null($login_form_url))
				{
					$redirect_to = $login_form_url;
				}

			}
		
		}
		MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'mo2f_current_user_id', $currentuser->ID, 600);
		MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'mo2f_1stfactor_status', 'VALIDATE_SUCCESS', 600);


		$this->mo2f_userID=$currentuser->ID;
		$this->fstfactor='VALIDATE_SUCCESS';

		$is_customer_admin = true;

		$dG90YWxVc2Vyc0Nsb3Vk = get_site_option("dG90YWxVc2Vyc0Nsb3Vk"); //directly added without encoding
		if($dG90YWxVc2Vyc0Nsb3Vk<3)
			$is_customer_admin = true;

		$roles = ( array ) $currentuser->roles;
		$twofactor_enabled  = 0;
		foreach ( $roles as $role ) {
			if(get_option('mo2fa_'.$role)=='1')
				$twofactor_enabled=1;
		}
		  if ($twofactor_enabled!=1 && is_super_admin( $currentuser->ID )){
                  	if(get_site_option('mo2fa_superadmin')==1){
                      		$twofactor_enabled=1;
                  	}
		  }

		if ( $is_customer_admin && $twofactor_enabled ) {
			$mo_2factor_user_registration_status = $Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status', $currentuser->ID );
			$kba_configuration_status            = $Mo2fdbQueries->get_user_detail( 'mo2f_SecurityQuestions_config_status', $currentuser->ID );
			
			if(MoWpnsUtility::get_mo2f_db_option('mo2f_enable_brute_force', 'get_option')){
				$mo2f_allwed_login_attempts=get_option('mo2f_allwed_login_attempts');
			}else{
				$mo2f_allwed_login_attempts= 'disabled';
			}
			update_user_meta( $currentuser->ID, 'mo2f_user_login_attempts', $mo2f_allwed_login_attempts );

			$twofactor_transactions = new Mo2fDB;
			$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($currentuser->ID);
			$tfa_enabled = $Mo2fdbQueries->get_user_detail( 'mo2f_2factor_enable_2fa_byusers', $currentuser->ID );
			if($tfa_enabled == 0 && ($mo_2factor_user_registration_status != 'MO_2_FACTOR_PLUGIN_SETTINGS') && $tfa_enabled != '')
				$exceeded =1;		

            if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' ) { //checking if user has configured any 2nd factor method
	            $email                               = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $currentuser->ID );
                try {
					$mo2f_rba_status             = mo2f_collect_attributes( $email, stripslashes( $attributes ) ); // Rba flow
					MO2f_Utility::set_user_values( $session_id_encrypt, 'mo2f_rba_status', $mo2f_rba_status );
					$this->mo2f_rbastatus=$mo2f_rba_status;
				} catch ( Exception $e ) {
					echo $e->getMessage();
				}

				if ( $mo2f_rba_status['status'] == 'SUCCESS' && $mo2f_rba_status['decision_flag'] ) {
					$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
				} else if ( ($mo2f_rba_status['status'] == 'DENY' ) && get_option( 'mo2f_rba_installed' ) ) {

					$this->mo2f_restrict_access( 'Access_denied' );
					exit;
				} else if ( ($mo2f_rba_status['status'] == 'ERROR') && get_option( 'mo2f_rba_installed' ) ) {
					$this->mo2f_restrict_access( 'Access_denied' );
					exit;
				} else {

					$mo2f_second_factor = '';

                        $mo2f_second_factor = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $currentuser->ID );

                    if(!MO2F_IS_ONPREM and $mo2f_second_factor!= 'OTP Over Telegram') 
                        $mo2f_second_factor = mo2f_get_user_2ndfactor( $currentuser );

                      // adding function for the mfa call
					 					  
					 					  $configure_array_method = $this->mo2fa_return_methods_value($currentuser->ID);
					 					 
                       if (sizeof( $configure_array_method) >1 && get_site_option('mo2f_nonce_enable_configured_methods') == true && !MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option')) {
                       		update_site_option('mo2f_login_with_mfa_use','1');
                       		mo2fa_prompt_mfa_form_for_user($configure_array_method,$session_id_encrypt,$redirect_to);
                       		exit;
                       }
                      else
                      	$this->mo2fa_select_method($currentuser, $mo2f_second_factor, $otp_token,$session_id_encrypt, $redirect_to,$kba_configuration_status);
				}
			}else if(!$exceeded && (MoWpnsUtility::get_mo2f_db_option('mo2f_inline_registration', 'site_option') || $this->mo2f_is_grace_period_expired($currentuser) )){
			  $this->mo2fa_inline( $currentuser, $redirect_to, $session_id_encrypt );

            } else {
            	if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request'))
					$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
				else
					return $currentuser;
			}

		}else { //plugin is not activated for current role then logged him in without asking 2 factor
			$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
		}

	}
	function mo2fa_return_methods_value($currentuserid)
	{
		global $Mo2fdbQueries;
		$count_methods = $Mo2fdbQueries->get_user_configured_methods($currentuserid );
		$value          = empty( $count_methods ) ? '' : get_object_vars( $count_methods[0] );
		$configured_methods_arr=array();
		foreach ($value as $config_status_option => $config_status) {
			if(strpos($config_status_option, 'config_status')){
	  		$config_status_string_array =explode('_',$config_status_option);
	  		 $config_method = MO2f_Utility::mo2f_decode_2_factor($config_status_string_array[1],'wpdb');
	  		 if(1 == $value[$config_status_option])
	  		 	array_push($configured_methods_arr,$config_method);

			}
		}

		return $configured_methods_arr;
	}
function mo2fa_select_method($currentuser, $mo2f_second_factor, $otp_token,$session_id_encrypt, $redirect_to,$kba_configuration_status)
{
	global $moWpnsUtility;
	
	if($mo2f_second_factor == 'OTP Over Email' || $mo2f_second_factor == 'OTP OVER EMAIL' || $mo2f_second_factor == "EMAIL") {
		$mo2f_second_factor = "EMAIL";
				if(MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option')<=0)
				{
							update_site_option("bGltaXRSZWFjaGVk",1);
				}
	}
	else
		$mo2f_second_factor = MO2f_Utility::mo2f_decode_2_factor($mo2f_second_factor, "server" );
	
	if($mo2f_second_factor == 'OTPOverTelegram')
		$mo2f_second_factor = "OTP Over Telegram";
	
	if((($mo2f_second_factor == 'GOOGLE AUTHENTICATOR') || ($mo2f_second_factor =='SOFT TOKEN') || ($mo2f_second_factor =='AUTHY AUTHENTICATOR')) && MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option')&& !get_option('mo2f_remember_device') && !isset($_POST['mo_woocommerce_login_prompt']) ) 
	{	
		$error=$this->mo2f_validate_soft_token($currentuser, $mo2f_second_factor, $otp_token,$session_id_encrypt, $redirect_to);
		if(is_wp_error( $error))
		{
			return $error;
		}
	}
	else
	{
		if ( MO2f_Utility::check_if_request_is_from_mobile_device( $_SERVER['HTTP_USER_AGENT'] ) && $kba_configuration_status ) 
		{
			$this->mo2f_pass2login_kba_verification( $currentuser->ID, $redirect_to, $session_id_encrypt  );
		}
		else
		{
			if ( $mo2f_second_factor == 'MOBILE AUTHENTICATION' ) 
			{
                $this->mo2f_pass2login_mobile_verification( $currentuser, $redirect_to, $session_id_encrypt );
            }
			else if ( $mo2f_second_factor == 'PUSH NOTIFICATIONS' || $mo2f_second_factor == 'OUT OF BAND EMAIL' || $mo2f_second_factor == 'Email Verification') 
			{
				 MO2f_Utility::mo2f_debug_file('Initiating 2fa validation template for '.$mo2f_second_factor.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
				$this->mo2f_pass2login_push_oobemail_verification( $currentuser, $mo2f_second_factor, $redirect_to, $session_id_encrypt );
			}
			else if ( $mo2f_second_factor == 'SOFT TOKEN' || $mo2f_second_factor == 'SMS' || $mo2f_second_factor == 'PHONE VERIFICATION' || $mo2f_second_factor == 'GOOGLE AUTHENTICATOR' || $mo2f_second_factor == 'EMAIL' ||$mo2f_second_factor == 'OTP Over Telegram'|| $mo2f_second_factor == 'OTP Over Whatsapp') {
				 MO2f_Utility::mo2f_debug_file('Initiating 2fa validation template for '.$mo2f_second_factor.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
				$this->mo2f_pass2login_otp_verification( $currentuser, $mo2f_second_factor, $redirect_to, $session_id_encrypt  );
			}
			else if ( $mo2f_second_factor == 'KBA' or $mo2f_second_factor == 'Security Questions') {
				MO2f_Utility::mo2f_debug_file('Initiating 2fa validation template for '.$mo2f_second_factor.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
				$this->mo2f_pass2login_kba_verification( $currentuser->ID, $redirect_to , $session_id_encrypt );
			}
			else if ( $mo2f_second_factor == 'Duo Authenticator') {
				 MO2f_Utility::mo2f_debug_file('Initiating 2fa validation template for '.$mo2f_second_factor.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
				$this->mo2f_pass2login_duo_push_verification( $currentuser, $mo2f_second_factor, $redirect_to, $session_id_encrypt ); 

			}
			else if ( $mo2f_second_factor == 'NONE' ) {
				MO2f_Utility::mo2f_debug_file('mo2f_second_factor is NONE'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
				if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request'))
					$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
				else
					return $currentuser;
			}
			else 
			{
				$this->remove_current_activity($session_id_encrypt);
				$error = new WP_Error();
				if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request')){
					 MO2f_Utility::mo2f_debug_file('Two factor method has not been configured '.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
					$data = array('notice' => '<div style="border-left:3px solid #dc3232;">&nbsp; Two Factor method has not been configured.', );
					wp_send_json_success($data);	
				}
				else
				{
					 MO2f_Utility::mo2f_debug_file('Two factor method has not been configured '.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
					$error->add( 'empty_username', __( '<strong>ERROR</strong>: Two Factor method has not been configured.' ) );
					return $error;
				}
			}
		}
	}
}
	function mo2fa_inline($currentuser,$redirect_to,$session_id){
		
		global $Mo2fdbQueries;
		$currentUserId = $currentuser->ID;
		$email = $currentuser->user_email;
        $Mo2fdbQueries->insert_user( $currentUserId, array( 'user_id' => $currentUserId ) );
        $Mo2fdbQueries->update_user_details( $currentUserId, array(
            'user_registration_with_miniorange' =>'SUCCESS',
            'mo2f_user_email' =>$email,
            'mo_2factor_user_registration_status' =>'MO_2_FACTOR_INITIALIZE_TWO_FACTOR'
        ) );

        $mo2fa_login_message = '';
        $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
        
        $this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message,$redirect_to,null,$session_id);
	}

	function mo2f_validate_soft_token($currentuser, $mo2f_second_factor, $softtoken,$session_id_encrypt,$redirect_to = null){
		global $Mo2fdbQueries;
		$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $currentuser->ID );
			$customer = new Customer_Setup();
			$content = json_decode( $customer->validate_otp_token( $mo2f_second_factor, $email, null, $softtoken, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
		if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
			if ( get_option( 'mo2f_remember_device' ) ) {
				$mo2fa_login_status = 'MO_2_FACTOR_REMEMBER_TRUSTED_DEVICE';
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, null, $redirect_to, null, $session_id_encrypt );
			} else {
				$this->mo2fa_pass2login( $redirect_to, $session_id_encrypt );
			}
		} else {
				if( MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request')){
					$data = array('notice' => '<div style="border-left:3px solid #dc3232;">&nbsp; Invalid One Time Passcode.', );
					wp_send_json_success($data);	
				}
				else
					return new WP_Error( 'invalid_one_time_passcode', '<strong>ERROR</strong>: Invalid One Time Passcode.');
		}
    }

    function mo2f_otp_over_email_send($email,$redirect_to,$session_id_encrypt,$current_user)
	{

		$challengeMobile = new Customer_Setup();
		$content = '';
		$response = [];
		$otpLIMiTE = 0;
		if(get_site_option("cmVtYWluaW5nT1RQ")>0)
		{
			$content         = $challengeMobile->send_otp_token( $email, 'EMAIL', get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ,$current_user);
			$response        = json_decode( $content, true );
			if(!MO2F_IS_ONPREM)
			{
				if(isset($response['txId'])){
					MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'mo2f_transactionid', $response['txId']);
				}
			}

		}
		else
		{
			$response['status'] = 'FAILED';
			$response['message'] = '<p style = "color:red;">OTP limit has been exceeded</p>';
			$otpLIMiTE = 1;
		}
		if ( json_last_error() == JSON_ERROR_NONE ) {
			if ( $response['status'] == 'SUCCESS' ) {			
			$cmVtYWluaW5nT1RQ = get_site_option("cmVtYWluaW5nT1RQ");
			if($cmVtYWluaW5nT1RQ>0)
			update_site_option("cmVtYWluaW5nT1RQ",$cmVtYWluaW5nT1RQ-1);
    		$mo2fa_login_message = 'An OTP has been sent to '.$email.' please verify to set the two-factor';
			$mo2fa_login_status  = 'MO_2_FACTOR_CHALLENGE_OTP_OVER_EMAIL';
			$mo2fa_transaction_id = isset($response['txId'])?$response['txId']:null;
			$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id_encrypt, 1 ,$mo2fa_transaction_id);
			}
			else
			{
				if($response['status'] == 'FAILED' && $response['message'] == 'OTP limit has been exceeded'){
				$mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				$mo2fa_login_message = 'There was an issue while sending the OTP to '.$email.'. Please check your remaining transactions and try again.';
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id_encrypt );
			    	}else if($response['status'] == 'FAILED'){
                                $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
				$mo2fa_login_message = 'Your SMTP has not been set, please set your SMTP first to get OTP.';
				$this->miniorange_pass2login_form_fields( $mo2fa_login_status, $mo2fa_login_message, $redirect_to,null, $session_id_encrypt );
			    }
			}
		}
	}
	function mo2f_restrict_access( $identity ) {
		apply_filters( 'mo2f_rba_addon', $identity );
		exit;
	}

	function mo2f_collect_device_attributes_for_authenticated_user( $currentuser, $redirect_to = null ) {
		$session_id=$this->create_session();
		if ( get_option( 'mo2f_remember_device' ) ) {
			$this->miniorange_pass2login_start_session();
			MO2f_Utility::set_user_values( $session_id, "mo2f_current_user_id", $currentuser->ID );
			$this->mo2f_userID=$currentuser->ID;
			mo2f_collect_device_attributes_handler($session_id,$redirect_to );
			exit;
		} else {
			$this->miniorange_initiate_2nd_factor( $currentuser, null, $redirect_to ,null ,$session_id );
		}
	}

	function mo2f_check_username_password( $user, $username, $password, $redirect_to = null ) {
		global $Mo2fdbQueries,$moWpnsUtility;
		if ( is_a( $user, 'WP_Error' ) && ! empty( $user ) ) {
			if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request')){
					$data = array('notice' => '<div style="border-left:3px solid #dc3232;">&nbsp;Invalid User Credentials', );
					wp_send_json_success($data);	
				}
			else
				return $user;
		}
		if($GLOBALS['pagenow'] == 'wp-login.php' && isset($_POST['mo_woocommerce_login_prompt'])){
			return new WP_Error( 'Unauthorized Access.' , '<strong>ERROR</strong>: Access Denied.');
		}
		// if an app password is enabled, this is an XMLRPC / APP login ?
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST  ) {

			$currentuser = wp_authenticate_username_password( $user, $username, $password );
			if ( is_wp_error( $currentuser ) ) {
				$this->error = new IXR_Error( 403, __( 'Bad login/pass combination.' ) );

				return false;
			} else {
				return $currentuser;
			}

		} else {
			$currentuser = wp_authenticate_username_password( $user, $username, $password );
			if ( is_wp_error( $currentuser ) ) {
				if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request')){
					$data = array('notice' => '<div style="border-left:3px solid #dc3232;">&nbsp; Invalid User Credentials', );
					wp_send_json_success($data);	
				}
				else{
					$currentuser->add( 'invalid_username_password', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Username or password.' ) );
					 MO2f_Utility::mo2f_debug_file('Invalid username and password.'.'User_IP-'.$moWpnsUtility->get_client_ip());
					return $currentuser;
				}
			} else {
				
				$session_id  = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
                MO2f_Utility::mo2f_debug_file('Username and password  validate successfully'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
				if(isset($_REQUEST['woocommerce-login-nonce'])){
					 MO2f_Utility::mo2f_debug_file('It is a woocommerce login form. Get woocommerce redirectUrl');
					if ( ! empty( $_REQUEST[ 'redirect_to' ] ) ) {
						$redirect_to = wp_unslash( $_REQUEST[ 'redirect_to' ] ); 
					} elseif ( isset($_REQUEST[ '_wp_http_referer' ]) ) {
						$redirect_to = sanitize_text_field($_REQUEST[ '_wp_http_referer' ]);
					} else {
						$redirect_to = wc_get_page_permalink( 'myaccount' );
					}
				}else{
					$redirect_to = isset($_REQUEST[ 'redirect_to' ]) ? sanitize_text_field($_REQUEST[ 'redirect_to' ]) : (isset($_REQUEST[ 'redirect' ]) ? sanitize_text_field($_REQUEST[ 'redirect' ]) : null);
				}
				$redirect_to = esc_url_raw($redirect_to);
                               	$mo2f_transactions = new Mo2fDB();
				$mo2f_configured_2FA_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $currentuser->ID );
                                	$mo2f_user_registration_status=$Mo2fdbQueries->get_user_detail('mo_2factor_user_registration_status',$currentuser->ID);
				$cloud_methods = array("MOBILE AUTHENTICATION","PUSH NOTIFICATIONS","SOFT TOKEN");
					if (MO2F_IS_ONPREM && $mo2f_configured_2FA_method=='Security Questions')
					{
						  MO2f_Utility::mo2f_debug_file('Initiating 2nd factor for KBA'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
							$this->miniorange_initiate_2nd_factor($currentuser, null , $redirect_to ,  "" , $session_id );
					}
					else if(MO2F_IS_ONPREM && $mo2f_configured_2FA_method =='Email Verification')
					{
						MO2f_Utility::mo2f_debug_file('Initiating 2nd factor for email verification'.'User_IP-'.$moWpnsUtility->get_client_ip() .' User_Id-'.$currentuser->ID.' Email-'.$currentuser->user_email);
						$this->miniorange_initiate_2nd_factor($currentuser, null , $redirect_to ,  null ,$session_id  );
				}
              
				else
				{	         
		                 
				      if($this->mo2f_is_new_user($currentuser))
				      {   
							
				           $twofactor_transactions = new Mo2fDB;
		                           $exceeded = $twofactor_transactions->check_alluser_limit_exceeded($currentuser->ID);
		                           if ( get_site_option('mo2fa_'.$currentuser->roles[0]) && $exceeded==false )
		                           {   
			                         if(get_site_option('mo2f_grace_period')=='on')
			                       {  
									  update_site_option('mo2f_user_login_status_'.$currentuser->ID,1);
					                  update_site_option('mo2f_grace_period_status_'.$currentuser->ID,strtotime(current_datetime()->format('h:ia M d Y')));
			                       }
		                           }
							
				      }
						
						if ( empty($_POST[ 'mo2f_use_backup_code' ]) && empty( $_POST['mo_softtoken'] ) && MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'get_option') && $mo2f_configured_2FA_method && !get_option('mo2f_remember_device') && (($mo2f_configured_2FA_method == 'Google Authenticator') ||($mo2f_configured_2FA_method == 'miniOrange Soft Token') || ($mo2f_configured_2FA_method =='Authy Authenticator')) && get_option('mo2fa_administrator'))
						{
							$currentuser = wp_authenticate_username_password( $user, $username, $password );
							if(class_exists('UM_Functions')){
								$passcode = isset($_POST[ "mo2f_validate_otp_token" ]) ? sanitize_text_field($_POST[ "mo2f_validate_otp_token" ]) : sanitize_text_field($_POST['mo_softtoken']);
							if(!is_null($passcode) and !empty($passcode))
							{
								$passcode = sanitize_text_field($passcode);
								$this->miniorange_pass2login_start_session();
								$session_id_encrypt=$this->create_session();
								MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'mo2f_current_user_id', $currentuser->ID, 600);
								MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'mo2f_1stfactor_status', 'VALIDATE_SUCCESS', 6000);

							$customer = new Customer_Setup();
							if($mo2f_configured_2FA_method == 'miniOrange Soft Token')
								$method='SOFT TOKEN';
							else if($mo2f_configured_2FA_method == 'Google Authenticator')
								$method = 'GOOGLE AUTHENTICATOR';
							$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$currentuser->ID);
							$content = json_decode($customer->validate_otp_token( $method,$email , null, $passcode, get_option('mo2f_customerKey'), get_option('mo2f_api_key'),$currentuser),true);

							if(strcasecmp($content['status'], 'SUCCESS') == 0) {
							$redirect_to = isset($_POST[ 'redirect_to' ]) ? esc_url_raw($_POST[ 'redirect_to' ]) : null;
							
									$this->mo2fa_pass2login($redirect_to, $session_id_encrypt);
							}
							else
							{
								$error = new WP_Error();
							      $error->add('WRONG PASSCODE:', __('<strong>Wrong Two-factor Authentication code.</strong>'));
							       return $error;
							}


							}
							else
							{
							$error = new WP_Error();
							   $error->add('EMPTY PASSCODE:', __('<strong>Empty Two-factor Authentication code.</strong>'));
							   return $error;
							}
							}


								if(isset($_POST['mo_woocommerce_login_prompt'])){
						
									$this->miniorange_initiate_2nd_factor( $currentuser, "", $redirect_to,"",$session_id);
									}
							if(MO2f_Utility::get_index_value('GLOBALS','mo2f_is_ajax_request')){
								$data = array('notice' => '<div style="border-left:3px solid #dc3232;">&nbsp; Please enter the One Time Passcode', );
								wp_send_json_success($data);	
							}
							else
								return new WP_Error( 'one_time_passcode_empty', '<strong>ERROR</strong>: Please enter the One Time Passcode.');
							 // Prevent PHP notices when using app password login
							
						}
						else 
						{
							$otp_token = isset($_POST[ 'mo_softtoken' ]) ? trim( $_POST[ 'mo_softtoken' ] ) : '';
						}
						$attributes  = isset( $_POST['miniorange_rba_attribures'] ) ? sanitize_text_field($_POST['miniorange_rba_attribures']) : null;
		                $session_id  = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
						$mo2f_backup_code=isset($_POST[ 'mo2f_use_backup_code' ]) ?  trim($_POST[ 'mo2f_use_backup_code' ] ) : '';

		                if(is_null($session_id)) {
		                    $session_id=$this->create_session();
		                }

						if("mo2f_use_backup_code"==$mo2f_backup_code){	//BACKUP CODES
							MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_1stfactor_status', 'VALIDATE_SUCCESS', 600);
							MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_current_user_id', $currentuser->ID, 600);
							$mo2fa_login_message = __('Please provide backup code.','miniorange-2-factor-authentication');
							$mo2fa_login_status = 'MO_2_FACTOR_CHALLENGE_BACKUP';
							$this->miniorange_pass2login_form_fields($mo2fa_login_status, $mo2fa_login_message, $redirect_to, null, $session_id);
							exit;
						}

		                $error=$this->miniorange_initiate_2nd_factor( $currentuser, $attributes, $redirect_to, $otp_token, $session_id );


		                if(is_wp_error( $error)){
								  return $error;
					   }
					   return $error;
					}	
				}
			}
				
	}

   function mo2f_is_new_user($currentuser)
  {
       if(get_site_option('mo2f_user_login_status_'.$currentuser->ID))
	   {
		          return false;
	   }
	   else
	   {
		  
		  return true;
	   }
  }

   function mo2f_is_grace_period_expired($currentuser)
  { 
	    $grace_period_set_time=get_site_option('mo2f_grace_period_status_'.$currentuser->ID);

		$grace_period=get_site_option("mo2f_grace_period_value");
		if(get_site_option("mo2f_grace_period_type")=="hours")
		{
             $grace_period=$grace_period*60*60;

		}
		else
		{
			$grace_period=$grace_period*24*60*60;
		}

		$total_grace_period=$grace_period+$grace_period_set_time;
		$current_time_stamp=strtotime(current_datetime()->format('h:ia M d Y'));
		
     
	   return $total_grace_period<=$current_time_stamp;
   }


	function display_email_verification($head,$body,$color)
	{	
		echo "<div  style='background-color: #d5e3d9; height:850px;' >
		    <div style='height:350px; background-color: #3CB371; border-radius: 2px; padding:2%;  '>
		        <div class='mo2f_tamplate_layout' style='background-color: #ffffff;border-radius: 5px;box-shadow: 0 5px 15px rgba(0,0,0,.5); width:850px;height:350px; align-self: center; margin: 180px auto; ' >
		            <img  alt='logo'  style='margin-left:240px ;
		        margin-top:10px;width=40%;' src='https://auth.miniorange.com/moas/images/logo_large.png' />
		            <div><hr></div>

		            <tbody>
		            <tr>
		                <td>

		                    <p style='margin-top:0;margin-bottom:10px'>
		                    <p style='margin-top:0;margin-bottom:10px'> <h1 style='color:".$color.";text-align:center;font-size:50px'>".esc_html($head)."</h1></p>
		                    <p style='margin-top:0;margin-bottom:10px'>
		                    <p style='margin-top:0;margin-bottom:10px;text-align:center'><h2 style='text-align:center'>".esc_html($body)."</h2></p>
		                    <p style='margin-top:0;margin-bottom:0px;font-size:11px'>

		                </td>
		            </tr>

		        </div>
		    </div>
		</div>";
	}

	function mo_2_factor_enable_jquery_default_login() {
		wp_enqueue_script( 'jquery' );
	}

	function miniorange_pass2login_footer_form() {
		?>
        <script>
            jQuery(document).ready(function () {
                if (document.getElementById('loginform') != null) {
                    jQuery('#loginform').on('submit', function (e) {
                        jQuery('#miniorange_rba_attribures').val(JSON.stringify(rbaAttributes.attributes));
                    });
                } else {
                    if (document.getElementsByClassName('login') != null) {
                        jQuery('.login').on('submit', function (e) {
                            jQuery('#miniorange_rba_attribures').val(JSON.stringify(rbaAttributes.attributes));
                        });
                    }
                }
            });
        </script>
		<?php

	}


}

?>
