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

class Miniorange_Rba_Attributes {
	
	private $auth_mode = 2;	//  miniorange test or not
	private $https_mode = false; // website http or https
	
	function mo2f_collect_attributes( $useremail, $rba_attributes ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url          = MO_HOST_NAME . '/moas/rest/rba/acs';
		$customerKey  = get_option( 'mo2f_customerKey' );
		$field_string = "{\"customerKey\":\"" . $customerKey . "\",\"userKey\":\"" . $useremail . "\",\"attributes\":" . $rba_attributes . "}";
		$mo2fApi= new Mo2f_Api();
		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $field_string, $http_header_array );
	}

	function get_curl_error_message() {
		$message = mo2f_lt( 'Please enable curl extension.' ) .
		           ' <a href="admin.php?page=mo_2fa_troubleshooting">' .
		           mo2f_lt( 'Click here' ) .
		           ' </a> ' .
		           mo2f_lt( 'for the steps to enable curl.' );

		return json_encode( array( "status" => 'ERROR', "message" => $message ) );
	}

	function mo2f_evaluate_risk( $useremail, $sessionUuid ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url          = MO_HOST_NAME . '/moas/rest/rba/evaluate-risk';
		$customerKey  = get_option( 'mo2f_customerKey' );
		$field_string = array(
			'customerKey' => $customerKey,
			'appSecret'   => get_option( 'mo2f_app_secret' ),
			'userKey'     => $useremail,
			'sessionUuid' => $sessionUuid
		);
		$mo2fApi= new Mo2f_Api();
		
		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $field_string, $http_header_array );
	}

	function mo2f_register_rba_profile( $useremail, $sessionUuid ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url          = MO_HOST_NAME . '/moas/rest/rba/register-profile';
		$customerKey  = get_option( 'mo2f_customerKey' );
		$field_string = array(
			'customerKey' => $customerKey,
			'userKey'     => $useremail,
			'sessionUuid' => $sessionUuid
		);
		$mo2fApi= new Mo2f_Api();
		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $field_string, $http_header_array );
	}

	function mo2f_get_app_secret() {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}
		
		$mo2fApi= new Mo2f_Api();

		$url          = MO_HOST_NAME . '/moas/rest/customer/getapp-secret';
		$customerKey  = get_option( 'mo2f_customerKey' );
		$field_string = array(
			'customerId' => $customerKey
		);

		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $field_string, $http_header_array );
	}

	function mo2f_google_auth_service( $useremail,  $googleAuthenticatorName="" ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}
		$mo2fApi= new Mo2f_Api();
		$url          = MO_HOST_NAME . '/moas/api/auth/google-auth-secret';
		$customerKey  = get_option( 'mo2f_customerKey' );
		$field_string = array(
			'customerKey' => $customerKey,
			'username'    => $useremail,
			'googleAuthenticatorName'    => $googleAuthenticatorName
		);

		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $field_string, $http_header_array );
	}

	function mo2f_validate_google_auth( $useremail, $otptoken, $secret ) {
		if(MO2F_IS_ONPREM){
			include_once dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR. 'handler'.DIRECTORY_SEPARATOR. 'twofa' . DIRECTORY_SEPARATOR . 'gaonprem.php';
			$gauth_obj= new Google_auth_onpremise();
			$session_id_encrypt = isset($_POST['mo2f_session_id']) ? sanitize_text_field($_POST['mo2f_session_id']) : null;
			if($session_id_encrypt){
				$secret_ga = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'secret_ga');
			}else{
				$secret_ga = $secret;
			}
			$content=$gauth_obj->verifyCode($secret_ga , $otptoken );
			$value = json_decode($content,true);
			if($value['status'] == 'SUCCESS'){
				$user = wp_get_current_user();
				$user_id = $user->ID;
				$gauth_obj->mo_GAuth_set_secret($user_id, $secret_ga);
				update_user_meta($user_id,'mo2f_2FA_method_to_configure','Google Authenticator');
				update_user_meta( $user_id, 'mo2f_external_app_type', "Google Authenticator" );
				global $Mo2fdbQueries;//might not need this
				$Mo2fdbQueries->update_user_details( $user_id, array('mo2f_configured_2FA_method' =>'Google Authenticator') );
			}
		}else{
			if ( ! MO2f_Utility::is_curl_installed() ) {
				return $this->get_curl_error_message();
			}


			$url = MO_HOST_NAME . '/moas/api/auth/validate-google-auth-secret';
			$mo2fApi= new Mo2f_Api();
			
			$customerKey  = get_option( 'mo2f_customerKey' );
			$field_string = array(
				'customerKey' => $customerKey,
				'username'    => $useremail,
				'secret'      => $secret,
				'otpToken'    => $otptoken,
				'authenticatorType'	=> 'GOOGLE AUTHENTICATOR',
			);

			$http_header_array = $mo2fApi->get_http_header_array();
			$content = $mo2fApi->make_curl_call( $url, $field_string, $http_header_array );
		}

		return $content;
	}

}

?>