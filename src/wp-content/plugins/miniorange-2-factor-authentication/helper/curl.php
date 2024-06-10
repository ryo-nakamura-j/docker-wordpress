<?php

class MocURL
{

	public static function create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = '')
	{
		$url = MoWpnsConstants::HOST_NAME . '/moas/rest/customer/add';
		$fields = array (
			'companyName' 	 => $company,
			'areaOfInterest' => 'WordPress 2 Factor Authentication Plugin',
			'productInterest' => 'API_2FA',
			'firstname' 	 => $first_name,
			'lastname' 		 => $last_name,
			'email' 		 => $email,
			'phone' 		 => $phone,
			'password' 		 => $password
		);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	public static function get_customer_key($email, $password) 
	{
		$url 	= MoWpnsConstants::HOST_NAME. "/moas/rest/customer/key";
		$fields = array (
					'email' 	=> $email,
					'password'  => $password
				);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);

		return $response;
	}
	
	function submit_contact_us( $q_email, $q_phone, $query, $call_setup=false)
	{
		$current_user = wp_get_current_user();
		$url    = MoWpnsConstants::HOST_NAME . "/moas/rest/customer/contact-us";
		
		$is_nc_with_1_user          = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NNC', 'get_option');
		$is_ec_with_1_user          = ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');
        	$onprem = MO2F_IS_ONPREM ?'O':'C';

		$customer_feature = "";

		if ( $is_ec_with_1_user ) {
			$customer_feature = "V1";
		}elseif ( $is_nc_with_1_user ) {
			$customer_feature = "V3";
		}
		global $moWpnsUtility;
		if($call_setup)
			$query = '[Call Request - WordPress 2 Factor Authentication Plugin: ' .$onprem.$customer_feature . ' - V '.MO2F_VERSION.'- Ticket Id:'.$moWpnsUtility->getFeatureStatus().']: ' . $query;
		else
		$query = '[WordPress 2 Factor Authentication Plugin: ' .$onprem.$customer_feature . ' - V '.MO2F_VERSION.'- Ticket Id:'.$moWpnsUtility->getFeatureStatus().']: ' . $query;
		
		$fields = array(
					'firstName'	=> $current_user->user_firstname,
					'lastName'	=> $current_user->user_lastname,
					'company' 	=> sanitize_text_field($_SERVER['SERVER_NAME']),
					'email' 	=> $q_email,
					'ccEmail' 	=> '2fasupport@xecurify.com',
					'phone'		=> $q_phone,
					'query'		=> $query
				);
		$field_string = json_encode( $fields );
		$response = self::callAPI($url, $field_string);
		
		return true;
	}

	function lookupIP($ip)
	{
		$url 	= MoWpnsConstants::HOST_NAME. "/moas/rest/security/iplookup";
		$fields = array (
					'ip' => $ip
				);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	//CHECK
	function send_otp_token($auth_type, $phone, $email)
	{
		
		$url 		 = MoWpnsConstants::HOST_NAME . '/moas/api/auth/challenge';
		$customerKey = MoWpnsConstants::DEFAULT_CUSTOMER_KEY;
		$apiKey 	 = MoWpnsConstants::DEFAULT_API_KEY;

		$fields  	 = array(
							'customerKey' 	  => $customerKey,
							'email' 	  	  => $email,
							'phone' 	  	  => $phone,
							'authType' 	  	  => $auth_type,
							'transactionName' => 'miniOrange 2-Factor'
						);
		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
		$response 	 = self::callAPI($url, $json, $authHeader);
		return $response;
	}

	function validate_recaptcha($ip,$response)
	{
		$url 		 = MoWpnsConstants::RECAPTCHA_VERIFY;
		$json		 = "";
		$fields 	 = array(
							'response' => $response,
							'secret'   => get_option('mo_wpns_recaptcha_secret_key'),
							'remoteip' => $ip
						);
		foreach($fields as $key=>$value) { $json .= $key.'='.$value.'&'; }
		rtrim($json, '&');
		$response 	 = self::callAPI($url, $json, null);
		return $response;
	}

	function get_Captcha_v3($Secretkey)
	{
		$json		 = "";
		$url         = "https://www.google.com/recaptcha/api/siteverify";
	    $fields 	 = array(
						'response' => $Secretkey,
						'secret'   => get_option('mo_wpns_recaptcha_secret_key_v3'),
						'remoteip' => sanitize_text_field($_SERVER['REMOTE_ADDR'])
					);
	foreach($fields as $key=>$value) { $json .= $key.'='.$value.'&'; }
	json_encode($json);
	$result 	 = $this->callAPI($url, $json, null);
		
	return $result;
	}

	function validate_otp_token($transactionId,$otpToken)
	{
		$url 		 = MoWpnsConstants::HOST_NAME . '/moas/api/auth/validate';
		$customerKey = MoWpnsConstants::DEFAULT_CUSTOMER_KEY;
		$apiKey 	 = MoWpnsConstants::DEFAULT_API_KEY;

		$fields 	 = array(
						'txId'  => $transactionId,
						'token' => $otpToken,
					 );

		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
		$response    = self::callAPI($url, $json, $authHeader);
		return $response;
	}
	
	function check_customer($email)
	{
		$url 	= MoWpnsConstants::HOST_NAME . "/moas/rest/customer/check-if-exists";
		$fields = array(
					'email' 	=> $email,
				);
		$json     = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	function mo_wpns_forgot_password()
	{
	
		$url 		 = MoWpnsConstants::HOST_NAME . '/moas/rest/customer/password-reset';
		$email       = get_option('mo2f_email');
		$customerKey = get_option('mo2f_customerKey');
		$apiKey 	 = get_option('mo2f_api_key');
	
		$fields 	 = array(
						'email' => $email
					 );
	
		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
		$response    = self::callAPI($url, $json, $authHeader);
		return $response;
	}

	function send_notification($toEmail,$subject,$content,$fromEmail,$fromName,$toName)
	{
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		$headers .= 'From: '.$fromName.'<'.$fromEmail.'>' . "\r\n";

		mail($toEmail,$subject,$content,$headers);

		return json_encode(array("status"=>'SUCCESS','statusMessage'=>'SUCCESS'));
	}

	//added for feedback

    function send_email_alert($email,$phone,$message,$feedback_option){
    	    global $moWpnsUtility;
	    global $user;
        $url = MoWpnsConstants::HOST_NAME . '/moas/api/notify/send';
        $customerKey = MoWpnsConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 = MoWpnsConstants::DEFAULT_API_KEY;
        $fromEmail			= 'no-reply@xecurify.com';
         $Di = get_site_option('No_of_days_active_work');
         $Di = intval($Di);
         if ($feedback_option == 'mo_wpns_skip_feedback')
		{
			$subject = "Deactivate [Feedback Skipped]: WordPress miniOrange 2-Factor Plugin :" .$Di ;

		}
		elseif ($feedback_option == 'mo_wpns_feedback' )
		{
		
			$subject     = "Feedback: WordPress miniOrange 2-Factor Plugin - ". $email.' : ' .$Di;
		}

        $user         = wp_get_current_user();
		
		$is_nc_with_1_user          = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NNC', 'get_option');
		$is_ec_with_1_user          = ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');
        $onprem = MO2F_IS_ONPREM ? 'O':'C';

		$customer_feature = "";

		if ( $is_ec_with_1_user ) {
			$customer_feature = "V1";
		}elseif ( $is_nc_with_1_user ) {
			$customer_feature = "V3";
		}

		$query = '[WordPress 2 Factor Authentication Plugin: ' .$onprem.$customer_feature . ' - V '.MO2F_VERSION.']: ' . $message;

        $content='<div >Hello, <br><br>Ticket ID:'.$moWpnsUtility->getFeatureStatus().'<br><br>First Name :'.$user->user_firstname.'<br><br>Last  Name :'.$user->user_lastname.'   <br><br>Company :<a href="'.esc_url($_SERVER['SERVER_NAME']).'" target="_blank" >'.esc_html($_SERVER['SERVER_NAME']).'</a><br><br>Phone Number :'.$phone.'<br><br>Email :<a href="mailto:'.esc_html($email).'" target="_blank">'.esc_html($email).'</a><br><br>Query :'.wp_kses_post($query).'</div>';

        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'fromName' 		=> 'Xecurify',
                'toEmail' 		=> '2fasupport@xecurify.com',
                'toName' 		=> '2fasupport@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);
        $authHeader  = $this->createAuthHeader($customerKey,$apiKey);
        $response = self::callAPI($url, $field_string,$authHeader);
		
        return $response;
    }


	private static function createAuthHeader($customerKey, $apiKey) {
		$currentTimestampInMillis = round(microtime(true) * 1000);
		$currentTimestampInMillis = number_format($currentTimestampInMillis, 0, '', '');

		$stringToHash = $customerKey . $currentTimestampInMillis . $apiKey;
		$authHeader = hash("sha512", $stringToHash);

		$header = [
            "Content-Type"  => "application/json",
            "Customer-Key"  => $customerKey,
            "Timestamp"     => $currentTimestampInMillis,
            "Authorization" => $authHeader
        ];
        return $header;
	}

	private static function callAPI($url, $json_string, $http_header_array =array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic")) {
        //For testing (0, false)
        //For Production (1, true)
        
         $args = array(
            'method' => 'POST',
            'body' => $json_string,
            'timeout' => '5',
            'redirection' => '5',
            'sslverify'  =>true,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $http_header_array
        );
         
		$mo2f_api=new Mo2f_Api();
		$response=$mo2f_api->mo2f_wp_remote_post($url,$args);
        return $response;
    }

	}
