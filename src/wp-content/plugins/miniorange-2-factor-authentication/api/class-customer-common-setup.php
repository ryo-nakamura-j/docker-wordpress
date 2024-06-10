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

include_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR.'mo2f_api.php';

class Customer_Cloud_Setup  {

	public $email;
	public $phone;
	public $customerKey;
	public $transactionId;

	private $auth_mode = 2;	//  miniorange test or not
	private $https_mode = false; // website http or https


	function check_customer() {
		$url = MO_HOST_NAME . "/moas/rest/customer/check-if-exists";
		$email = get_option( "mo2f_email" );
		$mo2fApi= new Mo2f_Api();
		$fields = array (
			'email' => $email
		);
		$field_string = json_encode ( $fields );

		$headers = array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic");

		$response = $mo2fApi->make_curl_call( $url, $field_string );
		return $response;

	}

	function guest_audit() {
		$url = MO_HOST_NAME . "/moas/rest/customer/guest-audit";
		$email = get_option( "mo2f_email" );

		$user = wp_get_current_user();

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			if (empty($email))
				$email = $user->user_email;
		}

		$mo2fApi= new Mo2f_Api();
		$MoWpnsUtility = new MoWpnsUtility();
		$company     = get_option( 'mo2f_admin_company' ) != '' ? get_option( 'mo2f_admin_company' ) : sanitize_text_field($_SERVER['SERVER_NAME']);
		$applicationName='Wordpress Two Factor; Multisite: '.is_multisite().' '.$MoWpnsUtility->checkPlugins();
		$fields = array (
			'emailAddress' => $email,
			'companyName'=>$company,
			'cmsName'=>"WP",
			'applicationType'=>'Two Factor Upgrade',
			'applicationName'=>$applicationName,
			'pluginVersion'=>MO2F_VERSION,
			'inUse'=>$MoWpnsUtility->getFeatureStatus()
		);



		$headers = array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic");

		$field_string = json_encode ( $fields );

		$response = $mo2fApi->make_curl_call( $url, $field_string,$headers );
		return $response;

	}

	function send_email_alert( $email, $phone, $message ) {

		$url = MO_HOST_NAME . '/moas/api/notify/send';

		$mo2fApi= new Mo2f_Api();
		$customerKey = "16555";
		$apiKey      = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

		$currentTimeInMillis = $mo2fApi->get_timestamp();
		$stringToHash        = $customerKey . $currentTimeInMillis . $apiKey;
		$hashValue           = hash( "sha512", $stringToHash );
		$fromEmail           = $email;
		$subject             = "WordPress 2FA Plugin Feedback - " . $email;

		global $user;
		$user                       = wp_get_current_user();
		$is_nc_with_1_user          = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NNC', 'get_option');
		$is_ec_with_1_user          = ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');


		$customer_feature = "";

		if ( $is_ec_with_1_user ) {
			$customer_feature = "V1";
		}elseif ( $is_nc_with_1_user ) {
			$customer_feature = "V3";
		}

		$query = '[WordPress 2 Factor Authentication Plugin: ' . $customer_feature . ' - V '.MO2F_VERSION.']: ' . $message;

		$content = '<div >First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . sanitize_text_field($_SERVER['SERVER_NAME']) . '" target="_blank" >' . sanitize_text_field($_SERVER['SERVER_NAME']) . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>Query :' . $query . '</div>';

		$fields       = array(
			'customerKey' => $customerKey,
			'sendEmail'   => true,
			'email'       => array(
				'customerKey' => $customerKey,
				'fromEmail'   => $fromEmail,
				'fromName'    => 'Xecurify',
				'toEmail'     => '2fasupport@xecurify.com',
				'toName'      => '2fasupport@xecurify.com',
				'subject'     => $subject,
				'content'     => $content
			),
		);
		$field_string = json_encode( $fields );

		$headers = $mo2fApi->get_http_header_array();

		$response = $mo2fApi->make_curl_call( $url, $field_string, $headers );
		return $response;


	}

	function create_customer() {
		global $Mo2fdbQueries;
		if ( ! MO2f_Utility::is_curl_installed() ) {
			$message = 'Please enable curl extension. <a href="admin.php?page=mo_2fa_troubleshooting">Click here</a> for the steps to enable curl.';

			return json_encode( array( "status" => 'ERROR', "message" => $message ) );
		}

		$url = MO_HOST_NAME . '/moas/rest/customer/add';
		$mo2fApi= new Mo2f_Api();
		global $user;
		$user        = wp_get_current_user();
		$this->email = get_option( 'mo2f_email' );
		$this->phone = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
		$password    = get_option( 'mo2f_password' );
		$company     = get_option( 'mo2f_admin_company' ) != '' ? get_option( 'mo2f_admin_company' ) : sanitize_text_field($_SERVER['SERVER_NAME']);

		$fields       = array(
			'companyName'     => $company,
			'areaOfInterest'  => 'WordPress 2 Factor Authentication Plugin',
			'productInterest' => 'API_2FA',
			'email'           => $this->email,
			'phone'           => $this->phone,
			'password'        => $password
		);
		$field_string = json_encode( $fields );
		$headers = array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic");

		$content = $mo2fApi->make_curl_call( $url, $field_string );

		return $content;
	}


	function get_customer_key() {
		if ( ! MO2f_Utility::is_curl_installed() ) {
			$message = 'Please enable curl extension. <a href="admin.php?page=mo_2fa_troubleshooting">Click here</a> for the steps to enable curl.';

			return json_encode( array( "status" => 'ERROR', "message" => $message ) );
		}

		$url      = MO_HOST_NAME . "/moas/rest/customer/key";

		$email    = get_option( "mo2f_email" );
		$password = get_option( "mo2f_password" );
		$mo2fApi= new Mo2f_Api();
		$fields       = array(
			'email'    => $email,
			'password' => $password
		);
		$field_string = json_encode( $fields );

		$headers = array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic");

		$content = $mo2fApi->make_curl_call( $url, $field_string );

		return $content;
	}


	function send_otp_token( $uKey, $authType, $cKey, $apiKey, $currentuser=null ) {

			if ( ! MO2f_Utility::is_curl_installed()) {
				$message = 'Please enable curl extension. <a href="admin.php?page=mo_2fa_troubleshooting">Click here</a> for the steps to enable curl.';

				return json_encode( array( "status" => 'ERROR', "message" => $message ) );
			}

			$url     = MO_HOST_NAME . '/moas/api/auth/challenge';
			$mo2fApi = new Mo2f_Api();
			/* The customer Key provided to you */
			$customerKey = $cKey;

			/* The customer API Key provided to you */
			$apiKey = $apiKey;

			/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
			$currentTimeInMillis = $mo2fApi->get_timestamp();

			/* Creating the Hash using SHA-512 algorithm */
			$stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
			$hashValue    = hash( "sha512", $stringToHash );

			$headers = $mo2fApi->get_http_header_array();

			$fields = '';
			if ( $authType == 'EMAIL' || $authType == 'OTP Over Email' || $authType == 'OUT OF BAND EMAIL' ) {
				$fields = array(
					'customerKey'     => $customerKey,
					'email'           => $uKey,
					'authType'        => $authType,
					'transactionName' => 'WordPress 2 Factor Authentication Plugin'
				);
			} elseif ( $authType == 'SMS' ) {
				$authType = "SMS";
				$fields   = array(
					'customerKey' => $customerKey,
					'phone'       => $uKey,
					'authType'    => $authType
				);
			} else {
				$fields = array(
					'customerKey'     => $customerKey,
					'username'        => $uKey,
					'authType'        => $authType,
					'transactionName' => 'WordPress 2 Factor Authentication Plugin'
				);
			}

			$field_string = json_encode( $fields );

			$content = $mo2fApi->make_curl_call( $url, $field_string, $headers );
		
		$content1 = json_decode($content,true);

		if ( $content1['status'] == "SUCCESS" ) {
			if(get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z') == 4 && $authType == 'SMS'){
               	Miniorange_Authentication::low_otp_alert("sms");
               }
            if(get_site_option('cmVtYWluaW5nT1RQ') == 5 && $authType == 'OTP Over Email'){
              	Miniorange_Authentication::low_otp_alert("email");
            }
		}

		return $content;
	}




	function get_customer_transactions( $cKey, $apiKey ,$license_type) {

		$url = MO_HOST_NAME . '/moas/rest/customer/license';

		$customerKey = $cKey;
		$apiKey      = $apiKey;
		$mo2fApi= new Mo2f_Api();
		$currentTimeInMillis = $mo2fApi->get_timestamp();
		$stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
		$hashValue    = hash( "sha512", $stringToHash );

		$fields = '';
		if($license_type == 'DEMO'){
		$fields = array(
			'customerId'      => $customerKey,
			'applicationName' => '-1',
			'licenseType'   => $license_type
		);
		}else{
		    $fields = array(
                'customerId'      => $customerKey,
                'applicationName' => 'otp_recharge_plan',
                'licenseType'   => $license_type
            );
		}

		$field_string = json_encode( $fields );

		$headers = $mo2fApi->get_http_header_array();

		$content = $mo2fApi->make_curl_call( $url, $field_string, $headers );


		return $content;
	}

	public function mo_2f_generate_backup_codes($mo2f_user_email,$site_url){
		$url = MoWpnsConstants::GENERATE_BACK_CODE;

		$data = $this->mo_2f_autnetication_backup_code_request($mo2f_user_email,$site_url);
			
        $postdata = array('mo2f_email'=> $mo2f_user_email,
							'mo2f_domain' =>$site_url,
							'HTTP_AUTHORIZATION'=>'Bearer|'.$data,
							'mo2f_generate_backup_codes'=>'initiated_backup_codes');	  	 
			
		
       
		return $this->mo_2f_remote_call_function($url,$postdata);

    }

    public function mo_2f_autnetication_backup_code_request($mo2f_user_email,$site_url){

    	$url = MoWpnsConstants::AUTHENTICATE_REQUEST;

    	$postdata = array('mo2f_email'=> $mo2f_user_email,
							'mo2f_domain' =>$site_url,
							'mo2f_cKey'=>MoWpnsConstants::DEFAULT_CUSTOMER_KEY,
							'mo2f_cSecret'=>MoWpnsConstants::DEFAULT_API_KEY
							);

        return $this->mo_2f_remote_call_function($url,$postdata);
    }

    public function mo_2f_remote_call_function($url,$postdata){


    	$args = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'sslverify'   => false,
			'headers'     => array(),
			'body'        => $postdata,

		);
		
		$mo2f_api=new Mo2f_Api();
		$data = $mo2f_api->mo2f_wp_remote_post($url,$args);
		$status_code = wp_remote_retrieve_response_code(wp_remote_post($url,$args));
        
		$data1 = json_decode($data, true);
		if( is_array($data1) && $data1['status'] == "ERROR" || $status_code != '200'){
         return 'InternetConnectivityError';
		}else 
			return $data;	
    }

    public function mo2f_validate_backup_codes($mo2f_backup_code,$mo2f_user_email){
    	$url = MoWpnsConstants::VALIDATE_BACKUP_CODE;
        $site_url = site_url(); 
    	$data = $this->mo_2f_autnetication_backup_code_request($mo2f_user_email,$site_url);
    	
    	    
			$postdata = array('mo2f_otp_token' => $mo2f_backup_code,
								'mo2f_user_email'=> $mo2f_user_email,
								'HTTP_AUTHORIZATION'=>'Bearer|'.$data,
								'mo2f_site_url' => $site_url);

			$args = array(
				'method'      => 'POST',
				'timeout'     => 45,
				'sslverify'   => false,
				'headers'     => array(),
				'body'        => $postdata,
			);
			
			$data=wp_remote_post($url,$args);

			$data=wp_remote_retrieve_body( $data );

			return $data;
    }


	function validate_otp_token( $authType, $username, $transactionId, $otpToken, $cKey, $customerApiKey, $current_user =null) {
		$content='';
			if ( ! MO2f_Utility::is_curl_installed() ) {
				$message = 'Please enable curl extension. <a href="admin.php?page=mo_2fa_troubleshooting">Click here</a> for the steps to enable curl.';

				return json_encode( array( "status" => 'ERROR', "message" => $message ) );
			}

			$url = MO_HOST_NAME . '/moas/api/auth/validate';
			$mo2fApi= new Mo2f_Api();
			/* The customer Key provided to you */
			$customerKey = $cKey;

			/* The customer API Key provided to you */
			$apiKey = $customerApiKey;

			/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
			$currentTimeInMillis = $mo2fApi->get_timestamp();

			/* Creating the Hash using SHA-512 algorithm */
			$stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
			$hashValue    = hash( "sha512", $stringToHash );

			$headers = $mo2fApi->get_http_header_array();
			$fields = '';
			if ( $authType == 'SOFT TOKEN' || $authType == 'GOOGLE AUTHENTICATOR' ) {
				/*check for soft token*/
				$fields = array(
					'customerKey' => $customerKey,
					'username'    => $username,
					'token'       => $otpToken,
					'authType'    => $authType
				);
			} elseif ( $authType == 'KBA' ) {
					$fields = array(
						'txId'    => $transactionId,
						'answers' => array(
							array(
								'question' => $otpToken[0],
								'answer'   => $otpToken[1]
							),
							array(
								'question' => $otpToken[2],
								'answer'   => $otpToken[3]
							)
						)
					);

			} else {
				//*check for otp over sms/email
				$fields = array(
					'txId'  => $transactionId,
					'token' => $otpToken
				);
			}
			$field_string = json_encode( $fields );

			
			$content = $mo2fApi->make_curl_call( $url, $field_string, $headers );
		return $content;
	}

	function submit_contact_us( $q_email, $q_phone, $query ) {
		if ( ! MO2f_Utility::is_curl_installed() ) {
			$message = 'Please enable curl extension. <a href="admin.php?page=mo_2fa_troubleshooting">Click here</a> for the steps to enable curl.';

			return json_encode( array( "status" => 'ERROR', "message" => $message ) );
		}

		$url = MO_HOST_NAME . "/moas/rest/customer/contact-us";
		global $user;
		$user                       = wp_get_current_user();
		$is_nc_with_1_user          = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NNC', 'get_option');
		$is_ec_with_1_user          = ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');

		$mo2fApi= new Mo2f_Api();
		$customer_feature = "";

		if ( $is_ec_with_1_user ) {
			$customer_feature = "V1";
		} elseif ( $is_nc_with_1_user ) {
			$customer_feature = "V3";
		}
		global $moWpnsUtility;

		$query        = '[WordPress 2 Factor Authentication Plugin: ' . $customer_feature . ' - V '.MO2F_VERSION.'- Ticket Id:'.$moWpnsUtility->getFeatureStatus().']: ' . $query;
		$fields       = array(
			'firstName' => $user->user_firstname,
			'lastName'  => $user->user_lastname,
			'company'   => sanitize_text_field($_SERVER['SERVER_NAME']),
			'email'     => $q_email,
			'ccEmail' => '2fasupport@xecurify.com',
			'phone'     => $q_phone,
			'query'     => $query
		);
		$field_string = json_encode( $fields );

		$headers = array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic");

		$content = $mo2fApi->make_curl_call( $url, $field_string );
		

		return true;
	}

}


?>
